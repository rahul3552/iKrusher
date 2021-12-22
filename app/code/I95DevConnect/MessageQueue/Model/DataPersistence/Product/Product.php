<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Product;

/**
 * Class for creating Product, getting Product info and setting Product response
 *
 */
class Product
{
    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Create
     */
    public $productCreate;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Info
     */
    public $productInfo;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Response
     */
    public $productResponse;

    /**
     * Product constructor.
     *
     * @param Product\CreateFactory $productCreate
     * @param Product\Info $productInfo
     * @param Product\Response $productResponse
     */
    public function __construct(
        Product\CreateFactory $productCreate,
        Product\Info $productInfo,
        Product\Response $productResponse
    ) {
        $this->productCreate = $productCreate;
        $this->productInfo = $productInfo;
        $this->productResponse = $productResponse;
    }

    /**
     * Create Product.
     *
     * @param string $stringData
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function create($stringData, $entityCode, $erpCode)
    {
        return $this->productCreate->create()->createProduct($stringData, $entityCode, $erpCode);
    }

    /**
     * Get Product information
     *
     * @param int $productId
     * @param string $entityCode
     * @param string $erpCode
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInfo($productId, $entityCode, $erpCode)
    {
        return  $this->productInfo->getInfo($productId, $entityCode, $erpCode);
    }

    /**
     * Sets target Product information
     *
     * @param array $requestData
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return \I95DevConnect\MessageQueue\Model\AbstractDataPersistence
     */
    public function getResponse($requestData, $entityCode, $erpCode)
    {
        return $this->productResponse->setProductResponse($requestData, $entityCode, $erpCode);
    }
}
