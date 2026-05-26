
export class MoverCarrossel {
    constructor() {
        this.trilhoCarrossel = document.querySelector('.c-grupo');
        this.fileiraLivros = document.querySelectorAll('.c-grupo .c-card');
        this.setaDireita = document.querySelector('.btn-seta-direita');
        this.setaEsquerda = document.querySelector('.btn-seta-esquerda');
    }

    init() {
        if (!this.trilhoCarrossel || !this.setaDireita || !this.setaEsquerda) return;

        this.setas();
    }

    setas() {
        // ================================

        // seta >

        this.setaDireita?.addEventListener('click', () => {
            const moverDireita = this.trilhoCarrossel.clientWidth * 0.50;

            if (this.trilhoCarrossel.scrollLeft + this.trilhoCarrossel.clientWidth >= this.trilhoCarrossel.scrollWidth - 10) {
                this.trilhoCarrossel.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                this.trilhoCarrossel.scrollBy({ left: moverDireita, behavior: 'smooth' });
            }
        });

        // ================================

        // seta <

        this.setaEsquerda?.addEventListener('click', () => {
            const moverEsquerda = this.trilhoCarrossel.clientWidth * -0.50;

            if (this.trilhoCarrossel.scrollLeft <= 0) {
                trilhoCarrossel.scrollTo({ left: trilhoCarrossel.scrollWidth, behavior: 'smooth' });
            } else {
                this.trilhoCarrossel.scrollBy({ left: moverEsquerda, behavior: 'smooth' });
            }
        })
    }
};