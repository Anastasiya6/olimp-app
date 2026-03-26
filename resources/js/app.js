import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// ---- TomSelect ----
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

// (опціонально) ти можеш зробити доступним глобально, якщо хочеш
window.TomSelect = TomSelect;
