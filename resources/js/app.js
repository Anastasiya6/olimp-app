import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// ---- TomSelect ----
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

// (опціонально) ти можеш зробити доступним глобально, якщо хочеш
window.TomSelect = TomSelect;

function initTomSelect() {
    const el = document.querySelector('#designation-select');
    if (!el) return;

    if (el.tomselect) return;

    new TomSelect(el, {
        valueField: 'value',
        labelField: 'text',
        searchField: ['text'],
        maxItems: 1,

        load(query, callback) {
            fetch(`/api/designations/search?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(callback)
                .catch(() => callback());
        },

        onChange(value) {
            Livewire.dispatch('designationSelected', value);
        }
    });
}

document.addEventListener('livewire:init', initTomSelect);
document.addEventListener('livewire:navigated', initTomSelect);

