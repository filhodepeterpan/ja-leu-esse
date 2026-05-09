<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
aplicaRestricao();
include('../scripts/stock_photo.php');

// Livros do próprio usuário — usados no seletor de oferta
$meus_livros = array_values(
    array_filter($stock_photos, fn($l) => $l['id_usuario'] === $_SESSION['id'])
);

// Livros dos outros — exibidos na lista para desejar
$stock_photos = array_values(
    array_filter($stock_photos, fn($l) => $l['id_usuario'] !== $_SESSION['id'])
);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse? | Trocas</title>
</head>

<body>
    <?php include('../layouts/header.php'); ?>

    <main id="main-trocas">
        <h1 class="trocas-titulo">Livros disponíveis para troca</h1>

        <div id="lista-livros" class="lista-livros">
            <?php foreach ($stock_photos as $index => $photo): ?>
                <div class="card-livro-container">
                    <a href="perfil.php?id_perfil=<?= $photo['id_usuario'] ?>"
                        style="text-decoration: none; color: inherit;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                            <?php if ($photo['foto_dono']): ?>
                                <img src="<?= $photo['foto_dono'] ?>"
                                    style="width: 25px; height: 25px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div
                                    style="width: 25px; height: 25px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center;">
                                    <svg viewBox="0 0 80 80" width="14" fill="#888">
                                        <circle cx="40" cy="30" r="16" />
                                        <path d="M10 70 Q10 50 40 50 Q70 50 70 70Z" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <span style="font-size: 0.85em;"><?= htmlspecialchars($photo['nm_usuario']) ?></span>
                        </div>
                    </a>

                    <div class="card-livro" data-index="<?= $index ?>" data-id-usuario="<?= $photo['id_usuario'] ?>"
                        data-nome="<?= htmlspecialchars($photo['nome']) ?>"
                        data-url="<?= htmlspecialchars($photo['url']) ?>" data-alt="<?= htmlspecialchars($photo['alt']) ?>">

                        <img src="<?= $photo['url'] ?>" alt="<?= htmlspecialchars($photo['alt']) ?>">
                        <p class="card-livro-nome"><?= htmlspecialchars($photo['nome']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Modal de troca -->
        <div id="modalTroca" class="modal-overlay hidden">
            <div class="modal-box">

                <button class="modal-fechar" id="modalFechar">&times;</button>
                <h2 class="modal-titulo">Propor troca</h2>

                <div class="modal-livros">

                    <!-- Slot esquerdo: livro do usuário -->
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

                    <!-- Slot direito: livro desejado (clicado) -->
                    <div class="modal-slot" id="slotDesejo">
                        <img class="slot-img" id="slotDesejoImg" src="" alt="">
                        <p class="slot-nome" id="slotDesejoNome"></p>
                    </div>

                </div>

                <button class="btn-negociar" id="btnNegociar" disabled>Negociar</button>
            </div>

            <!-- Seletor de livros (abre ao clicar no +) -->
            <div class="seletor-overlay hidden" id="seletorLivros">
                <div class="seletor-box">
                    <div class="seletor-header">
                        <h3>Qual livro você quer oferecer?</h3>
                        <button class="seletor-fechar" id="seletorFechar">&times;</button>
                    </div>
                    <div class="seletor-grid">
                        <?php foreach ($meus_livros as $index => $photo): ?>
                            <div class="seletor-card" data-index="<?= $index ?>" data-nome="<?= $photo['nome'] ?>"
                                data-url="<?= $photo['url'] ?>" data-alt="<?= $photo['alt'] ?>">
                                <img src="<?= $photo['url'] ?>" alt="<?= $photo['alt'] ?>" width="100px">
                                <p><?= $photo['nome'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('../layouts/footer.php'); ?>
</body>

</html>