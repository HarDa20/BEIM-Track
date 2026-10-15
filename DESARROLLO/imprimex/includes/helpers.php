<?php
// Devuelve la clase CSS del badge según el estado del pedido
function claseBadgeEstado($estado) {
    $mapa = [
        'Recibido'            => 'badge-recibido',
        'Confirmado'          => 'badge-confirmado',
        'En producción'       => 'badge-produccion',
        'Control de calidad'  => 'badge-calidad',
        'Listo para entrega'  => 'badge-listo',
        'Entregado'           => 'badge-entregado',
    ];
    return $mapa[$estado] ?? 'badge-recibido';
}

// Lista ordenada de los estados posibles de un pedido
function pasosEstado() {
    return ['Recibido', 'Confirmado', 'En producción', 'Control de calidad', 'Listo para entrega', 'Entregado'];
}

function formatoMoneda($valor) {
    return '$ ' . number_format((float) $valor, 0, ',', '.');
}

function formatoFecha($fecha) {
    if (!$fecha) return '—';
    $ts = strtotime($fecha);
    $meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    return date('d', $ts) . ' ' . $meses[date('n', $ts) - 1] . ' ' . date('Y', $ts);
}
