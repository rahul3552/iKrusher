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
namespace Aheadworks\Ctq\Controller\QuoteList;

use Aheadworks\Ctq\Model\QuoteList\Permission\Checker;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Aheadworks\Ctq\Controller\QuoteList
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Checker
     */
    private $checker;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Checker $checker
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Checker $checker
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->checker->isAllowed()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Quote List'));

            return $resultPage;
        } else {
            return $this->_redirect('noRoute');
        }
    }
}
