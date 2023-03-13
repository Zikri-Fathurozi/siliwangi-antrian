<template>
  <div class="container">
    <div class="row row-cards">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Pengaturan Info Text</h3>
            <div class="card-options">
              <span class="badge badge-primary">{{
                Object.keys(list_info).length
              }}</span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table card-table table-striped table-vcenter">
              <thead>
                <tr>
                  <th style="width:60%">Info Text</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in list_info" :key="p.id">
                  <td>{{ p.info_text }}</td>
                  <td>{{ p.info_status == 1 ? "Aktif" : "Nonaktif" }}</td>
                  <td>
                    <a href="javascript:void(0)" @click="edit(p)" class="icon"
                      ><i class="fa fa-pencil-square fa-lg text-info"></i></a
                    >&nbsp;&nbsp;
                    <a href="javascript:void(0)" @click="del(p)" class="icon"
                      ><i class="fa fa-trash fa-lg text-danger"></i
                    ></a>
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
              <h3 class="card-title">Form Info Text</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label"
                      >Text <span class="form-required">*</span></label
                    >
                    <textarea
                      v-model="infoSelected.info_text"
                      :class="invalidText ? 'is-invalid' : ''"
                      class="form-control"
                      rows="2"
                      placeholder="Info Text"
                    ></textarea>
                    <div class="invalid-feedback" v-show="errors.info_text">
                      {{ errors.info_text }}
                    </div>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <div class="form-label">
                      Status <span class="form-required">*</span>
                    </div>
                    <select
                      class="form-control custom-select"
                      v-model="infoSelected.info_status"
                      :class="invalidStatus ? 'is-invalid' : ''"
                    >
                      <option value="1">Aktif</option>
                      <option value="0">Non Aktif</option>
                    </select>
                    <div class="invalid-feedback" v-show="errors.info_status">
                      {{ errors.info_status }}
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
                  <span v-if="infoSelected.info_id">Edit Data</span>
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
      list_info: [],
      infoSelected: [],
      info: [],
      errors: [],
      invalidText: false,
      invalidStatus: false
    };
  },

  methods: {
    edit(info) {
      this.infoSelected = Object.assign({}, info);
    },

    reset() {
      this.infoSelected = [];
    },

    del(info) {
      Swal.fire({
        type: "question",
        title: "Hapus Data",
        html: "Hapus data berikut? <br/>" + info.info_text,
        showCancelButton: true,
        confirmButtonText: "Ya"
      }).then(result => {
        if (result.value) {
          let self = this;
          axios.post("info/del", { info_id: info.info_id }).then(response => {
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
      this.invalidText = false;
      this.invalidStatus = false;

      if (!this.infoSelected.info_text) {
        this.errors["info_text"] = "Text Harus diisi";
        this.invalidText = true;
      }

      if (!this.infoSelected.info_status) {
        this.errors["info_status"] = "Status Harus dipilih";
        this.invalidStatus = true;
      }

      if (!Object.keys(this.errors).length) {
        this.save();
      }

      e.preventDefault();
    },

    save() {
      let self = this;
      let info = {
        info_id: self.infoSelected.info_id,
        info_text: self.infoSelected.info_text,
        info_status: self.infoSelected.info_status
      };
      axios.post("info/save", { info: info }).then(response => {
        if (response.data.res == "SUCCESS") {
          self.init_data();
          self.infoSelected = [];

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
        .post("info/list")
        .then(response => (this.list_info = response.data));
    }
  },

  created() {
    this.init_data();
  }
};
</script>
