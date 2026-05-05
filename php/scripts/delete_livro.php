<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
aplicaRestricao();

$idLivro = (int) ($_GET['id'] ?? 0);

if (!$idLivro) {
    header("Location: ../pages/perfil.php");
    exit();
}

// Verifica se o livro pertence ao usuário logado antes de qualquer coisa
$livro = buscarLivro($idLivro);

if (empty($livro) || (int) $livro['id_usuario'] !== $_SESSION['id']) {
    header("Location: ../pages/perfil.php");
    exit();
}

// ─── POST: executa a deleção ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = deletarLivro($idLivro);

    // Remove a imagem do livro do servidor se existir
    if ($ok && $livro['img_livro']) {
        $caminhoFisico = __DIR__ . "/../../{$livro['img_livro']}";
        if (file_exists($caminhoFisico)) unlink($caminhoFisico);
    }

    header("Location: ../pages/perfil.php");
    exit();
}

// ─── GET: exibe página de confirmação ─────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse? | Excluir Livro</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>

    <main>
        <p>Tem certeza que deseja excluir o livro <strong><?= htmlspecialchars($livro['nm_livro']) ?></strong>? Essa ação não pode ser desfeita.</p>

        <form method="POST" action="delete_livro.php?id=<?= $idLivro ?>" id="form-delete">
            <button type="submit">Sim, excluir</button>
        </form>

        <a href="../pages/perfil.php">Cancelar</a>
    </main>

    <script>
        document.getElementById('form-delete').addEventListener('submit', function (e) {
            if (!confirm('Tem certeza que deseja excluir "<?= htmlspecialchars($livro['nm_livro']) ?>"?')) {
                e.preventDefault();
            }
        });
    </script>

    <?php include('../layouts/footer.php'); ?>
</body>
</html>