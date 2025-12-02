<?php
include('database.php');

header('Content-Type: application/json');

$fechaInicio = $_GET['inicio'] ?? date('Y-m-01');
$fechaFin = $_GET['fin'] ?? date('Y-m-t');
$empresaFilter = $_GET['empresa'] ?? '';

// Validaciones de fecha
$inicioDT = DateTime::createFromFormat('Y-m-d', $fechaInicio);
$finDT = DateTime::createFromFormat('Y-m-d', $fechaFin);

if (!$inicioDT || !$finDT || $inicioDT->format('Y-m-d') !== $fechaInicio || $finDT->format('Y-m-d') !== $fechaFin) {
    echo json_encode(['error' => 'Formato de fecha inválido. Use AAAA-MM-DD.']);
    exit;
}
if ($fechaInicio > $fechaFin) {
    echo json_encode(['error' => 'La fecha inicial debe ser anterior o igual a la fecha final.']);
    exit;
}

try {
    // ---------------------------------------------------------
    // 1. VENTAS Y COMPRAS (SIN FILTRO DE EMPRESA)
    // ---------------------------------------------------------

    // Total ventas
    $stmt = $db->prepare("SELECT ROUND(COALESCE(SUM(subtotal),0), 0) FROM nota_venta WHERE fecha_emision BETWEEN :inicio AND :fin");
    $stmt->bindParam(':inicio', $fechaInicio);
    $stmt->bindParam(':fin', $fechaFin);
    $stmt->execute();
    $sumaventa = (int)$stmt->fetchColumn();

    // Total gastos compra
    $stmt = $db->prepare("SELECT COALESCE(SUM(neto),0) FROM documento_compra WHERE fecha_emision BETWEEN :inicio AND :fin");
    $stmt->bindParam(':inicio', $fechaInicio);
    $stmt->bindParam(':fin', $fechaFin);
    $stmt->execute();
    $totalcomprasmes = (int)$stmt->fetchColumn();


    // ---------------------------------------------------------
    // 2. LOGICA PARA FILTRO DINÁMICO
    // ---------------------------------------------------------
    
    $sqlEmpresa = ""; 
    if (!empty($empresaFilter)) {
        $sqlEmpresa = " AND empresa = :empresa ";
    }

    // Función auxiliar para bindear parámetros
    function bindParamsConEmpresa($stmt, $inicio, $fin, $empresa, $empresaSql) {
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fin', $fin);
        if (!empty($empresaSql)) {
            $stmt->bindParam(':empresa', $empresa);
        }
    }

    // ---------------------------------------------------------
    // 3. CONSULTAS CON FILTRO DE EMPRESA (Ordenes y Cotizaciones)
    // ---------------------------------------------------------

    // Total cotizaciones - Tabla orden_servicio_cotizacion
    $sql = "SELECT COALESCE(SUM(precio_mano_obra),0) FROM orden_servicio_cotizacion WHERE fecha_mantenimiento BETWEEN :inicio AND :fin" . $sqlEmpresa;
    $stmt = $db->prepare($sql);
    bindParamsConEmpresa($stmt, $fechaInicio, $fechaFin, $empresaFilter, $sqlEmpresa);
    $stmt->execute();
    $totalordenservicio = (int)$stmt->fetchColumn();

    // Estado OT - abiertas - Tabla orden_servicio
    $sql = "SELECT COUNT(*) FROM orden_servicio WHERE estado_ot = 'abierta' AND fecha_mantenimiento BETWEEN :inicio AND :fin" . $sqlEmpresa;
    $stmt = $db->prepare($sql);
    bindParamsConEmpresa($stmt, $fechaInicio, $fechaFin, $empresaFilter, $sqlEmpresa);
    $stmt->execute();
    $estado_ot_a = (int)$stmt->fetchColumn();

    // Estado OT - cerradas
    $sql = "SELECT COUNT(*) FROM orden_servicio WHERE estado_ot = 'cerrada' AND fecha_mantenimiento BETWEEN :inicio AND :fin" . $sqlEmpresa;
    $stmt = $db->prepare($sql);
    bindParamsConEmpresa($stmt, $fechaInicio, $fechaFin, $empresaFilter, $sqlEmpresa);
    $stmt->execute();
    $estado_ot_c = (int)$stmt->fetchColumn();

    // Tipos de mantención - Tabla orden_servicio_cotizacion
    $tipos = ['confeccion/reparacion', 'preventivo', 'correctivo', 'otros', 'preventivo carta gant'];
    $resultados = [];
    foreach ($tipos as $tipo) {
        $sql = "SELECT COUNT(*) FROM orden_servicio_cotizacion WHERE tipo_mantencion = :tipo AND fecha_mantenimiento BETWEEN :inicio AND :fin" . $sqlEmpresa;
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':tipo', $tipo);
        bindParamsConEmpresa($stmt, $fechaInicio, $fechaFin, $empresaFilter, $sqlEmpresa);
        $stmt->execute();
        $resultados[] = (int)$stmt->fetchColumn();
    }
    list($confec, $prev, $corr, $otros, $prevcg) = $resultados;

    // Con / Sin Cotización
    $sqlConCot = "
        SELECT COUNT(*) 
        FROM orden_servicio_cotizacion osc
        INNER JOIN orden_servicio os ON osc.id_mantenimiento = os.id_orden_servicio
        WHERE osc.precio_mano_obra > 0
          AND os.fecha_mantenimiento BETWEEN :inicio AND :fin
    ";
    if (!empty($empresaFilter)) {
        $sqlConCot .= " AND os.empresa = :empresa";
    }
    
    $stmt = $db->prepare($sqlConCot);
    bindParamsConEmpresa($stmt, $fechaInicio, $fechaFin, $empresaFilter, $sqlEmpresa);
    $stmt->execute();
    $conCotizacion = (int)$stmt->fetchColumn();

    $margentotal = ($totalordenservicio - $totalcomprasmes);

    // Total para calcular 'Sin Cotización'
    $sqlTotalOS = "SELECT COUNT(*) FROM orden_servicio os WHERE os.fecha_mantenimiento BETWEEN :inicio AND :fin";
    if (!empty($empresaFilter)) {
        $sqlTotalOS .= " AND os.empresa = :empresa";
    }
    $stmt = $db->prepare($sqlTotalOS);
    bindParamsConEmpresa($stmt, $fechaInicio, $fechaFin, $empresaFilter, $sqlEmpresa);
    $stmt->execute();
    $totalOs = (int)$stmt->fetchColumn();

    $sinCotizacion = $totalOs - $conCotizacion;
    if ($sinCotizacion < 0) $sinCotizacion = 0;


    // Retorno JSON
    echo json_encode([
        'sumaventa' => $sumaventa,
        'sumaventa_formateado' => number_format($sumaventa, 0, ',', '.'),
        'totalcomprasmes' => $totalcomprasmes,
        'totalcomprasmes_formateado' => number_format($totalcomprasmes, 0, ',', '.'),
        'totalordenservicio' => $totalordenservicio,
        'totalordenservicio_formateado' => number_format($totalordenservicio, 0, ',', '.'),
        'estado_ot_a' => $estado_ot_a,
        'estado_ot_c' => $estado_ot_c,
        'confec' => $confec,
        'prev' => $prev,
        'corr' => $corr,
        'otros' => $otros,
        'prevcg' => $prevcg,
        'conCotizacion' => $conCotizacion,
        'sinCotizacion' => $sinCotizacion,
        'margentotal'=> number_format($margentotal, 0, ',', '.'),

    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>