<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Index;

use \Magento\Customer\Model\Session;

/**
 * Sales order history block
 */
class PenaltyDetails extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    private $penaltyDetailsFactory;
    private $penaltyModelFactory;
    protected $penalty;
    protected $penaltyDetails;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\ArPenaltyDetailsFactory $penaltyDetailsFactory
     * @param Session $customerSession
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \I95DevConnect\BillPay\Model\ArPenaltyFactory $penaltyModelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\BillPay\Model\ArPenaltyDetailsFactory $penaltyDetailsFactory,
        Session $customerSession,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \I95DevConnect\BillPay\Model\ArPenaltyFactory $penaltyModelFactory,
        array $data = []
    ) {
        $this->penaltyDetailsFactory = $penaltyDetailsFactory;
        $this->penaltyModelFactory = $penaltyModelFactory;
        $this->customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return bool|\I95DevConnect\BillPay\Model\ResourceModel\ArPenaltyDetails\Collection
     */
    public function getPenaltyReferences()
    {
        if (!($this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->penaltyDetails) {
            $this->penaltyDetails = $this->penaltyDetailsFactory->create()->getCollection()->addFieldToFilter(
                'penalty_id',
                $this->getRequest()->getParam('invoice_id')
            );
        }

        return $this->penaltyDetails;
    }

    /**
     * @return bool|\I95DevConnect\BillPay\Model\ResourceModel\ArPenalty
     */
    public function getPenaltyDetails()
    {
        if (!($this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->penalty) {
            $this->penalty = $this->penaltyModelFactory->create()->getCollection()->addFieldToFilter(
                'penalty_id',
                $this->getRequest()->getParam('invoice_id')
            )->getLastItem();
        }

        return $this->penalty;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }
}
