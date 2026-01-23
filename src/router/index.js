import { createRouter, createWebHashHistory } from 'vue-router'
import { SystemInformation } from '@/SystemInformation.js';
import VUEGestionale         from '../views/VUEGestionale.vue';
import VUEFeatures           from '../views/VUEFeatures.vue';
import VUELogin              from '../views/VUELogin.vue';
import VUERegistrati         from '../views/VUERegistrati.vue';
import VUEPage404            from '../views/VUEPage404.vue';
import VUESchedaUtente       from '../views/VUESchedaUtente.vue';
import VUEMessaggio          from '../views/VUEMessaggio.vue';
import VUEPrimoAccesso       from '../views/VUEPrimoAccesso.vue';
import VUEIframeStampe       from '../components/FrameComponentsMultiPurpose/VUEIframe.vue';

const routes = [
  {
    path : '/',
    name : 'Home',
    component : VUELogin
  },
  {
    path: '/login',
    name: 'Login',
    component: VUELogin
  },
  {
    path: '/Registrati',
    name: 'Registrati',
    component: VUERegistrati
  },
  {
    path: '/appMainWindow/:pagina',
    name: 'MainWindow',
    component: VUEGestionale
  },
  {
    path: '/features',
    name: 'Features',
    component: VUEFeatures
  },
  { path: '/:pathMatch(.*)*', component: VUEPage404 },
  {
    path: '/SchedaUtente/:pagina',
    name: 'SchedaUtente',
    component: VUESchedaUtente
  },
  {
    path: '/Messaggio/:pagina',
    name: 'Messaggio',
    component: VUEMessaggio
  },
  {
    path: '/PrimoAccesso',
    name: 'PrimoAccesso',
    component: VUEPrimoAccesso
  },
  {
    path: '/IFrameStampe',
    name: 'IFrameStampe',
    component: VUEIframeStampe
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
});
SystemInformation.Router = router;

export default router