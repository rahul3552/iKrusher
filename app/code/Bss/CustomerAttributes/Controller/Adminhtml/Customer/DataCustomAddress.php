<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerAttributes\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;

/**
 * Class for saving of customer address
 */
class DataCustomAddress extends Action
{

    protected $data;

    /**
     * @var \Bss\CustomerAttributes\Helper\CustomerAddress
     */
    protected $customerAddress;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Save constructor.
     * @param \Bss\CustomerAttributes\Helper\CustomerAddress $customerAddress
     * @param Action\Context $context
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param LoggerInterface $logger
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Bss\CustomerAttributes\Helper\Data $data,
        \Bss\CustomerAttributes\Helper\CustomerAddress $customerAddress,
        Action\Context $context,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        LoggerInterface $logger,
        JsonFactory $resultJsonFactory
    ) {
        $this->data = $data;
        $this->customerAddress = $customerAddress;
        parent::__construct($context);
        $this->addressRepository = $addressRepository;
        $this->logger = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Get custom address
     *
     * @return Json
     */
    public function execute()
    {
        $data = [];
        $error = true;
        if ($this->data->isEnable()) {
            $addressId = $this->getRequest()->getParam('entity_id', false);
            $error = false;
            try {
                /** @var \Magento\Customer\Api\Data\AddressInterface $address */
                $address = $this->addressRepository->getById($addressId);
                if ($customAddress = $address->getCustomAttributes()) {
                    $data = $this->customerAddress->getDataCustomAddressGrid($customAddress);
                }
            } catch (\Exception $e) {
                $error = true;
                $this->logger->critical($e);
            }
        }
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData(
            [
                'error' => $error,
                'custom_attributes_address' => $data
            ]
        );

        return $resultJson;
    }
}
