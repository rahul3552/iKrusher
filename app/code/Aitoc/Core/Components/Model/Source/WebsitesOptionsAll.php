<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_Core
 */


namespace Aitoc\Core\Components\Model\Source;

/**
 * includes "All Websites" option
 */
class WebsitesOptionsAll extends WebsitesOptions
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = $this->store->getWebsiteValuesForForm(false, true);
        foreach ($options as &$option) {
            if ($option['value'] === 0) {
                $option['label'] = __('All Websites');
                break;
            }
        }
        return $options;
    }
}
