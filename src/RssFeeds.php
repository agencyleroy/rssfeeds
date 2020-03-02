<?php
/**
 * Rss Feeds plugin for Craft CMS 3.x
 *
 * RSS feed from rss.app
 *
 * @link      https://agencyleroy.com
 * @copyright Copyright (c) 2020 Agency leroy
 */

namespace agencyleroy\rssfeeds;

use agencyleroy\rssfeeds\services\RssFeedsService as RssFeedsServiceService;
use agencyleroy\rssfeeds\variables\RssFeedsVariable;
use agencyleroy\rssfeeds\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class RssFeeds
 *
 * @author    Agency leroy
 * @package   RssFeeds
 * @since     1.0.0
 *
 * @property  RssFeedsServiceService $rssFeedsService
 */
class RssFeeds extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var RssFeeds
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Constants
    // =========================================================================

    /**
     * Plugin name
     */
    const PLUGIN_NAME = 'Rss feeds';
    
    /**
     * Database Table name for SiteSettings records
     */
    const SITE_SETTINGS_TABLE = '{{%rss_feeds_site_settings}}';

    /**
     * Default feed url
     */
    const DEFAULT_FEED_URL = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->installCpEventListeners();

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('rssFeeds', RssFeedsVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'rss-feeds',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'rss-feeds/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }


    /**
     * Install CP Event Listeners
     */
    protected function installCpEventListeners()
    {
      Event::on(
        UrlManager::class,
        UrlManager::EVENT_REGISTER_CP_URL_RULES,
        function (RegisterUrlRulesEvent $event) {
          Craft::debug('Loaded RSS Feeds CP Routes', 'rss-feeds');
          $event->rules = array_merge(
            $event->rules,
            $this->customAdminCpRoutes()
          );
        }
      );
    }

    /**
     * Return the custom Control Panel routes
     *
     * @return array
     */
    protected function customAdminCpRoutes(): array
    {
      return [
        'rss-feeds' 													=>	'rss-feeds/settings/index',
        'rss-feeds/site/<siteHandle:{handle}>'							=> 	'rss-feeds/settings/edit-site-settings',
      ];
    }
}
