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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Controller\QuickOrder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool;

/**
 * Class AddToList
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder
 */
class AddToList extends AbstractAddToList
{
    /**
     * @var Pool
     */
    private $productProviderPool;

    /**
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param OperationManager $operationManager
     * @param Pool $productProviderPool
     */
    public function __construct(
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        OperationManager $operationManager,
        Pool $productProviderPool
    ) {
        parent::__construct($context, $dataObjectProcessor, $storeManager, $operationManager);
        $this->productProviderPool = $productProviderPool;
    }

    /**
     * Add single product to list
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $requestData = $this->getRequest()->getParams();
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $operationResult = $this->operationManager->addItemsToCurrentList([$requestData], $storeId);
            $result = $this->convertToResultArray($operationResult);
            $errors = $operationResult->getErrorMessages();
            if (!count($errors)) {
                $provider = $this->productProviderPool->getWithoutCaching($requestData);
                $result['is_editable'] = $provider->isEditable();
            }
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }

        return $resultJson->setData($result);
    }
}
