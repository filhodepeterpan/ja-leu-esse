<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
aplicaRestricao();

$usuario = buscarUsuario($_SESSION['id']);
$fotoPerfil = $usuario['img_icone_perfil'] ? "../../{$usuario['img_icone_perfil']}" : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse? | Meu Perfil</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>

    <main>
        <div class="perfil-container">
            <h2>Meu Perfil</h2>

            <!-- Foto -->
            <div class="foto-perfil">
                <?php if ($fotoPerfil): ?>
                    <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil">
                <?php else: ?>
                    <!-- Silhueta SVG quando não há foto -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" fill="#aaa">
                        <circle cx="40" cy="30" r="16"/>
                        <path d="M10 70 Q10 50 40 50 Q70 50 70 70Z"/>
                    </svg>
                <?php endif; ?>
            </div>

            <!-- Dados -->
            <div class="perfil-campo">
                <label>Nome</label>
                <span><?= htmlspecialchars($usuario['nm_usuario'] ?? '—') ?></span>
            </div>
            <div class="perfil-campo">
                <label>E-mail</label>
                <span><?= htmlspecialchars($usuario['nm_email'] ?? '—') ?></span>
            </div>
            <div class="perfil-campo">
                <label>Telefone</label>
                <span><?= htmlspecialchars($usuario['cd_telefone'] ?? '—') ?></span>
            </div>
            <div class="perfil-campo">
                <label>Gênero</label>
                <span><?= htmlspecialchars($usuario['sg_genero'] ?? '—') ?></span>
            </div>
            <div class="perfil-campo">
                <label>Gênero literário favorito</label>
                <span><?= htmlspecialchars($usuario['nm_genero_literario_favorito'] ?? '—') ?></span>
            </div>
            <div class="perfil-campo">
                <label>Endereço</label>
                <span>
                    <?= htmlspecialchars($usuario['nm_logradouro'] ?? '') ?>
                    <?= $usuario['cd_numero'] ? ', ' . $usuario['cd_numero'] : '' ?>
                    <?= $usuario['ds_complemento'] ? ' — ' . htmlspecialchars($usuario['ds_complemento']) : '' ?><br>
                    <?= htmlspecialchars($usuario['nm_bairro'] ?? '') ?>,
                    <?= htmlspecialchars($usuario['nm_cidade'] ?? '') ?> —
                    <?= htmlspecialchars($usuario['sg_uf'] ?? '') ?>,
                    CEP <?= htmlspecialchars($usuario['cd_cep'] ?? '') ?>
                </span>
            </div>

            <div class="perfil-acoes">
                <a href="perfil_edicao.php">Editar perfil</a>
            </div>
        </div>
    </main>

    <?php include('../layouts/footer.php'); ?>
</body>
</html>