export class ValidadorForm {
    constructor(formCadastro, senha, confirmarSenha, msg) {
        this.formCadastro = document.getElementById(formCadastro);

        this.senha = this.formCadastro.querySelector(`#${senha}`);
        this.confirmarSenha = this.formCadastro.querySelector(`#${confirmarSenha}`);
        this.msg = this.formCadastro.querySelector(`#${msg}`);
    }

    init() {
        this.confereSenhas();
        this.controlaValidacaoHTML();
    }
    
    confereSenhas() {
        const validar = () => {
            const senha = this.senha.value;
            const confirmar = this.confirmarSenha.value;

            this.msg.innerHTML = "";
            this.confirmarSenha.style.outline = "";

            if (confirmar === "") return;

            if (senha !== confirmar) {
                this.confirmarSenha.style.outline = "1px solid red";
                this.msg.innerHTML = "As senhas não coincidem.";
            }
        };

        this.senha.addEventListener('input', validar);
        this.confirmarSenha.addEventListener('input', validar);
    }

    controlaValidacaoHTML(){
        this.formCadastro.addEventListener('submit', (e) => {
            const primeiroCampoInvalido = this.formCadastro.querySelector(':invalid');
            
            if (primeiroCampoInvalido) {
                e.preventDefault();
                primeiroCampoInvalido.scrollIntoView({ block: 'center' }); // sem smooth
                primeiroCampoInvalido.focus();
                primeiroCampoInvalido.reportValidity();
            }
        });
    }
}