<?php
// Busca todos os livros cadastrados na plataforma e monta o array $stock_photos.
// Páginas que incluem este arquivo devem ter session_start(),
// env.php e functions.php carregados antes.

$todosLivros = buscarTodosLivros();

$stock_photos = array_map(fn($livro) => [
    'id' => (int) $livro['id_livro'],
    'id_usuario' => (int) $livro['id_usuario'],
    'nome' => $livro['nm_livro'],
    'url' => $livro['img_livro'] ? "../../{$livro['img_livro']}" : '../../assets/img/capa_padrao.png',
    'alt' => "Capa do livro " . $livro['nm_livro'],
    'genero' => $livro['nm_genero_literario'] ?? '',
    'descricao' => $livro['ds_livro'] ?? '',
    'status' => 'active',
], $todosLivros);