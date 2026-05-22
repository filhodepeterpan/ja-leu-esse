<?php
session_start();

require '../config/env.php';
require '../scripts/functions.php';
$logado = verificaLogin();

// Busca todos os livros no banco
$todosOsLivros = buscarTodosLivros();

if ($logado) {
    // Livros do próprio usuário (usados para oferecer na troca)
    $meus_livros = array_values(
        array_filter($todosOsLivros, fn($l) => (int) $l['id_usuario'] === (int) $_SESSION['id'] ?? null)
    );

    // Livros dos outros (Filtramos para não ver o próprio livro)
    $livrosParaTroca = array_values(
        array_filter($todosOsLivros, fn($l) => (int) $l['id_usuario'] !== (int) $_SESSION['id'] ?? null)
    );

    // Para o carrossel - apenas 15 mais recentes
    $livrosCarrossel15 = array_slice(array_reverse($livrosParaTroca), 0, 15);
} else {
    // Para o carrossel - apenas 15 mais recentes
    $livrosCarrossel15 = array_slice(array_reverse($todosOsLivros), 0, 15);
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include '../partials/head.php'; ?>
    <title>Já leu esse? | Home</title>
</head>

<body>

    <?php include '../layouts/header.php'; ?>

    <main>

        <!-- Início da frase -->
        <h3 class="c-titulo" style="text-align: center; margin-top: 32px; margin-bottom: 16px;">Que tal começar uma nova
            viagem...</h3>
        <h3 class="c-titulo" style="margin-top: 8px; margin-bottom: 16px;">Hoje!</h3>
        <!-- Fim da frase e Botão de Login -->
        <?php if (!$logado): ?>
            <div class="login-action-container">
                <a href="login.php" class="btn-login-grande">Login</a>
            </div>
        <?php endif; ?>


        <div class="c-container">
            <button class="btn-seta-esquerda">
                <img src="/sistema/ja-leu-esse/assets/img/setas/btn_seta_esquerda.svg" alt="Voltar">
            </button>

            <button class="btn-seta-direita">
                <img src="/sistema/ja-leu-esse/assets/img/setas/bnt_seta_direita.svg" alt="Avançar">
            </button>

            <div class="c-grupo" id="lista-livros">
                <?php foreach ($livrosCarrossel15 as $index => $livro): ?>
                    <div class="c-card">
                        <img src="/sistema/ja-leu-esse/<?= $livro['img_livro'] ?>" class="c-capa">
                        <div class="c-info">
                            <h3><?= $livro['nm_livro'] ?></h3>

                            <?php if ($logado): ?>
                                <div class="btn-propor-troca" data-index="<?= $index ?>"
                                    data-id-usuario="<?= $livro['id_usuario'] ?>"
                                    data-nome="<?= htmlspecialchars($livro['nm_livro']) ?>"
                                    data-url="/sistema/ja-leu-esse/<?= $livro['img_livro'] ?>"
                                    data-alt="<?= htmlspecialchars($livro['nm_livro']) ?>" style="width: 32px; height: 32px; border-radius: 50%; background: #222222;
                                display: flex; align-items: center; justify-content: center;
                                cursor: pointer; flex-shrink: 0;">
                                    <svg viewBox="0 0 24 24" width="16" fill="none" stroke="#afafaf" stroke-width="2.5">
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


        <?php if ($logado): ?>

            <!-------------- MODAL DE TROCAS DO CARROSSEL E FEED ------------------>

            <div id="modalTroca" class="modal-overlay hidden">
                <div class="modal-box">

                    <button class="modal-fechar" id="modalFechar">&times;</button>
                    <h2 class="modal-titulo">Propor troca</h2>

                    <div class="modal-livros">

                        <!-- Slot esquerdo: seu livro -->
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

                        <!-- Slot direito: livro desejado -->
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
                                <div class="seletor-card" data-index="<?= $index ?>"
                                    data-nome="<?= htmlspecialchars($livro['nm_livro']) ?>"
                                    data-url="/sistema/ja-leu-esse/<?= $livro['img_livro'] ?>"
                                    data-alt="<?= htmlspecialchars($livro['nm_livro']) ?>">
                                    <img src="/sistema/ja-leu-esse/<?= $livro['img_livro'] ?>" width="100px">
                                    <p><?= htmlspecialchars($livro['nm_livro']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-------------- FIM DO MODAL DE TROCAS ------------------>


            <br><br><br><br><br>


            <!--------------------------------- FEED ------------------------------------->

        <div class="feed-livros" id="feed-livros">

            <?php foreach ($livrosParaTroca as $l) {
                if (empty($l['img_livro'])) {
                    continue;
                } ?>
            <div class="feed-card">
                <img src="/sistema/ja-leu-esse/<?= $l['img_livro'] ?>" class="feed-capa">

                <div class="feed-info">
                    <h3>
                        <?= $l['nm_livro'] ?>
                    </h3>

                    <div class="feed-rodape">
                        <a href="perfil.php?id_perfil=<?= $l['id_usuario'] ?>">
                            <?= $l['nm_usuario'] ?>
                        </a>

                        <div class="btn-propor-troca" data-index="<?= $l['id_livro'] ?>"
                            data-id-usuario="<?= $l['id_usuario'] ?>" data-nome="<?= $l['nm_livro'] ?>"
                            data-url="/sistema/ja-leu-esse/<?= $l['img_livro'] ?>" data-alt="<?= $l['nm_livro'] ?>"
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

        <!---------------------------- FIM DO FEED ---------------------------------->

        <?php endif; ?>


        <!-- Seção Quem Somos -->
        <section class="quem-somos-container">
            <div class="quem-somos-conteudo">
                <h2>Muito além das páginas</h2>
                <p>
                    O <strong>Já leu esse?</strong> nasceu do desejo de conectar pessoas através das histórias que
                    guardamos na estante. Acreditamos que um livro parado é um universo adormecido.
                </p>
                <p>
                    Mais do que uma plataforma de trocas, este é um espaço de encontros afetuosos, calor humano e
                    partilha de conhecimento. Aqui, cada troca é um laço criado, um novo hobby compartilhado e o início
                    de uma nova jornada. Traga seus livros, descubra novos horizontes e faça parte dessa nossa ciranda
                    de leitores.
                </p>
            </div>
        </section>

    </main>

    <?php if ($logado): ?>
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
    <?php endif; ?>

    <?php include '../layouts/footer.php'; ?>
</body>

</html>