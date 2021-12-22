<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product;

use \I95DevConnect\MessageQueue\Helper\Data;

/**
 * Class responsible for saving erp responses in product
 */
class Response
{
    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterface
     */
    public $logger;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     *
     * @var \Magento\Framework\Event\Manager
     */
    public $eventManager;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory
     */
    public $i95DevMagentoMQRepository;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory
     */
    public $i95DevMagentoMQData;

    /**
     *
     * @var Object
     */
    public $product = null;
    public $abstractDataPersistence;

    /**
     * @var \Magento\Catalog\Model\ProductRepositoryFactory
     */
    public $productRepo;

    public $productId;

    public $erpCode;
    const SKIPOBRVR = "i95_observer_skip";

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Catalog\Model\ProductRepositoryFactory $productRepo
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        Data $dataHelper,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Catalog\Model\ProductRepositoryFactory $productRepo,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
    ) {
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->i95DevMagentoMQData = $i95DevMagentoMQData;
        $this->eventManager = $eventManager;
        $this->productRepo = $productRepo;
        $this->abstractDataPersistence = $abstractDataPersistence;
    }

    /**
     * Sets target product details in product
     *
     * @param array $requestData
     * @return \I95DevConnect\MessageQueue\Model\AbstractDataPersistence
     * @author Hrusieksh Manna
     */
    public function setProductResponse($requestData)
    {
        try {
            /** @updatedBy vinayakrao shetkar. Changed targetId to sourceId
             * validate Data with productId instead of sku **/
            $this->productId = $this->dataHelper->getValueFromArray("sourceId", $requestData);
            if ($this->validateData()) {
                $this->erpCode = isset($requestData['erp_name']) ? $requestData['erp_name'] : __("ERP");
                $productResponseBeforeEvent = "erpconnect_forward_product_beforeresponse";
                $this->eventManager->dispatch($productResponseBeforeEvent, ['currentObject' => $this]);
                $this->dataHelper->unsetGlobalValue(self::SKIPOBRVR);
                $this->dataHelper->setGlobalValue(self::SKIPOBRVR, true);
                $result = $this->erpUpdatesForProduct();
                $this->dataHelper->unsetGlobalValue(self::SKIPOBRVR);
                $productResponseAfterEvent = "erpconnect_forward_product_afterresponse";
                $this->eventManager->dispatch($productResponseAfterEvent, ['currentObject' => $this]);
                return $result;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            /** @updatedBy Debashis S. Gopal. Returning false instead of throwing exception,
             * as expected by the calling function. **/
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );

            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );

        }
    }

    /**
     * Validate response data from ERP
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Debashis S. Gopal. Replaced Undefined variable with $this->productRepo. And optimized code.
     */
    public function validateData()
    {
        try {
            $this->product = $this->productRepo->create()->getById($this->productId);

            if ($this->product->getId()) {
                return true;
            } else {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                    __("Product Not Exist With Id ".$this->productId),
                    null
                );

            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * Update product with with ERP Data
     * @updatedBy Debashis S. Gopal. Removed unnecessary new product creation,
     * instead initialized in validate method. And optimized the code
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function erpUpdatesForProduct()
    {
        try {
            $this->product->setCustomAttribute("targetproductstatus", Data::SYNCED)
                    ->setCustomAttribute("update_by", $this->erpCode);
            $result = $this->productRepo->create()->save($this->product);
            if ($result->getId()) {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                    __("Response send successfully"),
                    null
                );
            } else {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                    __("Some error occured in response sync"),
                    null
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
