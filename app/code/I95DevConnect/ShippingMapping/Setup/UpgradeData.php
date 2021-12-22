<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\ShippingMapping\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Config\FileResolverInterface;

/**
 * Upgrade Data script
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * UpgradeData constructor.
     * @param FileResolverInterface $fileResolver
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Reverse\ShippingAddress $shippingAddress
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Reverse\ShippingAddress $shippingAddress,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper
    ) {
        $this->fileResolver = $fileResolver;
        $this->shippingAddress = $shippingAddress;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.5') < 0) {

            $csvData = $this->fileResolver->get("shipping.csv", 'global');

            if (count($csvData) > 0) {

                $activeMethods = $this->shippingAddress->getActiveShippingMethods();
                if (!empty($activeMethods)) {
                    foreach ($csvData as $content) {
                        $csvRow = str_getcsv($content, "\n");
                    }

                    $this->insertShippingMapping($setup, $csvRow, $activeMethods);
                }
            }
        }

        $setup->endSetup();
    }

    /**
     * @param $setup
     * @param $csvRow
     * @param $activeMethods
     */
    public function insertShippingMapping($setup, $csvRow, $activeMethods)
    {
        foreach ($csvRow as $mappingData) {

            $currentEntity = explode(",", $mappingData);

            if (in_array($currentEntity[2], $activeMethods)) {

                $setup->getConnection()->insert(
                    $setup->getTable('i95dev_shipping_mapping_list'),
                    [
                        'erp_code' => $currentEntity[3],
                        'magento_code' => $currentEntity[2],
                        'is_ecommerce_default' => $currentEntity[5],
                        'is_erp_default' => $currentEntity[6]
                    ]
                );
            }
        }
    }
}
