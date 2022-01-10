/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/template',
    'text!Mageplaza_ProductAttachments/template/group/popup/attachments-detail.html',
    "mage/translate",
    'jquery/ui'
], function ($, modal, mageTemplate, galleryPopupTemplate, $t) {
    "use strict";

    return {
        sortFileGallery: function (galleryContainer) {
            /** set hidden file */
            galleryContainer.find('.image').each(function () {
                if ($(this).find('input.file-status').val() === '0') {
                    $(this).addClass('hidden-for-front');
                }
            });

            /** draggable */
            galleryContainer.sortable({
                start: function (e, ui) {
                    var el = this;

                    // creates a temporary attribute on the element with the old index
                    $(el).attr('data-previndex', ui.item.index() + 1);
                },
                update: function (e, ui) {
                    var el          = this;
                    // gets the new and old index then removes the temporary attribute
                    var positionNew = ui.item.index() + 1;
                    var positionOld = parseInt($(el).attr('data-previndex'), 10);

                    $(el).find('.image').each(function () {
                        if (positionNew > positionOld) {
                            if (parseInt($(this).find('input.position').val(), 10) <= positionNew) {
                                $(this).find('input.position')
                                .val(parseInt($(this).find('input.position').val(), 10) - 1);
                            }
                            if (parseInt($(this).find('input.position').val(), 10) < positionOld) {
                                $(this).find('input.position')
                                .val(parseInt($(this).find('input.position').val(), 10) + 1);
                            }
                        } else {
                            if (parseInt($(this).find('input.position').val(), 10) >= positionNew) {
                                $(this).find('input.position')
                                .val(parseInt($(this).find('input.position').val(), 10) + 1);
                            }
                            if (parseInt($(this).find('input.position').val(), 10) > positionOld) {
                                $(this).find('input.position')
                                .val(parseInt($(this).find('input.position').val(), 10) - 1);
                            }
                        }
                        $(this).find('input.is-updated').val(1);
                    });
                    $(ui.item).find('input.position').val(positionNew);
                    $(this).removeAttr('data-previndex');
                }
            });
        },

        /** remove file function */
        removeFile: function (removeSelector) {
            removeSelector.on('click', function (e) {
                var fileContainer = $(this).parent().parent().parent();

                e.preventDefault();
                e.stopPropagation();
                fileContainer.find('input.is-removed').val(1);
                fileContainer.hide();
            });
        },

        /** init file detail popup function */
        initFileDetailPopup: function (selector, config) {
            selector.on('click', function () {
                var modalHtml       = mageTemplate(
                    galleryPopupTemplate,
                    {
                        data: {
                            fileId: $(this).find('input.file-id').val(),
                            fileSize: $(this).find('.item-size span').text(),
                            fileLabel: $(this).find('input.file-label').val(),
                            fileType: $(this).find('input.file-type').val(),
                            iconUrl: $(this).find('input.file-icon-path').val() !== ''
                                ? config.iconUrl.fileIcon + '/' + $(this).find('input.file-icon-path').val()
                                : config.iconUrl.defaultIcon

                        },
                        options: config.options,
                        label: {
                            fileLabel: $t('File Label'),
                            fileName: $t('File Name'),
                            fileStatus: $t('Status'),
                            fileAction: $t('Customer Action'),
                            fileStoreView: $t('Store Views'),
                            fileCustomerGroup: $t('Show file to customer group(s)'),
                            fileCustomerGroupNote: $t('Select customer group(s) to show attachments to.'),
                            fileIcon: $t('Icon'),
                            fileGroup: $t('Select Group'),
                            filePriority: $t('Priority'),
                            fileCustomerLogin: $t('Customer must log in to download/view file'),
                            fileIsBuyer: $t('Only purchased customers can view/download'),
                            fileOrderStatuses: $t('Order Status'),
                            fileOrderStatusesNote: $t('When the customer place orders and the order status is satisfied, the attached file can be viewed / downloaded'),
                            fileSize: $t('File Size')
                        }
                    }
                    ),
                    attachmentPopup  = $('<div/>').html(modalHtml),
                    productContent   = $("#mp_product_attachments_content"),
                    fileLabelElement,
                    fileNameElement,
                    fileStatusElement,
                    fileActionElement,
                    fileStoreElement,
                    fileCustomerElement,
                    fileGroupElement,
                    fileIconElement,
                    filePriorityElement,
                    fileIsBuyerElement,
                    fileOrderStatusesElement,
                    fileCustomerLoginElement,
                    statusValue,
                    actionValue,
                    storeValue,
                    storeArr,
                    customerValue,
                    customerArr,
                    groupValue,
                    OrderStatusesValue,
                    OrderStatusesArr,
                    iconPathValue, i, y;

                attachmentPopup.modal({
                    type: 'slide',
                    title: $t('File details'),
                    innerScroll: true,
                    responsive: true,
                    buttons: []
                });
                attachmentPopup.trigger('openModal');

                /** mp_product_attachment custom js */
                fileLabelElement         = $("input[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][label]']");
                fileNameElement          = $("input[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][name]']");
                fileStatusElement        = $("select[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][status]']");
                fileActionElement        = $("select[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][file_action]']");
                fileStoreElement         = $("select[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][store_ids]']");
                fileCustomerElement      = $("select[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][customer_group]']");
                fileGroupElement         = $("select[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][group]']");
                fileIconElement          = $("select[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][file_icon_path]']");
                filePriorityElement      = $("input[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][priority]']");
                fileIsBuyerElement       = $("input[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][is_buyer]']");
                fileOrderStatusesElement = $("select[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][order_statuses]']");
                fileCustomerLoginElement = $("input[name='product[mpattachments][images]["
                    + $(this).find('input.file-id').val() + "][customer_login]']");

                /** Field file Label */
                fileLabelElement.on('change', function () {
                    var attrName       = $(this).attr('name'),
                        attrIsUpdate   = attrName.replace('label', 'is_updated'),
                        inputLabel     = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate  = productContent.find("input[name='" + attrIsUpdate + "']");

                    inputLabel.parent().find(".item-title").text($(this).val());
                    inputLabel.val($(this).val());
                    inputIsUpdate.val(1);
                });
                fileLabelElement.val(
                    productContent.find("input[name='" + fileLabelElement.attr('name') + "']").val()
                );

                /** Field file Name */
                fileNameElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('name', 'is_updated'),
                        inputName     = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    inputName.val($(this).val());
                    inputIsUpdate.val(1);
                });
                fileNameElement.val(productContent
                .find("input[name='" + fileNameElement.attr('name') + "']").val());

                /** Field file Status */
                fileStatusElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('status', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    if (!this.val()) {
                        inputLabel.parent().addClass('hidden-for-front');
                    } else {
                        inputLabel.parent().removeClass('hidden-for-front');
                    }
                    inputLabel.val($(this).val());
                    inputIsUpdate.val(1);
                });
                statusValue = productContent.find("input[name='" + fileStatusElement.attr('name') + "']").val();

                $(fileStatusElement).find('option[value="' + statusValue + '"]').attr("selected", true);

                /** Field file File Action */
                fileActionElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('file_action', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    inputLabel.parent().find(".item-role").text($(this).find(":selected").text());
                    inputLabel.val($(this).val());
                    inputIsUpdate.val(1);
                });
                actionValue = productContent.find("input[name='"
                    + fileActionElement.attr('name') + "']").val();

                $(fileActionElement).find('option[value="' + actionValue + '"]').attr("selected", true);

                /** Field file Store Ids */
                fileStoreElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('store_ids', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    inputLabel.val($(this).val().toString());
                    inputIsUpdate.val(1);

                });
                storeValue = productContent.find("input[name='" + fileStoreElement.attr('name') + "']").val();
                storeArr   = storeValue.split(',');

                for (i = 0; i < storeArr.length; i++){
                    fileStoreElement.find('option[value="' + storeArr[i] + '"]').attr("selected", true);
                }

                /** Field file Customer Group */
                fileCustomerElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('customer_group', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    inputLabel.val($(this).val().toString());
                    inputIsUpdate.val(1);

                });
                customerValue = productContent.find("input[name='" + fileCustomerElement.attr('name') + "']").val();
                customerArr   = customerValue.split(',');

                for (y = 0; y < customerArr.length; y++){
                    fileCustomerElement.find('option[value="' + customerArr[y] + '"]').attr("selected", true);
                }

                /** Field file Order Status */
                fileOrderStatusesElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('order_statuses', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    inputLabel.val($(this).val().toString());
                    inputIsUpdate.val(1);

                });
                OrderStatusesValue = productContent.find("input[name='"
                    + fileOrderStatusesElement.attr('name') + "']").val();
                OrderStatusesArr   = OrderStatusesValue.split(',');

                for (y = 0; y < OrderStatusesArr.length; y++){
                    fileOrderStatusesElement.find('option[value="' + OrderStatusesArr[y] + '"]').attr("selected", true);
                }

                /** Field file Group */
                fileGroupElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('group', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    inputLabel.parent().find(".item-role").text($(this).find(":selected").text());
                    inputLabel.val($(this).val());
                    inputIsUpdate.val(1);
                });
                groupValue = productContent.find("input[name='"
                    + fileGroupElement.attr('name') + "']").val();

                $(fileGroupElement).find('option[value="' + groupValue + '"]').attr("selected", true);

                /** Field file File Icon Path */
                fileIconElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('file_icon_path', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    if ($(this).val() === 'mp_attachment_default_icon') {
                        inputLabel.val(null);
                        inputLabel.parent().find('.product-image-wrapper img').attr("src", config.iconUrl.defaultIcon);
                        $('#mp_attachments-details .image-panel-preview img').attr("src", config.iconUrl.defaultIcon);
                    } else {
                        inputLabel.parent().find('.product-image-wrapper img')
                        .attr("src", config.iconUrl.fileIcon + '/' + $(this).val());
                        $('#mp_attachments-details .image-panel-preview img')
                        .attr("src", config.iconUrl.fileIcon + '/' + $(this).val());
                        inputLabel.val($(this).val());
                    }
                    inputIsUpdate.val(1);
                });
                iconPathValue = productContent.find("input[name='" + fileIconElement.attr('name') + "']").val();

                if (!iconPathValue) {
                    $(fileIconElement).find('option[value="mp_attachment_default_icon"]').attr("selected", true);
                } else {
                    $(fileIconElement).find('option[value="' + iconPathValue + '"]').attr("selected", true);
                }

                /** Field file File Priority */
                filePriorityElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('priority', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    if (isNaN($(this).val())) {
                        $(this).val(0);
                    } else {
                        $(this).val(parseInt($(this).val(), 10));
                    }
                    inputLabel.val($(this).val());
                    inputIsUpdate.val(1);
                });
                filePriorityElement.val(
                    productContent
                    .find("input[name='" + filePriorityElement.attr('name') + "']").val()
                );

                /** Field file Is Buyer */
                fileIsBuyerElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('is_buyer', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    if ($(this).is(':checked')) {
                        inputLabel.val(1);
                        $(".field-order-statuses").show();
                    } else {
                        inputLabel.val(0);
                        $(".field-order-statuses").hide();
                    }
                    inputIsUpdate.val(1);
                });
                fileIsBuyerElement.prop(
                    "checked",
                    productContent
                    .find("input[name='" + fileIsBuyerElement.attr('name') + "']").val() === '1'
                );
                if (fileIsBuyerElement.is(':checked')) {
                    $(".field-order-statuses").show();
                } else {
                    $(".field-order-statuses").hide();
                }

                /** Field file Customer Login */
                fileCustomerLoginElement.on('change', function () {
                    var attrName      = $(this).attr('name'),
                        attrIsUpdate  = attrName.replace('customer_login', 'is_updated'),
                        inputLabel    = productContent.find("input[name='" + attrName + "']"),
                        inputIsUpdate = productContent.find("input[name='" + attrIsUpdate + "']");

                    if ($(this).is(':checked')) {
                        inputLabel.val(1);
                    } else {
                        inputLabel.val(0);
                    }
                    inputIsUpdate.val(1);
                });
                fileCustomerLoginElement.prop(
                    "checked",
                    productContent
                    .find("input[name='" + fileCustomerLoginElement.attr('name') + "']").val() === '1'
                );
            });
        },

        /** disable show on box when click use config */
        showOn: function (useConfig, showOnBox, defaultShow) {
            var showOnElements = $('.mp_selection');

            function updateDefault(showOnBox, defaultShow) {
                showOnBox.find('option').each(function () {
                    var value = $(this).attr('value');

                    if (defaultShow.indexOf(value)>=0) {
                        $(this).prop('selected', true);
                        $('[name="product[mpattachments][attachment_location]['+value+']"]').attr('value','1');
                    }
                });
            }

            if (useConfig.is(":checked")) {
                showOnBox.prop("disabled", true);
                useConfig.val(1);
                updateDefault(showOnBox, defaultShow);
            } else {
                showOnBox.prop("disabled", false);
                useConfig.val(0);
            }
            useConfig.change(function () {
                if ($(this).is(":checked")) {
                    showOnBox.prop("disabled", true);
                    useConfig.val(1);
                    updateDefault(showOnBox, defaultShow);
                } else {
                    showOnBox.prop("disabled", false);
                    useConfig.val(0);
                }
            });
            showOnElements.each(function () {
                if ($(showOnBox).val() != null) {
                    if ($(showOnBox).val().indexOf($(this).attr('data-value')) >= 0) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                }
            });
            showOnBox.change(function () {
                var el = this;

                showOnElements.each(function () {
                    if ($(el).val().indexOf($(this).attr('data-value')) >= 0) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                });
            });
        }
    };
});
