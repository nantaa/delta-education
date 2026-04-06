import './bootstrap';

/**
 * Livewire 4 bundles its own Alpine instance.
 * To extend it, hook into the `alpine:init` lifecycle event
 * BEFORE Livewire boots (which happens on DOMContentLoaded).
 * This guarantees our plugins register on the same Alpine instance
 * that Livewire controls.
 */
import Collapse from '@alpinejs/collapse';

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Collapse);
});
