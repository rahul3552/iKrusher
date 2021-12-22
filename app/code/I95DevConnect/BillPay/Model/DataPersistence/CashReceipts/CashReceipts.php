<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\DataPersistence\CashReceipts;

class CashReceipts
{

    public $requestHelper;
    public $cashReceiptInfo;
    public $cashReceiptResponse;
    public $cashReceiptCreate;

    public function __construct(
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \I95DevConnect\BillPay\Model\DataPersistence\CashReceipts\CashReceipts\Create $cashReceiptCreate
    ) {
        $this->requestHelper = $requestHelper;
        $this->cashReceiptCreate = $cashReceiptCreate;
    }

    public function create($stringData, $entityCode, $erp)
    {
        return $this->cashReceiptCreate->createCR($stringData, $entityCode, $erp);
    }
}
