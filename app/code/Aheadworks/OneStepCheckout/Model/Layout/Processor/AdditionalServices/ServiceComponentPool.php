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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ServiceComponentPool
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices
 */
class ServiceComponentPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $pool = [
        'google-autocomplete' => GoogleAutocomplete::class
    ];

    /**
     * @var ServiceComponentInterface[]
     */
    private $instances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $pool
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $pool = []
    ) {
        $this->objectManager = $objectManager;
        $this->pool = array_merge($this->pool, $pool);
    }

    /**
     * Get service component instance
     *
     * @param string $code
     * @return ServiceComponentInterface|null
     * @throws \Exception
     */
    public function getServiceComponent($code)
    {
        if (!isset($this->instances[$code])) {
            if (!isset($this->pool[$code])) {
                return null;
            }
            $instance = $this->objectManager->create($this->pool[$code]);
            if (!$instance instanceof ServiceComponentInterface) {
                throw new LocalizedException(
                    sprintf(
                        'Service component %s does not implement required interface.',
                        $code
                    )
                );
            }
            $this->instances[$code] = $instance;
        }
        return $this->instances[$code];
    }
}
