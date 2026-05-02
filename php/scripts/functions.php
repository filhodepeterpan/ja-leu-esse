<?php

// ─── Sessão ───────────────────────────────────────────────────────────────────

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

// ─── Comunicação com a API ────────────────────────────────────────────────────

function chamarAPI(string $url, string $method = 'GET', array $dados = []): array
{
    $opcoes = [
        'http' => [
            'method'        => $method,
            'header'        => 'Content-Type: application/json',
            'content'       => in_array($method, ['POST', 'PUT']) ? json_encode($dados) : null,
            'ignore_errors' => true,
        ],
    ];

    $resposta = file_get_contents($url, false, stream_context_create($opcoes));
    return json_decode($resposta, true) ?? [];
}

// ─── Autenticação ─────────────────────────────────────────────────────────────

function logarUsuario(string $email, string $senha): bool
{
    global $API_CRUD;

    $url      = "{$API_CRUD['url']}?tabela=usuario&nm_email=" . urlencode($email);
    $usuarios = chamarAPI($url);

    if (empty($usuarios) || !password_verify($senha, $usuarios[0]['cd_senha'])) {
        return false;
    }

    $_SESSION['logado'] = true;
    $_SESSION['user']   = $usuarios[0]['nm_email'];
    $_SESSION['nome']   = $usuarios[0]['nm_usuario'];
    return true;
}

function cadastrarUsuario(array $dados): bool
{
    global $API_CRUD;

    $dados['cd_senha'] = password_hash($dados['cd_senha'], PASSWORD_DEFAULT);
    $resposta          = chamarAPI("{$API_CRUD['url']}?tabela=usuario", 'POST', $dados);

    return isset($resposta['id']);
}