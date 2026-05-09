<?php
if (!verificaLogin()) return;

$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $protocolo . '://' . $_SERVER['HTTP_HOST'];
$raizFisica = realpath($_SERVER['DOCUMENT_ROOT']);
$apiPath = realpath(__DIR__ . '/../../api/mensagens.php');
$apiUrl = $host . str_replace('\\', '/', str_replace($raizFisica, '', $apiPath));
?>

<div id="chat-widget" data-api-url="<?= htmlspecialchars($apiUrl) ?>">

    <!-- Botão fixo — badge "!" aparece quando há mensagens não lidas -->
    <button id="chat-toggle" title="Chat" aria-label="Abrir chat">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" fill="currentColor">
            <path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z" />
        </svg>
        <span id="chat-badge" class="chat-oculto">!</span>
    </button>

    <!-- Painel do chat -->
    <div id="chat-panel" class="chat-oculto">

        <div id="chat-header">
            <span>Chat</span>
            <button id="chat-minimizar" title="Minimizar" aria-label="Minimizar chat">─</button>
        </div>

        <div id="chat-corpo">

            <div id="chat-conversas">
                <div id="chat-conversas-lista">
                    <p class="chat-placeholder">Você ainda não iniciou uma conversa.</p>
                </div>
            </div>

            <div id="chat-mensagens-container">
                <div id="chat-mensagens-header">
                    <span id="chat-contato-nome">Selecione uma conversa</span>
                </div>

                <div id="chat-mensagens-lista">
                    <p class="chat-placeholder">Selecione uma conversa para começar.</p>
                </div>

                <div id="chat-input-area">
                    <div id="chat-emoji-picker" class="chat-oculto">
                        <?php
                        $emojis = [
                            '😊',
                            '😂',
                            '❤️',
                            '👍',
                            '🙏',
                            '😢',
                            '🔥',
                            '✨',
                            '🎉',
                            '😍',
                            '🤔',
                            '😅',
                            '😎',
                            '🥰',
                            '😤',
                            '🤣',
                            '💯',
                            '👏',
                            '🙌',
                            '😭',
                            '📚',
                            '📖',
                            '✍️',
                            '🖊️',
                            '💬',
                            '🗣️',
                            '👀',
                            '💡',
                            '⭐',
                            '🌟'
                        ];
                        foreach ($emojis as $emoji): ?>
                            <span class="chat-emoji" data-emoji="<?= $emoji ?>"><?= $emoji ?></span>
                        <?php endforeach; ?>
                    </div>

                    <button id="chat-emoji-btn" title="Emojis" aria-label="Inserir emoji">😊</button>
                    <input type="text" id="chat-input" placeholder="Digite uma mensagem..." autocomplete="off"
                        maxlength="500">
                    <button id="chat-enviar" title="Enviar" aria-label="Enviar mensagem">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" fill="currentColor">
                            <path d="M2 21l21-9L2 3v7l15 2-15 2z" />
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>