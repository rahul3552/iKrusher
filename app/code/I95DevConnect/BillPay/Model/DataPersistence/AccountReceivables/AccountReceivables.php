<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\BillPay\Model\DataPersistence\AccountReceivables;

class AccountReceivables
{

    public $requestHelper;
    public $accountReceivablesInfo;
    public $accountReceivablesResponse;
    public $accountReceivablesCreate;

    public function __construct(
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \I95DevConnect\BillPay\Model\DataPersistence\AccountReceivables\AccountReceivables\Response $accountReceivablesResponse,
        \I95DevConnect\BillPay\Model\DataPersistence\AccountReceivables\AccountReceivables\Info $accountReceivablesInfo,
        \I95DevConnect\BillPay\Model\DataPersistence\AccountReceivables\AccountReceivables\Create $accountReceivablesCreate
    ) {
        $this->requestHelper = $requestHelper;
        $this->accountReceivablesResponse = $accountReceivablesResponse;
        $this->accountReceivablesInfo = $accountReceivablesInfo;
        $this->accountReceivablesCreate = $accountReceivablesCreate;
    }

    public function create($stringData, $entityCode, $erp)
    {
        try {
            return $this->accountReceivablesCreate->create($stringData, $entityCode, $erp);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }

    public function getInfo($paymentId, $entityCode, $erpCode)
    {
        try {
            return  $this->accountReceivablesInfo->getInfo($paymentId, $entityCode, $erpCode);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }

    public function getResponse($requestData, $entityCode, $erpCode) //NOSONAR
    {
        try {
            return $this->accountReceivablesResponse->setResponse($requestData, $erpCode);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }
}
