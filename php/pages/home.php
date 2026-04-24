<?php
    session_start();
    
    require('../scripts/functions.php');
    include('../scripts/stock_photo.php');

    $logado = verificaLogin();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse?</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>

    <main id="main-home" class="container-fluid px-4 py-4">
        <div class="row g-5">

            <div class="col-12 col-md-3 d-flex align-items-center">
                <div id="meuCarrossel" class="carousel slide w-100" data-bs-ride="carousel">

                    <!-- Indicadores -->
                    <div class="carousel-indicators">
                        <?php foreach($stock_photos as $index => $photo): ?>
                            <button type="button" data-bs-target="#meuCarrossel" data-bs-slide-to="<?=$index?>" class="<?=$photo['status']?>"></button>
                        <?php endforeach; ?>
                    </div>

                    <div class="carousel-inner rounded">
                        <?php foreach($stock_photos as $photo): ?>
                            <div class="carousel-item <?=$photo['status']?>">
                                <img src="<?=$photo['url']?>" class="d-block w-100" alt="<?=$photo['alt']?>" height="600">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#meuCarrossel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>

                    <button class="carousel-control-next" type="button" data-bs-target="#meuCarrossel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>

                </div>
            </div>

            <div class="sessao-de-noticias col-12 col-md-9">
                <h1>teste</h1>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt repellendus, et laborum adipisci maiores corrupti tempore neque ratione totam sit fugiat. Nemo sint non nisi illo, corrupti perferendis assumenda veritatis!
                    Dolor sint error labore. Rerum dolores saepe hic velit porro asperiores necessitatibus consequuntur dolorum, quod accusantium cupiditate dolor vero temporibus quos magni dignissimos? Commodi, adipisci? Maiores dolores dolorem earum sapiente!
                    Cum facilis laudantium consequuntur odit quod quam exercitationem perspiciatis, aliquid ab modi voluptate asperiores veniam aliquam porro quaerat iste sapiente assumenda? Itaque aliquam blanditiis enim optio dolorum perferendis in unde.
                </p>
            </div>

        </div>
    </main>

    <?php include('../layouts/footer.php'); ?>
</body>
</html>