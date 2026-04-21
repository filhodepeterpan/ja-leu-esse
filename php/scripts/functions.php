<?php

/**
 * Retorna lista de conversas do usuário logado.
 * Substitua pela query real:
 *   SELECT u.id_usuario, u.nm_usuario, u.img_icone_perfil,
 *          m.ds_mensagem AS ultima_mensagem, m.dt_envio,
 *          SUM(m.lido = 0 AND m.id_destinatario = ?) AS nao_lidas
 *   FROM mensagem m
 *   JOIN usuario u ON u.id_usuario = IF(m.id_remetente=?, m.id_destinatario, m.id_remetente)
 *   WHERE m.id_remetente = ? OR m.id_destinatario = ?
 *   GROUP BY u.id_usuario
 *   ORDER BY m.dt_envio DESC
 */
function mock_getConversas(int $idUsuario): array
{
    return [
        ['id_usuario' => 2, 'nm_usuario' => 'Carlos Lima', 'img_icone_perfil' => 'https://api.dicebear.com/8.x/notionists/svg?seed=Carlos', 'ultima_mensagem' => 'Oi! Você leu O Hobbit?', 'dt_envio' => '2026-03-27 09:15:00', 'nao_lidas' => 2],
        ['id_usuario' => 3, 'nm_usuario' => 'Beatriz Santos', 'img_icone_perfil' => 'https://api.dicebear.com/8.x/notionists/svg?seed=Beatriz', 'ultima_mensagem' => 'Adorei a indicação!', 'dt_envio' => '2026-03-26 21:40:00', 'nao_lidas' => 0],
        ['id_usuario' => 4, 'nm_usuario' => 'Diego Ferreira', 'img_icone_perfil' => 'https://api.dicebear.com/8.x/notionists/svg?seed=Diego', 'ultima_mensagem' => 'Vou começar hoje mesmo.', 'dt_envio' => '2026-03-25 14:05:00', 'nao_lidas' => 0],
        ['id_usuario' => 5, 'nm_usuario' => 'Fernanda Costa', 'img_icone_perfil' => 'https://api.dicebear.com/8.x/notionists/svg?seed=Fernanda', 'ultima_mensagem' => 'Você tem algum livro de Saramago?', 'dt_envio' => '2026-03-24 18:30:00', 'nao_lidas' => 1],
    ];
}

/**
 * Retorna mensagens entre dois usuários.
 * Substitua pela query real:
 *   SELECT * FROM mensagem
 *   WHERE (id_remetente=? AND id_destinatario=?)
 *      OR (id_remetente=? AND id_destinatario=?)
 *   ORDER BY dt_envio ASC
 *   LIMIT 50
 */
function mock_getMensagens(int $idUsuario, int $idContato): array
{
    $pool = [
        [
            2,
            ['
            ["id_mensagem"=>1,"id_remetente"=>2,"ds_mensagem"=>"Oi Ana! Tudo bem?","dt_envio"=>"2026-03-27 09:00:00"],
            ["id_mensagem"=>2,"id_remetente"=>1,"ds_mensagem"=>"Tudo sim! E você?","dt_envio"=>"2026-03-27 09:02:00"],
            ["id_mensagem"=>3,"id_remetente"=>2,"ds_mensagem"=>"Bem também! Você leu O Hobbit?","dt_envio"=>"2026-03-27 09:15:00"],
        '
            ]
        ],
    ];

    $mensagens = [
        2 => [
            ['id_mensagem' => 1, 'id_remetente' => 2, 'ds_mensagem' => 'Oi Ana! Tudo bem?', 'dt_envio' => '2026-03-27 09:00:00'],
            ['id_mensagem' => 2, 'id_remetente' => 1, 'ds_mensagem' => 'Tudo sim! E você?', 'dt_envio' => '2026-03-27 09:02:00'],
            ['id_mensagem' => 3, 'id_remetente' => 2, 'ds_mensagem' => 'Você leu O Hobbit?', 'dt_envio' => '2026-03-27 09:15:00'],
        ],
        3 => [
            ['id_mensagem' => 4, 'id_remetente' => 1, 'ds_mensagem' => 'Ei Bia! Vi que você gosta de fantasia também!', 'dt_envio' => '2026-03-26 21:30:00'],
            ['id_mensagem' => 5, 'id_remetente' => 3, 'ds_mensagem' => 'Sim! Adoro! Você indicou aquele livro da Ursula Le Guin', 'dt_envio' => '2026-03-26 21:35:00'],
            ['id_mensagem' => 6, 'id_remetente' => 3, 'ds_mensagem' => 'Adorei a indicação!', 'dt_envio' => '2026-03-26 21:40:00'],
        ],
        4 => [
            ['id_mensagem' => 7, 'id_remetente' => 4, 'ds_mensagem' => 'Ana, você tem Cem Anos de Solidão?', 'dt_envio' => '2026-03-25 14:00:00'],
            ['id_mensagem' => 8, 'id_remetente' => 1, 'ds_mensagem' => 'Tenho sim! Posso te emprestar', 'dt_envio' => '2026-03-25 14:02:00'],
            ['id_mensagem' => 9, 'id_remetente' => 4, 'ds_mensagem' => 'Vou começar hoje mesmo.', 'dt_envio' => '2026-03-25 14:05:00'],
        ],
        5 => [
            ['id_mensagem' => 10, 'id_remetente' => 5, 'ds_mensagem' => 'Olá Ana! Você tem algum livro de Saramago?', 'dt_envio' => '2026-03-24 18:30:00'],
        ],
    ];
    return $mensagens[$idContato] ?? [];
}

// ── Helpers ─────────────────────────────────────────────────────────────────
function tempoRelativo(string $dt): string
{
    $diff = time() - strtotime($dt);
    if ($diff < 60)
        return 'agora';
    if ($diff < 3600)
        return floor($diff / 60) . 'min';
    if ($diff < 86400)
        return floor($diff / 3600) . 'h';
    if ($diff < 604800)
        return floor($diff / 86400) . 'd';
    return date('d/m', strtotime($dt));
}

function avatarFallback(string $nome): string
{
    $partes = explode(' ', trim($nome));
    $ini = strtoupper(mb_substr($partes[0], 0, 1));
    if (isset($partes[1]))
        $ini .= strtoupper(mb_substr($partes[1], 0, 1));
    return $ini;
}
?>