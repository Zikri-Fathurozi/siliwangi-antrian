<template> </template>
<script>
import terbilang from "../../utils/terbilang";
export default {
  props: ["loket", "app_multiloket"],
  data() {
    return {
      audios_name: [
        "bell",
        "nomor_urut",
        "loket",
        "menuju",
        "ruangan",
        "pendaftaran",
        "poli_umum",
        "poli_gigi",
        "poli_anak",
        "poli_konseling",
        "poli_kia",
        "poli_lansia",
        "poli_rujukan",
        "poli_tb",
        "poli_vaksin",
        "ruang_tensi",
        "P",
        "U",
        "A",
        "L",
        "G",
        "K",
        "T",
        "S",
        "R",
        "V",
        "0",
        "satu",
        "dua",
        "tiga",
        "empat",
        "lima",
        "enam",
        "tujuh",
        "delapan",
        "sembilan",
        "sepuluh",

        "sebelas",
        "duabelas",
        "tigabelas",
        "empatbelas",
        "limabelas",
        "enambelas",
        "tujuhbelas",
        "delapanbelas",
        "sembilanbelas",

        "duapuluh",
        "tigapuluh",
        "empatpuluh",
        "limapuluh",
        "enampuluh",
        "tujuhpuluh",
        "delapanpuluh",
        "sembilanpuluh",

        "seratus",
        "duaratus",
        "tigaratus",
        "empatratus",
        "limaratus",
        "enamratus",
        "tujuhratus",
        "delapanratus",
        "ratus"
      ],
      base_url: document.head.querySelector('meta[name="base-url"]').content,
      audio_poli: "",
      voice: "male",
      ctx: new (window.AudioContext || window.webkitAudioContext)(),
      audios_buffer: []
    };
  },

  methods: {
    terbilang(a) {
      return terbilang(a);
    },

    play_audio(params) {
      console.log('params')
      console.log(params)
      let nomor = params.nomor.toString();
      let time = 0;
      // pembukaan
      const sequence = ["bell", "nomor_urut"];

      // nomor, misal kosong - kosong
      for (var i = 0; i < nomor.length; i++) {
        let id = nomor[i];
        if (parseInt(id) === 0 || isNaN(parseInt(id))) {
          sequence.push(id);
        } else {
          break;
        }
      }

      const addTerbilangToSequence = (sequence, nomor) => {
        var arr_terbilang = this.terbilang(nomor);
        for (var i = 0; i < arr_terbilang.length; i++) {
          let text = arr_terbilang[i];
          sequence.push(text);
        }
        return sequence;
      };

      // terbilang
      var terbilang = parseInt(nomor.substring(i, nomor.length));
      addTerbilangToSequence(sequence, terbilang);

      if (params.tujuan == "pendaftaran") {
        sequence.push("loket");
        console.log('masuk pendaftaran')
        console.log(params.loket)
        console.log('params.loket')
        if (params.loket) {
          addTerbilangToSequence(sequence, params.loket);
        } else {
          sequence.push("pendaftaran");
        }
      } else {
        sequence.push("menuju");
        if (params.tujuan == 'kamar') {
          sequence.push("ruangan")
          addTerbilangToSequence(sequence, params.kamar)
        } else {
          sequence.push(params.poli.poli_audio);
        }
      }

      sequence.forEach(name => {
        console.log('name: '+  name)
        const audioIndex = this.audios_name.findIndex(
          audioName => audioName === name
        );

        let source = this.ctx.createBufferSource();
        source.buffer = this.audios_buffer[audioIndex];
        source.connect(this.ctx.destination);
        source.start(this.ctx.currentTime + time);
        if (source.buffer.duration) {
          time += source.buffer.duration;
        }
      });
      return time;
    },

    fetchBuffers() {
      const audios = this.audios_name.map(
        name => `${this.base_url}/audios/${this.voice}/audio_${name}.mp3`
      );

      return Promise.all(
        audios.map(url =>
          fetch(url)
            .then(r => r.arrayBuffer())
            .then(buf => this.ctx.decodeAudioData(buf))
        )
      );
    }
  },

  async created() {
    this.audios_buffer = await this.fetchBuffers();
  }
};
</script>