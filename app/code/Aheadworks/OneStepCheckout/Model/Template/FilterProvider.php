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
namespace Aheadworks\OneStepCheckout\Model\Template;

use Magento\Cms\Model\Template\Filter;
use Magento\Framework\Filter\Template as TemplateFilter;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class FilterProvider
 * @package Aheadworks\OneStepCheckout\Model\Template
 */
class FilterProvider
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $filterClassName;

    /**
     * @var TemplateFilter|null
     */
    private $filterInstance = null;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $filterClassName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $filterClassName = Filter::class
    ) {
        $this->objectManager = $objectManager;
        $this->filterClassName = $filterClassName;
    }

    /**
     * Get filter instance
     *
     * @return TemplateFilter|mixed|null
     * @throws \Exception
     */
    public function getFilter()
    {
        if ($this->filterInstance === null) {
            $filterInstance = $this->objectManager->get($this->filterClassName);
            if (!$filterInstance instanceof TemplateFilter) {
                throw new LocalizedException(
                    'Template filter ' . $this->filterClassName . ' does not implement required interface.'
                );
            }
            $this->filterInstance = $filterInstance;
        }
        return $this->filterInstance;
    }
}
