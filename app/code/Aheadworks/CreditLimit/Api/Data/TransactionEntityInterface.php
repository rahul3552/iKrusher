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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TransactionEntityInterface
 * @api
 */
interface TransactionEntityInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const TRANSACTION_ID = 'transaction_id';
    const ENTITY_TYPE = 'entity_type';
    const ENTITY_ID = 'entity_id';
    const ENTITY_LABEL = 'entity_label';
    /**#@-*/

    /**
     * Set transaction ID
     *
     * @param  int $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId);

    /**
     * Get transaction ID
     *
     * @return int
     */
    public function getTransactionId();

    /**
     * Set entity type
     *
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType);

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType();

    /**
     * Set entity ID
     *
     * @param  int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Get entity ID
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity label
     *
     * @param string|null $entityLabel
     * @return $this
     */
    public function setEntityLabel($entityLabel);

    /**
     * Get entity label
     *
     * @return string|null
     */
    public function getEntityLabel();

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionEntityExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set extension attributes object
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionEntityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\TransactionEntityExtensionInterface $extensionAttributes
    );
}
