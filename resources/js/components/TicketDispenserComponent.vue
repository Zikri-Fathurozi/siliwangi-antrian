<template>
  <div class="page">
    <div class="page-main">
      <header-component
        :app_name="app_name"
        :app_address="app_address"
      ></header-component>
      <div class="my-3 my-md-5 app_panel">
        <div class="">
          <div class="page-header">
            <br />
            <br /><br />
            <h1 style="font-size:4.3vh">
              <b
                ><span v-if="is_prioritas">
                  KHUSUS LANSIA / PRIORITAS
                </span>
                <span v-else>
                  PENDAFTARAN POLI
                </span>
              </b>
            </h1>
            <br />
          </div>
          <div class="row row-cards justify-content-lg-center">
            <div class="col-lg-12">
              <div class="row row-cards">
                <button-poli
                  v-bind:polis="daftar_poli"
                  v-for="poli in daftar_poli"
                  v-bind:poli="poli"
                  v-bind:is_prioritas="is_prioritas"
                  :key="poli.id"
                  @send="send"
                  @update_sim="update_sim"
                  @printing="printing"
                  @update_kuota="init_data"
                  @set_prioritas="set_prioritas"
                ></button-poli>

                <div
                  class="col-sm-6 col-md-6 col-poli"
                  :class="
                    Object.keys(daftar_poli).length % 4 > 0
                      ? 'col-lg-4'
                      : 'col-lg-6'
                  "
                  v-if="is_prioritas"
                >
                  <div class="card btn-poli" @click="set_prioritas(false)">
                    <div class="d-flex mt-5 mx-3 mb-2 align-items-center">
                      <h1 class="m-0">
                        <i class="fa fa-chevron-circle-right text-white"></i>
                        KEMBALI
                      </h1>
                    </div>
                    <div class="card-footer p-0 mx-3 mt-2 pt-1 mb-5">
                      <div class="row align-items-center pt-2 p-0 pb-4">
                        <div class="col d-flex" style="font-size:2vh">
                          <b
                            >Bukan pasien Lansia / Prioritas? <br />Klik tombol
                            ini untuk kembali</b
                          >
                          <i
                            class="fa fa-chevron-circle-right text-success d-block ml-auto"
                            style="font-size:3em!important"
                          ></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <br />
            </div>
          </div>
        </div>
      </div>
    </div>

    <footer
      class="footer cstm_footer_app"
      style="position: fixed; left: 0; bottom: 0; width: 100%"
    >
      <div class="container">
        <div class="row align-items-center">
          <div class="col-auto"></div>
          <div class="col-12 mt-2 mt-lg-0 text-center">
            <h5
              style="color:#495057;font-size:3.5vh!important;text-transform:uppercase;margin-bottom:-2px"
            >
              <time-component></time-component>
            </h5>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import HeaderComponent from "./commons/HeaderComponent.vue";
import TimeComponent from "./elements/TimeComponent.vue";
import Swal from "sweetalert2";
import moment from "moment";
moment.locale("id");

Vue.component("button-poli", {
  props: ["poli", "polis", "is_prioritas"],
  template: `<div class="col-sm-6 col-md-6 col-poli" :class="Object.keys(polis).length % 4>0? 'col-lg-4' : 'col-lg-6' " v-if="show_poli(poli)">
              <div class="card btn-poli"
                :class="'btn_poli'+poli.poli_id + (is_prioritas?' bg-primary' : ' bg-success') + (poli.tutup?' card-disabled' : '')"
                @click="choose(poli)">
                <div class="d-flex mt-5 mx-3 mb-2 align-items-center" :class="poli.tutup?'btn-tutup' : ''">                 
                  </span>
                  <div>
                    <h1 class="m-0 text-white"> <i :class="poli.poli_icon + ' '+ 'text-'+poli.poli_color" ></i>w {{poli.poli_nama}}</h1>
                  </div>
                </div>
                
                <div v-if="!poli.tutup" class="card-footer p-0 mx-3 mt-2 pt-1 mb-5" style="border-top: 1px solid rgb(255 255 255 / 52%);">
                  <div class="row align-items-center pt-2 p-0">
                    <div class="col text-white font-weight-bold" v-if="poli.poli_deskripsi!=''">
                      <span v-html="poli.poli_deskripsi" style="font-size:2vh" ></span>
                      <span v-if="is_prioritas" class="text-warning h3" style="font-size: 2vh"><br/><br/> <i class="fa fa-lightbulb-o"></i> KHUSUS LANSIA / PRIORITAS</span>
                      <span v-else><br/></span>
                    </div>
                    <div class="col" v-else>
                      <span v-if="is_prioritas" class="text-warning h3" style="font-size: 2vh"><br/><i class="fa fa-lightbulb-o"></i> KHUSUS LANSIA / PRIORITAS</span>
                      <span v-else><br/></span>
                    </div>
                  </div>
                </div>

                <div v-else class="card-footer p-0 mt-2 pt-1 pb-3" style="border-top: 1px solid rgb(255 255 255 / 52%);background:#fff">
                  <div class="row align-items-center mx-3 pt-3 p-0">
                    <div class="col font-weight-bold h4" style="font-size: 2vh" v-if="poli.poli_deskripsi!=''">
                      <i class="fa fa-info-circle mr-1 text-warning"></i>
                      <span v-if="poli.buka == 0">
                        <span v-if="poli.timeout">
                          {{poli.poli_deskripsi.toUpperCase()}}
                        </span>
                        <span v-else>
                          HARI INI POLI TUTUP
                        </span>
                      </span>
                      <span v-else>
                        <span v-if="poli.sisa_kuota <= 0">MAAF KUOTA POLI SUDAH HABIS</span>
                      </span>
                      <br/>
                    </div>
                  </div>
                </div>

              </div>
						</div>
				  	`,
  methods: {
    success_register(nomor, poli) {
      let self = this;
      $.LoadingOverlay("hide");

      setTimeout(() => {
        Swal.fire({
          title: '<b style="color:#0C0CBD;font-size:10vh">' + nomor + "</b>",
          html: "<b style='font-size:2.5vh'>Silakan ambil tiket Anda</b>",
          showConfirmButton: false,
          timer: 3000,

          imageUrl: "terimakasih.jpg",
          imageWidth: 180,
          imageHeight: 25,
          imageAlt: "Custom image",
          animation: false
        });

        self.$emit("update_sim", nomor, poli);
        self.$emit("send");
        self.$emit("printing", nomor, poli);
        self.$emit("update_kuota");
        self.$emit("set_prioritas", false);
      }, 200);
    },

    show_poli: function(poli) {
      let bool = true;
      if (
        this.is_prioritas &&
        (poli.poli_prioritas || poli.poli_hide_on_prioritas)
      ) {
        bool = false;
      }
      return bool;
    },

    choose(poli) {
      console.log(poli)
      alert('bentar')
      return
      if (poli.tutup) return false;

      if (poli.poli_prioritas === 1) {
        //pasien lansia
        this.$emit("set_prioritas", true);
      } else {
        $.LoadingOverlay("show");
        axios
          .post("api/pendaftaran/register", {
            ...poli,
            is_prioritas: this.is_prioritas
          })
          .then(response => this.success_register(response.data, poli))
          .finally(response => $.LoadingOverlay("hide"));
      }
    }
  }
});

export default {
  components: {
    TimeComponent,
    HeaderComponent
  },
  props: ["app_name", "app_address"],
  data() {
    return {
      ws: null,
      printer: null,
      service: null,
      daftar_poli: [],
      is_prioritas: false
    };
  },

  methods: {
    send() {
      this.ws.send(
        JSON.stringify({
          target: "loket",
          sub_target: "pendaftaran",
          action: "add_new"
        })
      );
      this.ws.send(
        JSON.stringify({
          target: "display",
          sub_target: "pendaftaran",
          action: "update_summary"
        })
      );
    },

    set_prioritas(bool) {
      this.is_prioritas = bool;
    },

    check_buka() {
      for (var x in this.daftar_poli) {
        this.daftar_poli[x].tutup = true;

        if (
          this.daftar_poli[x].buka > 0 &&
          this.daftar_poli[x].sisa_kuota > 0
        ) {
          this.daftar_poli[x].tutup = false;
        }
      }

      this.check_jam_tutup();
    },

    check_jam_tutup() {
      let self = this;
      let i = 1;
      setInterval(function() {
        for (var x in self.daftar_poli) {
          if (self.daftar_poli[x].buka > 0) {
            let id = self.daftar_poli[x].poli_id;
            let time = self.daftar_poli[x].poli_closehour;
            let hours = time.split(":");
            var timeNow = new Date();
            let jam = parseInt(hours[0], 10);
            let menit = parseInt(hours[1], 10);
            var timeNow = new Date();
            var timePoli = new Date(
              timeNow.getFullYear(),
              timeNow.getMonth(),
              timeNow.getDate(),
              jam,
              menit
            );

            if (timeNow > timePoli) {
              self.daftar_poli[x].tutup = true;
              self.daftar_poli[x].buka = 0;
              self.daftar_poli[x].poli_deskripsi = "waktu pelayanan habis";
              self.daftar_poli[x].timeout = true;
            }
          }
        }
        i++;
      }, 1000);
    },

    printing(number, poli) {
      axios
        .post(
          `${this.printer.printer_url_service}/register`,
          {
            poli_nama: poli.poli_nama,
            tiket_nomor: number,
            prioritas: this.is_prioritas,
            printer_alias: this.printer.printer_alias,
            date: moment().format("dddd, Do MMMM YYYY H:mm:ss")
          },
          {
            headers: {
              "X-Service-Key": document.head.querySelector(
                'meta[name="service-key"]'
              ).content
            }
          }
        )
        .then(response => {})
        .catch(response => {});
    },

    update_sim(nomor, poli) {
      if (this.service.service_enabled) {
        axios
          .post(`${this.service.service_url}/gateway/save`, {
            poli_id: poli.poli_id,
            tipe_antrian_id: poli.poli_id,
            nomor
          })
          .then(response => console.log(response));
      }
    },

    init_data() {
      let self = this;

      axios
        .post("api/poli/list-menu")
        .then(
          response => ((self.daftar_poli = response.data), self.check_buka(), console.log(response.data))
        );

      axios.post("api/printer/key/tiket").then(response => {
        this.printer = response.data.res;
      });

      axios.post("api/service/key/sim").then(response => {
        this.service = response.data.res;
      });
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
        if (data.target == "tiket_dispenser") {
          self.init_data();

          if (data.action == "refresh_browser") {
            location.reload(true);
          }
        }
      };
    }
  },

  created() {
    let self = this;
    this.ws_connect();
    const body = document.querySelector("body");
    body.classList.add("ticket-dispenser");
  }
};
</script>
