<?php
// Formulari de contacte - SD Espanyol Futbol Sala
header("Content-Type: application/json; charset=UTF-8");

const CLUB_TO   = "info@espanyolfutsal.es";
const CLUB_FROM = "web@espanyolfutsal.es"; // ha d'existir com a compte al hosting

function field($k) { return isset($_POST[$k]) ? trim(strip_tags($_POST[$k])) : ""; }
function clean_header($v) { return str_replace(["\r", "\n"], "", $v); }
function enc($s) { return "=?UTF-8?B?" . base64_encode($s) . "?="; }

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Mètode no permès."]);
    exit;
}

// Anti-spam (camp ocult)
if (field("website") !== "") {
    echo json_encode(["status" => "success"]);
    exit;
}

$name    = field("name");
$surname = field("surname");
$email   = clean_header(filter_var(field("email"), FILTER_SANITIZE_EMAIL));
$subject = field("subject");
$message = field("message");
$privacy = isset($_POST["privacy"]);

if ($name === "" || $surname === "" || $message === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$privacy) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Dades incompletes o incorrectes."]);
    exit;
}

$body  = "Nou missatge des del formulari de contacte de la web:\n\n";
$body .= "Nom: $name $surname\n";
$body .= "E-mail: $email\n";
$body .= "Tema: $subject\n\n";
$body .= "Missatge:\n$message\n";

$headers  = "From: " . enc("Web Espanyol Futsal") . " <" . CLUB_FROM . ">\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";

$ok = mail(CLUB_TO, enc("Contacte web: $subject - $name $surname"), $body, $headers, "-f" . CLUB_FROM);

if ($ok) {
    echo json_encode(["status" => "success"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error del servidor de correu."]);
}
