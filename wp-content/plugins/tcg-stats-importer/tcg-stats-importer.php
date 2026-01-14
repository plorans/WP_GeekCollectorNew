<?php

/**
 * Plugin Name: TCG Stats Importer
 * Description: Upload and import TCG tournament statistics from XLSX files.
 * Version: 0.1.0
 * Author: GeekDev
 */

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once __DIR__ . '/app/Models/TournamentStats.php';
require_once __DIR__ . '/helpers/Seasons.php';


if (!defined('ABSPATH')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Create Table on Plugin Activation
register_activation_hook(__FILE__, 'tcg_stats_create_table');

function tcg_stats_create_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'tcg_tournament_stats';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        tcg VARCHAR(50) NOT NULL,
        fecha DATE NOT NULL,
        torneo TINYINT NOT NULL,
        usuario VARCHAR(190) NOT NULL,
        tcg_id VARCHAR(100) NOT NULL,

        geek_tag VARCHAR(100) NULL,
        ranking INT NULL,
        puntos INT NULL,
        ganador TINYINT(1) NOT NULL DEFAULT 0,

        victorias INT NOT NULL,
        derrotas INT NOT NULL,
        omw DECIMAL(6,2) NULL,

        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (id),
        UNIQUE KEY uniq_entry (tcg, torneo, fecha, tcg_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}


// Sidebar Menu
add_action('admin_menu', function () {
    add_menu_page(
        'TCG Stats',
        'TCG Stats',
        'manage_options',
        'tcg-stats',
        'tcg_stats_upload_page',
        'dashicons-chart-bar'
    );
});

//Log contenido
function tcg_stats_log($message, $context = [])
{
    $dir = WP_CONTENT_DIR . '/uploads/tcg-import';

    if (!is_dir($dir)) {
        wp_mkdir_p($dir);
    }

    $file = $dir . '/import.log';

    $entry = [
        'time'    => current_time('mysql'),
        'message' => $message,
        'context' => $context,
    ];

    file_put_contents(
        $file,
        json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

// Back Button for Errors
function tcg_stats_back_button($label = '← Back')
{
    echo '<p style="margin-top: 1em;">
        <a href="' . esc_url(menu_page_url('tcg-stats', false)) . '" class="button button-secondary">
            ' . esc_html($label) . '
        </a>
    </p>';
}

// Insert Rows Helper
function tcg_stats_upsert_row(array $row, bool $allowGeekOverwrite = false)
{
    global $wpdb;
    $table = $wpdb->prefix . 'tcg_tournament_stats';

    $geekUpdateSql = $allowGeekOverwrite
        ? "geek_tag = VALUES(geek_tag)"
        : "geek_tag = IF(
            VALUES(geek_tag) IS NOT NULL AND VALUES(geek_tag) != '',
            VALUES(geek_tag),
            geek_tag
        )";


    $sql = "
        INSERT INTO {$table}
        (
            tcg, torneo, fecha, usuario, tcg_id,
            geek_tag, ranking, puntos, ganador,
            victorias, derrotas, omw
        )
        VALUES
        (
            %s, %d, %s, %s, %s,
            %s, %d, %d, %d,
            %d, %d, %f
        )
        ON DUPLICATE KEY UPDATE
            usuario = VALUES(usuario),
            {$geekUpdateSql},
            ranking   = VALUES(ranking),
            puntos    = VALUES(puntos),
            ganador   = VALUES(ganador),
            victorias = VALUES(victorias),
            derrotas  = VALUES(derrotas),
            omw       = VALUES(omw)
    ";

    return $wpdb->query(
        $wpdb->prepare(
            $sql,
            $row['tcg'],
            $row['torneo'],
            $row['fecha'],
            $row['usuario'],
            $row['tcg_id'] ?: null,
            $row['geek_tag'],
            $row['ranking'],
            $row['puntos'],
            $row['ganador'],
            $row['victorias'],
            $row['derrotas'],
            $row['omw']
        )
    );
}


function tcg_stats_upload_page()
{
    echo '<div style="margin-top: 3rem;" class="wrap">';
    echo '<h1>TCG Stats Import</h1>';

    // Handle POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Geek Id Overwrite
        $allowGeekOverwrite = !empty($_POST['allow_geek_overwrite']);

        // Security
        if (
            !isset($_POST['tcg_stats_nonce']) ||
            !wp_verify_nonce($_POST['tcg_stats_nonce'], 'tcg_stats_upload')
        ) {
            echo '<div class="notice notice-error"><p>Security check failed.</p></div>';
            echo '</div>';
            tcg_stats_back_button();
            return;
        }

        // TCG check
        if (empty($_POST['tcg'])) {
            echo '<div class="notice notice-error"><p>Please select a TCG.</p></div>';
            echo '</div>';
            tcg_stats_back_button();
            return;
        }

        $tcg = sanitize_text_field($_POST['tcg']);

        $allowed_tcgs = [
            'onepiece',
            'dragonball',
            'digimon',
            'pokemon',
            'lorcana',
            'starwars',
            'magic',
            'riftbound',
            'gundam',
        ];

        if (!in_array($tcg, $allowed_tcgs, true)) {
            echo '<div class="notice notice-error"><p>Invalid TCG selected.</p></div>';
            echo '</div>';
            tcg_stats_back_button();
            return;
        }


        // File check
        if (
            !isset($_FILES['tcg_stats_file']) ||
            $_FILES['tcg_stats_file']['error'] !== UPLOAD_ERR_OK
        ) {
            echo '<div class="notice notice-error"><p>File upload failed.</p></div>';
            echo '</div>';
            tcg_stats_back_button();
            return;
        }

        $file = $_FILES['tcg_stats_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($ext !== 'xlsx') {
            echo '<div class="notice notice-error"><p>Only XLSX files are allowed.</p></div>';
            echo '</div>';
            tcg_stats_back_button();
            return;
        }

        try {
            // Load spreadsheet
            $spreadsheet = IOFactory::load($file['tmp_name']);

            $allowedSheets = [
                'Torneo 1' => 1,
                'Torneo 2' => 2,
            ];

            $foundSheets = [];

            $errors = [];
            $validRows = [];
            $seen = [];
            $duplicateCount = 0;

            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                $sheetName = trim($sheet->getTitle());

                if (!isset($allowedSheets[$sheetName])) {
                    continue; // Ignore unrelated sheets
                }

                $torneo = $allowedSheets[$sheetName];
                $foundSheets[$sheetName] = true;

                $rows = $sheet->toArray(null, true, true, true);
                $headerRow = $rows[1] ?? [];

                $headers = [];
                foreach ($headerRow as $col => $value) {
                    $headers[$col] = strtolower(trim(str_replace('%', '', $value)));
                }



                $requiredColumns = [
                    'fecha',
                    'usuario',
                    'tcg id',
                    'victorias',
                    'derrotas',
                ];


                $optionalColumns = [
                    'ranking',
                    'geek tag',
                    'puntos',
                    'ganador torneo',
                    'omw',
                ];

                $missing = [];

                foreach ($requiredColumns as $required) {
                    if (!in_array($required, $headers, true)) {
                        $missing[] = $required;
                    }
                }

                if (!empty($missing)) {
                    echo '<div class="notice notice-error"><p>';
                    echo 'Missing required columns: ' . esc_html(implode(', ', $missing));
                    echo '</p></div>';
                    tcg_stats_back_button();
                    return;
                }

                $allColumns = array_merge($requiredColumns, $optionalColumns);

                $columnMap = [];
                foreach ($headers as $col => $name) {
                    if (in_array($name, $allColumns, true)) {
                        $columnMap[$name] = $col;
                    }
                }

                foreach ($rows as $rowIndex => $row) {
                    if ($rowIndex === 1) {
                        continue; // skip header
                    }

                    if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                        continue;
                    }

                    $record = [
                        'tcg' => $tcg,
                        'torneo' => $torneo,
                        'fecha' => trim($row[$columnMap['fecha']] ?? ''),
                        'usuario' => trim($row[$columnMap['usuario']] ?? ''),
                        'tcg_id' => trim($row[$columnMap['tcg id']] ?? ''),

                        'geek_tag' => isset($columnMap['geek tag'])
                            ? trim($row[$columnMap['geek tag']] ?? '') : '',

                        'ranking' => isset($columnMap['ranking'])
                            ? $row[$columnMap['ranking']] : null,

                        'puntos' => isset($columnMap['puntos'])
                            ? $row[$columnMap['puntos']] : null,

                        'ganador' => isset($columnMap['ganador torneo'])
                            ? trim($row[$columnMap['ganador torneo']] ?? '') : '',

                        'victorias' => $row[$columnMap['victorias']] ?? null,
                        'derrotas' => $row[$columnMap['derrotas']] ?? null,

                        'omw' => isset($columnMap['omw'])
                            ? $row[$columnMap['omw']] : null,
                    ];

                    // Basic validation
                    if ($record['fecha'] === '' || $record['usuario'] === '') {
                        $errors[] = "Sheet {$sheetName}, Row {$rowIndex}: Fecha and Usuario are required.";
                        continue;
                    }

                    if (!is_numeric($record['victorias']) || !is_numeric($record['derrotas'])) {
                        $errors[] = "Row {$rowIndex}: Victorias and Derrotas must be numeric.";
                        continue;
                    }

                    $record['ganador'] = is_numeric($record['ganador']) && (int)$record['ganador'] === 1 ? 1 : 0;
                    $record['ranking'] = is_numeric($record['ranking']) ? (int) $record['ranking'] : null;
                    $record['puntos']  = is_numeric($record['puntos']) ? (int) $record['puntos'] : null;
                    if (is_string($record['omw'])) {
                        $record['omw'] = str_replace('%', '', $record['omw']);
                    }

                    $record['omw'] = is_numeric($record['omw'])
                        ? (float) $record['omw']
                        : null;


                    $fechaCol  = $columnMap['fecha'];     // e.g. 'B'
                    $fechaCell = $fechaCol . $rowIndex;   // e.g. 'B2'

                    $cell = $sheet->getCell($fechaCell);

                    // ALWAYS trust the displayed value
                    $formatted = trim($cell->getFormattedValue());
                    $normalized = str_replace(['.', '-', ' '], '/', $formatted);

                    // Accept DD/MM/YY or DD/MM/YYYY
                    if (!preg_match('#^\d{1,2}/\d{1,2}/\d{2,4}$#', $normalized)) {
                        $errors[] = "Row {$rowIndex}: Fecha ({$formatted}) must be DD/MM/YY or DD/MM/YYYY.";
                        continue;
                    }

                    [$day, $month, $year] = array_map('intval', explode('/', $normalized));

                    // Expand 2-digit years explicitly (your rule)
                    if ($year < 100) {
                        $year += 2000;
                    }

                    // Final safety check
                    if (!checkdate($month, $day, $year)) {
                        $errors[] = "Row {$rowIndex}: Invalid Fecha ({$formatted}).";
                        continue;
                    }

                    // Store in ISO format
                    $record['fecha'] = sprintf('%04d-%02d-%02d', $year, $month, $day);




                    $key = strtolower(
                        $record['torneo'] . '|' .
                            $record['fecha'] . '|' .
                            $record['tcg_id']
                    );

                    if (isset($seen[$key])) {
                        $duplicateCount++;
                        tcg_stats_log('ROW_DUPLICATE_SKIPPED', $record);
                        continue;
                    }

                    $seen[$key] = true;

                    $validRows[] = $record;
                    tcg_stats_log('ROW_READY_FOR_IMPORT', $record);
                }
            }

            foreach ($allowedSheets as $name => $_) {
                if (!isset($foundSheets[$name])) {
                    echo '<div class="notice notice-error"><p>';
                    echo "Missing required sheet: {$name}";
                    echo '</p></div>';
                    tcg_stats_back_button();
                    return;
                }
            }

            tcg_stats_log('IMPORT_SUMMARY', [
                'tcg'         => $tcg,
                'valid_rows' => count($validRows),
                'errors'     => count($errors),
                'duplicates' => $duplicateCount,
            ]);

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo '<div class="notice notice-error"><p>' . esc_html($error) . '</p></div>';
                }
                tcg_stats_back_button();
                return;
            }

            echo '<div class="notice notice-info"><p>';
            echo 'Selected TCG: <strong>' . esc_html(ucfirst($tcg)) . '</strong>';
            echo '</p></div>';

            echo '<div class="notice notice-success"><p>';
            echo count($validRows) . ' valid rows ready for import.';
            echo '</p></div>';

            $inserted = 0;
            global $wpdb;

            foreach ($validRows as $row) {
                $result = tcg_stats_upsert_row($row, $allowGeekOverwrite);

                if ($result !== false) {
                    $inserted++;
                } else {
                    tcg_stats_log('DB_INSERT_FAILED', [
                        'error' => $wpdb->last_error,
                        'row'   => $row,
                    ]);
                }
            }

            echo '<div class="notice notice-success"><p>';
            echo "Imported {$inserted} rows into the database.";
            echo '</p></div>';


            // echo '<pre>';
            // print_r(array_slice($validRows, 0, 5));
            // echo '</pre>';



        } catch (Exception $e) {
            echo '<div class="notice notice-error"><p>';
            echo esc_html($e->getMessage());
            echo '</p></div>';
            echo '<p style="margin-top: 1em;">
                    <a href="' . esc_url(menu_page_url('tcg-stats', false)) . '" class="button button-secondary">
                        ← Back
                    </a>
                </p>';
        }
    }

    // Upload form
?>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('tcg_stats_upload', 'tcg_stats_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="tcg">TCG</label>
                </th>
                <td>
                    <select name="tcg" id="tcg" required>
                        <option value="">— Select TCG —</option>
                        <option value="onepiece">One Piece</option>
                        <option value="dragonball">Dragon Ball</option>
                        <option value="digimon">Digimon</option>
                        <option value="pokemon">Pokémon</option>
                        <option value="lorcana">Lorcana</option>
                        <option value="starwars">Star Wars</option>
                        <option value="magic">Magic</option>
                        <option value="riftbound">RiftBound</option>
                        <option value="gundam">Gundam</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="tcg_stats_file">XLSX file</label>
                </th>
                <td>
                    <input type="file" name="tcg_stats_file" id="tcg_stats_file" accept=".xlsx" required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="allow_geek_overwrite">Geek ID</label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="allow_geek_overwrite" value="1">
                        Permitir que se sobrescriban los Geek ID (admin only)
                    </label>
                    <p class="description">
                        Usar solamente para corregir errores.
                    </p>
                </td>
            </tr>

        </table>

        <?php submit_button('Upload'); ?>
    </form>

    </div>
<?php
}
