import { FormDinamico } from './FormDinamico.js';
import { ValidadorForm } from './VailidadorForm.js';
import { Endereco } from './Endereco.js';
import { Trocas } from './Trocas.js';
import { TrocaFotos } from './TrocaFotos.js';

document.addEventListener('DOMContentLoaded', () => {     
    const formDinamico = new FormDinamico('formLogin', 'formCadastro', 'semCadastro', 'comCadastro');
    const validadorForm = new ValidadorForm('formCadastro', 'cd_senha', 'cd_confirmacao_senha', 'erro-senhas', 'cd_telefone', 'cd_cep');
    const endereco = new Endereco('cd_cep', 'sg_uf', 'nm_cidade', 'nm_bairro', 'nm_logradouro', 'erro-cep');
    const trocas = new Trocas('lista-livros', 'modalTroca');
    const trocaFotos = new TrocaFotos('input-foto');

    formDinamico?.init();
    validadorForm?.init();
    endereco?.init();
    trocas?.init();
    trocaFotos?.init();
});