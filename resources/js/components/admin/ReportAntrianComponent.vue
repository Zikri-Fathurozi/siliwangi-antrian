<template>
  <div class="container">
    <div class="row row-cards">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Data Antrian
              <span class="badge badge-primary"></span>
            </h3>
          </div>

          <div class="table-responsive p-4">
            <data-table
              :filters="filters"
              :data="data"
              :columns="columns"
              @on-table-props-changed="reloadTable"
              class="table card-table table-striped table-vcenter"
            >
              <div slot="filters" slot-scope="{ tableFilters, perPage }">
                <div class="row mb-2">
                  <div class="col-md-3">
                    <select
                      class="form-control custom-select"
                      v-model="tableFilters.length"
                    >
                      <option :key="page" v-for="page in perPage">{{
                        page
                      }}</option>
                    </select>
                  </div>
                  <div class="col-md-2">
                    <select
                      v-model="tableFilters.filters.poli_id"
                      class="form-control custom-select"
                    >
                      <option value="">SEMUA POLI</option>
                      <option
                        :value="p.poli_id"
                        v-for="p in poli"
                        :key="p.poli_id"
                        >{{ p.poli_nama }}</option
                      >
                    </select>
                  </div>
                  <div class="col-md-2">
                    <select
                      v-model="tableFilters.filters.dirujuk"
                      class="form-control custom-select"
                    >
                      <option value="">SEMUA PASIEN</option>
                      <option value="1">Dirujuk</option>
                      <option value="0">Tidak Dirujuk</option>
                      >
                    </select>
                  </div>
                  <div class="col-md-2">
                    <select
                      v-model="tableFilters.filters.channel_id"
                      class="form-control custom-select"
                    >
                      <option value="">SEMUA CHANNEL</option>
                      <option
                        :value="c.channel_id"
                        v-for="c in channel"
                        :key="c.channel_id"
                        >{{ c.channel_nama }}</option
                      >
                    </select>
                  </div>
                  <div class="col-md-3">
                    <div class="input-group mb-2">
                      <input
                        type="date"
                        class="form-control"
                        v-model="tableFilters.filters.tiket_date"
                        ref="inputDate"
                        placeholder="Search Table"
                      />
                      <button
                        class="btn"
                        type="button"
                        @click="tableFilters.filters.tiket_date = ''"
                      >
                        Clear
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </data-table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from "laravel-vue-datatable";
import moment from "moment";
moment.locale("id");
Vue.use(DataTable);

export default {
  data() {
    return {
      poli: [],
      channel: [],
      url: `${window.axios.defaults.baseURL}/report/antrian/datatable`,
      data: {},
      tableProps: {
        search: "",
        length: 10
      },
      filters: {
        poli_id: "",
        channel_id: "",
        dirujuk: "",
        tiket_date: moment(new Date()).format("Y-MM-DD")
      },
      columns: [
        {
          label: "Tanggal Periksa",
          name: "tiket_date"
        },
        {
          label: "Nomor",
          name: "tiket_nomor"
        },
        {
          label: "Poli",
          name: "poli.poli_nama"
        },
        {
          label: "NIK Pasien",
          name: "tiket_pasien_nik"
        },
        {
          label: "Pasien Dirujuk?",
          name: "tiket_pasien_dirujuk",
          transform: ({ data, name }) => (data[name] == "0" ? "Tidak" : "Ya")
        },
        {
          label: "Channel",
          name: "channel.channel_nama"
        }
      ]
    };
  },

  created() {
    this.getData(this.url);
    axios.post("poli/list-all").then(response => (this.poli = response.data));
    axios
      .post("channel/list/type")
      .then(response => (this.channel = response.data));
  },
  methods: {
    getData(
      url = this.url,
      options = {
        ...this.tableProps,
        filters: this.filters
      }
    ) {
      axios
        .get(url, {
          params: options
        })
        .then(response => {
          this.data = response.data;
        })
        // eslint-disable-next-line
        .catch(errors => {
          //Handle Errors
        });
    },
    reloadTable(tableProps) {
      this.getData(this.url, tableProps);
    },
    clearDate() {
      this.$refs.inputDate.value = "";
    }
  }
};
</script>
