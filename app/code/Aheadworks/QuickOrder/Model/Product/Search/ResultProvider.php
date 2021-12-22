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
namespace Aheadworks\QuickOrder\Model\Product\Search;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;

/**
 * Class ResultProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\Search
 */
class ResultProvider
{
    /**
     * Image ID used for rendering
     */
    const IMAGE_ID = 'aw_qo_small_image';

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var Searcher
     */
    private $searcher;

    /**
     * @param ImageHelper $imageHelper
     * @param Searcher $searcher
     */
    public function __construct(
        ImageHelper $imageHelper,
        Searcher $searcher
    ) {
        $this->imageHelper = $imageHelper;
        $this->searcher = $searcher;
    }

    /**
     * Get search results using currently available search engine
     *
     * @param string $searchTerm
     * @return array
     */
    public function get($searchTerm)
    {
        $items = $this->searcher->search($searchTerm);

        $result = [];
        /** @var Product $item */
        foreach ($items as $item) {
            $imageHelper = $this->imageHelper->init($item, self::IMAGE_ID);
            $result[] = [
                ProductInterface::NAME => $item->getName(),
                ProductInterface::SKU => $item->getSku(),
                'url' => $imageHelper->getUrl()
            ];
        }

        return $result;
    }
}
