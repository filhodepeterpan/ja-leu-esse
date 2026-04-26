<?php
session_start();
    
require('../scripts/functions.php');

if(verificaLogin()){
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postType = $_POST['post-type'];

    if ($postType === 'login') {
        $email = $_POST['nm_email'];
        $senha = $_POST['cd_senha'];

        if ($email === 'admin@example.com' && $senha === '123') {
            $_SESSION['logado'] = true;
            $_SESSION['user'] = $email;
            header("Location:../pages/home.php");
            exit();
        } 
        else {
            echo "<script>alert('Credenciais inválidas!');</script>";
        }
    } 
    else if ($postType === 'cadastro') {
        $nome = $_POST['nm_usuario'];
        $email = $_POST['nm_email'];
        $senha = $_POST['cd_senha'];

        // lógica do banco

        echo "<script>alert('Cadastro realizado com sucesso!');</script>";  
    }
    else{
        echo throw new Exception("Ocorreu um erro inesperado. Tente novamente mais tarde.");
    }
}
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
        <div id="formularios">
            <form action="#" method="POST" id="formLogin">
                <h1>Login</h1>
                <label for="nm_email">Email:</label>
                <input type="email" id="nm_email" name="nm_email" required>
                
                <label for="cd_senha">Senha:</label>
                <input type="password" id="cd_senha" name="cd_senha" required>

                <input type="hidden" name="post-type" value="login">

                <button type="submit" class="form-submit">Login</button>
                <span id="semCadastro" class="verificacao-cadastro">Ainda não possuo cadastro</span>
            </form>
            <form action="#" method="POST" id="formCadastro">
                <h1>Cadastro</h1>
                <label for="nm_usuario">Nome:</label>
                <input type="text" id="nm_usuario" name="nm_usuario" required>

                <label for="nm_email">Email:</label>
                <input type="email" id="nm_email" name="nm_email" required>
                
                <label for="cd_senha">Senha:</label>
                <input type="password" id="cd_senha" name="cd_senha" required>

                <input type="hidden" name="post-type" value="cadastro">

                <button type="submit" class="form-submit">Cadastrar</button>
                <span id="comCadastro" class="verificacao-cadastro">Já possuo cadastro</span>
            </form>
        </div>
    </main>

    <?php include('../layouts/footer.php'); ?>
</body>

</html>