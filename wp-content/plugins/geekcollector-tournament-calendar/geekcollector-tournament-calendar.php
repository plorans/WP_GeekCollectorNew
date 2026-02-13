<?php

/**
 * Plugin Name: GeekCollector Tournament Calendar
 * Description: A tournament calendar plugin for GeekCollector.
 * Version: 0.1.0
 * Author: GeekDev
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    register_post_type('gc_tournament', [
        'labels' => [
            'name' => 'Torneos',
            'singular_name' => 'Torneo',
            'add_new_item' => 'Agregar Torneo',
            'edit_item' => 'Editar Torneo',
            'new_item' => 'Nuevo Torneo',
            'view_item' => 'Ver Torneo',
            'search_items' => 'Buscar Torneos',
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title'],
        'has_archive' => false,
        'rewrite' => ['slug' => 'torneos'],
    ]);
});

add_action('rest_api_init', function () {
    register_rest_route('gc/v1', '/tournaments', [
        'methods'  => 'GET',
        'callback' => function () {

            $query = new WP_Query([
                'post_type'      => 'gc_tournament',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ]);

            $events = [];

            foreach ($query->posts as $post) {
                $tcg = get_field('tcg', $post->ID);

                $events[] = [
                    'title' => get_field('titulo', $post->ID),
                    'start' => get_field('fecha', $post->ID),
                    'link'   => get_field('producto', $post->ID) ?: null,
                    'imageurl' => gc_get_tcg_image($tcg),
                    'tcg'      => $tcg,
                ];
            }

            return $events;
        },
        'permission_callback' => '__return_true',

    ]);
});

function gc_get_tcg_image($tcg)
{
    $map = [
        'onepiece' => get_template_directory_uri() . '/resources/images/tcg/One Piece.png',
        'pokemon'  => get_template_directory_uri() . '/resources/images/tcg/Pokémon.png',
        'magic'      => get_template_directory_uri() . '/resources/images/tcg/Magic The Gathering.png',
        'gundam'     => get_template_directory_uri() . '/resources/images/tcg/Gundam.png',
        'lorcana'  => get_template_directory_uri() . '/resources/images/tcg/Lorcana.png',
        'starwars' => get_template_directory_uri() . '/resources/images/tcg/STAR WARS.png',
        'riftbound' => get_template_directory_uri() . '/resources/images/tcg/RIFTBOUND.tif.png',
    ];

    return $map[$tcg] ?? null;
}

// Se agrega la columna de fecha y tcg al listado de torneos en el admin
add_filter('manage_gc_tournament_posts_columns', function ($columns) {
    $new = [];

    foreach ($columns as $key => $label) {
        $new[$key] = $label;

        // Insert after title
        if ($key === 'title') {
            $new['fecha'] = 'Fecha';
            $new['tcg']   = 'TCG';
        }
    }

    return $new;
});

// Se muestra el valor de fecha y tcg en la columna personalizada
add_action('manage_gc_tournament_posts_custom_column', function ($column, $post_id) {

    if ($column === 'fecha') {
        $fecha = get_field('fecha', $post_id);

        if ($fecha) {
            echo esc_html(
                date_i18n(
                    'd M Y H:i',
                    strtotime($fecha)
                )
            );
        } else {
            echo '—';
        }
    }

    if ($column === 'tcg') {
        $tcg = get_field('tcg', $post_id);

        if ($tcg) {
            // Optional: convert value → label
            $field = get_field_object('tcg', $post_id);
            echo esc_html($field['choices'][$tcg] ?? $tcg);
        } else {
            echo '—';
        }
    }
}, 10, 2);

// Se agrega funcion para filtrar por tcg en el listado de torneos en el admin
add_action('restrict_manage_posts', function () {

    $screen = get_current_screen();

    if ($screen->post_type !== 'gc_tournament') {
        return;
    }

    // Get ACF field definition
    $field = get_field_object('tcg');

    if (!$field || empty($field['choices'])) {
        return;
    }

    $current = $_GET['tcg'] ?? '';

    echo '<select name="tcg">';
    echo '<option value="">All TCGs</option>';

    foreach ($field['choices'] as $value => $label) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($value),
            selected($current, $value, false),
            esc_html($label)
        );
    }

    echo '</select>';
});

// Se filtra query por tcg en el listado de torneos en el admin
add_action('pre_get_posts', function ($query) {

    if (
        !is_admin() ||
        !$query->is_main_query()
    ) {
        return;
    }

    if ($query->get('post_type') !== 'gc_tournament') {
        return;
    }

    if (!empty($_GET['tcg'])) {
        $query->set('meta_query', [
            [
                'key'   => 'tcg',
                'value' => sanitize_text_field($_GET['tcg']),
            ]
        ]);
    }
});

// Se agrega acción para duplicar torneos en el listado de torneos en el admin
add_filter('post_row_actions', function ($actions, $post) {

    if ($post->post_type !== 'gc_tournament') {
        return $actions;
    }

    $url = wp_nonce_url(
        admin_url('admin.php?action=gc_duplicate_tournament&post=' . $post->ID),
        'gc_duplicate_tournament_' . $post->ID
    );

    $actions['duplicate'] = '<a href="' . esc_url($url) . '">Duplicar</a>';

    return $actions;
}, 10, 2);

// Se maneja la logica de duplicar torneos en el admin
add_action('admin_action_gc_duplicate_tournament', function () {

    if (empty($_GET['post'])) {
        wp_die('Post no especificado');
    }

    $post_id = (int) $_GET['post'];

    if (!wp_verify_nonce($_GET['_wpnonce'], 'gc_duplicate_tournament_' . $post_id)) {
        wp_die('Nonce inválido');
    }

    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'gc_tournament') {
        wp_die('Torneo inválido');
    }

    // Create the duplicated post
    $new_post_id = wp_insert_post([
        'post_title'   => $post->post_title,
        'post_content' => $post->post_content,
        'post_type'    => 'gc_tournament',
        'post_status'  => 'draft',
        'post_author'  => get_current_user_id(),
    ]);

    if (!$new_post_id) {
        wp_die('No se pudo duplicar el torneo');
    }

    // Copy all meta (including ACF)
    $meta = get_post_meta($post_id);

    foreach ($meta as $key => $values) {
        foreach ($values as $value) {
            add_post_meta($new_post_id, $key, maybe_unserialize($value));
        }
    }

    // Redirect to edit screen
    wp_redirect(
        admin_url('post.php?action=edit&post=' . $new_post_id)
    );
    exit;
});
