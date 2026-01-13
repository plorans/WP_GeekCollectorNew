<?php

/**
 * Plugin Name: GeekCollector API
 * Description: API para login, avatar, membresías y achievements para la app.
 * Version: 1.1.0
 */

if (!defined('ABSPATH')) exit;

// ==============================
//  LOGIN
// ==============================
add_action('rest_api_init', function () {
    register_rest_route('geekcollector/v1', '/login', [
        'methods'  => 'POST',
        'callback' => 'gc_login_user',
        'permission_callback' => '__return_true'
    ]);
});

function gc_login_user($request)
{
    $params = $request->get_json_params();

    $username = sanitize_text_field($params['username'] ?? '');
    $password = sanitize_text_field($params['password'] ?? '');

    // WordPress: login con usuario o email
    $user = get_user_by('login', $username);
    if (!$user) $user = get_user_by('email', $username);

    if (!$user) {
        wp_send_json([
            'success' => false,
            'message' => 'Credenciales incorrectas (usuario no existe)'
        ]);
    }

    $check = wp_authenticate($user->user_login, $password);

    if (is_wp_error($check)) {
        wp_send_json([
            'success' => false,
            'message' => 'Credenciales incorrectas'
        ]);
    }


    // ==============================
    // Obtener Avatar (Simple Local Avatar)
    // ==============================
    $avatar_raw = get_user_meta($user->ID, 'simple_local_avatar', true);
    $avatar_url = '';

    // simple_local_avatar a veces devuelve array
    if (is_array($avatar_raw) && isset($avatar_raw['full'])) {
        $avatar_url = esc_url($avatar_raw['full']);
    } elseif (is_string($avatar_raw)) {
        $avatar_url = esc_url($avatar_raw);
    }

    // ==============================
    // Suscripciones WooCommerce
    // ==============================
    $subscriptions = [];
    if (function_exists('wcs_get_users_subscriptions')) {
        $subs = wcs_get_users_subscriptions($user->ID);

        foreach ($subs as $sub) {
            $subscriptions[] = [
                'id'           => $sub->get_id(),
                'status'       => $sub->get_status(),
                'total'        => $sub->get_total(),
                'start'        => $sub->get_date_created()?->date('Y-m-d H:i:s'),
                'next_payment' => $sub->get_date('next_payment')
            ];
        }
    }

    // ==============================
    // Achievements GamiPress
    // ==============================
    $achievements = [];
    if (function_exists('gamipress_get_user_achievements')) {
        $user_achievements = gamipress_get_user_achievements([
            'user_id' => $user->ID
        ]);

        if (!empty($user_achievements)) {
            foreach ($user_achievements as $ach) {
                $achievements[] = [
                    'id'          => $ach->ID,
                    'title'       => get_the_title($ach->ID),
                    'description' => get_post_meta($ach->ID, '_gamipress_achievement_description', true),
                    'icon'        => get_the_post_thumbnail_url($ach->ID, 'thumbnail') ?: '',
                    'type'        => get_post_meta($ach->ID, '_gamipress_achievement_type', true),
                    'date_earned' => get_user_meta($user->ID, 'gamipress_achievement_' . $ach->ID, true) ?: ''
                ];
            }
        }
    }

    // ==============================
    // Collector Tag, Bio y Fecha
    // ==============================
    $collector_tag = get_user_meta($user->ID, 'collector_tag', true) ?: '#G33K' . $user->ID;
    $bio = get_user_meta($user->ID, 'description', true) ?: '';
    $join_date = date('Y-m-d', strtotime($user->user_registered));

    // ==============================
    //  CRÉDITO WooWallet
    // ==============================
    $credit = 0;

    if (function_exists('woo_wallet')) {
        $wallet = woo_wallet()->wallet ?? null;

        if ($wallet && method_exists($wallet, 'get_wallet_balance')) {
            // WooWallet unformatted value
            $credit = floatval($wallet->get_wallet_balance($user->ID, 'unformatted'));
        }
    }


    // ==============================
    // RESPUESTA JSON COMPLETA
    // ==============================
    wp_send_json([
        'success' => true,
        'message' => 'Login correcto',
        'data'    => [
            'user_id'       => $user->ID,
            'username'      => $user->user_login,
            'email'         => $user->user_email,
            'name'          => $user->display_name,
            'collector_tag' => $collector_tag,
            'bio'           => $bio,
            'join_date'     => $join_date,
            'credit'        => $credit,
            'avatar'        => $avatar_url,   // <--- AQUI EL AVATAR REAL
            'subscriptions' => $subscriptions,
            'achievements'  => $achievements
        ]
    ]);
}

// ==============================
//  MEMBERSHIPS API
// ==============================
add_action('rest_api_init', function () {
    register_rest_route('geekcollector/v1', '/memberships', [
        'methods'  => 'GET',
        'callback' => 'gc_get_memberships',
        'permission_callback' => '__return_true'
    ]);
});

function gc_get_memberships($request)
{
    $user_id = intval($request->get_param('user_id'));

    if (!$user_id) wp_send_json(['success' => false, 'message' => 'Falta user_id']);
    if (!function_exists('wcs_get_users_subscriptions')) {
        wp_send_json(['success' => false, 'message' => 'WooCommerce Subscriptions no está activo']);
    }

    $subs = wcs_get_users_subscriptions($user_id);
    $data = [];

    foreach ($subs as $sub) {
        $data[] = [
            'id'           => $sub->get_id(),
            'status'       => $sub->get_status(),
            'total'        => $sub->get_total(),
            'start'        => $sub->get_date_created()?->date('Y-m-d H:i:s'),
            'next_payment' => $sub->get_date('next_payment')
        ];
    }

    wp_send_json(['success' => true, 'memberships' => $data]);
}

// ==============================
//  ACHIEVEMENTS API
// ==============================
add_action('rest_api_init', function () {
    register_rest_route('geekcollector/v1', '/achievements', [
        'methods' => 'GET',
        'callback' => 'gc_get_user_achievements_api',
        'permission_callback' => '__return_true'
    ]);
});

function gc_get_user_achievements_api($request)
{
    $user_id = intval($request->get_param('user_id'));

    if (!$user_id) wp_send_json(['success' => false, 'message' => 'Falta user_id']);
    if (!function_exists('gamipress_get_user_achievements')) {
        wp_send_json(['success' => false, 'message' => 'El plugin GamiPress no está activo']);
    }

    $achievements = gamipress_get_user_achievements(['user_id' => $user_id]);
    $data = [];

    if (!empty($achievements)) {
        foreach ($achievements as $ach) {
            $data[] = [
                'id'          => $ach->ID,
                'title'       => get_the_title($ach->ID),
                'description' => get_post_meta($ach->ID, '_gamipress_achievement_description', true),
                'icon'        => get_the_post_thumbnail_url($ach->ID, 'thumbnail') ?: '',
                'type'        => get_post_meta($ach->ID, '_gamipress_achievement_type', true),
                'date_earned' => get_user_meta($user_id, 'gamipress_achievement_' . $ach->ID, true) ?: ''
            ];
        }
    }

    wp_send_json(['success' => true, 'achievements' => $data]);
}


// ==============================
// LEADERBOARD TORNEOS
// ==============================

add_action('rest_api_init', function () {
    register_rest_route('geekcollector/v1', '/tournament-stats', [
        'methods'  => 'GET',
        'callback' => 'gc_api_tournament_stats',
        'permission_callback' => '__return_true'
    ]);
});

use TCGStats\Models\TournamentStat;

function gc_api_tournament_stats(WP_REST_Request $request)
{
    $tcg  = sanitize_text_field($request->get_param('tcg'));
    $mes  = sanitize_text_field($request->get_param('mes'));
    $tipo = sanitize_text_field($request->get_param('tipo')) ?: 'torneos';

    if (!$tcg || !$mes) {
        return wp_send_json_error([
            'message' => 'Faltan parámetros requeridos'
        ], 400);
    }

    try {
        switch ($tipo) {
            case 'global':
                $data = TournamentStat::global($tcg, $mes);
                break;

            case 'torneos':
            default:
                $data = TournamentStat::torneos($tcg, $mes);
                break;
        }

        return wp_send_json([
            'success' => true,
            'meta' => [
                'tcg'  => $tcg,
                'mes'  => $mes,
                'tipo' => $tipo
            ],
            'data' => $data
        ]);
    } catch (Throwable $e) {
        return wp_send_json_error([
            'message' => 'Error al obtener estadísticas',
            'error'   => $e->getMessage()
        ], 500);
    }
}

function gc_get_user_from_token()
{
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';

    if (!$auth || !str_starts_with($auth, 'Bearer ')) {
        return false;
    }

    $token = sanitize_text_field(substr($auth, 7));

    $users = get_users([
        'meta_key'   => 'gc_auth_token',
        'meta_value' => $token,
        'number'     => 1,
        'count_total' => false,
    ]);

    return $users[0] ?? false;
}
