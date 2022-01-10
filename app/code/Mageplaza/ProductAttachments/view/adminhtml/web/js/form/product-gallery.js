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
    'Magento_Ui/js/modal/alert',
    "mage/translate",
    'mp_fileGallery',
    'jquery/ui',
    "jquery/validate",
    'jquery/file-uploader'
], function ($, modal, mageTemplate, galleryPopupTemplate, alert, $t, mp_FileGallery) {
    'use strict';
    return function (config) {
        /** ajax upload file function */
        function uploadProcessor (formdata, files, counter, limit) {
            var currentDate    = new Date(),
                imgLoader      = $('.mp_image_loader'),
                imgWrapper     = $('#mp-product-image-wrapper'),
                imgWrapperText = $('#mp-product-image-wrapper p'),
                file,
                imagePlaceholder = $('#mp-image-placeholder');

            if (counter < limit) {
                file = files[counter];

                imgLoader.show();
                imgWrapper.addClass('no-before');
                imgWrapperText.hide();
                if (formdata) {
                    formdata.delete('image');
                    formdata.delete('position');
                    formdata.delete('value_id');
                    formdata.append('image', file);
                    formdata.append('position', imagePlaceholder.prev().find('input.position').length>0?
                        imagePlaceholder.prev().find('input.position').val():1);
                    formdata.append('value_id', currentDate.getTime());
                    formdata.append('type', '0');
                    $.ajax({
                        type: "POST",
                        url: config.ajaxUrl,
                        data: formdata,
                        processData: false,
                        contentType: false,
                        success:
                            function (response) {
                                if (response.status) {
                                    $('.mp_image_loader').hide();
                                    $('#mp-product-image-wrapper').removeClass('no-before');
                                    $('#mp-product-image-wrapper p').show();
                                    $('#mp-image-placeholder').before($(response.faq_list).html());
                                    mp_FileGallery.initFileDetailPopup(
                                        $('[data-role="mp_file_new_' + currentDate.getTime() + '"]'),
                                        config
                                    );
                                    mp_FileGallery.removeFile($('.action-remove'));
                                } else {
                                    $('.mp_image_loader').hide();
                                    $('#mp-product-image-wrapper').removeClass('no-before');
                                    $('#mp-product-image-wrapper p').show();
                                    alert({
                                        title: $t('Attention'),
                                        content: response.errorSize
                                    });
                                }
                                counter++;
                                uploadProcessor(formdata, files, counter, limit);
                            }
                    })
                    ;
                }
            }
        }

        function ajaxFileUpload (uploadSelector) {
            /** file upload function */
            var formdata = new FormData();

            uploadSelector.change(function () {
                var files = this.files,
                    len   = files.length;

                uploadProcessor(formdata, files, 0, len);
            }).click(function () {
                $(this).val('');
            });
        }

        function getInputHtml (type) {
            switch (type){
                case 0:
                    return '<label class="admin__field-label" for="file_link"><span>File Link</span></label>' +
                        '<div class="admin__field-control mp_attachments_location_container__selection">' +
                        '<input type="url" name="product[mpattachments][file_path]"' +
                        ' class="input-text admin__control-text required-entry _required"'
                        + ' style="width: 89%;min-width: 15rem"></div>';
                case 1:
                    return '<label class="admin__field-label" for="file_label"><span>File Label</span></label>' +
                        '<div class="admin__field-control mp_attachments_location_container__selection">' +
                        '<input type="text" name="product[mpattachments][file_label]"' +
                        ' class="input-text admin__control-text required-entry _required"' +
                        ' id="mp_attachments_input_label" style="width: 89%;min-width: 15rem"></div>';
                case 2:
                    return '<label class="admin__field-label" for="file_link"><span>File Name</span></label>' +
                        '<div class="admin__field-control mp_attachments_location_container__selection">' +
                        '<input type="text" name="product[mpattachments][file_name]"' +
                        ' class="input-text admin__control-text required-entry _required"' +
                        ' id="mp_attachments_input_name" style="width: 89%;min-width: 15rem"></div>';
                case 3:
                    return '<div class="admin__field-control mp_attachments_location_container__selection">' +
                        '<button id="mp_attachment_link_add" class="mp-attachment-details-save" type="button">'
                        + $t('Add') + '</div>';
            }
        }

        $('#mp_attachments_file_type').on('change',function () {
            if ($(this).val() === '1') {
                $('#mp_attachments_link').html(getInputHtml(0));
                $('#mp_attachments_label').html(getInputHtml(1));
                $('#mp_attachments_name').html(getInputHtml(2));
                $('#mp_attachments_link_button').html(getInputHtml(3));
                $('#mp-image-placeholder').hide();
            }else {
                $('#mp_attachments_link').empty();
                $('#mp_attachments_label').empty();
                $('#mp_attachments_name').empty();
                $('#mp_attachments_link_button').empty();
                $('#mp-image-placeholder').show();
            }
        });

        $('body').on('click', '#mp_attachment_link_add', function () {
            var formEL      = $('#mp_productgrid_attachments'),
                formData    = new FormData(),
                currentDate = new Date();

            if (formEL.valid()) {
                $('.ln_overlay').show();
                formData.append('value_id', currentDate.getTime());
                formData.append('file_path', formEL.find('#mp_attachments_link>div>input').val());
                formData.append('label', formEL.find('#mp_attachments_input_label').val());
                formData.append('name', formEL.find('#mp_attachments_input_name').val());
                formData.append('type', '1');
                formData.append('position', '0');

                $.ajax({
                    type: "POST",
                    url: config.ajaxUrl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success:
                        function (response) {
                            if (response.status) {
                                $('#mp-image-placeholder').before($(response.faq_list).html());
                                mp_FileGallery.initFileDetailPopup(
                                    $('[data-role="mp_file_new_' + currentDate.getTime() + '"]'),
                                    config
                                );
                                mp_FileGallery.removeFile($('.action-remove'));
                                formEL.trigger('reset');
                            } else {
                                alert({
                                    title: $t('Attention'),
                                    content: $t('Something error.')
                                });
                            }
                        },
                    complete: function () {
                        $('.ln_overlay').hide();
                    }
                });
            }
        });

        mp_FileGallery.showOn($('#use_config_mp_attachments_location'),
            $('#mp_attachments_location'), config.defaultShow);
        mp_FileGallery.sortFileGallery($(".mp-ui-sortable"));
        ajaxFileUpload($('#fileupload'));
        mp_FileGallery.initFileDetailPopup($('[data-role="mp_file"]'), config);
        mp_FileGallery.removeFile($('.action-remove'));
    };
});
