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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit;

use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Magento\Catalog\Helper\Product\Composite as ProductCompositeHelper;
use Magento\Framework\View\Result\Layout as ResultLayout;

/**
 * Class ConfigureProductToAdd
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit
 */
class ConfigureProductToAdd extends BackendAction
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @var ProductCompositeHelper
     */
    private $productHelper;

    /**
     * @param Context $context
     * @param DataObjectFactory $dataObjectFactory
     * @param QuoteSession $quoteSession
     * @param ProductCompositeHelper $productHelper
     */
    public function __construct(
        Context $context,
        DataObjectFactory $dataObjectFactory,
        QuoteSession $quoteSession,
        ProductCompositeHelper $productHelper
    ) {
        parent::__construct($context);
        $this->dataObjectFactory = $dataObjectFactory;
        $this->quoteSession = $quoteSession;
        $this->productHelper = $productHelper;
    }

    /**
     * @inheritdoc
     *
     * @return ResultLayout
     */
    public function execute()
    {
        $productId = (int) $this->getRequest()->getParam('id');

        $configureResult = $this->dataObjectFactory->create();
        $configureResult->setOk(true);
        $configureResult->setProductId($productId);
        $configureResult->setCurrentStoreId($this->quoteSession->getStore()->getId());
        $configureResult->setCurrentCustomerId($this->quoteSession->getCustomerId());

        return $this->productHelper->renderConfigureResult($configureResult);
    }
}
