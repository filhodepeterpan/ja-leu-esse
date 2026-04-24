<?php
    session_start();
    
    require('../scripts/functions.php');
    aplicaRestricao();
    
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse? | Meu Perfil</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>
    <main></main>
    <?php include('../layouts/footer.php'); ?>
</body>
</html>