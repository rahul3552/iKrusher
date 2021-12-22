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
namespace Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;

interface ProcessorInterface
{
    /**
     * Process data after load
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function afterLoad($object);

    /**
     * Process data before save
     *
     * @param CompanyUserInterface $object
     * @return CompanyUserInterface
     */
    public function beforeSave($object);
}
