import { FormDinamico } from './FormDinamico.js';
import { ValidadorForm } from './VailidadorForm.js';
import { Endereco } from './Endereco.js';

document.addEventListener('DOMContentLoaded', () => {     
    const formDinamico = new FormDinamico('formLogin', 'formCadastro', 'semCadastro', 'comCadastro');
    const validadorForm = new ValidadorForm('formCadastro', 'cd_senha', 'cd_confirmacao_senha', 'cadastrar', 'erro-senhas');
    const endereco = new Endereco('cd_cep', 'sg_uf', 'nm_cidade', 'nm_bairro', 'nm_logradouro', 'erro-cep');

    formDinamico.init();
    validadorForm.init();
    endereco.init();
});