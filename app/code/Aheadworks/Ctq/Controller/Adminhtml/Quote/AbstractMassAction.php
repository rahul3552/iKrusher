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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Aheadworks\Ctq\Api\SellerQuoteManagementInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\Ctq\Model\ResourceModel\Quote\Collection;
use Aheadworks\Ctq\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\Auth\Session as AuthSession;

/**
 * Class AbstractMassAction
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
abstract class AbstractMassAction extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SellerQuoteManagementInterface
     */
    protected $sellerQuoteManagement;

    /**
     * @var AuthSession
     */
    protected $authSession;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param SellerQuoteManagementInterface $sellerQuoteManagement
     * @param AuthSession $authSession
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        SellerQuoteManagementInterface $sellerQuoteManagement,
        AuthSession $authSession
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->sellerQuoteManagement = $sellerQuoteManagement;
        $this->authSession = $authSession;
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
