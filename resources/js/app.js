import './frase';
import { iniciarLightbox } from './lightbox.js';
import { iniciarMenu } from './menu.js';

document.addEventListener('DOMContentLoaded', () => {
    iniciarMenu();
    iniciarLightbox();
});
