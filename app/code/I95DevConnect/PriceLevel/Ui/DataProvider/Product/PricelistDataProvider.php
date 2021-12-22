<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Ui\DataProvider\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use I95DevConnect\PriceLevel\Model\ResourceModel\ItemPriceListData\CollectionFactory;
use I95DevConnect\PriceLevel\Model\ResourceModel\ItemPriceListData\Collection;

/**
 * Class PricelistDataProvider for UI component
 *
 * @method Collection getCollection
 */
class PricelistDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productloader;

    /**
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productloader,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
        $this->productloader = $productloader;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
//var_dump($this->data); exit();
       echo $currentProductId = (int)$this->request->getParam('current_product_id'); exit();
        $sku = $this->productloader->create()->load($currentProductId)->getSku();
        $this->getCollection()->addFieldToSelect('*')->addFieldToFilter('sku', $sku);

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }
}
