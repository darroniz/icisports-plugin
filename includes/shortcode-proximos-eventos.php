<?php

function shortcode_proximos_eventos($atts) {
    ob_start();
    
    // Configurar la localización a español
    setlocale(LC_TIME, 'es_ES.UTF-8');
    // Para servidores que usan Windows, usa:
    // setlocale(LC_TIME, 'spanish');

    // Primero, obtenemos los próximos eventos
    $args = array(
        'post_type' => 'evento',
        'meta_key' => '_fecha_inicio',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'posts_per_page' => 4,
        'meta_query' => array(
            array(
                'key' => '_fecha_inicio',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE'
            )
        )
    );
    
    $query = new WP_Query($args);
    $eventos_futuros = $query->posts;
    $total_eventos = count($eventos_futuros);
    
    // Si hay menos de 4 eventos, obtenemos los eventos pasados adicionales
    if ($total_eventos < 4) {
        $faltantes = 4 - $total_eventos;
        
        $args_pasados = array(
            'post_type' => 'evento',
            'meta_key' => '_fecha_inicio',
            'orderby' => 'meta_value',
            'order' => 'DESC',
            'posts_per_page' => $faltantes,
            'meta_query' => array(
                array(
                    'key' => '_fecha_inicio',
                    'value' => date('Y-m-d'),
                    'compare' => '<',
                    'type' => 'DATE'
                )
            )
        );
        
        $query_pasados = new WP_Query($args_pasados);
        $eventos_pasados = $query_pasados->posts;
        
        // Unimos los eventos pasados y futuros, con los pasados primero
        $eventos = array_merge($eventos_pasados, $eventos_futuros);
    } else {
        $eventos = $eventos_futuros;
    }
    
    if (!empty($eventos)) {
        echo '<div class="proximos-eventos">';
        foreach ($eventos as $evento) {
            setup_postdata($evento);
            $fecha_inicio = get_post_meta($evento->ID, '_fecha_inicio', true);
            $fecha_fin = get_post_meta($evento->ID, '_fecha_fin', true);
            $lugar = get_post_meta($evento->ID, '_lugar', true);
            $organizador = get_post_meta($evento->ID, '_organizador', true);
            $enlace_inscripcion = get_post_meta($evento->ID, '_enlace_inscripcion', true);

            // Formatear fechas
            $fecha_inicio_dt = new DateTime($fecha_inicio);
            $fecha_fin_dt = new DateTime($fecha_fin);
            $formato_fecha = '';

            if ($fecha_inicio_dt->format('F') === $fecha_fin_dt->format('F')) {
                $formato_fecha = strftime('%d', $fecha_inicio_dt->getTimestamp()) . ' - ' . strftime('%d de %B', $fecha_fin_dt->getTimestamp());
            } else {
                $formato_fecha = strftime('%d de %B', $fecha_inicio_dt->getTimestamp()) . ' - ' . strftime('%d de %B', $fecha_fin_dt->getTimestamp());
            }

            echo '<div class="evento">';
            echo '<div class="fecha-evento">' . esc_html($formato_fecha) . '</div>';
            echo '<div class="titulo-evento"><a href="' . esc_url($enlace_inscripcion) . '">' . get_the_title($evento) . '</a></div>';
            echo '<div class="info-evento">';
            echo '<span class="lugar-evento">' . icl_translate('Eventos', 'Ciudad', 'Ciudad') . ': <span class="lugar">' . esc_html($lugar) . '</span></span>';
            echo ' | ';
            echo '<span class="organizador-evento">' . icl_translate('Eventos', 'Organizador', 'Organizador') . ': <span class="organizador">' . esc_html($organizador) . '</span></span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No hay eventos disponibles.</p>';
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('proximos_eventos', 'shortcode_proximos_eventos');
?>

