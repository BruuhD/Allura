<?php
include('../config/database.php');

header("Content-Type: application/json; charset=UTF-8");

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $token          = $data['token'] ?? '';
    $currentPassword = $data['current_password'] ?? '';
    $newPassword     = $data['new_password'] ?? '';

    if (empty($token) || empty($currentPassword) || empty($newPassword)) {
        echo json_encode(["status" => false, "msg" => "Faltan campos obligatorios."]);
        exit;
    }

    // 🔹 Conexión DB
    $pdo = new PDO("mysql:host=localhost;dbname=allura_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 🔹 Buscar token válido
    $sql = "SELECT u.id, u.password 
            FROM users u 
            INNER JOIN user_tokens t ON t.user_id = u.id 
            WHERE t.token = :token AND t.expires_at > NOW()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status" => false, "msg" => "Token inválido o expirado."]);
        exit;
    }

    // 🔹 Verificar contraseña actual
    if ($user['password'] !== md5($currentPassword)) {
        echo json_encode(["status" => false, "msg" => "Contraseña actual incorrecta."]);
        exit;
    }

    // 🔹 Actualizar contraseña
    $newPasswordHashed = md5($newPassword);
    $update = $pdo->prepare("UPDATE users SET password = :pwd WHERE id = :id");
    $update->execute([':pwd' => $newPasswordHashed, ':id' => $user['id']]);

    echo json_encode(["status" => true, "msg" => "Contraseña actualizada correctamente."]);

} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "msg" => "Error interno: " . $e->getMessage()
    ]);
}
