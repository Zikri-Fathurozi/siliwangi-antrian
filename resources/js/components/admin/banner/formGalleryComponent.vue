<template>
  <form @submit="checkForm">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Form Edit</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="row">
            <div class="col-md-6">
              <div class="col-md-12">
                <div class="card p-3" style="min-height:200px">
                  <span
                    v-show="isImage || bannerSelected.banner_type == 'image'"
                  >
                    <span v-if="bannerSelected.banner_path && !showPreview">
                      <img
                        :src="'../' + bannerSelected.banner_path"
                        class="rounded"
                      />
                    </span>
                    <span v-else>
                      <img
                        v-bind:src="imagePreview"
                        class="rounded"
                        style="width:40%"
                      />
                    </span>
                  </span>

                  <span
                    v-show="isVideo || bannerSelected.banner_type == 'video'"
                  >
                    <span v-if="bannerSelected.banner_path && !showPreview">
                      <video id="main-video" controls style="width: 100%;">
                        <source
                          type="video/mp4"
                          ref="video"
                          :src="'../' + bannerSelected.banner_path"
                        />
                      </video>
                    </span>
                    <span v-else>
                      <video id="main-video" controls style="width: 100%;">
                        <source type="video/mp4" :src="imagePreview" />
                      </video>
                    </span>
                  </span>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <div class="custom-file">
                    <input
                      class="custom-file-input"
                      type="file"
                      id="file"
                      ref="file"
                      v-on:change="handleFileUpload()"
                    />
                    <label class="custom-file-label">Choose file</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label"
                    >Judul <span class="form-required">*</span></label
                  >
                  <input
                    type="text"
                    v-model="bannerSelected.banner_title"
                    class="form-control"
                    :class="invalidTitle ? 'is-invalid' : ''"
                    placeholder="Judul"
                  />
                  <div class="invalid-feedback" v-show="errors.banner_title">
                    {{ errors.banner_title }}
                  </div>
                </div>
                <br />
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label"
                    >Deskripsi <span class="form-required">*</span></label
                  >
                  <textarea
                    v-model="bannerSelected.banner_desc"
                    class="form-control"
                    :class="invalidDesc ? 'is-invalid' : ''"
                    rows="2"
                    placeholder="Deskripsi"
                  ></textarea>
                  <div class="invalid-feedback" v-show="errors.banner_desc">
                    {{ errors.banner_desc }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer text-right">
        <div class="">
          <button type="button" @click="back()" class="btn btn-default ml-auto">
            Kembali
          </button>
          <button type="submit" class="btn btn-primary ml-auto">
            <span v-if="bannerSelected.banner_id">Simpan</span>
            <span v-else>Tambah</span>
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import Swal from "sweetalert2";

export default {
  props: ["bannerSelected", "showPreview", "imagePreview"],
  data() {
    return {
      errors: [],
      fileInput: null,
      file: "",
      cacheImage: "",
      invalidTitle: false,
      invalidDesc: false,
      isImage: false,
      isVideo: false
    };
  },

  methods: {
    handleFileUpload() {
      this.file = this.$refs.file.files[0];
      let reader = new FileReader();

      let self = this;
      reader.addEventListener(
        "load",
        function() {
          let source = "";

          if (this.file) {
            if (/\.(jpe?g|png|gif)$/i.test(this.file.name)) {
              source = reader.result;
            } else if (/\.(mpe?g|mp4)$/i.test(this.file.name)) {
              source = URL.createObjectURL(this.file);
            }

            self.$emit("setImagePreview", source);
            self.cacheImage = source;

            let _VIDEO = document.querySelector("#main-video");
            _VIDEO.load();
          }
        }.bind(this),
        false
      );

      if (this.imagePreview == "") {
        this.$emit("setImagePreview", this.cacheImage);
      }
      this.$emit("setShowPreview", true);

      if (this.file) {
        if (/\.(jpe?g|png|gif)$/i.test(this.file.name)) {
          reader.readAsDataURL(this.file);
          this.isImage = true;
          this.isVideo = false;
        } else if (/\.(mpe?g|mp4)$/i.test(this.file.name)) {
          reader.readAsDataURL(this.file);
          this.isImage = false;
          this.isVideo = true;
        }
      }
    },

    back() {
      this.$emit("openMenu", "gallery");
    },

    checkForm: function(e) {
      this.errors = [];
      this.invalidTitle = false;
      this.invalidDesc = false;
      this.invalidType = false;
      this.invalidStatus = false;

      if (!this.bannerSelected.banner_title) {
        this.errors["banner_title"] = "Judul Harus diisi";
        this.invalidTitle = true;
      }

      if (this.bannerSelected.banner_desc == "") {
        this.errors["banner_desc"] = "Deskripsi Harus diisi";
        this.invalidDesc = true;
      }

      if (!Object.keys(this.errors).length) {
        this.save();
      }

      e.preventDefault();
    },

    save() {
      let self = this;
      let banner = {
        banner_title: self.bannerSelected.banner_title,
        banner_desc: self.bannerSelected.banner_desc,
        file: self.file
      };

      console.log(this.file);
      let formData = new FormData();
      $.each(banner, function(key, value) {
        formData.append(key, value);
      });

      if (self.bannerSelected.banner_id) {
        formData.append("banner_id", self.bannerSelected.banner_id);
      }

      axios
        .post("banner/save", formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(response => {
          if (response.data.res == "SUCCESS") {
            self.$emit("init_data");
            self.$emit("openMenu", "gallery");

            Swal.fire({
              type: "success",
              title: "Data berhasil disimpan",
              showConfirmButton: false,
              timer: 1000
            });
          }
        });
    }
  },

  created() {}
};
</script>
