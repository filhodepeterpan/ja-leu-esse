<?php
    session_start();

    require '../config/env.php';
    require '../scripts/functions.php';
    $logado = verificaLogin();

    // Busca todos os livros no banco
    $todosOsLivros = buscarTodosLivros();

    // Livros do próprio usuário (usados para oferecer na troca)
    $meus_livros = array_values(
        array_filter($todosOsLivros, fn($l) => (int) $l['id_usuario'] === (int) $_SESSION['id'])
    );

    // Livros dos outros (Filtramos para não ver o próprio livro)
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
    <title>Já leu esse? | Home</title>
</head>

<body>

    <?php include '../layouts/header.php'; ?>

<main>

    <!-- Início da frase -->
    <h3 class="c-titulo" style="text-align: center; margin-top: 32px; margin-bottom: 16px;">Que tal começar uma nova viagem...</h3>
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
                    <img src="/sistema/ja-leu-esse/assets/img/examples/<?= $livro['img_livro'] ?>" class="c-capa">
                    <div class="c-info">
                        <h3><?= $livro['nm_livro'] ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Seção Quem Somos -->
    <section class="quem-somos-container">
        <div class="quem-somos-conteudo">
            <h2>Muito além das páginas</h2>
            <p>
                O <strong>Já leu esse?</strong> nasceu do desejo de conectar pessoas através das histórias que guardamos na estante. Acreditamos que um livro parado é um universo adormecido. 
            </p>
            <p>
                Mais do que uma plataforma de trocas, este é um espaço de encontros afetuosos, calor humano e partilha de conhecimento. Aqui, cada troca é um laço criado, um novo hobby compartilhado e o início de uma nova jornada. Traga seus livros, descubra novos horizontes e faça parte dessa nossa ciranda de leitores.
            </p>
        </div>
    </section>

</main>

<?php include '../layouts/footer.php'; ?>
</body>
</html>