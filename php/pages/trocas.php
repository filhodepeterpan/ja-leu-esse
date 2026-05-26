<?php
session_start();

require '../config/env.php';
require '../scripts/functions.php';
aplicaRestricao();

//Busca todos os livros no banco
$todosOsLivros = buscarTodosLivros();

// Livros do próprio usuário (usados para oferecer na troca)
$meus_livros = array_values(
    array_filter($todosOsLivros, fn($l) => (int) $l['id_usuario'] === (int) $_SESSION['id'])
);

//Livros dos outros (Novidades) - Filtramos para não ver o próprio livro
$livrosParaTroca = array_values(
    array_filter($todosOsLivros, fn($l) => (int) $l['id_usuario'] !== (int) $_SESSION['id'])
);

// Para o carrossel - apenas 15 mais recentes
$livrosCarrossel15 = array_slice(array_reverse($livrosParaTroca), 0, 15);

?>




<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include '../partials/head.php'; ?>
    <title>Já leu esse? | Trocas</title>
</head>

<body>

    <?php include '../layouts/header.php'; ?>

    <main>


        <!------------------------------ CARROSSEL --------------------------->

        <!------------- Titulo do Carrossel ------------->
        <h3 class="c-titulo" style="margin-bottom: 16px;">Novidades para você</h3>


        <!---container carrossel--->
        <div class="c-container">


            <!---------------- Botão ------------------------>

            <!--- < ESQUERDO --->
            <button class="btn-seta-esquerda">
                <img src="/sistema/ja-leu-esse/assets/img/setas/btn_seta_esquerda.svg" alt="Voltar">
            </button>

            <!--- DIREITO > --->
            <button class="btn-seta-direita">
                <img src="/sistema/ja-leu-esse/assets/img/setas/bnt_seta_direita.svg" alt="Avançar">
            </button>

            <!----------------fim do Botão-------------------------->


            <!-------------- Grupo do card ------------------>

            <div class="c-grupo" id="lista-livros">
                <?php foreach ($livrosCarrossel15 as $index => $livro): ?>

                    <div class="c-card">
                        <img src="/sistema/ja-leu-esse/<?php echo $livro['img_livro'] ?>" class="c-capa">

                        <div class="c-info">
                            <h3><?php echo $livro['nm_livro'] ?></h3>

                            <div class="btn-propor-troca" data-index="<?php echo $index ?>"
                                data-id-usuario="<?php echo $livro['id_usuario'] ?>"
                                data-nome="<?php echo htmlspecialchars($livro['nm_livro']) ?>"
                                data-url="/sistema/ja-leu-esse/<?php echo $livro['img_livro'] ?>"
                                data-alt="<?php echo htmlspecialchars($livro['nm_livro']) ?>" style="width: 32px; height: 32px; border-radius: 50%; background: #222222;
                            display: flex; align-items: center; justify-content: center;
                             cursor: pointer; flex-shrink: 0;">

                                <svg viewBox="0 0 24 24" width="16" fill="none" stroke="#afafaf" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
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
        <!----------------- FIM MODAL DE TROCA --------------------->

        <!------------------------------ FIM DO CARROSSEL --------------------------->

        <br>
        <br>
        <br>
        <br>
        <br>


        <!--------------------------------- FEED ------------------------------------->

        <div class="feed-livros" id="feed-livros">

            <?php foreach ($livrosParaTroca as $l) {
                if (empty($l['img_livro'])) {
                    continue;
                }
                ?>
            <div class="feed-card">
                <img src="/sistema/ja-leu-esse/<?php echo $l['img_livro'] ?>" class="feed-capa">

                <div class="feed-info">
                    <h3>
                        <?php echo $l['nm_livro'] ?>
                    </h3>

                    <div class="feed-rodape">
                        <a href="perfil.php?id_perfil=<?php echo $l['id_usuario'] ?>">
                            <?php echo $l['nm_usuario'] ?>
                        </a>

                        <div class="btn-propor-troca" data-index="<?php echo $l['id_livro'] ?>"
                            data-id-usuario="<?php echo $l['id_usuario'] ?>" data-nome="<?php echo $l['nm_livro'] ?>"
                            data-url="/sistema/ja-leu-esse/<?php echo $l['img_livro'] ?>"
                            data-alt="<?php echo $l['nm_livro'] ?>"
                            style="width: 32px; height: 32px; border-radius: 50%; background: #222222; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0;">

                            <svg viewBox="0 0 24 24" width="16" fill="none" stroke="#afafaf" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>


        <!------------------------------ FIM DO FEED -------------------------------->



    </main>


    <script src="/sistema/ja-leu-esse/js/masonry.pkgd.js"></script>
    <script>
        window.addEventListener('load', () => {
            const feed = document.querySelector('#feed-livros');
            const msnry = new Masonry(feed, {
                itemSelector: '.feed-card',
                columnWidth: 224,
                gutter: 16,
                fitWidth: true
            });

            // Recalcula o layout assim que cada imagem carregar
            feed.querySelectorAll('img').forEach(img => {
                img.addEventListener('load', () => msnry.layout());
            });
        });
    </script>
    <?php include '../layouts/footer.php'; ?>


</body>

</html>