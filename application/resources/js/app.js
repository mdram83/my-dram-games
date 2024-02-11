const modules = import.meta.glob('./**/*.jsx');
modules.hasOwnProperty(undefined);

import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
