<?php
namespace Vueai\ProductRecommendations\Block;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class DatePicker extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {
        //get configuration element
        $html = $element->getElementHtml();
        // add timepicker with element by jquery
        $html .= '<script type="text/javascript">
               require([
                    "jquery",
                    "mage/calendar"
               ], function($){
                    $("#oauth_details_catalog_update_frequency_timepicker").timepicker({
                        timeFormat:"HH:mm"
                    });
                });
                </script>';
        return $html;
    }
}
