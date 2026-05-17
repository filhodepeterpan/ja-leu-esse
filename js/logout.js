document.addEventListener('DOMContentLoaded', function() {
    // Pega o formulário de logout inteiro
    const formLogout = document.getElementById('form-user-logout');
    
    // Se o formulário existir na tela (ou seja, se o usuário estiver logado)
    if (formLogout) {
        const modalSair = document.getElementById('modalSair');
        const btnConfirmarSair = document.getElementById('btnConfirmarSair');
        const btnCancelarSair = document.getElementById('btnCancelarSair');

        // 1. Quando o formulário tentar ser enviado (ao clicar em Logout)
        formLogout.addEventListener('submit', function(event) {
            event.preventDefault(); // Pausa o envio do formulário
            modalSair.style.display = 'flex'; // Exibe o modal na tela
        });

        // 2. Se clicar em "Sim" no modal
        btnConfirmarSair.addEventListener('click', function() {
            // Força o envio do formulário original
            // Isso vai ativar aquele seu `if (isset($_POST['logout']))` no topo do PHP
            formLogout.submit(); 
        });

        // 3. Se clicar em "Não" no modal
        btnCancelarSair.addEventListener('click', function() {
            modalSair.style.display = 'none'; // Apenas esconde o modal, mantendo na página
        });

        // 4. Se clicar no fundo escuro fora da caixinha branca
        window.addEventListener('click', function(event) {
            if (event.target === modalSair) {
                modalSair.style.display = 'none';
            }
        });
    }
});