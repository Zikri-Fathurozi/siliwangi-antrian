<template>
  <div class="container app_panel">
    <div class="row row-cards">
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-blue mr-3">
              <i class="fa fa-home"></i>
            </span>
            <div>
              <h4 class="m-0">{{ poli.poli_nama }}</h4>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-yellow mr-3">
              <i class="fa fa-users"></i>
            </span>
            <div>
              <h4 class="m-0">
                {{ total_pendaftar }} <small>Total Pendaftar</small>
              </h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-red mr-3">
              <i class="fa fa-stack-overflow"></i>
            </span>
            <div>
              <h4 class="m-0">
                {{ antri_waiting.length }} <small>Mengantri</small>
              </h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-green mr-3">
              <i class="fa fa-check"></i>
            </span>
            <div>
              <h4 class="m-0">
                {{ antri_completed.length }} <small>Selesai</small>
              </h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card">
          <div class="card-status bg-blue"></div>
          <div class="card-header">
            <h3 class="card-title"><b>Sedang Mengantri</b></h3>
            <div class="card-options">
              <span class="badge badge-danger">{{ antri_waiting.length }}</span>
            </div>
          </div>
          <div class="card-body o-auto" style="height: 17.4rem">
            <ul class="list-unstyled list-separated">
              <li
                class="list-separated-item"
                v-for="(antri, key) in antri_waiting"
                :key="key"
              >
                <div class="row align-items-center">
                  <div class="col">
                    <div>
                      <a class="text-inherit"
                        >Nomor Antri <b>{{ antri.tiket_tensi_nomor }}</b></a
                      >
                    </div>
                    <small class="d-block item-except text-sm text-muted h-1x"
                      ><b>Jam Registrasi :</b> {{ antri.tiket_accepted }}</small
                    >
                  </div>
                </div>
              </li>

              <li
                class="list-separated-item"
                v-show="antri_waiting.length === 0"
              >
                <div class="row align-items-center">
                  <div class="col">
                    <div>
                      <a class="text-inherit"
                        ><i class="fa fa-check-circle text-success"></i>
                        <i>Tidak ada antrian.</i></a
                      >
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-footer"></div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card">
          <div class="card-status bg-yellow"></div>
          <div class="card-header">
            <h3 class="card-title"><b>Antrian Sekarang</b></h3>
          </div>

          <div class="card-body text-center">
            <h3
              v-if="show_success_attend"
              style="font-size:6.2em;color:#CCC;margin:0"
            >
              {{ antri_current.tiket_nomor }}
            </h3>
            <h3 v-else style="font-size:6.2em;color:#FFA500;margin:0">
              {{ antri_current.tiket_nomor ? antri_current.tiket_nomor : "-" }}
            </h3>
          </div>

          <div class="card-footer">
            <div class="row align-items-center">
              <div class="col-6">
                <small class="d-block item-except text-sm m-0"
                  ><b>Registrasi :</b>
                  {{ antri_current.tiket_accepted }}
                </small>
              </div>

              <div class="col-6">
                <small class="d-block item-except text-sm m-0"
                  ><b>Nomor Antri :</b>
                  {{ antri_current.tiket_nomor }}
                </small>
              </div>

              <div class="col-6"></div>
              <div class="col-6 mt-2" v-show="show_attend">
                <div class="dropdown">
                  <button
                    type="button"
                    class="btn btn-secondary dropdown-toggle"
                    data-toggle="dropdown"
                    aria-expanded="false"
                  >
                    <div class="tag tag-success" v-if="show_success_attend">
                      hadir
                      <span class="tag-addon"><i class="fa fa-check"></i></span>
                    </div>
                    <span v-else>
                      Kehadiran
                    </span>
                    <i class="fa fa-user" v-if="show_success_attend"></i>
                    <i class="fa fa-user-times" v-else></i>
                  </button>
                  <div
                    class="dropdown-menu"
                    x-placement="bottom-start"
                    style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                  >
                    <div class="dropdown-item disabled">
                      Apakah pendaftar hadir?
                    </div>
                    <div class="dropdown-divider"></div>
                    <a
                      class="dropdown-item"
                      v-if="show_success_attend"
                      href="javascript:void(0)"
                      @click="set_attend(antri_current, false)"
                      ><i class="fa fa-close text-danger"></i> Tidak Hadir</a
                    >
                    <a
                      class="dropdown-item"
                      v-else
                      href="javascript:void(0)"
                      @click="set_attend(antri_current, true)"
                      ><i class="fa fa-check text-success"></i> Ya, Pendaftar
                      Hadir</a
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer p-3">
            <button
              type="button"
              @click="panggil_ulang()"
              :disabled="disabled_panggil_ulang"
              class="btn bg-warning"
              id="panggil_ulang"
            >
              <span v-if="show_panggil_antrian"
                ><i class="fa fa-volume-up"></i> Panggil Antrian</span
              >
              <span v-else><i class="fa fa-refresh"></i> Panggil Ulang </span>
              <span v-if="antri_current.panggil_ulang > 0"
                >({{ antri_current.panggil_ulang }})</span
              >
            </button>
            <span v-if="antri_waiting.length > 0">
              <button
                type="button"
                @click="panggil_selanjutnya()"
                :disabled="disabled_panggil_selanjutnya"
                class="btn btn-success"
                id="panggil_selanjutnya"
              >
                <i class="fa fa-play mr-2"></i>Panggil Selanjutnya
              </button>
            </span>
            <span v-else>
              <button
                type="button"
                @click="selesai()"
                :disabled="disabled_selesai"
                class="btn btn-success"
                id="selesai"
              >
                <i class="fa fa-check-circle mr-2"></i>Selesai
              </button>
            </span>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card">
          <div class="card-status bg-green"></div>
          <div class="card-header">
            <h3 class="card-title"><b>Selesai</b></h3>
            <div class="card-options">
              <span class="badge badge-success">{{
                antri_completed.length
              }}</span>
            </div>
          </div>
          <div class="card-body o-auto" style="height: 15rem">
            <ul class="list-unstyled list-separated">
              <li
                class="list-separated-item"
                v-for="(antri, key) in antri_completed"
                :key="key"
              >
                <div class="row align-items-center">
                  <div class="col">
                    <div>
                      <a
                        class="text-inherit"
                        :title="
                          antri.tiket_tensi_status == 1
                            ? 'Pendaftar hadir'
                            : 'Pendaftar tidak hadir'
                        "
                      >
                        <i
                          class="fa fa-check-circle text-success"
                          v-show="antri.tiket_tensi_status == 1"
                        ></i>
                        <i
                          class="fa fa-minus-circle text-danger"
                          v-show="antri.tiket_tensi_status == 2"
                        ></i>
                        Nomor Antri <b>{{ antri.tiket_tensi_nomor }}</b>
                      </a>
                    </div>

                    <div class="dropdown pull-right" style="margin-top:-20px">
                      <button
                        type="button"
                        class="btn btn-secondary"
                        data-toggle="dropdown"
                        aria-expanded="false"
                      >
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                      <div
                        class="dropdown-menu"
                        x-placement="bottom-start"
                        style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"
                      >
                        <a
                          class="dropdown-item"
                          v-if="antri.tiket_tensi_status == 1"
                          href="javascript:void(0)"
                          @click="set_attend(antri, false)"
                          ><i class="fa fa-close text-danger"></i> Klik, jika
                          tidak Hadir</a
                        >
                        <a
                          class="dropdown-item"
                          v-else
                          href="javascript:void(0)"
                          @click="set_attend(antri, true)"
                          ><i class="fa fa-check text-success"></i> Klik, jika
                          pasien Hadir</a
                        >
                      </div>
                    </div>

                    <small
                      class="d-block item-except text-sm text-muted h-1x"
                      v-if="antri.tiket_tensi_status == 1"
                      ><b>Jam Dilayani :</b>
                      {{ antri.tiket_tensi_accepted }}</small
                    >
                    <small
                      class="d-block item-except text-sm text-muted h-1x"
                      v-else
                      ><b>Pasien tidak Hadir</b></small
                    >
                  </div>
                </div>
              </li>

              <li
                class="list-separated-item"
                v-show="antri_completed.length === 0"
              >
                <div class="row align-items-center">
                  <div class="col">
                    <div>
                      <a class="text-inherit"
                        ><i class="fa fa-info-circle text-info"></i>
                        <i v-if="cari == ''">Belum ada antrian selesai.</i
                        ><i v-else>Nomor Antrian tidak ditemukan.</i></a
                      >
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-footer">
            <input
              type="text"
              class="form-control"
              v-model="cari"
              placeholder="Cari nomor Antrian"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";
import moment from "moment";
import VueMask from "v-mask";
Vue.use(VueMask);

moment.locale("id");

export default {
  props: ["id_tensi"],
  data() {
    return {
      ws: null,
      poli: { poli_nama: "" },
      antri_waiting: [],
      antri_current: {},
      antri_completed: [],
      completed: [],
      disabled_call: false,
      total_pendaftar: 0,
      cari: ""
    };
  },
  watch: {
    cari: function(newVal, oldVal) {
      // watch it
      if (newVal != "" && this.completed.length > 0) {
        this.antri_completed = this.completed.filter(i =>
          i.tiket_tensi_nomor.toLowerCase().includes(newVal.toLowerCase())
        );
      } else {
        this.antri_completed = this.completed;
      }
    },
    disabled_call: function(val) {
      this.set_disabled_button(val);
    }
  },
  computed: {
    show_success_attend() {
      return this.antri_current.tiket_tensi_acceptor != null;
    },

    show_attend() {
      if (this.show_panggil_antrian) {
        return false;
      }
      return this.antri_current.tiket_tensi_nomor != "-";
    },

    show_panggil_antrian() {
      return this.antri_current.panggil_ulang === 0;
    },

    disabled_selesai() {
      return (
        this.antri_current.panggil_ulang === 0 ||
        this.antri_current.tiket_tensi_nomor == "-" ||
        this.antri_waiting.length > 0 ||
        this.disabled_call
      );
    },

    disabled_panggil_selanjutnya() {
      return (
        this.antri_current.panggil_ulang === 0 ||
        this.antri_waiting.length === 0 ||
        this.disabled_call
      );
    },

    disabled_panggil_ulang() {
      return (
        this.antri_current.tiket_tensi_nomor == "-" ||
        this.disabled_call ||
        this.show_success_attend
      );
    }
  },

  methods: {
    save_config() {
      let self = this;
      axios.post("tensi/save", { poli: this.poli }).then(response => {
        if (response.data.res == "SUCCESS") {
          self.init_data();

          Swal.fire({
            type: "success",
            title: "Pengaturan berhasil disimpan",
            showConfirmButton: false,
            timer: 1000
          });

          self.ws.send(JSON.stringify({ target: "tiket_dispenser" }));
        }
      });
    },

    parse_antri(data) {
      this.total_pendaftar = 0;
      this.antri_waiting = [];
      this.antri_current = {
        tiket_tensi_nomor: "-"
      };
      this.antri_completed = [];

      let current = true;
      let completed = [];
      for (let x in data) {
        this.total_pendaftar++;
        if (data[x].tiket_tensi_status == 0) {
          if (current) {
            this.antri_current = data[x];
            current = false;
          } else {
            this.antri_waiting.push(data[x]);
          }
        } else if (data[x].tiket_tensi_status > 0) {
          completed.push(data[x]);
        }
      }

      for (let x = completed.length - 1; x >= 0; x--) {
        this.antri_completed.push(completed[x]);
      }

      this.completed = this.antri_completed;
    },

    init_data() {
      var vm = this;

      axios
        .post("tensi/antrian")
        .then(response => vm.parse_antri(response.data));

      axios
        .post("poli/get/" + this.id_tensi)
        .then(response => (this.poli = response.data));
    },

    call_number(number) {
      this.ws.send(
        JSON.stringify({
          target: "display",
          sub_target: "poli",
          poli: this.id_tensi,
          nomor: number
        })
      );
    },

    panggil_ulang() {
      let nomor = this.antri_current.tiket_tensi_nomor;
      this.antri_current.panggil_ulang++;
      axios
        .post("tensi/call", {
          nomor: nomor,
          call: this.antri_current.panggil_ulang
        })
        .then(response => {
          this.call_number(this.antri_current.tiket_tensi_nomor);
          if (this.antri_current.panggil_ulang == 1) {
            this.ws.send(
              JSON.stringify({
                target: "display",
                sub_target: "poli",
                action: "update_summary"
              })
            );
          }
        });
      this.set_disabled_button(true);
    },

    panggil_selanjutnya() {
      let next = this.antri_waiting[0].tiket_tensi_nomor;
      if (this.antri_current.tiket_tensi_acceptor == null) {
        let nomor = this.antri_current.tiket_tensi_nomor;
        let title = "Konfirmasi";
        let question =
          "<b style='color:#FF0000!important'><button type='button' class='btn btn-secondary dropdown-toggle'><i class='fa fa-user-times'></i></button> Tanda pendaftar hadir belum dicentang.</b><br/>Apakah antrian " +
          nomor +
          " tidak hadir?<br/><br/>";

        Swal.fire({
          title: title,
          html: question,
          width: "60%",
          showCancelButton: true,
          cancelButtonText: "Tutup",
          confirmButtonText: `Ya ${nomor}  tidak hadir, Panggil nomor selanjutnya (${next})`
        }).then(result => {
          if (result.value) {
            this.set_disabled_button(true);
            axios
              .post("tensi/next", { poli: this.antri_current, next: next })
              .then(
                response => (
                  this.call_number(next),
                  this.init_data(),
                  this.ws.send(
                    JSON.stringify({
                      target: "loket",
                      sub_target: "poli",
                      poli: this.antri_current.tiket_poli_id
                    })
                  ),
                  this.ws.send(JSON.stringify({ target: "tiket_dispenser" })),
                  this.ws.send(
                    JSON.stringify({
                      target: "display",
                      sub_target: "poli",
                      action: "update_summary"
                    })
                  )
                )
              );
          }
        });
      } else {
        this.set_disabled_button(true);
        axios.post("tensi/next", { poli: this.antri_current, next: next }).then(
          response => (
            this.call_number(next),
            this.init_data(),
            this.ws.send(
              JSON.stringify({
                target: "loket",
                sub_target: "poli",
                poli: this.antri_current.tiket_poli_id
              })
            ),
            this.ws.send(JSON.stringify({ target: "tiket_dispenser" })),
            this.ws.send(
              JSON.stringify({
                target: "display",
                sub_target: "poli",
                action: "update_summary"
              })
            )
          )
        );
      }
    },

    selesai() {
      const self = this;

      if (self.antri_current.tiket_tensi_acceptor == null) {
        let nomor = self.antri_current.tiket_tensi_nomor;
        let title = "Konfirmasi";
        let question =
          "<b style='color:#FF0000!important'><button type='button' class='btn btn-secondary dropdown-toggle'><i class='fa fa-user-times'></i></button> Tanda pendaftar hadir belum dicentang.</b><br/>Apakah antrian " +
          nomor +
          " tidak hadir?<br/><br/>";

        Swal.fire({
          title: title,
          html: question,
          width: "50%",
          showCancelButton: true,
          confirmButtonText: "Ya"
        }).then(result => {
          if (result.value) {
            axios.post("tensi/end", { poli: self.antri_current }).then(
              response => (
                self.init_data(),
                self.ws.send(
                  JSON.stringify({
                    target: "loket",
                    sub_target: "poli",
                    poli: self.antri_current.tiket_poli_id
                  })
                ),
                self.ws.send(JSON.stringify({ target: "tiket_dispenser" }))
              )
            );
          }
        });
      } else {
        axios.post("tensi/end", { poli: self.antri_current }).then(
          response => (
            self.init_data(),
            self.ws.send(
              JSON.stringify({
                target: "loket",
                sub_target: "poli",
                poli: self.antri_current.tiket_poli_id
              })
            ),
            self.ws.send(JSON.stringify({ target: "tiket_dispenser" }))
          )
        );
      }
    },

    set_attend(poli, value) {
      console.log(poli);
      let self = this;
      axios.post("tensi/attend", { poli, value }).then(response => {
        self.init_data();
        self.ws.send(JSON.stringify({ target: "tiket_dispenser" }));
        self.ws.send(
          JSON.stringify({
            target: "loket",
            sub_target: "poli",
            poli: poli.tiket_poli_id
          })
        );
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
        console.log(data);
        if (data.target == "loket") {
          if (data.action == "refresh_browser") {
            location.reload(true);
          }

          if (data.sub_target == "poli") {
            if (data.gedung == self.poli.poli_gedung) {
              self.disabled_call = data.disabled_call ? true : false;
            }

            if (data.action == "add_new") {
              if (self.btnSelesaiClick) {
                Swal.closeModal();
              }
            }

            if (data.poli == self.id_tensi) {
              self.init_data();
            }
          }
        }
      };
    },

    set_disabled_button(bool) {
      if (bool) {
        $("#panggil_ulang").attr("disabled", "disabled");
        $("#panggil_selanjutnya").attr("disabled", "disabled");
        $("#selesai").attr("disabled", "disabled");
      } else {
        $("#panggil_ulang").removeAttr("disabled");
        if (this.antri_waiting.length > 0)
          $("#panggil_selanjutnya").removeAttr("disabled");
        else $("#selesai").removeAttr("disabled");
      }
    }
  },

  created() {
    let self = this;
    this.ws_connect();
  }
};
</script>
