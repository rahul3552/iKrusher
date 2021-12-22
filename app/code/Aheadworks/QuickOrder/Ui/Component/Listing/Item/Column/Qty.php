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
namespace Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column;

use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;

/**
 * Class Qty
 *
 * @package Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column
 */
class Qty extends Column
{
    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductDetailPool $productDetailPool
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductDetailPool $productDetailPool,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->productDetailPool = $productDetailPool;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                try {
                    $provider = $this->productDetailPool->get($item);
                    $item['is_qty_editable'] = $provider->isQtyEditable();
                    $item['qty_salable_message'] =
                        $provider->getQtySalableMessage($item[ProductListItemInterface::PRODUCT_QTY]);
                } catch (NoSuchEntityException $e) {
                }
            }
        }

        return $dataSource;
    }
}
