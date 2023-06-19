<script>
export default {
  data() {
    return {
      base_url: document.head.querySelector('meta[name="base-url"]').content
    };
  },
  props: ["banner", "index"],
  template: `<div class="carousel-item" :class="index==1?'active':''">
				<img class="d-block w-100 banner banner-poli" alt="" :src="base_url+'/'+banner.banner_path" data-holder-rendered="true">
				<div class="carousel-caption d-none d-md-block" style="background:#FFF;opacity:.5;color:#000">
				<h3>{{ banner.banner_title }}</h3>
				<p>{{ banner.banner_desc }}</p>
				</div>
			</div>`
};
</script>
