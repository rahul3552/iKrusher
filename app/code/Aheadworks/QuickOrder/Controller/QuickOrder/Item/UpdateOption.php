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
namespace Aheadworks\QuickOrder\Controller\QuickOrder\Item;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;

/**
 * Class UpdateOption
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder\Item
 */
class UpdateOption extends AbstractAction
{
    /**
     * Configure item
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $itemKey = $this->getRequest()->getParam(ProductListItemInterface::ITEM_KEY);
        if (!$itemKey) {
            $result = [
                'error' => __('Product list item key is required'),
            ];

            return $resultJson->setData($result);
        }

        try {
            $buyRequest = $this->getRequest()->getParams();
            $storeId = $this->storeManager->getStore()->getId();
            $operationResult = $this->operationManager->updateItemOption($itemKey, $buyRequest, $storeId);
            $result = $this->convertToResultArray($operationResult);
        } catch (\Exception $exception) {
            $result = [
                'error' => $exception->getMessage(),
            ];
        }
        return $resultJson->setData($result);
    }
}
