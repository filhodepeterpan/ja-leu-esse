<?php
/**
 * partials/scripts.php
 * Incluído no final do chat.php.
 * Injeta os dados do PHP como variáveis JS e carrega Chat.js + script.js.
 *
 * $usuarioLogado e as funções mock_* já foram definidos antes deste include.
 */
?>

<!-- Dados do PHP → JS -->
<script>
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

<!-- Classes e lógica -->
<script src="../../js/Chat.js"></script>
<script src="../../js/script.js"></script>
