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
namespace Aheadworks\OneStepCheckout\Model\Layout;

use Magento\Framework\ObjectManagerInterface;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class LayoutProcessorProvider
 * @package Aheadworks\OneStepCheckout\Model\Layout
 */
class LayoutProcessorProvider
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var LayoutProcessorInterface[]
     */
    private $metadataInstances = [];

    /**
     * @var array
     */
    private $processors = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $processors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $processors = []
    ) {
        $this->objectManager = $objectManager;
        $this->processors = $processors;
    }

    /**
     * Retrieves array of layout processors
     *
     * @return LayoutProcessorInterface[]
     */
    public function getLayoutProcessors()
    {
        if (empty($this->metadataInstances)) {
            foreach ($this->processors as $layoutProcessorClassName) {
                if ($this->isLayoutProcessorCanBeCreated($layoutProcessorClassName)) {
                    $this->metadataInstances[$layoutProcessorClassName] =
                        $this->objectManager->create($layoutProcessorClassName);
                }
            }
        }
        return $this->metadataInstances;
    }

    /**
     * Check is layout processor can be created
     *
     * @param string $layoutProcessorClassName
     * @return bool
     */
    private function isLayoutProcessorCanBeCreated($layoutProcessorClassName)
    {
        $result = false;
        if (class_exists($layoutProcessorClassName)) {
            if (is_subclass_of($layoutProcessorClassName, LayoutProcessorInterface::class)) {
                $result = true;
            }
        }
        return $result;
    }
}
