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
namespace Aheadworks\OneStepCheckout\Api\Data;

/**
 * Interface DataFieldCompletenessInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface DataFieldCompletenessInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const FIELD_NAME = 'field_name';
    const IS_COMPLETED = 'is_completed';
    const SCOPE = 'scope';
    /**#@-*/

    /**
     * Get field name
     *
     * @return string
     */
    public function getFieldName();

    /**
     * Set field name
     *
     * @param string $fieldName
     * @return $this
     */
    public function setFieldName($fieldName);

    /**
     * Get is completed flag
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsCompleted();

    /**
     * Set is completed flag
     *
     * @param bool $isCompleted
     * @return $this
     */
    public function setIsCompleted($isCompleted);

    /**
     * Get scope
     *
     * @return string|null
     */
    public function getScope();

    /**
     * Set scope
     *
     * @param string $scope
     * @return $this
     */
    public function setScope($scope);
}
