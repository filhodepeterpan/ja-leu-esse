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
            'method' => $method,
            'header' => 'Content-Type: application/json',
            'content' => in_array($method, ['POST', 'PUT']) ? json_encode($dados) : '',
            'ignore_errors' => true,
        ],
    ];

    $resposta = file_get_contents($url, false, stream_context_create($opcoes));
    return json_decode($resposta, true) ?? [];
}

// ─── Usuário ──────────────────────────────────────────────────────────────────

function buscarUsuario(int $id): array
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=usuario&id=$id");
    return $resposta ?: [];
}

function atualizarUsuario(int $id, array $dados): bool
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=usuario&id=$id", 'PUT', $dados);
    return isset($resposta['updated']);
}

function deletarUsuario(int $id): bool
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=usuario&id=$id", 'DELETE');
    return isset($resposta['deleted']) && $resposta['deleted'] > 0;
}

function verificaDuplicata(string $campo, string $valor, ?int $ignorarId = null): bool
{
    global $API_CRUD;

    $url = "{$API_CRUD['url']}?tabela=usuario&$campo=" . urlencode($valor);
    $resultado = chamarAPI($url);

    if (empty($resultado))
        return false;

    if ($ignorarId !== null) {
        $resultado = array_filter($resultado, fn($u) => (int) $u['id_usuario'] !== $ignorarId);
    }

    return !empty($resultado);
}

// ─── Livros ───────────────────────────────────────────────────────────────────

function buscarLivro(int $idLivro): array
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=livro&id=$idLivro");
    return $resposta ?: [];
}

function buscarLivrosDoUsuario(int $idUsuario): array
{
    global $API_CRUD;
    return chamarAPI("{$API_CRUD['url']}?tabela=livro&id_usuario=$idUsuario");
}

function buscarTodosLivros(): array
{
    global $API_CRUD;
    return chamarAPI("{$API_CRUD['url']}?tabela=livro");
}

function cadastrarLivro(array $dados): int|false
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=livro", 'POST', $dados);
    return isset($resposta['id']) ? (int) $resposta['id'] : false;
}

function atualizarLivro(int $idLivro, array $dados): bool
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=livro&id=$idLivro", 'PUT', $dados);
    return isset($resposta['updated']);
}

function deletarLivro(int $idLivro): bool
{
    global $API_CRUD;
    $resposta = chamarAPI("{$API_CRUD['url']}?tabela=livro&id=$idLivro", 'DELETE');
    return isset($resposta['deleted']) && $resposta['deleted'] > 0;
}

/**
 * Salva a imagem do livro em assets/img/user{id}_books_images/{idLivro}.{ext}
 * Substitui qualquer imagem anterior do mesmo livro.
 * Retorna o caminho relativo à raiz para salvar no banco, ou false em erro.
 */
function salvarImagemLivro(int $idUsuario, int $idLivro, array $arquivo): string|false
{
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas) || $arquivo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // __DIR__ = php/scripts/ → sobe 2 níveis até a raiz
    $pasta = __DIR__ . "/../../assets/img/user{$idUsuario}_books_images/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    // Remove imagem anterior deste livro (qualquer extensão)
    foreach (glob($pasta . "$idLivro.*") as $imgAntiga) {
        unlink($imgAntiga);
    }

    $destino = $pasta . "$idLivro.$extensao";
    $caminhoDb = "assets/img/user{$idUsuario}_books_images/$idLivro.$extensao";

    return move_uploaded_file($arquivo['tmp_name'], $destino) ? $caminhoDb : false;
}

// ─── Foto de perfil ───────────────────────────────────────────────────────────

function salvarFotoPerfil(int $idUsuario, array $arquivo): string|false
{
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas) || $arquivo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $pasta = __DIR__ . "/../../assets/img/users_profile_images/$idUsuario/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    foreach (glob($pasta . "photo.*") as $fotoAntiga) {
        unlink($fotoAntiga);
    }

    $destino = $pasta . "photo.$extensao";
    $caminhoDb = "assets/img/users_profile_images/$idUsuario/photo.$extensao";

    return move_uploaded_file($arquivo['tmp_name'], $destino) ? $caminhoDb : false;
}

// ─── Autenticação ─────────────────────────────────────────────────────────────

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