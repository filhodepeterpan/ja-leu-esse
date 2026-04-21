/**
 * script.js — Ponto de entrada da aplicação
 *
 * USUARIO_LOGADO e MOCK_MENSAGENS são injetados pelo PHP no chat.php
 * antes deste script ser carregado.
 */

document.addEventListener('DOMContentLoaded', () => {

  const chat = new Chat({
    usuarioLogado    : USUARIO_LOGADO,
    mensagensIniciais: MOCK_MENSAGENS,   // remova quando a API real estiver pronta
  });

  chat.init();

  // Quando integrar polling, descomente:
  // chat.iniciarPolling(5000);

});
