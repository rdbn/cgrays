<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.02.2018
 * Time: 10:34
 */

namespace ProcessingBundle\Statistic;

class SellSkinsEvent
{
    const ODKU_SQL = "
    INSERT INTO statistic_cases (date_at, user_id, cases_id, cases_domain_id, cases_category_id, sell_skins) VALUES (
      %s
    ) ON CONFLICT (date_at, user_id, cases_id, cases_domain_id, cases_category_id) DO UPDATE SET
      sell_skins = statistic_cases.sell_skins + 1;
    ";

    /**
     * @param array $data
     * @return string
     */
    public static function handle(array $data)
    {
        $data['sell_skins'] = $data['price'];

        return sprintf(self::ODKU_SQL, implode(",", $data));
    }
}