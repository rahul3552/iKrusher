<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentMapping
 */

namespace I95DevConnect\PaymentMapping\Api\Data;

/**
 * Represents Data Object for a Payment Mapping
 */
interface PaymentMappingDataInterface
{
    
    const ID = 'id';
    const MAPPED_DATA = 'mapped_data';
    const CREATED_AT = 'created_at';

    /**
     * get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $Id
     * @return $this
     */
    public function setId($Id);

    /**
     * get mapping data
     *
     * @return string|null
     */
    public function getMappedData();

    /**
     * Set Payment Mapping Data
     *
     * @param string $mappedData
     * @return $this
     */
    public function setMappedData($mappedData);

    /**
     * get created date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * set created date
     *
     * @param string $createdDate
     * @return $this
     */
    public function setCreatedAt($createdDate);
}
