<script>
export default {
  props: ["daftar_video"],
  data() {
    return {
      currentVideo: 0,
      base_url: document.head.querySelector('meta[name="base-url"]').content
    };
  },
  template: `<video width="100%" id="main-video" class="video" autoplay muted>
					<source>
					Your browser does not support the video tag.
				</video>
				`,
  methods: {
    nextVideo() {
      // get the element

      let videoPlayer = document.querySelector("#main-video");

      // remove the event listener, if there is one
      videoPlayer.removeEventListener("ended", this.nextVideo, false);

      // update the source with the currentVideo from the videos array
      videoPlayer.src =
        this.base_url + "/" + this.daftar_video[this.currentVideo];
      // play the video
      videoPlayer.load();
      var playPromise = videoPlayer.play();

      if (playPromise !== undefined) {
        playPromise
          .then(_ => {
            // Automatic playback started!
            // Show playing UI.
          })
          .catch(error => {
            console.log("error", error);
            // Auto-play was prevented
            // Show paused UI.
          });
      }

      // increment the currentVideo, looping at the end of the array
      this.currentVideo = (this.currentVideo + 1) % this.daftar_video.length;

      // add an event listener so when the video ends it will call the nextVideo function again
      videoPlayer.addEventListener("ended", this.nextVideo, false);
    }
  },

  mounted() {
    this.nextVideo();
  }
};
</script>
