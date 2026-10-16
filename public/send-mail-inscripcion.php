<?php
// send-mail-inscripcion.php
// Script to handle REGISTRATION form submission

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect Input Data
    $playerName = strip_tags(trim($_POST["player-name"]));
    $playerSurname = strip_tags(trim($_POST["player-surname"]));
    $birthDate = strip_tags(trim($_POST["birth-date"]));
    $gender = strip_tags(trim($_POST["gender"]));
    
    $guardianName = strip_tags(trim($_POST["guardian-name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"]));
    
    $activityType = strip_tags(trim($_POST["activity-type"]));
    $sport = strip_tags(trim($_POST["sport"]));
    $category = strip_tags(trim($_POST["category"]));
    $previousClub = strip_tags(trim($_POST["previous-club"]));
    
    $privacy = isset($_POST["acceptance-terms"]);

    // Validation
    if (empty($playerName) || empty($guardianName) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$privacy) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Dades del formulari incorrectes o incomples."]);
        exit;
    }

    // Email Configuration
    $to = "info@clubesportiuacademia.es";
    $subject = "Nova Inscripció: $playerName $playerSurname - $sport";
    
    // Admin Email Body
    $email_content = "Nova sol·licitud d'inscripció rebuda:\n\n";
    $email_content .= "--- DADES DEL JUGADOR/A ---\n";
    $email_content .= "Nom: $playerName\n";
    $email_content .= "Cognoms: $playerSurname\n";
    $email_content .= "Data Naixement: $birthDate\n";
    $email_content .= "Gènere: $gender\n\n";
    
    $email_content .= "--- DADES DE CONTACTE ---\n";
    $email_content .= "Tutor/a: $guardianName\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Telèfon: $phone\n\n";
    
    $email_content .= "--- ACTIVITAT ---\n";
    $email_content .= "Tipus: $activityType\n";
    $email_content .= "Esport: $sport\n";
    $email_content .= "Categoria: $category\n";
    $email_content .= "Club Anterior: $previousClub\n";

    // Headers Admin
    $headers = "From: Web Inscripcions <wordpress@clubesportiuacademia.es>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send Admin Email
    $adminSent = mail($to, $subject, $email_content, $headers);

    // User Confirmation Email
    $userSubject = "Sol·licitud d'inscripció rebuda - Club Esportiu Acadèmia";
    $userMessage = "Hola $guardianName,\n\n";
    $userMessage .= "Hem rebut correctament la sol·licitud d'inscripció per a $playerName ($sport).\n";
    $userMessage .= "L'equip tècnic revisarà les dades i ens posarem en contacte amb tu properament per confirmar la plaça i els horaris.\n\n";
    $userMessage .= "Gràcies per confiar en el Club Esportiu Acadèmia Barcelona.\n\n";
    
    $userHeaders = "From: Club Esportiu Acadèmia <info@clubesportiuacademia.es>\r\n";
    $userHeaders .= "X-Mailer: PHP/" . phpversion();

    $userSent = mail($email, $userSubject, $userMessage, $userHeaders);

    if ($adminSent) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Sol·licitud enviada correctament."]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error al servidor de correu."]);
    }

} else {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Accés denegat."]);
}
?>
