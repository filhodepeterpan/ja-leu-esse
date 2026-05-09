export class Chat {
    constructor() {
        this.widget = document.getElementById('chat-widget');
        this.panel = document.getElementById('chat-panel');
        this.toggle = document.getElementById('chat-toggle');
        this.badge = document.getElementById('chat-badge');
        this.minimizar = document.getElementById('chat-minimizar');
        this.conversasLista = document.getElementById('chat-conversas-lista');
        this.mensagensLista = document.getElementById('chat-mensagens-lista');
        this.mensagensHeader = document.getElementById('chat-mensagens-header');
        this.mensagensContainer = document.getElementById('chat-mensagens-container');
        this.input = document.getElementById('chat-input');
        this.enviarBtn = document.getElementById('chat-enviar');
        this.emojiBtn = document.getElementById('chat-emoji-btn');
        this.emojiPicker = document.getElementById('chat-emoji-picker');

        this.apiUrl = this.widget?.dataset.apiUrl ?? '';
        this.idDestinatario = null;
        this.pollingInterval = null;
        this.badgeInterval = null;
        this._idDestinatarioTroca = null;
    }

    init() {
        if (!this.widget) return;

        this.mensagensContainer?.classList.add('sem-conversa');

        // ── Toggle abrir/fechar
        this.toggle.addEventListener('click', () => this.abrirChat());
        this.minimizar.addEventListener('click', () => this.fecharChat());

        // ── Envio de mensagem
        this.enviarBtn.addEventListener('click', () => this.enviarMensagem());
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.enviarMensagem();
            }
        });

        // ── Emojis
        this.emojiBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.emojiPicker.classList.toggle('chat-oculto');
        });
        this.emojiPicker.querySelectorAll('.chat-emoji').forEach(el => {
            el.addEventListener('click', () => {
                this.input.value += el.dataset.emoji;
                this.emojiPicker.classList.add('chat-oculto');
                this.input.focus();
            });
        });
        document.addEventListener('click', (e) => {
            if (!this.emojiPicker.contains(e.target) && e.target !== this.emojiBtn) {
                this.emojiPicker.classList.add('chat-oculto');
            }
        });

        // ── Integração com trocas (capture phase)
        document.querySelectorAll('.card-livro').forEach(card => {
            card.addEventListener('click', () => {
                const id = parseInt(card.dataset.idUsuario);
                if (id) this._idDestinatarioTroca = id;
            }, true);
        });

        document.getElementById('btnNegociar')?.addEventListener('click', async (e) => {
            e.stopImmediatePropagation();
            e.preventDefault();

            if (!this._idDestinatarioTroca) return;

            const ofertaImg = document.getElementById('slotOfertaImg')?.src ?? '';
            const ofertaNome = document.getElementById('slotOfertaNome')?.textContent ?? '';
            const desejoImg = document.getElementById('slotDesejoImg')?.src ?? '';
            const desejoNome = document.getElementById('slotDesejoNome')?.textContent ?? '';

            document.getElementById('modalTroca')?.classList.add('hidden');

            // Salva o contexto da troca no banco — assim o outro usuário também vê a barra
            await this.salvarContextoTroca(this._idDestinatarioTroca, { ofertaImg, ofertaNome, desejoImg, desejoNome });

            this.abrirChat();
            this.selecionarConversa(this._idDestinatarioTroca);
        }, true);

        // ── Badge
        this.verificarNaoLidas();
        this.iniciarBadgePolling();
    }

    // ── Painel ───────────────────────────────────────────────────────────────

    abrirChat() {
        this.panel.classList.remove('chat-oculto');
        this.widget.classList.add('chat-aberto');
        this.pararBadgePolling();
        this.ocultarBadge();
        this.carregarConversas();
    }

    fecharChat() {
        this.panel.classList.add('chat-oculto');
        this.widget.classList.remove('chat-aberto');
        this.pararPolling();
        this.iniciarBadgePolling();
    }

    // ── Badge ────────────────────────────────────────────────────────────────

    async verificarNaoLidas() {
        try {
            const res = await fetch(`${this.apiUrl}?action=nao_lidas`);
            const data = await res.json();
            data.tem ? this.exibirBadge() : this.ocultarBadge();
        } catch { /* silencioso */ }
    }

    exibirBadge() { this.badge?.classList.remove('chat-oculto'); }
    ocultarBadge() { this.badge?.classList.add('chat-oculto'); }

    iniciarBadgePolling() {
        this.badgeInterval = setInterval(() => this.verificarNaoLidas(), 5000);
    }

    pararBadgePolling() {
        clearInterval(this.badgeInterval);
        this.badgeInterval = null;
    }

    // ── Conversas ────────────────────────────────────────────────────────────

    async carregarConversas() {
        try {
            const res = await fetch(`${this.apiUrl}?action=conversas`);
            const data = await res.json();
            this.renderConversas(data);
        } catch {
            this.conversasLista.innerHTML =
                '<p class="chat-placeholder">Erro ao carregar conversas.</p>';
        }
    }

    renderConversas(conversas) {
        if (!Array.isArray(conversas) || conversas.length === 0) {
            this.conversasLista.innerHTML =
                '<p class="chat-placeholder">Você ainda não iniciou uma conversa.</p>';
            return;
        }

        this.conversasLista.innerHTML = conversas.map(c => {
            const foto = c.img_icone_perfil
                ? `../../${c.img_icone_perfil}`
                : '../../assets/img/avatar_padrao.png';
            const ativo = this.idDestinatario === parseInt(c.id_usuario) ? 'ativo' : '';
            const ultima = c.ultima_mensagem ? this.truncar(c.ultima_mensagem, 22) : '—';

            return `
                <div class="chat-contato ${ativo}" data-id="${c.id_usuario}">
                    <img class="chat-contato-foto" src="${foto}" alt="${this.escapeHtml(c.nm_usuario)}">
                    <div class="chat-contato-info">
                        <span class="chat-contato-nome">${this.escapeHtml(c.nm_usuario)}</span>
                        <span class="chat-contato-ultima">${this.escapeHtml(ultima)}</span>
                    </div>
                </div>`;
        }).join('');

        this.conversasLista.querySelectorAll('.chat-contato').forEach(el => {
            el.addEventListener('click', () => {
                this.selecionarConversa(parseInt(el.dataset.id));
            });
        });
    }

    // ── Mensagens ────────────────────────────────────────────────────────────

    selecionarConversa(idUsuario) {
        this.idDestinatario = idUsuario;
        this.pararPolling();

        this.mensagensContainer?.classList.remove('sem-conversa');

        document.querySelectorAll('.chat-contato').forEach(el => {
            el.classList.toggle('ativo', parseInt(el.dataset.id) === idUsuario);
        });

        this.marcarLidas(idUsuario);
        this.carregarMensagens();
        this.iniciarPolling();
        this.input.focus();
    }

    // Salva o contexto da troca no banco como mensagem especial (tipo='troca')
    async salvarContextoTroca(idDestinatario, ctx) {
        try {
            await fetch(`${this.apiUrl}?action=enviar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_destinatario: idDestinatario,
                    ds_mensagem: 'Proposta de troca',
                    tipo: 'troca',
                    ds_contexto: ctx,
                }),
            });
        } catch { /* silencioso */ }
    }

    // Renderiza a barra de contexto de troca fixa acima das mensagens
    atualizarContextoTroca(ctx) {
        document.getElementById('chat-contexto-troca')?.remove();
        if (!ctx || (!ctx.ofertaNome && !ctx.desejoNome)) return;

        const div = document.createElement('div');
        div.id = 'chat-contexto-troca';
        div.innerHTML = `
            <img src="${this.escapeHtml(ctx.ofertaImg)}"  alt="${this.escapeHtml(ctx.ofertaNome)}">
            <span>${this.escapeHtml(ctx.ofertaNome)}</span>
            <span>⇄</span>
            <img src="${this.escapeHtml(ctx.desejoImg)}"  alt="${this.escapeHtml(ctx.desejoNome)}">
            <span>${this.escapeHtml(ctx.desejoNome)}</span>`;

        // Insere ANTES da lista de mensagens, logo abaixo do header da conversa
        this.mensagensLista.parentNode.insertBefore(div, this.mensagensLista);
    }

    async carregarMensagens() {
        if (!this.idDestinatario) return;

        try {
            const res = await fetch(`${this.apiUrl}?action=mensagens&id_usuario=${this.idDestinatario}`);
            const data = await res.json();
            this.renderMensagens(data);
            this.marcarLidas(this.idDestinatario);
        } catch { /* silencioso no polling */ }
    }

    renderMensagens(data) {
        if (!data || data.error) return;

        const { meuId, usuario, mensagens, contexto_troca } = data;

        // Header com foto, nome e botão de deletar
        if (usuario) {
            const foto = usuario.img_icone_perfil
                ? `../../${usuario.img_icone_perfil}`
                : '../../assets/img/avatar_padrao.png';

            this.mensagensHeader.innerHTML = `
                <img src="${foto}" alt="${this.escapeHtml(usuario.nm_usuario)}">
                <span id="chat-contato-nome">${this.escapeHtml(usuario.nm_usuario)}</span>
                <button id="chat-deletar-conversa" title="Deletar conversa" aria-label="Deletar conversa">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" fill="currentColor">
                        <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                        <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2"/>
                        <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>`;

            document.getElementById('chat-deletar-conversa')
                ?.addEventListener('click', () => this.deletarConversa());
        }

        // Barra de contexto de troca — vem do banco, visível para os dois usuários
        this.atualizarContextoTroca(contexto_troca);

        if (!mensagens || mensagens.length === 0) {
            this.mensagensLista.innerHTML =
                '<p class="chat-placeholder">Nenhuma mensagem ainda. Diga olá! 👋</p>';
            return;
        }

        const atBottom = this.mensagensLista.scrollTop + this.mensagensLista.clientHeight
            >= this.mensagensLista.scrollHeight - 20;

        this.mensagensLista.innerHTML = mensagens.map(m => {
            const enviada = parseInt(m.id_remetente) === parseInt(meuId);
            return `
                <div class="chat-msg ${enviada ? 'enviada' : 'recebida'}">
                    <span class="chat-msg-texto">${this.escapeHtml(m.ds_mensagem)}</span>
                    <span class="chat-msg-hora">${this.formatarHora(m.dt_envio)}</span>
                </div>`;
        }).join('');

        if (atBottom) this.mensagensLista.scrollTop = this.mensagensLista.scrollHeight;
    }

    async enviarMensagem() {
        const texto = this.input.value.trim();
        if (!texto || !this.idDestinatario) return;

        this.input.value = '';

        try {
            await fetch(`${this.apiUrl}?action=enviar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_destinatario: this.idDestinatario, ds_mensagem: texto }),
            });
            await this.carregarMensagens();
            await this.carregarConversas();
        } catch {
            this.input.value = texto;
        }
    }

    async marcarLidas(idUsuario) {
        try {
            await fetch(`${this.apiUrl}?action=marcar_lidas&id_usuario=${idUsuario}`, { method: 'POST' });
        } catch { /* silencioso */ }
    }

    async deletarConversa() {
        if (!confirm('Deletar esta conversa? As mensagens serão apagadas para os dois lados.')) return;

        try {
            await fetch(`${this.apiUrl}?action=deletar&id_usuario=${this.idDestinatario}`, { method: 'POST' });
        } catch { /* silencioso */ }

        this.pararPolling();
        this.idDestinatario = null;
        this.mensagensContainer.classList.add('sem-conversa');
        this.mensagensHeader.innerHTML = '<span id="chat-contato-nome">Selecione uma conversa</span>';
        this.mensagensLista.innerHTML = '<p class="chat-placeholder">Selecione uma conversa para começar.</p>';
        document.getElementById('chat-contexto-troca')?.remove();
        await this.carregarConversas();
    }

    // ── Polling ──────────────────────────────────────────────────────────────

    iniciarPolling() {
        this.pollingInterval = setInterval(() => this.carregarMensagens(), 3000);
    }

    pararPolling() {
        clearInterval(this.pollingInterval);
        this.pollingInterval = null;
    }

    // ── Utilitários ──────────────────────────────────────────────────────────

    escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    truncar(str, max) {
        return str.length > max ? str.slice(0, max) + '…' : str;
    }

    formatarHora(dt) {
        if (!dt) return '';
        const d = new Date(dt.replace(' ', 'T'));
        return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }
}