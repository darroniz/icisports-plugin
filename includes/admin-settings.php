<?php

// Añadir menú de opciones en el panel de administración
function ecp_add_admin_menu() {
    add_menu_page(
        'Configuración de Eventos y Cursos', // Título de la página
        'Eventos y Cursos',                  // Título del menú
        'manage_options',                    // Capacidad requerida
        'ecp-settings',                      // Slug del menú
        'ecp_settings_page',                 // Función de la página de opciones
        'dashicons-admin-generic',           // Icono del menú
        20                                   // Posición del menú
    );
}
add_action('admin_menu', 'ecp_add_admin_menu');

// Registrar configuraciones
function ecp_register_settings() {
    register_setting('ecp_settings_group', 'ecp_custom_css');
}
add_action('admin_init', 'ecp_register_settings');

// Función de la página de opciones
function ecp_settings_page() {
    ?>
    <div class="wrap">
        <h1>Configuración de Eventos y Cursos</h1>
        <form method="post" action="options.php">
            <?php settings_fields('ecp_settings_group'); ?>
            <?php do_settings_sections('ecp_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">CSS Personalizado</th>
                    <td>
                        <textarea name="ecp_custom_css" rows="10" cols="50" class="large-text code"><?php echo esc_textarea(get_option('ecp_custom_css')); ?></textarea>
                        <p class="description">Añade tu CSS personalizado para los shortcodes aquí.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
