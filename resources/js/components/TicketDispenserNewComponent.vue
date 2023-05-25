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
            <h1 style="font-size:4.3vh">
              <b><span>
                ANTREAN PENDAFTARAN
                <!-- <span v-if="is_prioritas">
                  KHUSUS LANSIA / PRIORITAS
                </span>
                <span v-else>
                  PENDAFTARAN POLI
                </span> -->
              </span></b>
            </h1>
          </div>
          <div class="row row-cards justify-content-lg-center">
            <div class="col-lg-12">

              <div class="col-lg-10 offset-lg-1" v-if="positionDisplay == '0'">
                <div class="row">
                  <!-- Start Choose Type Pasien -->
                  <div class="col-sm-6 col-lg-6 align-self-center">
                    <div class="card mx-auto target-type-patient" style="width: 18rem;" @click="typePatient('BPJS')">
                      <div class="icon-pasien">
                        <img src="bpjs-logo.png" class="card-img-top img-fluid" alt="...">
                      </div>
                      <div class="card-body text-center">
                        <h1>BPJS LAMA</h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-6 align-self-center">
                    <div class="card mx-auto target-type-patient" style="width: 18rem;" @click="typePatient('UMUM')">
                      <div class="icon-pasien">
                        <img src="logo-umum.png" class="card-img-top img-fluid" alt="...">
                      </div>
                      <div class="card-body text-center">
                        <h1>UMUM/BARU</h1>
                      </div>
                    </div>
                  </div>
                  <!-- End Choose Type Pasien -->
                </div>
              </div>

              <div class="col-lg-10 offset-lg-1" v-if="positionDisplay == '1'">
                <div class="row">
                  <div class="col-md-6 col-md-offset-1 mx-auto" style="min-height: 100vh;">
                      <h4><a href="/ticket-dispenser"><i class="fa fa-arrow-left"></i>&nbsp;<b>Kembali</b></a></h4>
                      <div class="row d-flex justify-content-center padding-50">
                          <img src="logo.png" width="20%" id="logo_antrean"/>
                      </div>
                      <br>
                      <div class="row">   
                          <div class="col-sm-12 text-center">
                            <h3><b>{{ app_name }}</b></h3>
                          </div>
                          <div class="col-sm-12 text-center">
                              <p><i id="infoText">Silahkan Scan Kartu BPJS Anda atau Ketik Nomor BPJS / No. KTP</i></p>
                          </div>
                          <div class="col-lg-12" style="padding: 0px 50px 0px 50px;">
                              <input type="text" v-model="nomer_peserta" class="form-control input-big text-center" autocomplete="off"> 
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6 mx-auto" style="min-height: 100vh;">
                    <div class="d-flex justify-content-center" style="padding-top: 8%;">
                      <div class="buttons mx-auto" >
                        <div class="row">
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b7" value="7" @click="checkNumber(7)"/>
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b8" value="8" @click="checkNumber(8)"/>
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b9" value="9" @click="checkNumber(9)"/>
                        </div>
                        
                        <div class="row">
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b4" value="4" @click="checkNumber(4)"/>
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b5" value="5" @click="checkNumber(5)"/>
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b6" value="6" @click="checkNumber(6)"/>
                        </div>
                        
                        <div class="row">
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b1" value="1" @click="checkNumber(1)"/>
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b2" value="2" @click="checkNumber(2)"/>
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b3" value="3" @click="checkNumber(3)"/>
                        </div>
                        
                        <div class="row">
                          <button type="button" @click="resetNumber()" class="btn btn-lg b_cari btn-big btn-warning shadow-action"><i class="fa fa-arrow-left" id="icon-size"></i></button>
                          <input type="button" class="btn btn-lg btn-number btn-number-white shadow-button" name="b0" value="0" @click="checkNumber(0)"/>
                          <button type="button" @click="searchNumber()" class="btn btn-lg b_cari btn-big btn-warning shadow-action"><i class="fa fa-search" id="icon-size"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row row-cards" v-if="positionDisplay == '2'">
                <button-poli
                  v-bind:polis="daftar_poli"
                  v-for="poli in daftar_poli"
                  v-bind:poli="poli"
                  v-bind:is_prioritas="is_prioritas"
                  v-bind:nomer_peserta="nomer_peserta"
                  :key="poli.id"
                  @send="send"
                  @update_sim="update_sim"
                  @printing="printing"
                  @update_kuota="init_data"
                  @set_prioritas="set_prioritas"
                ></button-poli>

                <div
                  class="col-sm-6 col-md-6 col-poli"
                  :class="Object.keys(daftar_poli).length % 4 > 0 ? 'col-lg-4' : 'col-lg-6'" 
                  v-if="is_prioritas">
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
      style="position: fixed; left: 0; bottom: 0; width: 100%">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-auto"></div>
          <div class="col-12 mt-2 mt-lg-0 text-center">
            <h5 style="color:#495057;font-size:3.5vh!important;text-transform:uppercase;margin-bottom:-2px">
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
  props: ["poli", "polis", "is_prioritas", "nomer_peserta"],
  template: `
    <div class="col-sm-6 col-md-6 col-poli" :class="Object.keys(polis).length % 4>0? 'col-lg-4' : 'col-lg-6' " v-if="show_poli(poli)">
      <div class="card btn-poli" :class="'btn_poli'+poli.poli_id + (is_prioritas?' bg-primary' : ' bg-success') + (poli.tutup?' card-disabled' : '')" @click="choose(poli)">
        <div class="d-flex mt-5 mx-3 mb-2 align-items-center" :class="poli.tutup?'btn-tutup' : ''"></span>
          <div>
            <h1 class="m-0 text-white"> <i :class="poli.poli_icon + ' '+ 'text-'+poli.poli_color" ></i> {{poli.poli_nama}} </h1>
          </div>
        </div>
        
        <div v-if="!poli.tutup" class="card-footer p-0 mx-3 mt-2 pt-1 mb-5" style="border-top: 1px solid rgb(255 255 255 / 52%);">
          <div class="row align-items-center pt-2 p-0">
            <div class="col text-white font-weight-bold" v-if="poli.poli_deskripsi!=''">
              <span v-html="poli.poli_deskripsi" style="font-size:2vh"></span>
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
                <span v-if="poli.timeout">{{ poli.poli_deskripsi.toUpperCase() }}</span>
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
        self.$parent.positionDisplay = 0
        self.$parent.nomer_peserta = ''
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
      if (poli.tutup) return false;
      if (poli.poli_prioritas === 1) {
        //pasien lansia
        this.$emit("set_prioritas", true);
      } else {
        $.LoadingOverlay("show");
        axios
          .post("api/pendaftaran/register", {
            is_prioritas: this.is_prioritas,
            nomer_peserta: this.nomer_peserta,
            ...poli,
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
      is_prioritas: false,
      positionDisplay: 0,
      nomer_peserta: ''
    };
  },
  methods: {
    typePatient(type) {
      if (type == 'BPJS') {
        this.positionDisplay = '1'
      } else {
        // UMUM
        this.positionDisplay = 2
      }
    },
    checkNumber(value) {
      if (this.nomer_peserta.length > 16) {
        alert('Nomer BPJS(13) / NIK(16) digit melebihi batas!')
        return
      }
      this.nomer_peserta += value.toString()
    },
    resetNumber() {
      this.nomer_peserta = ''
    },
    searchNumber() {
      if (this.nomer_peserta.length === 16 || this.nomer_peserta.length === 13) {
        $(".app_panel").LoadingOverlay("show", {
          image: "",
          text: "Mencari pasien..",
          textColor: "#9C9999"
        })
        axios.post(document.head.querySelector('meta[name="base-url"]').content + '/api/get-peserta', {
          no_asuransi: this.nomer_peserta
        }, {
          headers: {
            "X-Service-Key": document.head.querySelector(
              'meta[name="service-key"]'
            ).content
          }
        }).then(response => {
          $(".app_panel").LoadingOverlay("hide", true)
          var peserta = response.data.response
          if (response.data.cerrorcode == 'timeout') {
            Swal.fire({
              icon: 'error',
              title: 'Pencarian Pasien BPJS',
              html: response.data.message,
              showConfirmButton: true,
            })
          } else {
            if (peserta == null) {
              Swal.fire({
                icon: 'error',
                title: 'Pencarian Pasien BPJS',
                html: "Mohon maaf, <br>nomor BPJS anda salah. <br><br>Silahkan mengambil <br>antrian <b style='color:red'>PASIEN BARU</b> <br>untuk melakukan pendaftaran <br>melalui LOKET PENDAFTARAN",
                confirmButtonText: 'Kembali',
              }).then((result) => {
                this.nomer_peserta = ''
              })
            } else {
              if (peserta.ketAktif == 'AKTIF' && peserta.status) {
                this.nomer_peserta = peserta.noKartu
                
                var pstPrbValue = peserta.pstPrb ?? 'Bukan'
                var pstProlValue = peserta.pstProl ?? 'Bukan'
                var nama_asuransi = peserta.asuransi.nmAsuransi == null ? 'BPJS Kesehatan' : peserta.asuransi.nmAsuransi
                var no_asuransi = peserta.noKartu == null ? '-' : peserta.noKartu
                var status_bpjs = peserta.ketAktif
                var nama_peserta_bpjs = peserta.nama
                var faskes_bpjs = peserta.kdProviderPst.nmProvider
                var kelasbpjs = peserta.jnsKelas.nama
                var jenisbpjs = peserta.jnsPeserta.nama
                var prolanis = pstProlValue+" / "+pstPrbValue 
                var dataPasien = `
                  Nomer Asuransi: ${no_asuransi}<br>
                  Nama Asuransi: ${nama_asuransi}<br>
                  Nama Pasien: ${nama_peserta_bpjs}<br>
                  Status BPJS: ${status_bpjs}<br>
                  Faskes BPJS: ${faskes_bpjs}<br>
                  Kelas: ${kelasbpjs}<br>
                  Jenis BPJS: ${jenisbpjs}<br>
                  Peserta Prolanis: ${prolanis}<br>
                `
                Swal.fire({
                  icon: 'success',
                  title: 'Data Peseta',
                  html: dataPasien,
                  showDenyButton: true,
                  showCancelButton: true,
                  confirmButtonText: 'Benar, lanjutkan',
                  denyButtonText: `Bukan`,
                }).then((result) => {
                  if (result.value) {
                    this.positionDisplay = 2
                  } else if (result.dismiss == 'cancel') {
                    Swal.fire('Jika data salah, silahkan hubungi petugas', '', 'info')
                    this.positionDisplay = 0
                    this.nomer_peserta = ''
                  }
                })
              } else if(peserta.ketAktif == 'AKTIF' && !peserta.status) {
                Swal.fire({
                  icon: 'error',
                  title: 'Pencarian Pasien BPJS',
                  html: "Mohon maaf, <br>Anda sudah terdaftar <br>tetapi faskes BPJS anda di "+peserta.kdProviderPst.nmProvider+", <BR><BR>Silahkan mengambil antrian <br><b style='color:red'>UMUM/BARU</b> untuk melakukan pendaftaran",
                  confirmButtonText: 'Kembali',
                }).then((result) => {
                  this.nomer_peserta = ''
                  this.positionDisplay = 0
                })
              } else if (peserta.aktif == false ) {
                Swal.fire({
                  icon: 'error',
                  title: 'Pencarian Pasien BPJS',
                  html: "Anda sudah terdaftar<br> tetapi BPJS anda TIDAK AKTIF, <BR><BR>Silahkan mengambil antrian <br><b style='color:red'>UMUM/BARU</b><br> untuk melakukan pendaftaran",
                  confirmButtonText: 'Kembali',
                }).then((result) => {
                  this.nomer_peserta = ''
                  this.positionDisplay = 0
                })
              }
            }
          }
        })
        .catch(response => {})
      } else {
        alert('Nomer kartu harus digit BPJS(13) / NIK(16)')
        return
      }
    },
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
          response => ((self.daftar_poli = response.data), self.check_buka())
        );
      axios.post("api/printer/key/tiket").then(response => {
        this.printer = response.data.res;
      });
      axios.post("api/service/key/sim").then(response => {
        this.service = response.data.res;
        console.log(response.data.res)
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
      self.init_data();
      this.ws = new WebSocket(
        document.head.querySelector('meta[name="web-socket"]').content
      );
      this.ws.onopen = function() {
        // $(".app_panel").LoadingOverlay("hide", true);
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