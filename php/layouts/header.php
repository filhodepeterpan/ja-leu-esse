<?php
$logado = verificaLogin();

if (isset($_POST['login'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (isset($_POST['logout'])) {
    header("Location: ../scripts/logout.php");
    exit();
}

$fotoPerfil = !empty($_SESSION['foto']) ? "../../{$_SESSION['foto']}" : null;
?>

<header>
    <nav>
        <div class="logo">
            <a href="../../index.php">
                <?php include('../../assets/img/logo.svg'); ?>
            </a>
        </div>

        <div class="nav-links">
            <a href="../pages/home.php">Início</a>

            <?php if ($logado): ?>
            <a href="../pages/trocas.php">Trocas</a>
            <?php endif; ?>
        </div>

        <div class="nav-usuario">
            <?php if ($logado): ?>
            <form action="#" method="POST" id="form-user-logout" class="form-user">

                <?php if ($fotoPerfil): ?>
                <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil" class="header-foto-perfil">
                <?php else: ?>
                <svg class="header-foto-perfil header-foto-placeholder" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 40 40">
                    <circle cx="20" cy="20" r="20" fill="#e0e0e0" />
                    <circle cx="20" cy="15" r="7" fill="#aaa" />
                    <path d="M5 38 Q5 27 20 27 Q35 27 35 38Z" fill="#aaa" />
                </svg>
                <?php endif; ?>

                <a href="../pages/perfil.php">Meu perfil</a>
                <input type="hidden" value="logout" name="logout">
                <button type="submit" id="logout" class="session-button">Logout</button>
            </form>
            <?php else: ?>
            <form action="#" method="POST" id="form-user-login" class="form-user">
                <input type="hidden" value="login" name="login">
                <button type="submit" id="login" class="session-button">Login</button>
            </form>
            <?php endif; ?>
        </div>
    </nav>
</header>

<div id="modalSair" class="modal-overlay">
    <div class="modal-box">
        <h3>Confirmação</h3>
        <p>Você deseja mesmo sair do sistema?</p>
        <div class="modal-botoes">
            <button id="btnConfirmarSair" type="button" class="btn-sim">Sim</button>
            <button id="btnCancelarSair" type="button" class="btn-nao">Não</button>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    display: none; /* Começa escondido */
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal-box {
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    width: 300px;
    max-width: 90%;
    font-family: Arial, sans-serif;
}

.modal-box h3 { margin-top: 0; color: #333; }
.modal-box p { color: #666; margin-bottom: 25px; }
.modal-botoes { display: flex; justify-content: space-between; gap: 15px; }

.btn-sim, .btn-nao {
    flex: 1; padding: 10px; border-radius: 5px; font-weight: bold;
    cursor: pointer; border: none; font-size: 16px;
}

.btn-sim { background-color: #e74c3c; color: white; }
.btn-sim:hover { background-color: #c0392b; }

.btn-nao { background-color: #bdc3c7; color: #333; }
.btn-nao:hover { background-color: #95a5a6; }
</style>

<script>
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
</script>