<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_ConfigurableProducts
 */

namespace I95DevConnect\ConfigurableProducts\Model\DataPersistence;

/**
 * Configurable product class for sync
 */
class ConfigurableProducts
{
    /**
     *
     * @var \I95DevConnect\ConfigurableProducts\Model\DataPersistence\ConfigurableProducts\CreateFactory
     */
    public $configurableProductCreate;

    /**
     * constructor for class used for sync of Configurable Products at persistent level
     * @param ConfigurableProducts\CreateFactory $create
     */
    public function __construct(
        \I95DevConnect\ConfigurableProducts\Model\DataPersistence\ConfigurableProducts\CreateFactory $create
    ) {
        $this->configurableProductCreate = $create;
    }

    /**
     * Defining configurable product sync class
     *
     * @param array $stringData
     * @return obj
     */
    public function create($stringData)
    {
        return $this->configurableProductCreate->create()->createConfigurableProduct($stringData);
    }
}
