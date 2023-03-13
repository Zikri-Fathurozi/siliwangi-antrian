<template>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="text-wrap p-lg-6">
          <h2 class="mt-0 mb-4">Rujukan Harian</h2>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <select name="beast" id="select-beast" class="form-control custom-select" v-model="month" placeholder="Bulan">
                  <option :value="m.id" v-for="m in months" :selected="month === m.id" :key="m.id">{{ m.value }}</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <select name="beast" id="select-beast" class="form-control custom-select" v-model="year" placeholder="Tahun">
                  <option :value="y.id" v-for="y in years" :key="y.id" :selected="y.id === year">{{ y.value }}</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <button type="button" class="btn btn-primary" @click="apply">Terapkan</button>
            </div>
          </div>

          <div class="row row-cards">

            <div class="col-6 col-sm-4 col-lg-3">
              <div class="card bg-green text-white">
                <div class="card-body p-3 text-center">
                  <div class="h3 m-0 mt-3">{{ data.all }} / hari</div>
                  <div class="mb-4 mt-1">Keseluruhan</div>
                </div>
              </div>
            </div>

            <div class="col-6 col-sm-4 col-lg-3" v-for="p in data.poli" :key="p.poli_id">
              <div class="card text-white" :class="'bg-'+p.poli_color">
                <div class="card-body p-3 text-center">
                  <div class="h3 m-0 mt-3">{{ p.total }} / hari</div>
                  <div class="mb-4 mt-1">{{ p.poli_nama }}</div>
                </div>
              </div>
            </div>

          </div>

        </div>

      </div>
    </div>
	</div>
</template>

<script>

    import moment from 'moment';
    moment.locale('id');

    function get_years() {
      var currentYear = new Date().getFullYear(), years = [];
      var startYear = currentYear - 5;
      while ( startYear <= currentYear ) {
          let y = startYear++
          years.push({
            id: y,
            value: y
          });
      }
      return years;
    }

    let months = []
    let list_months = moment.months()
    for(var x=0;x<list_months.length;x++){
      months.push({
        id: x+1,
        value: list_months[x]
      })
    }

		export default {
				data(){
					return{
						data: {
              all : 0,
              poli: []
            },
            months,
            years: get_years(),
            year: new Date().getFullYear(),
            month: new Date().getMonth(),

					}
				},

				methods : {
          init_data(){
						this.apply()
					},

          apply(){
            let self = this
            axios
						.post('report/rujukan', {month: this.month, year:this.year})
						.then(response => {
              self.data = response.data
            });
          }

				},

				created(){
					this.init_data();
				},


    }
</script>
