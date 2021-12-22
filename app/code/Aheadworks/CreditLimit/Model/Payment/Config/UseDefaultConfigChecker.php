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
namespace Aheadworks\CreditLimit\Model\Payment\Config;

use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class UseDefaultConfigChecker
 *
 * @package Aheadworks\CreditLimit\Model\Payment\Config
 */
class UseDefaultConfigChecker
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * Check if used default is checked for config field
     *
     * @param array $config
     * @param string $section
     * @param string $path
     * @return bool
     */
    public function isUsedByDefault($config, $section, $path)
    {
        $result = false;
        if (isset($config['section']) && $config['section'] == $section) {
            $fieldConfig = $this->arrayManager->get($path, $config);
            $result = $fieldConfig['inherit'] ?? false;
        }

        return (bool)$result;
    }
}
