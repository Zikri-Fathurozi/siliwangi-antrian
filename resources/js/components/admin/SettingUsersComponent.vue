<template>
  <div class="container">
    <div class="row row-cards justify-content-lg-center">
      <div class="col-lg-12" v-if="!userSelected">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Pengaturan Petugas</h3>
            <a
              href="javascript:void(0)"
              @click="showAddUserForm"
              class="btn btn-secondary btn-sm ml-3"
              >Tambah</a
            >
            <div class="card-options">
              <span class="badge badge-primary">{{
                Object.keys(users).length
              }}</span>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table card-table table-striped table-vcenter">
              <thead>
                <tr>
                  <th>Nama Petugas</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Loket</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    {{ user.user_role ? user.user_role.role_nama : user.role }}
                    <span v-if="user.prioritas == '1'">(Prioritas)</span>
                    <span v-if="user.poli_nama != '-'"
                      >({{ user.poli_nama }})</span
                    >
                  </td>
                  <td>{{ user.loket }}</td>
                  <td>
                    <a
                      href="javascript:void(0)"
                      @click="edit(user)"
                      class="icon mr-1"
                      ><i class="fa fa-pencil-square fa-lg text-info"></i
                    ></a>
                    <a href="javascript:void(0)" @click="del(user)" class="icon"
                      ><i class="fa fa-trash fa-lg text-danger"></i
                    ></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-7" v-if="userSelected || addUser">
        <form @submit="checkForm">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Form Petugas</h3>
              <div class="card-options">
                <a
                  href="javascript:void(0)"
                  @click="clearUserSelected"
                  class="btn btn-secondary btn-sm"
                  >Kembali</a
                >
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >Nama Petugas <span class="form-required">*</span></label
                    >
                    <input
                      type="text"
                      v-model="userSelected.name"
                      class="form-control"
                      :class="invalidName ? 'is-invalid' : ''"
                      placeholder="Nama Petugas"
                    />
                    <div class="invalid-feedback" v-show="errors.name">
                      {{ errors.name }}
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >Email <span class="form-required">*</span></label
                    >
                    <input
                      type="email"
                      v-model="userSelected.email"
                      :readonly="userSelected.id ? true : false"
                      class="form-control"
                      :class="invalidEmail ? 'is-invalid' : ''"
                      name="example-disabled-input"
                      placeholder="your-email@domain.com"
                    />
                    <div class="invalid-feedback" v-show="errors.email">
                      {{ errors.email }}
                    </div>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label
                      class="form-label"
                      :class="invalidRole ? 'text-danger' : ''"
                      >Role <span class="form-required">*</span></label
                    >
                    <div class="selectgroup w-100">
                      <label class="selectgroup-item">
                        <input
                          type="radio"
                          name="role"
                          value="admin"
                          class="selectgroup-input"
                          @click="handleSelectRole"
                          v-model="userSelected.role"
                        />
                        <span class="selectgroup-button">Admin</span>
                      </label>
                      <label class="selectgroup-item">
                        <input
                          type="radio"
                          name="role"
                          value="poli"
                          class="selectgroup-input"
                          @click="handleSelectRole"
                          v-model="userSelected.role"
                        />
                        <span class="selectgroup-button">Poli</span>
                      </label>
                      <label class="selectgroup-item">
                        <input
                          type="radio"
                          name="role"
                          value="tensi"
                          class="selectgroup-input"
                          @click="handleSelectRole"
                          v-model="userSelected.role"
                        />
                        <span class="selectgroup-button">Tensi</span>
                      </label>
                      <label class="selectgroup-item">
                        <input
                          type="radio"
                          name="role"
                          value="pendaftaran"
                          class="selectgroup-input"
                          @click="handleSelectRole"
                          v-model="userSelected.role"
                        />
                        <span class="selectgroup-button">Pendaftaran</span>
                      </label>
                    </div>
                    <div class="invalid-feedback" v-show="errors.role">
                      {{ errors.role }}
                    </div>
                  </div>
                </div>

                <div class="col-md-12" v-show="show_poli_options">
                  <div class="form-group">
                    <label class="form-label"
                      >Poli
                      <span class="form-required" v-show="show_poli_options"
                        >*</span
                      ></label
                    >
                    <select
                      name="beast"
                      id="select-beast"
                      :disabled="!show_poli_options"
                      class="form-control custom-select"
                      :class="invalidPoli ? 'is-invalid' : ''"
                      v-model="userSelected.poli"
                    >
                      <option
                        v-for="p in poli"
                        :value="p.poli_id"
                        :key="p.poli_id"
                        >{{ p.poli_nama }}</option
                      >
                    </select>
                    <div class="invalid-feedback" v-show="errors.poli">
                      {{ errors.poli }}
                    </div>
                  </div>
                </div>

                <div class="col-md-12" v-show="show_tensi_options">
                  <div class="form-group">
                    <label class="form-label"
                      >Ruang Tensi
                      <span class="form-required" v-show="show_tensi_options"
                        >*</span
                      ></label
                    >
                    <select
                      name="beast"
                      id="select-beast"
                      :disabled="!show_tensi_options"
                      class="form-control custom-select"
                      :class="invalidPoli ? 'is-invalid' : ''"
                      v-model="userSelected.tensi"
                    >
                      <option
                        v-for="p in tensi"
                        :value="p.poli_id"
                        :key="p.poli_id"
                        >{{ p.poli_nama }}</option
                      >
                    </select>
                    <div class="invalid-feedback" v-show="errors.tensi">
                      {{ errors.tensi }}
                    </div>
                  </div>
                </div>

                <div
                  class="col-md-12"
                  v-show="userSelected.role === 'pendaftaran'"
                >
                  <div class="form-group">
                    <label class="custom-switch">
                      <input
                        type="checkbox"
                        class="custom-switch-input"
                        value="1"
                        v-model="is_multiple_loket"
                      />
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description"
                        >Multi Loket Pendaftaran?</span
                      >
                    </label>
                  </div>
                </div>

                <div
                  class="col-md-12"
                  v-show="userSelected.role === 'pendaftaran'"
                >
                  <div class="form-group">
                    <label class="form-label"
                      >Loket
                      <span
                        class="form-required"
                        v-show="
                          userSelected.role == 'pendaftaran' &&
                            is_multiple_loket
                        "
                        >*</span
                      ></label
                    >
                    <select
                      name="beast"
                      id="select-beast"
                      :disabled="
                        userSelected.role !== 'pendaftaran' ||
                          !is_multiple_loket
                      "
                      class="form-control custom-select"
                      :class="invalidLoket ? 'is-invalid' : ''"
                      v-model="userSelected.loket"
                    >
                      <option
                        :value="l.loket_id"
                        v-for="l in loket"
                        :key="l.loket_id"
                        >{{ l.loket_nama }}</option
                      >
                    </select>
                    <div class="invalid-feedback" v-show="errors.loket">
                      {{ errors.loket }}
                    </div>
                  </div>
                </div>

                <div
                  class="col-md-12"
                  v-show="userSelected.role === 'pendaftaran'"
                >
                  <div class="form-group">
                    <label class="custom-switch">
                      <input
                        type="checkbox"
                        class="custom-switch-input"
                        value="1"
                        :disabled="
                          userSelected.role != 'pendaftaran' &&
                            userSelected.role != 'poli'
                        "
                        v-model="userSelected.prioritas"
                      />
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description"
                        >Loket Pasien Prioritas?</span
                      >
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-footer text-right">
              <div class="d-flex">
                <a href="javascript:void(0)" @click="reset" class="btn btn-link"
                  >Reset</a
                >
                <button type="submit" class="btn btn-primary ml-auto">
                  <span v-if="userSelected.id">Edit Data</span>
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

export default {
  data() {
    return {
      users: [],
      userSelected: null,
      poli: [],
      tensi: [],
      loket: [
        { loket_id: 1, loket_nama: "Loket 1" },
        { loket_id: 2, loket_nama: "Loket 2" },
        { loket_id: 3, loket_nama: "Loket 3" },
        { loket_id: 4, loket_nama: "Loket 4" }
      ],
      errors: [],
      is_multiple_loket: false,
      invalidName: false,
      invalidEmail: false,
      invalidRole: false,
      invalidPoli: false,
      invalidTensi: false,
      invalidLoket: false,
      addUser: false
    };
  },

  watch: {
    is_multiple_loket: function(newVal, oldVal) {
      if (newVal !== oldVal && !newVal) {
        this.userSelected.loket = "";
      }
    }
  },

  computed: {
    show_poli_options() {
      return this.userSelected.role === "poli";
    },
    show_tensi_options() {
      return this.userSelected.role === "tensi";
    }
  },
  methods: {
    edit(user) {
      this.userSelected = Object.assign({}, user);
    },

    reset() {
      this.userSelected = [];
    },

    clearUserSelected() {
      this.userSelected = null;
      this.addUser = false;
    },

    showAddUserForm() {
      this.addUser = true;
      this.userSelected = [];
    },

    del(user) {
      Swal.fire({
        type: "question",
        title: "Hapus Data",
        html:
          "Hapus data berikut? <br/>" +
          user.name +
          " / " +
          user.role +
          (user.poli_nama != "-" ? ` / ${user.poli_nama}` : ""),
        showCancelButton: true,
        confirmButtonText: "Ya"
      }).then(result => {
        if (result.value) {
          let self = this;
          axios.post("users/del", { id: user.id }).then(response => {
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
      this.invalidEmail = false;
      this.invalidRole = false;
      this.invalidPoli = false;
      this.invalidLoket = false;
      this.invalidTensi = false;

      if (!this.userSelected.name) {
        this.errors["name"] = "Username Harus diisi";
        this.invalidName = true;
      }
      if (!this.userSelected.email) {
        this.errors["email"] = "Email Harus diisi";
        this.invalidEmail = true;
      } else if (!this.validEmail(this.userSelected.email)) {
        this.errors["email"] = "Email Tidak Valid";
        this.invalidEmail = true;
      }

      if (!this.userSelected.role) {
        this.errors["role"] = "Role Harus dipilih";
        this.invalidRole = true;
      }

      if (this.show_poli_options) {
        if (!this.userSelected.poli) {
          this.errors["poli"] = "Poli Harus dipilih";
          this.invalidPoli = true;
        }
      }

      if (this.show_tensi_options) {
        if (!this.userSelected.tensi) {
          this.errors["tensi"] = "Ruang Tensi Harus dipilih";
          this.invalidTensi = true;
        }
      }

      if (this.userSelected.role === "pendaftaran" && this.is_multiple_loket) {
        if (!this.userSelected.loket) {
          this.errors["loket"] = "Loket Harus dipilih";
          this.invalidLoket = true;
        }
      }

      if (!Object.keys(this.errors).length) {
        this.save();
      }

      e.preventDefault();
    },

    save() {
      let self = this;

      let user = {
        id: self.userSelected.id,
        name: self.userSelected.name,
        email: self.userSelected.email,
        role: self.userSelected.role,
        poli: self.userSelected.poli,
        tensi: self.userSelected.tensi,
        loket: self.userSelected.loket,
        prioritas: self.userSelected.prioritas
      };
      axios.post("users/save", { user: user }).then(response => {
        if (response.data.res == "SUCCESS") {
          self.init_data();
          self.userSelected = null;

          self.errors = [];
          self.invalidEmail = false;

          Swal.fire({
            type: "success",
            title: "Data berhasil disimpan",
            showConfirmButton: false,
            timer: 1000
          });
        } else {
          self.errors["email"] = "Email Duplicate!";
          self.invalidEmail = true;
        }
      });
    },

    validEmail: function(email) {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    },

    handleSelectRole(e) {
      let role = e.target.value;
      if (role != "poli") this.userSelected.poli = "";
      if (role != "pendaftaran") this.userSelected.pendaftaran = "";
      if (role != "pendaftaran") this.userSelected.loket = "";
      if (role != "pendaftaran") this.userSelected.prioritas = false;
      if (role != "pendaftaran") this.is_multiple_loket = false;
    },

    init_data() {
      axios.post("users/list").then(response => (this.users = response.data));
    }
  },

  created() {
    this.init_data();

    axios.post("poli/list").then(response => (this.poli = response.data.list));
    axios
      .post("tensi/list")
      .then(response => (this.tensi = response.data.list));
  }
};
</script>
