<?php

declare(strict_types=1);

namespace Jajuma\PotMatomoReports\ViewModel;

use Jajuma\MatomoAnalytics\Helper\Data;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Matomo
 * @package Jajuma\PotMatomoReports\ViewModel
 */
class Matomo implements ArgumentInterface
{

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var Http
     */
    protected $request;

    /**
     * Matomo constructor.
     * @param Data $dataHelper
     * @param EncryptorInterface $encryptor
     * @param Http $request
     */
    public function __construct(
        Data $dataHelper,
        EncryptorInterface $encryptor,
        Http $request
    ) {
        $this->_encryptor = $encryptor;
        $this->dataHelper = $dataHelper;
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function validateMatomoConfig()
    {
        $storeId = $this->request->getParam('store') ? $this->request->getParam('store') : null;
        $siteUrl = $this->dataHelper->getSiteUrl($storeId);
        $siteId = $this->dataHelper->getSiteId($storeId);
        $accessToken = $this->dataHelper->getViewAccessToken($storeId);
        $accessToken = $this->_encryptor->decrypt($accessToken);

        if (empty($siteUrl) || empty($siteId) || empty($accessToken)) {
            return false;
        }

        return true;
    }

    /**
     * @param $widgetType
     * @return bool|string
     */
    public function getWidgetUrl($widgetType)
    {
        $storeId = $this->request->getParam('store') ? $this->request->getParam('store') : null;
        $siteUrl = $this->dataHelper->getSiteUrl($storeId);
        $siteId = $this->dataHelper->getSiteId($storeId);
        $accessToken = $this->dataHelper->getViewAccessToken($storeId);
        $accessToken = $this->_encryptor->decrypt($accessToken);
        $period = $this->dataHelper->getPeriod($storeId) ?: 'day';

        if (empty($siteUrl) || empty($siteId) || empty($accessToken)) {
            return false;
        }

        $widgetUrl = $siteUrl . '?module=Widgetize&action=iframe&disableLink=1&widget=1';
        switch ($widgetType) {
            case 'UserCountryMap':
            case 'Live':
                $widgetUrl .= '&widget=1&moduleToWidgetize=' . $widgetType . '&actionToWidgetize=' . $this->getActionToWidgetize($widgetType);
                break;
            case 'VisitsSummary':
                $widgetUrl .= '&widget=1&moduleToWidgetize=' . $widgetType . '&actionToWidgetize=' . $this->getActionToWidgetize($widgetType) . '&forceView=1&viewDataTable=graphEvolution';
                break;
            case 'CoreHome':
                $widgetUrl .= '&widget=1&moduleToWidgetize=' . $widgetType . '&actionToWidgetize=' . $this->getActionToWidgetize($widgetType) . '&containerId=VisitOverviewWithGraph';
                break;
        }

        $widgetUrl .= '&idSite=' . $siteId . '&period=' . $period . '&date=yesterday&token_auth=' . $accessToken;
        return $widgetUrl;
    }

    /**
     * @param $moduleToWidgetize
     * @return mixed|null
     */
    public function getActionToWidgetize($moduleToWidgetize)
    {
        $actionToWidgetize = null;
        foreach ($this->getSupportWidgetTypes() as $supportWidgetType) {
            if ($supportWidgetType['moduleToWidgetize'] == $moduleToWidgetize) {
                $actionToWidgetize = $supportWidgetType['actionToWidgetize'];
            }
        }

        return $actionToWidgetize;
    }

    /**
     * @return array[]
     */
    public function getSupportWidgetTypes()
    {
        return [
            [
                'moduleToWidgetize' => 'Live',
                'actionToWidgetize' => 'getSimpleLastVisitCount',
                'label' => 'Real-time'
            ],
//            [
//                'moduleToWidgetize' => 'VisitsSummary',
//                'actionToWidgetize' => 'getEvolutionGraph',
//                'label' => __('Visits Over Time')
//            ],
            [
                'moduleToWidgetize' => 'CoreHome',
                'actionToWidgetize' => 'renderWidgetContainer',
                'label' => 'Visits'
            ],
            [
                'moduleToWidgetize' => 'UserCountryMap',
                'actionToWidgetize' => 'realtimeMap',
                'label' => 'Map'
            ]
        ];
    }
}
