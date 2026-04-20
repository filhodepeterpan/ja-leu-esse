<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Login</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>

    <main>
        <h1>Login</h1>
        <form>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <button type="submit">Login</button>
        </form>
    </main>

    <?php include('../layouts/footer.php'); ?>
</body>
</html>