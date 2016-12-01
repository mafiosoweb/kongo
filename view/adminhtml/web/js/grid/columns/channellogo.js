
/**
* Magento Module developed by NoStress Commerce
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to info@nostresscommerce.cz so we can send you a copy immediately.
*
* @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)
*
**/

define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template',
    //'text!Nostress_Koongo/templates/grid/cells/channellogo/preview.html',
    'text!Magento_Ui/templates/grid/cells/thumbnail/preview.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, thumbnailPreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/thumbnail',
        	//bodyTmpl: 'Nostress_Koongo/templates/grid/cells/channellogo', //nefunguje
            fieldClass: {
                //'data-grid-channellogo-cell': true  //tohle funguje - pridat do valstniho css
                'data-grid-thumbnail-cell': true
            }
        },
        getSrc: function (row) {
            return row[this.index + '_src']
        },
        getOrigSrc: function (row) {
            return row[this.index + '_orig_src'];
        },
        getLink: function (row) {
            return row[this.index + '_link'];
        },
        getAlt: function (row) {
            return row[this.index + '_alt']
        },
        isPreviewAvailable: function() {
            return this.has_preview || false;
        },
        preview: function (row) {
            var modalHtml = mageTemplate(
                thumbnailPreviewTemplate,
                {
                    src: this.getOrigSrc(row), alt: this.getAlt(row), link: this.getLink(row),
                    linkText: ''//$.mage.__('Go to Details Page')
                }
            );
            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                title: this.getAlt(row),
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []}).trigger('openModal');
        },
        getFieldHandler: function (row) {
            if (this.isPreviewAvailable()) {
                return this.preview.bind(this, row);
            }
        }
    });
});
