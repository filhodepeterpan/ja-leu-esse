<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
aplicaRestricao();


//Busca todos os livros no banco
$todosOsLivros = buscarTodosLivros(); 

// Livros do próprio usuário (usados para oferecer na troca)
$meus_livros = array_values(
    array_filter($todosOsLivros, fn($l) => (int)$l['id_usuario'] === (int)$_SESSION['id'])
);

//Livros dos outros (Novidades) - Filtramos para não ver o próprio livro
$livrosParaTroca = array_values(
    array_filter($todosOsLivros, fn($l) => (int)$l['id_usuario'] !== (int)$_SESSION['id'])
);

// Pegamos apenas os 15 mais recentes para exibir na tela
$stock_photos = array_slice(array_reverse($livrosParaTroca), 0, 15);
?>




<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse? | Trocas</title>
</head>

<body>
    <?php include('../layouts/header.php'); ?>

 <main>
    <h3 class="c-titulo" style="margin-bottom: 16px;">Novidades para você</h3>
    
    <div class="c-container" style="position: relative;">
        
                <button class="btn-seta-esquerda">
                    <img src="/sistema/ja-leu-esse/assets/img/setas/btn_seta_esquerda.svg" alt="Voltar">
                </button>

                <button class="btn-seta-direita">
                    <img src="/sistema/ja-leu-esse/assets/img/setas/bnt_seta_direita.svg" alt="Avançar">
                </button>
            <div class="c-grupo">

           <?php foreach ($stock_photos as $livro): ?>
                <div class="c-card" style="background: #271919;">
                    <img src="/sistema/ja-leu-esse/assets/img/examples/<?php echo $livro['img_livro']; ?>" class="c-capa">
                    
                    <div class="c-info">
                    <h3><?php echo $livro['nm_livro']; ?></h3>
                    </div> 
                </div> <?php endforeach; ?>


        </div> 
    </div> 
</main>




    <?php include('../layouts/footer.php'); ?>
</body>

</html>