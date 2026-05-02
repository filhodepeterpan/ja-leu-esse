<?php

// Sessão

function verificaLogin(): bool
{
    return !empty($_SESSION['logado']);
}

function aplicaRestricao(): void
{
    if (!verificaLogin()) {
        header("Location: ../pages/login.php");
        exit();
    }
}

// Comunicação com a API

function chamarAPI(string $url, string $method = 'GET', array $dados = []): array
{
    $opcoes = [
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/json',
            'content' => in_array($method, ['POST', 'PUT']) ? json_encode($dados) : null,
            'ignore_errors' => true,
        ],
    ];

    $resposta = file_get_contents($url, false, stream_context_create($opcoes));
    return json_decode($resposta, true) ?? [];
}

// Usuário

/**
 * Retorna todos os dados de um usuário pelo ID.
 */
function buscarUsuario(int $id): array
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=usuario&id=$id");
    return $resposta ?: [];
}

/**
 * Atualiza campos do usuário pelo ID.
 */
function atualizarUsuario(int $id, array $dados): bool
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=usuario&id=$id", 'PUT', $dados);
    return isset($resposta['updated']);
}

// Deleta o usuário.
function deletarUsuario(int $id): bool
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=usuario&id=$id", 'DELETE');
    return isset($resposta['deleted']) && $resposta['deleted'] > 0;
}

// Foto de perfil

/**
 * Salva a foto de perfil do usuário, substituindo qualquer foto anterior.
 * Cria a pasta do usuário se não existir.
 * Retorna o caminho relativo à raiz do projeto (para salvar no banco),
 * ou false em caso de erro.
 */
function salvarFotoPerfil(int $idUsuario, array $arquivo): string|false
{
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas) || $arquivo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // __DIR__ = php/scripts/ → ../../ = raiz do projeto
    $pasta = __DIR__ . "/../../assets/img/users_profile_images/$idUsuario/";

    // Cria a pasta do usuário se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true); // Permissões padrão para pastas. Permite a sobrescrição dos arquivos.
    }

    // Remove foto anterior (qualquer extensão)
    foreach (glob($pasta . "photo.*") as $fotoAntiga) {
        unlink($fotoAntiga);
    }

    $destino = $pasta . "photo.$extensao";
    $caminhoDb = "assets/img/users_profile_images/$idUsuario/photo.$extensao";

    return move_uploaded_file($arquivo['tmp_name'], $destino) ? $caminhoDb : false;
}

// Autenticação

function logarUsuario(string $email, string $senha): bool
{
    global $API_CRUD;

    $url = "{$API_CRUD['url']}?tabela=usuario&nm_email=" . urlencode($email);
    $usuarios = chamarAPI($url);

    if (empty($usuarios) || !password_verify($senha, $usuarios[0]['cd_senha'])) {
        return false;
    }

    $_SESSION['logado'] = true;
    $_SESSION['id'] = (int) $usuarios[0]['id_usuario'];
    $_SESSION['user'] = $usuarios[0]['nm_email'];
    $_SESSION['nome'] = $usuarios[0]['nm_usuario'];
    $_SESSION['foto'] = $usuarios[0]['img_icone_perfil'] ?? null;
    return true;
}

function cadastrarUsuario(array $dados): bool
{
    global $API_CRUD;

    $dados['cd_senha'] = password_hash($dados['cd_senha'], PASSWORD_DEFAULT);
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=usuario", 'POST', $dados);

    return isset($resposta['id']);
}