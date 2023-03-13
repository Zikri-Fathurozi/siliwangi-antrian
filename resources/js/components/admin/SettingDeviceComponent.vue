<template>
  <div class="container">
    <div class="row row-cards">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Pengaturan Device</h3>
            <div class="card-options">
              <span class="badge badge-primary">{{
                Object.keys(device).length
              }}</span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table card-table table-striped table-vcenter">
              <thead>
                <tr>
                  <th>Nama Device</th>
                  <th>IP</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in device" :key="p.id">
                  <td>{{ p.device_nama }}</td>
                  <td>{{ p.device_ip }}</td>
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
              <h3 class="card-title">Form Device</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >Nama Device <span class="form-required">*</span></label
                    >
                    <input
                      type="text"
                      v-model="deviceSelected.device_nama"
                      class="form-control"
                      :class="invalidName ? 'is-invalid' : ''"
                      readonly
                      placeholder="Nama Device"
                    />
                    <div class="invalid-feedback" v-show="errors.device_nama">
                      {{ errors.device_nama }}
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"
                      >IP <span class="form-required">*</span></label
                    >
                    <input
                      type="text"
                      maxlength="15"
                      v-model="deviceSelected.device_ip"
                      :readonly="!deviceSelected.device_id"
                      class="form-control"
                      :class="invalidIP ? 'is-invalid' : ''"
                      placeholder=""
                    />
                    <div class="invalid-feedback" v-show="errors.device_ip">
                      {{ errors.device_ip }}
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
                  :disabled="!deviceSelected.device_id"
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
      device: [],
      deviceSelected: [],
      poli: [],
      errors: [],

      invalidName: false,
      invalidIP: false
    };
  },

  methods: {
    edit(p) {
      this.deviceSelected = Object.assign({}, p);
    },

    reset() {
      this.deviceSelected = [];
    },

    checkForm: function(e) {
      this.errors = [];
      this.invalidName = false;
      this.invalidIP = false;

      if (!this.deviceSelected.device_nama) {
        this.errors["device_nama"] = "Nama Harus diisi";
        this.invalidName = true;
      }

      if (!this.deviceSelected.device_ip) {
        this.errors["device_ip"] = "IP Harus diisi";
        this.invalidIP = true;
      }

      if (!Object.keys(this.errors).length) {
        this.save();
      }

      e.preventDefault();
    },

    save() {
      let self = this;
      let device = {
        device_id: self.deviceSelected.device_id,
        device_nama: self.deviceSelected.device_nama,
        device_ip: self.deviceSelected.device_ip
      };
      axios.post("device/save", { device: device }).then(response => {
        if (response.data.res == "SUCCESS") {
          self.init_data();
          self.deviceSelected = [];

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
      axios.post("device/list").then(response => (this.device = response.data));
    }
  },

  created() {
    this.init_data();
  }
};
</script>
