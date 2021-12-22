/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'authnetcim',
                component: 'ParadoxLabs_Authnetcim/js/view/payment/method-renderer/authnetcim'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
