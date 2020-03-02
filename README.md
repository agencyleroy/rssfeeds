# Rss Feeds plugin for Craft CMS 3.x

RSS feed from rss.app

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require agencyleroy/rss-feeds

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Rss Feeds.

## Rss Feeds Overview

-Insert text here-

## Configuring Rss Feeds

-Insert text here-

## Using Rss Feeds


### Individual feed
<pre>
  {% for item in craft.rssFeeds.findFeed('instagram') %}
    {{ item.serviceName }}
    {{ item.authorTitle }}
    {{ item.authorLink }}
    {{ item.feedTitle }}
    {{ item.feedLink }}
    {{ item.feedPubDate }}
    {{ item.timestamp }}
    {{ item.feedImage }}
  {% endfor %}
</pre>

### All enabled feed
<pre>
{% set feeds = craft.rssFeeds.findFeed() %}
{% for item in feeds %}
  {{ item.serviceName }}
  {{ item.authorTitle }}
  {{ item.authorLink }}
  {{ item.feedTitle }}
  {{ item.feedLink }}
  {{ item.feedPubDate }}
  {{ item.timestamp }}
  {{ item.feedImage }}
{% endfor %}
</pre>

## Rss Feeds Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [Agency leroy](https://agencyleroy.com)
