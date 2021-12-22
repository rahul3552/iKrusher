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
namespace Aheadworks\Ca\Model\Company\Address;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Customer\Block\Address\Renderer\RendererInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Ca\Api\Data\CompanyInterface;

/**
 * Class Renderer
 *
 * @package Aheadworks\Ca\Model\Company\Address
 */
class Renderer
{
    /**
     * @var AddressConfig
     */
    private $addressConfig;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @var EntityProcessor
     */
    private $processor;

    /**
     * @param AddressConfig $addressConfig
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param AddressInterfaceFactory $addressFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param EntityProcessor $processor
     */
    public function __construct(
        AddressConfig $addressConfig,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        AddressInterfaceFactory $addressFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        EntityProcessor $processor
    ) {
        $this->addressConfig = $addressConfig;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->addressFactory = $addressFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->processor = $processor;
    }

    /**
     * Render customer address
     *
     * @param AddressInterface $address
     * @return string
     */
    public function render($address)
    {
        $formatType = $this->addressConfig->getFormatByCode('html');
        if (!$formatType || !$formatType->getRenderer()) {
            return null;
        }

        /** @var RendererInterface $renderer */
        $renderer = $formatType->getRenderer();
        $flatAddressArray = $this->convertAddressToArray($address);

        return empty($flatAddressArray) ? '' : $renderer->renderArray($flatAddressArray);
    }

    /**
     * Render address from company
     *
     * @param CompanyInterface $company
     * @return string
     */
    public function renderAddressFromCompany($company)
    {
        /** @var AddressInterface $company */
        $address = $this->addressFactory->create();
        $addressData = $this->dataObjectProcessor->buildOutputDataArray($company, CompanyInterface::class);

        $addressData = $this->processor->process($addressData);

        $this->dataObjectHelper->populateWithArray(
            $address,
            $addressData,
            AddressInterface::class
        );

        return $this->render($address);
    }

    /**
     * Convert address to flat array
     *
     * @param AddressInterface $address
     * @return array
     */
    private function convertAddressToArray($address)
    {
        $flatAddressArray = $this->extensibleDataObjectConverter->toFlatArray(
            $address,
            [],
            AddressInterface::class
        );

        return $this->prepareAddressData($flatAddressArray, $address);
    }

    /**
     * Prepare address data
     *
     * @param array $flatAddressArray
     * @param AddressInterface $address
     * @return mixed
     */
    private function prepareAddressData($flatAddressArray, $address)
    {
        $street = $address->getStreet();
        if (!empty($street) && is_array($street)) {
            $streetKeys = array_keys($street);
            foreach ($streetKeys as $key) {
                unset($flatAddressArray[$key]);
            }
            $flatAddressArray[AddressInterface::STREET] = $street;
        }

        return $flatAddressArray;
    }
}
