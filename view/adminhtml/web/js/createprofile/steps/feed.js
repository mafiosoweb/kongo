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
            feedsByLink: [],
            feedList: [],
            feedTypeSectionName: "",            
            feedLink: "",
            storeId: "",
            isFeedChecked: "",
            feedCode: "",
            createProfileUrl: "",
            manualsByCode: '',
            channelManual: '',            
            manuals: [],
        },
        
        initObservable: function () 
        {            
        	this._super().observe('feedTypeSectionName feedLink storeId feedCode isFeedChecked channelManual');
        	this.feedList = ko.observableArray([]);
        	
        	this.isFeedChecked = ko.observable(false);  
        	
            return this;
        },          
        
        nextLabelText: $.mage.__('Create Export Profile'),
        variations: [],

        render: function (wizard) {
        	
        	self = this; 
        	
            this.wizard = wizard;
            this.feedLink(wizard.data.feedLink());
            this.storeId(wizard.data.storeId());
            this.feedTypeSectionName(wizard.data.feedLink()+" "+$.mage.__('Feed Type'));            
            this.feedList(this.feedsByLink[wizard.data.feedLink()]);    
            this.feedCode(this.feedsByLink[wizard.data.feedLink()][0].code);
            
            this.isFeedChecked.subscribe( this.updateFeed);
            this.isFeedChecked( this.feedCode());
        },
        force: function (wizard) {
        	
        	// redirect to create profile
        	window.location=  this.createProfileUrl+'?feed_code='+this.feedCode()+'&store_id='+this.storeId();
        	
        	// ajax try
        	//this.requestNewProfile();
        	
        },
        back: function () {
        },
        
        updateFeed: function( value) { 
        	
        	// bind feedCode
        	self.feedCode(value);        	
        	
        	if( !self.manuals[ value]) {
	        	$.get( self.manualsByCode[ value], function(data) { 
	        	    // Now use this data to update your view models, 
	        	    // and Knockout will update your UI automatically
	        		
	        		self.manuals[ self.feedCode()] = data;        		
	        		self.channelManual( data);
	        	});
        	} else {
        		self.channelManual( self.manuals[ self.feedCode()]);
        	}
        	
        	return this;
        },
        
        requestNewProfile: function () {
            $.ajax({
                type: 'POST',
                url: this.createProfileUrl,
                data: {
                    feed_code: this.feedCode(),
                    store_id: this.storeId()
                },
                showLoader: true
            }).done(function (message){
            	//this.notificationMessage.text = message.result;
            	$('[data-role=step-wizard-dialog]').trigger('closeModal');
            	location.reload(); 
            	
            	//$('[data-role=grid-wrapper]').trigger('reload');
            	
            }.bind(this));
        },
    });
});
