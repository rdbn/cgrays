<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.02.2018
 * Time: 10:34
 */

namespace ProcessingBundle\Metrics\Event;

class PickUpSkinsEvent
{
    const ODKU_SQL = "
    INSERT INTO statistic_cases (date_at, user_id, cases_id, cases_domain_id, cases_category_id, pick_up_skins) VALUES (
      %s
    ) ON CONFLICT (date_at, user_id, cases_id, cases_domain_id, cases_category_id) DO UPDATE SET
      pick_up_skins = statistic_cases.pick_up_skins + 1;
    ";

    /**
     * @param array $data
     * @return string
     */
    public static function handle(array $data)
    {
        $data['pick_up_skins'] = 1;

        return sprintf(self::ODKU_SQL, implode(",", $data));
    }
}