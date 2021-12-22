<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentMapping
 */

namespace I95DevConnect\PaymentMapping\Setup;

use I95DevConnect\MessageQueue\Helper\Data;
use I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Reverse\Payment;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Upgrade Data script
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var DateTime
     */
    public $date;
    /**
     * @var FileResolverInterface
     */
    public $fileResolver;
    /**
     * @var Payment
     */
    public $payment;
    /**
     * @var Data
     */
    public $dataHelper;
    /**
     * @var LoggerInterface
     */
    public $logger;
    /**
     * @var State
     */
    public $appState;

    /**
     * UpgradeData constructor.
     * @param FileResolverInterface $fileResolver
     * @param Payment $payment
     * @param Data $dataHelper
     * @param State $appState
     * @param LoggerInterface $logger
     * @param DateTime $date
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        Payment $payment,
        Data $dataHelper,
        State $appState,
        LoggerInterface $logger,
        DateTime $date
    ) {
        $this->fileResolver = $fileResolver;
        $this->payment = $payment;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        $this->appState = $appState;
        $this->date = $date;

        /* Set the area code and catch exception if thrown */
        try {
            $this->appState->setAreaCode('global');
        } catch (LocalizedException $exception) {
            $this->logger->debug($exception->getMessage());
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            $setup->startSetup();
            if (version_compare($context->getVersion(), '1.0.1') < 0) {
                $csvRow = [];
                $csvData = $this->fileResolver->get("payment.csv", 'global');
                if (count($csvData) > 0) {
                    $activeMethods = $this->payment->getActivePaymentMethods();
                    if (!empty($activeMethods)) {
                        foreach ($csvData as $content) {
                            $csvRow = str_getcsv($content, "\n");
                        }

                        $mappingList = $this->prepareListData($csvRow, $activeMethods);

                        if (!empty($mappingList)) {
                            $setup->getConnection()->insert(
                                $setup->getTable('i95dev_payment_mapping_list'),
                                [
                                    'mapped_data' => json_encode($mappingList),
                                    'created_at' => $this->date->gmtDate(),
                                ]
                            );
                        }
                    }
                }
            }

            $setup->endSetup();

        } catch (LocalizedException $exception) {
            $this->logger->debug($exception->getMessage());
        }
    }

    /**
     * @param $csvRow
     * @param $activeMethods
     * @return mixed
     */
    public function prepareListData($csvRow, $activeMethods)
    {
        $mappingList = [];
        foreach ($csvRow as $mappingData) {
            $currentEntity = explode(",", $mappingData);
            if (in_array($currentEntity[1], $activeMethods)) {
                $mappingList[] = [
                    "ecommerceMethod" => $currentEntity[1],
                    "erpMethod" => $currentEntity[2],
                    "isEcommerceDefault" => $currentEntity[3],
                    "isErpDefault" => $currentEntity[4],
                ];
            }
        }

        return $mappingList;
    }
}
