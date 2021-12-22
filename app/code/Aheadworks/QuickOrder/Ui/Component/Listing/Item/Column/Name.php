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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;

/**
 * Class Name
 *
 * @package Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column
 */
class Name extends Column
{
    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductDetailPool $productDetailPool
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductDetailPool $productDetailPool,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->storeManager = $storeManager;
        $this->productDetailPool = $productDetailPool;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                try {
                    $provider = $this->productDetailPool->get($item);
                    $item['product_name_url'] = $provider->getProductUrl();
                    $item['preparation_error'] = $provider->getProductPreparationError();
                    $item['is_available'] = $provider->isAvailableForSite($websiteId);
                    $item['is_available_for_quick_order'] = $provider->isAvailableForQuickOrder($websiteId);
                    $item['is_salable'] = $provider->isSalable();
                    $item['is_disabled'] = $provider->isDisabled();
                    $item['is_editable'] = $provider->isEditable();
                    $item['product_attributes'] = $provider->getProductAttributes();
                } catch (NoSuchEntityException $e) {
                    $item['is_available'] = false;
                    $item['product_attributes'] = [];
                }
            }
        }

        return $dataSource;
    }
}
