<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */
namespace I95DevConnect\VatTax\Model\DataPersistence;

use \I95DevConnect\VatTax\Model\DataPersistence\TaxProductPostingGroupsSync\Create;

/**
 * Class for Tax Product Posting Groups Sync
 */
class TaxProductPostingGroupsSync
{

    private $requestHelper;
    public $taxProductPostingGroupsCreate;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param Create $taxProductPostingGroupsCreate
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        Create $taxProductPostingGroupsCreate
    ) {
        $this->requestHelper = $requestHelper;
        $this->taxProductPostingGroups = $taxProductPostingGroupsCreate;
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
        return $this->taxProductPostingGroups->sync($stringData, $entityCode, $erp);
    }
}
