<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Controller\Adminhtml\Pricelist;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\Framework\Controller\ResultFactory;

/**
 * Price Level Grid Layout Definition
 */
class Grid extends Action
{

    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Builder
     */
    public $productBuilder;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Magento\Backend\App\Action\Context $context
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
     * Get price levels grid
     *
     * @return \Magento\Framework\View\Result\Layout
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        /**
         * @var \Magento\Framework\View\Result\Layout $resultLayout
         */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $resultLayout->getLayout()->getBlock('admin.product.pricelist')
            ->setUseAjax(true);
        return $resultLayout;
    }
}
