/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

'use strict';
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/file-uploader',
    './dependency',
    'mage/url'
], function ($, ko, Element, dependency, url) {

    return Element.extend(dependency).extend({
        defaults: {
            elementTmpl: 'Mageplaza_CustomForm/form/field/uploader',
            isLoading: true,
            maxFileSize: 1000000,
            autoUpload: false,
            previewTmpl: 'Mageplaza_CustomForm/form/field/uploader/preview',
            uploaderConfig: {
                url: url.build('mpcustomform/viewfile/upload')
            }
        },
        initObservable: function () {
            var self = this;

            this._super();
            this.fileUrl = ko.observable();
            this.value.subscribe(function (newValue) {
                if (newValue && newValue[0] && newValue[0].file) {
                    self.fileUrl(newValue[0].file);
                } else {
                    self.fileUrl('');
                }
            });
            this.addFieldToProvider();
            this.dependencyObs();

            return this;
        },
        resetField: function () {
            this.reset();
        },
        onBeforeFileUpload: function (e, data) {
            var file              = data.files[0],
                allowed           = this.isFileAllowed(file),
                target            = $(e.target),
                allowedExtensions = this.allowedExtensions || '';


            if (allowed.passed) {
                target.on('fileuploadsend', function (event, postData) {
                    postData.data.append('param_name', this.paramName);
                    postData.data.append('allowed_extensions', allowedExtensions);
                }.bind(data));

                target.fileupload('process', data).done(function () {
                    data.submit();
                });
            } else {
                this.notifyError(allowed.message);
            }
        }
    });
});