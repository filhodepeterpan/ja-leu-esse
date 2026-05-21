
export class MoverCarrossel {
    init() {
        
let trilhoCarrossel = document.querySelector('.c-grupo');
let fileiraLivros = document.querySelectorAll('.c-grupo .c-card');
let setaDireita = document.querySelector('.btn-seta-direita');
let setaEsquerda = document.querySelector('.btn-seta-esquerda');

// ================================

// seta >

setaDireita.addEventListener('click', () => {
    let moverDireita = trilhoCarrossel.clientWidth * 0.50;

    if (trilhoCarrossel.scrollLeft + trilhoCarrossel.clientWidth >= trilhoCarrossel.scrollWidth - 10) {
        trilhoCarrossel.scrollTo({ left: 0, behavior: 'smooth' }); 
    } else {
        trilhoCarrossel.scrollBy({ left: moverDireita, behavior: 'smooth' }); 
    }
});

// ================================

// seta <

setaEsquerda.addEventListener('click', () => {
    let moverEsquerda = trilhoCarrossel.clientWidth * -0.50; 

    if (trilhoCarrossel.scrollLeft <= 0) {
        trilhoCarrossel.scrollTo({ left: trilhoCarrossel.scrollWidth, behavior: 'smooth' });
    } else {
        trilhoCarrossel.scrollBy({ left: moverEsquerda, behavior: 'smooth' }); 
    }
})
}
};