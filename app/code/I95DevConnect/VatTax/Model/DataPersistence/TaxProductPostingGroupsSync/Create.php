<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\DataPersistence\TaxProductPostingGroupsSync;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class for creating Tax product posting group sync
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
        'targetId' => 'Tax product posting code is required'
    ];
    public $success_msg = "Record Successfully Synced";

    /**
     * @var \I95DevConnect\SalesTax\Model\TaxAreasFactory
     */
    public $taxProductPostingGroupsModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;
    public $taxHelper;
    public $abstractDataPersistence;

    /**
     * Create constructor.
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevResponseInterface $i95DevResponse
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
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
        $this->taxProductPostingGroupsModel = $taxProductPostingGroupsModel;
        $this->date = $date;
        $this->taxHelper = $taxHelper;
        $this->abstractDataPersistence = $abstractDataPersistence;
    }

    /**
     * Create Tax Product Posting Group
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
                    $code = $stringData['targetId'];
                    $sourceId = isset($stringData['previousCode']) ? $stringData['previousCode'] : '';
                    $taxProductPostingGroupsData = $this->taxProductPostingGroupsModel->create()->getCollection()
                        ->addFieldtoFilter('code', $code);
                    $taxProductPostingGroupsData->getSelect()->limit(1);

                    $taxProductPostingGroupsData = $taxProductPostingGroupsData->getData();

                    $taxProductPostingObj = $this->prepareTaxProductPostingGroup(
                        $sourceId,
                        $taxProductPostingGroupsData,
                        $code
                    );

                    if (get_class($taxProductPostingObj) ===
                        \I95DevConnect\VatTax\Model\TaxProductPostingGroups::class) {
                        $taxProductPostingGroups = $taxProductPostingObj;
                    } else {
                        return $taxProductPostingObj;
                    }

                    $taxProductPostingGroups->setDescription($stringData['reference']);
                    $taxProductPostingGroups->Save();
                    $magentoId = $taxProductPostingGroups->getId();

                    $this->taxHelper->taxSyncEventAndRegistry(
                        $entityCode,
                        $code,
                        $this
                    );

                    return $this->abstractDataPersistence->setResponse(
                        \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                        __($this->success_msg),
                        $magentoId
                    );
                }
            }
        } catch (LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($ex->getMessage())
            );
        }
    }

    /**
     * @param $sourceId
     * @param $taxProductPostingGroupsData
     * @param $code
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function prepareTaxProductPostingGroup($sourceId, $taxProductPostingGroupsData, $code)
    {
        if (empty($sourceId)) {
            if (!empty($taxProductPostingGroupsData)) {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                    __($this->success_msg),
                    $taxProductPostingGroupsData[0]['id']
                );
            } else {
                $taxProductPostingGroups = $this->taxProductPostingGroupsModel->create();
                $taxProductPostingGroups->setCode($code);
                $taxProductPostingGroups->setCreatedDt($this->date->gmtDate());
            }
        } else {
            if (!empty($taxProductPostingGroupsData)) {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                    __($this->success_msg),
                    $taxProductPostingGroupsData[0]['id']
                );
            } else {
                //@Hrusieksh Get rowId from sourceId to update tax code
                $rowId = $this->taxProductPostingGroupsModel->create()->getCollection()
                    ->addFieldtoFilter('code', $sourceId)
                    ->getData();
                $taxProductPostingGroups = $this->taxProductPostingGroupsModel->create()->load(
                    $rowId[0]['id']
                );

                $taxProductPostingGroups->setCode($code);
            }
        }
        return $taxProductPostingGroups;
    }
}
