<template>
  <span>{{ today }}</span>
</template>

<script>
import moment from "moment";
moment.locale("id");

export default {
  data() {
    return {
      today: moment().format("dddd, Do MMMM YYYY H:mm:ss")
    };
  },
  created() {
    var self = this;
    setInterval(function() {
      self.today = moment().format("dddd, Do MMMM YYYY H:mm:ss");
    }, 1000);
  }
};
</script>
