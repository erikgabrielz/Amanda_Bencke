<?php

// Ativa a exibição de erros para depuração (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica se o método da requisição é POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit(json_encode(["error" => "Método não permitido"]));
}

// // Verifica se a requisição é AJAX
// if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
//     http_response_code(403);
//     exit(json_encode(["error" => "Acesso negado."]));
// }

// Verifica se a requisição veio do seu site
$allowed_host = "http://localhost:8080"; // Substitua pelo seu domínio
$referer = $_SERVER['HTTP_REFERER'] ?? '';

if (!str_starts_with($referer, $allowed_host)) {
    http_response_code(403);
    exit(json_encode(["error" => "Acesso negado."]));
}

// // Valida o token secreto
// $token_secreto = "SEU_TOKEN_SECRETO"; // Substitua pelo seu token secreto
// $token_recebido = $_POST['token'] ?? '';

// if ($token_recebido !== $token_secreto) {
//     http_response_code(403);
//     exit(json_encode(["error" => "Token inválido."]));
// }

// Captura os dados do formulário
$name = htmlspecialchars($_POST['name'] ?? '');
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$subject = htmlspecialchars($_POST['subject'] ?? 'Contato via formulário do site Amanda Bencke');
$message = htmlspecialchars($_POST['message'] ?? '');

if (!$email || strlen($message) < 2) {
    http_response_code(400);
    exit(json_encode(["error" => "O e-mail deve ser válido e a mensagem deve ter mais de 1 caractere."]));
}

// Configurações do e-mail
$to = "email@dominio.com"; //email que será encaminhado os dados preenchidos no formulários 
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$body = "Nome: $name\n";
$body .= "E-mail: $email\n";
$body .= "Assunto: $subject\n\n";
$body .= "Mensagem:\n$message\n";

// Envio do e-mail
if (mail($to, $subject, $body, $headers)) {
    header('Content-Type: application/json');
    echo json_encode(["message" => "Sua mensagem foi enviada com sucesso!"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao enviar mensagem. Tente novamente."]);
}