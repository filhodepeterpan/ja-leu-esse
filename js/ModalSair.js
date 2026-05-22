export class ModalSair {

    constructor() {
        this.formLogout = document.getElementById('form-user-logout');
        this.modalSair = document.getElementById('modalSair');
        this.btnConfirmarSair = document.getElementById('btnConfirmarSair');
        this.btnCancelarSair = document.getElementById('btnCancelarSair');
    }

    init() {
        if (!this.formLogout) return;

        this.exibeModal();
        this.manipulaBotoes();
    }

    exibeModal() {
        this.formLogout?.addEventListener('submit', (event) => {
            event.preventDefault();
            this.modalSair.style.display = 'flex'; // ← this.
        });
    }

    manipulaBotoes() {
        this.btnConfirmarSair?.addEventListener('click', () => {
            this.formLogout.submit();
        });

        this.btnCancelarSair?.addEventListener('click', () => {
            this.modalSair.style.display = 'none'; // ← this.
        });

        window.addEventListener('click', (event) => {
            if (event.target === this.modalSair) { // ← this.
                this.modalSair.style.display = 'none';
            }
        });
    }
}