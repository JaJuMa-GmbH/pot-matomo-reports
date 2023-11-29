<?php
/**
 * @author    JaJuMa GmbH <info@jajuma.de>
 * @copyright Copyright (c) 2023 JaJuMa GmbH <https://www.jajuma.de>. All rights reserved.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Jajuma\PotMatomoReports\Block;

use Jajuma\PotMatomoReports\ViewModel\Matomo;
use Jajuma\PowerToys\Block\PowerToys\Dashboard;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Module\Manager;

/**
 * Class Reports
 * @package Jajuma\PotMatomoReports\Block
 */
class Reports extends Dashboard
{

    /**
     *
     */
    public const XML_PATH_ENABLE = 'power_toys/matomo_reports/is_enabled';

    /**
     * @var Matomo
     */
    protected $viewModel;

    protected $moduleManager;

    /**
     * Reports constructor.
     * @param Matomo $viewModel
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Matomo $viewModel,
        Template\Context $context,
        Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->viewModel = $viewModel;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return bool
     */
    public function isParentEnable()
    {
        return $this->moduleManager->isEnabled('Jajuma_MatomoAnalytics');
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_ENABLE);
    }

    /**
     * @return Matomo
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }
}
