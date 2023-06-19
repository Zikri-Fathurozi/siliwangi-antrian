<script>
export default {
  props: ["poli"],
  template: `<div :class="poli.poli_gedung==1? 'col-sm-6' : 'col-sm-4'">
						<div class="card ">
							<div class="card-header" :class="'bg-'+poli.poli_color">
							<h3 class="m-0" style="color:#FFF">
								{{poli.poli_nama}}
							</h3>
						</div>
						<div class="card-body text-center">
						  <div class="display-3 font-weight-bold m-0"> {{ poli.tiket_poli_nomor }}</div>
						    <div class="progress progress-sm">
								<div class="progress-bar bg-white" style="width: 100%"></div>
							</div>
						  </div>
						</div>
					</div>`
};
</script>
