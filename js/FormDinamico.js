export class FormDinamico {
    constructor(formLogin, formCadastro, semCadastro, comCadastro) {
        this.formLogin = document.getElementById(formLogin);
        this.formCadastro = document.getElementById(formCadastro);
        this.semCadastro = document.getElementById(semCadastro);
        this.comCadastro = document.getElementById(comCadastro);
    }

    init() {
        this.mostraFormLogin();
        
        this.semCadastro?.addEventListener('click', () => {
            this.mostraFormCadastro();
        });
        this.comCadastro?.addEventListener('click', () => {
            this.mostraFormLogin();
        });
    }

    mostraFormLogin(){
        this.formCadastro.style.display = 'none';
        this.formLogin.style.display = 'flex';
    }

    mostraFormCadastro(){
        this.formLogin.style.display = 'none';
        this.formCadastro.style.display = 'flex';
    }
}