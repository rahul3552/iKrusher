<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Index;

/**
 * Adminhtml customer manage payment grid block
 */
class OrderAddressDetails extends \Magento\Backend\Block\Template
{

    protected $_template = 'orderAddressDetails.phtml';
    protected $salesOrder;

    /**
     *
     * @var \Magento\Sales\Block\Order\Info
     */
    protected $salesInfo;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Model\OrderFactory $salesOrder
     * @param \Magento\Sales\Block\Order\Info $salesInfo
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $salesOrder,
        \Magento\Sales\Block\Order\Info $salesInfo,
        array $data = []
    ) {
        $this->salesOrder = $salesOrder;
        $this->salesInfo = $salesInfo;
        parent::__construct($context, $data);
    }

    public function getOrder()
    {
        $order = '';
        if (!empty($this->getRequest()->getParam('order_id'))) {
            $order = $this->salesOrder->create()->loadByIncrementId($this->getRequest()->getParam('order_id'));
        }
        
        return $order;
    }

    public function getSalesInfo()
    {
        return $this->salesInfo;
    }
}
