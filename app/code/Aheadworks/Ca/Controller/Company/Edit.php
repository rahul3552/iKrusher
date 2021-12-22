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
namespace Aheadworks\Ca\Controller\Company;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Ca\Controller\Company
 */
class Edit extends AbstractCompanyAction
{
    /**
     * {@inheritdoc}
     */
    const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param PageFactory $resultPageFactory
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        PageFactory $resultPageFactory,
        CompanyRepositoryInterface $companyRepository
    ) {
        parent::__construct($context, $customerSession, $companyRepository);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $company = $this->getEntity();
        $resultPage = $this->resultPageFactory->create();
        $this->getRequest()->setPostValue('id', $company->getId());
        $resultPage->getConfig()->getTitle()->set(__('Edit "%1" company', $company->getName()));

        return $resultPage;
    }
}
