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
 * @category  Mageplaza
 * @package   Mageplaza_CustomForm
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

// eslint-disable-next-line strict
'use strict';
define(
    [
        'jquery',
        'underscore',
        'mage/translate',
        'mage/template',
        'Magento_Ui/js/modal/modal',
        './template',
        'mage/adminhtml/wysiwyg/tiny_mce/setup'
        // eslint-disable-next-line strict
    ], function ($, _, $t, mageTemplate, modal, formTemplate) {
        var formCreatorEl = $('#form_creator_fieldset');

        $.widget(
            'mageplaza.customForm', {
                options: {
                    template: formTemplate
                },
                _create: function () {
                    var responses    = this.prepareData(this.options.responses),
                        tabResponses = $('li[data-ui-id="mageplaza-custom-form-form-tabs-tab-item-responses-summary"]');

                    if (responses) {
                        tabResponses.show();
                    }

                    this.initFormCreator();
                    $('.field-group-view').sortable({cancel: ".google-map,input,select,textarea,.mp-popup-container"});
                },
                initFormCreator: function () {
                    this.renderForm(this.options.formData);
                    this.loadTemplateObs();
                    this.previewObs();
                    this.addNewPageObs();
                    this.deletePageObs();
                    this.duplicatePageObs();
                    this.addNewFieldGroupObs();
                    this.deleteFieldGroupObs();
                    this.changeFieldGroupNameObs();
                    this.duplicateFieldGroupObs();
                    this.addNewFieldObs();
                    this.duplicateFieldObs();
                    this.editFieldObs();
                    this.addOptionObs();
                    this.addDependObs();
                    this.deleteDependObs();
                    this.selectDependObs();
                    this.selectDependValueObs();
                    this.deleteFieldObs();
                    this.removeOptionObs();
                    this.changeDatetimeTypeObs();
                    this.addGridRowColumnObs();
                    this.openAgreementModalObs();
                    this.changeAnchorTypeObs();
                    this.ratingObs();
                    this.renderEmailAddressField();
                },
                previewObs: function () {
                    var self = this;

                    formCreatorEl.on('click', 'button.primary.preview', function () {
                        var form            = $('<form/>', {
                                action: $(this).data('url'),
                                target: '_blank',
                                method: 'POST'
                            }),
                            formData        = self.getFormDataByType(
                                formCreatorEl.find('.form-creator-block')
                                .find('input:not([type="checkbox"]):not([type="radio"]),select,textarea,input:checked')
                                , 'page'),
                            customStyle     = $('#form_custom_css').val(),
                            formStyle       = $('#form_form_style').val(),
                            popupButtonText = $('#form_fb_button_text').val(),
                            popupType       = $('#form_popup_type').val();

                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'form[custom_form]',
                            value: JSON.stringify(formData)
                        }));
                        form.append($('<input>', {type: 'hidden', name: 'form[custom_css]', value: customStyle}));
                        form.append($('<input>', {type: 'hidden', name: 'form[form_style]', value: formStyle}));
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'form[fb_button_text]',
                            value: popupButtonText
                        }));
                        form.append($('<input>', {type: 'hidden', name: 'form[popup_type]', value: popupType}));

                        $('body').append(form);
                        form.submit();
                        form.remove();
                    });
                },
                loadTemplateObs: function () {
                    var self = this;

                    formCreatorEl.on('click', 'button.load-template', function () {
                        var templateId = $('#load-template').val(),
                            formData   = self.options.template[templateId].data,
                            newId      = self.getIdByTime(),
                            count      = 0;

                        $('#form_custom_css').text(self.options.template[templateId].style);
                        _.each(formData, function (page, pageId) {
                            formData[pageId]._id = newId + count;
                            count++;
                        });
                        formCreatorEl.find('.form-creator-block').html('');

                        self.renderForm(formData);
                        $('.admin__collapsible-title').trigger('click');
                    });
                },
                renderEmailAddressField: function () {
                    var html                = '',
                        emailAddressFieldEl = $('#form_auto_res_email_address');

                    html += '<option value="">' + $t('--Please Select--') + '</option>';
                    formCreatorEl.find('.field-container.text').each(function () {
                        var label    = $(this).find('.field-title strong').text(),
                            value    = $(this).parents('.admin__collapsible-block-wrapper').data('id') +
                                '-' + $(this).parents('.field-group-view-container').data('id') + '-' + $(this).data('id'),
                            selected = '';

                        if (emailAddressFieldEl.val() === value) {
                            selected = 'selected';
                        }
                        html += '<option value="' + value + '" ' + selected + '>' + label + '</option>';
                    });
                    emailAddressFieldEl.html(html);
                },
                createWysiwygDescription: function (id) {
                    var self = this;

                    require([
                        'tinymce' + (self.options.isNewVersion ? '4' : '')
                    ], function (tinyMCE) {
                        var config = self.options.wysiwygConfig,
                            wysiwyg_description;

                        $.extend(config, {
                            settings: {
                                width: '99%',
                                height: '200px',
                                'white-space': 'normal',
                                theme_advanced_buttons1:
                                    'bold,italic,|,justifyleft,justifycenter,justifyright,|,forecolor,backcolor,',
                                theme_advanced_buttons2: 'fontselect,fontsizeselect,|,',
                                theme_advanced_buttons3: '|,link,unlink,image,|,bullist,numlist,|,code',
                                theme_advanced_buttons4: null
                            },
                            files_browser_window_url: false
                        });
                        if (typeof self.options.tinymceConfig == 'string') {
                            self.options.tinymceConfig = JSON.parse(self.options.tinymceConfig);
                        }
                        if (self.options.isNewVersion) {
                            wysiwyg_description = new wysiwygSetup(id, config);
                        } else {
                            wysiwyg_description = new tinyMceWysiwygSetup(id, config);
                        }
                        if ($.isReady) {
                            tinyMCE.dom.Event.domLoaded = true;
                        }
                        wysiwyg_description.setup("exact");
                    });
                },
                duplicatePageObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.duplicate-page', function () {
                        var pageEl      = $(this).parents('.admin__collapsible-block-wrapper'),
                            pageId      = self.getIdByTime(),
                            pageData    = self.getFormDataByType(
                                pageEl.find('input:not([type="checkbox"]),select,textarea,input:checked'),
                                'page',
                                pageId
                            )[pageEl.data('id')],
                            dupPageHtml = self.renderPage(pageData),
                            dupPageEl   = pageEl.next(),
                            fieldData;

                        pageData.default = 0;
                        pageEl.after(dupPageHtml);
                        self.renamePage();
                        self.renameFieldGroups(dupPageEl.find('.field-groups'));
                        self.createWysiwygDescription('form_page_' + pageId + '_description');
                        dupPageEl.find('.dropdown-options ul').sortable();
                        dupPageEl.find('.field-container').each(function () {
                            if ($(this).find('.field-type').val() === 'map') {
                                fieldData = self.getFormDataByType($(this).find('input,select,textarea')
                                .serializeArray());
                                self.initMap(fieldData, $(this));
                            }
                        });
                        self.renderEmailAddressField();
                    });
                },
                duplicateFieldGroupObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.duplicate-field-group', function () {
                        var fieldGroupHtml,
                            fieldGroupViewHtml,
                            dupFieldGroupViewEl,
                            fieldData,
                            pageEl           = $(this).parents('.admin__collapsible-block-wrapper'),
                            fieldGroupEl     = $(this).parents('.custom-field-group'),
                            fieldGroupViewEl = $(this).parents('.custom-page-content')
                            .find('.form-view .field-group-view-container[data-id="' + fieldGroupEl.data('id') + '"]'),
                            fieldGroupData   = fieldGroupViewEl.find('input,select,textarea').serializeArray()
                            .concat(fieldGroupEl.find('input,select,textarea').serializeArray());

                        fieldGroupData         = self.getFormDataByType(
                            fieldGroupData,
                            'groups',
                            self.getIdByTime()
                        )[fieldGroupViewEl.data('id')];
                        fieldGroupData.default = 0;
                        fieldGroupHtml         = self.renderFieldGroup(fieldGroupData);
                        fieldGroupViewHtml     = self.renderFieldGroupView(fieldGroupData);
                        fieldGroupEl.after(fieldGroupHtml);
                        fieldGroupViewEl.after(fieldGroupViewHtml);
                        self.renameFieldGroups(pageEl.find('.field-groups'));
                        dupFieldGroupViewEl =
                            pageEl.find('.field-group-view-container[data-id="' + fieldGroupData._id + '"]');
                        dupFieldGroupViewEl.find('.field-container').each(function () {
                            if ($(this).find('.field-type').val() === 'map') {
                                fieldData = self.getFormDataByType(
                                    $(this).find('input,select,textarea').serializeArray()
                                );
                                self.initMap(fieldData, $(this));
                            }
                        });
                        self.renderEmailAddressField();
                        dupFieldGroupViewEl.find('.dropdown-options ul').sortable();
                    });
                },
                renamePage: function () {
                    var count = 1;

                    $('#form_creator_fieldset .admin__collapsible-block-wrapper').each(function () {
                        $(this).find('.custom-page-title').text($t('Page ') + count);
                        count++;
                    });
                },
                renderPage: function (pageData) {
                    var html = mageTemplate('#page_template');

                    return html({data: pageData});
                },
                renderFieldGroup: function (fieldGroupData) {
                    var html = mageTemplate('#field_group_template');

                    return html({data: fieldGroupData});
                },
                renderFieldRequired: function (data) {
                    var html = mageTemplate('#required-field-template');

                    return html({data: data});
                },
                renderFieldControlButton: function () {
                    var html = mageTemplate('#field-control-button-template');

                    return html({data: {}});
                },
                getIdByTime: function () {
                    var d = new Date();

                    return d.getTime() + '_' + d.getMilliseconds();
                },
                preparePageData: function (data) {
                    var self = this;

                    return $.extend(data, {
                        renderFieldGroup: function (fieldGroupData) {
                            return self.renderFieldGroup(fieldGroupData);
                        },
                        renderFieldGroupView: function (fieldGroupData) {
                            return self.renderFieldGroupView(fieldGroupData);
                        }
                    });
                },
                prepareFieldGroupData: function (data) {
                    var self = this;

                    return $.extend(data, {
                        renderField: function (fieldData) {
                            return self.renderField(fieldData);
                        }
                    });
                },
                addNewPageObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.form-control-button .add-new-page', function () {
                        var id   = self.getIdByTime(),
                            data = self.preparePageData({
                                _id: id,
                                sm_button_text: $t('Submit'),
                                field_groups: [{
                                    _id: id,
                                    default: 1,
                                    title: $t('Field Group 1'),
                                    parentName: 'form[page][' + id + '][field_groups]'
                                }]
                            }),
                            html = self.renderPage(data);

                        formCreatorEl
                        .find(
                            '.admin__collapsible-block-wrapper > .fieldset-wrapper-title.admin__fieldset-wrapper-title'
                        ).each(function () {
                            if ($(this).siblings('.fieldset-wrapper-content').css('display') !== 'none') {
                                $(this).find('span.admin__collapsible-title').trigger('click');
                            }
                        });
                        $('.form-creator-block').append(html)
                        .find('.fieldset-wrapper-content.admin__fieldset-wrapper-content').collapse('hide');
                        $('#page-content-' + id).collapse('show');
                        $('.field-group-view').sortable({
                            cancel: ".google-map,input,select,textarea,.mp-popup-container"
                        });
                        self.createWysiwygDescription('form_page_' + id + '_description');
                        self.renamePage();
                    });
                },
                deletePageObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.page-control-button .delete-page', function () {
                        $(this).parents('.admin__collapsible-block-wrapper').remove();
                        self.renamePage();
                    });
                },
                renameFieldGroups: function (fieldGroups) {
                    var count = 1;

                    fieldGroups.children().each(function () {
                        $(this).find('.admin__collapsible-title > span').text($t('Field Group ') + count);
                        count++;
                    });
                },
                addNewFieldGroupObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.page-control-button .add-new-field', function () {
                        var id                 = self.getIdByTime(),
                            parent             = $(this).parents('.admin__collapsible-block-wrapper'),
                            parentName         = parent.data('parentname') + '[field_groups]',
                            fieldGroupData     = {_id: id, parentName: parentName, name: $t('Field Group')},
                            fieldGroupHtml     = self.renderFieldGroup(fieldGroupData),
                            fieldGroupViewHtml = self.renderFieldGroupView(fieldGroupData);

                        parent.find('.field-groups').append(fieldGroupHtml);
                        parent.find('.form-view').append(fieldGroupViewHtml);
                        self.renameFieldGroups(parent.find('.field-groups'));
                        $('.field-group-view[data-id="' + id + '"]')
                        .sortable({cancel: ".google-map,input,select,textarea,.mp-popup-container"});
                    });
                },
                deleteFieldGroupObs: function () {
                    formCreatorEl.on('click', '.field-group-control-button .delete-field-group', function () {
                        var fieldGroupEl   = $(this).parents('.custom-field-group'),
                            id             = fieldGroupEl.data('id'),
                            fieldGroupView = $('.field-group-view-container[data-id="' + id + '"]');

                        fieldGroupView.find('.field-control-button .delete').trigger('click');
                        fieldGroupView.remove();
                        fieldGroupEl.remove();
                    });
                },
                prepareFieldData: function (data) {
                    var self = this;

                    data = $.extend(data, {
                        renderFieldRequired: function (fieldData) {
                            return self.renderFieldRequired(fieldData);
                        },
                        renderFieldControlButton: function () {
                            return self.renderFieldControlButton();
                        },
                        renderFieldDepend: function (fieldData) {
                            return self.renderFieldDepend(fieldData);
                        },
                        renderGrid: function (fieldData) {
                            return self.renderGrid(fieldData);
                        },
                        renderStars: function (fieldData) {
                            return self.renderStars(fieldData);
                        },
                        renderCheckboxOptionsView: function (fieldData) {
                            return self.renderCheckboxOptionsView(fieldData);
                        }
                    });

                    return data;
                },
                renderStars: function (data) {
                    var html = '',
                        i,
                        selectedClass;

                    for (i = 1; i <= +data.number_star; i++){
                        selectedClass = i <= +data.default ? ' selected' : '';

                        html +=
                            '<li class="star' + selectedClass + '" data-value="' + i + '">' +
                            '   <i class="fa fa-star fa-fw"></i>' +
                            '</li>';
                    }
                    return html;
                },
                prepareDataForAddNewField: function (type, parentName) {
                    var id      = this.getIdByTime(),
                        rows    = {},
                        columns = {},
                        data    = {
                            _id: id,
                            type: type,
                            width: 100,
                            depends: {},
                            parentName: parentName,
                            title: this.options.fieldTypes[type] + ' Field'
                        },
                        options = {};

                    options[id + '1'] = {
                        label: $t('Option A'),
                        value: 'a'
                    };
                    options[id + '2'] = {
                        label: $t('Option B'),
                        value: 'b'
                    };
                    options[id + '3'] = {
                        label: $t('Option C'),
                        value: 'c'
                    };
                    switch (type){
                        case 'datetime':
                            data.dateTimeType = 'datetime-local';
                            break;
                        case 'dropdown':
                            data.options = options;
                            break;
                        case 'checkbox':
                            data.options = options;
                            data.row     = 1;
                            break;
                        case 'radio':
                            data.options = options;
                            data.row     = 1;
                            break;
                        case 'grid':
                            rows[id + '1']    = 'Row 1';
                            rows[id + '2']    = 'Row 2';
                            rows[id + '3']    = 'Row 3';
                            data.rows         = rows;
                            columns[id + '1'] = 'Column 1';
                            columns[id + '2'] = 'Column 2';
                            columns[id + '3'] = 'Column 3';
                            data.columns      = columns;
                            data.select_type  = 'radio';
                            break;
                        case 'agreement':
                            data.anchor_type    = 'redirect';
                            data.checkbox_label = 'I agree with {anchor}';
                            data.anchor_text    = 'Terms and Conditions';
                            data.url            = 'https://www.google.com/';
                            break;
                        case 'rating':
                            data.number_star = 5;
                            data.default     = 0;
                            data.validate    = 'required-entry';
                            break;
                    }
                    data = this.prepareFieldData(data);
                    return data;
                },
                addNewFieldObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.add-field', function () {
                        var selectEl     = $(this).siblings('select'),
                            type         = selectEl.val(),
                            id           = selectEl.data('id'),
                            parentName   = $(this).parents('.custom-field-group').data('parentname') + '[' + id + ']' + '[fields]',
                            data         = self.prepareDataForAddNewField(type, parentName),
                            html         = self.renderField(data),
                            fieldGroupEl = $(this).parents('.fieldset-wrapper-content.admin__fieldset-wrapper-content').find('.field-group-view[data-id="' + id + '"]');

                        fieldGroupEl.append(html);
                        fieldGroupEl.find('.dropdown-options ul').sortable();
                        if (type === 'map') {
                            self.initMap(data, fieldGroupEl.find('.field-container[data-id="' + data._id + '"]'));
                        }
                        if (type === 'text') {
                            self.renderEmailAddressField();
                        }
                    });
                },
                renderField: function (data) {
                    var html = mageTemplate('#field-template-' + data.type);

                    return html({data: data});
                },
                renderFieldGroupView: function (data) {
                    var html = mageTemplate('#field_group_view_template');

                    data = this.prepareFieldGroupData(data);
                    return html({data: data});
                },
                updateDropdownOption: function (popup) {
                    var optionHtml = '',
                        fieldEl    = $(popup).parents('.field-container'),
                        defVal     = fieldEl.find('select.dropdown').val(),
                        selected;

                    $(popup).find('.dropdown-options li').each(function () {
                        selected = defVal === $(this).find('.option-value').val() ? ' selected' : '';
                        optionHtml +=
                            '<option value="' + $(this).find('.option-value').val() + '"' + selected + '>' +
                            $(this).find('.option-label').val() +
                            '</option>';
                    });
                    fieldEl.find('select.dropdown').html(optionHtml);
                },
                updateRowNumber: function (self) {
                    var parent    = $(self).parents('.field-container'),
                        fieldData = this.getFormDataByType(parent.find('input,select,textarea').serializeArray()),
                        html      = this.renderCheckboxOptionsView(fieldData);

                    parent.find('.checkbox-options-view').html(html);
                },
                editFieldObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.field-control-button .edit', function (e) {
                        var fieldEl = $(this).parents('.field-container'),
                            options = {
                                'type': 'popup',
                                'title': $(this).parent().siblings('label').find('strong').text() + $t(' Option'),
                                'responsive': true,
                                'appendTo': fieldEl.find('.mp-popup-container'),
                                'buttons': [
                                    {
                                        text: $t('Ok'),
                                        class: 'action primary',
                                        click: function () {
                                            if (this.element.find('.field-type').val() === 'grid') {
                                                self.mpValidateUnique(this.element.find('.grid-rows'));
                                                self.mpValidateUnique(this.element.find('.grid-columns'));
                                            } else {
                                                self.mpValidateUnique(this.element);
                                            }
                                            self.mpValidateRequire(this.element);
                                            if (this.element.find('.field-type').val() === 'upload') {
                                                self.mpValidateFileType(this.element);
                                            }
                                            if (!this.element.find('.mp-unique-validate').length
                                                && !this.element.find('.mp-required-validate').length
                                                && this.element.find('input').valid()
                                            ) {
                                                self.updateField(this.element);
                                                switch (this.element.find('.field-type').val()){
                                                    case 'checkbox':
                                                    case 'radio':
                                                        self.updateRowNumber(this.element);
                                                        break;
                                                    case 'dropdown':
                                                        self.updateDropdownOption(this.element);
                                                        break;
                                                    case 'grid':
                                                        self.updateGridOptions(this.element);
                                                        break;
                                                    case 'agreement':
                                                        self.updateAgreementOptions(this.element);
                                                        break;
                                                    case 'rating':
                                                        self.updateRating(this.element);
                                                        break;
                                                    case 'text':
                                                        self.renderEmailAddressField();
                                                        break;
                                                }
                                                this.closeModal();
                                            }
                                        }
                                    }
                                ],
                                'modalCloseBtnHandler': function () {
                                    this.element.parents('.field-container').replaceWith(self.tempFieldHtml);
                                    this.closeModal();
                                }
                            },
                            modalEl = fieldEl.find('.edit-popup'),
                            popup;

                        e.preventDefault();
                        if (modalEl.data('modal')) {
                            modalEl.data('modal').openModal();
                            return;
                        }
                        popup = modal(options, modalEl);
                        popup.openModal();
                        modalEl.on('modalopened', function () {
                            var fieldData;

                            popup.focussedElement = $(this).parents('.field-container');
                            self.updateDepend(this);
                            fieldData          = self.getFormDataByType($(this).parents('.field-container')
                            .find('input,select').serializeArray(), 'field');
                            self.tempFieldHtml = self.renderField(fieldData);
                        });
                    });
                },
                updateField: function (self) {
                    $(self).parents('.field-container')
                    .find('.field-title strong').text($(self).find('.option-title').val());
                    $(self).parents('.field-container').css('width', $(self).find('.option-width').val() + '%');
                },
                updateRating: function (self) {
                    var fieldEl = $(self).parents('.field-container'),
                        data    = this.getFormDataByType($(self).find('input,select').serializeArray()),
                        html    = this.renderStars(data);

                    fieldEl.find('.rating-stars ul.stars').html(html);
                },
                updateAgreementOptions: function (self) {
                    var checkboxLabel = $(self).find('.checkbox-label input').val(),
                        anchorText    = $(self).find('.anchor-text input').val(),
                        fieldEl       = $(self).parents('.field-container'),
                        anchor, url, content;

                    if ($(self).find('.select-anchor-type').val() === 'redirect') {
                        url    = $(self).find('.agreement-url input').val();
                        anchor = '<a href="' + url + '" target="_blank">' + anchorText + '</a>';
                    } else {
                        content = $(self).find('.agreement-content textarea').val();
                        fieldEl.find('.anchor-modal').html(content);
                        anchor = '<a href="#" class="open-agreement-modal">' + anchorText + '</a>';
                    }
                    fieldEl.find('.checkbox-label.admin__field-control span')
                    .html(checkboxLabel.replace('{anchor}', anchor));
                },
                changeAnchorTypeObs: function () {
                    var self = this;

                    formCreatorEl.on('change', '.select-anchor-type', function () {
                        self.changeAnchorType(this);
                    });
                },
                changeAnchorType: function (self) {
                    var editPopupEl = $(self).parents('.edit-popup');

                    if ($(self).val() === 'redirect') {
                        editPopupEl.find('.agreement-url').show();
                        editPopupEl.find('.agreement-title').hide();
                        editPopupEl.find('.agreement-content').hide();
                    } else {
                        editPopupEl.find('.agreement-url').hide();
                        editPopupEl.find('.agreement-title').show();
                        editPopupEl.find('.agreement-content').show();
                    }
                },
                openAgreementModalObs: function () {
                    formCreatorEl.on('click', '.open-agreement-modal', function () {
                        var fieldEl = $(this).parents('.field-container'),
                            title   = fieldEl.find('.agreement-title input').val(),
                            modalEl = fieldEl.find('.anchor-modal'),
                            options = {
                                'type': 'popup',
                                'title': title,
                                'responsive': true,
                                'appendTo': fieldEl,
                                'buttons': [
                                    {
                                        text: $t('Cancel'),
                                        class: 'action secondary action-hide-popup',
                                        click: function () {
                                            this.closeModal();
                                        }
                                    },
                                    {
                                        text: $t('Ok'),
                                        class: 'action primary action-hide-popup',
                                        click: function () {
                                            this.closeModal();
                                        }
                                    }
                                ]
                            };

                        modal(options, modalEl).openModal();
                    });
                },
                build: function (base, key, value) {
                    base[key] = value;
                    return base;
                },
                push_counter: function (key) {
                    var push_counters = {};

                    if (push_counters[key] === undefined) {
                        push_counters[key] = 0;
                    }
                    return push_counters[key]++;
                },
                convertFormArrayToObject: function (array) {
                    var self     = this,
                        json     = {},
                        patterns = {
                            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                            "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
                            "push": /^$/,
                            "fixed": /^\d+$/,
                            "named": /^[a-zA-Z0-9_]+$/
                        };

                    _.each(array, function (obj) {
                        var k,
                            keys,
                            merge,
                            reverse_key;

                        // skip invalid keys
                        if (!patterns.validate.test(obj.name)) {
                            return;
                        }

                        keys        = obj.name.match(patterns.key);
                        merge       = obj.value;
                        reverse_key = obj.name;

                        while ((k = keys.pop()) !== undefined){

                            // adjust reverse_key
                            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                            // push
                            if (k.match(patterns.push)) {
                                merge = self.build([], self.push_counter(reverse_key), merge);
                            }

                            // fixed
                            else if (k.match(patterns.fixed)) {
                                merge = self.build([], k, merge);
                            }

                            // named
                            else if (k.match(patterns.named)) {
                                merge = self.build({}, k, merge);
                            }
                        }

                        json = $.extend(true, json, merge);
                    });

                    return json;
                },
                duplicateFieldObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.field-control-button .duplicate', function (e) {
                        var fieldEl   = $(this).parents('.field-container'),
                            form      = fieldEl.find('select,input').serializeArray(),
                            fieldData = self.getFormDataByType(form, 'field', self.getIdByTime()),
                            html      = self.renderField(fieldData);

                        e.preventDefault();
                        fieldEl.after(html);
                        if (fieldData.type === 'map') {
                            self.initMap(fieldData, fieldEl.next());
                        }
                        if (fieldData.type === 'text') {
                            self.renderEmailAddressField();
                        }
                    });
                },
                addOptionObs: function () {
                    var self = this;

                    $('body').on('click', 'button.mp-add-option', function () {
                        var id         = self.getIdByTime(),
                            parentEl   = $(this).parents('.edit-popup'),
                            parentId   = parentEl.data('id'),
                            parentName = parentEl.data('parentname') + '[' + parentId + ']';

                        $(this).siblings('ul').append(
                            '<li>' +
                            '   <label>' +
                            '      <span>' + $t('Label') + '</span>' +
                            '      <input class="option-label mp-required" type="text" name="' + parentName + '[options][' + id + '][label]">' +
                            '   </label>' +
                            '   <label>' +
                            '      <span>' + $t('Value') + '</span>' +
                            '      <input class="option-value mp-required" type="text" name="' + parentName + '[options][' + id + '][value]">' +
                            '   </label>' +
                            '   <a href="#" class="remove-option">x</a>' +
                            '   <div class="validate-container"></div>' +
                            '</li>'
                        );
                    });
                },
                removeOptionObs: function () {
                    formCreatorEl.on('click', '.remove-option,.remove-row,.remove-column', function (e) {
                        e.preventDefault();
                        $(this).parent().remove();
                    });
                },
                changeFieldGroupNameObs: function () {
                    formCreatorEl.on('keyup', '.field-group-name', function () {
                        var id = $(this).parents('.custom-field-group').data('id');

                        $(this).parents('.fieldset-wrapper-content')
                        .find('.field-group-label[data-id="' + id + '"] span').text($(this).val());
                    });
                },
                renderForm: function (formData) {
                    var self        = this,
                        id          = this.getIdByTime(),
                        fieldGroups = {};

                    if (!_.keys(formData).length) {
                        formData        = {};
                        fieldGroups[id] = {
                            _id: id,
                            default: 1,
                            parentName: 'form[page][' + id + '][field_groups]',
                            name: $t('Field Group'),
                            fields: {}
                        };
                        formData[id]    = {
                            _id: id,
                            default: 1,
                            sm_button_text: $t('Submit'),
                            field_groups: fieldGroups
                        };

                    }
                    formData = this.prepareData(formData);
                    _.each(formData, function (page) {
                        var html = self.renderPage(page);

                        $('.form-creator-block').append(html);
                        self.createWysiwygDescription('form_page_' + page._id + '_description');
                    });
                    $('.field-group-view').sortable();
                    $('.field-groups').each(function () {
                        self.renameFieldGroups($(this));
                    });
                    self.renamePage();
                    formCreatorEl.find('.field-container').each(function () {
                        var fieldData;

                        if ($(this).find('.field-type').val() === 'map') {
                            fieldData = self.getFormDataByType($(this).find('input,select,textarea').serializeArray());
                            self.initMap(fieldData, $(this));
                        }
                    });

                    $('li[data-ui-id="mageplaza-custom-form-form-tabs-tab-item-form-creator"]').on('click', function () {
                        var page = $('.form-creator-block > .admin__collapsible-block-wrapper > .admin__fieldset-wrapper-title > .admin__collapsible-title');

                        if (!page.hasClass('active')) {
                            page.trigger('click');
                        }
                    });

                    $('li[data-ui-id="mageplaza-custom-form-form-tabs-tab-item-responses-summary"]').on('click', function () {
                        var page = $('#form_tabs_responses_summary_content > .summary-page-container > .admin__collapsible-block-wrapper > .admin__fieldset-wrapper-content');

                        if (!page.is(':visible')) {
                            page.show();
                        }
                    });

                },
                getDependFieldHtml: function (self) {
                    var dropdownOpt = '',
                        popupId     = $(self).parents('.edit-popup').data('id'),
                        pageEl      = $(self).parents('.admin__collapsible-block-wrapper');

                    pageEl.find(
                        '.field-container.field-dropdown,.field-container.field-checkbox,.field-container.field-radio'
                    ).each(function () {
                        var id           = $(this).data('id'),
                            fieldGroupId = $(this).parents('.field-group-view-container').data('id');

                        if (id === popupId) {
                            return true;
                        }
                        dropdownOpt += '<option value="' + fieldGroupId + '-' + id + '" >'
                            + $(this).find('.field-title strong').text() + '</option>';
                        return true;
                    });
                    if (dropdownOpt !== '') {
                        dropdownOpt = '<option>' + $t('--Please Select--') + '</option>' + dropdownOpt;
                    }
                    return dropdownOpt;
                },
                addDependObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.add-depend', function () {
                        var dropdownOpt = self.getDependFieldHtml(this),
                            parentEl    = $(this).parents('.field-container'),
                            parentName  = parentEl.data('parentname') + '[' + parentEl.data('id') + ']'
                                + '[depends][' + self.getIdByTime() + '][field]',
                            mesEl;

                        if (dropdownOpt === '') {
                            mesEl = $(this).siblings('.messages');

                            mesEl.html($t('No field available to depend'));
                            clearInterval(window.mpCheckInterval);
                            window.mpCheckInterval = setInterval(
                                function () {
                                    mesEl.html('');
                                    clearInterval(window.mpCheckInterval);
                                }, 5000
                            );
                            return false;
                        }
                        dropdownOpt =
                            '<li>' +
                            '   <select class="field-depend-field" id="' + self.getIdByTime() + '" name="' + parentName + '">'
                            + dropdownOpt +
                            '   </select>' +
                            '   <a href="#" class="remove-depend">x</a>' +
                            '</li>';
                        $(this).siblings('ul').append(dropdownOpt);
                        return true;
                    });
                },
                selectDependObs: function () {
                    formCreatorEl.on('change', 'select.field-depend-field', function () {
                        var id         = $(this).val(),
                            parentEl   = $(this).parents('.edit-popup'),
                            parentName = parentEl.data('parentname') + '[' + parentEl.data('id') + '][depends]['
                                + $(this).attr('id') + ']',
                            options    = '',
                            fieldGroupId, fieldId, dependFieldEl, type, rowName, rowId, colName, colId;

                        if (id === '') {
                            $(this).siblings().remove();
                            return false;
                        }
                        id            = id.split('-');
                        fieldGroupId  = id[0];
                        fieldId       = id[1];
                        dependFieldEl = $(this).parents('.admin__collapsible-block-wrapper')
                        .find('.field-group-view-container[data-id="' + fieldGroupId + '"]')
                        .find('.field-container[data-id="' + fieldId + '"]');
                        type          = dependFieldEl.find('.field-type').val();
                        switch (type){
                            case 'radio':
                            case 'checkbox':
                                dependFieldEl.find('.checkbox-options-view label').each(function () {
                                    options += '<option value="' + $(this).find('input').data('id') + '">'
                                        + $(this).find('span').text() +
                                        '</option>';
                                });
                                break;
                            case 'dropdown':
                                options = dependFieldEl.find('.dropdown.select').html();
                                break;
                            case 'grid':
                                dependFieldEl.find('.grid-rows li').each(function () {
                                    rowName = $(this).find('input').val();
                                    rowId   = $(this).data('id');
                                    dependFieldEl.find('.grid-columns li').each(function () {
                                        colName = $(this).find('input').val();
                                        colId   = $(this).data('id');
                                        options += '<option value="' + rowId + '-' + colId + '">'
                                            + rowName + ' - ' + colName +
                                            '</option>';
                                    });
                                });
                                break;
                        }
                        $(this).siblings('.field-depend-value').remove();
                        $(this).after(
                            '<select class="field-depend-value field-value" name="' + parentName + '[value]">'
                            + options +
                            '</select>'
                        );
                        return true;
                    });
                },
                selectDependValueObs: function () {
                    formCreatorEl.on('change', 'select.field-depend-value.field-value', function () {
                        $(this).siblings('.value-name').text($(this).find('option:selected').text());
                    });
                },
                updateDepend: function (self) {
                    var elf  = this,
                        html = this.getDependFieldHtml($(self).find('.field-depend ul'));

                    $(self).find('.field-depend ul li').each(function () {
                        var dependField        = $(this).find('.field-depend-field'),
                            dependFieldVal     = dependField.val(),
                            dependFieldPath    = dependField.val().split('-'),
                            dependFieldGroupId = dependFieldPath[0],
                            dependFieldId      = dependFieldPath[1],
                            dependValue        = $(this).find('.field-depend-value.field-value').val(),
                            dependValueEl, issetVal;

                        if (!$(this).parents('.fieldset-wrapper-content')
                        .find('.field-group-view-container[data-id="' + dependFieldGroupId + '"]')
                        .find('.field-container[data-id="' + dependFieldId + '"]').length) {
                            $(this).remove();
                            return false;
                        }
                        dependField.trigger('change');
                        dependField.html(html);
                        dependField.val(dependFieldVal);
                        dependValueEl = dependField.siblings('.field-depend-value.field-value');
                        issetVal      = $.map(dependValueEl.find('option'), function (option) {
                            if (option.value === dependValue) {
                                return option.value;
                            }
                        });
                        if (issetVal.length) {
                            dependValueEl.val(dependValue);
                        } else {
                            $(this).remove();
                        }
                        return true;
                    });
                    if ($(self).hasClass('agreement')) {
                        elf.changeAnchorType($(self).find('.select-anchor-type'));
                    }
                },
                deleteDependObs: function () {
                    formCreatorEl.on('click', '.remove-depend', function () {
                        $(this).parent().remove();
                    });
                },
                renderFieldDepend: function (data) {
                    var html = mageTemplate('#field-depends-template');

                    return html({data: data});
                },
                deleteFieldObs: function () {
                    formCreatorEl.on('click', '.field-control-button .delete', function (e) {
                        var parentEl = $(this).parents('.field-container');

                        e.preventDefault();
                        if (!parentEl.hasClass('dependable')) {
                            parentEl.remove();
                            return false;
                        }
                        $(this).parents('.admin__collapsible-block-wrapper')
                        .find('select.field-depend-field')
                        .each(function () {
                            if ($(this).val() === parentEl.data('id')) {
                                $(this).parent().remove();
                            }
                        });
                        parentEl.remove();
                        return true;
                    });
                },
                changeDatetimeTypeObs: function () {
                    formCreatorEl.on('change', '.option-date', function () {
                        $(this).parents('.field-container')
                        .find('.admin__field-control input.date-time').attr('type', $(this).val());
                    });
                },
                addGridRowColumnObs: function () {
                    var self = this;

                    formCreatorEl.on('click', '.mp-add-row,.mp-add-column', function () {
                        var type       = $(this).hasClass('mp-add-row') ? 'row' : 'column',
                            id         = self.getIdByTime(),
                            parentEl   = $(this).parents('.edit-popup'),
                            parentName = parentEl.data('parentname');

                        $(this).siblings('ul').append(
                            '<li data-id="' + id + '">' +
                            '   <label>' +
                            '       <span>' + $t('Label') + '</span>' +
                            '       <input class="option-' + type + ' mp-required mp-unique" name="' + parentName + '[' + parentEl.data('id') + '][' + type + 's][' + id + ']" type="text">' +
                            '   </label>' +
                            '   <a href="#" class="remove-' + type + '">x</a>' +
                            '   <div class="validate-container"></div>' +
                            '</li>'
                        );
                    });
                },
                mpValidateUnique: function (self) {
                    var valid    = true,
                        uniqueEl = $(self).find('input.mp-unique'),
                        names    = uniqueEl.serializeArray();

                    uniqueEl.each(function () {
                        var val   = $(this).val(),
                            count = names.filter(function (name) {
                                return name.value.trim().replace(' ', '-').toLowerCase()
                                    === val.trim().replace(' ', '-').toLowerCase();
                            }).length;

                        if (count > 1) {
                            $(this).parents('li').find('.validate-container')
                            .html('<div class="mp-unique-validate" style="color: red">'
                                + $t('This value must be unique') +
                                '</div>'
                            );
                            valid = false;
                        }
                    });
                    if (valid) {
                        $(self).find('.mp-unique-validate').remove();
                    }
                },
                mpValidateRequire: function (self) {
                    var valid     = true,
                        requireEl = $(self).find('input.mp-required');

                    requireEl.each(function () {
                        if (!$(this).val().trim()) {
                            $(this).parents('li').find('.validate-container').html(
                                '<div class="mp-required-validate" style="color: red">'
                                + $t('This value is required') +
                                '</div>'
                            );
                            valid = false;
                        }
                    });
                    if (valid) {
                        $(self).find('.mp-required-validate').remove();
                    }
                },
                mpValidateFileType: function (self) {
                    var inputEl         = self.find('.file-type-allowed'),
                        fileTypeAllowed = inputEl.val().split(',').map(function (type) {
                            if (type && type.trim()) {
                                return type.trim();
                            }
                        }).filter(Boolean),
                        invalidTypes    = [],
                        mimeTypes       = this.options.mimeTypes;

                    _.each(fileTypeAllowed, function (type) {
                        if (!mimeTypes[type]) {
                            invalidTypes.push(type);
                        }
                    });
                    if (!_.isEmpty(invalidTypes)) {
                        invalidTypes = invalidTypes.join(',');
                        inputEl.siblings().remove();
                        inputEl.after(
                            '<div class="mp-required-validate" style="color: red">' +
                            $t('Some file type is not allowed') + ' (' + invalidTypes + ')' +
                            '</div>'
                        );
                    } else {
                        inputEl.siblings().remove();
                    }
                    inputEl.val(fileTypeAllowed.join(','));
                },
                renderGrid: function (data) {
                    var name = '',
                        html = '<table class="admin__table-secondary"><thead><tr><th></th>';

                    _.each(data.columns, function (obj) {
                        html += '<th>' + obj + '</th>';
                    });
                    if (data.select_type === 'radio') {
                        html += '<th>Unselect</th>';
                    }
                    html += '</tr></thead><tbody>';
                    _.each(data.rows, function (obj, index) {
                        html += '<tr>';
                        html += '<td>' + obj + '</td>';
                        _.each(data.columns, function (value, key) {
                            var checked = '';

                            if (data.select_type === 'radio') {
                                name = data.parentName + '[' + data._id + '][default][' + index + ']';
                                if (data.default && data.default[index] === key) {
                                    checked = ' checked';
                                }
                            } else {
                                if (data.default && data.default[index] && data.default[index][key] === key) {
                                    checked = ' checked';
                                }
                                name = data.parentName + '[' + data._id + '][default][' + index + '][' + key + ']';
                            }
                            html += '<td><input data-id="' + index + '" type="' + data.select_type + '" name="' + name + '" value="' + key + '"' + checked + '></td>';
                        });
                        if (data.select_type === 'radio') {
                            name = data.parentName + '[' + data._id + '][default][' + index + ']';
                            html += '<td><input class="unselect" type="' + data.select_type + '" name="' + name + '" value=""></td>';
                        }
                        html += '</tr>';
                    });
                    html += '</tbody></table>';
                    return html;
                },
                updateGridOptions: function (self) {
                    var fieldContainerEl = $(self).parents('.field-container'),
                        gridContainerEl  = fieldContainerEl.find('.grid-container'),
                        form             = fieldContainerEl.find('select,input').serializeArray(),
                        field            = this.getFormDataByType(form),
                        html             = this.renderGrid(field);

                    gridContainerEl.html(html);
                },
                prepareData: function (data, type, _id) {
                    var self = this;

                    _.each(data, function (page, pageId) {
                        page = self.preparePageData(page);
                        if (_id && type === 'page') {
                            page._id = _id;
                        }
                        _.each(page.field_groups, function (field_group, groupId) {
                            field_group = self.prepareFieldGroupData(field_group);
                            if (_id && type === 'groups') {
                                field_group._id = _id;
                            }
                            field_group.parentName = 'form[page][' + (page._id || pageId) + '][field_groups]';
                            _.each(field_group.fields, function (field) {
                                if (_id && type === 'field') {
                                    field._id = _id;
                                }
                                field            = self.prepareFieldData(field);
                                field.parentName = field_group.parentName +
                                    '[' + (field_group._id || groupId) + '][fields]';
                            });
                        });
                    });
                    return data;
                },
                getFormDataByType: function (form, type, _id) {
                    var fieldGroups, fields;

                    form = this.convertFormArrayToObject(form);
                    if (_.isEmpty(form)) {
                        return {};
                    }
                    form.form.page = this.prepareData(form.form.page, type, _id);
                    if (type === 'page') {
                        return form.form.page;
                    }
                    fieldGroups = form.form.page[_.keys(form.form.page)[0]].field_groups;
                    if (type === 'groups') {
                        return fieldGroups;
                    }
                    fields = fieldGroups[_.keys(fieldGroups)[0]].fields;
                    if (type === 'fields') {
                        return fields;
                    }
                    return fields[_.keys(fields)[0]];
                },
                ratingObs: function () {
                    formCreatorEl.on('mouseover', '.stars li', function () {
                        var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

                        // Now highlight all the stars that's not after the current hovered star
                        $(this).parent().children('li.star').each(function (e) {
                            if (e < onStar) {
                                $(this).addClass('hover');
                            } else {
                                $(this).removeClass('hover');
                            }
                        });
                    }).on('mouseout', '.stars li', function () {
                        $(this).parent().children('li.star').removeClass('hover');
                    }).on('click', '.stars li', function () {
                        $(this).siblings().removeClass('selected');
                        $(this).addClass('selected').prevAll().addClass('selected');
                        $(this).parents('.field-container').find('.default-star input').val($(this).data('value'));
                    });
                },
                initMap: function (data, fieldEl) {
                    require([
                        'https://maps.googleapis.com/maps/api/js?key='
                        + this.options.mpGoogleMapKey + '&libraries=places'
                    ], function () {
                        var mapEl    = fieldEl.find('.google-map'),
                            lng      = 0,
                            lat      = 0,
                            zoom     = +data.zoom || 1,
                            input    = fieldEl.find('.map-search')[0],
                            geocoder = new google.maps.Geocoder(),
                            position, map, marker, searchBox;

                        if (!mapEl.length) {
                            return;
                        }
                        if (data.position) {
                            position = data.position.split(',');
                            lat      = +position[0].replace('(', '');
                            lng      = +position[1].replace(')', '').trim();
                        }
                        map    = new google.maps.Map(mapEl[0], {
                            center: {lat: lat, lng: lng},
                            zoom: zoom,
                            enabledMarker: true
                        });
                        marker = new google.maps.Marker();
                        if (data.position) {
                            marker.setPosition(map.center);
                            marker.setMap(map);
                        }
                        google.maps.event.addListener(map, 'zoom_changed', function () {
                            fieldEl.find('input.map-zoom').val(map.zoom);
                        });

                        searchBox = new google.maps.places.SearchBox(input);
                        map.controls[google.maps.ControlPosition.BOTTOM].push(input);
                        searchBox.addListener('places_changed', function () {
                            var bounds = new google.maps.LatLngBounds(),
                                places = searchBox.getPlaces();

                            if (places.length === 0) {
                                return;
                            }
                            marker.setMap(null);
                            places.forEach(function (place) {
                                if (!place.geometry) {
                                    console.log("Returned place contains no geometry");
                                    return;
                                }

                                marker.setMap(null);
                                marker = new google.maps.Marker({
                                    position: place.geometry.location,
                                    map: map
                                });
                                fieldEl.find('input.map-position').val(place.geometry.location);
                                if (place.geometry.viewport) {
                                    bounds.union(place.geometry.viewport);
                                } else {
                                    bounds.extend(place.geometry.location);
                                }
                            });
                            map.fitBounds(bounds);
                            fieldEl.find('input.map-zoom').val(map.zoom);
                        });

                        google.maps.event.addListener(map, 'click', function (event) {
                            marker.setMap(null);
                            marker = new google.maps.Marker({
                                position: event.latLng,
                                map: map
                            });
                            geocoder.geocode({'latLng': event.latLng}, function (results, status) {
                                var address = 'Unknown';

                                // eslint-disable-next-line eqeqeq
                                if (status == google.maps.GeocoderStatus.OK) {
                                    if (results[0]) {
                                        address = results[0].formatted_address;
                                    }
                                }
                                fieldEl.find('input.map-search').val(address);
                            });
                            fieldEl.find('input.map-position').val(event.latLng);
                        });
                    });
                },
                renderCheckboxOptionsView: function (data) {
                    var count = 0,
                        html  = '',
                        type  = data.type,
                        countPerRow;

                    data.row_count = +data.row_count || 1;
                    countPerRow    = +Math.ceil(_.keys(data.options).length / data.row_count);
                    countPerRow    = +countPerRow || 1;
                    _.each(data.options, function (option, index) {
                        var checked = '';

                        if (count % countPerRow === 0) {
                            html += '<div>';
                        }
                        if (type === 'checkbox') {
                            if (option.checked) {
                                checked = 'checked';
                            }
                        } else {
                            checked = data.checked === option.value ? 'checked' : '';
                        }
                        html +=
                            '<label>' +
                            '   <input data-id="' + index + '" name="' + data.parentName + '[' + data._id + ']' + (type === 'checkbox' ? '[options][' + index + ']' : '') + '[checked]" type="' + type + '" ' + checked + (type === 'radio' ? ' value="' + option.value + '"' : '') + '>' +
                            '   <span>' + option.label + '</span>' +
                            '</label>';
                        count++;
                        if (count % countPerRow === 0 || count === _.keys(data.options).length) {
                            html += '</div>';
                        }
                    });
                    return html;
                }
            }
        );
        return $.mageplaza.customForm;
    }
);
