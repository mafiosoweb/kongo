
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
        'text!Nostress_Koongo/templates/grid/cells/profile/message.html',
        'Magento_Ui/js/modal/modal'
    ], function (Column, $, mageTemplate, messagePreviewTemplate) {
        'use strict';
     
        return Column.extend({
            defaults: {
                bodyTmpl: 'ui/grid/cells/html',
                fieldClass: {
                    'data-grid-html-cell': true
                }
            },
            gethtml: function (row) {
                return row[this.index + '_html'];
            },
            getMessage: function (row) {
                return row[this.index + '_message'];
            },
            getStatus: function (row) {
                return row[this.index + '_status'];
            },
            getLabel: function (row) {
                return row[this.index + '_html']
            },
            getTitle: function (row) {
                return row[this.index + '_title']
            },            
            
            preview: function (row) {
                var modalHtml = mageTemplate(
                		messagePreviewTemplate,
                    {
                        html: this.gethtml(row), 
                        title: this.getTitle(row), 
                        label: this.getLabel(row), 
                        status: this.getStatus(row),
                        message: this.getMessage(row)                        
                    }
                );
                var modalHtmlWithMessageHtml = modalHtml.replace('{{message_html_content}}',this.getMessage(row));
                var previewPopup = $('<div/>').html(modalHtmlWithMessageHtml);
                previewPopup.modal({
                    title: this.getTitle(row),
                    innerScroll: true,
                    modalClass: '_image-box',
                    buttons: []}).trigger('openModal');
            },
            getFieldHandler: function (row) {
                return this.preview.bind(this, row);
            }
        });
    });
