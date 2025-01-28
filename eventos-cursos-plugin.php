<?php
/*
Plugin Name: Eventos y Cursos Plugin
Description: Plugin para gestionar eventos asociados a cursos, incluyendo shortcodes para mostrar próximos eventos.
Version: 1.0
Author: Tu Nombre
*/

// Definir constantes para las rutas del plugin
define('ECP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ECP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Incluir archivos necesarios
require_once ECP_PLUGIN_DIR . 'includes/cpt-config.php';
require_once ECP_PLUGIN_DIR . 'includes/shortcode-proximos-eventos.php';
require_once ECP_PLUGIN_DIR . 'includes/shortcode-eventos-ano-actual.php';
require_once ECP_PLUGIN_DIR . 'includes/admin-settings.php';

// Enqueue estilos CSS
function ecp_enqueue_styles() {
    wp_enqueue_style('ecp-estilos', ECP_PLUGIN_URL . 'css/estilos.css');
    
    // Añadir estilos personalizados desde la configuración
    $custom_css = get_option('ecp_custom_css');
    if ($custom_css) {
        wp_add_inline_style('ecp-estilos', wp_strip_all_tags($custom_css));
    }
}
add_action('wp_enqueue_scripts', 'ecp_enqueue_styles');

// // Registrar las cadenas personalizadas con WPML
function ecp_register_wpml_strings() {
    if (function_exists('icl_register_string')) {
        icl_register_string('Eventos', 'Ver eventos anteriores', 'Ver eventos anteriores');
        icl_register_string('Eventos', 'Ocultar eventos anteriores', 'Ocultar eventos anteriores');
        icl_register_string('Eventos', 'Ciudad', 'Ciudad');
        icl_register_string('Eventos', 'Organizador', 'Organizador');
    }
}
add_action('init', 'ecp_register_wpml_strings');
?>
