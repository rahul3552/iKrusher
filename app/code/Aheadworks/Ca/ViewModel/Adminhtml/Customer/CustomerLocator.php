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
namespace Aheadworks\Ca\ViewModel\Adminhtml\Customer;

use Magento\Backend\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;

/**
 * Class RegistryLocator
 */
class CustomerLocator
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @param Registry $registry
     * @param Session $session
     * @param RequestInterface $request
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Registry $registry,
        Session $session,
        RequestInterface $request,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->registry = $registry;
        $this->session = $session;
        $this->request = $request;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Retrieve customer
     *
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        if (null == $this->customer) {
            $customerId = $this->getCustomerId();
            if ($customerId) {
                try {
                    $customer = $this->customerRepository->getById($customerId);
                } catch (\Exception $e) {
                    $customer = null;
                }
                $this->customer = $customer;
            }
        }

        return $this->customer;
    }

    /**
     * Retrieve customer id
     *
     * @return int|null
     */
    private function getCustomerId()
    {
        if ($id = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)) {
            return $id;
        }
        if ($data = $this->session->getCustomerData()) {
            return $data['customer_id'] ?? null;
        }

        if ($id = $this->request->getParam('id')) {
            return $id;
        }

        return null;
    }
}
