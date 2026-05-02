export class ValidadorForm {
    constructor(formCadastro, senha, confirmarSenha, msg, tel, cep) {
        this.formCadastro = document.getElementById(formCadastro);

        this.senha = this.formCadastro?.querySelector(`#${senha}`);
        this.confirmarSenha = this.formCadastro?.querySelector(`#${confirmarSenha}`);
        this.msg = this.formCadastro?.querySelector(`#${msg}`);

        this.telefone = this.formCadastro?.querySelector(`#${tel}`);
        this.cep = this.formCadastro?.querySelector(`#${cep}`);

        this.perfilContainer = document.querySelector('.perfil-container');
    }

    init() {
        if(!this.formCadastro && !this.perfilContainer) return; 
        this.aplicaMascaras();

        if(this.senha && this.confirmarSenha) {
            this.confereSenhas();
            this.controlaValidacaoHTML();
        }
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
                primeiroCampoInvalido.scrollIntoView({ block: 'center' });
                primeiroCampoInvalido.focus();
                primeiroCampoInvalido.reportValidity();
            }
        });
    }

    aplicaMascaras() {
        // (99) 99999-9999
        if (this.telefone) {
            this.telefone.addEventListener('input', (e) => {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
                e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            });
        }

        // 12345-678
        if (this.cep) {
            this.cep.addEventListener('input', (e) => {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,3})/);
                e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
            });
        }
    }
}