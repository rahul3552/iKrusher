<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model;

/**
 * Class to calculate Vat Tax based on the Tax Posting Setup
 */
class TaxCalculation
{

    /**
     * var  \Magento\Customer\Model\Customer
     */
    protected $customerModel;

    /**
     * var  \Magento\Catalog\Model\Product
     */
    protected $productModel;

    /**
     * var  \I95DevConnect\VatTax\Model\TaxBusPostingGroups
     */
    protected $businessTaxGroupModel;

    /**
     * var  \I95DevConnect\VatTax\Model\TaxProductPostingGroups
     */
    protected $itemTaxGroupModel;

    /**
     * var  \I95DevConnect\VatTax\Model\TaxPostingSetup
     */
    protected $taxSetupModel;

    /**
     * Quote session object
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $session;

    /**
     * Checkout session object
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Request object
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * TaxCalculation constructor.
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productModel
     * @param TaxPostingSetupFactory $taxSetupModel
     * @param TaxBusPostingGroupsFactory $businessTaxGroupModel
     * @param TaxProductPostingGroupsFactory $itemTaxGroupModel
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\State $request
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productModel,
        \I95DevConnect\VatTax\Model\TaxPostingSetupFactory $taxSetupModel,
        \I95DevConnect\VatTax\Model\TaxBusPostingGroupsFactory $businessTaxGroupModel,
        \I95DevConnect\VatTax\Model\TaxProductPostingGroupsFactory $itemTaxGroupModel,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\State $request,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerModel = $customerModel;
        $this->productModel = $productModel;
        $this->businessTaxGroupModel = $businessTaxGroupModel;
        $this->itemTaxGroupModel = $itemTaxGroupModel;
        $this->taxSetupModel = $taxSetupModel;
        $this->session = $quoteSession;
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Get Tax by item and customer Id
     * @param $itemId
     * @param $customerId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTax($itemId, $customerId)
    {
        $customer = $this->customerModel->load($customerId);
        $businessTaxGroup = $customer->getTaxBusPostingGroup();

        $this->logger->debug($businessTaxGroup);
        $taxRate = 0;
        $busTax = '';
        if ($businessTaxGroup != "") {
            $busTaxGroupsCollection = $this->businessTaxGroupModel->create()
                ->getCollection()
                ->addFieldToSelect("code")
                ->addFieldToFilter("code", $businessTaxGroup)
                ->getData();
            if (!empty($busTaxGroupsCollection)) {
                foreach ($busTaxGroupsCollection as $eachBusTaxGroup) {
                    $busTax = $eachBusTaxGroup["code"];
                }
            }
        }

        $itemTax = '';
        $this->logger->debug($itemId);
        $product = $this->productModel->get($itemId);
        $itemTaxGroup = $product->getTaxProductPostingGroup();
        $itemTax = $this->getItemTax($itemTaxGroup);

        if ($busTax !== '' && $itemTax !== '') {
            $taxGroups = $this->taxSetupModel->create()
                    ->getCollection()
                    ->addFieldToSelect("tax_percentage")
                    ->addFieldToFilter("tax_busposting_group_code", $busTax)
                    ->addFieldToFilter("tax_productposting_group_code", $itemTax)
                    ->getData();

            $this->logger->debug(json_encode($taxGroups));
            if (!empty($taxGroups)) {
                foreach ($taxGroups as $eachTaxGroup) {
                    $taxRate = $eachTaxGroup["tax_percentage"];
                }
                $this->logger->debug($taxRate);
            }
        }

        return $taxRate;
    }

    /**
     * @param $itemTaxGroup
     * @return string
     */
    public function getItemTax($itemTaxGroup)
    {
        $itemTax = "";
        if ($itemTaxGroup != "") {
            $itemTaxGroupsCollection = $this->itemTaxGroupModel->create()
                ->getCollection()
                ->addFieldToSelect("code")
                ->addFieldToFilter("code", $itemTaxGroup)
                ->getData();
            if (!empty($itemTaxGroupsCollection)) {
                foreach ($itemTaxGroupsCollection as $eachItemTaxGroup) {
                    $itemTax = $eachItemTaxGroup["code"];
                }
            }
        }

        return $itemTax;
    }
}
