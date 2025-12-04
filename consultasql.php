<?php

$fechaInicio = date('Y-m-01');
$fechaFin    = date('Y-m-t');

try {
    $stmtEmpresas = $db->prepare("SELECT DISTINCT empresa FROM orden_servicio WHERE empresa IS NOT NULL AND empresa != '' ORDER BY empresa ASC");
    $stmtEmpresas->execute();
    $listaEmpresas = $stmtEmpresas->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $listaEmpresas = [];
}


// Consulta SQL para obtener datos de órdenes de servicio 
try{
    $sentencia = $db->prepare("SELECT COUNT(estado_ot) FROM orden_servicio WHERE estado_ot = 'abierta' AND fecha_mantenimiento BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $estado_ot_a = $sentencia->fetchColumn();

    $sentencia = $db->prepare("SELECT COUNT(estado_ot) FROM orden_servicio WHERE estado_ot = 'cerrada' AND fecha_mantenimiento BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $estado_ot_c = $sentencia->fetchColumn(); 

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}


// Consulta SQL para obtener suma de ventas
try{
    $sentencia = $db->prepare("SELECT ROUND(SUM(subtotal), 0) FROM nota_venta");
    $sentencia->execute();
    $sumaventa = $sentencia->fetchColumn();

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

// Consulta SQL para obtener suma de compras del mes actual

try {
    $sentencia = $db->prepare("SELECT SUM(neto) FROM documento_compra WHERE fecha_emision BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $totalcomprasmes = $sentencia->fetchColumn();
    $totalcomprasmes = $totalcomprasmes ?? 0;
} catch (PDOException $e) {
    $totalcomprasmes = 0;
}


// Costos por servicio
$totalordenservicio = 0;
try{
    $sentencia = $db->prepare("SELECT COALESCE(SUM(precio_mano_obra),0) FROM orden_servicio_cotizacion WHERE fecha_mantenimiento BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $totalordenservicio = (int)$sentencia->fetchColumn();

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}


// Consulta SQL para obtener tipos de mantención
try{

    $sentencia = $db->prepare("SELECT COUNT(tipo_mantencion) FROM orden_servicio_cotizacion WHERE tipo_mantencion = 'confeccion y/o reparacion' AND fecha_mantenimiento BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $confec = $sentencia->fetchColumn();

    $sentencia = $db->prepare("SELECT COUNT(tipo_mantencion) FROM orden_servicio_cotizacion WHERE tipo_mantencion = 'preventivo' AND fecha_mantenimiento BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $prev = $sentencia->fetchColumn();

    $sentencia = $db->prepare("SELECT COUNT(tipo_mantencion) FROM orden_servicio_cotizacion WHERE tipo_mantencion = 'correctivo' AND fecha_mantenimiento BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $corr = $sentencia->fetchColumn();

    $sentencia = $db->prepare("SELECT COUNT(tipo_mantencion) FROM orden_servicio_cotizacion WHERE tipo_mantencion = 'otros' AND fecha_mantenimiento BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $otros = $sentencia->fetchColumn();

    $sentencia = $db->prepare("SELECT COUNT(tipo_mantencion) FROM orden_servicio_cotizacion WHERE tipo_mantencion = 'preventivo carta gant' AND fecha_mantenimiento BETWEEN :inicio AND :fin");
    $sentencia->bindParam(':inicio', $fechaInicio);
    $sentencia->bindParam(':fin', $fechaFin);
    $sentencia->execute();
    $prevcg = $sentencia->fetchColumn();

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}



try{
    // Contar órdenes de servicio CON cotización
    $sentConCotizacion = $db->prepare('
        SELECT COUNT(*) 
        FROM orden_servicio_cotizacion osc
        INNER JOIN orden_servicio os
        ON osc.id_mantenimiento = os.id_orden_servicio
        WHERE osc.precio_mano_obra > 0
        AND osc.fecha_mantenimiento BETWEEN :inicio AND :fin
    ');
    $sentConCotizacion->bindParam(':inicio', $fechaInicio);
    $sentConCotizacion->bindParam(':fin', $fechaFin);
    $sentConCotizacion->execute();
    $conCotizacion = $sentConCotizacion->fetchColumn();

    // Contar órdenes de servicio TOTAL
    $sentTotalOs = $db->prepare('
        SELECT COUNT(*) 
        FROM orden_servicio
        WHERE fecha_mantenimiento BETWEEN :inicio AND :fin
    ');
    $sentTotalOs->bindParam(':inicio', $fechaInicio);
    $sentTotalOs->bindParam(':fin', $fechaFin);
    $sentTotalOs->execute();
    $totalOs = $sentTotalOs->fetchColumn();

    $sinCotizacion = ($totalOs - $conCotizacion);
    if ($sinCotizacion < 0) $sinCotizacion = 0;

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

$margentotal = ($totalordenservicio - $totalcomprasmes);




?>


