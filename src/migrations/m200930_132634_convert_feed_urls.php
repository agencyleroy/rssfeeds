<?php

namespace agencyleroy\rssfeeds\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\Json;
use agencyleroy\rssfeeds\records\SiteSettings;

/**
 * m200930_132634_convert_feed_urls migration.
 */
class m200930_132634_convert_feed_urls extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $siteSettings = SiteSettings::find()->all();

        foreach ($siteSettings as $siteSetting) {
            $feedUrls = $siteSetting->feedUrls;
            if(!!$feedUrls) {
                $feedUrls = self::convertToArray($feedUrls);
            }

            $siteSetting->feedUrls = $feedUrls;
            $siteSetting->save();
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m200930_132634_convert_feed_urls cannot be reverted.\n";
        return false;
    }

    private static function isJsonObject(string $str): bool
    {
        return (bool)preg_match('/^(?:\{.*\}|\[.*\])$/s', $str);
    }


    private static function convertToArray($feedUrls)
    {
        if (!!$feedUrls && !self::isJsonObject($feedUrls)) {
            $feedUrls = Json::decode($feedUrls);
            self::convertToArray($feedUrls);
        }

        return Json::decode($feedUrls);
    }
}
