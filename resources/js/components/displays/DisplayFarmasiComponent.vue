<template>
  <div class="page">
    <div>
      <div class="page-main">
        <header-component
          :app_name="app_name"
          :app_address="app_address"
        ></header-component>
        <div class="my-3 my-md-12">
          <div class="container-fluid app_panel">
            <div class="row row-cards">
              <div class="col-lg-8">
                <div class="card p-0">
                  <div class="card-body p-2">
                    <video-component
                      v-if="config.type == 'video'"
                      :daftar_video="daftar_video"
                    >
                    </video-component>

                    <div
                      id="carousel-indicators"
                      v-show="config.type == 'image'"
                      class="carousel-banner slide"
                      data-ride="carousel"
                    >
                      <ol class="carousel-indicators">
                        <banner-indicator-component
                          v-for="(banner, index) in daftar_image"
                          :banner="banner"
                          :index="index"
                          :key="banner.id"
                        ></banner-indicator-component>
                      </ol>
                      <div class="carousel-inner">
                        <banner-component
                          v-for="(banner, index) in daftar_image"
                          :banner="banner"
                          :index="index"
                          :key="banner.id"
                        ></banner-component>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="card mb-0" style="background-color:#0f4110">
                      <div class="card-header text-center">
                        <div
                          class="card-body h1 p-0"
                          style="font-weight:bold;color:#FBC02D;font-size:4vh"
                        >
                          NOMOR DIPANGGIL
                        </div>
                      </div>
                      <div
                        class="card-body text-center pt-1"
                        style="background-color:#1A7818"
                      >
                        <div
                          class="display-1 font-weight-bold mb-2 mt-0"
                          style="color:#FFF176;font-size:8vh"
                        >
                          {{ nomor_daftar }}
                        </div>
                        <div class="progress progress-sm">
                          <div
                            class="progress-bar bg-yellow"
                            style="width: 100%"
                          ></div>
                        </div>
                      </div>
                    </div>

                    <div
                      class="card mt-0"
                      style="background-color:#0f4110;margin-top:-15px!important"
                    >
                      <div class="card-header text-center">
                        <div
                          class="card-body h1 p-0"
                          style="font-weight:bold;color:#FBC02D;font-size:4vh"
                        >
                          ANTRIAN SELANJUTNYA
                        </div>
                      </div>
                      <table
                        class="table card-table h1 font-weight-bold"
                        style="background-color:#1A7818;color:#FFF176"
                      >
                        <tr v-for="(s, index) in daftar_nomor" :key="index">
                          <td>
                            <span style="font-size:4vh">
                              {{ s.tiket_farmasi_nomor }}
                            </span>
                          </td>
                          <td
                            class="text-right text-white"
                            style="font-size:4vh"
                          >
                            {{ s.current }}
                          </td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="alert cstm_footer" style="background:#FFF;">
      <span id="info_text"></span>
    </div>

    <footer class="footer cstm_footer_app cstm_footer text-right p-3">
      <h5><time-component></time-component></h5>
    </footer>

    <audio-component
      :audios="audios"
      :loket="loket"
      :app_multiloket="true"
      ref="audio"
    ></audio-component>
  </div>
</template>

<script>
import Swal from "sweetalert2";
import Typed from "typed.js";

import HeaderComponent from "../commons/HeaderComponent.vue";
import PoliComponent from "../elements/PoliComponent.vue";
import BannerComponent from "../elements/BannerComponent.vue";
import BannerIndicatorComponent from "../elements/BannerIndicatorComponent.vue";
import VideoComponent from "../elements/VideoComponent.vue";
import AudioComponent from "../elements/AudioComponent.vue";
import TimeComponent from "../elements/TimeComponent.vue";

const waitFor = ms => new Promise(r => setTimeout(r, ms));
async function asyncForEach(array, callback) {
  for (let index = 0; index < array.length; index++) {
    await callback(array[index], index, array);
  }
}

export default {
  components: {
    HeaderComponent,
    PoliComponent,
    BannerComponent,
    BannerIndicatorComponent,
    VideoComponent,
    AudioComponent,
    TimeComponent
  },
  props: ["app_name", "app_address"],
  data() {
    return {
      base_url: document.head.querySelector('meta[name="base-url"]').content,
      ws: null,
      nomor_daftar: "-",
      daftar_nomor: [],
      daftar_image: [],
      daftar_video: [],
      info_text: [],
      audios: "",
      loket: "",
      config: [],
      summary: [],

      queues: [],
      waitingAudio: 0,
      isCalling: false,
      queues_processing: []
    };
  },

  watch: {
    queues: async function(val) {
      if (val.length === 0) return;

      val.forEach(e => {
        if (!this.queues_processing.find(p => p.nomor === e.nomor)) {
          this.queues_processing.push(e);
        }
      });

      if (this.isCalling) {
        await waitFor(this.waitingAudio * 1000);
      }
      asyncForEach(this.queues_processing, async element => {
        // Update display nomor yang sedang dipanggil
        this.nomor_daftar = element.nomor;

        // display antrian selanjutnya secara otomatis
        await axios.post("api/farmasi/all-nomor").then(response => {
          this.daftar_nomor = response.data;
        });

        this.play_audio(element);
        await waitFor(this.waitingAudio * 1000);
        let index = this.queues_processing.indexOf(element);
        this.queues_processing.splice(index, 1);
      });
      this.queues = [];
    },

    waitingAudio: function(val) {
      if (this.waitingAudio === 0) this.isCalling = false;

      this.isCalling = true;
      setTimeout(() => {
        this.isCalling = false;
        this.waitingAudio = 0;
      }, val * 1000);
    },

    isCalling: function(val) {
      if (val) {
        this.disable_tombol_panggil(true);
      } else {
        this.disable_tombol_panggil(false);
      }
    }
  },

  methods: {
    get_summary() {
      axios
        .post("api/farmasi/summary")
        .then(response => (this.summary = response.data));
    },

    init_data() {
      axios
        .post("api/farmasi/nomor-current")
        .then(response => (this.nomor_daftar = response.data));

      axios
        .post("api/farmasi/all-nomor")
        .then(response => (this.daftar_nomor = response.data));

      this.get_summary();

      axios
        .post("api/banner/list-image/1")
        .then(response => (this.daftar_image = response.data));

      axios
        .post("api/banner/list-video/1")
        .then(response => (this.daftar_video = response.data));

      axios.post("api/info/list-display").then(
        response => (
          (this.info_text = response.data),
          new Typed("#info_text", {
            strings: response.data,
            typeSpeed: 30,
            backDelay: 7000,
            showCursor: false,
            cursorChar: "",
            loop: true
          })
        )
      );

      axios
        .post("api/banner/config")
        .then(response => (this.config = response.data));
    },

    disable_tombol_panggil(bool) {
      this.ws.send(
        JSON.stringify({
          target: "loket",
          sub_target: "farmasi",
          gedung: 1,
          disabled_call: bool
        })
      );
    },

    ws_error_message() {
      $(".app_panel").LoadingOverlay("show", {
        image: "",
        text: "koneksi socket server terputus, hubungi wawicom.",
        textColor: "#9C9999"
      });
    },

    ws_connect() {
      let self = this;

      this.ws = new WebSocket(
        document.head.querySelector('meta[name="web-socket"]').content
      );

      this.ws.onopen = function() {
        $(".app_panel").LoadingOverlay("hide", true);
        self.init_data();
      };

      this.ws.onclose = function(e) {
        self.ws_error_message();
        setTimeout(function() {
          self.ws_connect();
        }, 3000);
      };

      this.ws.onerror = function(err) {
        self.ws_error_message();
        self.ws.close();
      };

      this.ws.onmessage = function(e) {
        var data = JSON.parse(e.data);
        console.log('data hubung')
        self.init_data()
        if (data.target == "display") {
          if (data.sub_target == "update_setting") {
            self.init_data();
          }

          if (data.sub_target == "farmasi") {
            if (data.action == "update_summary") {
              self.get_summary();
            }

            if (data.nomor) {
              // save request audio to queues

              self.queues.push({
                nomor: data.nomor,
                loket: data.loket
              });
            }
          }

          if (data.action == "refresh_browser") {
            location.reload(true);
          }
        }
      };
    },

    play_audio(data) {
      const { nomor, loket } = data;

      //play audio
      this.waitingAudio = this.$refs.audio.play_audio({
        tujuan: "farmasi",
        loket: loket,
        nomor: nomor
      });
      //end audio

      Swal.fire({
        html: `
          <div class="card mb-0" style="background-color:#0f4110">
            <div class="card-header text-center">
              <div class="card-body p-0" style="font-size:4em;font-weight:bold;color:#FBC02D">
                PEMANGGILAN OBAT
              </div>
            </div>
            <div class="card-body text-center pt-1" style="background-color:#1A7818;">
              <div class="display-1 font-weight-bold mb-2 mt-0" style="color:#FFF176;font-size:18em">${nomor}</div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-yellow" style="width: 100%"></div>
              </div>
            </div>
          </div>`,
        width: "80%",
        padding: "1em",
        showConfirmButton: false,
        timer: 9000,
        animation: false,
        customClass: {
          content: "modal-display-antrian"
        }
      });
    }
  },

  created() {
    let self = this;
    this.ws_connect();
    this.init_data();
  }
};
</script>
