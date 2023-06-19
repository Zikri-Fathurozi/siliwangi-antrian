<template>
  <div class="container">
    <div class="row row-cards">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Pengaturan Service</h3>
            <div class="card-options">
              <span class="badge badge-primary">{{
                Object.keys(service).length
              }}</span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table card-table table-striped table-vcenter">
              <thead>
                <tr>
                  <th>Nama Service</th>
                  <th>URL</th>
                  <th>Enabled</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in service" :key="p.id">
                  <td>{{ p.service_nama }}</td>
                  <td>{{ p.service_url }}</td>
                  <td>
                    <i
                      class="fa fa-check-square text-success"
                      v-if="p.service_enabled"
                    ></i>
                    <i v-else class="fa fa-close text-black"></i>
                  </td>
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

      <div class="col-lg-5">
        <form @submit="checkForm">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Form Service</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label"
                      >Nama Service <span class="form-required">*</span></label
                    >
                    <input
                      type="text"
                      v-model="serviceSelected.service_nama"
                      class="form-control"
                      :class="invalidName ? 'is-invalid' : ''"
                      readonly
                      placeholder="Nama Service"
                    />
                    <div class="invalid-feedback" v-show="errors.service_nama">
                      {{ errors.service_nama }}
                    </div>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label"
                      >URL Service <span class="form-required">*</span></label
                    >
                    <div class="input-group mb-2">
                      <input
                        type="text"
                        v-model="serviceSelected.service_url"
                        :readonly="!serviceSelected.service_id"
                        class="form-control"
                        :class="invalidURLService ? 'is-invalid' : ''"
                        placeholder="http://127.0.0.1/api-service"
                      />
                      <button
                        class="btn"
                        type="button"
                        :disabled="!serviceSelected.service_id"
                        @click="test_connection"
                      >
                        <i
                          v-if="connection.isLoading"
                          class="fa fa-hourglass-start"
                        ></i>
                        Test Koneksi
                      </button>
                      <div
                        class="invalid-feedback"
                        style="display: inline"
                        v-show="errors.service_url"
                      >
                        {{ errors.service_url }}
                      </div>
                      <div
                        :class="
                          connection.isSuccess
                            ? 'valid-feedback'
                            : 'invalid-feedback'
                        "
                        v-show="connection.isTested"
                        style="display: inline"
                      >
                        <i v-if="connection.isSuccess" class="fa fa-check"></i>
                        <i v-else class="fa fa-close"></i>
                        {{ connection.message }}
                      </div>
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
                        v-model="serviceSelected.service_enabled"
                      />
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description">Enabled</span>
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
                <button
                  type="submit"
                  :disabled="!serviceSelected.service_id"
                  class="btn btn-primary ml-auto"
                >
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
      service: [],
      serviceSelected: [],
      poli: [],
      errors: [],

      invalidName: false,
      invalidURLService: false,

      connection: {
        isLoading: false,
        isSuccess: false,
        isTested: false,
        message: ""
      }
    };
  },
  watch: {
    // whenever question changes, this function will run
    serviceSelected: {
      handler(val) {
        this.connection.isTested = false;
      },
      deep: true
    }
  },

  methods: {
    edit(p) {
      this.serviceSelected = Object.assign({}, p);
    },

    reset() {
      this.serviceSelected = [];
    },

    checkForm: function(e) {
      this.errors = [];
      this.invalidName = false;
      this.invalidURLService = false;

      if (!this.serviceSelected.service_nama) {
        this.errors["service_nama"] = "Nama Harus diisi";
        this.invalidName = true;
      }

      if (!Object.keys(this.errors).length) {
        this.save();
      }

      e.preventDefault();
    },

    save() {
      let self = this;
      let service = {
        service_id: self.serviceSelected.service_id,
        service_nama: self.serviceSelected.service_nama,
        service_url: self.serviceSelected.service_url,
        service_enabled: self.serviceSelected.service_enabled
      };
      axios.post("service/save", { service: service }).then(response => {
        if (response.data.res == "SUCCESS") {
          self.init_data();
          self.serviceSelected = [];

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
        .post("service/list")
        .then(response => (this.service = response.data));
    },

    test_connection() {
      let self = this;
      self.connection.isLoading = true;
      axios
        .get(this.serviceSelected.service_url, {
          headers: {
            "X-Service-Key": document.head.querySelector(
              'meta[name="service-key"]'
            ).content
          }
        })
        .then(response => {
          self.connection = {
            ...self.connection,
            isTested: true,
            isSuccess: true,
            message: response.data.message
          };
        })
        .catch(err => {
          self.connection = {
            ...self.connection,
            isTested: true,
            isSuccess: false,
            message: "Koneksi Tidak tersambung"
          };
        })
        .finally(function() {
          self.connection.isLoading = false;
        });
    }
  },

  created() {
    this.init_data();
  }
};
</script>
