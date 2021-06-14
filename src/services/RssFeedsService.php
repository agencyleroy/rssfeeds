<?php
/**
 * Rss Feeds plugin for Craft CMS 3.x
 *
 * RSS feed from rss.app
 *
 * @link      https://agencyleroy.com
 * @copyright Copyright (c) 2020 Agency leroy
 */

namespace agencyleroy\rssfeeds\services;

use DateTime;
use DateTimeZone;
use agencyleroy\rssfeeds\RssFeeds;
use agencyleroy\rssfeeds\records\SiteSettings;

use Craft;
use craft\base\Component;
use craft\helpers\Json;
use craft\helpers\ArrayHelper;

/**
 * @author    Agency leroy
 * @package   RssFeeds
 * @since     1.0.0
 */
class RssFeedsService extends Component
{
    // Public Methods
    // =========================================================================
    private $settings;
    private $feedUrls;
    private $currentSiteId;
    private $activated;

    /**
     * Initiate the plugin for this site
     */
    public function init()
    {
      $this->currentSiteId = Craft::$app->getSites()->currentSite->id;
      $this->settings = SiteSettings::find()->where(['site_id' => $this->currentSiteId])->one();
      if($this->settings) {
        $this->activated = $this->settings->activated;
        $this->feedUrls = Json::decode($this->settings->feedUrls);
      }
      parent::init();
    }

    public function isActive()
    {
      return $this->activated;
    }

    /**
     * @return FormModel[]
     */
    public function getAllFeeds(): array
    {
        $feeds = $this->feedUrls;
        $selectedFeeds = [];
        
        foreach ($feeds as $i => $feed) {
          if ($feed['activated']) {
            $selectedFeeds[$feed['url']] = $feed;
          }
        }

        return $selectedFeeds;
    }

    public function findFeedByUrl($feedUrl)
    {
      if(!$this->activated) {
        return null;
      }

      $feeds = $this->feedUrls;
      $selectedFeeds = [];

      $selectedFeed = ArrayHelper::firstWhere($feeds, 'url', $feedUrl);

      if ($selectedFeed && $selectedFeed['activated']) {
        $selectedFeeds[$selectedFeed['name']] = $selectedFeed;
      }

      $handles = array_column($selectedFeeds, 'name');
      $feedHandle = 'feed-'.$this->currentSiteId.'-'.implode('_', $handles);
      $data = Craft::$app->cache->get($feedHandle);
      // $data = '';

      if (!$data) {
        if ($data = $this->_fetchFeed($selectedFeeds)) {
          if (!Craft::$app->cache->set($feedHandle, $data,  1440 )) {
            Craft::error("Could not write to cache");
          }
          return $data;
        }
        return null;
      }

      $feedDetail['type'] = $selectedFeed['name'];
      $feedDetail['feed'] = $data;

      return $feedDetail;
    }

    public function findFeed($serviceName = null)
    {
      if(!$this->activated) {
        return null;
      }

      $feeds = $this->feedUrls;
      $selectedFeeds = [];

      if ($serviceName !== null) {
        //If services is defined
        $selectedFeed = ArrayHelper::firstWhere($feeds, 'name', $serviceName);

        if ($selectedFeed && $selectedFeed['activated']) {
          $selectedFeeds[$serviceName] = $selectedFeed;
        }
      } else {
        foreach ($feeds as $i => $feed) {
          if ($feed['activated']) {
            $selectedFeeds[$feed['name']] = $feed;
          }
        }
      }

      $handles = array_column($selectedFeeds, 'name');
      $feedHandle = 'feed-'.$this->currentSiteId.'-'.implode('_', $handles);
      $data = Craft::$app->cache->get($feedHandle);
      // $data = '';

      if (!$data) {
        if ($data = $this->_fetchFeed($selectedFeeds)) {
          if (!Craft::$app->cache->set($feedHandle, $data,  1440 )) {
            Craft::error("Could not write to cache");
          }
          return $data;
        }
        return null;
      }

      return $data;
    }

    /**
     * @return mixed
     */
    public function _fetchFeed($selectedFeeds = [])
    {
      $feedItems = [];
      foreach ($selectedFeeds as $i => $selectedFeed) {
        $serviceName = $selectedFeed['name'];
        $url = $selectedFeed['url'];
        $xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);

        $channel = $xml->channel[0];
        // $xml = simplexml_load_string($xml_string);
        $json = Json::decodeIfJson(json_encode($channel));

        if($json) {
          $items = $json['item'];

          foreach ($items as $j => $item) {

            $feedItem = new \stdClass();

            if($serviceName === 'bundle') {
              $pattern = '/https\:\/\/(www\.)?(.*?)\.com\//';
              preg_match($pattern, $item['link'], $matches);

              $feedServiceName = sizeof($matches) > 0 ? $matches[2] : '';

              $authorTitle = strip_tags($channel->item[$j]->children("dc", true)->asXML());
              $authorLink = '';
            } else {
              $feedServiceName = $i;
              $authorTitle = $json['title'];
              $authorLink = $json['link'];
            }

            $feedItem->serviceName = $feedServiceName;
            $feedItem->authorTitle = $authorTitle;
            $feedItem->authorLink = $authorLink;
            $feedItem->feedTitle = $item['title'];
            $feedItem->feedDescription = $item['description'];
            $feedItem->feedLink = $item['link'];
            $feedItem->feedPubDate = date_create_from_format("D, d M Y H:i:s O", $item['pubDate']);
            $feedItem->timestamp = date_timestamp_get($feedItem->feedPubDate);
            $feedItem->feedImage = isset($item['enclosure']) ? $item['enclosure']['@attributes']['url'] : null;

            $feedItems[] = $feedItem;
          }
        }
      }

      $json = $this->_processFeed($feedItems);

      return $json;
    }

    public function _processFeed($feedItems) {
      usort($feedItems, function($a, $b) {
        $ad = $a->feedPubDate;
        $bd = $b->feedPubDate;

        if ($ad == $bd) {
          return 0;
        }

        return $ad > $bd ? -1 : 1;
      });

      return $feedItems;
    }
}
