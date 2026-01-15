<?php

namespace TCGStats\Models;

use Illuminate\Support\Carbon;

class TournamentStat
{
    protected static function table()
    {
        global $wpdb;
        return $wpdb->prefix . 'tcg_tournament_stats';
    }

    /** Torneos Mensuales */
    public static function torneos(string $tcg, string $mes): array
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    %s AS mes,
                    torneo,
                    geek_tag,
                    usuario AS jugador,
                    SUM(puntos) AS puntos,
                    ROUND(AVG(omw), 0) AS omw
                FROM " . self::table() . "
                WHERE tcg = %s
                AND LEFT(fecha, 7) = %s
                GROUP BY torneo, tcg_id
                ORDER BY torneo ASC, puntos DESC, omw DESC
                ",
                $mes,
                $tcg,
                $mes
            ),
            ARRAY_A
        );
    }

    /** Leaderbord Global */
    public static function global(string $tcg, string $mes): array
    {
        global $wpdb;

        $semester = tcg_semester_for_month($tcg, $mes);

        if (!$semester) {
            return [];
        }

        $start = $semester['start']->toDateString();
        $end   = $semester['end']->toDateString();

        $table = self::table();


        return $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    %s AS semestre,
                    t.tcg_id,
                    u.usuario AS jugador,
                    g.geek_tag AS geek_tag,
                    SUM(t.puntos) AS puntos,
                    SUM(t.ganador) AS torneos_ganados,
                    SUM(t.victorias) AS total_victorias,
                    ROUND(AVG(t.omw), 0) AS omw
                FROM " . $table . " t

                /* Latest username */
                INNER JOIN (
                    SELECT tcg_id, MAX(fecha) AS last_fecha
                    FROM " . $table . "
                    WHERE tcg = %s
                    AND fecha BETWEEN %s AND %s
                    GROUP BY tcg_id
                ) latest_name
                    ON latest_name.tcg_id = t.tcg_id

                INNER JOIN " . $table . " u
                    ON u.tcg_id = latest_name.tcg_id
                AND u.fecha = latest_name.last_fecha

                /* Latest non-null geek_tag */
                LEFT JOIN (
                    SELECT tcg_id, geek_tag
                    FROM " . $table . " gt1
                    WHERE geek_tag IS NOT NULL
                    AND geek_tag != ''
                    AND fecha = (
                        SELECT MAX(gt2.fecha)
                        FROM " . $table . " gt2
                        WHERE gt2.tcg_id = gt1.tcg_id
                            AND gt2.geek_tag IS NOT NULL
                            AND gt2.geek_tag != ''
                    )
                ) g
                    ON g.tcg_id = t.tcg_id

                WHERE t.tcg = %s
                AND t.fecha BETWEEN %s AND %s
                GROUP BY t.tcg_id
                ORDER BY puntos DESC, omw DESC
                ",
                $start,
                $tcg,
                $start,
                $end,
                $tcg,
                $start,
                $end
            ),
            ARRAY_A
        );
    }
}
