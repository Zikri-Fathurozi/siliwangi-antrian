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
              <div class="col-lg-7">
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

              <div class="col-md-5">
                <div class="row">
                  <div class="col-sm-12">
                    <div
                      class="card mb-0"
                      style="background-color:#0f4110;height:100vh"
                    >
                      <div class="card-header text-center">
                        <div
                          class="card-body h1 p-0"
                          style="font-weight:bold;color:#FBC02D;font-size:4vh"
                        >
                          ANTRIAN DILAYANI
                        </div>
                      </div>
                      <table
                        class="table card-table h1 font-weight-bold"
                        style="background-color:#1A7818;color:#FFF176;font-size:3.7vh"
                      >
                        <tr>
                          <td style="color:#FBC02D">
                            RUANGAN
                          </td>
                          <td class="text-right" style="color:#FBC02D">
                            NOMOR
                          </td>
                        </tr>
                        <tr v-for="(p, index) in daftar_poli" :key="index">
                          <td>Ruangan {{ p.nomor_kamar }}</td>
                          <td class="text-right text-white">
                            {{ p.tiket_poli_nomor }}
                          </td>
                          <!-- <td class="text-right text-white">sisa
                            {{
                              summary.find(s => s.poli_nama == p.poli_nama) &&
                                summary.find(s => s.poli_nama == p.poli_nama)
                                  .sisa
                            }}
                          </td> -->
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

      <div class="alert cstm_footer" style="background:#FFF;">
        <span id="info_text"></span>
      </div>

      <footer class="footer cstm_footer_app cstm_footer text-right p-3">
        <h5><time-component></time-component></h5>
      </footer>

      <audio-component :audios="audios" ref="audio"></audio-component>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";
import Typed from "typed.js";

import PoliComponent from "../elements/PoliComponent.vue";
import BannerComponent from "../elements/BannerComponent.vue";
import BannerIndicatorComponent from "../elements/BannerIndicatorComponent.vue";
import VideoComponent from "../elements/VideoComponent.vue";
import AudioComponent from "../elements/AudioComponent.vue";
import TimeComponent from "../elements/TimeComponent.vue";
import HeaderComponent from "../commons/HeaderComponent.vue";

const waitFor = ms => new Promise(r => setTimeout(r, ms));
async function asyncForEach(array, callback) {
  for (let index = 0; index < array.length; index++) {
    await callback(array[index], index, array);
  }
}

export default {
  components: {
    PoliComponent,
    BannerComponent,
    BannerIndicatorComponent,
    VideoComponent,
    AudioComponent,
    TimeComponent,
    HeaderComponent
  },
  props: ["app_name", "app_address", "gedung", "poli_id"],
  data() {
    return {
      base_url: document.head.querySelector('meta[name="base-url"]').content,
      ws: null,
      daftar_poli: [],
      daftar_image: [],
      daftar_video: [],
      config: [],
      info_text: [],
      audios: "",
      nomor_daftar: "-",
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
          console.log('asyncForEach ')
          console.log(element)
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
        .post("api/poli/summary", { poli_gedung: this.gedung })
        .then(response => (this.summary = response.data));
    },

    get_display() {
      axios
        .post("api/kamar/list-display", { poli: this.poli_id, poli_gedung: this.gedung })
        .then(response => (this.daftar_poli = response.data));
    },

    init_data() {
      this.get_display();
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
          sub_target: "kamar",
          gedung: this.gedung,
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
        if (data.target == "display") {
          if (data.sub_target == "update_setting") {
            self.init_data();
          } else if (data.sub_target == "kamar") {
            if (data.action == "update_summary") {
              self.get_summary();
            }

            if (data.nomor) {
              if (self.daftar_poli[data.poli]) {
                // save request audio to queues
                self.queues.push({
                  nomor: data.nomor,
                  ruangan: data.nomor_kamar,
                  poli: self.daftar_poli[data.poli]
                });

                self.get_display();
                self.get_summary();
              }
            }
          }

          if (data.action == "refresh_browser") {
            location.reload(true);
          }
        }
      };
    },

    play_audio(data) {
      const { nomor, poli , ruangan} = data;
      //play audio
      this.waitingAudio = this.$refs.audio.play_audio({
        tujuan: "kamar",
        poli: poli,
        kamar: ruangan,
        nomor: nomor
      });
      //end audio

      Swal.fire({
        html: `
          <div class="card mb-0" style="background-color:#0f4110">
            <div class="card-header text-center">
              <div class="card-body p-0" style="font-size:4em;font-weight:bold;color:#FBC02D">
                ${poli.poli_nama} - RUANGAN ${ruangan}
              </div>
            </div>
            <div class="card-body text-center pt-1" style="background-color:#1A7818;">
              <div class="display-1 font-weight-bold mb-2 mt-0" style="color:#FFF176;font-size:18em">
                ${nomor}
              </div>
              <div class="progress progress-sm">
                <div class="progress-bar bg-yellow" style="width: 100%"></div>
              </div>
            </div>
          </div>`,
        width: "80%",
        padding: "2em",
        showConfirmButton: false,
        timer: 10000,
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
  }
};
</script>
