
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
        'text!Nostress_Koongo/templates/grid/cells/profile/preview.html',
        'text!Nostress_Koongo/templates/tooltip.html',
        'Magento_Ui/js/modal/modal',
        '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.3.0/highlight.min.js',
        'mage/backend/tabs'
    ], function (Column, $, mageTemplate, previewTemplate, tooltipTemplate) {
        'use strict';
     
        return Column.extend({
            defaults: {
                bodyTmpl: 'Nostress_Koongo/ui/grid/cells/feedpreviewactions',
                fieldClass: {
                    'data-grid-html-cell': true
                }
            },         
            
            isLicenseValid: function( row) {
            	return row['is_license_valid'];
            },
            
            getTitle: function (row) {
            	return this.getRowValue( row, 'title');
            },
            getRowValue: function (row, key) {
                return row[this.index][key];
            },
            
            
            preview: function (row) {     
            	
            	$('.modal-feed-preview .modal-content').remove();
            	
            	var self = this;                 
            	
            	var modalHtml = mageTemplate( previewTemplate, row[ this.index]);
            	
            	var tooltipHtml = row[this.index]['preview_help_url'] ? mageTemplate( tooltipTemplate, { url: row[this.index]['preview_help_url']}) : '';
            	
            	var buttons = [
                      {
                          text: $.mage.__('Back'),
                          class: 'back',
                          click: function () {
                          	this.closeModal();
                          }
                      },
                      {
                          text: $.mage.__('Edit Profile #'+row['entity_id']),
                          class: 'default',
                          click: function () {
                          	location.href = row[ self.index]['edit_general'];
                          }
                      },                                            
                      {
                          text: $.mage.__('Download File'),
                          class: 'primary',
                          click: function () {
                        	  var win = window.open( row[self.index]['download_url'], '_blank');
                        	  win.focus();
                          }
                      }
                ];
            	if( row[self.index]['upload_enabled']) {
            		buttons.push({
        				text: $.mage.__('Upload File via FTP'),
                        class: 'primary',
                        click: function () {
                      	   location.href = row[self.index]['upload_url'];
                        }
            		});
            	}
            	
                var modal = $('<div/>').html( modalHtml);
                modal.modal({
                	type: 'slide',
                    title: this.getTitle(row) + tooltipHtml,
                    innerScroll: true,
                    modalClass: 'no-inner-scroll modal-feed-preview',
                    buttons: buttons 
                });
                
                $('.modal-tabs').tabs({});                       
                
                $.ajax({
                	method: 'get',
            	    url: self.getRowValue( row, 'preview_url'),
            	    showLoader: true, // enable loader
            	}).done(function( data ) {            		
            		
            		if( data.error) {
            			$('.feed-preview .tab-content').html( data.message);
            		} else {
            			$('.feed-preview .tab-content').html( data);
            			$('pre code.html').each(function(i, block) {
         			       hljs.highlightBlock(block);
         			    });
            		}            		

        	    });
                
                $.get( self.getRowValue( row, 'manual_url'), function(data) { 
                	
                	$('.feed-manual .tab-content').html( data);
	        	});
                
                modal.trigger('openModal');
            },
            getFieldHandler: function (row) {
                var result = this.preview.bind(this, row);
                
                // sp is global js var
                if( sp == row['entity_id']) {
                	// sp must be deactived to avoid double opening of preview window 
                	sp = false;
                	this.preview( row);
                }
                
                return result;
            }
        });
    });
