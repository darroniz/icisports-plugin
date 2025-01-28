<?php

function shortcode_eventos_ano_actual($atts) {
    ob_start();
    ?>

    <button id="ver-eventos-anteriores" data-action="show-past"><?php echo icl_translate('Eventos', 'Ver eventos anteriores', 'Ver eventos anteriores'); ?></button>
    <div id="eventos-container">
        <?php
        // Mostrar los eventos futuros por defecto
        mostrar_eventos('future');
        ?>
    </div>

    <script type="text/javascript">
    document.getElementById('ver-eventos-anteriores').addEventListener('click', function() {
        var container = document.getElementById('eventos-container');
        var button = this;
        var action = button.getAttribute('data-action');

        if (action === 'show-past') {
            // Mostrar eventos anteriores
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '<?php echo admin_url('admin-ajax.php?action=cargar_eventos_anteriores'); ?>', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    container.innerHTML = xhr.responseText;
                    button.textContent = '<?php echo icl_translate('Eventos', 'Ocultar eventos anteriores', 'Ocultar eventos anteriores'); ?>';
                    button.setAttribute('data-action', 'show-future');
                }
            };
            xhr.send();
        } else {
            // Mostrar eventos futuros
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '<?php echo admin_url('admin-ajax.php?action=cargar_eventos_futuros'); ?>', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    container.innerHTML = xhr.responseText;
                    button.textContent = '<?php echo icl_translate('Eventos', 'Ver eventos anteriores', 'Ver eventos anteriores'); ?>';
                    button.setAttribute('data-action', 'show-past');
                }
            };
            xhr.send();
        }
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('eventos_ano_actual', 'shortcode_eventos_ano_actual');

function mostrar_eventos($tipo) {
    // Detectar el idioma actual
    if (defined('ICL_LANGUAGE_CODE')) {
        $lang = ICL_LANGUAGE_CODE;

        // Configurar la localización según el idioma
        if ($lang == 'es') {
            setlocale(LC_TIME, 'es_ES.UTF-8');  // Español
        } elseif ($lang == 'en') {
            setlocale(LC_TIME, 'en_US.UTF-8');  // Inglés
        } 
    } else {
        // Si no se detecta el idioma, usar español por defecto
        setlocale(LC_TIME, 'es_ES.UTF-8');
    }

    $hoy = date('Y-m-d');

    if ($tipo === 'future') {
        $meta_query = array(
            array(
                'key' => '_fecha_inicio',
                'value' => $hoy,
                'compare' => '>=',
                'type' => 'DATE'
            )
        );
    } else {
        $meta_query = array(
            array(
                'key' => '_fecha_inicio',
                'value' => $hoy,
                'compare' => '<',
                'type' => 'DATE'
            )
        );
    }

    $args = array(
        'post_type' => 'evento',
        'meta_key' => '_fecha_inicio',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'posts_per_page' => -1,
        'meta_query' => $meta_query
    );

    $query = new WP_Query($args);
    $current_month = '';

    if ($query->have_posts()) {
        echo '<div class="eventos-ano-actual">';
        while ($query->have_posts()) {
            $query->the_post();
            $fecha_inicio = get_post_meta(get_the_ID(), '_fecha_inicio', true);
            $fecha_fin = get_post_meta(get_the_ID(), '_fecha_fin', true);
            $lugar = get_post_meta(get_the_ID(), '_lugar', true);
            $organizador = get_post_meta(get_the_ID(), '_organizador', true);
            $enlace_inscripcion = get_post_meta(get_the_ID(), '_enlace_inscripcion', true);

            // Formatear fechas
            $fecha_inicio_dt = new DateTime($fecha_inicio);
            $fecha_fin_dt = new DateTime($fecha_fin);
            $formato_fecha = '';

            if ($lang == 'es') {
                // Español: Meses con solo la primera letra en mayúscula y con "de"
                if ($fecha_inicio_dt->format('F') === $fecha_fin_dt->format('F')) {
                    $formato_fecha = strftime('%d', $fecha_inicio_dt->getTimestamp()) . ' - ' . ucfirst(strftime('%d de %B', $fecha_fin_dt->getTimestamp()));
                } else {
                    $formato_fecha = ucfirst(strftime('%d de %B', $fecha_inicio_dt->getTimestamp())) . ' - ' . ucfirst(strftime('%d de %B', $fecha_fin_dt->getTimestamp()));
                }
            } elseif ($lang == 'en') {
                // Inglés: Sin "de" y con solo la primera letra del mes en mayúscula
                if ($fecha_inicio_dt->format('F') === $fecha_fin_dt->format('F')) {
                    $formato_fecha = $fecha_inicio_dt->format('d') . ' - ' . ucfirst($fecha_fin_dt->format('d F'));
                } else {
                    $formato_fecha = ucfirst($fecha_inicio_dt->format('d F')) . ' - ' . ucfirst($fecha_fin_dt->format('d F'));
                }
            }

            // Comprobar si hay un cambio de mes
            $evento_mes = ucfirst(strftime('%B', $fecha_inicio_dt->getTimestamp()));
            if ($current_month !== $evento_mes) {
                if ($current_month !== '') {
                    echo '</div>'; // Cierra el div del mes anterior
                }
                $current_month = $evento_mes;
                echo '<div class="mes-eventos">';
                echo '<div class="mes-header"><h2 class="nombre-mes">' . $current_month . '</h2><hr class="divisor-mes"></div>';
            }

            echo '<div class="evento">';
            echo '<div class="fecha-evento">' . esc_html($formato_fecha) . '</div>';
            echo '<div class="titulo-evento"><a href="' . esc_url($enlace_inscripcion) . '">' . get_the_title() . '</a></div>';
            echo '<div class="info-evento">';
            echo '<span class="lugar-evento">' . icl_translate('Eventos', 'Ciudad', 'Ciudad') . ': <span class="lugar">' . esc_html($lugar) . '</span></span>';
            echo ' | ';
            echo '<span class="organizador-evento">' . icl_translate('Eventos', 'Organizador', 'Organizador') . ': <span class="organizador">' . esc_html($organizador) . '</span></span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>'; // Cierra el div del último mes
        echo '</div>'; // Cierra el contenedor de todos los eventos
    } else {
        echo '<p>No hay eventos para el año en curso.</p>';
    }

    wp_reset_postdata();
}




function cargar_eventos_anteriores() {
    mostrar_eventos('past');
    wp_die();
}
add_action('wp_ajax_cargar_eventos_anteriores', 'cargar_eventos_anteriores');
add_action('wp_ajax_nopriv_cargar_eventos_anteriores', 'cargar_eventos_anteriores');

function cargar_eventos_futuros() {
    mostrar_eventos('future');
    wp_die();
}
add_action('wp_ajax_cargar_eventos_futuros', 'cargar_eventos_futuros');
add_action('wp_ajax_nopriv_cargar_eventos_futuros', 'cargar_eventos_futuros');
?>
