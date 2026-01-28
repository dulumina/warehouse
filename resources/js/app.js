import "./bootstrap";

import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";

window.Alpine = Alpine;

Alpine.plugin(collapse);

Alpine.start();
