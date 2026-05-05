<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
aplicaRestricao();

$idLivro = (int) ($_GET['id'] ?? 0);

if (!$idLivro) {
    header("Location: perfil.php");
    exit();
}

$livro = buscarLivro($idLivro);

// Garante que o livro existe e pertence ao usuário logado
if (empty($livro) || (int) $livro['id_usuario'] !== $_SESSION['id']) {
    header("Location: perfil.php");
    exit();
}

$mensagem = ['texto' => '', 'tipo' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nm_livro'            => $_POST['nm_livro'],
        'nm_genero_literario' => $_POST['nm_genero_literario'] ?: null,
        'ds_livro'            => $_POST['ds_livro']            ?: null,
    ];

    if (!empty($_FILES['img_livro']['name'])) {
        $caminho = salvarImagemLivro($_SESSION['id'], $idLivro, $_FILES['img_livro']);

        if ($caminho) {
            $dados['img_livro'] = $caminho;
        } else {
            $mensagem = ['texto' => 'Formato de imagem inválido. Use jpg, png ou webp.', 'tipo' => 'erro'];
        }
    }

    if ($mensagem['tipo'] !== 'erro') {
        $ok = atualizarLivro($idLivro, $dados);

        if ($ok) {
            header("Location: perfil.php");
            exit();
        }
        $mensagem = ['texto' => 'Erro ao salvar. Tente novamente.', 'tipo' => 'erro'];
    }
}

$capaAtual = $livro['img_livro'] ? "../../{$livro['img_livro']}" : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse? | Editar Livro</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>

    <?php if ($mensagem['texto']): ?>
        <script>alert('<?= htmlspecialchars($mensagem['texto']) ?>');</script>
    <?php endif; ?>

    <main>
        <div class="perfil-container">
            <h2>Editar Livro</h2>

            <form action="#" method="POST" enctype="multipart/form-data">

                <!-- Capa do livro -->
                <label for="input-capa" class="foto-wrapper" title="Alterar capa do livro">
                    <div class="foto-perfil">
                        <?php if ($capaAtual): ?>
                            <img src="<?= htmlspecialchars($capaAtual) ?>" alt="Capa do livro" id="preview-foto">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="60" fill="#aaa" id="svg-placeholder">
                                <rect x="15" y="10" width="50" height="60" rx="4"/>
                                <line x1="25" y1="25" x2="55" y2="25" stroke="#fff" stroke-width="3"/>
                                <line x1="25" y1="35" x2="55" y2="35" stroke="#fff" stroke-width="3"/>
                                <line x1="25" y1="45" x2="45" y2="45" stroke="#fff" stroke-width="3"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="btn-trocar-foto">+</div>
                </label>
                <input type="file" id="input-capa" name="img_livro" accept="image/jpeg,image/png,image/webp">

                <div class="form-item">
                    <label for="nm_livro">Título *</label>
                    <input type="text" id="nm_livro" name="nm_livro" maxlength="200" required
                           value="<?= htmlspecialchars($livro['nm_livro'] ?? '') ?>">
                </div>
                <div class="form-item">
                    <label for="nm_genero_literario">Gênero literário</label>
                    <input type="text" id="nm_genero_literario" name="nm_genero_literario" maxlength="60"
                           value="<?= htmlspecialchars($livro['nm_genero_literario'] ?? '') ?>">
                </div>
                <div class="form-item">
                    <label for="ds_livro">Descrição</label>
                    <textarea id="ds_livro" name="ds_livro" maxlength="2000" rows="4"><?= htmlspecialchars($livro['ds_livro'] ?? '') ?></textarea>
                </div>

                <div class="form-acoes">
                    <button type="submit">Salvar</button>
                    <a href="perfil.php">Cancelar</a>
                </div>

            </form>
        </div>
    </main>

    <script type="module">
        import { TrocaFotos } from '../../js/TrocaFotos.js';
        new TrocaFotos('input-capa').init();
    </script>

    <?php include('../layouts/footer.php'); ?>
</body>
</html>