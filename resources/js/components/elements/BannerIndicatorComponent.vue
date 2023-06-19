<script>
export default {
  props: ["banner", "index"],
  template: `<li data-target="#carousel-indicators" :data-slide-to="index" :class="[index==1?'active':'']"></li>`
};
</script>
