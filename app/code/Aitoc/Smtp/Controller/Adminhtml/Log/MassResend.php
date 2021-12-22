<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_Smtp
 */


namespace Aitoc\Smtp\Controller\Adminhtml\Log;

use Aitoc\Smtp\Model\ResourceModel\Log\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Aitoc\Smtp\Model\Sender;
use Magento\Framework\Registry;
use Aitoc\Smtp\Controller\RegistryConstants;

class MassResend extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Sender $sender,
        Registry $registry,
        CollectionFactory $collectionFactory
    )
    {
        $this->filter = $filter;
        $this->registry = $registry;
        $this->sender = $sender;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $resCount = 0;

        if ($collection->getSize()) {
            $this->registry->register(RegistryConstants::CURRENT_RULE, true);
        }

        foreach ($collection as $item) {
            if ($this->sender->sendByLogId($item->getLogId())) {
                $resCount += 1;
            }
        }

        $this->messageManager->addSuccess(__('A total of %1 email(s) have been sent.', $resCount));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('aitoc_smtp/*/index');
    }
}
