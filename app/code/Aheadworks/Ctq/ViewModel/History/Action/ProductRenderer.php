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
namespace Aheadworks\Ctq\ViewModel\History\Action;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class ProductRenderer
 * @package Aheadworks\Ctq\ViewModel\History\Action
 */
class ProductRenderer implements ArgumentInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Retrieve product name by id
     *
     * @param int $productId
     * @return string
     */
    public function getProductName($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);
            $productName = $product->getName();
        } catch (NoSuchEntityException $e) {
            $productName = 'Undefined';
        }
        return $productName;
    }
}
