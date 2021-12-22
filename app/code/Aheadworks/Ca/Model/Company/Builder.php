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
namespace Aheadworks\Ca\Model\Company;

/**
 * Class Builder
 * @package Aheadworks\Ca\Model\Company
 */
class Builder implements BuilderInterface
{
    /**
     * @var array
     */
    private $builders;

    /**
     * @param array $builders
     */
    public function __construct(
        $builders = []
    ) {
        $this->builders = $builders;
    }

    /**
     * {@inheritdoc}
     */
    public function create($company, $customer)
    {
        foreach ($this->builders as $builder) {
            $builder->create($company, $customer);
        }
    }
}
