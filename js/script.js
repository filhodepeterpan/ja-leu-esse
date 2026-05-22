import { FormDinamico } from './FormDinamico.js';
import { ValidadorForm } from './VailidadorForm.js';
import { Endereco } from './Endereco.js';
import { Trocas } from './Trocas.js';
import { TrocaFotos } from './TrocaFotos.js';
import { Chat } from './Chat.js';
import { MoverCarrossel } from './MoverCarrossel.js';
import { ModalSair } from './ModalSair.js';

document.addEventListener('DOMContentLoaded', () => {     
    const formDinamico = new FormDinamico('formLogin', 'formCadastro', 'semCadastro', 'comCadastro');
    const validadorForm = new ValidadorForm('formCadastro', 'cd_senha', 'cd_confirmacao_senha', 'erro-senhas', 'cd_telefone', 'cd_cep');
    const endereco = new Endereco('cd_cep', 'sg_uf', 'nm_cidade', 'nm_bairro', 'nm_logradouro', 'erro-cep');
    const trocas = new Trocas('lista-livros', 'modalTroca');
    const trocaFotos = new TrocaFotos('input-foto');
    const chat = new Chat();
    const moverCarrossel = new MoverCarrossel();
    const modalSair = new ModalSair();

    formDinamico?.init();
    validadorForm?.init();
    endereco?.init();
    trocas?.init();
    trocaFotos?.init();
    chat?.init();
    moverCarrossel?.init();
    modalSair?.init();
});