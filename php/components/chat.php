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
    <button id="chat-fab" aria-label="Abrir chat" onclick="toggleChat()">
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
                <button class="btn-icon" onclick="toggleChat()" title="Fechar">
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
                        data-avatar="<?= htmlspecialchars($c['img_icone_perfil'] ?? '') ?>"
                        onclick="abrirConversa(<?= $c['id_usuario'] ?>, <?= json_encode($c['nm_usuario']) ?>, <?= json_encode($c['img_icone_perfil'] ?? '') ?>)">

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
                <button class="btn-icon" onclick="voltarLista()" title="Voltar">
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

                <button class="btn-icon" onclick="toggleChat()" title="Fechar">
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
                <button class="btn-send" id="btn-send" onclick="enviarMensagem()">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z" />
                    </svg>
                </button>
            </div>

        </div><!-- /view-mensagens -->

    </div><!-- /chat-widget -->
</div>






<!-- ══════════════════════════════════════════════════════════
     DADOS PHP → JS
═══════════════════════════════════════════════════════════ -->
<script>
    // Dados injetados pelo PHP (substitua pelas chamadas AJAX reais)
    const USUARIO_LOGADO = <?= json_encode($usuarioLogado, JSON_UNESCAPED_UNICODE) ?>;

    const MOCK_MENSAGENS = <?= json_encode(
        array_reduce(
            array_keys(array_fill(2, 4, null)),
            fn($acc, $id) => $acc + [$id => mock_getMensagens($usuarioLogado['id_usuario'], $id)],
            []
        ),
        JSON_UNESCAPED_UNICODE
    ) ?>;
</script>

<script>
    /* ═══════════════════════════════════════════════
       ESTADO
    ════════════════════════════════════════════════ */
    let chatAberto = false;
    let conversaAtiva = null; // id_usuario do contato
    let mensagensCache = {};   // { id_usuario: [{...}] }

    /* ═══════════════════════════════════════════════
       TOGGLE WIDGET
    ════════════════════════════════════════════════ */
    function toggleChat() {
        chatAberto = !chatAberto;
        document.getElementById('chat-widget').classList.toggle('open', chatAberto);

        // Zera badge ao abrir
        if (chatAberto) atualizarBadgeFab();
    }

    /* ═══════════════════════════════════════════════
       NAVEGAÇÃO DE VIEWS
    ════════════════════════════════════════════════ */
    function abrirConversa(idContato, nome, avatar) {
        conversaAtiva = idContato;

        // Atualiza header
        document.getElementById('header-nome').textContent = nome;
        const ha = document.getElementById('header-avatar');
        ha.innerHTML = '';
        if (avatar) {
            const img = document.createElement('img');
            img.src = avatar;
            img.alt = nome;
            img.onerror = () => { ha.innerHTML = ''; ha.textContent = iniciais(nome); };
            ha.appendChild(img);
        } else {
            ha.textContent = iniciais(nome);
        }

        // Marca ativo na lista
        document.querySelectorAll('.conv-item').forEach(el => el.classList.remove('active'));
        document.getElementById(`conv-${idContato}`)?.classList.add('active');

        // Slide
        document.getElementById('view-lista').classList.add('hidden-left');
        document.getElementById('view-mensagens').classList.remove('hidden-right');
        document.getElementById('view-mensagens').classList.remove('hidden-left');

        carregarMensagens(idContato);
    }

    function voltarLista() {
        conversaAtiva = null;
        document.getElementById('view-lista').classList.remove('hidden-left');
        document.getElementById('view-mensagens').classList.add('hidden-right');
        document.getElementById('view-mensagens').classList.remove('hidden-left');
    }

    /* ═══════════════════════════════════════════════
       CARREGAR MENSAGENS
    ════════════════════════════════════════════════ */
    async function carregarMensagens(idContato) {
        const area = document.getElementById('messages-area');
        area.innerHTML = '';

        // Tenta cache; senão busca na API
        let msgs = mensagensCache[idContato];
        if (!msgs) {
            // ── Integração real: descomente e ajuste a URL ──
            // try {
            //   const r = await fetch(`/api/mensagens.php?contato=${idContato}`);
            //   msgs = await r.json();
            // } catch(e) { msgs = []; }

            // Mock por ora:
            msgs = MOCK_MENSAGENS[idContato] ?? [];
            mensagensCache[idContato] = msgs;
        }

        renderizarMensagens(msgs);
    }

    /* ═══════════════════════════════════════════════
       RENDERIZAR MENSAGENS
    ════════════════════════════════════════════════ */
    function renderizarMensagens(msgs) {
        const area = document.getElementById('messages-area');
        area.innerHTML = '';

        if (!msgs.length) {
            area.innerHTML = `
      <div class="empty-chat">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Nenhuma mensagem ainda.<br>Diga olá!
      </div>`;
            return;
        }

        let ultimaData = null;

        msgs.forEach((msg, idx) => {
            const ehMeu = msg.id_remetente === USUARIO_LOGADO.id_usuario;
            const dt = new Date(msg.dt_envio);
            const dataStr = dt.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' });
            const hora = dt.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

            // Separador de data
            if (dataStr !== ultimaData) {
                ultimaData = dataStr;
                const sep = document.createElement('div');
                sep.className = 'date-sep';
                sep.textContent = dataStr;
                area.appendChild(sep);
            }

            // Decide se próxima mensagem é do mesmo remetente (para omitir avatar)
            const proxMsg = msgs[idx + 1];
            const proxMesmo = proxMsg && proxMsg.id_remetente === msg.id_remetente;

            const row = document.createElement('div');
            row.className = `msg-row ${ehMeu ? 'me' : 'them'}`;
            row.dataset.id = msg.id_mensagem;

            // Avatar (só na última de uma sequência)
            const avatarDiv = document.createElement('div');
            avatarDiv.className = `msg-avatar${proxMesmo ? ' invisible' : ''}`;
            if (!ehMeu) {
                // avatar do contato
                const contaEl = document.getElementById(`conv-${conversaAtiva}`);
                const avatarSrc = contaEl?.dataset.avatar;
                if (avatarSrc) {
                    const img = document.createElement('img');
                    img.src = avatarSrc;
                    img.onerror = () => { avatarDiv.innerHTML = ''; avatarDiv.textContent = iniciais(contaEl.dataset.nome); };
                    avatarDiv.appendChild(img);
                } else {
                    avatarDiv.textContent = iniciais(contaEl?.dataset.nome ?? '?');
                }
            }

            // Bubble
            const bubbleWrap = document.createElement('div');
            bubbleWrap.style.display = 'flex';
            bubbleWrap.style.flexDirection = 'column';
            bubbleWrap.style.alignItems = ehMeu ? 'flex-end' : 'flex-start';

            const bubble = document.createElement('div');
            bubble.className = 'bubble';
            bubble.textContent = msg.ds_mensagem;

            const timeEl = document.createElement('div');
            timeEl.className = 'bubble-time';
            timeEl.textContent = hora + (ehMeu ? ' ✓' : '');

            bubbleWrap.appendChild(bubble);
            bubbleWrap.appendChild(timeEl);

            if (ehMeu) {
                row.appendChild(bubbleWrap);
                row.appendChild(avatarDiv); // lado direito (invisível pra mim, mas mantém espaço)
            } else {
                row.appendChild(avatarDiv);
                row.appendChild(bubbleWrap);
            }

            area.appendChild(row);
        });

        // Scroll para o fim
        area.scrollTop = area.scrollHeight;
    }

    /* ═══════════════════════════════════════════════
       ENVIAR MENSAGEM
    ════════════════════════════════════════════════ */
    async function enviarMensagem() {
        const input = document.getElementById('message-input');
        const texto = input.value.trim();
        if (!texto || !conversaAtiva) return;

        input.value = '';
        autoResize(input);
        document.getElementById('btn-send').classList.remove('ready');

        const novaMensagem = {
            id_mensagem: Date.now(), // temporário até o banco retornar o ID real
            id_remetente: USUARIO_LOGADO.id_usuario,
            id_destinatario: conversaAtiva,
            ds_mensagem: texto,
            dt_envio: new Date().toISOString().replace('T', ' ').slice(0, 19),
        };

        // Adiciona ao cache local
        if (!mensagensCache[conversaAtiva]) mensagensCache[conversaAtiva] = [];
        mensagensCache[conversaAtiva].push(novaMensagem);

        // Renderiza a bolha imediatamente (otimista)
        adicionarBolha(novaMensagem);
        atualizarUltimaMensagem(conversaAtiva, texto, novaMensagem.dt_envio);

        // ── Integração real: descomente e ajuste ──
        // try {
        //   const resp = await fetch('/api/enviar_mensagem.php', {
        //     method: 'POST',
        //     headers: {'Content-Type':'application/json'},
        //     body: JSON.stringify({
        //       id_destinatario: conversaAtiva,
        //       ds_mensagem: texto
        //     })
        //   });
        //   const data = await resp.json();
        //   // Atualiza id_mensagem real se necessário
        // } catch(e) {
        //   // Trate erros de envio aqui (ex: mostrar ícone de erro na bolha)
        //   console.error('Erro ao enviar mensagem:', e);
        // }
    }

    /* Adiciona só a nova bolha sem re-renderizar tudo */
    function adicionarBolha(msg) {
        const area = document.getElementById('messages-area');

        // Remove estado vazio se existir
        const empty = area.querySelector('.empty-chat');
        if (empty) empty.remove();

        const ehMeu = msg.id_remetente === USUARIO_LOGADO.id_usuario;
        const dt = new Date(msg.dt_envio);
        const hora = dt.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

        const row = document.createElement('div');
        row.className = `msg-row ${ehMeu ? 'me' : 'them'}`;
        row.dataset.id = msg.id_mensagem;

        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'msg-avatar invisible';

        const bubbleWrap = document.createElement('div');
        bubbleWrap.style.display = 'flex';
        bubbleWrap.style.flexDirection = 'column';
        bubbleWrap.style.alignItems = ehMeu ? 'flex-end' : 'flex-start';

        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.textContent = msg.ds_mensagem;

        const timeEl = document.createElement('div');
        timeEl.className = 'bubble-time';
        timeEl.textContent = hora + (ehMeu ? ' ✓' : '');

        bubbleWrap.appendChild(bubble);
        bubbleWrap.appendChild(timeEl);

        if (ehMeu) {
            row.appendChild(bubbleWrap);
            row.appendChild(avatarDiv);
        } else {
            row.appendChild(avatarDiv);
            row.appendChild(bubbleWrap);
        }

        area.appendChild(row);
        area.scrollTop = area.scrollHeight;
    }

    /* ═══════════════════════════════════════════════
       ATUALIZAR PREVIEW NA LISTA
    ════════════════════════════════════════════════ */
    function atualizarUltimaMensagem(idContato, texto, dt) {
        const item = document.getElementById(`conv-${idContato}`);
        if (!item) return;
        const lastEl = item.querySelector('.conv-last');
        const timeEl = item.querySelector('.conv-time');
        if (lastEl) lastEl.textContent = texto;
        if (timeEl) timeEl.textContent = 'agora';

        // Move item para o topo da lista
        const lista = document.getElementById('conversations-list');
        lista.prepend(item);
    }

    /* ═══════════════════════════════════════════════
       BADGE FAB
    ════════════════════════════════════════════════ */
    function atualizarBadgeFab(total) {
        const badge = document.getElementById('chat-badge');
        if (total === undefined) {
            badge.classList.remove('visible');
            return;
        }
        badge.textContent = total > 9 ? '9+' : total;
        badge.classList.toggle('visible', total > 0);
    }

    // Inicializa badge com valor do PHP
    (function () {
        const total = <?= (int) $totalNaoLidas ?>;
        if (total > 0) {
            const badge = document.getElementById('chat-badge');
            badge.textContent = total > 9 ? '9+' : total;
            badge.classList.add('visible');
        }
    })();

    /* ═══════════════════════════════════════════════
       BUSCA DE CONVERSAS
    ════════════════════════════════════════════════ */
    function filtrarConversas(q) {
        q = q.toLowerCase();
        document.querySelectorAll('.conv-item').forEach(item => {
            const nome = item.dataset.nome?.toLowerCase() ?? '';
            item.style.display = nome.includes(q) ? '' : 'none';
        });
    }

    /* ═══════════════════════════════════════════════
       INPUT HANDLERS
    ════════════════════════════════════════════════ */
    function onInputChange(el) {
        autoResize(el);
        document.getElementById('btn-send').classList.toggle('ready', el.value.trim().length > 0);
    }

    function onKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            enviarMensagem();
        }
    }

    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 100) + 'px';
    }

    /* ═══════════════════════════════════════════════
       HELPERS
    ════════════════════════════════════════════════ */
    function iniciais(nome) {
        const p = nome.trim().split(' ');
        let s = p[0][0].toUpperCase();
        if (p[1]) s += p[1][0].toUpperCase();
        return s;
    }

    /* ═══════════════════════════════════════════════
       POLLING SIMPLES (opcional — substitua por WebSocket)
       Busca novas mensagens a cada 5s se o chat estiver aberto
    ════════════════════════════════════════════════ */
    // setInterval(async () => {
    //   if (!chatAberto || !conversaAtiva) return;
    //   const r = await fetch(`/api/mensagens.php?contato=${conversaAtiva}&depois=${ultimoIdMensagem}`);
    //   const novas = await r.json();
    //   novas.forEach(m => {
    //     mensagensCache[conversaAtiva].push(m);
    //     adicionarBolha(m);
    //   });
    // }, 5000);
</script>

</body>

</html>