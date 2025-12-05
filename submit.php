<?php
header("Content-Type: text/html; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

function clean($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$name   = trim($_POST["name"] ?? "");
$email  = trim($_POST["email"] ?? "");
$phone  = trim($_POST["phone"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$course = trim($_POST["course"] ?? "");

$errors = [];

if ($name === "")   $errors[] = "Name cannot be empty.";
if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = "Enter a valid email.";
if ($phone === "")  $errors[] = "Phone cannot be empty.";

if ($errors) {
    echo "<b>Validation Errors:</b><ul><li>" . implode("</li><li>", array_map('clean', $errors)) . "</li></ul>";
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=web_assignments_db;charset=utf8mb4",
        "root",
        "",
        [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
    );

    $stmt = $pdo->prepare("
        INSERT INTO registrations (name,email,phone,gender,course)
        VALUES (:n,:e,:p,:g,:c)
    ");
    $stmt->execute([
        ":n" => $name,
        ":e" => $email,
        ":p" => $phone,
        ":g" => $gender,
        ":c" => $course
    ]);

    $id = $pdo->lastInsertId();

    echo "
    <div class='display-card'>
        <p><b>Registration Successful</b></p>
        <p><b>ID:</b> $id</p>
        <p><b>Name:</b> " . clean($name) . "</p>
        <p><b>Email:</b> " . clean($email) . "</p>
        <p><b>Phone:</b> " . clean($phone) . "</p>
        <p><b>Gender:</b> " . clean($gender) . "</p>
        <p><b>Course:</b> " . clean($course) . "</p>
    </div>
    ";

} catch (Exception $e) {
    echo "Database error: " . clean($e->getMessage());
}
?>
