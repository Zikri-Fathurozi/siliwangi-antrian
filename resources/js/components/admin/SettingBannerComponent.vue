<template>
  <div class="container">
    <div class="row row-cards">
      <div class="col-md-3">
        <div>
          <div class="list-group list-group-transparent mb-0">
            <a
              href="#"
              @click="openMenu('setting')"
              class="list-group-item list-group-item-action d-flex align-items-center"
              :class="activeMenu == 'setting' ? 'active' : ''"
            >
              <span class="icon mr-3"><i class="fa fa-gear"></i></span
              >Pengaturan Tampilan
            </a>
            <a
              href="#"
              @click="openMenu('gallery')"
              class="list-group-item list-group-item-action d-flex align-items-center"
              :class="activeMenu == 'gallery' ? 'active' : ''"
            >
              <span class="icon mr-3"><i class="fa fa-image"></i></span>Gallery
              <span class="ml-auto badge badge-primary">{{
                Object.keys(list_banner).length
              }}</span>
            </a>
          </div>
          <div class="mt-6">
            <a href="#" class="btn btn-secondary btn-block" @click="add"
              >Tambah Gambar atau Video</a
            >
          </div>
        </div>
      </div>

      <div class="col-lg-9" v-show="showTable">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Daftar Banner</h3>
          </div>

          <div class="table-responsive">
            <table class="table card-table table-striped table-vcenter">
              <thead>
                <tr>
                  <th style="width:5%">Image</th>
                  <th style="width:20%">Judul</th>
                  <th style="width:30%">Deskripsi</th>
                  <th style="width:5%">Jenis</th>
                  <th style="width:10%"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="b in list_banner" :key="b.id">
                  <td>
                    <span
                      class="avatar"
                      v-if="b.banner_type == 'image'"
                      :style="{
                        backgroundImage: 'url(../' + b.banner_path + ')'
                      }"
                    ></span>
                    <span class="avatar" style="padding-left:5px" v-else
                      ><i class="fa fa-play"></i
                    ></span>
                  </td>
                  <td>{{ b.banner_title }}</td>
                  <td>{{ b.banner_desc }}</td>
                  <td>{{ b.banner_type }}</td>
                  <td>
                    <a href="javascript:void(0)" @click="edit(b)" class="icon"
                      ><i class="fa fa-pencil-square fa-lg text-info"></i></a
                    >&nbsp;&nbsp;
                    <a href="javascript:void(0)" @click="del(b)" class="icon"
                      ><i class="fa fa-trash fa-lg text-danger"></i
                    ></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-9" v-show="showGalleryForm">
        <setting-banner-form-gallery-component
          :bannerSelected="bannerSelected"
          :showPreview="showPreview"
          :imagePreview="imagePreview"
          @openMenu="openMenu"
          @init_data="init_data"
          @setShowPreview="setShowPreview"
          @setImagePreview="setImagePreview"
          ref="form_gallery"
        >
        </setting-banner-form-gallery-component>
      </div>

      <div class="col-lg-5" v-show="showSettingForm">
        <setting-banner-setting-component
          ref="form_setting"
        ></setting-banner-setting-component>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
  data() {
    return {
      list_banner: [],
      list_image: [],
      list_video: [],
      bannerSelected: { banner_path: "" },
      banner: [],
      errors: [],

      invalidTitle: false,
      invalidDesc: false,

      activeMenu: "setting",

      showTable: false,
      showGalleryForm: false,
      showSettingForm: true,

      showPreview: false,
      imagePreview: ""
    };
  },

  methods: {
    setShowPreview(showPreview) {
      this.showPreview = showPreview;
    },

    setImagePreview(imagePreview) {
      this.imagePreview = imagePreview;
    },

    edit(banner) {
      this.bannerSelected = Object.assign({}, banner);

      this.showGalleryForm = true;
      this.showTable = false;

      if (banner.banner_type == "video") {
        let _VIDEO = document.querySelector("#main-video");
        _VIDEO.load();
      }

      this.$refs.form_gallery.$refs.file.value = "";
      this.showPreview = false;
      this.imagePreview = "";
    },

    add() {
      this.bannerSelected = [];

      this.showGalleryForm = true;
      this.showTable = false;
      this.showSettingForm = false;
    },

    del(banner) {
      Swal.fire({
        type: "question",
        title: "Hapus Data",
        html:
          "Hapus data berikut? <br/>" +
          banner.banner_title +
          " / " +
          banner.banner_desc +
          " / " +
          banner.banner_type,
        showCancelButton: true,
        confirmButtonText: "Ya"
      }).then(result => {
        if (result.value) {
          let self = this;
          axios
            .post("banner/del", { banner_id: banner.banner_id })
            .then(response => {
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

    init_data() {
      axios
        .post("banner/list-all")
        .then(response => (this.list_banner = response.data));
    },

    openMenu(menu) {
      this.activeMenu = menu;

      this.showTable = false;
      this.showGalleryForm = false;
      this.showSettingForm = false;

      if (menu == "setting") {
        this.showSettingForm = true;
      } else if (menu == "gallery") {
        this.showTable = true;
      }

      this.$refs.form_setting.init_data();
      this.file = "";
      this.showPreview = false;
      this.imagePreview = "";
    }
  },

  created() {
    this.init_data();
  }
};
</script>
