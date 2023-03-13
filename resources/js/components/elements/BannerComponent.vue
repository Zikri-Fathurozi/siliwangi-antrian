<script>
export default {
  data() {
    return {
      base_url: document.head.querySelector('meta[name="base-url"]').content
    };
  },
  props: ["banner", "index"],
  template: `<div class="carousel-item" :class="index==1?'active':''">
				<img class="d-block w-100 banner" alt="" :src="base_url+'/'+banner.banner_path" data-holder-rendered="true">
				<div class="carousel-caption d-none d-md-block" style="background:#FFF;opacity:.5;color:#000">
				</div>
			</div>`
};
</script>
