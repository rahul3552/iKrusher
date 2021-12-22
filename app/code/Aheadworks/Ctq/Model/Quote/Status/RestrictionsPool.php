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
namespace Aheadworks\Ctq\Model\Quote\Status;

/**
 * Class RestrictionsPool
 * @package Aheadworks\Ctq\Model\Quote\Status
 */
class RestrictionsPool
{
    /**
     * @var RestrictionsInterfaceFactory
     */
    private $restrictionsFactory;

    /**
     * @var array
     */
    private $restrictions = [];

    /**
     * @var RestrictionsInterface[]
     */
    private $restrictionsInstance = [];

    /**
     * @param RestrictionsInterfaceFactory $restrictionsFactory
     * @param array $restrictions
     */
    public function __construct(
        RestrictionsInterfaceFactory $restrictionsFactory,
        $restrictions = []
    ) {
        $this->restrictionsFactory = $restrictionsFactory;
        $this->restrictions = $restrictions;
    }

    /**
     * Retrieve restrictions by status
     *
     * @param int $status
     * @return RestrictionsInterface
     * @throws \Exception
     */
    public function getRestrictions($status)
    {
        if (!isset($this->restrictionsInstance[$status])) {
            if (!isset($this->restrictions[$status])) {
                throw new \Exception(sprintf('Unknown status: %s requested', $status));
            }
            $instance = $this->restrictionsFactory->create(['data' => $this->restrictions[$status]]);
            $this->restrictionsInstance[$status] = $instance;
        }
        return $this->restrictionsInstance[$status];
    }
}
