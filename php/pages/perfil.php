<?php
    session_start();

    require '../config/env.php';
    require '../scripts/functions.php';
    aplicaRestricao();

    if ((isset($_GET['id_perfil'])) && ($_SESSION['id'] !== $_GET['id_perfil'])) {
    $perfilUsuarioLogado    = false;
    $usuario                = buscarUsuario($_GET['id_perfil']);
    $livros                 = buscarLivrosDoUsuario($_GET['id_perfil']);
    $fotoPerfilOutroUsuario = $usuario['img_icone_perfil'] ? "../../{$usuario['img_icone_perfil']}" : null;
    } else {
    $perfilUsuarioLogado = true;
    $usuario             = buscarUsuario($_SESSION['id']);
    $livros              = buscarLivrosDoUsuario($_SESSION['id']);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include '../partials/head.php'; ?>
    <?php if ($perfilUsuarioLogado): ?>
        <title>Meu Perfil</title>
    <?php else: ?>
        <title>Perfil de <?php echo $usuario['nm_usuario'] ?></title>
    <?php endif; ?>
</head>

<body>
    <?php include '../layouts/header.php'; ?>

    <main>
        <div class="perfil-container">

            <!-- ========== COLUNA ESQUERDA: CARD DE PERFIL ========== -->
            <div class="coluna-esquerda">

                <!-- Foto de perfil -->
                <div class="foto-perfil">
                    <?php if (!$perfilUsuarioLogado): ?>
                        <?php if ($fotoPerfilOutroUsuario): ?>
                            <img src="<?php echo htmlspecialchars($fotoPerfilOutroUsuario) ?>" alt="Foto de perfil">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" fill="#aaa">
                                <circle cx="40" cy="30" r="16" />
                                <path d="M10 70 Q10 50 40 50 Q70 50 70 70Z" />
                            </svg>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($fotoPerfil): ?>
                            <img src="<?php echo htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" fill="#aaa">
                                <circle cx="40" cy="30" r="16" />
                                <path d="M10 70 Q10 50 40 50 Q70 50 70 70Z" />
                            </svg>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Nome e e-mail abaixo da foto -->
                <div class="perfil-nome-bloco">
                    <strong class="perfil-nome"><?php echo htmlspecialchars($usuario['nm_usuario'] ?? '—') ?></strong>
                    <?php if ($perfilUsuarioLogado): ?>
                        <span class="perfil-email-sub"><?php echo htmlspecialchars($usuario['nm_email'] ?? '—') ?></span>
                    <?php endif; ?>
                </div>

                <hr class="perfil-divider">

                <!-- Dados pessoais -->
                <div class="dados-pessoais">
                    <?php if ($perfilUsuarioLogado): ?>
                        <div class="perfil-campo">
                            <h5>E-mail</h5>
                            <h6><?php echo htmlspecialchars($usuario['nm_email'] ?? '—') ?></h6>
                        </div>
                        <div class="perfil-campo">
                            <h5>Telefone</h5>
                            <h6><?php echo htmlspecialchars($usuario['cd_telefone'] ?? '—') ?></h6>
                        </div>
                    <?php endif; ?>
                    <div class="perfil-campo">
                        <h5>Gênero</h5>
                        <h6><?php echo htmlspecialchars($usuario['sg_genero'] ?? '—') ?></h6>
                    </div>
                    <div class="perfil-campo">
                        <h5>Gênero literário favorito</h5>
                        <h6><?php echo htmlspecialchars($usuario['nm_genero_literario_favorito'] ?? '—') ?></h6>
                    </div>
                    <?php if ($perfilUsuarioLogado): ?>
                        <div class="perfil-campo">
                            <h5>Endereço</h5>
                            <h6>
                                <?php echo htmlspecialchars($usuario['nm_logradouro'] ?? '') ?>
                                <?php echo $usuario['cd_numero'] ? ', ' . $usuario['cd_numero'] : '' ?>
                                <?php echo $usuario['ds_complemento'] ? ' — ' . htmlspecialchars($usuario['ds_complemento']) : '' ?><br>
                                <?php echo htmlspecialchars($usuario['nm_bairro'] ?? '') ?>,
                                <?php echo htmlspecialchars($usuario['nm_cidade'] ?? '') ?> —
                                <?php echo htmlspecialchars($usuario['sg_uf'] ?? '') ?>,
                                CEP <?php echo htmlspecialchars($usuario['cd_cep'] ?? '') ?>
                            </h6>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ações do perfil -->
                <?php if ($perfilUsuarioLogado): ?>
                    <div class="perfil-acoes">
                        <a href="perfil_edicao.php" class="btn-editar-perfil">Editar Perfil</a>
                        <a href="../scripts/delete.php" id="deletarConta">Deletar conta</a>
                    </div>
                <?php endif; ?>

            </div>
            <!-- ========== FIM COLUNA ESQUERDA ========== -->


            <!-- ========== BLOCO DE LIVROS (DIREITA) ========== -->
            <section class="bloco-livros">

                <div class="livros-header">
                    <?php if ($perfilUsuarioLogado): ?>
                        <h3>Meus Livros</h3>
                        <a href="livro_cadastro.php" class="btn-cadastrar-livro">Cadastrar um Livro</a>
                    <?php else: ?>
                        <h2>Livros de <?php echo htmlspecialchars($usuario['nm_usuario']) ?></h2>
                    <?php endif; ?>
                </div>

                <div id="meus_livros" class="livros-grid">
                    <?php if (!empty($livros)): ?>
                        <?php foreach ($livros as $livro): ?>
                            <div class="livro-card" data-id="<?php echo $livro['id_livro'] ?>">

                                <div class="livro-capa-container">
                                    <img src="../../<?php echo $livro['img_livro'] ?>" alt="<?php echo htmlspecialchars($livro['nm_livro']) ?>">
                                </div>

                                <div class="livro-detalhes">
                                    <h5 class="livro-titulo" title="<?php echo htmlspecialchars($livro['nm_livro']) ?>">
                                        <?php echo htmlspecialchars($livro['nm_livro']) ?>
                                    </h5>

                                    <?php if ($perfilUsuarioLogado): ?>
                                        <div class="livro-acoes-inline">
                                            <a href="livro_cadastro_edicao.php?id=<?php echo $livro['id_livro'] ?>" class="btn-acao-editar">
                                                Editar
                                            </a>
                                            <a href="../scripts/delete_livro.php?id=<?php echo $livro['id_livro'] ?>" class="btn-acao-deletar">
                                                Deletar
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="msg-vazio">
                            <h5>Você ainda não cadastrou nenhum livro.</h5>
                        </div>
                    <?php endif; ?>
                </div>

            </section>
            <!-- ========== FIM BLOCO DE LIVROS ========== -->

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

    <?php include '../layouts/footer.php'; ?>
</body>

</html>