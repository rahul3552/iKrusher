<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\DataPersistence;

/**
 * Class for Tax Posting Setup Sync
 */
class TaxPostingSetupSync
{

    private $requestHelper;
    public $taxPostingSetupCreate;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \I95DevConnect\VatTax\Model\DataPersistence\TaxPostingSetupSync\Create $taxPostingSetupCreate
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \I95DevConnect\VatTax\Model\DataPersistence\TaxPostingSetupSync\Create $taxPostingSetupCreate
    ) {
        $this->requestHelper = $requestHelper;
        $this->taxPostingSetup = $taxPostingSetupCreate;
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
        return $this->taxPostingSetup->sync($stringData, $entityCode, $erp);
    }
}
