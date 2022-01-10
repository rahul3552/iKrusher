<?php

namespace Addify\RestrictOrderByCustomer\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Addify\RestrictOrderByCustomer\Model\ResourceModel\RestrictOrderByCustomer\CollectionFactory;

class MassDisable extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    protected $_tabFactory;
    protected $_productsFactory;
    protected $_productRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory,
        \Addify\RestrictOrderByCustomer\Model\ResourceModel\RestrictOrderByCustomer\CollectionFactory $tabFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) 
    {
            
        $this->_tabFactory = $tabFactory;
        $this->_productsFactory = $productFactory;
        $this->_productRepository = $productRepository;

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $page) {

            $page->setData('is_active', '0');
            $page->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 item(s) have been disabled.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
    
}