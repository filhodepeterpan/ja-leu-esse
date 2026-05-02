<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
require('../scripts/atributos.php');

if (verificaLogin()) {
    header("Location: home.php");
    exit();
}

// ─── Processamento do formulário ──────────────────────────────────────────────
$mensagem = ['texto' => '', 'tipo' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postType = $_POST['post-type'] ?? '';

    if ($postType === 'login') {

        $email = $_POST['login_nm_email'] ?? '';
        $senha = $_POST['login_cd_senha'] ?? '';

        if (logarUsuario($email, $senha)) {
            header("Location: ../pages/home.php");
            exit();
        }

        $mensagem = ['texto' => 'E-mail ou senha inválidos.', 'tipo' => 'erro'];

    } elseif ($postType === 'cadastro') {

        $senha = $_POST['cd_senha'] ?? '';
        $confirmacaoSenha = $_POST['cd_confirmacao_senha'] ?? '';
        $genero = $_POST['sg_genero'] ?? null;

        if ($senha !== $confirmacaoSenha) {
            $mensagem = ['texto' => 'As senhas não coincidem.', 'tipo' => 'erro'];
        } elseif (!$genero) {
            $mensagem = ['texto' => 'Selecione um gênero.', 'tipo' => 'erro'];
        } else {
            $dados = [
                'nm_usuario' => $_POST['nm_usuario'],
                'nm_email' => $_POST['nm_email'],
                'cd_senha' => $senha,
                'cd_telefone' => $_POST['cd_telefone'] ?? null,
                'sg_genero' => $genero,
                'cd_cep' => $_POST['cd_cep'],
                'sg_uf' => $_POST['sg_uf'],
                'nm_cidade' => $_POST['nm_cidade'],
                'nm_bairro' => $_POST['nm_bairro'],
                'nm_logradouro' => $_POST['nm_logradouro'],
                'cd_numero' => $_POST['cd_numero'],
                'ds_complemento' => $_POST['ds_complemento'] ?? null,
                'nm_genero_literario_favorito' => $_POST['nm_genero_literario_favorito'] ?? null,
            ];

            $mensagem = cadastrarUsuario($dados)
                ? ['texto' => 'Cadastro realizado com sucesso!', 'tipo' => 'sucesso']
                : ['texto' => 'Erro ao cadastrar. Tente novamente.', 'tipo' => 'erro'];
        }
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

    <?php if ($mensagem['texto']): ?>
        <script>alert('<?= htmlspecialchars($mensagem['texto']) ?>');</script>
    <?php endif; ?>

    <main>
        <div id="formularios">

            <form action="#" method="POST" id="formLogin">
                <h1 class="form-name">Login</h1>
                <div class="form-item">
                    <label for="login_nm_email">Email:</label>
                    <input type="email" id="login_nm_email" name="login_nm_email" required>
                </div>
                <div class="form-item">
                    <label for="login_cd_senha">Senha:</label>
                    <input type="password" id="login_cd_senha" name="login_cd_senha" required>
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
                                    <input type="radio" id="<?= $atributo['id'] . '_' . $optionValue ?>"
                                        name="<?= $atributo['id'] ?>" value="<?= $optionValue ?>" <?= $atributo['constraints'] ?>
                                        <?= ($atributo['default'] ?? '') === $optionValue ? 'checked' : '' ?>>
                                    <label for="<?= $atributo['id'] . '_' . $optionValue ?>"><?= $optionName ?></label>
                                </div>
                            <?php endforeach; ?>
                            
                        <?php else: ?>
                            <input type="<?= $atributo['tipo'] ?>" id="<?= $atributo['id'] ?>" name="<?= $atributo['id'] ?>"
                                <?= $atributo['constraints'] ?>>
                            <?php if ($atributo['id'] === 'cd_cep'): ?>
                                <span class="msg-erro" id="erro-cep"></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <span class="msg-erro" id="erro-senhas"></span>
                <input type="hidden" name="post-type" value="cadastro">
                <button type="submit" class="form-submit" id="cadastrar">Cadastrar</button>
                <span id="comCadastro" class="verificacao-cadastro">Já possuo cadastro</span>
            </form>

        </div>
    </main>

    <?php include('../layouts/footer.php'); ?>
</body>

</html>