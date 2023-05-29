/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component(
  "loket-pendaftaran-component",
  require("./components/LoketPendaftaranComponent.vue").default
);
Vue.component(
  "loket-poli-component",
  require("./components/LoketPoliComponent.vue").default
);
Vue.component(
  "loket-kamar-component",
  require("./components/LoketKamarComponent.vue").default
);
Vue.component(
  "loket-tensi-component",
  require("./components/LoketTensiComponent.vue").default
);

Vue.component(
  "ticket-dispenser-component",
  require("./components/TicketDispenserComponent.vue").default
);
Vue.component(
  "ticket-dispenser-new-component",
  require("./components/TicketDispenserNewComponent.vue").default
);

if (process.env.MIX_MULTILOKET_PENDAFTARAN === "true") {
  Vue.component(
    "display-pendaftaran-component",
    require("./components/displays/multiloket/DisplayPendaftaranComponent.vue")
      .default
  );
} else {
  Vue.component(
    "display-pendaftaran-component",
    require("./components/displays/singleloket/DisplayPendaftaranComponent.vue")
      .default
  );
}

Vue.component(
  "display-poli-component",
  require("./components/displays/DisplayPoliComponent.vue").default
);

//admin
Vue.component(
  "setting-profile-component",
  require("./components/admin/SettingProfileComponent.vue").default
);
Vue.component(
  "setting-users-component",
  require("./components/admin/SettingUsersComponent.vue").default
);
Vue.component(
  "setting-channel-component",
  require("./components/admin/SettingChannelComponent.vue").default
);
Vue.component(
  "setting-poli-component",
  require("./components/admin/SettingPoliComponent.vue").default
);
Vue.component(
  "setting-printer-component",
  require("./components/admin/SettingPrinterComponent.vue").default
);
Vue.component(
  "setting-service-component",
  require("./components/admin/SettingServiceComponent.vue").default
);
Vue.component(
  "setting-device-component",
  require("./components/admin/SettingDeviceComponent.vue").default
);
Vue.component(
  "setting-info-component",
  require("./components/admin/SettingInfoComponent.vue").default
);
Vue.component(
  "setting-banner-component",
  require("./components/admin/SettingBannerComponent.vue").default
);
Vue.component(
  "setting-banner-setting-component",
  require("./components/admin/banner/SettingComponent.vue").default
);
Vue.component(
  "setting-banner-form-gallery-component",
  require("./components/admin/banner/formGalleryComponent.vue").default
);
Vue.component(
  "setting-update-component",
  require("./components/admin/SettingUpdateComponent.vue").default
);

Vue.component(
  "report-visit-component",
  require("./components/admin/ReportVisitComponent.vue").default
);
Vue.component(
  "report-rujukan-component",
  require("./components/admin/ReportRujukanComponent.vue").default
);
Vue.component(
  "report-antrian-component",
  require("./components/admin/ReportAntrianComponent.vue").default
);
Vue.component(
  "report-pasien-component",
  require("./components/admin/ReportPasienComponent.vue").default
);

//element
Vue.component(
  "poli-component",
  require("./components/elements/PoliComponent.vue").default
);
Vue.component(
  "audio-component",
  require("./components/elements/AudioComponent.vue").default
);
Vue.component(
  "banner-component",
  require("./components/elements/BannerComponent.vue").default
);
Vue.component(
  "banner-indicator-component",
  require("./components/elements/BannerIndicatorComponent.vue").default
);
Vue.component(
  "video-component",
  require("./components/elements/VideoComponent.vue").default
);
Vue.component(
  "time-component",
  require("./components/elements/TimeComponent.vue").default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
  el: "#app"
});
