<template>
  <footer id="footer">
    <div class="bg-dark dker wrapper">
      <div class="container text-center  m-t-lg">
        <div class="row justify-content-center align-items-start">

          <!-- Sezione "Dove siamo" -->
          <div class="col-md-4 mb-4" data-ride="animated" data-animation="fadeInLeft" data-delay="300">
            <i class="fa fa-map-marker fa-3x icon-muted mb-3"></i>
            <h5 class="text-uppercase mb-3">Dove siamo</h5>
            <p class="text-sm">
              <strong>SMART TRACK SRL</strong><br>
              Via Gian Pietro Sery 11/4<br>
              16154 Genova (IT)<br>
              P.IVA 02291180996
            </p>
          </div>

          <!-- Sezione "Contattaci" -->
          <div class="col-md-4 mb-4" data-ride="animated" data-animation="fadeInUp" data-delay="600">
            <i class="fa fa-mobile fa-3x icon-muted mb-3"></i>
            <h5 class="text-uppercase mb-3">Contattaci</h5>
            <div class="d-flex justify-content-center gap-3">
              <a href="https://t.me/+393474526664" target="_blank">
                <img src="img/Telegram.png" alt="Telegram" width="32" height="32">
              </a>
              <a href="tel:+393474526664">
                <img src="img/Telefono.png" alt="Telefono" width="32" height="32">
              </a>
              <a href="https://wa.me/+393474526664" target="_blank">
                <img src="img/Whatsapp.png" alt="Whatsapp" width="32" height="32">
              </a>
              <a href="https://www.facebook.com/smarttrack.io" target="_blank">
                <i class="fa fa-facebook" style="font-size: 32px; color: #3b5998;"></i>
              </a>
              <a href="https://www.linkedin.com/company/smart-track-s.r.l/" target="_blank">
                <i class="fa fa-linkedin" style="font-size: 32px; color: #0e76a8;"></i>
              </a>
            </div>
          </div>

          <!-- Sezione "Chi siamo" -->
          <div class="col-md-4 mb-4" data-ride="animated" data-animation="fadeInRight" data-delay="900">
            <i class="fa fa-globe fa-3x icon-muted mb-3"></i>
            <h5 class="text-uppercase mb-3">Chi siamo</h5>
            <p class="text-sm">
              <a href="https://www.smarttrack.io" class="text-light">
                CLICCA QUI PER SCOPRIRE CHI SIAMO
              </a>
            </p>
          </div>

        </div>
      </div>
    </div>
  </footer>
</template>


<script>
export default {
  name: "VUEFooter",
  data() {
    return {};
  },
  components: {},
  methods: {},
};
</script>

<style scoped>
.d-flex {
  display: flex;
}
.justify-content-center {
  justify-content: center;
}
.gap-3 {
  gap: 1rem;
}
.align-items-center {
  align-items: center;
}
</style>
