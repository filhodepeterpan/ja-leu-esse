/**
 * Chat.js — Classe principal do widget de chat
 *
 * Uso (em script.js):
 *   const chat = new Chat({
 *     usuarioLogado: USUARIO_LOGADO,
 *     mensagensIniciais: MOCK_MENSAGENS,   // remova quando integrar a API real
 *   });
 *   chat.init();
 */

class Chat {

  // ─────────────────────────────────────────────
  // CONSTRUTOR
  // ─────────────────────────────────────────────
  constructor({ usuarioLogado, mensagensIniciais = {} }) {
    this.usuario         = usuarioLogado;   // { id_usuario, nm_usuario, img_icone_perfil }
    this.aberto          = false;
    this.conversaAtiva   = null;            // id_usuario do contato aberto
    this.mensagensCache  = mensagensIniciais; // { id_usuario: [ {…} ] }

    // Referências aos elementos do DOM (preenchidas em init())
    this.el = {};
  }

  // ─────────────────────────────────────────────
  // INICIALIZAÇÃO
  // ─────────────────────────────────────────────
  init() {
    this.el = {
      widget        : document.getElementById('chat-widget'),
      fab           : document.getElementById('chat-fab'),
      badge         : document.getElementById('chat-badge'),
      viewLista     : document.getElementById('view-lista'),
      viewMensagens : document.getElementById('view-mensagens'),
      lista         : document.getElementById('conversations-list'),
      searchInput   : document.getElementById('search-input'),
      messagesArea  : document.getElementById('messages-area'),
      typingIndicator: document.getElementById('typing-indicator'),
      headerNome    : document.getElementById('header-nome'),
      headerAvatar  : document.getElementById('header-avatar'),
      messageInput  : document.getElementById('message-input'),
      btnSend       : document.getElementById('btn-send'),
    };

    this._bindEvents();
    this._inicializarBadge();
  }

  // ─────────────────────────────────────────────
  // BIND DE EVENTOS GLOBAIS
  // ─────────────────────────────────────────────
  _bindEvents() {
    // FAB
    this.el.fab.addEventListener('click', () => this.toggle());

    // Busca
    this.el.searchInput.addEventListener('input', e => this._filtrarConversas(e.target.value));

    // Textarea
    this.el.messageInput.addEventListener('input',   e => this._onInputChange(e.target));
    this.el.messageInput.addEventListener('keydown', e => this._onKeyDown(e));

    // Botão enviar
    this.el.btnSend.addEventListener('click', () => this.enviarMensagem());

    // Botões de navegação (delegados, pois estão dentro do widget)
    this.el.widget.addEventListener('click', e => {
      const btnVoltar = e.target.closest('[data-acao="voltar"]');
      const btnFechar = e.target.closest('[data-acao="fechar"]');
      const convItem  = e.target.closest('.conv-item');

      if (btnFechar) this.toggle();
      if (btnVoltar) this.voltarLista();
      if (convItem  && !btnFechar && !btnVoltar) {
        const { id, nome, avatar } = convItem.dataset;
        this.abrirConversa(Number(id), nome, avatar);
      }
    });
  }

  // ─────────────────────────────────────────────
  // TOGGLE WIDGET
  // ─────────────────────────────────────────────
  toggle() {
    this.aberto = !this.aberto;
    this.el.widget.classList.toggle('open', this.aberto);
    if (this.aberto) this._atualizarBadge();
  }

  // ─────────────────────────────────────────────
  // NAVEGAÇÃO DE VIEWS
  // ─────────────────────────────────────────────
  abrirConversa(idContato, nome, avatar) {
    this.conversaAtiva = idContato;

    // Atualiza header
    this.el.headerNome.textContent = nome;
    this._renderizarAvatar(this.el.headerAvatar, avatar, nome, { width: '36px', height: '36px', fontSize: '11px' });

    // Marca ativo
    this.el.lista.querySelectorAll('.conv-item').forEach(el => el.classList.remove('active'));
    document.getElementById(`conv-${idContato}`)?.classList.add('active');

    // Slide para view de mensagens
    this.el.viewLista.classList.add('hidden-left');
    this.el.viewMensagens.classList.remove('hidden-right', 'hidden-left');

    this._carregarMensagens(idContato);
  }

  voltarLista() {
    this.conversaAtiva = null;
    this.el.viewLista.classList.remove('hidden-left');
    this.el.viewMensagens.classList.add('hidden-right');
    this.el.viewMensagens.classList.remove('hidden-left');
  }

  // ─────────────────────────────────────────────
  // CARREGAR MENSAGENS
  // ─────────────────────────────────────────────
  async _carregarMensagens(idContato) {
    this.el.messagesArea.innerHTML = '';

    if (!this.mensagensCache[idContato]) {
      // ── Integração real: descomente e ajuste a URL ──
      // try {
      //   const r = await fetch(`/api/mensagens.php?contato=${idContato}`);
      //   this.mensagensCache[idContato] = await r.json();
      // } catch (e) {
      //   this.mensagensCache[idContato] = [];
      // }

      // Mock: nenhuma mensagem para contatos sem dados pré-carregados
      this.mensagensCache[idContato] = [];
    }

    this._renderizarMensagens(this.mensagensCache[idContato]);
  }

  // ─────────────────────────────────────────────
  // RENDERIZAR LISTA COMPLETA DE MENSAGENS
  // ─────────────────────────────────────────────
  _renderizarMensagens(msgs) {
    const area = this.el.messagesArea;
    area.innerHTML = '';

    if (!msgs.length) {
      area.appendChild(this._criarEstadoVazio());
      return;
    }

    let ultimaData = null;

    msgs.forEach((msg, idx) => {
      const ehMeu   = msg.id_remetente === this.usuario.id_usuario;
      const dt      = new Date(msg.dt_envio);
      const dataStr = dt.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' });
      const hora    = dt.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

      // Separador de data
      if (dataStr !== ultimaData) {
        ultimaData = dataStr;
        area.appendChild(this._criarSeparadorData(dataStr));
      }

      const proxMesmo = msgs[idx + 1]?.id_remetente === msg.id_remetente;
      area.appendChild(this._criarBolha(msg, ehMeu, hora, proxMesmo));
    });

    area.scrollTop = area.scrollHeight;
  }

  // ─────────────────────────────────────────────
  // ENVIAR MENSAGEM
  // ─────────────────────────────────────────────
  async enviarMensagem() {
    const input = this.el.messageInput;
    const texto = input.value.trim();
    if (!texto || !this.conversaAtiva) return;

    input.value = '';
    this._autoResize(input);
    this.el.btnSend.classList.remove('ready');

    const nova = {
      id_mensagem    : Date.now(),
      id_remetente   : this.usuario.id_usuario,
      id_destinatario: this.conversaAtiva,
      ds_mensagem    : texto,
      dt_envio       : new Date().toISOString().replace('T', ' ').slice(0, 19),
    };

    if (!this.mensagensCache[this.conversaAtiva]) this.mensagensCache[this.conversaAtiva] = [];
    this.mensagensCache[this.conversaAtiva].push(nova);

    this._adicionarBolha(nova);
    this._atualizarPreviewLista(this.conversaAtiva, texto);

    // ── Integração real: descomente e ajuste ──
    // try {
    //   const resp = await fetch('/api/enviar_mensagem.php', {
    //     method : 'POST',
    //     headers: { 'Content-Type': 'application/json' },
    //     body   : JSON.stringify({ id_destinatario: this.conversaAtiva, ds_mensagem: texto }),
    //   });
    //   const data = await resp.json();
    //   // Atualize o id_mensagem real se necessário
    // } catch (e) {
    //   console.error('Erro ao enviar mensagem:', e);
    //   // Marque a bolha com erro visual aqui se quiser
    // }
  }

  // ─────────────────────────────────────────────
  // FÁBRICA DE ELEMENTOS DOM
  // ─────────────────────────────────────────────
  _criarBolha(msg, ehMeu, hora, proxMesmo) {
    const row = document.createElement('div');
    row.className = `msg-row ${ehMeu ? 'me' : 'them'}`;
    row.dataset.id = msg.id_mensagem;

    // Avatar
    const avatarDiv = document.createElement('div');
    avatarDiv.className = `msg-avatar${proxMesmo ? ' invisible' : ''}`;

    if (!ehMeu) {
      const contaEl   = document.getElementById(`conv-${this.conversaAtiva}`);
      const avatarSrc = contaEl?.dataset.avatar;
      const nomeCont  = contaEl?.dataset.nome ?? '?';

      if (avatarSrc) {
        const img = document.createElement('img');
        img.src     = avatarSrc;
        img.onerror = () => { avatarDiv.innerHTML = ''; avatarDiv.textContent = this._iniciais(nomeCont); };
        avatarDiv.appendChild(img);
      } else {
        avatarDiv.textContent = this._iniciais(nomeCont);
      }
    }

    // Wrapper da bolha
    const wrap = document.createElement('div');
    wrap.style.cssText = `display:flex;flex-direction:column;align-items:${ehMeu ? 'flex-end' : 'flex-start'}`;

    const bubble = document.createElement('div');
    bubble.className   = 'bubble';
    bubble.textContent = msg.ds_mensagem;

    const time = document.createElement('div');
    time.className   = 'bubble-time';
    time.textContent = hora + (ehMeu ? ' ✓' : '');

    wrap.appendChild(bubble);
    wrap.appendChild(time);

    if (ehMeu) {
      row.appendChild(wrap);
      row.appendChild(avatarDiv);
    } else {
      row.appendChild(avatarDiv);
      row.appendChild(wrap);
    }

    return row;
  }

  _criarSeparadorData(dataStr) {
    const sep = document.createElement('div');
    sep.className   = 'date-sep';
    sep.textContent = dataStr;
    return sep;
  }

  _criarEstadoVazio() {
    const div = document.createElement('div');
    div.className = 'empty-chat';
    div.innerHTML = `
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      Nenhuma mensagem ainda.<br>Diga olá!`;
    return div;
  }

  // Adiciona uma única bolha nova sem re-renderizar tudo
  _adicionarBolha(msg) {
    const area  = this.el.messagesArea;
    const empty = area.querySelector('.empty-chat');
    if (empty) empty.remove();

    const ehMeu = msg.id_remetente === this.usuario.id_usuario;
    const hora  = new Date(msg.dt_envio).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

    area.appendChild(this._criarBolha(msg, ehMeu, hora, false));
    area.scrollTop = area.scrollHeight;
  }

  // ─────────────────────────────────────────────
  // ATUALIZAR PREVIEW DA LISTA
  // ─────────────────────────────────────────────
  _atualizarPreviewLista(idContato, texto) {
    const item = document.getElementById(`conv-${idContato}`);
    if (!item) return;
    const el = item.querySelector('.conv-last');
    const tm = item.querySelector('.conv-time');
    if (el) el.textContent = texto;
    if (tm) tm.textContent = 'agora';
    this.el.lista.prepend(item);
  }

  // ─────────────────────────────────────────────
  // BADGE DO FAB
  // ─────────────────────────────────────────────
  _inicializarBadge() {
    const total = Number(this.el.badge.textContent.trim()) || 0;
    if (total > 0) {
      this.el.badge.textContent = total > 9 ? '9+' : total;
      this.el.badge.classList.add('visible');
    }
  }

  _atualizarBadge(total) {
    if (total === undefined) {
      this.el.badge.classList.remove('visible');
      return;
    }
    this.el.badge.textContent = total > 9 ? '9+' : total;
    this.el.badge.classList.toggle('visible', total > 0);
  }

  // ─────────────────────────────────────────────
  // BUSCA DE CONVERSAS
  // ─────────────────────────────────────────────
  _filtrarConversas(q) {
    const termo = q.toLowerCase();
    this.el.lista.querySelectorAll('.conv-item').forEach(item => {
      const nome = item.dataset.nome?.toLowerCase() ?? '';
      item.style.display = nome.includes(termo) ? '' : 'none';
    });
  }

  // ─────────────────────────────────────────────
  // AVATAR HELPER
  // ─────────────────────────────────────────────
  _renderizarAvatar(container, src, nome, estilos = {}) {
    container.innerHTML = '';
    Object.assign(container.style, estilos);

    if (src) {
      const img   = document.createElement('img');
      img.src     = src;
      img.alt     = nome;
      img.onerror = () => { container.innerHTML = ''; container.textContent = this._iniciais(nome); };
      container.appendChild(img);
    } else {
      container.textContent = this._iniciais(nome);
    }
  }

  // ─────────────────────────────────────────────
  // INPUT HANDLERS
  // ─────────────────────────────────────────────
  _onInputChange(el) {
    this._autoResize(el);
    this.el.btnSend.classList.toggle('ready', el.value.trim().length > 0);
  }

  _onKeyDown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      this.enviarMensagem();
    }
  }

  _autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
  }

  // ─────────────────────────────────────────────
  // UTILS
  // ─────────────────────────────────────────────
  _iniciais(nome) {
    const p = nome.trim().split(' ');
    let s = p[0][0].toUpperCase();
    if (p[1]) s += p[1][0].toUpperCase();
    return s;
  }

  // ─────────────────────────────────────────────
  // POLLING (opcional — substitua por WebSocket)
  // ─────────────────────────────────────────────
  // iniciarPolling(intervaloMs = 5000) {
  //   setInterval(async () => {
  //     if (!this.aberto || !this.conversaAtiva) return;
  //     const ultimoId = this.mensagensCache[this.conversaAtiva]?.at(-1)?.id_mensagem ?? 0;
  //     const r = await fetch(`/api/mensagens.php?contato=${this.conversaAtiva}&depois=${ultimoId}`);
  //     const novas = await r.json();
  //     novas.forEach(m => {
  //       this.mensagensCache[this.conversaAtiva].push(m);
  //       this._adicionarBolha(m);
  //     });
  //   }, intervaloMs);
  // }
}
