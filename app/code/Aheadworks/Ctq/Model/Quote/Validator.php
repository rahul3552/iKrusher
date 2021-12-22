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
namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Validator
 * @package Aheadworks\Ctq\Model\Quote
 */
class Validator extends AbstractValidator implements ValidatorInterface
{
    /**
     * @var string
     */
    private $event;

    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param string $event
     * @param bool|null $isSeller
     * @param AbstractValidator[] $validators
     */
    public function __construct(
        $event = 'save',
        $isSeller = null,
        array $validators = []
    ) {
        $prefix = $isSeller === null ? '' : ($isSeller === true ? 'seller' : 'buyer');
        $this->event = SimpleDataObjectConverter::snakeCaseToCamelCase($prefix . '_' . $event);
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($quote)
    {
        if (isset($this->validators[$this->event])) {
            /** @var AbstractValidator[] $eventValidators */
            $eventValidators = $this->validators[$this->event];
            foreach ($eventValidators as $validator) {
                if (!$validator->isValid($quote)) {
                    $this->_addMessages($validator->getMessages());
                }
            }
        }
        return empty($this->getMessages());
    }
}
