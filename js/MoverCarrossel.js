
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
            const moverDireita = trilhoCarrossel.clientWidth * 0.50;

            if (trilhoCarrossel.scrollLeft + trilhoCarrossel.clientWidth >= trilhoCarrossel.scrollWidth - 10) {
                trilhoCarrossel.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                trilhoCarrossel.scrollBy({ left: moverDireita, behavior: 'smooth' });
            }
        });

        // ================================

        // seta <

        this.setaEsquerda?.addEventListener('click', () => {
            const moverEsquerda = trilhoCarrossel.clientWidth * -0.50;

            if (trilhoCarrossel.scrollLeft <= 0) {
                trilhoCarrossel.scrollTo({ left: trilhoCarrossel.scrollWidth, behavior: 'smooth' });
            } else {
                trilhoCarrossel.scrollBy({ left: moverEsquerda, behavior: 'smooth' });
            }
        })
    }
};