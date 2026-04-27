import { FormDinamico } from './FormDinamico.js';
import { ValidadorForm } from './VailidadorForm.js';

document.addEventListener('DOMContentLoaded', () => {     
    const formDinamico = new FormDinamico('formLogin', 'formCadastro', 'semCadastro', 'comCadastro');
    const validadorForm = new ValidadorForm('formCadastro', 'cd_senha', 'cd_confirmacao_senha', 'cadastrar', 'msg-erro');

    formDinamico.init();
    validadorForm.init();
});