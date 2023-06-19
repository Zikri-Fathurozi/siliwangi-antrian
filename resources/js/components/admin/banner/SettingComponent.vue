<template>
  <form @submit="checkForm">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Pengaturan Tampilan</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="form-label"
                >Aktif Display <span class="form-required">*</span></label
              >
              <div class="selectgroup w-100">
                <label class="selectgroup-item">
                  <input
                    type="radio"
                    name="role"
                    value="image"
                    class="selectgroup-input"
                    v-model="settingBanner.type"
                  />
                  <span class="selectgroup-button">Image</span>
                </label>
                <label class="selectgroup-item">
                  <input
                    type="radio"
                    name="role"
                    value="video"
                    class="selectgroup-input"
                    v-model="settingBanner.type"
                  />
                  <span class="selectgroup-button">Video</span>
                </label>
              </div>
              <div class="invalid-feedback" v-show="errors.role">
                {{ errors.role }}
              </div>
            </div>
          </div>

          <div class="col-md-12 mb-0" v-if="settingBanner.type == 'image'">
            <div class="form-group">
              <label class="form-label">Pilih Gambar</label>
              <div class="row gutters-sm">
                <div class="col-12" v-show="list_image.length === 0">
                  Gambar tidak tersedia.
                </div>
                <div
                  class="col-6 col-sm-4"
                  v-for="b in list_image"
                  :key="b.banner_id"
                >
                  <label class="imagecheck mb-4" v-show="b.banner_path">
                    <input
                      name="imagecheck"
                      type="checkbox"
                      :value="b.banner_id"
                      class="imagecheck-input"
                      v-model="settingBanner.list_banner"
                    />
                    <figure class="imagecheck-figure">
                      <img
                        :src="'../' + b.banner_path"
                        alt=""
                        class="imagecheck-image"
                      />
                      <small class="p-1">{{ b.banner_title }}</small>
                    </figure>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12 mb-0" v-if="settingBanner.type == 'video'">
            <div class="form-group">
              <label class="form-label">Pilih Video</label>
              <div class="row gutters-sm">
                <div class="col-12" v-show="list_image.length === 0">
                  Video tidak tersedia.
                </div>
                <div
                  class="col-6 col-sm-4"
                  v-for="b in list_video"
                  :key="b.banner_id"
                >
                  <label class="imagecheck mb-4" v-show="b.banner_path">
                    <input
                      name="imagecheck"
                      type="checkbox"
                      :value="b.banner_id"
                      class="imagecheck-input"
                      v-model="settingBanner.list_banner"
                    />
                    <figure class="imagecheck-figure">
                      <video
                        id="main-video"
                        controls
                        style="width: 100%;"
                        class="imagecheck-image"
                      >
                        <source type="video/mp4" :src="'../' + b.banner_path" />
                      </video>
                      <small class="p-1">{{ b.banner_title }}</small>
                    </figure>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer text-right mt-0">
        <div class="d-flex">
          <button type="submit" class="btn btn-primary ml-auto">
            Simpan Pengaturan
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import Swal from "sweetalert2";

export default {
  data() {
    return {
      errors: [],
      settingBanner: {
        type: "photo",
        list_banner: []
      },
      list_banner: [],
      list_image: [],
      list_video: [],

      invalidType: false,
      invalidList: false
    };
  },

  methods: {
    checkForm: function(e) {
      this.errors = [];
      this.invalidType = false;
      this.invalidList = false;

      if (!this.settingBanner.type) {
        this.errors["type"] = "Jenis Display Harus diisi";
        this.invalidType = true;
      }

      if (this.settingBanner.list == "") {
        this.errors["list"] = "Photo / Vide Harus dipilih";
        this.invalidList = true;
      }

      if (!Object.keys(this.errors).length) {
        this.save();
      }

      e.preventDefault();
    },

    save() {
      let self = this;
      let config = {
        config_key: "display_banner",
        config_value: self.settingBanner.type
      };
      axios
        .post("banner/config/save", {
          config: config,
          banner: self.settingBanner.list_banner
        })
        .then(response => {
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
        .post("banner/config")
        .then(response => (this.settingBanner = response.data));

      axios
        .post("banner/list-image/0")
        .then(response => (this.list_image = response.data));

      axios
        .post("banner/list-video/0")
        .then(response => (this.list_video = response.data));
    }
  },

  created() {
    this.init_data();
  }
};
</script>
