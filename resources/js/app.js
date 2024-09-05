import { createApp } from 'vue';
import router from "./router/router";
import App from './App.vue';
import '../js/app.css';
import VueApexCharts from 'vue3-apexcharts';

const app = createApp(App);
app.use(VueApexCharts);
// app.component('apexchart', VueApexCharts);
app.use(router);
app.mount('#app')