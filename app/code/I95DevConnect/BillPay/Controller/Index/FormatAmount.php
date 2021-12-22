<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Controller\Index;

use \Magento\Framework\App\Request\Http;

class FormatAmount extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $pricehelper;
    protected $request;
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        Http $request,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->priceHelper = $priceHelper;
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $amt = $this->getRequest()->getParam('amt');
            $formattedAmt = $this->priceHelper->currency($amt, true, false);

            return $result->setData($formattedAmt);
        }
    }
}
