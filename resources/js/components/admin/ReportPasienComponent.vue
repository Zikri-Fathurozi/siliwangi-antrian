<template>
  <div class="container">
    <div class="row row-cards">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Data Pasien JKN
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
                  <div class="col-md-4"></div>
                  <div class="col-md-5">
                    <div class="input-group mb-2">
                      <input
                        type="text"
                        class="form-control"
                        v-model="tableFilters.search"
                        ref="inputDate"
                        placeholder="Search NIK / Nomor Kartu / Nama"
                      />
                      <button
                        class="btn"
                        type="button"
                        @click="tableFilters.search = ''"
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
Vue.use(DataTable);

export default {
  data() {
    return {
      poli: [],
      channel: [],
      url: `${window.axios.defaults.baseURL}/report/pasien/datatable`,
      data: {},
      tableProps: {
        search: "",
        length: 10
      },
      filters: {
        poli_id: "",
        channel_id: "",
        dirujuk: ""
      },
      columns: [
        {
          label: "Nomor Kartu",
          name: "pasien_nomor_kartu"
        },
        {
          label: "NIK",
          name: "pasien_nik"
        },
        {
          label: "Nama",
          name: "pasien_nama"
        },
        {
          label: "Gender",
          name: "pasien_gender"
        },
        {
          label: "Alamat",
          name: "pasien_address"
        }
      ]
    };
  },

  created() {
    this.getData(this.url);
  },
  methods: {
    getData(url = this.url, options = this.tableProps) {
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
