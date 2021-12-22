<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class PrintAction extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultForwardFactory;
    protected $fileFactory;
    
    /**
     * @var \I95DevConnect\BillPay\Model\Customer\Payment\Pdf\View
     */
    protected $pdfView;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @param Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \I95DevConnect\BillPay\Model\Customer\Payment\Pdf\ViewFactory $pdfView
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \I95DevConnect\BillPay\Model\Customer\Payment\Pdf\ViewFactory $pdfView,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->fileFactory = $fileFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->pdfView = $pdfView;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }

    /**
     * manage payments
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $payment_id = $this->getRequest()->getParam('payment_id');

        if ($payment_id) {
            $pdf = $this->pdfView->create()->getPdf($payment_id);
            $date = $this->dateTime->date('Y-m-d_H-i-s');
            return $this->fileFactory->create(
                'ArDetails' . $date . '.pdf',
                $pdf->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
