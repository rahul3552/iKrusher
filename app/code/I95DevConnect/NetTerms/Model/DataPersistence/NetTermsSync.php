<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Model\DataPersistence;

use I95DevConnect\MessageQueue\Helper\ServiceRequest;
use \I95DevConnect\MessageQueue\Model\AbstractDataPersistence;
use I95DevConnect\NetTerms\Model\DataPersistence\NetTermsSync\Create;
use \Magento\Store\Model\ScopeInterface;

/**
 * Class for Netterms sync
 */
class NetTermsSync
{
    private $requestHelper;
    public $netTermsInfo;
    public $netTermsResponse;
    public $netTermsCreate;

    /**
     * Constructor
     * @param ServiceRequest $requestHelper
     * @param Create $netTermsCreate
     */
    public function __construct(
        ServiceRequest $requestHelper,
        Create $netTermsCreate
    ) {
        $this->requestHelper = $requestHelper;
        $this->netTermsCreate = $netTermsCreate;
    }

    /**
     * @param $stringData
     * @param $entityCode
     * @param $erp
     * @return NetTermsSync\I95DevResponseInterfaceFactory
     */
    public function create($stringData, $entityCode, $erp)
    {
        return $this->netTermsCreate->create($stringData, $entityCode, $erp);
    }
}
