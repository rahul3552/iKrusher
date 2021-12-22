<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\Framework\Controller\ResultFactory;

/**
 * Product grid
 */
class Grid extends Action
{

    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Builder
     */
    public $productBuilder;

    /**
     * @param \Magento\Backend\App\Action\Context                   $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     */
    public function __construct(
        Context $context,
        ProductBuilder $productBuilder
    ) {
        $this->productBuilder = $productBuilder;
        parent::__construct($context);
    }

    /**
     * Get product reviews grid
     *
     * @return \Magento\Framework\View\Result\Layout
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $resultLayout->getLayout()->getBlock('admin.product.custominfo')
            ->setUseAjax(true);
        return $resultLayout;
    }
}
