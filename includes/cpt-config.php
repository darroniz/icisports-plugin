<?php

// Register Custom Post Type for Eventos
function crear_custom_post_type_eventos() {
    $labels = array(
        'name'               => _x('Eventos', 'post type general name'),
        'singular_name'      => _x('Evento', 'post type singular name'),
        'menu_name'          => _x('Eventos', 'admin menu'),
        'name_admin_bar'     => _x('Evento', 'add new on admin bar'),
        'add_new'            => _x('Añadir Nuevo', 'evento'),
        'add_new_item'       => __('Añadir Nuevo Evento'),
        'new_item'           => __('Nuevo Evento'),
        'edit_item'          => __('Editar Evento'),
        'view_item'          => __('Ver Evento'),
        'all_items'          => __('Todos los Eventos'),
        'search_items'       => __('Buscar Eventos'),
        'parent_item_colon'  => __('Eventos Padre:'),
        'not_found'          => __('No se encontraron eventos.'),
        'not_found_in_trash' => __('No se encontraron eventos en la papelera.')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'evento'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'taxonomies'         => array('category', 'post_tag')
    );

    register_post_type('evento', $args);
}
add_action('init', 'crear_custom_post_type_eventos');

// Add Custom Fields to Eventos
function agregar_campos_personalizados_eventos() {
    add_meta_box(
        'campos_personalizados_eventos',
        'Información del Evento',
        'mostrar_campos_personalizados_eventos',
        'evento',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'agregar_campos_personalizados_eventos');

function mostrar_campos_personalizados_eventos($post) {
    wp_nonce_field('guardar_campos_personalizados_eventos', 'campos_personalizados_eventos_nonce');

    $fecha_inicio = get_post_meta($post->ID, '_fecha_inicio', true);
    $fecha_fin = get_post_meta($post->ID, '_fecha_fin', true);
    $lugar = get_post_meta($post->ID, '_lugar', true);
    $curso = get_post_meta($post->ID, '_curso', true);
    $organizador = get_post_meta($post->ID, '_organizador', true);
    $enlace_inscripcion = get_post_meta($post->ID, '_enlace_inscripcion', true);

    echo '<label for="fecha_inicio">Fecha de Inicio:</label>';
    echo '<input type="date" id="fecha_inicio" name="fecha_inicio" value="' . esc_attr($fecha_inicio) . '" size="25" /><br>';

    echo '<label for="fecha_fin">Fecha de Fin:</label>';
    echo '<input type="date" id="fecha_fin" name="fecha_fin" value="' . esc_attr($fecha_fin) . '" size="25" /><br>';

    echo '<label for="lugar">Lugar:</label>';
    echo '<input type="text" id="lugar" name="lugar" value="' . esc_attr($lugar) . '" size="25" /><br>';

    echo '<label for="curso">Curso:</label>';
    wp_dropdown_pages(array('name' => 'curso', 'selected' => $curso, 'post_type' => 'cursos', 'show_option_none' => 'Selecciona un Curso', 'sort_column' => 'menu_order, post_title'));
    echo '<br>';

    echo '<label for="organizador">Organizador:</label>';
    echo '<input type="text" id="organizador" name="organizador" value="' . esc_attr($organizador) . '" size="25" /><br>';

    echo '<label for="enlace_inscripcion">Enlace de Inscripción:</label>';
    echo '<input type="url" id="enlace_inscripcion" name="enlace_inscripcion" value="' . esc_attr($enlace_inscripcion) . '" size="25" />';
}

function guardar_campos_personalizados_eventos($post_id) {
    if (!isset($_POST['campos_personalizados_eventos_nonce']) || !wp_verify_nonce($_POST['campos_personalizados_eventos_nonce'], 'guardar_campos_personalizados_eventos')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['fecha_inicio'])) {
        $fecha_inicio = date('Y-m-d', strtotime(sanitize_text_field($_POST['fecha_inicio'])));
        update_post_meta($post_id, '_fecha_inicio', $fecha_inicio);
    }

    if (isset($_POST['fecha_fin'])) {
        $fecha_fin = date('Y-m-d', strtotime(sanitize_text_field($_POST['fecha_fin'])));
        update_post_meta($post_id, '_fecha_fin', $fecha_fin);
    }

    if (isset($_POST['lugar'])) {
        $lugar = sanitize_text_field($_POST['lugar']);
        update_post_meta($post_id, '_lugar', $lugar);
    }

    if (isset($_POST['curso'])) {
        $curso = intval($_POST['curso']);
        update_post_meta($post_id, '_curso', $curso);
    }

    if (isset($_POST['organizador'])) {
        $organizador = sanitize_text_field($_POST['organizador']);
        update_post_meta($post_id, '_organizador', $organizador);
    }

    if (isset($_POST['enlace_inscripcion'])) {
        $enlace_inscripcion = esc_url($_POST['enlace_inscripcion']);
        update_post_meta($post_id, '_enlace_inscripcion', $enlace_inscripcion);
    }
}
add_action('save_post', 'guardar_campos_personalizados_eventos');
?>
