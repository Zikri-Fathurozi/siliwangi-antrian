<template>
  <div class="container">
    <div class="row row-cards justify-content-lg-center">
      <div class="col-lg-7">
        <form @submit="checkForm">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Pengaturan Profile</h3>
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
                      placeholder="your-email@domain.com"
                    />
                    <div class="invalid-feedback" v-show="errors.email">
                      {{ errors.email }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-footer">
              <div class="row">
                <div class="col-md-12">
                  <label class="custom-switch">
                    <input
                      type="checkbox"
                      class="custom-switch-input"
                      value="1"
                      v-model="change_password"
                    />
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"
                      >Ganti Password</span
                    >
                  </label>
                </div>
              </div>
            </div>

            <div class="card-footer" v-show="change_password">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label"
                      >Password Lama <span class="form-required">*</span></label
                    >
                    <input
                      type="password"
                      v-model="userSelected.password_old"
                      class="form-control"
                      :class="invalidPasswordOld ? 'is-invalid' : ''"
                      placeholder="Password Lama"
                    />
                    <div class="invalid-feedback" v-show="errors.password_old">
                      {{ errors.password_old }}
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >Password Baru <span class="form-required">*</span></label
                    >
                    <input
                      type="password"
                      v-model="userSelected.password"
                      class="form-control"
                      :class="invalidPassword ? 'is-invalid' : ''"
                      placeholder="Password Baru"
                    />
                    <div class="invalid-feedback" v-show="errors.password">
                      {{ errors.password }}
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >Password Confirm
                      <span class="form-required">*</span></label
                    >
                    <input
                      type="password"
                      v-model="userSelected.password_confirm"
                      class="form-control"
                      :class="invalidPasswordConfirm ? 'is-invalid' : ''"
                      placeholder="Password confirm"
                    />
                    <div
                      class="invalid-feedback"
                      v-show="errors.password_confirm"
                    >
                      {{ errors.password_confirm }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-footer text-right">
              <div class="d-flex">
                <button type="submit" class="btn btn-primary ml-auto">
                  Edit Data
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
      user: [],
      userSelected: [],
      poli: [],
      errors: [],

      change_password: false,
      invalidName: false,
      invalidEmail: false,
      invalidPasswordOld: false,
      invalidPassword: false,
      invalidPasswordConfirm: false
    };
  },

  methods: {
    edit(user) {
      this.userSelected = Object.assign({}, user);
    },

    reset() {
      //this.userSelected = [];
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
          " / " +
          user.poli_nama,
        showCancelButton: true,
        confirmButtonText: "Ya"
      }).then(result => {
        if (result.value) {
          let self = this;
          axios.post("user/del", { id: user.id }).then(response => {
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
      this.invalidPasswordOld = false;
      this.invalidPassword = false;
      this.invalidPasswordConfirm = false;

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

      if (this.change_password) {
        if (!this.userSelected.password_old) {
          this.errors["password_old"] = "Password Lama Harus diisi";
          this.invalidPasswordOld = true;
        }

        if (!this.userSelected.password) {
          this.errors["password"] = "Password Harus diisi";
          this.invalidPassword = true;
        }

        if (!this.userSelected.password_confirm) {
          this.errors["password_confirm"] = "Password Konfirm Harus diisi";
          this.invalidPasswordConfirm = true;
        }

        if (this.userSelected.password) {
          if (
            this.userSelected.password != this.userSelected.password_confirm
          ) {
            this.errors["password_confirm"] = "Password Harus sama";
            this.invalidPasswordConfirm = true;
          }
        }

        if (!Object.keys(this.errors).length) {
          let self = this;
          axios
            .post("users/check_password", { user: this.userSelected })
            .then(response => {
              if (response.data.res == "SUCCESS") {
                self.save();
              } else {
                self.errors["password_old"] = "Password Lama tidak valid";
                self.invalidPasswordOld = true;
              }
            });
        }
      } else {
        if (!Object.keys(this.errors).length) {
          this.save();
        }
      }

      e.preventDefault();
    },

    save() {
      let self = this;
      let user = {
        id: self.userSelected.id,
        name: self.userSelected.name,
        email: self.userSelected.email,
        password: self.userSelected.password
      };
      axios.post("users/change_profile", { user: user }).then(response => {
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

    validEmail: function(email) {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    },

    handleSelectRole(e) {
      let role = e.target.value;
      if (role != "poli") this.userSelected.poli = "";
    },

    init_data() {
      axios
        .post("users/me")
        .then(response => (this.userSelected = response.data));
    }
  },

  created() {
    this.init_data();
  }
};
</script>
