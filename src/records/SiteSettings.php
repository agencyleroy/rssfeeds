<?php


namespace agencyleroy\rssfeeds\records;

use Craft;
use craft\db\ActiveRecord;
use craft\records\Site;
use craft\helpers\Json;
use agencyleroy\rssfeeds\RssFeeds;
use yii\db\ActiveQueryInterface;
use yii\web\NotFoundHttpException;

/**
 * @property boolean    $activated
 * @property string $feedUrls
 */
class SiteSettings extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = [
            'site_id',
            'activated',
            'feedUrls'
        ];
        return array_merge($fields, parent::fields());
    }

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return RssFeeds::SITE_SETTINGS_TABLE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedUrls'], 'string'],
            [['feedUrls'], 'required'],
            [['activated'], 'boolean'],
            [['activated', 'feedUrls'], 'validatePermission'],
            ['activated', 'default', 'value' => 0],
            ['site_id', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'site_id' => Craft::t('rss-feeds', 'Site ID'),
            'activated' => Craft::t('rss-feeds', 'Activated'),
            'feedUrls' => Craft::t('rss-feeds', 'Feed Urls')
        ];
    }

    /**
     *
     */
    public function permissions()
    {
        return [
            'activated' => 'rss-feeds:site-settings:activate',
            'feedUrls' => 'rss-feeds:site-settings:feedUrls'
        ];
    }

    /**
     *
     */
    public function validatePermission($attribute, $params)
    {
        $attribute_to_permission = $this->permissions();
        if (in_array($attribute, array_keys($this->getDirtyAttributes())) && !Craft::$app->user->checkPermission($attribute_to_permission[$attribute])) {
            $this->addError($attribute, Craft::t('rss-feeds', 'You do not have permission to change this attribute'));
        }
    }

    /**
     *
     */
    public function getLastUpdate()
    {
        $dates = [];
        if (isset($this->dateUpdated)) $dates[] = strtotime($this->dateUpdated);
        return max($dates);
    }

    /**
     * Returns the URL to edit this record
     *
     * @return int|null|string
     * @throws NotFoundHttpException
     */
    public function getEditUrl()
    {
        if ($this->site_id) {
            return 'rss-feeds/site/'.$this->getSiteHandleFromId($this->site_id);
        }
        return 'rss-feeds';
    }

    /**
     *
     */
    public function getSite(): ActiveQueryInterface
    {
        return $this->hasOne(Site::class, ['id' => 'site_id']);
    }

    /**
     * Return a siteHandle from a siteId
     *
     * @param string $siteId
     *
     * @return int|null
     * @throws NotFoundHttpException
     */
    protected function getSiteHandleFromId($siteId)
    {
        if ($siteId !== null) {
            $site = Craft::$app->getSites()->getSiteById($siteId);
            if (!$site) {
                throw new NotFoundHttpException('Invalid site ID: '.$siteId);
            }
            $siteHandle = $site->handle;
        } else {
            $siteHandle = Craft::$app->getSites()->currentSite->handle;
        }

        return $siteHandle;
    }
}
