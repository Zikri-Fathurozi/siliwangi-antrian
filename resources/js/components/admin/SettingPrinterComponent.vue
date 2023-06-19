<template>
  <div class="container">
    <div class="row row-cards">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Pengaturan Printer</h3>
            <div class="card-options">
              <span class="badge badge-primary">{{
                Object.keys(printer).length
              }}</span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table card-table table-striped table-vcenter">
              <thead>
                <tr>
                  <th>Nama Printer</th>
                  <th>URL</th>
                  <th>Alias</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in printer" :key="p.id">
                  <td>{{ p.printer_nama }}</td>
                  <td>{{ p.printer_url_service }}</td>
                  <td>{{ p.printer_alias }}</td>
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
              <h3 class="card-title">Form Printer</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >Nama Printer <span class="form-required">*</span></label
                    >
                    <input
                      type="text"
                      v-model="printerSelected.printer_nama"
                      class="form-control"
                      :class="invalidName ? 'is-invalid' : ''"
                      readonly
                      placeholder="Nama Printer"
                    />
                    <div class="invalid-feedback" v-show="errors.printer_nama">
                      {{ errors.printer_nama }}
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >Alias <span class="form-required">*</span></label
                    >
                    <input
                      type="text"
                      maxlength="4"
                      v-model="printerSelected.printer_alias"
                      :readonly="!printerSelected.printer_id"
                      class="form-control"
                      :class="invalidAlias ? 'is-invalid' : ''"
                      placeholder=""
                    />
                    <div class="invalid-feedback" v-show="errors.printer_alias">
                      {{ errors.printer_alias }}
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
                        v-model="printerSelected.printer_url_service"
                        :readonly="!printerSelected.printer_id"
                        class="form-control"
                        :class="invalidURLService ? 'is-invalid' : ''"
                        placeholder="http://127.0.0.1/ticket-service"
                      />
                      <button
                        class="btn"
                        type="button"
                        :disabled="!printerSelected.printer_id"
                        @click="test_connection"
                      >
                        <i
                          v-if="connection.isLoading"
                          class="fa fa-hourglass-start"
                        ></i>
                        Test Koneksi
                      </button>
                      <div
                        class="valid-feedback"
                        v-show="errors.printer_url_service"
                      >
                        {{ errors.printer_url_service }}
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
              </div>
            </div>

            <div class="card-footer text-right">
              <div class="d-flex">
                <a href="javascript:void(0)" @click="reset" class="btn btn-link"
                  >Reset</a
                >
                <button
                  type="submit"
                  :disabled="!printerSelected.printer_id"
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
      printer: [],
      printerSelected: [],
      poli: [],
      errors: [],

      invalidName: false,
      invalidURLService: false,
      invalidAlias: false,

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
    printerSelected: {
      handler(val) {
        this.connection.isTested = false;
      },
      deep: true
    }
  },

  methods: {
    edit(p) {
      this.printerSelected = Object.assign({}, p);
    },

    reset() {
      this.printerSelected = [];
    },

    checkForm: function(e) {
      this.errors = [];
      this.invalidName = false;
      this.invalidURLService = false;
      this.invalidAlias = false;

      if (!this.printerSelected.printer_nama) {
        this.errors["printer_nama"] = "Nama Harus diisi";
        this.invalidName = true;
      }

      if (!this.printerSelected.printer_url_service) {
        this.errors["printer_url_service"] = "URL Service Harus diisi";
        this.invalidURLService = true;
      }

      if (!this.printerSelected.printer_alias) {
        this.errors["printer_alias"] = "Alias Harus diisi";
        this.invalidAlias = true;
      }

      if (!Object.keys(this.errors).length) {
        this.save();
      }

      e.preventDefault();
    },

    save() {
      let self = this;
      let printer = {
        printer_id: self.printerSelected.printer_id,
        printer_nama: self.printerSelected.printer_nama,
        printer_url_service: self.printerSelected.printer_url_service
      };
      axios.post("printer/save", { printer: printer }).then(response => {
        if (response.data.res == "SUCCESS") {
          self.init_data();
          self.printerSelected = [];

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
        .post("printer/list")
        .then(response => (this.printer = response.data));
    },

    test_connection() {
      let self = this;
      self.connection.isLoading = true;
      axios
        .get(this.printerSelected.printer_url_service, {
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
