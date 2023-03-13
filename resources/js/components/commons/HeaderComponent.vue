<template>
  <div class="header app py-5 cstm_header">
    <div class="col-sm-12 h-10 text-white ng-scope">
      <div class="row h-100">
        <div class="col-sm-2 text-center pt-2">
          <img :src="base_url + '/logo-left.png'" border="0" />
        </div>
        <div class="col-sm-8 text-center title">
          <span class="h1 font-weight-bold ng-binding">{{ app_name }}</span
          ><br />
          <span class="h5 ng-binding">{{ app_address }}</span>
        </div>
        <div class="col-sm-2 text-center pt-2">
          <img :src="base_url + '/logo-right.png'" border="0" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["app_name", "app_address"],
  data() {
    return {
      base_url: document.head.querySelector('meta[name="base-url"]').content
    };
  }
};
</script>
