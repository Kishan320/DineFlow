import './bootstrap';
import '../css/app.css';
import 'vue-toastification/dist/index.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueApexCharts from 'vue3-apexcharts';
import Toast, { POSITION } from 'vue-toastification';

import router from './router/index.js';
import App from './App.vue';

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(VueApexCharts);
app.use(Toast, {
  position: POSITION.BOTTOM_RIGHT,
  timeout: 3000,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  toastClassName: 'rms-toast',
});

app.mount('#app');
