export class Endereco {
    constructor(cep, estado, cidade, bairro, logradouro, msg) {
        this.cep = document.getElementById(cep);
        this.estado = document.getElementById(estado);
        this.cidade = document.getElementById(cidade);
        this.bairro = document.getElementById(bairro);
        this.logradouro = document.getElementById(logradouro);
        this.msg = document.getElementById(msg);
    }

    init() {  
        if(!this.cep) return;
            
        this.verificaCEP();
    }

    viaCEP() {
        const cepFormatado = this.cep.value.replace('-', '').trim();
        const api = `https://viacep.com.br/ws/${cepFormatado}/json/`;

        fetch(api)
            .then(resposta => resposta.json())
            .then(data => {
                if (!('erro' in data)) {
                    this.estado.value = data.uf;
                    this.cidade.value = data.localidade;
                    this.bairro.value = data.bairro;
                    this.logradouro.value = data.logradouro;

                    this.msg.innerHTML = '';
                }
                else {
                    this.msg.innerHTML = 'CEP inválido ou não encontrado. Tente digitar novamente.';
                    this.limpaEndereco();
                }
            })
            .catch(() => {
                this.msg.innerHTML = 'Erro ao buscar o CEP.';
                this.limpaEndereco();
            });
    }

    verificaCEP() {
        this.cep.addEventListener('blur', () => {
            this.viaCEP();
        });
    }

    limpaEndereco(){
        this.estado.value = '';
        this.cidade.value = '';
        this.bairro.value = '';
        this.logradouro.value = '';
    }

}