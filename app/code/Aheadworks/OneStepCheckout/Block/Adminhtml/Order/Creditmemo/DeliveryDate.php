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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Order\Creditmemo;

use Aheadworks\OneStepCheckout\Block\Adminhtml\Order\AbstractDeliveryDate;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class DeliveryDate
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Order\Creditmemo
 */
class DeliveryDate extends AbstractDeliveryDate
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrder()
    {
        /** @var Creditmemo $creditmemo */
        $creditmemo = $this->coreRegistry->registry('current_creditmemo');
        return $creditmemo->getOrder();
    }
}
