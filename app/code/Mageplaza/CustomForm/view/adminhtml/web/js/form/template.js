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

"use strict";
define([], function() {

    return {
        'feedback': {
            data: {
                "1556175573875_875": {
                    "_id": "1556175573875_875",
                    "default": "1",
                    "title": "Feedback",
                    "description": "",
                    "sm_button_text": "Send Feedback",
                    "field_groups": {
                        "1556175573875_875": {
                            "parentName": "form[page][1556175573875_875][field_groups]",
                            "_id": "1556175573875_875",
                            "default": "1",
                            "title": "",
                            "description": "",
                            "fields": {
                                "1556175614657_657": {
                                    "type": "text",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556175614657_657",
                                    "title": "Your Name",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1556175615008_8": {
                                    "type": "text",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556175615008_8",
                                    "title": "Email",
                                    "tooltip": "",
                                    "validate_class": "validate-email",
                                    "width": "100"
                                },
                                "1556175639906_906": {
                                    "type": "textarea",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556175639906_906",
                                    "title": "Your Review",
                                    "tooltip": "Tips and Guidelines",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1556175742459_459": {
                                    "type": "radio",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556175742459_459",
                                    "title": "Overall Experience",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "row_count": "1",
                                    "options": {
                                        "1556175742459_4591": {"label": "Very Good", "value": "a"},
                                        "1556175742459_4592": {"label": "Good", "value": "b"},
                                        "1556175742459_4593": {"label": "Fair", "value": "c"},
                                        "1556175763593_593": {"label": "Poor", "value": "d"}
                                    },
                                    "checked": "a"
                                },
                                "1556175793373_373": {
                                    "type": "radio",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556175793373_373",
                                    "title": "Timely Response",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "row_count": "1",
                                    "options": {
                                        "1556175742459_4591": {"label": "Very Good", "value": "a"},
                                        "1556175742459_4592": {"label": "Good", "value": "b"},
                                        "1556175742459_4593": {"label": "Fair", "value": "c"},
                                        "1556175763593_593": {"label": "Poor", "value": "d"}
                                    },
                                    "checked": "a"
                                },
                                "1556175801775_775": {
                                    "type": "radio",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556175801775_775",
                                    "title": "Our Support",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "row_count": "1",
                                    "options": {
                                        "1556175742459_4591": {"label": "Very Good", "value": "a"},
                                        "1556175742459_4592": {"label": "Good", "value": "b"},
                                        "1556175742459_4593": {"label": "Fair", "value": "c"},
                                        "1556175763593_593": {"label": "Poor", "value": "d"}
                                    },
                                    "checked": "a"
                                },
                                "1556176476697_697": {
                                    "type": "radio",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556176476697_697",
                                    "title": "Overall Satisfaction",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "row_count": "1",
                                    "options": {
                                        "1556175742459_4591": {"label": "Very Good", "value": "a"},
                                        "1556175742459_4592": {"label": "Good", "value": "b"},
                                        "1556175742459_4593": {"label": "Fair", "value": "c"},
                                        "1556175763593_593": {"label": "Poor", "value": "d"}
                                    },
                                    "checked": "a"
                                },
                                "1556175830872_872": {
                                    "type": "rating",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556175830872_872",
                                    "title": "Want to rate with us for customer services?",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "number_star": "5",
                                    "default": "2"
                                },
                                "1556175880896_896": {
                                    "type": "textarea",
                                    "parentName": "form[page][1556175573875_875][field_groups][1556175573875_875][fields]",
                                    "_id": "1556175880896_896",
                                    "title": "Is there anything you would like to tell us?",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100"
                                }
                            }
                        }
                    }
                }
            },
            style: ''
        },
        'contact-form': {
            data: {
                "1558926082737_7370": {
                    "_id": "1558926082737_7370",
                    "default": "1",
                    "title": "Contact Us",
                    "description": "<div class=\"widget block block-static-block\">\r\n<div class=\"contact-info cms-content\">\r\n<p class=\"cms-content-important\">We love hearing from you, our Luma customers. Please contact us about anything at all. Your latest passion, unique health experience or request for a specific product. We&rsquo;ll do everything we can to make your Luma experience unforgettable every time. Reach us however you like<\/p>\r\n<div class=\"block block-contact-info\">\r\n<div class=\"block-title\"><strong>Contact Us Info<\/strong><\/div>\r\n<div class=\"block-content\">\r\n<div class=\"box box-phone\"><strong class=\"box-title\"> <span>Phone<\/span> <\/strong>\r\n<div class=\"box-content\"><span class=\"contact-info-number\">1-800-403-8838<\/span>\r\n<p>Call the Luma Helpline for concerns, product questions, or anything else. We&rsquo;re here for you 24 hours a day - 365 days a year.<\/p>\r\n<\/div>\r\n<\/div>\r\n<div class=\"box box-design-inquiries\"><strong class=\"box-title\"> <span>Apparel Design Inquiries<\/span> <\/strong>\r\n<div class=\"box-content\">\r\n<p>Are you an independent clothing designer? Feature your products on the Luma website! Please direct all inquiries via email to: <a href=\"mailto:cs@luma.com\">cs@luma.com<\/a><\/p>\r\n<\/div>\r\n<\/div>\r\n<div class=\"box box-press-inquiries\"><strong class=\"box-title\"> <span>Press Inquiries<\/span> <\/strong>\r\n<div class=\"box-content\">\r\n<p>Please direct all media inquiries via email to: <a href=\"mailto:pr@luma.com\">pr@luma.com<\/a><\/p>\r\n<\/div>\r\n<\/div>\r\n<\/div>\r\n<\/div>\r\n<\/div>\r\n<\/div>",
                    "sm_button_text": "Submit",
                    "field_groups": {
                        "1556175573875_875": {
                            "parentName": "form[page][1558926082737_7370][field_groups]",
                            "_id": "1556175573875_875",
                            "default": "1",
                            "title": "",
                            "description": "",
                            "fields": {
                                "1558928123755_755": {
                                    "type": "map",
                                    "parentName": "form[page][1558926082737_7370][field_groups][1556175573875_875][fields]",
                                    "_id": "1558928123755_755",
                                    "title": "Google Map Field",
                                    "tooltip": "",
                                    "notice": "",
                                    "additional_class": "contact-map",
                                    "validate_class": "",
                                    "width": "100",
                                    "zoom": "",
                                    "position": "",
                                    "address": ""
                                }
                            }
                        }, "1558928575273_273": {
                            "parentName": "form[page][1558926082737_7370][field_groups]",
                            "_id": "1558928575273_273",
                            "default": "0",
                            "title": "Write Us",
                            "description": "Jot us a note and we\u2019ll get back to you as quickly as possible.",
                            "fields": {
                                "1558926387645_645": {
                                    "type": "text",
                                    "parentName": "form[page][1558926082737_7370][field_groups][1558928575273_273][fields]",
                                    "_id": "1558926387645_645",
                                    "title": "Name",
                                    "tooltip": "",
                                    "notice": "",
                                    "additional_class": "contact-field",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1558926389781_781": {
                                    "type": "text",
                                    "parentName": "form[page][1558926082737_7370][field_groups][1558928575273_273][fields]",
                                    "_id": "1558926389781_781",
                                    "title": "Email",
                                    "tooltip": "",
                                    "notice": "",
                                    "additional_class": "contact-field",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1558926389935_935": {
                                    "type": "text",
                                    "parentName": "form[page][1558926082737_7370][field_groups][1558928575273_273][fields]",
                                    "_id": "1558926389935_935",
                                    "title": "Phone Number",
                                    "tooltip": "",
                                    "notice": "",
                                    "additional_class": "contact-field",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1558926445801_801": {
                                    "type": "textarea",
                                    "parentName": "form[page][1558926082737_7370][field_groups][1558928575273_273][fields]",
                                    "_id": "1558926445801_801",
                                    "title": "What\u2019s on your mind?",
                                    "tooltip": "",
                                    "notice": "",
                                    "additional_class": "contact-field",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "100"
                                }
                            }
                        }
                    }
                }
            },
            style: '.mp-custom-form-popup .mp-1column.contact-map{width:72%;margin-right:3%;min-height: 500px;} .mp-custom-form-popup .mp-1column.contact-field{width:25%} .page-1558926082737_7370 .mp-field-group-title {margin: 0 0 20px;padding: 0 0 10px;width: 100%;box-sizing: border-box;font-weight: 300;line-height: 1.2;font-size: 1.8rem;} .page-1558926082737_7370 .step-title.mp-page-title {border-bottom: unset;font-size: 40px;margin-bottom: 40px;}'
        },
        'pre-order': {
            data: {
                "1556185698519_5190": {
                    "_id": "1556185698519_5190",
                    "default": "1",
                    "title": "Mageplaza",
                    "description": "<p>Please give us more your information about your concerned product.<\/p>",
                    "sm_button_text": "Submit",
                    "field_groups": {
                        "1556175573875_875": {
                            "parentName": "form[page][1556185698519_5190][field_groups]",
                            "_id": "1556175573875_875",
                            "default": "1",
                            "title": "",
                            "description": "",
                            "fields": {
                                "1556185863305_305": {
                                    "type": "text",
                                    "parentName": "form[page][1556185698519_5190][field_groups][1556175573875_875][fields]",
                                    "_id": "1556185863305_305",
                                    "title": "Which one do you want",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1556185891909_909": {
                                    "type": "text",
                                    "parentName": "form[page][1556185698519_5190][field_groups][1556175573875_875][fields]",
                                    "_id": "1556185891909_909",
                                    "title": "Size",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "50"
                                },
                                "1556185910707_707": {
                                    "type": "text",
                                    "parentName": "form[page][1556185698519_5190][field_groups][1556175573875_875][fields]",
                                    "_id": "1556185910707_707",
                                    "title": "Color",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "50"
                                }
                            }
                        },
                        "1556185928900_900": {
                            "parentName": "form[page][1556185698519_5190][field_groups]",
                            "_id": "1556185928900_900",
                            "default": "",
                            "title": "",
                            "description": "",
                            "fields": {
                                "1556185951232_232": {
                                    "type": "text",
                                    "parentName": "form[page][1556185698519_5190][field_groups][1556185928900_900][fields]",
                                    "_id": "1556185951232_232",
                                    "title": "Firstname",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "50"
                                },
                                "1556185975576_576": {
                                    "type": "text",
                                    "parentName": "form[page][1556185698519_5190][field_groups][1556185928900_900][fields]",
                                    "_id": "1556185975576_576",
                                    "title": "Email",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "50"
                                },
                                "1556185976944_944": {
                                    "type": "text",
                                    "parentName": "form[page][1556185698519_5190][field_groups][1556185928900_900][fields]",
                                    "_id": "1556185976944_944",
                                    "title": "Lastname",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "50"
                                }
                            }
                        }
                    }
                }
            },
            style: '.mp-custom-form .actions-toolbar {clear: both;} button.button.action.continue.primary {margin-top: 20px;}.field.mp-2column-left. {margin-bottom: 20px;}li.mp-custom-form.field-group {border-top: 1px solid #ccc;clear: both;}.step-title.mp-page-title {color: white;background-color: #3599e5;}'
        },
        'survey-form': {
            data: {
                "1557227417280_280": {
                    "_id": "1557227417280_280",
                    "default": "1",
                    "title": "General Information",
                    "description": "",
                    "sm_button_text": "Next",
                    "field_groups": {
                        "1557227417280_280": {
                            "parentName": "form[page][1557227417280_280][field_groups]",
                            "_id": "1557227417280_280",
                            "default": "1",
                            "title": "",
                            "description": "",
                            "fields": {
                                "1557228365668_668": {
                                    "type": "text",
                                    "parentName": "form[page][1557227417280_280][field_groups][1557227417280_280][fields]",
                                    "_id": "1557228365668_668",
                                    "title": "Your Name",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557228374984_984": {
                                    "type": "text",
                                    "parentName": "form[page][1557227417280_280][field_groups][1557227417280_280][fields]",
                                    "_id": "1557228374984_984",
                                    "title": "Your Email",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557228387046_46": {
                                    "type": "text",
                                    "parentName": "form[page][1557227417280_280][field_groups][1557227417280_280][fields]",
                                    "_id": "1557228387046_46",
                                    "title": "Your Phone",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557228398565_565": {
                                    "type": "radio",
                                    "parentName": "form[page][1557227417280_280][field_groups][1557227417280_280][fields]",
                                    "_id": "1557228398565_565",
                                    "title": "Your Gender",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "row_count": "2",
                                    "options": {
                                        "1557228398565_5651": {"label": "Male", "value": "a"},
                                        "1557228398565_5652": {"label": "Female", "value": "b"}
                                    }
                                },
                                "1557228429958_958": {
                                    "type": "radio",
                                    "parentName": "form[page][1557227417280_280][field_groups][1557227417280_280][fields]",
                                    "_id": "1557228429958_958",
                                    "title": "Your Age Group",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "row_count": "5",
                                    "options": {
                                        "1557228398565_5651": {
                                            "label": "Under 18 years old",
                                            "value": "a"
                                        },
                                        "1557228398565_5652": {
                                            "label": "19-25 years old",
                                            "value": "b"
                                        },
                                        "1557228442339_339": {"label": "26-35 years old", "value": "c"},
                                        "1557228446217_217": {"label": "36-45 years old", "value": "d"},
                                        "1557228449314_314": {
                                            "label": "Over 46 years old",
                                            "value": "e"
                                        }
                                    }
                                }
                            }
                        }
                    }
                }, "1557228466174_174": {
                    "_id": "1557228466174_174",
                    "default": "",
                    "title": "Shopping Experience",
                    "description": "",
                    "sm_button_text": "Submit",
                    "field_groups": {
                        "1557228466174_174": {
                            "parentName": "form[page][1557228466174_174][field_groups]",
                            "_id": "1557228466174_174",
                            "default": "1",
                            "title": "",
                            "description": "",
                            "fields": {
                                "1557228486582_582": {
                                    "type": "radio",
                                    "parentName": "form[page][1557228466174_174][field_groups][1557228466174_174][fields]",
                                    "_id": "1557228486582_582",
                                    "title": "Have you ever ordered products at our store?",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "row_count": "2",
                                    "options": {
                                        "1557228486582_5821": {"label": "Yes", "value": "a"},
                                        "1557228486582_5822": {"label": "No", "value": "b"}
                                    }
                                },
                                "1557228504311_311": {
                                    "type": "grid",
                                    "parentName": "form[page][1557228466174_174][field_groups][1557228466174_174][fields]",
                                    "_id": "1557228504311_311",
                                    "title": "If Yes, how satisfied are you with",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "depends": {
                                        "1557228792674_674": {
                                            "field": "1557228466174_174-1557228486582_582",
                                            "value": "1557228486582_5821"
                                        }
                                    },
                                    "rows": {
                                        "1557228504311_3111": "The Product",
                                        "1557228504311_3112": "The Support from our staff",
                                        "1557228504311_3113": "The Shipping Service",
                                        "1557228756508_508": "Overall"
                                    },
                                    "columns": {
                                        "1557228504311_3111": "Very Satisfied",
                                        "1557228504311_3112": "Satisfied",
                                        "1557228504311_3113": "Normal",
                                        "1557228761792_792": "Unsatisfied ",
                                        "1557228775172_172": "Very Unsatisfied"
                                    },
                                    "select_type": "radio"
                                },
                                "1557228820035_35": {
                                    "type": "checkbox",
                                    "parentName": "form[page][1557228466174_174][field_groups][1557228466174_174][fields]",
                                    "_id": "1557228820035_35",
                                    "title": "How likely are you to",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "depends": {
                                        "1557228836248_248": {
                                            "field": "1557228466174_174-1557228486582_582",
                                            "value": "1557228486582_5821"
                                        }
                                    },
                                    "row_count": "3",
                                    "options": {
                                        "1557228820035_351": {
                                            "label": "Buy Products from our store",
                                            "value": "a"
                                        },
                                        "1557228820035_352": {
                                            "label": "Recommend our products to your relatives and friends",
                                            "value": "b"
                                        },
                                        "1557228820035_353": {
                                            "label": "Recommend our store to your relatives and friends",
                                            "value": "c"
                                        }
                                    }
                                },
                                "1557228914514_514": {
                                    "type": "checkbox",
                                    "parentName": "form[page][1557228466174_174][field_groups][1557228466174_174][fields]",
                                    "_id": "1557228914514_514",
                                    "title": "If No, the factors that you often consider when shopping online are",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "depends": {
                                        "1557228919102_102": {
                                            "field": "1557228466174_174-1557228486582_582",
                                            "value": "1557228486582_5822"
                                        }
                                    },
                                    "row_count": "5",
                                    "options": {
                                        "1557228914514_5141": {
                                            "label": "Products have clear origin",
                                            "value": "a"
                                        },
                                        "1557228914514_5142": {
                                            "label": "Clear product information (Products have images, videos, description)",
                                            "value": "b"
                                        },
                                        "1557228914514_5143": {
                                            "label": "Review from other purchasers",
                                            "value": "c"
                                        },
                                        "1557228938318_318": {
                                            "label": "Support Service before and after purchase",
                                            "value": "d"
                                        },
                                        "1557228942935_935": {"label": "Sale Promotion", "value": "e"}
                                    }
                                },
                                "1557228962791_791": {
                                    "type": "textarea",
                                    "parentName": "form[page][1557228466174_174][field_groups][1557228466174_174][fields]",
                                    "_id": "1557228962791_791",
                                    "title": "Additional Suggestions",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557229028913_913": {
                                    "type": "radio",
                                    "parentName": "form[page][1557228466174_174][field_groups][1557228466174_174][fields]",
                                    "_id": "1557229028913_913",
                                    "title": "Receive notifications when there is a promotion",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100",
                                    "row_count": "4",
                                    "options": {
                                        "1557229028913_9131": {
                                            "label": "No, I don't want to receive notification",
                                            "value": "a"
                                        },
                                        "1557229028913_9132": {"label": "Via emails", "value": "b"},
                                        "1557229028913_9133": {
                                            "label": "Via SMS messages",
                                            "value": "c"
                                        },
                                        "1557229054277_277": {
                                            "label": "See on promo banners on website's homepage",
                                            "value": "d"
                                        }
                                    },
                                    "checked": "d"
                                }
                            }
                        }
                    }
                }
            },
            style: ''
        },
        'customer-order-request': {
            data: {
                "1557229583914_9140": {
                    "_id": "1557229583914_9140",
                    "default": "1",
                    "title": "Customer's Order Request",
                    "description": "",
                    "sm_button_text": "Submit",
                    "field_groups": {
                        "1556175573875_875": {
                            "parentName": "form[page][1557229583914_9140][field_groups]",
                            "_id": "1556175573875_875",
                            "default": "1",
                            "title": "",
                            "description": "",
                            "fields": {
                                "1557229760409_409": {
                                    "type": "text",
                                    "parentName": "form[page][1557229583914_9140][field_groups][1556175573875_875][fields]",
                                    "_id": "1557229760409_409",
                                    "title": "Your Name",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557229782729_729": {
                                    "type": "text",
                                    "parentName": "form[page][1557229583914_9140][field_groups][1556175573875_875][fields]",
                                    "_id": "1557229782729_729",
                                    "title": "Your Email",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557229783289_289": {
                                    "type": "text",
                                    "parentName": "form[page][1557229583914_9140][field_groups][1556175573875_875][fields]",
                                    "_id": "1557229783289_289",
                                    "title": "Your Phone",
                                    "tooltip": "",
                                    "is_required": "on",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557229784409_409": {
                                    "type": "text",
                                    "parentName": "form[page][1557229583914_9140][field_groups][1556175573875_875][fields]",
                                    "_id": "1557229784409_409",
                                    "title": "Your Order Request",
                                    "tooltip": "What do you want us to make for you?",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557229789163_163": {
                                    "type": "textarea",
                                    "parentName": "form[page][1557229583914_9140][field_groups][1556175573875_875][fields]",
                                    "_id": "1557229789163_163",
                                    "title": "Request Details",
                                    "tooltip": "Describe the details of your expectation for the ordered products.",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557229793636_636": {
                                    "type": "text",
                                    "parentName": "form[page][1557229583914_9140][field_groups][1556175573875_875][fields]",
                                    "_id": "1557229793636_636",
                                    "title": "Additional Fee for Customer",
                                    "tooltip": " The fee you are willing to pay more for this request.",
                                    "validate_class": "",
                                    "width": "100"
                                },
                                "1557229801506_506": {
                                    "type": "datetime",
                                    "parentName": "form[page][1557229583914_9140][field_groups][1556175573875_875][fields]",
                                    "_id": "1557229801506_506",
                                    "title": "Delivery Time",
                                    "tooltip": "",
                                    "notice": "Please note that we need about 2-3 days to customize the products.",
                                    "validate_class": "",
                                    "width": "100",
                                    "dateTimeType": "datetime-local"
                                },
                                "1557229803875_875": {
                                    "type": "text",
                                    "parentName": "form[page][1557229583914_9140][field_groups][1556175573875_875][fields]",
                                    "_id": "1557229803875_875",
                                    "title": "Shipping Address",
                                    "tooltip": "",
                                    "validate_class": "",
                                    "width": "100"
                                }
                            }
                        }
                    }
                }
            },
            style: ''
        }
    }
});