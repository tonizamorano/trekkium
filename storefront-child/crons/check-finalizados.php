<?php
/**
 * Cron CLI: marcar actividades finalizadas.
 *
 * - Ejecutable solo por CLI (no HTTP)
 * - Usa timezone de WordPress
 * - Logs propios en wp-content/logs/cron-finalizados.log
 * - Exit code != 0 en caso de error (SiteGround puede alertar por email)
 */

if ( php_sapi_name() !== 'cli' ) {
    cron_log( 'Este script solo puede ejecutarse desde la línea de comandos', 'ERROR' );
    exit(1);
}

/* ------------------------------------------------------------
 * Cargar WordPress
 * ------------------------------------------------------------ */

$search_dir = __DIR__;
$wp_loaded  = false;

for ( $i = 0; $i < 10; $i++ ) {
    if ( file_exists( $search_dir . '/wp-load.php' ) ) {
        require_once $search_dir . '/wp-load.php';
        $wp_loaded = true;
        break;
    }
    $parent = dirname( $search_dir );
    if ( $parent === $search_dir ) {
        break;
    }
    $search_dir = $parent;
}

if ( ! $wp_loaded ) {
    cron_log( 'No se pudo cargar WordPress', 'ERROR' );
    exit(1);
}

/* ------------------------------------------------------------
 * Logging
 * ------------------------------------------------------------ */

$log_dir  = __DIR__ . '/logs';
$log_info = $log_dir . '/cron-finalizados-info-' . date('Y-m-d') . '.log';
$log_err  = $log_dir . '/cron-finalizados-error-' . date('Y-m-d') . '.log';

if ( ! is_dir( $log_dir ) ) {
    mkdir( $log_dir, 0755, true );
}

function cron_log( $message, $level = 'INFO' ) {
    global $log_info, $log_err;
    $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), $level, $message);
    if ($level === 'ERROR') {
        error_log($line, 3, $log_err);
    } else {
        error_log($line, 3, $log_info);
    }
}


/* ------------------------------------------------------------
 * Inicio ejecución
 * ------------------------------------------------------------ */

$start_time = microtime( true );
$had_errors = false;

cron_log( 'Start exec cron check-finalizados' );


// timezone wp
$tz  = wp_timezone();
$now = ( new DateTime( 'now', $tz ) )->getTimestamp();

// query get productos activos de tipo actividad
$args = array(
    'post_type'      => 'product',
    'post_status'    => array( 'publish', 'private', 'future', 'pending' ),
    'posts_per_page' => -1,
    'meta_query'     => array(
        array(
            'key'     => 'estado_producto',
            'value'   => 'Activo',
            'compare' => '=',
        ),
    ),
    'tax_query' => array(
        array(
            'taxonomy' => 'tipo',
            'field'    => 'name',
            'terms'    => 'Actividad',
        ),
    ),
    'fields' => 'ids', // optimización: solo IDs
);

$q = new WP_Query( $args );

$total_activos     = count( $q->posts );
$total_finalizados = 0;

cron_log( "Productos activos encontrados: {$total_activos}" );

// check cada producto
foreach ( $q->posts as $product_id ) {

    $fecha = get_post_meta( $product_id, 'fecha', true );
    $hora  = get_post_meta( $product_id, 'hora', true );

    if ( empty( $fecha ) || empty( $hora ) ) {
        continue;
    }

    $datetime_str = trim( $fecha . ' ' . $hora );

    $dt = DateTime::createFromFormat( 'Y-m-d H:i', $datetime_str, $tz );

    if ( $dt === false ) {
        cron_log(
            "Fecha/hora inválida en producto {$product_id}: '{$datetime_str}'",
            'ERROR'
        );
        $had_errors = true;
        continue;
    }

    $ts = $dt->getTimestamp();

    // fecha + 12h < ahora => finalizar
    if ( ( $ts + 12 * 3600 ) < $now ) {

        update_post_meta( $product_id, 'estado_producto', 'Finalizado' );

        $result = wp_update_post(
            array(
                'ID'          => $product_id,
                'post_status' => 'draft',
            ),
            true
        );

        if ( is_wp_error( $result ) ) {
            cron_log(
                "Error al actualizar producto {$product_id}: " . $result->get_error_message(),
                'ERROR'
            );
            $had_errors = true;
            continue;
        }

        $total_finalizados++;
    }
}


$duration = round( microtime( true ) - $start_time, 2 );

cron_log(
    "Fin ejecución. Finalizados: {$total_finalizados} / {$total_activos}. Duración: {$duration}s"
);

if ( $had_errors ) {
    cron_log( 'La ejecución terminó con errores.', 'ERROR' );
    exit(1);
}

exit(0);
