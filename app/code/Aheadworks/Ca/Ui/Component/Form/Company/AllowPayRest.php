<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Ui\Component\Form\Company;

use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;

/**
 * Class AllowPayRest
 * @package Aheadworks\Ca\Ui\Component\Form\Company
 */
class AllowPayRest extends Fieldset
{
    /**
     * @var Manager
     */
    private $thirdPartyModuleManager;

    /**
     * @param ContextInterface $context
     * @param Manager $thirdPartyModuleManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Manager $thirdPartyModuleManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->thirdPartyModuleManager->isAwPayRestModuleEnabled()) {
            $config = $this->getData('config');
            $config['visible'] = false;
            $this->setData('config', $config);
        }
    }
}
