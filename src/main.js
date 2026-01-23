import { createApp } from 'vue';
import router from './router';
//import VueGtag from "vue-gtag";
import App from './App.vue';
import './assets/css/animate.css'
import './assets/css/font-awesome.min.css'
import './assets/css/font.css'
import './assets/css/bootstrap.css'
import './assets/css/nestable.css'
import './assets/css/app.css'
import './assets/css/landing.css'
import './assets/css/ZMSoftware.css'
import { SystemInformation } from './SystemInformation';

// const HOST_LOCALE            = 'localhost:8080';
//

SystemInformation.Init(function()
{
  // const host = ;
  // if(host == HOST_ARTAX_ANTINCENDIO)
  //   createApp(App).use(VueGtag, { config: { id: "G-8NP3FJK11N" }}).use(router).mount('#ZArtaxApp');
  //else 
  createApp(App).use(router).mount('#ZGeminiApp');
});
