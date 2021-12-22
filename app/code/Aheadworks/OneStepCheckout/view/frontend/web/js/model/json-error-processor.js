define([
    'Magento_Ui/js/model/messageList'
], function (globalMessageList) {
    'use strict';

    return {

        /**
         * Process error message
         *
         * @param {Object} response
         * @param {Object} messageContainer
         */
        process: function (response, messageContainer) {
            messageContainer = messageContainer || globalMessageList;
            messageContainer.addErrorMessage({message: response.errorMessage});
        }
    };
});
