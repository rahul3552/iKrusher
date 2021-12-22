<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\DataPersistence\TaxPostingSetupSync;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class for syncing tax posting setup
 */
class Create
{
    private $requestHelper;
    public $i95DevResponse;
    public $logger;
    public $dataHelper;
    public $eventManager;
    public $validate;
    public $validateFields = [
        'targetId' => 'Tax posting code is required'
    ];

    /**
     * @var \I95DevConnect\SalesTax\Model\TaxAreasFactory
     */
    public $taxPostingSetupModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;
    public $taxHelper;
    public $taxBusPostingGroupsModel;
    public $taxProductPostingGroupsModel;
    public $abstractDataPersistence;

    /**
     * Create constructor.
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevResponseInterface $i95DevResponse
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\VatTax\Model\TaxPostingSetupFactory $taxPostingSetupModel
     * @param \I95DevConnect\VatTax\Model\TaxBusPostingGroupsFactory $taxBusPostingGroupsModel
     * @param \I95DevConnect\VatTax\Model\TaxProductPostingGroupsFactory $taxProductPostingGroupsModel
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \I95DevConnect\VatTax\Helper\Data $taxHelper
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Api\I95DevResponseInterface $i95DevResponse,
        \I95DevConnect\MessageQueue\Model\Logger $logger,
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\VatTax\Model\TaxPostingSetupFactory $taxPostingSetupModel,
        \I95DevConnect\VatTax\Model\TaxBusPostingGroupsFactory $taxBusPostingGroupsModel,
        \I95DevConnect\VatTax\Model\TaxProductPostingGroupsFactory $taxProductPostingGroupsModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \I95DevConnect\VatTax\Helper\Data $taxHelper,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
    ) {
        $this->i95DevResponse = $i95DevResponse;
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
        $this->requestHelper = $requestHelper;
        $this->eventManager = $eventManager;
        $this->validate = $validate;
        $this->taxPostingSetupModel = $taxPostingSetupModel;
        $this->date = $date;
        $this->taxBusPostingGroupsModel = $taxBusPostingGroupsModel;
        $this->taxProductPostingGroupsModel = $taxProductPostingGroupsModel;
        $this->taxHelper = $taxHelper;
        $this->abstractDataPersistence = $abstractDataPersistence;
    }

    /**
     * Create Tax Posting Setup
     *
     * @param string $stringData
     * @param string $entityCode
     * @param string $erp
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync($stringData, $entityCode)
    {
        try {
            $this->stringData = $stringData;
            $this->entityCode = $entityCode;
            $isEnabled = $this->taxHelper->isVatTaxEnabled();

            if ($isEnabled) {
                $this->validate->validateFields = $this->validateFields;
                $this->validate->validateData($this->stringData);

                if (!empty($stringData)) {
                    $sourceId = $this->dataHelper->getValueFromArray("sourceId", $this->stringData);
                    $code = $this->dataHelper->getValueFromArray("targetId", $this->stringData);
                    $buspostingCode = $this->dataHelper->getValueFromArray("taxBusPostingGroupCode", $this->stringData);
                    $productpostingCode = $this->dataHelper->getValueFromArray(
                        "taxProductPostingGroupCode",
                        $this->stringData
                    );
                    $taxPercentage = $this->dataHelper->getValueFromArray("taxPercentage", $this->stringData);

                    if ($sourceId == '') {
                        //@updatedBy Arushi Bansal added limit for query optimization
                        $taxBusPostingGroupData = $this->taxBusPostingGroupsModel->create()->getCollection()
                                ->addFieldtoFilter('code', $buspostingCode);
                        $taxBusPostingGroupData->getSelect()->limit(1);
                        $taxBusPostingGroupData =$taxBusPostingGroupData->getData();

                        $postingGroupCheck = $this->checkPostingGroupExists(
                            $taxBusPostingGroupData,
                            $productpostingCode
                        );

                        if (is_string($postingGroupCheck)) {
                            return $this->abstractDataPersistence->setResponse(
                                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                                __($postingGroupCheck),
                                null
                            );
                        }

                        $taxPostingSetupData = $this->getPostingCollection($buspostingCode, $productpostingCode);
                        $sourceId = empty($taxPostingSetupData) ? null : $taxPostingSetupData["id"];
                    }

                    $taxPostingSetup = $this->setTaxPostingData(
                        $sourceId,
                        $buspostingCode,
                        $productpostingCode,
                        $taxPercentage
                    );

                    $taxPostingSetup->save();
                    $magentoId = $taxPostingSetup->getId();

                    $this->taxHelper->taxSyncEventAndRegistry(
                        $entityCode,
                        $code,
                        $this
                    );

                    $record_msg = "Record Successfully Synced";
                    $record_status = \I95DevConnect\MessageQueue\Helper\Data::SUCCESS;
                }
            }
        } catch (LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($ex->getMessage())
            );
        }

        return $this->abstractDataPersistence->setResponse(
            $record_status,
            __($record_msg),
            $magentoId
        );
    }

    /**
     * @param $sourceId
     * @param $buspostingCode
     * @param $productpostingCode
     * @param $taxPercentage
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function prepareTaxPostingSetup($sourceId, $buspostingCode, $productpostingCode, $taxPercentage)
    {

        if ($sourceId == '') {
            //@updatedBy Arushi Bansal added limit for query optimization
            $taxBusPostingGroupData = $this->taxBusPostingGroupsModel->create()->getCollection()
                ->addFieldtoFilter('code', $buspostingCode);
            $taxBusPostingGroupData->getSelect()->limit(1);
            $taxBusPostingGroupData =$taxBusPostingGroupData->getData();

            if ($taxBusPostingGroupData) {
                //@updatedBy Arushi Bansal added limit for performance improvement
                $taxProductPostingGroupData = $this->taxProductPostingGroupsModel->create()->getCollection()
                    ->addFieldtoFilter('code', $productpostingCode);
                $taxProductPostingGroupData->getSelect()->limit(1);
                $taxProductPostingGroupData = $taxProductPostingGroupData->getData();

                if (!$taxProductPostingGroupData) {
                    throw new LocalizedException(
                        __('ProductPosting Group Is Not Available')
                    );
                }
            } else {
                throw new LocalizedException(
                    __('BusinessPosting Group Is Not Available ')
                );
            }

            $taxPostingSetupData = $this->getPostingCollection($buspostingCode, $productpostingCode);

            $sourceId = empty($taxPostingSetupData) ? null : $taxPostingSetupData["id"];
        }

        return $this->setTaxPostingData(
            $sourceId,
            $buspostingCode,
            $productpostingCode,
            $taxPercentage
        );
    }

    /**
     * check if product and buisiness posting group exists
     * @param $taxBusPostingGroupData
     * @param $productpostingCode
     * @return bool|string
     */
    public function checkPostingGroupExists($taxBusPostingGroupData, $productpostingCode)
    {
        if ($taxBusPostingGroupData) {
            //@updatedBy Arushi Bansal added limit for performance improvement
            $taxProductPostingGroupData = $this->taxProductPostingGroupsModel->create()->getCollection()
                ->addFieldtoFilter('code', $productpostingCode);
            $taxProductPostingGroupData->getSelect()->limit(1);
            $taxProductPostingGroupData = $taxProductPostingGroupData->getData();

            if (!$taxProductPostingGroupData) {
                return 'ProductPosting Group Is Not Available';
            }
        } else {
            return 'BusinessPosting Group Is Not Available';
        }

        return true;
    }

    /**
     * @param $sourceId
     * @param $buspostingCode
     * @param $productpostingCode
     * @param $taxPercentage
     * @return mixed
     */
    public function setTaxPostingData($sourceId, $buspostingCode, $productpostingCode, $taxPercentage)
    {

        if (empty($sourceId)) {
            $taxPostingSetup = $this->taxPostingSetupModel->create();
        } else {
            $taxPostingSetup = $this->taxPostingSetupModel->create()->load($sourceId);
        }
        $taxPostingSetup->setTaxBuspostingGroupCode($buspostingCode);
        $taxPostingSetup->setTaxProductpostingGroupCode($productpostingCode);
        $taxPostingSetup->setTaxPercentage($taxPercentage);
        $taxPostingSetup->setUpdateDate($this->date->gmtDate());

        return $taxPostingSetup;
    }

    /**
     * @param $buspostingCode
     * @param $productpostingCode
     */
    public function getPostingCollection($buspostingCode, $productpostingCode)
    {
        return $this->taxPostingSetupModel->create()->getCollection()
            ->addFieldToSelect('id')
            ->addFieldtoFilter('tax_busposting_group_code', $buspostingCode)
            ->addFieldtoFilter('tax_productposting_group_code', $productpostingCode)
            ->getLastItem()
            ->getData();
    }
}
