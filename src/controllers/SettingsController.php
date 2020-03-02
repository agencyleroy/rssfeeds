<?php

namespace agencyleroy\rssfeeds\controllers;

use Craft;
use craft\web\Controller;
use agencyleroy\rssfeeds\RssFeeds;
use agencyleroy\rssfeeds\records\SiteSettings;
use yii\web\NotFoundHttpException;
use craft\helpers\UrlHelper;

class SettingsController extends Controller
{
	public function actionIndex()
	{
		$variables = [
			'content' => file_get_contents(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'README.md'),
			'currentPage' => 'readme',
			'site' => Craft::$app->getSites()->currentSite
		];
		$this->_prepVariables($variables);
		$variables['currentPage'] = 'readme';
		$variables['title'] = Craft::t('rss-feeds', 'Readme');
		$this->_prepSiteSettingsPermissionVariables($variables);
		return $this->renderTemplate('rss-feeds/settings/index', $variables);
	}

	/**
	 * Render the view for editing SiteSettings
	 *
	 * @param string|null       $siteHandle
	 * @param SiteSettings|null $model
	 *
	 * @return \yii\web\Response
	 * @throws \yii\web\ForbiddenHttpException
	 */
	public function actionEditSiteSettings(string $siteHandle = null, SiteSettings $model = null)
	{
		$this->requirePermission('rss-feeds:site-settings');

		Craft::$app->getRequest();
		$variables = [
			'currentSiteHandle' => $siteHandle,
			'model' => $model
		];
		$this->_prepVariables($variables);
		$variables['currentPage'] = 'site';
		$variables['title'] = Craft::t('rss-feeds', 'Site Settings');
		$this->_checkSiteEditPermission($variables['currentSiteId']);
		$this->_prepSiteSettingsPermissionVariables($variables);

		return $this->renderTemplate('rss-feeds/settings/site', $variables);
	}

	/**
	 * Save site settings
	 *
	 * @return null
	 * @throws NotFoundHttpException
	 * @throws \craft\errors\MissingComponentException
	 * @throws \yii\web\BadRequestHttpException
	 * @throws \yii\web\ForbiddenHttpException
	 */
	public function actionSaveSiteSettings()
	{
		$this->requirePostRequest();

		$this->_checkSiteEditPermission(Craft::$app->request->post('site_id'));

		$record = SiteSettings::findOne(Craft::$app->request->post('site_id'));
		if(!$record) {
			throw new NotFoundHttpException('Settings for site not found');
		}
		$record->load(Craft::$app->request->post(), '');
		$record->activated = (int) $record->activated;
		if($record->save()) {
			Craft::$app->getSession()->setNotice(Craft::t('rss-feeds', 'Settings saved.'));
		}
		else {
			Craft::$app->getUrlManager()->setRouteParams([
				'model' => $record
			]);
			Craft::$app->getSession()->setError(Craft::t('rss-feeds', 'Couldn’t save the settings.'));
		}
		return null;
	}

	/**
	 * Return a siteId from a siteHandle
	 *
	 * @param string $siteHandle
	 *
	 * @return int
	 * @throws NotFoundHttpException
	 */
	protected function getSiteIdFromHandle($siteHandle) : int
	{
		if ($siteHandle !== null) {
			$site = Craft::$app->getSites()->getSiteByHandle($siteHandle);
			if (!$site) {
				throw new NotFoundHttpException('Invalid site handle: '.$siteHandle);
			}
			$siteId = $site->id;
		} else {
			$siteId = Craft::$app->getSites()->currentSite->id;
		}

		return $siteId;
  }
  
	/**
	 * Populate SiteSettings record with
	 * default data
	 *
	 * @param SiteSettings $record
	 */
	protected function insertDefaultRecord(SiteSettings &$record)
	{
		$record->feedUrls = RssFeeds::DEFAULT_FEED_URL;
		$record->activated = false;
		$record->save(false);
	}


	/**
	 * Prepare twig variables for Site Settings
	 *
	 * @param array $variables
	 */
	private function _prepVariables(array &$variables)
	{
		if(empty($variables['currentSiteHandle']))
		{
			$variables['site'] = Craft::$app->getSites()->currentSite;
			$variables['currentSiteId'] = $variables['site']->id;
			$variables['currentSiteHandle'] = $variables['site']->handle;
		}
		else {
			$variables['site'] = Craft::$app->sites->getSiteByHandle($variables['currentSiteHandle']);
			$variables['currentSiteId'] = $variables['site']->id;
		}
		if (empty($variables['model'])) {
			$variables['model'] = SiteSettings::findOne($variables['currentSiteId']);
			if (!$variables['model']) {
				$variables['model'] = new SiteSettings();
				$variables['model']->site_id = $variables['currentSiteId'];
				$this->insertDefaultRecord($variables['model']);
			}
		}

		$variables['fullPageForm'] = true;
		$variables['crumbs'] = [
			[
				'label' => RssFeeds::PLUGIN_NAME,
				'url' => UrlHelper::cpUrl('rss-feeds'),
			],
			[
				'label' => $variables['site']->name,
				'url' => UrlHelper::cpUrl('rss-feeds/site/'.$variables['site']->handle),
			]
		];
	}


	/**
	 * Check if the user can edit the current site
	 *
	 * @param int $siteId
	 *
	 * @throws \yii\web\ForbiddenHttpException
	 */
	private function _checkSiteEditPermission(int $siteId)
	{
		if (Craft::$app->getIsMultiSite()) {

			$variables['editableSites'] = Craft::$app->getSites()->getEditableSiteIds();

			if (!\in_array($siteId, $variables['editableSites'], false)) {
					$this->requirePermission('editSite:'.$siteId);
			}
		}
	}

	private function _prepSiteSettingsPermissionVariables(array &$variables)
	{
		$variables['canActivate'] = Craft::$app->user->checkPermission('rss-feeds:site-settings:activate');
		$variables['canChangeFeedUrls'] = Craft::$app->user->checkPermission('rss-feeds:site-settings:feedUrls');
		$variables['canUpdate'] = Craft::$app->user->checkPermission('rss-feeds:site-settings:content');
	}
}