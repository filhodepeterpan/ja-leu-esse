import { FormDinamico } from './FormDinamico.js';

document.addEventListener('DOMContentLoaded', () => {     
    const formDinamico = new FormDinamico('formLogin', 'formCadastro', 'semCadastro', 'comCadastro');
    formDinamico.init();
});