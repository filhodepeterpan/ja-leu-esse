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

    $meus_livros = $livros;
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

            <!-- COLUNA ESQUERDA -->
            <div class="coluna-esquerda">

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

                <div class="perfil-nome-bloco">
                    <strong class="perfil-nome"><?php echo htmlspecialchars($usuario['nm_usuario'] ?? '—') ?></strong>
                    <?php if ($perfilUsuarioLogado): ?>
                        <span class="perfil-email-sub"><?php echo htmlspecialchars($usuario['nm_email'] ?? '—') ?></span>
                    <?php endif; ?>
                </div>

                <hr class="perfil-divider">

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

                <?php if ($perfilUsuarioLogado): ?>
                    <div class="perfil-acoes">
                        <a href="perfil_edicao.php" class="btn-editar-perfil">Editar Perfil</a>
                        <a href="../scripts/delete.php" id="deletarConta">Deletar conta</a>
                    </div>
                <?php endif; ?>

            </div>
            <!-- FIM COLUNA ESQUERDA -->


            <!-- BLOCO DE LIVROS -->
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
                                    <div style="flex-grow: 1; overflow: hidden; padding-right: 8px;">
                                        <h5 class="livro-titulo" title="<?php echo htmlspecialchars($livro['nm_livro']) ?>">
                                            <?php echo htmlspecialchars($livro['nm_livro']) ?>
                                        </h5>
                                        <?php if ($perfilUsuarioLogado): ?>
                                            <div class="livro-acoes-inline">
                                                <a href="livro_cadastro_edicao.php?id=<?php echo $livro['id_livro'] ?>" class="btn-acao-editar">Editar</a>
                                                <a href="../scripts/delete_livro.php?id=<?php echo $livro['id_livro'] ?>" class="btn-acao-deletar">Deletar</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!$perfilUsuarioLogado): ?>
                                        <button
                                            type="button"
                                            class="btn-propor-troca"
                                            data-index="<?php echo $livro['id_livro'] ?>"
                                            data-id-usuario="<?php echo $livro['id_usuario'] ?>"
                                            data-nome="<?php echo htmlspecialchars($livro['nm_livro']) ?>"
                                            data-url="/sistema/ja-leu-esse/<?php echo $livro['img_livro'] ?>"
                                            data-alt="<?php echo htmlspecialchars($livro['nm_livro']) ?>">
                                            +
                                        </button>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="msg-vazio">
                            <h5>Nenhum livro encontrado.</h5>
                        </div>
                    <?php endif; ?>
                </div>

            </section>
            <!-- FIM BLOCO DE LIVROS -->

        </div>


        <!-- MODAL DE TROCA -->
        <div id="modalTroca" class="modal-overlay hidden">
            <div class="modal-box">

                <button class="modal-fechar" id="modalFechar">&times;</button>
                <h2 class="modal-titulo">Propor Troca</h2>

                <div class="modal-livros">

                    <div class="modal-slot" id="slotOferta">
                        <div class="slot-placeholder" id="slotPlaceholder">
                            <span class="slot-hint">Seu livro</span>
                            <button class="btn-adicionar" id="btnAdicionar" title="Selecionar livro">+</button>
                        </div>
                        <img class="slot-img hidden" id="slotOfertaImg" src="" alt="">
                        <p class="slot-nome hidden" id="slotOfertaNome"></p>
                        <button class="btn-trocar hidden" id="btnTrocar">Escolher outro livro</button>
                    </div>

                    <span class="modal-seta">⇄</span>

                    <div class="modal-slot" id="slotDesejo">
                        <img class="slot-img" id="slotDesejoImg" src="" alt="">
                        <p class="slot-nome" id="slotDesejoNome"></p>
                    </div>

                </div>

                <button class="btn-negociar" id="btnNegociar" disabled>Negociar</button>
            </div>

            <!-- Seletor de livros -->
            <div class="seletor-overlay hidden" id="seletorLivros">
                <div class="seletor-box">
                    <div class="seletor-header">
                        <h3>Qual livro você quer oferecer?</h3>
                        <button class="seletor-fechar" id="seletorFechar">&times;</button>
                    </div>
                    <div class="seletor-grid">
                        <?php foreach ($meus_livros as $index => $livro): ?>
                            <div class="seletor-card"
                                data-index="<?php echo $index ?>"
                                data-nome="<?php echo htmlspecialchars($livro['nm_livro']) ?>"
                                data-url="/sistema/ja-leu-esse/<?php echo $livro['img_livro'] ?>"
                                data-alt="<?php echo htmlspecialchars($livro['nm_livro']) ?>">
                                <img src="/sistema/ja-leu-esse/<?php echo $livro['img_livro'] ?>" width="100px">
                                <p><?php echo htmlspecialchars($livro['nm_livro']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM MODAL DE TROCA -->


    </main>

    <script>
        document.querySelectorAll('.livro-item').forEach(item => {
            item.addEventListener('click', function (e) {
                if (e.target.tagName === 'A') return;
                const aberto = this.classList.contains('ativo');
                document.querySelectorAll('.livro-item').forEach(i => i.classList.remove('ativo'));
                if (!aberto) this.classList.add('ativo');
            });
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.livro-item')) {
                document.querySelectorAll('.livro-item').forEach(i => i.classList.remove('ativo'));
            }
        });
    </script>

    <script type="module">
        import { Trocas } from '/sistema/ja-leu-esse/js/trocas.js';
        const trocas = new Trocas('meus_livros', 'modalTroca');
        trocas.init();
    </script>

    <?php include '../layouts/footer.php'; ?>
</body>

</html>