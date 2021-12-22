<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\DataPersistence\TaxBusPostingGroupsSync;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class for syncing tax bus posting group
 */
class Create
{

    const TARGET_ID = "targetId";
    private $requestHelper;
    public $i95DevResponse;
    public $logger;
    public $dataHelper;
    public $eventManager;
    public $validate;
    public $validateFields = [
        self::TARGET_ID => 'Tax bus posting code is required'
    ];
    public $success_msg = "Record Successfully Synced";

    /**
     * @var \I95DevConnect\SalesTax\Model\TaxAreasFactory
     */
    public $taxBusPostingGroupsModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;
    public $taxHelper;
    public $abstractDataPersistence;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevResponseInterface $i95DevResponse
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\VatTax\Model\TaxBusPostingGroupsFactory $taxBusPostingGroupsModel
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
        \I95DevConnect\VatTax\Model\TaxBusPostingGroupsFactory $taxBusPostingGroupsModel,
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
        $this->taxBusPostingGroupsModel = $taxBusPostingGroupsModel;
        $this->date = $date;
        $this->taxHelper = $taxHelper;
        $this->abstractDataPersistence = $abstractDataPersistence;
    }

    /**
     * Create Tax Business Posting Group
     *
     * @param string $stringData
     * @param string $entityCode
     * @param string $erp
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync($stringData, $entityCode)
    {
        $this->stringData = $stringData;
        $this->entityCode = $entityCode;
        $isEnabled = $this->taxHelper->isVatTaxEnabled();
        if ($isEnabled) {
            $this->validate->validateFields = $this->validateFields;
            $this->validate->validateData($this->stringData);

            try {
                if (!empty($stringData)) {
                    $code = $stringData[self::TARGET_ID];
                    // @Hrusikesh Changed sourceId to Previous tax code
                    $sourceId = isset($stringData['previousCode']) ? $stringData['previousCode'] : '';

                    // @updatedBy Arushi Bansal Added limit to enhance query performance
                    $taxBusPostingGroupsData = $this->taxBusPostingGroupsModel->create()->getCollection()
                        ->addFieldtoFilter('code', $code);
                    $taxBusPostingGroupsData->getSelect()->limit(1);
                    $taxBusPostingGroupsData = $taxBusPostingGroupsData->getData();

                    $taxBusPostingObj = $this->prepareTaxBusPostingObj($sourceId, $taxBusPostingGroupsData, $code);

                    if (get_class($taxBusPostingObj) === \I95DevConnect\VatTax\Model\TaxBusPostingGroups::class) {
                        $taxBusPostingGroups = $taxBusPostingObj;
                    } else {
                        return $taxBusPostingObj;
                    }

                    $taxBusPostingGroups->setDescription($stringData['reference']);
                    $taxBusPostingGroups->setUpdatedDate($this->date->gmtDate());
                    $taxBusPostingGroups->Save();
                    $magentoId = $taxBusPostingGroups->getId();

                    $this->dataHelper->unsetGlobalValue('i95_observer_skip');

                    $jsondata = json_encode(["entityCode" => $entityCode,
                        self::TARGET_ID => $code,
                        "source" => "ERP"]);

                    $this->dataHelper->coreRegistry->unregister('savingSource');
                    $this->dataHelper->coreRegistry->register('savingSource', $jsondata);

                    $aftereventname = 'erpconnect_messagequeuetomagento_aftersave_' . $entityCode;
                    $this->eventManager->dispatch($aftereventname, ['currentObject' => $this]);

                    $result = $this->abstractDataPersistence->setResponse(
                        \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                        __($this->success_msg),
                        $magentoId
                    );
                }
            } catch (LocalizedException $ex) {
                $this->logger->createLog(
                    '__METHOD__',
                    $ex->getMessage(),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    'error'
                );

                $result = $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                    __('There was an error while syncing data to magento.'),
                    null
                );
            }
        } else {
            $result = $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __('Please Enable VatTax Module'),
                null
            );
        }
        return $result;
    }

    /**
     * @param $sourceId
     * @param $taxBusPostingGroupsData
     * @param $code
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function prepareTaxBusPostingObj($sourceId, $taxBusPostingGroupsData, $code)
    {
        if (empty($sourceId)) {
            if (!empty($taxBusPostingGroupsData)) {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                    __($this->success_msg),
                    $taxBusPostingGroupsData[0]['id']
                );
            } else {
                $taxBusPostingGroups = $this->taxBusPostingGroupsModel->create();
                $taxBusPostingGroups->setCode($code);
                $taxBusPostingGroups->setCreatedDate($this->date->gmtDate());
            }
        } else {
            if (!empty($taxBusPostingGroupsData)) {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                    __($this->success_msg),
                    $taxBusPostingGroupsData[0]['id']
                );
            } else {
                $taxBusPostingGroups = $this->taxBusPostingGroupsModel->create()->load($sourceId);
                $taxBusPostingGroups->setCode($code);
            }
        }
        return $taxBusPostingGroups;
    }
}
