# Rss Feeds plugin for Craft CMS 3.x

Working with rss feed from rss.app
(Single, multiple feeds are supported as well as bundles)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Using Rss Feeds


### How to find individual feeds
```twig
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
```

### How to find all enabled feed
```twig
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
```

## Installation with Composer

Install the plugin by running:

```console
foo@bar:~$ composer require agencyleroy/rss-feeds
```

Brought to you by [Agency leroy](https://agencyleroy.com)
