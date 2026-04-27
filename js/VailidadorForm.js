export class ValidadorForm {
    constructor(formCadastro, senha, confirmarSenha, submit, msg) {
        this.formCadastro = document.getElementById(formCadastro);

        this.senha = this.formCadastro.querySelector(`#${senha}`);
        this.confirmarSenha = this.formCadastro.querySelector(`#${confirmarSenha}`);
        this.submit = this.formCadastro.querySelector(`#${submit}`);
        this.msg = this.formCadastro.querySelector(`#${msg}`);
    }

    init() {
        this.confereSenhas();
    }
    
    confereSenhas() {
        const validar = () => {
            const senha = this.senha.value;
            const confirmar = this.confirmarSenha.value;

            if (confirmar === "") {
                this.confirmarSenha.style.border = "";
                this.msg.innerHTML = "";
                this.submit.disabled = true;
                return;
            }

            if (senha !== confirmar) {
                this.confirmarSenha.style.outline = "1px solid red";
                this.msg.innerHTML = "As senhas não coincidem.";
                this.submit.disabled = true;
            } 
            else {
                this.msg.innerHTML = "";
                this.confirmarSenha.style.outline =  "none";    
                this.submit.disabled = false;
            }
        };

        this.senha.addEventListener('input', validar);
        this.confirmarSenha.addEventListener('input', validar);
    }
}