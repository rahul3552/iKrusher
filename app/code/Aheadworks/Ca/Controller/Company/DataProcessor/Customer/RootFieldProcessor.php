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
namespace Aheadworks\Ca\Controller\Company\DataProcessor\Customer;

use Aheadworks\Ca\Controller\Company\DataProcessor\DataProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class RootFieldProcessor
 *
 * @package Aheadworks\Ca\Controller\Company\DataProcessor\Customer
 */
class RootFieldProcessor implements DataProcessorInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param BooleanUtils $booleanUtils
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        BooleanUtils $booleanUtils,
        ArrayManager $arrayManager
    ) {
        $this->booleanUtils = $booleanUtils;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Prepare post data for saving
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        $path = $this->arrayManager->findPath('is_root', $data);

        if ($path) {
            $is_root = $this->arrayManager->get($path, $data);
            $is_root = $this->booleanUtils->toBoolean($is_root);
            $data = $this->arrayManager->set($path, $data, $is_root);
        }

        return $data;
    }
}
