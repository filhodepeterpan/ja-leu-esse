<?php
$atributos = [
    1 => [
        "id" => "nm_usuario",
        "nome" => "Nome *",
        "tipo" => "text",
        "constraints" => "required"
    ],
    2 => [
        "id" => "nm_email",
        "nome" => "Email *",
        "tipo" => "email",
        "constraints" => "required"
    ],
    3 => [
        "id" => "cd_senha",
        "nome" => "Senha *",
        "tipo" => "password",
        "constraints" => "required"
    ],
    4 => [
        "id" => "cd_confirmacao_senha",
        "nome" => "Confirme sua senha *",
        "tipo" => "password",
        "constraints" => "required"
    ],
    5 => [
        "id" => "cd_telefone",
        "nome" => "Celular",
        "tipo" => "tel",
        "constraints" => "pattern='\(\d{2}\) \d{5}-\d{4}' title='Digite um número de celular no formato (99) 99999-9999'"
    ],
    6 => [
        "id" => "sg_genero",
        "nome" => "Gênero",
        "tipo" => "radio",
        "options" => [
            "Feminino" => "F",
            "Masculino" => "M",
            "Não-binário" => "NB",
            "Prefiro não informar" => "NA"
        ],
        "constraints" => "required maxlength='2' placeholder='F, M, NB ou NA'"
    ],
    7 => [
        "id" => "cd_cep",
        "nome" => "CEP *",
        "tipo" => "text",
        "constraints" => "required pattern='\d{5}-\d{3}' title='Digite um CEP no formato 12345-678'"
    ],
    8 => [
        "id" => "sg_uf",
        "nome" => "Estado",
        "tipo" => "text",
        "constraints" => "readonly maxlength='2'"
    ],
    9 => [
        "id" => "nm_cidade",
        "nome" => "Cidade",
        "tipo" => "text",
        "constraints" => "readonly"
    ],
    10 => [
        "id" => "nm_bairro",
        "nome" => "Bairro",
        "tipo" => "text",
        "constraints" => "readonly"
    ],
    11 => [
        "id" => "nm_logradouro",
        "nome" => "Logradouro",
        "tipo" => "text",
        "constraints" => "readonly"
    ],
    12 => [
        "id" => "cd_numero",
        "nome" => "Número *",
        "tipo" => "number",
        "constraints" => "required min='1' max='99999999'"
    ],
    13 => [
        "id" => "ds_complemento",
        "nome" => "Complemento",
        "tipo" => "text",
        "constraints" => "maxlength='60'"
    ],
    14 => [
        "id" => "nm_genero_literario_favorito",
        "nome" => "Se quiser, nos conte qual é o seu gênero literário favorito",
        "tipo" => "text",
        "constraints" => "maxlength='60'"
    ]
];
?>