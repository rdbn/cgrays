<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.02.2018
 * Time: 10:34
 */

namespace ProcessingBundle\Metrics\Event;

class OpenCasesEvent
{
    const ODKU_SQL = "
    INSERT INTO statistic_cases (date_at, user_id, cases_id, cases_domain_id, cases_category_id, open_cases) VALUES (
      %s
    ) ON CONFLICT (date_at, user_id, cases_id, cases_domain_id, cases_category_id) DO UPDATE SET
      open_cases = statistic_cases.open_cases + 1;
    ";

    /**
     * @param array $data
     * @return string
     */
    public static function handle(array $data)
    {
        $data['open_cases'] = 1;

        return sprintf(self::ODKU_SQL, implode(",", $data));
    }
}