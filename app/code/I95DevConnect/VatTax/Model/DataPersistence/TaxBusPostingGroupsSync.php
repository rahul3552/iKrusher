<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\DataPersistence;

/**
 * Class for Tax Bus Posting Groups Sync
 */
class TaxBusPostingGroupsSync
{
    private $requestHelper;
    public $taxBusPostingGroupsCreate;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \I95DevConnect\VatTax\Model\DataPersistence\TaxBusPostingGroupsSync\Create $taxBusPostingGroupsCreate
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \I95DevConnect\VatTax\Model\DataPersistence\TaxBusPostingGroupsSync\Create $taxBusPostingGroupsCreate
    ) {
        $this->requestHelper = $requestHelper;
        $this->taxBusPostingGroups = $taxBusPostingGroupsCreate;
    }

    /**
     *
     * @param string $stringData
     * @param string $entityCode
     * @param string $erp
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($stringData, $entityCode, $erp)
    {
        return $this->taxBusPostingGroups->sync($stringData, $entityCode, $erp);
    }
}
