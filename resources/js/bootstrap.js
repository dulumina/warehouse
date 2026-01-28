import axios from "axios";
// import 'bootstrap';
import $ from "jquery";

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.$ = $;
window.jQuery = $;
