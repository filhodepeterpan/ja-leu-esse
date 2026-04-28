export class Trocas {
    constructor(listaId, modalId) {
        this.lista = document.getElementById(listaId);
        this.modal = document.getElementById(modalId);

        this.btnFechar = document.getElementById('modalFechar');
        this.btnAdicionar = document.getElementById('btnAdicionar');
        this.btnNegociar = document.getElementById('btnNegociar');
        this.btnTrocar = document.getElementById('btnTrocar');

        this.seletor = document.getElementById('seletorLivros');
        this.seletorFechar = document.getElementById('seletorFechar');

        this.slotPlaceholder = document.getElementById('slotPlaceholder');
        this.slotOfertaImg = document.getElementById('slotOfertaImg');
        this.slotOfertaNome = document.getElementById('slotOfertaNome');

        this.slotDesejoImg = document.getElementById('slotDesejoImg');
        this.slotDesejoNome = document.getElementById('slotDesejoNome');

        this.livroDesejado = null;
        this.livroOferta = null;
    }

    init() {
        if (!this.lista) return;

        this.lista.querySelectorAll('.card-livro').forEach(card => {
            card.addEventListener('click', () => this.abrirModal(card.dataset));
        });

        this.btnFechar.addEventListener('click', () => this.fecharModal());

        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) this.fecharModal();
        });

        this.btnAdicionar.addEventListener('click', (e) => {
            e.stopPropagation();
            this.abrirSeletor();
        });

        this.seletorFechar.addEventListener('click', () => this.fecharSeletor());

        this.seletor.querySelectorAll('.seletor-card').forEach(card => {
            card.addEventListener('click', () => this.selecionarOferta(card.dataset));
        });

        this.btnNegociar.addEventListener('click', () => {
            window.location.href = 'perfil.php';
        });

        this.btnTrocar.addEventListener('click', () => this.resetarOferta());
    }

    abrirModal(dataset) {
        this.livroDesejado = { nome: dataset.nome, url: dataset.url, alt: dataset.alt };

        this.slotDesejoImg.src = this.livroDesejado.url;
        this.slotDesejoImg.alt = this.livroDesejado.alt;
        this.slotDesejoNome.textContent = this.livroDesejado.nome;

        this.modal.classList.remove('hidden');
    }

    fecharModal() {
        this.modal.classList.add('hidden');
        this.fecharSeletor();
        this.resetarOferta();
    }

    abrirSeletor() {
        this.seletor.classList.remove('hidden');
    }

    fecharSeletor() {
        this.seletor.classList.add('hidden');
    }

    selecionarOferta(dataset) {
        this.livroOferta = { nome: dataset.nome, url: dataset.url, alt: dataset.alt };

        this.slotOfertaImg.src = this.livroOferta.url;
        this.slotOfertaImg.alt = this.livroOferta.alt;
        this.slotOfertaNome.textContent = this.livroOferta.nome;

        this.slotPlaceholder.classList.add('hidden');
        this.slotOfertaImg.classList.remove('hidden');
        this.slotOfertaNome.classList.remove('hidden');
        this.btnTrocar.classList.remove('hidden');

        this.fecharSeletor();
        this.atualizarBotao();
    }

    resetarOferta() {
        this.livroOferta = null;
        this.slotPlaceholder.classList.remove('hidden');
        this.slotOfertaImg.classList.add('hidden');
        this.slotOfertaNome.classList.add('hidden');
        this.btnTrocar.classList.add('hidden')
        this.btnNegociar.disabled = true;
    }

    atualizarBotao() {
        this.btnNegociar.disabled = !(this.livroOferta && this.livroDesejado);
    }
}