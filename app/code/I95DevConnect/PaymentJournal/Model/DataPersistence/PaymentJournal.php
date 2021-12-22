<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Model\DataPersistence;

/**
 * Payment Journal class for getInfo, set response
 */
class PaymentJournal
{

    /**
     * @var PaymentJournal\Forward\Info
     */
    public $paymentJournalInfo;

    /**
     * @var PaymentJournal\Forward\Response
     */
    public $paymentJournalResponse;

    /**
     * PaymentJournal constructor.
     * @param PaymentJournal\Forward\Info $paymentJournalInfo
     * @param PaymentJournal\Forward\Response $paymentJournalResponse
     */
    public function __construct(
        \I95DevConnect\PaymentJournal\Model\DataPersistence\PaymentJournal\Forward\Info $paymentJournalInfo,
        \I95DevConnect\PaymentJournal\Model\DataPersistence\PaymentJournal\Forward\Response $paymentJournalResponse
    ) {
        $this->paymentJournalInfo = $paymentJournalInfo;
        $this->paymentJournalResponse = $paymentJournalResponse;
    }

    /**
     * Get Payment Journal Info
     * @param $paymentJournalId
     * @param $entityCode
     * @param $erpCode
     * @return array
     * @author Hrusikesh Manna
     */
    public function getInfo($paymentJournalId, $entityCode, $erpCode)
    {
        return $this->paymentJournalInfo->getInfo($paymentJournalId, $entityCode, $erpCode);
    }

    /**
     * Get Payment Journal Response
     * @param $requestString
     * @param $entityCode
     * @param $erpCode
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @author Hrusikesh Manna
     */
    public function getResponse($requestString, $entityCode, $erpCode)
    {
        return $this->paymentJournalResponse->getResponse($requestString, $entityCode, $erpCode);
    }
}
