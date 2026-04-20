<?php
    session_start();
    
    require('../scripts/stock_photo.php');

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Home</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>

    <main class="d-flex justify-content-center align-items-center">
        <div id="meuCarrossel" class="carousel slide w-25" data-bs-ride="carousel">

            <!-- Indicadores (bolinhas) -->
            <div class="carousel-indicators">
                <?php foreach($stock_photos as $index => $photo): ?>
                    <button type="button" data-bs-target="#meuCarrossel" data-bs-slide-to="<?=$index?>" class="<?=$photo['status']?>"></button>
                <?php endforeach; ?>
            </div>

            <div class="carousel-inner rounded">
                <?php foreach($stock_photos as $photo): ?>
                    <div class="carousel-item <?=$photo['status']?>">
                        <h1><?=$photo['nome']?></h1>
                        <img src="<?=$photo['url']?>" class="d-block w-100" alt="<?=$photo['alt']?>">
                        <button class="btn btn-primary w-100 mt-4">Quero trocar!</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#meuCarrossel" data-bs-slide="prev">
        </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#meuCarrossel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#meuCarrossel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            </button>

        </div>
</main>

    <?php include('../layouts/footer.php'); ?>
</body>
</html>