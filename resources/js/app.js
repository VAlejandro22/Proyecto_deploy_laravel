import './bootstrap';
// Importar estilos para asegurar su inclusión en el bundle de producción
import '../css/app.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
