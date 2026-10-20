<?php
// Formulari "Vols jugar amb nosaltres?" - SD Espanyol Futbol Sala
header("Content-Type: application/json; charset=UTF-8");

const CLUB_TO   = "info@espanyolfutsal.es"; // pots afegir: ", coordinacio@espanyolfutsal.es"
const CLUB_FROM = "web@espanyolfutsal.es";  // ha d'existir com a compte al hosting

function field($k) { return isset($_POST[$k]) ? trim(strip_tags($_POST[$k])) : ""; }
function clean_header($v) { return str_replace(["\r", "\n"], "", $v); }
function enc($s) { return "=?UTF-8?B?" . base64_encode($s) . "?="; }

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Mètode no permès."]);
    exit;
}

if (field("website") !== "") { // anti-spam
    echo json_encode(["status" => "success"]);
    exit;
}

$playerName    = field("player-name");
$playerSurname = field("player-surname");
$birthDate     = field("birth-date");
$gender        = field("gender");
$guardianName  = field("guardian-name");
$email         = clean_header(filter_var(field("email"), FILTER_SANITIZE_EMAIL));
$phone         = field("phone");
$activityType  = field("activity-type");
$sport         = field("sport");
$category      = field("category");
$previousClub  = field("previous-club");
$privacy       = isset($_POST["acceptance-terms"]);

if ($playerName === "" || $playerSurname === "" || $phone === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$privacy) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Dades incompletes o incorrectes."]);
    exit;
}

$body  = "Nova sol·licitud d'informació / inscripció 2026-27\n\n";
$body .= "--- JUGADOR/A ---\n";
$body .= "Nom: $playerName\nCognoms: $playerSurname\nData de naixement: $birthDate\nGènere: $gender\n\n";
$body .= "--- CONTACTE ---\n";
$body .= "Pare, mare o tutor/a: $guardianName\nE-mail: $email\nTelèfon: $phone\n\n";
$body .= "--- ACTIVITAT ---\n";
$body .= "Tipus: $activityType\nEsport: $sport\nCategoria: $category\nClub anterior: $previousClub\n";

$headers  = "From: " . enc("Web Espanyol Futsal") . " <" . CLUB_FROM . ">\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";

$ok = mail(CLUB_TO, enc("Inscripció: $playerName $playerSurname - $category ($activityType)"), $body, $headers, "-f" . CLUB_FROM);

// Confirmació a la família
$who = $guardianName !== "" ? $guardianName : $playerName;
$conf  = "Hola $who,\n\n";
$conf .= "Hem rebut correctament la sol·licitud per a $playerName $playerSurname ($category).\n";
$conf .= "L'equip de coordinació revisarà les dades i us contactarem ben aviat.\n\n";
$conf .= "Gràcies per confiar en el SD Espanyol Futbol Sala.\n";
$conf .= "Més que un equip, un sentiment perico també a la pista.\n\n";
$conf .= "SD Espanyol Futbol Sala · +34 640 066 023 · info@espanyolfutsal.es\n";
$confHeaders  = "From: " . enc("SD Espanyol Futbol Sala") . " <" . CLUB_FROM . ">\r\n";
$confHeaders .= "Reply-To: " . CLUB_TO . "\r\n";
$confHeaders .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
@mail($email, enc("Hem rebut la teva sol·licitud - SD Espanyol Futbol Sala"), $conf, $confHeaders, "-f" . CLUB_FROM);

if ($ok) {
    echo json_encode(["status" => "success"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error del servidor de correu."]);
}
