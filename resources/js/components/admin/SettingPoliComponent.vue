<template>
  <div class="container">
    <div class="row row-cards">
      <div class="col-lg-12" v-show="!showForm">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Pengaturan Poli
              <span class="badge badge-primary">{{
                Object.keys(list_poli).length
              }}</span>
            </h3>
            <div class="card-options">
              <a
                href="javascript:void(0)"
                @click="add"
                class="btn btn-secondary btn-sm"
                >Tambah Poli</a
              >
            </div>
          </div>

          <div class="table-responsive">
            <table class="table card-table table-striped table-vcenter">
              <thead>
                <tr>
                  <th></th>
                  <th>Nama Poli</th>
                  <th>Deskripsi</th>
                  <th>Kuota</th>
                  <th>Buka Hari</th>
                  <th>Jam Tutup Pendaftaran</th>
                  <th>Prioritas?</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in list_poli" :key="p.id">
                  <td>
                    <div
                      class="w-3 h-3 bg-secondary rounded"
                      :class="'bg-' + p.poli_color"
                    ></div>
                  </td>
                  <td>{{ p.poli_nama }}</td>
                  <td>{{ p.poli_deskripsi }}</td>
                  <td>{{ p.poli_kuota }}</td>
                  <td>
                    <div class="dropdown">
                      <button
                        type="button"
                        class="btn btn-secondary dropdown-toggle"
                        data-toggle="dropdown"
                      >
                        <i class="fa fa-calendar mr-2"></i>Lihat disini
                      </button>
                      <div class="dropdown-menu">
                        <a
                          class="dropdown-item"
                          v-for="(day, index) in p.poli_dayopen"
                          :key="index"
                          href="javascript:void(0)"
                          >{{ hari[day] }}</a
                        >
                      </div>
                    </div>
                  </td>
                  <td>{{ p.poli_closehour }}</td>
                  <td>
                    <i
                      class="fa fa-check-square text-success"
                      v-if="p.poli_prioritas"
                    ></i>
                    <i v-else class="fa fa-close text-black"></i>
                  </td>
                  <td>{{ p.poli_status == 1 ? "Aktif" : "Nonaktif" }}</td>
                  <td>
                    <a href="javascript:void(0)" @click="edit(p)" class="icon"
                      ><i class="fa fa-pencil-square fa-lg text-info"></i></a
                    >&nbsp;&nbsp;
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-12" v-show="showForm">
        <form @submit="checkForm">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Form Poli</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-label"
                        >Nama Poli <span class="form-required">*</span></label
                      >
                      <input
                        type="text"
                        v-model="poliSelected.poli_nama"
                        class="form-control"
                        :class="invalidName ? 'is-invalid' : ''"
                        placeholder="Nama Poli"
                      />
                      <div class="invalid-feedback" v-show="errors.poli_nama">
                        {{ errors.poli_nama }}
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-label">Deskripsi</label>
                      <textarea
                        v-model="poliSelected.poli_deskripsi"
                        :readonly="poliSelected.id ? true : false"
                        class="form-control"
                        rows="2"
                        placeholder="Deskripsi"
                      ></textarea>
                      <div
                        class="invalid-feedback"
                        v-show="errors.poli_deskripsi"
                      >
                        {{ errors.poli_deskripsi }}
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-label"
                        >Icon <span class="form-required">*</span></label
                      >
                      <input
                        type="text"
                        v-model="poliSelected.poli_icon"
                        class="form-control"
                        :class="invalidIcon ? 'is-invalid' : ''"
                        placeholder="fa fa-user"
                      />
                      <div class="invalid-feedback" v-show="errors.poli_icon">
                        {{ errors.poli_icon }}
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label
                        class="form-label"
                        :class="invalidColor ? 'text-danger' : ''"
                        >Warna <span class="form-required">*</span></label
                      >
                      <div class="row gutters-xs">
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="azure"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-azure"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="indigo"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-indigo"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="purple"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-purple"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="pink"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-pink"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="red"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-red"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="orange"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-orange"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="yellow"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-yellow"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="lime"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-lime"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="green"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-green"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="teal"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-teal"></span>
                          </label>
                        </div>
                        <div class="col-auto">
                          <label class="colorinput">
                            <input
                              name="color"
                              type="radio"
                              value="blue"
                              class="colorinput-input"
                              v-model="poliSelected.poli_color"
                            />
                            <span class="colorinput-color bg-blue"></span>
                          </label>
                        </div>
                      </div>
                      <div class="invalid-feedback" v-show="errors.poli_color">
                        {{ errors.poli_color }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="col-md-12">
                    <div class="form-group">
                      <div
                        class="form-label"
                        :class="invalidDayopen ? 'text-danger' : ''"
                      >
                        Hari Buka Poli <span class="form-required">*</span>
                      </div>
                      <div class="custom-controls-stacked">
                        <label class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            id="2"
                            name="dayopen"
                            v-model="poliSelected.poli_dayopen"
                            value="2"
                          />
                          <span class="custom-control-label">Senin</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            id="3"
                            name="dayopen"
                            v-model="poliSelected.poli_dayopen"
                            value="3"
                          />
                          <span class="custom-control-label">Selasa</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            id="4"
                            name="dayopen"
                            v-model="poliSelected.poli_dayopen"
                            value="4"
                          />
                          <span class="custom-control-label">Rabu</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            id="5"
                            name="dayopen"
                            v-model="poliSelected.poli_dayopen"
                            value="5"
                          />
                          <span class="custom-control-label">Kamis</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            id="6"
                            name="dayopen"
                            v-model="poliSelected.poli_dayopen"
                            value="6"
                          />
                          <span class="custom-control-label">Jum'at</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            id="7"
                            name="dayopen"
                            v-model="poliSelected.poli_dayopen"
                            value="7"
                          />
                          <span class="custom-control-label">Sabtu</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            id="1"
                            name="dayopen"
                            v-model="poliSelected.poli_dayopen"
                            value="1"
                          />
                          <span class="custom-control-label">Minggu</span>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="form-label">
                        Jam Tutup Pendaftaran
                        <span class="form-required">*</span>
                      </div>
                      <input
                        type="text"
                        v-mask="'##:##'"
                        v-model="poliSelected.poli_closehour"
                        maxlength="5"
                        class="form-control"
                        :class="invalidHour ? 'is-invalid' : ''"
                        placeholder="00:00"
                      />
                      <div
                        class="invalid-feedback"
                        v-show="errors.poli_closehour"
                      >
                        {{ errors.poli_closehour }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="form-label">
                        Status Poli <span class="form-required">*</span>
                      </div>
                      <select
                        class="form-control custom-select"
                        v-model="poliSelected.poli_status"
                        :class="invalidStatus ? 'is-invalid' : ''"
                      >
                        <option value="1">Aktif</option>
                        <option value="0">Non Aktif</option>
                      </select>
                      <div class="invalid-feedback" v-show="errors.poli_status">
                        {{ errors.poli_status }}
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="form-label"
                        >Kuota Perhari
                        <span class="form-required">*</span></label
                      >
                      <input
                        type="number"
                        v-model="poliSelected.poli_kuota"
                        class="form-control"
                        :class="invalidKuota ? 'is-invalid' : ''"
                        placeholder="Kuota Perhari"
                      />
                      <div class="invalid-feedback" v-show="errors.poli_kuota">
                        {{ errors.poli_kuota }}
                      </div>
                      <div class="small" v-show="!errors.poli_kuota">
                        <i>jika tanpa batas, maka isi nol (0)</i>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="custom-switch">
                        <input
                          type="checkbox"
                          class="custom-switch-input"
                          value="1"
                          v-model="poliSelected.poli_prioritas"
                        />
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">Prioritas</span>
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="custom-switch">
                        <input
                          type="checkbox"
                          class="custom-switch-input"
                          value="1"
                          v-model="poliSelected.poli_hide_on_prioritas"
                        />
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"
                          >Sembunyikan untuk pasien Prioritas</span
                        >
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="custom-switch">
                        <input
                          type="checkbox"
                          class="custom-switch-input"
                          value="1"
                          v-model="poliSelected.poli_hide_on_register"
                        />
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description"
                          >Sembunyikan dari Tiket Dispenser</span
                        >
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-footer text-right">
              <div class="">
                <button
                  type="button"
                  class="btn btn-default ml-auto"
                  @click="reset"
                >
                  Kembali
                </button>
                <button type="submit" class="btn btn-primary ml-auto">
                  <span v-if="poliSelected.poli_id">Edit Data</span>
                  <span v-else>Tambah Data</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";
import VueMask from "v-mask";
Vue.use(VueMask);

export default {
  data() {
    return {
      list_poli: [],
      poliSelected: {
        poli_dayopen: []
      },
      poli: [],
      errors: [],
      hari: {
        1: "Minggu",
        2: "Senin",
        3: "Selasa",
        4: "Rabu",
        5: "Kamis",
        6: "Jum'at",
        7: "Sabtu"
      },
      invalidName: false,
      invalidKuota: false,
      invalidColor: false,
      invalidIcon: false,
      invalidStatus: false,
      invalidDayopen: false,
      invalidHour: false,
      showForm: false
    };
  },

  methods: {
    edit(poli) {
      this.poliSelected = Object.assign({}, poli);
      this.showForm = true;
    },

    reset() {
      this.poliSelected = {
        poli_dayopen: []
      };
      this.showForm = false;

      this.errors = [];
      this.invalidName = false;
      this.invalidKuota = false;
      this.invalidColor = false;
      this.invalidIcon = false;
      this.invalidStatus = false;
      this.invalidDayopen = false;
      this.invalidHour = false;
      this.showForm = false;
    },

    add() {
      this.poliSelected = {
        poli_dayopen: []
      };
      this.showForm = true;
    },

    del(poli) {
      Swal.fire({
        type: "question",
        title: "Hapus Data",
        html:
          "Hapus data berikut? <br/>" +
          poli.poli_nama +
          " / " +
          poli.poli_deskripsi +
          " / " +
          poli.poli_kuota,
        showCancelButton: true,
        confirmButtonText: "Ya"
      }).then(result => {
        if (result.value) {
          let self = this;
          axios.post("poli/del", { poli_id: poli.poli_id }).then(response => {
            self.init_data();
            Swal.fire({
              type: "success",
              title: "Data berhasil dihapus",
              showConfirmButton: false,
              timer: 1000
            });
          });
        }
      });
    },

    checkForm: function(e) {
      this.errors = [];
      this.invalidName = false;
      this.invalidKuota = false;
      this.invalidColor = false;
      this.invalidIcon = false;
      this.invalidStatus = false;
      this.invalidHour = false;

      if (!this.poliSelected.poli_nama) {
        this.errors["poli_nama"] = "Nama Poli Harus diisi";
        this.invalidName = true;
      }

      if (
        typeof this.poliSelected.poli_kuota == "undefined" ||
        this.poliSelected.poli_kuota === ""
      ) {
        this.errors["poli_kuota"] = "Kuota Harus diisi";
        this.invalidKuota = true;
      }

      if (!this.poliSelected.poli_color) {
        this.errors["poli_color"] = "Warna Harus dipilih";
        this.invalidColor = true;
      }

      if (!this.poliSelected.poli_icon) {
        this.errors["poli_icon"] = "Icon Harus diisi";
        this.invalidIcon = true;
      }

      if (!this.poliSelected.poli_status) {
        this.errors["poli_status"] = "Status Harus dipilih";
        this.invalidStatus = true;
      }

      if (this.poliSelected.poli_dayopen.length == 0) {
        this.errors["poli_dayopen"] = "Hari Harus dipilih";
        this.invalidDayopen = true;
      }

      if (!this.poliSelected.poli_closehour) {
        this.errors["poli_closehour"] = "Jam Tutup Pendaftaran Harus dipilih";
        this.invalidHour = true;
      }

      if (!Object.keys(this.errors).length) {
        this.save();
      }

      e.preventDefault();
    },

    save() {
      let self = this;
      axios.post("poli/save", { poli: self.poliSelected }).then(response => {
        if (response.data.res == "SUCCESS") {
          self.init_data();

          Swal.fire({
            type: "success",
            title: "Data berhasil disimpan",
            showConfirmButton: false,
            timer: 1000
          });
        }
      });
    },

    init_data() {
      axios
        .post("poli/list-all")
        .then(response => (this.list_poli = response.data));
    }
  },

  created() {
    this.init_data();
  }
};
</script>
