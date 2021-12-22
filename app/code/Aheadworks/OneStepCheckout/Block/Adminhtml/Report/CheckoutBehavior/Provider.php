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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior;

use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\CheckoutBehavior\Collection;
use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\CheckoutBehavior\CollectionFactory;
use Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilterPool;

/**
 * Class Provider
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior
 */
class Provider
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var DefaultFilterPool
     */
    private $defaultFilterPool;

    /**
     * @var array
     */
    private $data;

    /**
     * @param CollectionFactory $collectionFactory
     * @param DefaultFilterPool $defaultFilterPool
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        DefaultFilterPool $defaultFilterPool
    ) {
        $this->collection = $collectionFactory->create();
        $this->defaultFilterPool = $defaultFilterPool;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->data) {
            if (!$this->collection->isLoaded()) {
                $this->defaultFilterPool->applyFilters($this->collection);
                $this->collection->load();
            }

            $items = $this->collection->getItems();
            $this->data = [
                'totalRecords' => count($items),
                'items' => array_values($items),
                'totalsItems' => $this->collection->getTotalsItems()
            ];
        }
        return $this->data;
    }

    /**
     * Find items by criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findItems(array $criteria = [])
    {
        $result = [];
        $data = $this->getData();
        if ($data['totalRecords'] > 0) {
            foreach ($data['items'] as $item) {
                foreach ($criteria as $criteriaRow) {
                    $isMatch = true;
                    foreach ($criteriaRow as $field => $value) {
                        if ($item[$field] != $value) {
                            $isMatch = false;
                        }
                    }
                    if ($isMatch) {
                        $result[] = $item;
                    }
                }
            }
        }
        return $result;
    }
}
