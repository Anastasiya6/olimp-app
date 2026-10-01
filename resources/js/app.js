import './bootstrap';

import Alpine from 'alpinejs';

// Livewire includes and starts its own Alpine instance, with wire:* directives.
// Only start standalone Alpine on pages where Livewire is not loaded.
if (!window.Livewire && !document.querySelector('script[data-update-uri]') && !window.livewireScriptConfig) {
    window.Alpine = Alpine;
    Alpine.start();
}
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
            Livewire.dispatch('designationSelected', {value});
        }
    });
}

window.initDesignationSelect = initTomSelect;

document.addEventListener('livewire:init', initTomSelect);
document.addEventListener('livewire:navigated', initTomSelect);
