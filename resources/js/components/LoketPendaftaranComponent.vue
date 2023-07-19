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
              <h4 class="m-0">LOKET PENDAFTARAN</h4>
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

      <div class="col-md-6 col-lg-3">
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
                        >Nomor Antri <b>{{ antri.tiket_nomor }}</b></a
                      >
                    </div>
                    <small
                      class="d-block item-except text-sm text-muted h-1x"
                      v-if="antri.tiket_channel_id === '1'"
                      ><b>Jam Ambil Tiket :</b> {{ antri.tiket_created }}</small
                    >
                    <small
                      class="d-block item-except text-sm text-muted h-1x"
                      v-else
                      ><b>Tgl Ambil Tiket :</b>
                      {{ antri.tiket_date_created }}</small
                    >
                    <small
                      class="d-block item-except text-sm text-muted h-1x"
                      v-if="antri.tiket_channel_id !== 1"
                      ><b>Daftar melalui :</b>
                      <span
                        class="tag"
                        :class="
                          antri.tiket_channel_id === 1
                            ? 'text-info'
                            : 'text-danger'
                        "
                        >{{ antri.channel.channel_nama }}</span
                      >
                    </small>
                    <small class="d-block item-except text-sm text-muted h-1x"
                      ><b>Tujuan :</b> {{ antri.poli.poli_nama }}
                    </small>
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

      <div class="col-md-6 col-lg-4" v-if="antri_current">
        <div class="card">
          <div class="card-status bg-yellow"></div>
          <div class="card-header">
            <h3 class="card-title"><b>Antrian Sekarang</b></h3>
          </div>

          <div class="card-body text-center">
            <h3
              v-if="show_success_attend"
              style="font-size:5.1em;color:#CCC;margin:0"
            >
              {{ antri_current.tiket_nomor }}
            </h3>
            <h3 v-else style="font-size:5.1em;color:#FFA500;margin:0">
              {{ antri_current.tiket_nomor }}
            </h3>

            <small
              v-if="antri_current.tiket_channel_id === 1"
              class="d-block item-except text-sm text-muted m-0"
              ><b>Jam Ambil Tiket :</b>
              <span>{{ antri_current.tiket_created }}</span>
            </small>
            <small v-else class="d-block item-except text-sm text-muted m-0"
              ><b>Tgl Ambil Tiket :</b>
              <span>{{ antri_current.tiket_date_created }}</span>
            </small>
            <small
              class="d-block item-except text-sm text-muted m-0"
              v-if="antri_current.tiket_channel_id !== 1"
              ><b>Daftar melalui :</b>
              <span
                v-if="antri_current.channel"
                class="tag"
                :class="
                  antri_current.tiket_channel_id === 1
                    ? 'text-info'
                    : 'text-danger'
                "
              >
                {{ antri_current.channel.channel_nama }}
              </span>

              <span v-else>-</span>

              <button
                type="button"
                class="btn btn-sm"
                v-if="antri_current.tiket_pasien_nik"
                @click="show_detail_pasien(antri_current)"
              >
                <i class="fa fa-search" aria-hidden="true"></i>
              </button>
            </small>
          </div>
          <div class="card-footer">
            <div class="row align-items-center">
              <div class="col">
                <div>
                  <a class="text-inherit"
                    ><b>Tujuan :</b>
                    <span v-if="antri_current.poli">{{
                      antri_current.poli.poli_nama
                    }}</span>
                    <span v-else>-</span>
                  </a>
                </div>
                <small class="d-block item-except text-sm h-1x"
                  >Nomor Antri Poli :
                  <span v-if="show_success_attend"
                    ><b style="font-size:1.5em;color:#FFA500">{{
                      antri_current.tiket_poli_nomor
                    }}</b></span
                  >
                  <span v-else-if="antri_current.tiket_nomor == '-'">-</span>
                  <span v-else>belum hadir</span>
                </small>
              </div>
              <div class="col-auto" v-show="show_attend">
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
                <i class="fa fa-play mr-1"></i>Panggil Selanjutnya
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
                <i class="fa fa-check-circle mr-1"></i>Selesai
              </button>
            </span>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
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
                          antri.tiket_acceptor != null
                            ? 'Pendaftar hadir'
                            : 'Pendaftar tidak hadir'
                        "
                      >
                        <i
                          class="fa fa-check-circle text-success"
                          v-show="antri.tiket_acceptor != null"
                        ></i>
                        <i
                          class="fa fa-minus-circle text-danger"
                          v-show="antri.tiket_acceptor == null"
                        ></i>
                        Nomor Antri <b>{{ antri.tiket_nomor }}</b>
                      </a>

                      <div
                        class="dropdown pull-right"
                        v-if="antri.tiket_acceptor == null"
                      >
                        <button
                          type="button"
                          class="btn btn-secondary dropdown-toggle"
                          data-toggle="dropdown"
                          aria-expanded="false"
                        >
                          <i class="fa fa-user-times"></i>
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
                            href="javascript:void(0)"
                            @click="set_attend(antri, true)"
                            ><i class="fa fa-check text-success"></i> Ya,
                            Pendaftar Hadir</a
                          >
                        </div>
                      </div>
                    </div>
                    <small class="d-block item-except text-sm text-muted h-1x"
                      ><b>Tujuan :</b> {{ antri.poli.poli_nama }}</small
                    >
                    <small class="d-block item-except text-sm text-muted h-1x"
                      ><b>Jam Selesai :</b> {{ antri.tiket_accepted }}</small
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
                        <i>Belum ada antrian selesai.</i></a
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

      <div class="col-md-6 col-lg-2">
        <div class="card">
          <div class="card-status bg-blue"></div>
          <div class="card-header p-3">
            <h3 class="card-title"><b>Pasien Poli</b></h3>
          </div>

          <div class="card-body o-auto p-3" style="height: 17.4rem">
            <ul class="list-unstyled list-separated">
              <li class="p-0" v-for="(poli, key) in list_poli" :key="key">
                <div class="row align-items-center">
                  <div class="col">
                    {{ poli.poli_nama }}
                  </div>
                  <div class="col-auto">
                    <b>{{ poli.total }}</b>
                  </div>
                </div>
              </li>
              <li class="p-0">
                <div class="row align-items-center text-primary">
                  <div class="col font-weight-bold">
                    TOTAL
                  </div>
                  <div class="col-auto">
                    <b>{{ total_pendaftar_ulang }}</b>
                  </div>
                </div>
              </li>
            </ul>
          </div>

          <div class="card-footer"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";
import moment from "moment";
moment.locale("id");

export default {
  props: ["id_loket"],
  data() {
    return {
      ws: null,
      service: null,
      antri_waiting: [],
      antri_current: null,
      antri_completed: [],
      completed: [],
      list_poli: [],
      total_pendaftar_ulang: 0,
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
          i.tiket_nomor.toLowerCase().includes(newVal.toLowerCase())
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
      return this.antri_current.tiket_acceptor != null;
    },

    show_attend() {
      if (this.show_panggil_antrian) {
        return false;
      }
      return this.antri_current.tiket_nomor != "-";
    },

    show_panggil_antrian() {
      return this.antri_current.panggil_ulang === 0;
    },

    disabled_selesai() {
      return (
        this.antri_current.panggil_ulang === 0 ||
        this.antri_current.tiket_nomor == "-" ||
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
        this.antri_current.tiket_nomor == "-" ||
        this.disabled_call ||
        this.show_success_attend
      );
    }
  },

  methods: {
    parse_antri(data) {
      this.total_pendaftar = 0;
      this.antri_waiting = [];
      this.antri_current = {
        tiket_nomor: "-"
      };
      this.antri_completed = [];

      let current = true;
      let completed = [];
      for (let x in data) {
        this.total_pendaftar++;
        if (data[x].tiket_status == 0) {
          if (current) {
            this.antri_current = data[x];
            current = false;
          } else {
            this.antri_waiting.push(data[x]);
          }
        } else if (data[x].tiket_status > 0) {
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
        .post("pendaftaran/antrian")
        .then(response => vm.parse_antri(response.data));

      axios
        .post("poli/list")
        .then(
          response => (
            (vm.list_poli = response.data.list),
            (vm.total_pendaftar_ulang = response.data.total)
          )
        );

      axios.post("api/service/key/sim").then(response => {
        this.service = response.data.res;
      });
    },

    call_number(number) {
      this.ws.send(
        JSON.stringify({
          target: "display",
          sub_target: "pendaftaran",
          nomor: number,
          loket: this.id_loket == 0 ? "PENDAFTARAN" : this.id_loket
        })
      );
    },

    panggil_ulang() {
      this.antri_current.panggil_ulang++;
      let nomor = this.antri_current.tiket_nomor;
      let self = this;
      axios
        .post("pendaftaran/call", {
          nomor: nomor,
          call: this.antri_current.panggil_ulang
        })
        .then(response => {
          this.call_number(self.antri_current.tiket_nomor);
          this.panggil_gateway({
            poli_id: self.antri_current.tiket_poli_id,
            tipe_antrian_id: self.antri_current.tiket_poli_id,
            nomor,
            loket_id: self.id_loket
          });
          if (self.antri_current.panggil_ulang == 1) {
            this.ws.send(
              JSON.stringify({
                target: "display",
                sub_target: "pendaftaran",
                action: "update_summary"
              })
            );
          }
        });
      this.set_disabled_button(true);
    },

    panggil_gateway(params) {
      let self = this;
      if (this.service.service_enabled) {
        axios
          .post(`${this.service.service_url}/gateway/panggil`, {
            poli_id: params.poli_id,
            tipe_antrian_id: params.tipe_antrian_id,
            nomor: params.nomor,
            loket_id: params.loket_id
          })
          .then(response => {
            self.ws.send(
              JSON.stringify({
                target: "gateway",
                sub_target: "pendaftaran",
                loket_id: self.id_loket
              })
            );
          })
          .catch(err => console.log(err));
      }
    },

    set_attend(poli, value) {
      let self = this;
      axios.post("poli/register", { poli, value }).then(
        response => (
          self.init_data(),
          self.ws.send(JSON.stringify({ target: "tiket_dispenser" })),
          self.ws.send(
            JSON.stringify({ target: "loket", sub_target: "pendaftaran" })
          ),
          self.ws.send(
            JSON.stringify({
              target: "loket",
              sub_target: "poli",
              poli:
                self.antri_current.tiket_tensi_id ??
                self.antri_current.tiket_poli_id
            })
          )
        )
      );
    },

    panggil_selanjutnya() {
      let next = this.antri_waiting[0].tiket_nomor;

      if (this.antri_current.tiket_acceptor == null) {
        let nomor = this.antri_current.tiket_nomor;
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
              .post("pendaftaran/next", {
                poli: this.antri_current,
                next: next
              })
              .then(response => {
                this.call_number(next);
                this.init_data();
                this.ws.send(
                  JSON.stringify({
                    target: "loket",
                    sub_target: "pendaftaran"
                  })
                );
                this.ws.send(JSON.stringify({ target: "tiket_dispenser" }));
                this.panggil_gateway({
                  poli_id: this.antri_waiting[0].tiket_poli_id,
                  tipe_antrian_id: this.antri_waiting[0].tiket_poli_id,
                  nomor: next,
                  loket_id: this.id_loket
                });
                this.ws.send(
                  JSON.stringify({
                    target: "display",
                    sub_target: "pendaftaran",
                    action: "update_summary"
                  })
                );
                this.ws.send(
                  JSON.stringify({
                    target: "loket",
                    sub_target: "poli",
                    poli:
                      this.antri_current.tiket_tensi_id ??
                      this.antri_current.tiket_poli_id
                  })
                );
              });
          }
        });
      } else {
        this.set_disabled_button(true);
        axios
          .post("pendaftaran/next", { poli: this.antri_current, next: next })
          .then(response => {
            this.call_number(next);
            this.init_data();
            this.ws.send(
              JSON.stringify({ target: "loket", sub_target: "pendaftaran" })
            );
            this.ws.send(JSON.stringify({ target: "tiket_dispenser" }));
            this.panggil_gateway({
              poli_id: this.antri_waiting[0].tiket_poli_id,
              tipe_antrian_id: this.antri_waiting[0].tiket_poli_id,
              nomor: next,
              loket_id: this.id_loket
            });
            this.ws.send(
              JSON.stringify({
                target: "display",
                sub_target: "pendaftaran",
                action: "update_summary"
              })
            );
            this.ws.send(
              JSON.stringify({
                target: "loket",
                sub_target: "poli",
                poli:
                  this.antri_current.tiket_tensi_id ??
                  this.antri_current.tiket_poli_id
              })
            );
          });
      }
    },

    selesai() {
      if (this.antri_current.tiket_acceptor == null) {
        let nomor = this.antri_current.tiket_nomor;
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
            axios
              .post("pendaftaran/end", { poli: this.antri_current })
              .then(response => {
                this.init_data();
                this.ws.send(
                  JSON.stringify({
                    target: "loket",
                    sub_target: "pendaftaran"
                  })
                );
                this.ws.send(JSON.stringify({ target: "tiket_dispenser" }));
                this.ws.send(
                  JSON.stringify({
                    target: "loket",
                    sub_target: "poli",
                    poli:
                      this.antri_current.tiket_tensi_id ??
                      this.antri_current.tiket_poli_id
                  })
                );
              });
          }
        });
      } else {
        axios
          .post("pendaftaran/end", { poli: this.antri_current })
          .then(response => {
            this.init_data();
            this.ws.send(
              JSON.stringify({ target: "loket", sub_target: "pendaftaran" })
            );
            this.ws.send(JSON.stringify({ target: "tiket_dispenser" }));
            this.ws.send(
              JSON.stringify({
                target: "loket",
                sub_target: "poli",
                poli:
                  this.antri_current.tiket_tensi_id ??
                  this.antri_current.tiket_poli_id
              })
            );
          });
      }
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
        if (data.target == "loket") {
          if (data.action == "refresh_browser") {
            location.reload(true);
          }

          if (data.sub_target == "pendaftaran") {
            self.init_data();

            if (data.gedung === 1) {
              self.disabled_call = data.disabled_call ? true : false;
            }

            if (data.nomor === self.antri_current.tiket_nomor) {
              self.set_attend(self.antri_current, true);
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
    },

    show_detail_pasien(tiket) {
      axios
        .post(`pendaftaran/detail/${tiket.tiket_pasien_nik}`)
        .then(response => response.data)
        .then(data => {
          console.log(this.$data);

          let content = `<table class="table text-left">
            <tr>
              <td width="40%">NIK</td>
              <td width="60%">${data.pasien_nik}</td>
            </tr>
            <tr>
              <td>Nama</td>
              <td>${data.pasien_nama}</td>
            </tr>
            ${
              data.pasien_birth_date
                ? `<tr>
              <td>Tanggal Lahir</td>
              <td>${data.pasien_birth_date}</td>
            </tr>`
                : ""
            }       
            ${
              data.pasien_address
                ? `<tr>
              <td>Alamat</td>
              <td>${data.pasien_address}</td>
            </tr>`
                : ""
            }
          </table>`;
          Swal.fire({
            title: "Data Pasien",
            html: content
          });
        });
    }
  },

  created() {
    let self = this;
    console.log(self.id_loket)
    this.ws_connect();
  }
};
</script>
