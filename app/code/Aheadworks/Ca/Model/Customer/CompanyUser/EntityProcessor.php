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
namespace Aheadworks\Ca\Model\Customer\CompanyUser;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor\ProcessorInterface;

/**
 * Class EntityProcessor
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser
 */
class EntityProcessor
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare entity before save
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function prepareDataBeforeSave($object)
    {
        foreach ($this->processors as $processor) {
            $processor->beforeSave($object);
        }
        return $object;
    }

    /**
     * Prepare entity after load
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function prepareDataAfterLoad($object)
    {
        foreach ($this->processors as $processor) {
            $processor->afterLoad($object);
        }
        return $object;
    }
}
