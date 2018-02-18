<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.02.2018
 * Time: 10:34
 */

namespace ProcessingBundle\Metrics\Event;

class HitCasesEvent
{
    const ODKU_SQL = "
    INSERT INTO statistic_cases (date_at, user_id, cases_id, cases_domain_id, cases_category_id, hit_cases) VALUES (
      %s
    ) ON CONFLICT (date_at, user_id, cases_id, cases_domain_id, cases_category_id) DO UPDATE SET
      hit_cases = statistic_cases.hit_cases + 1;
    ";

    /**
     * @param array $data
     * @return string
     */
    public static function handle(array $data)
    {
        $data['hit_cases'] = 1;

        return sprintf(self::ODKU_SQL, implode(",", $data));
    }
}