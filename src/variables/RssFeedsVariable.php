<?php
/**
 * Rss Feeds plugin for Craft CMS 3.x
 *
 * RSS feed from rss.app
 *
 * @link      https://agencyleroy.com
 * @copyright Copyright (c) 2020 Agency leroy
 */

namespace agencyleroy\rssfeeds\variables;

use agencyleroy\rssfeeds\RssFeeds;

use Craft;
use craft\helpers\Json;

/**
 * @author    Agency leroy
 * @package   RssFeeds
 * @since     1.0.0
 */
class RssFeedsVariable
{
  // Public Methods
  // =========================================================================

  /**
   * HOW TO USE
   * 
   * 
    // Individual feed
    <pre>
      {% for item in craft.rssFeeds.findFeed('instagram') %}
        {{item.serviceName}}
        {{item.authorTitle}}
        {{item.authorLink}}
        {{item.feedTitle}}
        {{item.feedLink}}
        {{item.feedPubDate}}
        {{item.timestamp}}
        {{item.feedImage}}
      {% endfor %}
    </pre>

    // All enabled feed
    {% set feeds = craft.rssFeeds.findFeed() %}
    <pre>
      {% for item in feeds %}
        {{item.serviceName}}
        {{item.authorTitle}}
        {{item.authorLink}}
        {{item.feedTitle}}
        {{item.feedLink}}
        {{item.feedPubDate}}
        {{item.timestamp}}
        {{item.feedImage}}
      {% endfor %}
    </pre>

   */
  public function findFeed()
  {
      return Json::decodeIfJson(RssFeeds::getInstance()->rssFeedsService->findFeed());
  }
}
