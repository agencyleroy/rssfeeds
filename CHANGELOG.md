# Rss Feeds Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.0.0 - 2020-02-18
### Added
- Initial release

## 1.0.4 - 2020-08-20
### Fixing bugs
- Fix form resubmission modal popping up
- Fix invalid json error
- Add a fallback parameter for findFeed()
### New variable
- Setting a feed only with selected service name is now possible: findFeed('instagram')
- Checking if the feed is activated from the plugin by using: isActive()