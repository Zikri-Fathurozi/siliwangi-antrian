<template>
  <div class="container">
    <div class="row row-cards justify-content-lg-center">
      <div class="col-lg-10" v-if="!userSelected">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Pengaturan Channel</h3>
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
                  <th>Username</th>
                  <th>Nama Channel</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.email }}</td>
                  <td>{{ user.name }}</td>
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
              <h3 class="card-title">Form Channel</h3>
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
                      >Username <span class="form-required">*</span></label
                    >
                    <input
                      type="text"
                      v-model="userSelected.email"
                      :readonly="userSelected.id ? true : false"
                      class="form-control"
                      :class="invalidEmail ? 'is-invalid' : ''"
                      name="example-disabled-input"
                      placeholder="Username"
                    />
                    <div class="invalid-feedback" v-show="errors.email">
                      {{ errors.email }}
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >Nama Channel <span class="form-required">*</span></label
                    >
                    <input
                      type="text"
                      v-model="userSelected.name"
                      class="form-control"
                      :class="invalidName ? 'is-invalid' : ''"
                      placeholder="Nama Channel"
                    />
                    <div class="invalid-feedback" v-show="errors.name">
                      {{ errors.name }}
                    </div>
                  </div>
                </div>

                <div class="col-md-12" v-show="!addUser">
                  <div class="form-group border-top">
                    <label class="custom-switch mt-4">
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

                <div class="col-md-12" v-show="change_password">
                  <div class="form-group">
                    <label class="form-label"
                      >Password <span class="form-required">*</span></label
                    >
                    <div class="input-group mb-2">
                      <input
                        type="text"
                        v-model="password"
                        class="form-control"
                        :class="invalidPassword ? 'is-invalid' : ''"
                        name="example-disabled-input"
                        placeholder="Password"
                      />

                      <button
                        class="btn"
                        type="button"
                        @click="generatePassword"
                      >
                        Generate Password
                      </button>
                      <div class="invalid-feedback" v-show="errors.password">
                        {{ errors.password }}
                      </div>
                    </div>
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
      errors: [],
      password: "",
      change_password: false,

      invalidName: false,
      invalidEmail: false,
      invalidPassword: false,

      addUser: false
    };
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
      this.change_password = false;
      this.password = "";
    },

    showAddUserForm() {
      this.addUser = true;
      this.change_password = true;
      this.userSelected = [];
    },

    del(user) {
      Swal.fire({
        type: "question",
        title: "Hapus Data",
        html: "Hapus data berikut? <br/>" + user.name,
        showCancelButton: true,
        confirmButtonText: "Ya"
      }).then(result => {
        if (result.value) {
          let self = this;
          axios.post("channel/del", { id: user.id }).then(response => {
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
      this.invalidPassword = false;
      this.userSelected.password = this.password;

      if (!this.userSelected.name) {
        this.errors["name"] = "Nama Harus diisi";
        this.invalidName = true;
      }
      if (!this.userSelected.email) {
        this.errors["email"] = "Username Harus diisi";
        this.invalidEmail = true;
      }

      if (this.change_password) {
        if (!this.userSelected.password) {
          this.errors["password"] = "Password Harus diisi";
          this.invalidPassword = true;
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
        password: self.userSelected.password
      };
      axios.post("channel/save", { user: user }).then(response => {
        if (response.data.res == "SUCCESS") {
          self.init_data();
          self.userSelected = null;
          self.password = "";
          self.change_password = false;
          self.addUser = false;
          self.errors = [];
          self.invalidPassword = false;

          Swal.fire({
            type: "success",
            title: "Data berhasil disimpan",
            showConfirmButton: false,
            timer: 1000
          });
        } else {
          self.errors["email"] = "Username sudah tersedia!";
          self.invalidEmail = true;
        }
      });
    },

    init_data() {
      axios.post("channel/list").then(response => (this.users = response.data));
    },

    generatePassword() {
      var pass = "";
      var str =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZ" +
        "abcdefghijklmnopqrstuvwxyz0123456789@#$";

      for (var i = 1; i <= 15; i++) {
        var char = Math.floor(Math.random() * str.length + 1);

        pass += str.charAt(char);
      }

      this.password = pass;
    }
  },

  created() {
    this.init_data();
  }
};
</script>
