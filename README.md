# Rss Feeds
## Rss Feeds plugin for Craft CMS 3.x

Working with rss feed from rss.app
(Single, multiple feeds are supported as well as bundles)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Using Rss Feeds

1. Create a feed in <a href="https://rss.app/" target="_blank">rss.app</a>. Adjust the feed settings if needed.
2. Copy the Feed Url.
3. In the plugin settings, you can
  1. Activate/Deactivate the whole feed
  2. Add multiple feeds in the table
    - Feed type: Choose what services the feed is getting data from. This should be the same as the one in rss.app. If you want to get a feed from the multiple service providers, Make a feed on rss.app with bundle type.
    - Feed Url: The feed's url from rss.app
    - Activate/Deactivate the selected row

### To select an individual feed
4. Create a field from `/admin/settings/fields` with `socialFeed` field type
5. Select a feed to display in the frontend
6. `{% set feed = craft.rssFeeds.findFeedByUrl(entry.feed) %}` to get the type of the feed and the array of the feed

## Available functions
- findFeed($serviceName = null): Returns an array of the feed items. Service provider can be specified in the parameter which should be chosen from the feed type. Default is null.
  #### Available Properties
  - serviceName: Displays the service provider's name
  - authorTitle: Displays the author's title. This can be changes in rss.app
  - authorLink: Link to the author's feed in the provider
  - feedTitle: Displays the title or the description of the feed item
  - feedLink: Link to the feed item
  - feedPubDate: Datetime object of the time when the feed item is published.
  - timestamp: Timestamp of when the feed item is published
  - feedImage: Url of the image in the feed item
- isActive(): Returns boolean; whether the whole feed is activated.

## Twig code examples
### How to find individual feeds
```
<pre>
  {% for item in craft.rssFeeds.findFeed('instagram') %}
    {{ item.serviceName }}
    {{ item.authorTitle }}
    {{ item.authorLink }}
    {{ item.feedTitle }}
    {{ item.feedLink }}
    {{ item.feedPubDate|date('d.m.y') }}
    {{ item.timestamp }}
    {{ item.feedImage }}
  {% endfor %}
</pre>
```

### After creating the socialFeed field type and fetching the details on the frontend(Twig)
```
<pre>
  {% set socialfeed = craft.rssFeeds.findFeedByUrl(entry.feed) %}
  {% set feedArray = socialfeed.feed|slice(0,4) %}
  {% for feed in feedArray %}
    {% if feed.type == 'twitter' %}
      {# Frontend code for twitter feed #}
    {% else if feed.type == 'intagram' %}
      {# Frontend code for instagram feed #}
    {% else %}
      {# Frontend code for default feed #}
    {% endif %}
  {% endfor %}
</pre>
```

### How to find all enabled feed
```
<pre>
{% set feeds = craft.rssFeeds.findFeed() %}
{% for item in feeds %}
  ...
{% endfor %}
</pre>
```

## Installation with Composer

Install the plugin by running:

```console
foo@bar:~$ composer require agencyleroy/rss-feeds
```

Brought to you by [Agency leroy](https://agencyleroy.com)
