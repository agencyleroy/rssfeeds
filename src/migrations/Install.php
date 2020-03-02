<?php

namespace agencyleroy\rssfeeds\migrations;

use Craft;
use craft\db\Migration;
Use craft\db\Table;
use agencyleroy\rssfeeds\RssFeeds;

/**
 * Install migration.
 */
class Install extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp()
	{
		// Site Settings
		$this->createTable(
			RssFeeds::SITE_SETTINGS_TABLE,
			[
				'site_id' => $this->integer(11),
				'dateCreated' => $this->dateTime()->notNull(),
				'dateUpdated' => $this->dateTime()->notNull(),
				'uid'         => $this->uid(),
				'activated' => $this->boolean()->notNull()->defaultValue(false),
				'feedUrls' => $this->text()->notNull()
			]
		);
		$this->addPrimaryKey(
			'pk_rss_feeds_site_settings',
			RssFeeds::SITE_SETTINGS_TABLE,
			'site_id'
		);
		$this->addForeignKey(
			'fk_rss_feeds_setting_belong_to_site',
			RssFeeds::SITE_SETTINGS_TABLE,
			'site_id',
			Table::SITES,
			'id',
            'CASCADE',
            'CASCADE'
		);

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown()
	{
		$this->dropTableIfExists(RssFeeds::SITE_SETTINGS_TABLE);
		return true;
	}
}
