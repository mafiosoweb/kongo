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
 */
// jscs:disable jsDoc
define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'mage/translate'
], function (Component, $, ko, _) {
    'use strict';
    
    var self;

    return Component.extend({
        defaults: {
             notificationMessage: {
                text: null,
                error: null
            },                        
            feedLink: '',
            storeId: '',
            isChecked: '',
            channelsByLink: '',
            channel: '',
            channels: [],
            stores: []
        },
                
        updateChannel: function( value) {        	        	        	
        	
        	// bind feedLink
        	self.feedLink(value);
        	
        	//channel list not loaded
        	if(!self.channelsByLink)
        		return this;
        	
        	if( !self.channels[ value]) {
	        	$.get( self.channelsByLink[ value].description, function(data) { 
	        	    // Now use this data to update your view models, 
	        	    // and Knockout will update your UI automatically
	        		
	        		self.channels[ self.feedLink()] = self.channelsByLink[ value];
	        		self.channels[ self.feedLink()].description = data;
	        		self.channels[ self.feedLink()].link = value;
	        		self.channel( self.channels[ self.feedLink()]);
	        	});
        	} else {
        		self.channel( self.channels[ self.feedLink()]);
        	}
        	
        	return this;
        },
        
        initObservable: function () {
        	this._super().observe('feedLink storeId isChecked channel'); 
        	
        	this.isChecked = ko.observable(false);        	
        	
            return this;
        },
        
        initialize: function() {
        	this._super();
        	
        	self = this;
        	
        	this.isChecked.subscribe( this.updateChannel);             	
        	
        	this.isChecked( this.feedLink());
        },
        
        nextLabelText: $.mage.__('Next'),
        variations: [],

        render: function (wizard) {
            this.wizard = wizard;
            //this.sections(wizard.data.sections());
            //this.attributes(wizard.data.attributes());      
        },
        force: function (wizard) 
        {              	
        	wizard.data.feedLink = this.feedLink;
        	wizard.data.storeId = this.storeId;
        //    this.variationsComponent().render(this.variations, this.attributes());
        //    $('[data-role=step-wizard-dialog]').trigger('closeModal');
        },
        back: function () {
        }        
    });
});
