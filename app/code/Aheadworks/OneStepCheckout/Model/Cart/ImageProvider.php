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
namespace Aheadworks\OneStepCheckout\Model\Cart;

use Magento\Checkout\CustomerData\ItemPoolInterface;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\ConfigInterface as ViewConfigInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * Class ImageProvider
 * @package Aheadworks\OneStepCheckout\Model\Cart
 */
class ImageProvider
{
    const IMAGE_ID = 'mini_cart_product_thumbnail';

    /**
     * @var ViewConfigInterface
     */
    private $viewConfig;

    /**
     * @var AssetRepository
     */
    private $assetRepo;

    /**
     * @var CartItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var Factory
     */
    private $dataObjectFactory;

    /**
     * @var ItemPoolInterface
     */
    private $itemPool;

    /**
     * @param ItemPoolInterface $itemPool
     * @param ViewConfigInterface $viewConfig
     * @param AssetRepository $assetRepo
     * @param CartItemRepositoryInterface $itemRepository
     * @param Factory $dataObjectFactory
     */
    public function __construct(
        ItemPoolInterface $itemPool,
        ViewConfigInterface $viewConfig,
        AssetRepository $assetRepo,
        CartItemRepositoryInterface $itemRepository,
        Factory $dataObjectFactory
    ) {
        $this->itemPool = $itemPool;
        $this->viewConfig = $viewConfig;
        $this->assetRepo = $assetRepo;
        $this->itemRepository = $itemRepository;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Get cart items image data
     *
     * @param int $cartId
     * @return array
     */
    public function getItemsImageData($cartId)
    {
        $imageData = [];
        /** @var CartItemInterface|Item $item */
        foreach ($this->itemRepository->getList($cartId) as $item) {
            $itemData = $this->itemPool->getItemData($item);
            $imageData[$item->getItemId()] = [
                'src' => $itemData['product_image']['src'],
                'alt' => $itemData['product_image']['alt']
            ];
        }
        return $imageData;
    }

    /**
     * Get config image data
     *
     * @param int $cartId
     * @return array
     */
    public function getConfigImageData($cartId)
    {
        $imageAttributes = $this->getImageAttributes();
        return [
            'attributes' => [
                'width' => $imageAttributes->getWidth(),
                'height' => $imageAttributes->getHeight() ? : $imageAttributes->getWidth()
            ],
            'itemsData' => $this->getItemsImageData($cartId),
            'placeholderUrl' => $this->getPlaceholderUrl()
        ];
    }

    /**
     * Get image attributes
     *
     * @return array
     */
    private function getImageAttributes()
    {
        $viewConfig = $this->viewConfig->getViewConfig();
        $data = $viewConfig->read();
        $attributesData = isset($data['media']['Magento_Catalog']['images'][self::IMAGE_ID])
            ? $data['media']['Magento_Catalog']['images'][self::IMAGE_ID]
            : [];
        return $this->dataObjectFactory->create($attributesData);
    }

    /**
     * Get placeholder url
     *
     * @return string
     */
    private function getPlaceholderUrl()
    {
        $path = 'Magento_Catalog::images/product/placeholder/thumbnail.jpg';
        return $this->assetRepo->getUrl($path);
    }
}
