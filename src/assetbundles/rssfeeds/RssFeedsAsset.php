<?php
/**
 * Rss Feeds plugin for Craft CMS 3.x
 *
 * RSS feed from rss.app
 *
 * @link      https://agencyleroy.com
 * @copyright Copyright (c) 2020 Agency leroy
 */

namespace agencyleroy\rssfeeds\assetbundles\RssFeeds;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Agency leroy
 * @package   RssFeeds
 * @since     1.0.0
 */
class RssFeedsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@agencyleroy/rssfeeds/assetbundles/rssfeeds/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/RssFeeds.js',
        ];

        $this->css = [
            'css/RssFeeds.css',
        ];

        parent::init();
    }
}
