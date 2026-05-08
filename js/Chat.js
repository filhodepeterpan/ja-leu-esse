export class Chat {
    constructor() {
        this.widget = document.getElementById('chat-widget');
        this.panel = document.getElementById('chat-panel');
        this.toggle = document.getElementById('chat-toggle');
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
        this._idDestinatarioTroca = null;
    }

    init() {
        if (!this.widget) return;

        // ── Estado inicial: esconde área de input até ter conversa selecionada
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

        // ── Integração com trocas ────────────────────────────────────────────
        // CAPTURE PHASE: garante que capturamos o id antes do Trocas.js processar
        document.querySelectorAll('.card-livro').forEach(card => {
            card.addEventListener('click', () => {
                const id = parseInt(card.dataset.idUsuario);
                if (id) this._idDestinatarioTroca = id;
            }, true); // capture = true → roda antes do bubble do Trocas.js
        });

        // CAPTURE PHASE + stopImmediatePropagation: impede o Trocas.js de redirecionar
        document.getElementById('btnNegociar')?.addEventListener('click', (e) => {
            e.stopImmediatePropagation();
            e.preventDefault();

            if (!this._idDestinatarioTroca) return;

            const ofertaImg = document.getElementById('slotOfertaImg')?.src ?? '';
            const ofertaNome = document.getElementById('slotOfertaNome')?.textContent ?? '';
            const desejoImg = document.getElementById('slotDesejoImg')?.src ?? '';
            const desejoNome = document.getElementById('slotDesejoNome')?.textContent ?? '';

            document.getElementById('modalTroca')?.classList.add('hidden');

            this.abrirChat();
            this.selecionarConversa(
                this._idDestinatarioTroca,
                { ofertaImg, ofertaNome, desejoImg, desejoNome }
            );
        }, true); // capture = true
    }

    // ── Painel ───────────────────────────────────────────────────────────────

    abrirChat() {
        this.panel.classList.remove('chat-oculto');
        this.widget.classList.add('chat-aberto');   // esconde o botão toggle via CSS
        this.carregarConversas();
    }

    fecharChat() {
        this.panel.classList.add('chat-oculto');
        this.widget.classList.remove('chat-aberto'); // mostra o botão toggle novamente
        this.pararPolling();
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
                    <img class="chat-contato-foto"
                         src="${foto}"
                         alt="${this.escapeHtml(c.nm_usuario)}">
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

    selecionarConversa(idUsuario, contextoTroca = null) {
        this.idDestinatario = idUsuario;
        this.pararPolling();

        // Libera a área de input
        this.mensagensContainer?.classList.remove('sem-conversa');

        // Marca contato ativo na lista
        document.querySelectorAll('.chat-contato').forEach(el => {
            el.classList.toggle('ativo', parseInt(el.dataset.id) === idUsuario);
        });

        this.atualizarContextoTroca(contextoTroca);
        this.carregarMensagens();
        this.iniciarPolling();
        this.input.focus();
    }

    atualizarContextoTroca(ctx) {
        document.getElementById('chat-contexto-troca')?.remove();
        if (!ctx || (!ctx.ofertaNome && !ctx.desejoNome)) return;

        const div = document.createElement('div');
        div.id = 'chat-contexto-troca';
        div.innerHTML = `
            <img src="${this.escapeHtml(ctx.ofertaImg)}"
                 alt="${this.escapeHtml(ctx.ofertaNome)}">
            <span>${this.escapeHtml(ctx.ofertaNome)}</span>
            <span>⇄</span>
            <img src="${this.escapeHtml(ctx.desejoImg)}"
                 alt="${this.escapeHtml(ctx.desejoNome)}">
            <span>${this.escapeHtml(ctx.desejoNome)}</span>`;

        this.mensagensLista.parentNode.insertBefore(div, this.mensagensLista);
    }

    async carregarMensagens() {
        if (!this.idDestinatario) return;

        try {
            const res = await fetch(
                `${this.apiUrl}?action=mensagens&id_usuario=${this.idDestinatario}`
            );
            const data = await res.json();
            this.renderMensagens(data);
        } catch {
            /* falha silenciosa no polling */
        }
    }

    renderMensagens(data) {
        if (!data || data.error) return;

        const { meuId, usuario, mensagens } = data;

        // Atualiza header com foto e nome do contato
        if (usuario) {
            const foto = usuario.img_icone_perfil
                ? `../../${usuario.img_icone_perfil}`
                : '../../assets/img/avatar_padrao.png';
            this.mensagensHeader.innerHTML = `
                <img src="${foto}" alt="${this.escapeHtml(usuario.nm_usuario)}">
                <span id="chat-contato-nome">${this.escapeHtml(usuario.nm_usuario)}</span>`;
        }

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

        if (atBottom) {
            this.mensagensLista.scrollTop = this.mensagensLista.scrollHeight;
        }
    }

    async enviarMensagem() {
        const texto = this.input.value.trim();
        if (!texto || !this.idDestinatario) return;

        this.input.value = '';

        try {
            await fetch(`${this.apiUrl}?action=enviar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_destinatario: this.idDestinatario,
                    ds_mensagem: texto,
                }),
            });
            await this.carregarMensagens();
            await this.carregarConversas();
        } catch {
            this.input.value = texto; // devolve o texto em caso de erro
        }
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
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
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