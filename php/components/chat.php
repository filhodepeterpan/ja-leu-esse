<?php

/**
 * chat.php — Widget de Chat (modal fixo canto inferior direito)
 *
 * Integração esperada:
 *  - $usuarioLogado : array com dados do usuário da sessão
 *      ['id_usuario', 'nm_usuario', 'img_icone_perfil']
 *  - ChatService   : classe que abstrai as queries ao banco (a ser implementada)
 *
 * Por ora, toda a lógica de dados está mockada para demonstração.
 * Substitua as funções mock_ pelas chamadas reais à sua API/banco.
 */

// ── Mock de sessão ──────────────────────────────────────────────────────────
// Remova este bloco quando integrar com sua autenticação real.
$usuarioLogado = [
    'id_usuario' => 1,
    'nm_usuario' => 'Ana Oliveira',
    'img_icone_perfil' => 'https://api.dicebear.com/8.x/notionists/svg?seed=Ana',
];

$conversas = mock_getConversas($usuarioLogado['id_usuario']);
$totalNaoLidas = array_sum(array_column($conversas, 'nao_lidas'));
?>


<div class="chat">
    <button id="chat-fab" aria-label="Abrir chat" data-acao="fechar">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
        </svg>
        <span id="chat-badge"><?= $totalNaoLidas > 0 ? ($totalNaoLidas > 9 ? '9+' : $totalNaoLidas) : '' ?></span>
    </button>

    <div id="chat-widget" role="dialog" aria-label="Chat">

        <!-- ── VIEW 1: Lista de conversas ── -->
        <div class="chat-view" id="view-lista">

            <header class="chat-header">
                <div style="flex:1">
                    <div class="chat-header-title">Mensagens</div>
                    <div class="chat-header-subtitle">
                        <?= htmlspecialchars($usuarioLogado['nm_usuario']) ?>
                    </div>
                </div>
                <button class="btn-icon" data-acao="fechar" title="Fechar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div class="search-wrap">
                <input type="text" class="search-input" id="search-input" placeholder="Buscar conversa…"
                    oninput="filtrarConversas(this.value)">
            </div>

            <div class="conversations-list" id="conversations-list">
                <?php foreach ($conversas as $c): ?>
                    <div class="conv-item" id="conv-<?= $c['id_usuario'] ?>" data-id="<?= $c['id_usuario'] ?>"
                        data-nome="<?= htmlspecialchars($c['nm_usuario']) ?>"
                        data-avatar="<?= htmlspecialchars($c['img_icone_perfil'] ?? '') ?>">

                        <div class="avatar">
                            <div class="avatar-img" id="avatar-lista-<?= $c['id_usuario'] ?>">
                                <?php if (!empty($c['img_icone_perfil'])): ?>
                                    <img src="<?= htmlspecialchars($c['img_icone_perfil']) ?>"
                                        alt="<?= htmlspecialchars($c['nm_usuario']) ?>"
                                        onerror="this.style.display='none';this.parentNode.textContent='<?= avatarFallback($c['nm_usuario']) ?>'">
                                <?php else: ?>
                                    <?= avatarFallback($c['nm_usuario']) ?>
                                <?php endif; ?>
                            </div>
                            <!-- <span class="online-dot"></span> -->
                        </div>

                        <div class="conv-info">
                            <div class="conv-name"><?= htmlspecialchars($c['nm_usuario']) ?></div>
                            <div class="conv-last"><?= htmlspecialchars($c['ultima_mensagem']) ?></div>
                        </div>

                        <div class="conv-meta">
                            <span class="conv-time"><?= tempoRelativo($c['dt_envio']) ?></span>
                            <?php if ($c['nao_lidas'] > 0): ?>
                                <span class="badge-count"><?= $c['nao_lidas'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div><!-- /conversations-list -->

        </div><!-- /view-lista -->


        <!-- ── VIEW 2: Conversa aberta ── -->
        <div class="chat-view hidden-right" id="view-mensagens">

            <header class="chat-header">
                <button class="btn-icon" data-acao="voltar" title="Voltar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                </button>

                <div class="avatar">
                    <div class="avatar-img" id="header-avatar" style="width:36px;height:36px;font-size:11px;"></div>
                </div>

                <div class="conv-header-info">
                    <div class="chat-header-title" id="header-nome" style="font-size:14px;"></div>
                    <div class="chat-header-subtitle" id="header-status">online</div>
                </div>

                <button class="btn-icon" data-acao="fechar" title="Fechar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <!-- Mensagens -->
            <div class="messages-area" id="messages-area">
                <div class="empty-chat" id="empty-msg">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                    Nenhuma mensagem ainda.<br>Diga olá!
                </div>
            </div>

            <!-- Digitando -->
            <div class="typing-indicator" id="typing-indicator">
                <span></span><span></span><span></span>
            </div>

            <!-- Input -->
            <div class="message-input-wrap">
                <textarea class="message-textarea" id="message-input" placeholder="Digite uma mensagem…" rows="1"
                    oninput="onInputChange(this)" onkeydown="onKeyDown(event)"></textarea>
                <button class="btn-send" id="btn-send" >
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z" />
                    </svg>
                </button>
            </div>

        </div><!-- /view-mensagens -->

    </div><!-- /chat-widget -->
</div>


<?php include('../partials/scripts.php'); ?>