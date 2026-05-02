<?php
session_start();

require('../config/env.php');
require('../scripts/functions.php');
require('../scripts/atributos.php');
aplicaRestricao();

$mensagem = ['texto' => '', 'tipo' => ''];

// ─── Processamento do formulário ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nm_usuario'                  => $_POST['nm_usuario'],
        'nm_email'                    => $_POST['nm_email'],
        'cd_telefone'                 => $_POST['cd_telefone']                  ?: null,
        'sg_genero'                   => $_POST['sg_genero'],
        'cd_cep'                      => $_POST['cd_cep'],
        'sg_uf'                       => $_POST['sg_uf'],
        'nm_cidade'                   => $_POST['nm_cidade'],
        'nm_bairro'                   => $_POST['nm_bairro'],
        'nm_logradouro'               => $_POST['nm_logradouro'],
        'cd_numero'                   => (int) $_POST['cd_numero'] ?: null,
        'ds_complemento'              => $_POST['ds_complemento']               ?: null,
        'nm_genero_literario_favorito'=> $_POST['nm_genero_literario_favorito'] ?: null,
    ];

    if (!empty($_FILES['foto_perfil']['name'])) {
        $caminho = salvarFotoPerfil($_SESSION['id'], $_FILES['foto_perfil']);

        if ($caminho) {
            $dados['img_icone_perfil'] = $caminho;
            $_SESSION['foto']          = $caminho;
        } else {
            $mensagem = ['texto' => 'Formato de imagem inválido. Use jpg, png ou webp.', 'tipo' => 'erro'];
        }
    }

    if ($mensagem['tipo'] !== 'erro') {
        $ok = atualizarUsuario($_SESSION['id'], $dados);

        if ($ok) {
            $_SESSION['nome'] = $dados['nm_usuario'];
            $_SESSION['user'] = $dados['nm_email'];
            $mensagem = ['texto' => 'Perfil atualizado com sucesso!', 'tipo' => 'sucesso'];
        } else {
            $mensagem = ['texto' => 'Erro ao salvar. Tente novamente.', 'tipo' => 'erro'];
        }
    }
}

// Campos que não fazem sentido na edição de perfil
$camposIgnorados = ['cd_senha', 'cd_confirmacao_senha'];

$usuario    = buscarUsuario($_SESSION['id']);
$fotoPerfil = $usuario['img_icone_perfil'] ? "../../{$usuario['img_icone_perfil']}" : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include('../partials/head.php'); ?>
    <title>Já leu esse? | Editar Perfil</title>
</head>
<body>
    <?php include('../layouts/header.php'); ?>

    <?php if ($mensagem['texto']): ?>
        <script>alert('<?= htmlspecialchars($mensagem['texto']) ?>');</script>
    <?php endif; ?>

    <main>
        <div class="perfil-container">
            <h2>Editar Perfil</h2>

            <form action="#" method="POST" enctype="multipart/form-data">

                <!-- Foto clicável -->
                <label for="input-foto" class="foto-wrapper" title="Alterar foto de perfil">
                    <div class="foto-perfil">
                        <?php if ($fotoPerfil): ?>
                            <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil" id="preview-foto">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="80" fill="#aaa" id="svg-placeholder">
                                <circle cx="40" cy="30" r="16"/>
                                <path d="M10 70 Q10 50 40 50 Q70 50 70 70Z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="btn-trocar-foto">+</div>
                </label>
                <input type="file" id="input-foto" name="foto_perfil" accept="image/jpeg,image/png,image/webp">

                <!-- Campos gerados por atributos.php -->
                <?php foreach ($atributos as $atributo):
                    if (in_array($atributo['id'], $camposIgnorados)) continue;
                    $valorAtual = htmlspecialchars($usuario[$atributo['id']] ?? '');
                ?>
                    <div class="form-item">
                        <label for="<?= $atributo['id'] ?>"><?= $atributo['nome'] ?></label>

                        <?php if ($atributo['tipo'] === 'radio'): ?>
                            <?php foreach ($atributo['options'] as $optionName => $optionValue): ?>
                                <div class="radio">
                                    <input type="radio"
                                           id="<?= $atributo['id'] . '_' . $optionValue ?>"
                                           name="<?= $atributo['id'] ?>"
                                           value="<?= $optionValue ?>"
                                           <?= $atributo['constraints'] ?>
                                           <?= ($usuario[$atributo['id']] ?? '') === $optionValue ? 'checked' : '' ?>>
                                    <label for="<?= $atributo['id'] . '_' . $optionValue ?>"><?= $optionName ?></label>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <input type="<?= $atributo['tipo'] ?>"
                                   id="<?= $atributo['id'] ?>"
                                   name="<?= $atributo['id'] ?>"
                                   value="<?= $valorAtual ?>"
                                   <?= $atributo['constraints'] ?>>
                            <?php if ($atributo['id'] === 'cd_cep'): ?>
                                <span class="msg-erro" id="erro-cep"></span>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

                <div class="form-acoes">
                    <button type="submit">Salvar</button>
                    <a href="perfil.php">Cancelar</a>
                </div>

            </form>
        </div>
    </main>

    <?php include('../layouts/footer.php'); ?>
</body>
</html>