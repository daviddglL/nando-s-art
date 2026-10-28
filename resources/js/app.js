import './frase';
import { iniciarConfirmaciones } from './confirmar.js';
import { iniciarLightbox } from './lightbox.js';
import { iniciarMenu } from './menu.js';

document.addEventListener('DOMContentLoaded', () => {
    iniciarMenu();
    iniciarConfirmaciones();
    iniciarLightbox();
});
