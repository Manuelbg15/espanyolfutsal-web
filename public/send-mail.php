<?php
// send-mail.php
// Script to handle form submission from Astro Contact Form

// Set headers to allow cross-origin requests from the same domain (adjusted for security)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"]));
    $subjectOption = strip_tags(trim($_POST["subject"]));
    $message = strip_tags(trim($_POST["message"]));
    $privacy = isset($_POST["privacy"]);

    // Validation
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$privacy) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Dades del formulari incorrectes o incomples."]);
        exit;
    }

    // Email Configuration
    $to = "info@clubesportiuacademia.es"; // ADMIN EMAIL
    $subject = "Nou Contacte Web: $subjectOption - $name";
    
    // Email Body for Admin
    $email_content = "Nou missatge rebut des del formulari web:\n\n";
    $email_content .= "Nom: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Telèfon: $phone\n";
    $email_content .= "Assumpte: $subjectOption\n\n";
    $email_content .= "Missatge:\n$message\n";

    // Email Headers for Admin
    $headers = "From: Web Club Esportiu <wordpress@clubesportiuacademia.es>\r\n"; // Use a valid sender from your hosting
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send Email to Admin
    $adminSent = mail($to, $subject, $email_content, $headers);

    // Confirmation Email to User
    $userSubject = "Hem rebut la teva sol·licitud - Club Esportiu Acadèmia";
    $userMessage = "Hola $name,\n\n";
    $userMessage .= "Hem rebut correctament el teu missatge sobre '$subjectOption'.\n";
    $userMessage .= "Ens posarem en contacte amb tu en un termini aproximat de 48 hores.\n\n";
    $userMessage .= "Gràcies per contactar amb nosaltres.\n\n";
    $userMessage .= "Atentament,\nClub Esportiu Acadèmia Barcelona";

    $userHeaders = "From: Club Esportiu Acadèmia <info@clubesportiuacademia.es>\r\n";
    $userHeaders .= "X-Mailer: PHP/" . phpversion();

    $userSent = mail($email, $userSubject, $userMessage, $userHeaders);

    if ($adminSent) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Missatge enviat correctament."]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error al servidor de correu."]);
    }

} else {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Accés denegat."]);
}
?>
