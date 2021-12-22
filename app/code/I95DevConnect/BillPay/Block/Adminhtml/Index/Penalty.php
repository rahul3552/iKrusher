<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Index;

/**
 * Adminhtml customer penalty view block
 */
class Penalty extends \Magento\Backend\Block\Template
{

    protected $_template = 'penalty.phtml';

    /**
     * @var  \I95DevConnect\BillPay\Model\ArPenaltyFactory
     */
    protected $arPenaltyFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\ArPenaltyFactory $arPenaltyFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \I95DevConnect\BillPay\Model\ArPenaltyFactory $arPenaltyFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        $this->arPenaltyFactory = $arPenaltyFactory;
        $this->pricingHelper = $pricingHelper;
        parent::__construct($context, $data);
    }

    public function getPenalty()
    {
        $penalty = '';
        if (!empty($this->getRequest()->getParam('invoice_id'))) {
            $penalty = $this->arPenaltyFactory->create()
                ->load($this->getRequest()->getParam('invoice_id'), 'penalty_id');
        }

        return $penalty;
    }

    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }
}
