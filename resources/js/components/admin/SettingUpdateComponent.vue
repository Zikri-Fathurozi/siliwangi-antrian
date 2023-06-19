<template>
  <div class="mb-3 mt-5">
    <hr />
    <div class="divide-y">
      <div style="">
        <label class="row">
          <span class="col">Semua Browser</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch">
              <button
                type="button"
                @click="update('all')"
                class="btn btn-primary ml-auto"
              >
                Refresh All
              </button>
            </label>
          </span>
        </label>
      </div>
      <div>
        <label class="row">
          <span class="col">Display Monitor</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch">
              <button
                type="button"
                @click="update('display')"
                class="btn btn-success ml-auto"
              >
                Refresh
              </button>
            </label>
          </span>
        </label>
      </div>
      <div>
        <label class="row">
          <span class="col">Loket</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch">
              <button
                type="button"
                @click="update('loket')"
                class="btn btn-success ml-auto"
              >
                Refresh
              </button>
            </label>
          </span>
        </label>
      </div>
      <div>
        <label class="row">
          <span class="col">Tiket Dispenser</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch">
              <button
                type="button"
                @click="update('tiket_dispenser')"
                class="btn btn-success ml-auto"
              >
                Refresh
              </button>
            </label>
          </span>
        </label>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";

const ws = new WebSocket(
  document.head.querySelector('meta[name="web-socket"]').content
);
export default {
  data() {
    return {
      ws
    };
  },

  methods: {
    update(type) {
      let self = this;

      const typeMap = [
        { type: "all", label: "Semua Browser" },
        { type: "loket", label: "Browser Loket" },
        { type: "tiket_dispenser", label: "Browser Tiket Dispenser" },
        { type: "display", label: "Browser Display Monitor" }
      ];

      const typeLabel = typeMap.find(t => t.type === type).label;

      Swal.fire({
        title: "Refresh Aplikasi",
        html: `<br/>Terapkan update untuk <b class="text-primary">${typeLabel}</b> yang aktif?<br/><br/>`,
        showCancelButton: true
      }).then(result => {
        if (result.value) {
          if (type === "all") {
            typeMap.map(t => {
              self.ws.send(
                JSON.stringify({ target: t.type, action: "refresh_browser" })
              );
            });
          } else {
            self.ws.send(
              JSON.stringify({ target: type, action: "refresh_browser" })
            );
          }
        }
      });
    }
  }
};
</script>
