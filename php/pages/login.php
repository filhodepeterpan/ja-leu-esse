<?php
session_start();

require('../scripts/functions.php');
require('../scripts/atributos.php');

if (verificaLogin()) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postType = $_POST['post-type'];

    if ($postType === 'login') {
        $email = $_POST['nm_email'];
        $senha = $_POST['cd_senha'];
        $nome = $_POST['nm_usuario'] ?? 'Usuário'; // futuramente, ele vai ser resgatodo do banco, mas por enquanto, é só um placeholder

        if ($email === 'admin@example.com' && $senha === '123') {
            $_SESSION['logado'] = true;
            $_SESSION['user'] = $email;
            $_SESSION['nome'] = $nome; 
            header("Location:../pages/home.php");
            exit();
        } else {
            echo "<script>alert('Credenciais inválidas!');</script>";
        }
    } else if ($postType === 'cadastro') {
        $nome = $_POST['nm_usuario'];
        $email = $_POST['nm_email'];
        $senha = $_POST['cd_senha'];

        // lógica do banco

        echo "<script>alert('Cadastro realizado com sucesso!');</script>";
    } else if (!isset($postType)) {
        header("Location: login.php");
        exit();
    } else {
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
                <h1 class="form-name">Login</h1>
                <div class="form-item">
                    <label for="nm_email">Email:</label>
                    <input type="email" id="nm_email" name="nm_email" required>
                </div>
                <div class="form-item">
                    <label for="cd_senha">Senha:</label>
                    <input type="password" id="cd_senha" name="cd_senha" required>
                </div>
                <input type="hidden" name="post-type" value="login">
                <div class="form-item">
                    <button type="submit" class="form-submit">Login</button>
                    <span id="semCadastro" class="verificacao-cadastro">Ainda não possuo cadastro</span>
                </div>
            </form>
            <form action="#" method="POST" id="formCadastro">
                <h1 class="form-name">Cadastro</h1>
                <?php foreach ($atributos as $atributo): ?>
                    <div class="form-item">
                        <label for="<?= $atributo['id'] ?>"><?= $atributo['nome'] ?></label>
                        <?php if ($atributo['tipo'] === 'radio'): ?>
                            <?php foreach ($atributo['options'] as $optionName => $optionValue): ?>
                                <div class="radio">
                                    <input type="radio" id="<?= $atributo['id'] . '_' . $optionValue ?>" name="<?= $atributo['id'] ?>"
                                        value="<?= $optionValue ?>" <?= $atributo['constraints'] ?>>
                                    <label for="<?= $atributo['id'] . '_' . $optionValue ?>"><?= $optionName ?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <input type="<?= $atributo['tipo'] ?>" id="<?= $atributo['id'] ?>" name="<?= $atributo['id'] ?>"
                                <?= $atributo['constraints'] ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <input type="hidden" name="post-type" value="cadastro">

                <button type="submit" class="form-submit">Cadastrar</button>
                <span id="comCadastro" class="verificacao-cadastro">Já possuo cadastro</span>
            </form>
        </div>
    </main>

    <?php include('../layouts/footer.php'); ?>
</body>

</html>