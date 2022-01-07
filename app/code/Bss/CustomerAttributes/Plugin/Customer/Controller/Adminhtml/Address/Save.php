<?php
declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bss\CustomerAttributes\Plugin\Customer\Controller\Adminhtml\Address;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class for saving of customer address
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Address\Save
{
    const CUSTOMER_ADDRESS = 'customer_address';

    protected $customerAddress;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var \Bss\CustomerAttributes\Helper\Customerattribute
     */
    protected $helper;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Customer::manage';

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    private $formFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    private $addressDataFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

   public function __construct(
       \Bss\CustomerAttributes\Helper\CustomerAddress $customerAddress,
       \Bss\CustomerAttributes\Helper\Customerattribute $helper,
       Action\Context $context,
       \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
       \Magento\Customer\Model\Metadata\FormFactory $formFactory,
       \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
       \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
       \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
       LoggerInterface $logger,
       JsonFactory $resultJsonFactory
   ) {
       $this->customerAddress = $customerAddress;
       $this->addressRepository = $addressRepository;
       $this->formFactory = $formFactory;
       $this->customerRepository = $customerRepository;
       $this->dataObjectHelper = $dataObjectHelper;
       $this->addressDataFactory = $addressDataFactory;
       $this->logger = $logger;
       $this->resultJsonFactory = $resultJsonFactory;
       parent::__construct(
           $context,
           $addressRepository,
           $formFactory,
           $customerRepository,
           $dataObjectHelper,
           $addressDataFactory,
           $logger,
           $resultJsonFactory
       );
       $this->helper = $helper;
   }


    /**
     * Save customer address action
     * @param  \Magento\Customer\Controller\Adminhtml\Address\Save $subject
     * @param \Closure $proceed
     *
     * @return Json
     */
    public function aroundExecute($subject , $proceed)
    {
        $customerId = $this->getRequest()->getParam('parent_id', false);
        $addressId = $this->getRequest()->getParam('entity_id', false);

        $error = false;
        try {
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
            $customer = $this->customerRepository->getById($customerId);

            $addressForm = $this->formFactory->create(
                'customer_address',
                'adminhtml_customer_address',
                [],
                false,
                false
            );

            $addressData = $addressForm->extractData($this->getRequest());
            $addressData = $addressForm->compactData($addressData);

            $addressData['region'] = [
                'region' => $addressData['region'] ?? null,
                'region_id' => $addressData['region_id'] ?? null,
            ];
            $addressToSave = $this->addressDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $addressToSave,
                $addressData,
                \Magento\Customer\Api\Data\AddressInterface::class
            );
            $addressToSave->setCustomerId($customer->getId());
            $addressToSave->setIsDefaultBilling(
                (bool)$this->getRequest()->getParam('default_billing', false)
            );
            $addressToSave->setIsDefaultShipping(
                (bool)$this->getRequest()->getParam('default_shipping', false)
            );
            if ($addressId) {
                $addressToSave->setId($addressId);
                $message = __('Customer address has been updated.');
            } else {
                $addressToSave->setId(null);
                $message = __('New customer address has been added.');
            }
            $savedAddress = $this->addressRepository->save($addressToSave);
            $addressId = $savedAddress->getId();
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e);
            $error = true;
            $message = __('There is no customer with such id.');
        } catch (LocalizedException $e) {
            $error = true;
            $message = __($e->getMessage());
            $this->logger->critical($e);
        } catch (\Exception $e) {
            $error = true;
            $message = __('We can\'t change customer address right now.');
            $this->logger->critical($e);
        }

        $addressId = empty($addressId) ? null : $addressId;
        $resultJson = $this->resultJsonFactory->create();
        $attributeAddress = $this->helper->converAddressCollectioin();
        $resultJson->setData(
            [
                'messages' => $message,
                'error' => $error,
                'data' => [
                    'entity_id' => $addressId
                ],
                'custom_attributes_address' => $this->customerAddress->getDataCustomAddress($addressData, $attributeAddress)
            ]
        );

        return $resultJson;
    }
}
