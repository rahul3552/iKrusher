<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Controller\User;

use Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CheckEmail
 *
 * @package Aheadworks\Ca\Controller\User
 */
class CheckEmail extends Action
{
    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        CompanyUserManagementInterface $companyUserManagement,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->companyUserManagement = $companyUserManagement;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Check email action
     *
     * @return Json
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        if (!$email) {
            $result = [
                'error' => __('Email is required'),
            ];
        } else {
            try {
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
                $availabilityResult = $this->companyUserManagement->isEmailAvailable($email, $websiteId);
                $result = $this->dataObjectProcessor->buildOutputDataArray(
                    $availabilityResult,
                    EmailAvailabilityResultInterface::class
                );
            } catch (\Exception $e) {
                $result = [
                    'error' => $e->getMessage(),
                    'errorcode' => $e->getCode()
                ];
            }
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }
}
