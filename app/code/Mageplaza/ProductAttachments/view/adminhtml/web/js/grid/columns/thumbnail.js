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
    'Magento_Ui/js/grid/columns/thumbnail',
    'jquery',
    'mage/template',
    'text!Mageplaza_ProductAttachments/template/grid/cells/thumbnail/preview.html',
    'Magento_Ui/js/modal/alert',
    'mp_fileGallery',
    "uiRegistry",
    "mage/translate",
    "jquery/validate",
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, thumbnailPreviewTemplate, alert, mp_FileGallery, registry, $t) {
    'use strict';

    return Column.extend({
        modal: {},
        preview: function (row) {
            var popupCurrentDate = new Date(),
                productId        = row.entity_id,
                modalHtml,
                previewPopup,
                galleryContainer,
                formData,
                defaultValue;

            function uploadProcessor (uploadSelector, formdata, files, counter, limit) {
                var currentDate    = new Date(),
                    imgLoader      = uploadSelector.find('.mp_image_loader'),
                    imgWrapper     = uploadSelector.find('#mp-product-image-wrapper'),
                    imgWrapperText = uploadSelector.find('#mp-product-image-wrapper p'),
                    file;

                if (counter < limit) {
                    file = files[counter];

                    imgLoader.show();
                    imgWrapper.addClass('no-before');
                    imgWrapperText.hide();
                    if (formdata) {
                        formdata.delete('image');
                        formdata.delete('position');
                        formdata.delete('value_id');
                        formdata.delete('is_grid');
                        formdata.append('image', file);
                        formdata.append('position', uploadSelector.prev().find('input.position').val());
                        formdata.append('value_id', currentDate.getTime() + counter.toString());
                        formdata.append('is_grid', '1');
                        formdata.append('type', '0');
                        $.ajax({
                            type: "POST",
                            url: row.mp_attachment_ajax_url,
                            data: formdata,
                            processData: false,
                            contentType: false,
                            success:
                                function (response) {
                                    if (response.status) {
                                        imgLoader.hide();
                                        imgWrapper.removeClass('no-before');
                                        imgWrapperText.show();
                                        uploadSelector.before($(response.faq_list).html());
                                        mp_FileGallery.removeFile($('.action-remove'));
                                    } else {
                                        imgLoader.hide();
                                        imgWrapper.removeClass('no-before');
                                        imgWrapperText.show();
                                        alert({
                                            title: $t('Attention'),
                                            content: response.errorSize
                                        });
                                    }
                                    counter++;
                                    uploadProcessor(uploadSelector, formdata, files, counter, limit);
                                }
                        })
                        ;
                    }
                }
            }

            /** ajax upload file function */
            function ajaxFileUpload (uploadSelector) {
                /** file upload function */
                var formdata = new FormData();

                uploadSelector.find('input').on('input', function () {
                    var files = this.files,
                        len   = files.length;

                    uploadProcessor(uploadSelector, formdata, files, 0, len);
                }).click(function () {
                    $(this).val('');
                });
            }

            if (typeof this.modal[productId] === 'undefined') {
                formData    = new FormData();
                $.ajax({
                    type: "POST",
                    url: row.mp_attachment_ajax_get_config,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success:
                        function (response) {
                            defaultValue = response.config;
                            modalHtml    = mageTemplate(
                                thumbnailPreviewTemplate,
                                {
                                    popupId: popupCurrentDate.getTime(),
                                    fileData: JSON.parse(row.mp_attachment_file_data),
                                    fileSystem: JSON.parse(row.mp_attachment_file_system),
                                    loaderUrl: row.mp_attachment_loading_url,
                                    label: {
                                        showOn: $t('Show Block On'),
                                        useConfig: $t('Use Config Settings'),
                                        save: $t('Save'),
                                        fileDelete: $t('Delete file'),
                                        hidden: $t('Hidden'),
                                        browseFile: $t('Browse Files...'),
                                        browseFileText: $t('Browse to find or drag file here')
                                    },
                                    location: row.mp_attachment_location,
                                    defaultValue: defaultValue
                                }
                            );
                            previewPopup = $('<div/>').html(modalHtml);
                            previewPopup
                                .modal({
                                    type: 'slide',
                                    title: $t('Add Attachment for "' + row.name + '"'),
                                    innerScroll: true,
                                    modalClass: '_attachment-file-box',
                                    buttons: []
                                });
                            previewPopup.trigger('openModal');
                            galleryContainer = $(".mp-ui-sortable");
                            mp_FileGallery.sortFileGallery(galleryContainer);
                            mp_FileGallery.showOn(
                                $('#use_config_mp_attachments_location-' + popupCurrentDate.getTime()),
                                $('#mp_attachments_location-' + popupCurrentDate.getTime()),
                                defaultValue
                            );
                            ajaxFileUpload($("#mp-image-placeholder-" + popupCurrentDate.getTime()));
                            mp_FileGallery.removeFile($('.action-remove'));

                            /** reload grid content */
                            function updateGrid () {
                                var grid   = 'product_listing.product_listing_data_source',
                                    target = registry.get(grid);

                                if (target && typeof target === 'object') {
                                    target.set('params.t ', Date.now());
                                }
                            }
                            function getInputHtml (type) {
                                switch (type){
                                    case 0:
                                        return '<label class="admin__field-label" for="file_link">' +
                                            '<span>File Link</span></label>' +
                                            '<div ' +
                                            'class="admin__field-control mp_attachments_location_container__selection">'
                                            + '<input type="url" name="product[mpattachments][file_path]"' +
                                            ' class="input-text admin__control-text"'
                                            + ' style="width: 89%;min-width: 15rem"></div>';
                                    case 1:
                                        return '<label class="admin__field-label" for="file_label">' +
                                            '<span>File Label</span></label>' +
                                            '<div ' +
                                            'class="admin__field-control mp_attachments_location_container__selection">'
                                            + '<input type="text" name="product[mpattachments][file_label]"' +
                                            ' class="input-text admin__control-text required-entry _required"' +
                                            ' id="mp_attachments_input_label" style="width: 89%;min-width: 15rem">' +
                                            '</div>';
                                    case 2:
                                        return '<label class="admin__field-label" for="file_link">' +
                                            '<span>File Name</span></label>' +
                                            '<div ' +
                                            'class="admin__field-control mp_attachments_location_container__selection">'
                                            + '<input type="text" name="product[mpattachments][file_name]"' +
                                            ' class="input-text admin__control-text required-entry _required"' +
                                            ' id="mp_attachments_input_name" style="width: 89%;min-width: 15rem">' +
                                            '</div>';
                                    case 3:
                                        return '<div ' +
                                            'class="admin__field-control mp_attachments_location_container__selection">'
                                            + '<button id="mp_attachment_link_add-' + popupCurrentDate.getTime()
                                            + '" class="mp-attachment-details-save" type="button">'
                                            + $t('Add') + '</div>';
                                }
                            }
                            $('#mp_attachments_type-' + popupCurrentDate.getTime()).on('change', function () {
                                if ($(this).val() === '1') {
                                    $(this).parents('#mp_attachments_popup').find('#mp_attachments_link')
                                    .html(getInputHtml(0));
                                    $(this).parents('#mp_attachments_popup').find('#mp_attachments_label')
                                    .html(getInputHtml(1));
                                    $(this).parents('#mp_attachments_popup').find('#mp_attachments_name')
                                    .html(getInputHtml(2));
                                    $(this).parents('#mp_attachments_popup')
                                    .find('#mp_attachments_link_button').html(getInputHtml(3));
                                    $('#mp-image-placeholder-' + popupCurrentDate.getTime()).hide();
                                } else {
                                    $(this).parents('#mp_attachments_popup').find('#mp_attachments_link').empty();
                                    $(this).parents('#mp_attachments_popup').find('#mp_attachments_label').empty();
                                    $(this).parents('#mp_attachments_popup').find('#mp_attachments_name').empty();
                                    $(this).parents('#mp_attachments_popup')
                                    .find('#mp_attachments_link_button').empty();
                                    $('#mp-image-placeholder-' + popupCurrentDate.getTime()).show();
                                }
                            });
                            $('body').on('click', '#mp_attachment_link_add-' + popupCurrentDate.getTime(), function () {
                                var formEL      = $('#mp_productgrid_attachments-' + popupCurrentDate.getTime()),
                                    currentDate = new Date(),
                                    valid       = true;

                                formData    = new FormData();
                                if (formEL.find('#mp_attachments_input_label')
                                    && (formEL.find('#mp_attachments_input_label').val() === ''
                                        || formEL.find('#mp_attachments_input_name').val() === ''
                                        || formEL.find('[name="product[mpattachments][file_path]"]').val() === '')) {
                                    valid = false;
                                }

                                if (formEL.valid() && valid) {
                                    $('.ln_overlay').show();
                                    formData.append('value_id', currentDate.getTime() + '0');
                                    formData.append('file_path', formEL.find('#mp_attachments_link>div>input').val());
                                    formData.append('label', formEL.find('#mp_attachments_input_label').val());
                                    formData.append('name', formEL.find('#mp_attachments_input_name').val());
                                    formData.append('type', '1');
                                    formData.append('position', '0');
                                    formData.append('is_grid', '1');

                                    $.ajax({
                                        type: "POST",
                                        url: row.mp_attachment_ajax_url,
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        success:
                                            function (response) {
                                                if (response.status) {
                                                    formEL.find('#mp-productgrid-attachments')
                                                    .prepend($(response.faq_list).html());
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
                                } else if (valid === false) {
                                    alert({
                                        title: 'Validate',
                                        content: $t('Please do not leave the link, name and label fields blank.')
                                    });
                                }
                            });
                            /** save product grid attachment detail function */
                            $('#mp-popup-attachment-save-' + popupCurrentDate.getTime()).on('click', function () {
                                var formEL    = $('#mp_productgrid_attachments-' + popupCurrentDate.getTime()),
                                    popupForm = new FormData(formEL[0]);

                                $('.ln_overlay').show();
                                popupForm.append('product_id', productId);
                                $.ajax({
                                    type: "POST",
                                    url: row.mp_attachment_ajax_save_url,
                                    data: popupForm,
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    complete: function () {
                                        updateGrid();
                                        $('.ln_overlay').hide();
                                        previewPopup.trigger('closeModal');
                                    }
                                });
                            });
                        },
                    complete: function () {
                        $('.ln_overlay').hide();
                    }
                });
            }
        }
    });
});

