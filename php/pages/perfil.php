<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
aplicaRestricao();

if ((isset($_GET['id_perfil'])) && ($_SESSION['id'] !== $_GET['id_perfil'])) {
    $perfilUsuarioLogado = false;
    $usuario = buscarUsuario($_GET['id_perfil']);
    $livros = buscarLivrosDoUsuario($_GET['id_perfil']);
    $fotoPerfilOutroUsuario = $usuario['img_icone_perfil'] ? "../../{$usuario['img_icone_perfil']}" : null;
}
else {
    $perfilUsuarioLogado = true;
    $usuario = buscarUsuario($_SESSION['id']);
    $livros = buscarLivrosDoUsuario($_SESSION['id']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include('../partials/head.php'); ?>
    <?php if ($perfilUsuarioLogado): ?>
        <title>Meu Perfil</title>
    <?php else: ?>
        <title>Perfil de <?= $usuario['nm_usuario'] ?></title>
    <?php endif; ?>
</head>

<body>
    <?php include('../layouts/header.php'); ?>

    <main>
        <div class="perfil-container">
            <?php if ($perfilUsuarioLogado): ?>
                <h2>Meu Perfil</h2>
            <?php else: ?>
                <h2>Perfil de <?= $usuario['nm_usuario'] ?></h2>
            <?php endif; ?>

            <!-- Foto -->
            <div class="foto-perfil">
                <?php if (!$perfilUsuarioLogado): ?>
                    <?php if ($fotoPerfilOutroUsuario): ?>
                        <img src="<?= htmlspecialchars($fotoPerfilOutroUsuario) ?>" alt="Foto de perfil">
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" fill="#aaa">
                            <circle cx="40" cy="30" r="16" />
                            <path d="M10 70 Q10 50 40 50 Q70 50 70 70Z" />
                        </svg>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($fotoPerfil): ?>         
                        <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil">
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" fill="#aaa">
                            <circle cx="40" cy="30" r="16" />
                            <path d="M10 70 Q10 50 40 50 Q70 50 70 70Z" />
                        </svg>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Livros cadastrados -->
            <div class="perfil-campo">
                <?php if ($perfilUsuarioLogado): ?>
                    <label>Meus livros</label>
                <?php else: ?>
                    <label>Livros de <?= $usuario['nm_usuario']?></label>
                <?php endif; ?>
                <div id="meus_livros">
                    <?php if (!empty($livros)): ?>
                        <?php foreach ($livros as $livro): ?>
                            <div class="livro-item" data-id="<?= $livro['id_livro'] ?>">
                                <div>
                                    <img src="../../<?= $livro['img_livro'] ?>"
                                        alt="<?= htmlspecialchars($livro['nm_livro']) ?>" width="100">
                                </div>
                                <?php if ($perfilUsuarioLogado): ?>
                                    <div class="livro-acoes">
                                        <a href="livro_cadastro_edicao.php?id=<?= $livro['id_livro'] ?>">Editar</a>
                                        <a href="../scripts/delete_livro.php?id=<?= $livro['id_livro'] ?>">Deletar</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php if ($perfilUsuarioLogado): ?>
                            <span id="msgSemLivros"><i>Você ainda não cadastrou nenhum livro.</i></span>
                        <?php else: ?>
                            <span id="msgSemLivros"><i><?= $usuario['nm_usuario'] ?> ainda não cadastrou nenhum livro.</i></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php if ($perfilUsuarioLogado): ?>
                    <div>
                        <a href="livro_cadastro.php" id="cadastrarLivro">Cadastrar um Livro</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dados pessoais -->
            <div class="perfil-campo">
                <label>Nome</label>
                <span><?= htmlspecialchars($usuario['nm_usuario'] ?? '—') ?></span>
            </div>
            <?php if ($perfilUsuarioLogado): ?>
                <div class="perfil-campo">
                    <label>E-mail</label>
                    <span><?= htmlspecialchars($usuario['nm_email'] ?? '—') ?></span>
                </div>
                <div class="perfil-campo">
                    <label>Telefone</label>
                    <span><?= htmlspecialchars($usuario['cd_telefone'] ?? '—') ?></span>
                </div>
            <?php endif; ?>
            <div class="perfil-campo">
                <label>Gênero</label>
                <span><?= htmlspecialchars($usuario['sg_genero'] ?? '—') ?></span>
            </div>
            <div class="perfil-campo">
                <label>Gênero literário favorito</label>
                <span><?= htmlspecialchars($usuario['nm_genero_literario_favorito'] ?? '—') ?></span>
            </div>
            <?php if ($perfilUsuarioLogado): ?>
                <div class="perfil-campo">
                    <label>Endereço</label>
                    <span>
                        <?= htmlspecialchars($usuario['nm_logradouro'] ?? '') ?>
                        <?= $usuario['cd_numero'] ? ', ' . $usuario['cd_numero'] : '' ?>
                        <?= $usuario['ds_complemento'] ? ' — ' . htmlspecialchars($usuario['ds_complemento']) : '' ?><br>
                        <?= htmlspecialchars($usuario['nm_bairro'] ?? '') ?>,
                        <?= htmlspecialchars($usuario['nm_cidade'] ?? '') ?> —
                        <?= htmlspecialchars($usuario['sg_uf'] ?? '') ?>,
                        CEP <?= htmlspecialchars($usuario['cd_cep'] ?? '') ?>
                    </span>
                </div>

                <div class="perfil-acoes">
                    <a href="perfil_edicao.php">Editar perfil</a>
                    <a href="../scripts/delete.php" id="deletarConta">Deletar conta</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Ao clicar no livro, alterna a visibilidade do menu editar/deletar
        document.querySelectorAll('.livro-item').forEach(item => {
            item.addEventListener('click', function (e) {
                // Não fecha se o clique foi direto nos links
                if (e.target.tagName === 'A') return;

                const aberto = this.classList.contains('ativo');
                // Fecha todos antes de abrir o clicado
                document.querySelectorAll('.livro-item').forEach(i => i.classList.remove('ativo'));
                if (!aberto) this.classList.add('ativo');
            });
        });

        // Fecha ao clicar fora
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.livro-item')) {
                document.querySelectorAll('.livro-item').forEach(i => i.classList.remove('ativo'));
            }
        });
    </script>

    <?php include('../layouts/footer.php'); ?>
</body>

</html>