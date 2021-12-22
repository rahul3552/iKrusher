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
namespace Aheadworks\Ca\Controller\Adminhtml\Company;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ca\Model\ResourceModel\Company\Collection;
use Aheadworks\Ca\Model\ResourceModel\Company\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class AbstractMassAction
 * @package Aheadworks\Ca\Controller\Adminhtml\Company
 */
abstract class AbstractMassAction extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SellerCompanyManagementInterface
     */
    protected $companyManagement;

    /**
     * @var CompanyRepositoryInterface
     */
    protected $companyRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CompanyRepositoryInterface $companyRepository
     * @param SellerCompanyManagementInterface $companyManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CompanyRepositoryInterface $companyRepository,
        SellerCompanyManagementInterface $companyManagement
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->companyRepository = $companyRepository;
        $this->companyManagement = $companyManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            /** @var Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/index');
        }
    }

    /**
     * Performs mass action
     *
     * @param Collection $collection
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    abstract protected function massAction(Collection $collection);
}
