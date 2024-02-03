const modules = import.meta.glob('./**/*.jsx');
console.log(modules);

import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
