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

/**
 * Class EntityProcessor
 * @package Aheadworks\Ca\Model\Company\Address
 */
class EntityProcessor
{
    /**
     * @var array[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(
        $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Prepare entity data
     *
     * @param $data
     * @return array
     */
    public function process($data)
    {
        foreach ($this->processors as $processor) {
            $data = $processor->process($data);
        }
        return $data;
    }
}
