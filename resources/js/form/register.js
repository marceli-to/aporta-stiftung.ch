// app.js
import '@/bootstrap';

// Vue
import {createApp} from 'vue';
import App from './Register.vue';
const app = createApp(App);

// Axios
import VueAxios from "vue-axios";
app.use(VueAxios, axios);

// Mount app
app.mount("#form-register");
