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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Quote\Discount\Calculator;

/**
 * Class Pool
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount\Calculator
 */
class Pool
{
    /**
     * @var array
     */
    private $calculators;

    /**
     * @param array $calculators
     */
    public function __construct(
        array $calculators = []
    ) {
        $this->calculators = $calculators;
    }

    /**
     * Retrieve calculator by type
     *
     * @param string $type
     * @return DiscountCalculatorInterface
     * @throws \InvalidArgumentException
     */
    public function getCalculatorByType($type)
    {
        if (!isset($this->calculators[$type])) {
            throw new \InvalidArgumentException($type . ' is unknown type');
        }

        return $this->calculators[$type];
    }
}
