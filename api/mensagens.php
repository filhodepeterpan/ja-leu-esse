<?php
header('Content-Type: application/json; charset=UTF-8');
session_start();

require('../php/config/env.php');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado.']);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host={$DATABASE['host']};dbname={$DATABASE['dbname']};charset=utf8mb4",
        $DATABASE['username'],
        $DATABASE['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => true,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao conectar ao banco.']);
    exit;
}

$action = $_GET['action'] ?? null;
$meuId = (int) $_SESSION['id'];

switch ($action) {

    // ── Lista de conversas ────────────────────────────────────────────────────
    case 'conversas':
        $stmt = $pdo->prepare("
            SELECT
                u.id_usuario,
                u.nm_usuario,
                u.img_icone_perfil,
                (
                    SELECT ds_mensagem FROM mensagem
                    WHERE tipo = 'normal'
                      AND ((id_remetente = u.id_usuario AND id_destinatario = ?)
                       OR  (id_remetente = ? AND id_destinatario = u.id_usuario))
                    ORDER BY dt_envio DESC LIMIT 1
                ) AS ultima_mensagem,
                (
                    SELECT dt_envio FROM mensagem
                    WHERE (id_remetente = u.id_usuario AND id_destinatario = ?)
                       OR (id_remetente = ? AND id_destinatario = u.id_usuario)
                    ORDER BY dt_envio DESC LIMIT 1
                ) AS dt_ultima
            FROM usuario u
            WHERE u.id_usuario IN (
                SELECT id_destinatario FROM mensagem WHERE id_remetente = ?
                UNION
                SELECT id_remetente FROM mensagem WHERE id_destinatario = ?
            )
            ORDER BY dt_ultima DESC
        ");
        $stmt->execute([$meuId, $meuId, $meuId, $meuId, $meuId, $meuId]);
        echo json_encode($stmt->fetchAll());
        break;

    // ── Mensagens entre dois usuários ─────────────────────────────────────────
    case 'mensagens':
        $outroId = (int) ($_GET['id_usuario'] ?? 0);

        if (!$outroId) {
            http_response_code(400);
            echo json_encode(['error' => 'id_usuario obrigatório.']);
            exit;
        }

        $stmtU = $pdo->prepare("SELECT id_usuario, nm_usuario, img_icone_perfil FROM usuario WHERE id_usuario = ?");
        $stmtU->execute([$outroId]);
        $outroUsuario = $stmtU->fetch();

        // Apenas mensagens normais para a lista de mensagens
        $stmtM = $pdo->prepare("
            SELECT id_mensagem, id_remetente, id_destinatario, ds_mensagem, dt_envio, lido
            FROM mensagem
            WHERE tipo = 'normal'
              AND ((id_remetente = ? AND id_destinatario = ?)
               OR  (id_remetente = ? AND id_destinatario = ?))
            ORDER BY dt_envio ASC
        ");
        $stmtM->execute([$meuId, $outroId, $outroId, $meuId]);

        // Contexto de troca mais recente entre os dois usuários
        $stmtT = $pdo->prepare("
            SELECT ds_contexto FROM mensagem
            WHERE tipo = 'troca'
              AND ((id_remetente = ? AND id_destinatario = ?)
               OR  (id_remetente = ? AND id_destinatario = ?))
            ORDER BY dt_envio DESC LIMIT 1
        ");
        $stmtT->execute([$meuId, $outroId, $outroId, $meuId]);
        $contextoRow = $stmtT->fetch();
        $contextoTroca = $contextoRow ? json_decode($contextoRow['ds_contexto'], true) : null;

        echo json_encode([
            'meuId' => $meuId,
            'usuario' => $outroUsuario,
            'mensagens' => $stmtM->fetchAll(),
            'contexto_troca' => $contextoTroca,
        ]);
        break;

    // ── Enviar mensagem ───────────────────────────────────────────────────────
    case 'enviar':
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $destinatario = (int) ($input['id_destinatario'] ?? 0);
        $texto = trim($input['ds_mensagem'] ?? '');

        // ← FIX: extrai $tipoInput primeiro para não retornar null no ternário
        $tipoInput = $input['tipo'] ?? 'normal';
        $tipo = in_array($tipoInput, ['normal', 'troca']) ? $tipoInput : 'normal';
        $dsContexto = ($tipo === 'troca' && isset($input['ds_contexto']))
            ? json_encode($input['ds_contexto'])
            : null;

        if (!$destinatario || $texto === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Destinatário e mensagem são obrigatórios.']);
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO mensagem (id_remetente, id_destinatario, ds_mensagem, tipo, ds_contexto)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$meuId, $destinatario, $texto, $tipo, $dsContexto]);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;

    // ── Mensagens não lidas ───────────────────────────────────────────────────
    case 'nao_lidas':
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM mensagem WHERE id_destinatario = ? AND lido = 0 AND tipo = 'normal'");
        $stmt->execute([$meuId]);
        echo json_encode(['tem' => (int) $stmt->fetchColumn() > 0]);
        break;

    // ── Marcar como lidas ─────────────────────────────────────────────────────
    case 'marcar_lidas':
        $outroId = (int) ($_GET['id_usuario'] ?? 0);

        if (!$outroId) {
            http_response_code(400);
            echo json_encode(['error' => 'id_usuario obrigatório.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE mensagem SET lido = 1 WHERE id_destinatario = ? AND id_remetente = ? AND lido = 0");
        $stmt->execute([$meuId, $outroId]);
        echo json_encode(['ok' => true]);
        break;

    // ── Deletar conversa ──────────────────────────────────────────────────────
    case 'deletar':
        $outroId = (int) ($_GET['id_usuario'] ?? 0);

        if (!$outroId) {
            http_response_code(400);
            echo json_encode(['error' => 'id_usuario obrigatório.']);
            exit;
        }

        $stmt = $pdo->prepare("
            DELETE FROM mensagem
            WHERE (id_remetente = ? AND id_destinatario = ?)
               OR (id_remetente = ? AND id_destinatario = ?)
        ");
        $stmt->execute([$meuId, $outroId, $outroId, $meuId]);
        echo json_encode(['deleted' => $stmt->rowCount()]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action inválida.']);
}