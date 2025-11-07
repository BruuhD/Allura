<?php
include __DIR__ . "/../../config/database.php";
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["status" => false, "msg" => "No se recibieron datos válidos."]);
    exit;
}

$order_number = $data["order_number"] ?? null;
$nfc_code     = $data["nfc_code"] ?? null;
$proveedor    = trim($data["proveedor"] ?? "");
$rnc          = trim($data["rnc"] ?? "");
$telefono     = trim($data["telefono"] ?? "");
$items        = $data["items"] ?? [];
$tipo_cliente = $data["tipo_cliente"] ?? "nuevo"; // nuevo / registrado

if (!$order_number || !$nfc_code || empty($items)) {
    echo json_encode(["status" => false, "msg" => "Datos incompletos para registrar la orden."]);
    exit;
}

$totalGeneral = array_sum(array_map(fn($it) => floatval($it['total'] ?? 0), $items));

try {
    $pdo->beginTransaction();

    // =========================================================
    // VALIDAR CLIENTE POR CÉDULA / RNC
    // =========================================================
    $cliente = null;
    if (!empty($rnc)) {
        $check = $pdo->prepare("SELECT id, product FROM clients WHERE ruc = :ruc LIMIT 1");
        $check->execute([':ruc' => $rnc]);
        $cliente = $check->fetch(PDO::FETCH_ASSOC);

        if ($cliente && $tipo_cliente === 'nuevo') {
            $pdo->rollBack();
            echo json_encode([
                "status" => false,
                "msg" => "El cliente '{$proveedor}' con RNC '{$rnc}' ya existe. Seleccione 'Registrado' si desea continuar."
            ]);
            exit;
        }
    }

    // =========================================================
    // INSERTAR ORDEN PRINCIPAL
    // =========================================================
    $stmt = $pdo->prepare("
        INSERT INTO orders (order_number, nfc_code, proveedor, rnc, telefono, total, estado, order_type)
        VALUES (:order_number, :nfc_code, :proveedor, :rnc, :telefono, :total, 'Pendiente', 'venta')
    ");
    $stmt->execute([
        ':order_number' => $order_number,
        ':nfc_code'     => $nfc_code,
        ':proveedor'    => $proveedor,
        ':rnc'          => $rnc,
        ':telefono'     => $telefono,
        ':total'        => $totalGeneral
    ]);
    $order_id = $pdo->lastInsertId();

    // =========================================================
    // INSERTAR ÍTEMS
    // =========================================================
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (order_id, product_name, unit, quantity, price)
        VALUES (:order_id, :product_name, :unit, :quantity, :price)
    ");
    foreach ($items as $it) {
        $stmtItem->execute([
            ":order_id"     => $order_id,
            ":product_name" => $it["producto"] ?? "",
            ":unit"         => $it["unidad"] ?? "N/A",
            ":quantity"     => intval($it["cantidad"] ?? 0),
            ":price"        => floatval($it["costo"] ?? 0)
        ]);
    }

    // =========================================================
    // MARCAR NFC USADO
    // =========================================================
    $stmtNfc = $pdo->prepare("
        UPDATE nfc_codes 
        SET used = 1, assigned_order_id = :order_id, used_at = NOW() 
        WHERE code = :code
    ");
    $stmtNfc->execute([
        ":order_id" => $order_id,
        ":code"     => $nfc_code
    ]);

    // =========================================================
    // CLIENTE NUEVO
    // =========================================================
    if ($tipo_cliente === 'nuevo' && !$cliente) {
        foreach ($items as $it) {
            $productoTexto = trim($it["producto"] ?? "");
            if ($productoTexto === "") continue;

            $productosLimpios = array_filter(array_map('trim', explode(',', $productoTexto)));

            foreach ($productosLimpios as $producto) {
                if ($producto === "") continue;

                $insertCli = $pdo->prepare("
                    INSERT INTO clients (name, product, ruc, phone, active, created_at, updated_at)
                    VALUES (:name, :product, :ruc, :phone, 1, NOW(), NOW())
                ");
                $insertCli->execute([
                    ":name"   => $proveedor,
                    ":product"=> $producto,
                    ":ruc"    => $rnc,
                    ":phone"  => $telefono
                ]);
            }
        }
    }

    // =========================================================
    // CLIENTE REGISTRADO → validar y agregar productos nuevos
    // =========================================================
    if ($tipo_cliente === 'registrado' && $cliente) {
        $productosExistentes = array_map('trim', explode(',', $cliente['product'] ?? ''));

        foreach ($items as $it) {
            $productoTexto = trim($it["producto"] ?? "");
            if ($productoTexto === "") continue;

            $productosLimpios = array_filter(array_map('trim', explode(',', $productoTexto)));

            foreach ($productosLimpios as $producto) {
                if ($producto === "" || in_array($producto, $productosExistentes)) continue;

                $insertCli = $pdo->prepare("
                    INSERT INTO clients (name, product, ruc, phone, active, created_at, updated_at)
                    VALUES (:name, :product, :ruc, :phone, 1, NOW(), NOW())
                ");
                $insertCli->execute([
                    ":name"   => $proveedor,
                    ":product"=> $producto,
                    ":ruc"    => $rnc,
                    ":phone"  => $telefono
                ]);

                $productosExistentes[] = $producto;
            }
        }
    }

    $pdo->commit();
    echo json_encode([
        "status" => true,
        "msg" => "Orden registrada correctamente y cliente actualizado.",
        "order_id" => $order_id
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        "status" => false,
        "msg" => "Error al guardar la orden: " . $e->getMessage()
    ]);
}
?>
