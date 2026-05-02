export class TrocaFotos {
    constructor(inputFoto) {
        this.inputFoto = document.getElementById(inputFoto);
    }

    init() {
        if (!this.inputFoto) return;

        this.inputFoto.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                // Remove o SVG placeholder se ainda existir
                const svg = document.getElementById('svg-placeholder');
                if (svg) svg.remove();

                // Atualiza ou cria a tag <img> de preview
                let img = document.getElementById('preview-foto');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'preview-foto';
                    document.querySelector('.foto-perfil').appendChild(img);
                }
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
}