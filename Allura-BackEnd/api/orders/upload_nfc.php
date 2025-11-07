<?php
header("Content-Type: application/json");
include __DIR__ . "/../../config/database.php";

// ===========================
// ✅ VALIDAR ARCHIVO SUBIDO
// ===========================
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => false, "msg" => "No se recibió ningún archivo válido."]);
    exit;
}

$archivoTmp = $_FILES['file']['tmp_name'];
$nombreArchivo = $_FILES['file']['name'];
$extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

$codigos = [];

// ===========================
// 📄 LECTURA SEGÚN EXTENSIÓN
// ===========================
if (in_array($extension, ['csv', 'txt'])) {
    if (($handle = fopen($archivoTmp, 'r')) !== false) {
        while (($fila = fgetcsv($handle, 1000, ',')) !== false) {
            // Si el CSV usa punto y coma en vez de coma
            if (count($fila) === 1) {
                $fila = str_getcsv($fila[0], ';');
            }

            $codigo = trim($fila[0]);
            if ($codigo !== '' && strtolower($codigo) !== 'nfc' && strtolower($codigo) !== 'código') {
                $codigos[] = $codigo;
            }
        }
        fclose($handle);
    }
} else {
    echo json_encode(["status" => false, "msg" => "Formato no soportado. Solo se permiten archivos CSV o TXT."]);
    exit;
}

// ===========================
// 🚦 VALIDACIONES DE CONTENIDO
// ===========================
if (empty($codigos)) {
    echo json_encode(["status" => false, "msg" => "No se encontraron códigos válidos en el archivo."]);
    exit;
}

// Eliminar duplicados dentro del archivo
$codigos = array_unique($codigos);

// ===========================
// 💾 INSERCIÓN EN BASE DE DATOS
// ===========================
try {
    $insertados = 0;
    $existentes = 0;

    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM nfc_codes WHERE code = ?");
    $stmtInsert = $pdo->prepare("INSERT INTO nfc_codes (code) VALUES (?)");

    foreach ($codigos as $codigo) {
        $stmtCheck->execute([$codigo]);
        if ($stmtCheck->fetchColumn() == 0) {
            $stmtInsert->execute([$codigo]);
            $insertados++;
        } else {
            $existentes++;
        }
    }

    echo json_encode([
        "status" => true,
        "msg" => "Archivo procesado correctamente.",
        "total" => $insertados,
        "duplicados" => $existentes
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "msg" => "Error al guardar los códigos: " . $e->getMessage()
    ]);
}
?>
