<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Model\DataPersistence\NetTermsSync;

use I95DevConnect\MessageQueue\Helper\Data;
use \I95DevConnect\MessageQueue\Model\AbstractDataPersistence;
use I95DevConnect\MessageQueue\Model\DataPersistence\Validate;
use I95DevConnect\NetTerms\Model\NetTermsFactory;
use Magento\Framework\Event\Manager;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Netterms reverse sync class
 */
class Create
{
    const TARGET_NET_TERMS_ID = 'targetNetTermsId';
    /**
     * @var Data
     */
    public $dataHelper;

    /**
     * @var Manager
     */
    public $eventManager;

    /**
     * @var Validate
     */
    public $validate;

    /**
     * @var string[]
     */
    public $validateFields = [
        self::TARGET_NET_TERMS_ID => 'Target NetTermsId Not Found'
    ];

    /**
     * @var \I95DevConnect\NetTerms\Helper\Data
     */
    public $netTermsHelper;

    /**
     * @var NetTermsFactory
     */
    public $nettermsModel;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @param Data $dataHelper
     * @param NetTermsFactory $nettermsModel
     * @param \I95DevConnect\NetTerms\Helper\Data $netTermsHelper
     * @param Manager $eventManager
     * @param Validate $validate
     * @param DateTime $date
     * @param AbstractDataPersistence $abstractDataPersistence
     */
    public function __construct(
        Data $dataHelper,
        NetTermsFactory $nettermsModel,
        \I95DevConnect\NetTerms\Helper\Data $netTermsHelper,
        Manager $eventManager,
        Validate $validate,
        DateTime $date,
        AbstractDataPersistence $abstractDataPersistence
    ) {
        $this->dataHelper = $dataHelper;
        $this->eventManager = $eventManager;
        $this->validate = $validate;
        $this->netTermsHelper = $netTermsHelper;
        $this->nettermsModel = $nettermsModel;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->date = $date;
    }

    /**
     * Method for netterms reverse sync
     * @param array $stringData
     * @param string $entityCode
     * @param string $erp
     * @return I95DevResponseInterfaceFactory $i95DevResponse
     */
    public function create($stringData, $entityCode, $erp) // NOSONAR
    {
        $this->stringData = $stringData;
        $this->entityCode = $entityCode;
        $isEnabled = $this->netTermsHelper->isNetTermsEnabled();

        if ($isEnabled) {
            $this->validate->validateFields = $this->validateFields;
            $this->validate->validateData($this->stringData);

            if (!empty($stringData)) {
                $paymentTermId = $this->dataHelper->getValueFromArray(self::TARGET_NET_TERMS_ID, $this->stringData);
                if ($paymentTermId != "") {
                    $netTermsData = $this->nettermsModel->create()->getCollection()
                            ->addFieldtoFilter('target_net_terms_id', $paymentTermId)
                            ->getData();
                        // @codingStandardsIgnoreStart
                        $paymentTermId = $this->dataHelper->getValueFromArray(self::TARGET_NET_TERMS_ID, $this->stringData);
                        // @codingStandardsIgnoreEnd
                        $description = $this->dataHelper->getValueFromArray("description", $this->stringData);
                        $dueType = $this->dataHelper->getValueFromArray("dueDateCalculation", $this->stringData);
                        $dueTypeWithValue = $this->dataHelper->getValueFromArray("dueTypeWithValue", $this->stringData);
                        $discountType = $this->dataHelper
                            ->getValueFromArray("discountDateCalculation", $this->stringData);
                        $discountTypeWithValue = $this->dataHelper
                            ->getValueFromArray("discountTypeWithValue", $this->stringData);
                        $discountCalculationType = $this->dataHelper
                            ->getValueFromArray("discountCalculationType", $this->stringData);
                        $discountPercentage = $this->dataHelper
                            ->getValueFromArray("discountPercentage", $this->stringData);
                        $discountAmount = $this->dataHelper->getValueFromArray("discountAmount", $this->stringData);
                        $saleOrPurchase = $this->dataHelper->getValueFromArray("saleOrPurchase", $this->stringData);

                    if (empty($netTermsData)) {
                        $netTermsData = $this->nettermsModel->create();
                        $netTermsData->setTargetNetTermsId($paymentTermId);
                        $netTermsData->setDescription($description);
                        $netTermsData->setDueType($dueType);
                        $netTermsData->setDueTypeWithValue($dueTypeWithValue);
                        $netTermsData->setDiscountType($discountType);
                        $netTermsData->setDiscountTypeWithValue($discountTypeWithValue);
                        $netTermsData->setDiscountCalculationType($discountCalculationType);
                        $netTermsData->setDiscountPercentage($discountPercentage);
                        $netTermsData->setDiscountAmount($discountAmount);
                        $netTermsData->setCreatedDate($this->date->gmtDate());
                        $netTermsData->setUpdatedDate($this->date->gmtDate());
                        $netTermsData->setSaleOrPurchase($saleOrPurchase);
                    } else {
                        $netTermsDataId = $netTermsData[0]['net_terms_id'];
                        $netTermsData = $this->nettermsModel->create()->load($netTermsDataId);
                    }

                    $netTermsData->setTargetNetTermsId($paymentTermId);
                    $netTermsData->setDescription($description);
                    $netTermsData->setDueType($dueType);
                    $netTermsData->setDueTypeWithValue($dueTypeWithValue);
                    $netTermsData->setDiscountType($discountType);
                    $netTermsData->setDiscountTypeWithValue($discountTypeWithValue);
                    $netTermsData->setDiscountCalculationType($discountCalculationType);
                    $netTermsData->setDiscountPercentage($discountPercentage);
                    $netTermsData->setDiscountAmount($discountAmount);
                    $netTermsData->setCreatedDate($this->date->gmtDate());
                    $netTermsData->setUpdatedDate($this->date->gmtDate());
                    $netTermsData->setSaleOrPurchase($saleOrPurchase);

                    $beforeeventname = 'i95dev_messagequeuetomagento_beforesave_' . $entityCode;
                    $this->eventManager->dispatch($beforeeventname, ['currentObject' => $this]);
                    $netTermsData->Save();

                    $magentoId = $netTermsData->getId();
                    $aftereventname = 'i95dev_messagequeuetomagento_aftersave_' . $entityCode;
                    $this->eventManager->dispatch($aftereventname, ['currentObject' => $this]);

                    return $this->abstractDataPersistence->setResponse(
                        Data::SUCCESS,
                        "Record Successfully Synced",
                        $magentoId
                    );
                } else {
                    return $this->abstractDataPersistence->setResponse(
                        Data::ERROR,
                        __("Net Term target id is required"),
                        null
                    );
                }
            }
        } else {
            return $this->abstractDataPersistence->setResponse(
                Data::ERROR,
                __("Please enable Net Terms Module"),
                null
            );
        }
    }
}
