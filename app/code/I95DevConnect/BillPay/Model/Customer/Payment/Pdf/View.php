<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\Customer\Payment\Pdf;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Sales Order Invoice PDF model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{

    const ALIGN = 'align';
    const RIGHT = 'right';
    const TH = 'table_header';
    const UTF = 'UTF-8';
    const PT = 'payment_type';
    const TIID = 'target_invoice_id';
    const A = "amount";
    const S = "status";

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;
    public $_leftRectPad = 45;
    public $_leftTextPad = 55;
    protected $_arPaymentModel;
    protected $_arPaymentDetailsModel;
    protected $_arBookModel;
    protected $_scopeConfig;
    protected $_priceCurrency;
    protected $_date;
    protected $_logger;

    const XML_PATH_FOOTER_TEMPLATE = 'i95devconnect_billpay/billpay_enabled_settings/pdf_text';

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \I95DevConnect\BillPay\Model\ArPayment $arPaymentModel,
        \I95DevConnect\BillPay\Model\ArPaymentDetails $arPaymentDetailsModel,
        \I95DevConnect\BillPay\Model\Arbook $arBookModel,
        \Magento\Framework\Stdlib\DateTime $date,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_scopeConfig = $scopeConfig;
        $this->_priceCurrency = $priceCurrency;
        $this->_arPaymentModel = $arPaymentModel;
        $this->_arPaymentDetailsModel = $arPaymentDetailsModel;
        $this->_arBookModel = $arBookModel;
        $this->_date = $date;
        $this->_logger = $logger;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data
        );
    }

    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 9);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle($this->_leftRectPad, $this->y - 15, 570, $this->y - 35);
        $this->y -= 25;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.4, 0.4, 0.4));
        //columns headers
        $lines[0][] = ['text' => __('Order ID'), 'feed' => 50];
        $lines[0][] = ['text' => __('Return ID'), 'feed' => 185, self::ALIGN => self::RIGHT];
        $lines[0][] = ['text' => __('Invoice ID'), 'feed' => 300, self::ALIGN => self::RIGHT];
        $lines[0][] = ['text' => __('Invoice Date'), 'feed' => 400, self::ALIGN => self::RIGHT];
        $lines[0][] = ['text' => __('Amount Adjusted'), 'feed' => 490, self::ALIGN => self::RIGHT];
        $lines[0][] = ['text' => __('Invoice Status'), 'feed' => 560, self::ALIGN => self::RIGHT];
        $lineBlock = ['lines' => $lines, 'height' => 5];
        $this->drawLineBlocks($page, [$lineBlock], [self::TH => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($payment_id = null)
    {

        $this->_beforeGetPdf();
        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        $page = $this->newPage();
        $this->insertLogo($page);
        $this->insertAddress($page);
        $this->drawHeaderTitle($page);
        $this->insertTitles($page, $payment_id);
        $this->insertDocumentNumber($page, __('Payment # ') . $payment_id);
        $this->_drawHeader($page);
        $this->drawItem($page, $payment_id);
        $this->drawfooter($page);
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = [])
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings[self::TH])) {
            $this->_drawHeader($page);
        }
        return $page;
    }

    protected function insertTitles(&$page, $payment_id, $storeId = null)
    {

        $y = $this->y - 25;
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.8));
        $page->setLineWidth(0.5);
        $page->drawRectangle($this->_leftRectPad, $y, 570, $this->y -= 95);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 9);
        $topPosY = $y - 10;

        $y = $y - 10;
        $this->columns = [
                [__('Payment NO:'), $this->_leftTextPad, $y, self::UTF],
                [__('Transaction ID:'), $this->_leftTextPad, $y - 10, self::UTF],
                [__('Payment Date:'), $this->_leftTextPad, $y - 20, self::UTF],
                [__('Status:'), $this->_leftTextPad, $y - 30, self::UTF],
                [__('Payment Type:'), $this->_leftTextPad, $y - 40, self::UTF],
                [__('Payment Amount:'), $this->_leftTextPad, $y - 50, self::UTF]
        ];

        //#draw TABLE TITLES
        foreach ($this->columns as $item) {
            $textLabel = $item[0];
            $textPosX = $item[1];
            $textPosY = $item[2];
            $textEncod = $item[3];
            $page->drawText($textLabel, $textPosX, $textPosY, $textEncod);
        }

        $ardetail = $this->getArdetail($payment_id);
        for ($i = 0; $i < 6; $i++) {
            $page->drawText($ardetail[$i], $this->_leftTextPad + 70, $y - ($i * 10), self::UTF);
        }
        $y -= 10;
        $this->_setFontRegular($page, 9);
        $x = 380;
        $xPad = $x + 100;
        $y = $topPosY;
        $page->drawText(__('Cash Receipt Number:'), $x, $topPosY, self::UTF);
        $page->drawText($ardetail[6], $xPad, $y, self::UTF);
        $y -= 10;
    }

    /**
     * Draw header title
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    public function drawHeaderTitle($page)
    {
        $y = $this->y - 20;
        $this->_setFontRegular($page, 15);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->drawText(__('Payment Details'), $this->_leftTextPad, $y + 10, self::UTF);
    }

    public function drawfooter($page)
    {
        $footertext = $this->_scopeConfig->getValue(self::XML_PATH_FOOTER_TEMPLATE);
        $y = 20;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineWidth(0.5);
        $page->drawRectangle($this->_leftRectPad, $y, 585, 50);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->drawText($footertext, $this->_leftRectPad + 20, $y + 10, self::UTF);
    }

    protected function drawItem(\Zend_Pdf_Page $page, $payment_id)
    {
        /* Add table head */
        $this->_setFontRegular($page, 9);
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $this->y += 1;
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        //columns headers
        $items = $this->getPaymentdetail($payment_id);
        foreach ($items as $item) {
            $lines[0][] = ['text' => $item[0], 'feed' => 50];
            $lines[0][] = ['text' => $item[1], 'feed' => 185, self::ALIGN => self::RIGHT];
            $lines[0][] = ['text' => $item[2], 'feed' => 300, self::ALIGN => self::RIGHT];
            $lines[0][] = ['text' => $item[4], 'feed' => 400, self::ALIGN => self::RIGHT];
            $lines[0][] = ['text' => $item[5], 'feed' => 490, self::ALIGN => self::RIGHT];
            $lines[0][] = ['text' => $item[6], 'feed' => 560, self::ALIGN => self::RIGHT];
            $lineBlock = ['lines' => $lines, 'height' => 5];

            $this->drawLineBlocks($page, [$lineBlock], [self::TH => true]);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->y -= 15;
            unset($lines);
        }
    }

    public function getArdetail($paymentId)
    {
        if ($paymentId) {
            try {
                $collection = $this->_arPaymentModel->load($paymentId);
                $collectionData = (is_object($collection)) ? $collection->getData() : [];

                if (!empty($collectionData)) {
                    $paymentDate = $this->_date->formatDate($collection->getData('payment_date'), false);

                    $this->GPPaymentId = $collection->getData('cash_receipt_number');
                    $GPSyncStatus = $collection->getData('gp_sync_status');
                    if ($GPSyncStatus) {
                        $this->GPSyncStatus = "Payment Synced to GP";
                    } else {
                        $this->GPSyncStatus = "Payment Not Synced to GP";
                    }
                    $ardetails = [];
                    $ardetails[0] = $paymentId;
                    $ardetails[1] = $collection->getData('payment_trans_id');
                    $ardetails[2] = $paymentDate;
                    $ardetails[3] = ucfirst($collection->getData(self::S));
                    $paymentMethod = ucfirst(
                        $this->_scopeConfig->getValue(
                            'payment/' . $collection
                                ->getData(self::PT) . '/title'
                        )
                    );
                    if ($collection->getData(self::PT) == 'cashondelivery'
                        || $collection->getData(self::PT) == 'cash payment') {
                        $paymentMethod = 'Cash Payment';
                    }
                    $ardetails[4] = $paymentMethod;
                    $ardetails[5] = $this->_priceCurrency->format($collection->getData('total_amt'), false);
                    $ardetails[6] = $collection->getData('cash_receipt_number');
                    $ardetails[7] = $collection->getData('payment_comment');
                    return $ardetails;
                }
            } catch (\Exception $ex) {
                $this->_logger->createLog(
                    __METHOD__,
                    $ex->getMessage(),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    'critical'
                );
            }
        }
    }

    public function getPaymentdetail($paymentId)
    {
        try {
            $paymentDetails = $this->_arPaymentDetailsModel->getCollection()
                ->addFieldToFilter('payment_id', $paymentId);
            $paymentDetailsData = $paymentDetails->getData();
            $paymentBookData = [];
            $paymentdata = [];

            foreach ($paymentDetailsData as $payDetails) {
                $invoiceId = $payDetails[self::TIID];
                $cpay = $this->_arBookModel->getCollection()
                    ->addFieldToFilter(self::TIID, $invoiceId)->getData();
                $paymentBook = current($cpay);
                $paymentBook[self::A] = $payDetails[self::A];
                $paymentBook[self::S] = $payDetails[self::S];
                $paymentBookData[] = $paymentBook;
            }
            if (!empty($paymentBookData)) {
                $i = 0;
                foreach ($paymentBookData as $_eachpaymentData) {
                    $this->getPaymentData($i, $_eachpaymentData, $paymentdata);
                    $i++;
                }
            }
        } catch (\Exception $ex) {
	   $this->_logger->createLog(
                    __METHOD__,
                    $ex->getMessage(),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    'critical'
                );
        }
        return $paymentdata;
    }

    public function getPaymentData($i, $_eachpaymentData, &$paymentdata)
    {
        $paymentdata[$i][0] = isset($_eachpaymentData['magento_order_id'])
            ? $_eachpaymentData['magento_order_id'] : "";
        $type = ucfirst(isset($_eachpaymentData['type']) ? $_eachpaymentData['type'] : "");
        $returnId = $invoiceId = '';
        if ($type == 'Return') {
            $returnId = isset($_eachpaymentData[self::TIID])
                ? $_eachpaymentData[self::TIID] : "";
        } else {
            $invoiceId = isset($_eachpaymentData[self::TIID])
                ? $_eachpaymentData[self::TIID] : "";
        }
        $paymentdata[$i][1] = $returnId;
        $paymentdata[$i][2] = $invoiceId;
        $paymentdata[$i][3] = isset($_eachpaymentData['customer_po_number'])
            ? $_eachpaymentData['customer_po_number'] : "";
        $paymentdata[$i][4] = $this->_date->formatDate(isset($_eachpaymentData['modified_date'])
            ? $_eachpaymentData['modified_date'] : "", false);
        $paymentdata[$i][5] = $this->_priceCurrency->format(isset($_eachpaymentData[self::A])
            ? $_eachpaymentData[self::A] : "", false);
        $paymentdata[$i][6] = ucfirst($_eachpaymentData[self::S]);
    }
}
