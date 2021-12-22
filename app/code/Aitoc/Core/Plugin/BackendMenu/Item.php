<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_Core
 */


namespace Aitoc\Core\Plugin\BackendMenu;

use Magento\Backend\Model\Menu\Item as NativeItem;

class Item
{
    /**
     * @param NativeItem $subject
     * @param $url
     * @return string
     */
    public function afterGetUrl(NativeItem $subject, $url)
    {
        $id = $subject->getId();
        if ($id == 'Aitoc_Core::marketplace') {
            return 'https://www.aitoc.com/magento-2-extensions.html?utm_source=extensions_promo&utm_medium=backend&utm_campaign=from_magento_2_menu';
        } else {
            return $url;
        }
    }
}
