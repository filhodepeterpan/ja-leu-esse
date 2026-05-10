<?php
header('Content-Type: application/json; charset=UTF-8');
require('../php/config/env.php');

// ─── Whitelist de segurança ───────────────────────────────────────────────────
// Chave primária de cada tabela — usada nos WHERE de GET/PUT/DELETE por ID
$chavePrimaria = [
    'usuario' => 'id_usuario',
    'livro' => 'id_livro',
];

// Colunas permitidas como filtro de busca por tabela
$colunasFiltraveis = [
    'usuario' => ['nm_email', 'nm_usuario', 'cd_telefone'],
    'livro' => ['id_usuario', 'nm_livro'],
];

// ─── Validação da tabela ──────────────────────────────────────────────────────
$tabela = $_GET['tabela'] ?? null;

if (!$tabela || !array_key_exists($tabela, $chavePrimaria)) {
    http_response_code(400);
    echo json_encode(['error' => "Tabela inválida ou não informada."]);
    exit;
}

$pk = $chavePrimaria[$tabela];
$filtros_permitidos = $colunasFiltraveis[$tabela];

// ─── Conexão PDO ─────────────────────────────────────────────────────────────
try {
    $pdo = new PDO(
        "mysql:host={$DATABASE['host']};dbname={$DATABASE['dbname']};charset=utf8mb4",
        $DATABASE['username'],
        $DATABASE['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao conectar ao banco.']);
    exit;
}

// ─── Roteamento por método HTTP ───────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($method) {

    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM $tabela WHERE $pk = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch() ?: null);
        } 
        else {
            $filtros = array_intersect_key($_GET, array_flip($filtros_permitidos));
            
            if ($tabela === 'livro') {
                // Base da query com JOIN para sempre ter os dados do usuário
                $sql = "SELECT l.*, u.nm_usuario, u.img_icone_perfil 
                        FROM livro l 
                        JOIN usuario u ON l.id_usuario = u.id_usuario";
                
                if ($filtros) {
                    // Adiciona os filtros (ex: id_usuario) mantendo o JOIN
                    $where = implode(' AND ', array_map(fn($k) => "l.$k = :$k", array_keys($filtros)));
                    $stmt = $pdo->prepare("$sql WHERE $where");
                    $stmt->execute($filtros);
                } 
                else {
                    // Listagem geral (usada no stock_photo.php)
                    $stmt = $pdo->query($sql);
                }
            } 
            else {
                // Comportamento normal para outras tabelas (como 'usuario')
                if ($filtros) {
                    $where = implode(' AND ', array_map(fn($k) => "$k = :$k", array_keys($filtros)));
                    $stmt = $pdo->prepare("SELECT * FROM $tabela WHERE $where");
                    $stmt->execute($filtros);
                } else {
                    $stmt = $pdo->query("SELECT * FROM $tabela");
                }
            }

            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $campos = implode(', ', array_keys($input));
        $valores = ':' . implode(', :', array_keys($input));
        $stmt = $pdo->prepare("INSERT INTO $tabela ($campos) VALUES ($valores)");
        $stmt->execute($input);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => "Parâmetro 'id' é obrigatório para atualização."]);
            exit;
        }
        $sets = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($input)));
        $stmt = $pdo->prepare("UPDATE $tabela SET $sets WHERE $pk = :id");
        $stmt->execute([...$input, 'id' => $id]);
        echo json_encode(['updated' => $stmt->rowCount()]);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => "Parâmetro 'id' é obrigatório para remoção."]);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM $tabela WHERE $pk = ?");
        $stmt->execute([$id]);
        echo json_encode(['deleted' => $stmt->rowCount()]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método HTTP não permitido.']);
}