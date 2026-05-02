<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
aplicaRestricao();

// ─── POST: executa a deleção ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirm = deletarUsuario($_SESSION['id']);

    if ($confirm) {
        session_destroy();
        header("Location: ../../index.php");
    } else {
        header("Location: perfil.php?erro=delete");
    }
    exit();
}

// ─── GET: exibe página de confirmação ─────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse? | Excluir conta</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>

    <main>
        <p>Tem certeza que deseja excluir sua conta? Essa ação não pode ser desfeita.</p>

        <form method="POST" action="#" id="form-delete">
            <button type="submit">Sim, excluir minha conta</button>
        </form>

        <a href="perfil.php">Cancelar</a>
    </main>

    <script>
        // Com JS: intercepta o submit e exibe confirm() nativo antes de enviar
        document.getElementById('form-delete').addEventListener('submit', function (e) {
            if (!confirm('Tem certeza que deseja excluir sua conta? Essa ação não pode ser desfeita.')) {
                e.preventDefault();
            }
        });
    </script>

    <?php include('../layouts/footer.php'); ?>
</body>
</html>