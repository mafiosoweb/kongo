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

    return Component.extend({
    	
    	defaults: {
            attributes: [],
            attributeSource: [],                      
            currentAttributeIndex: 0,
            composedValueSelection: "",
        },
        
        initObservable: function () 
        {            
        	this._super().observe('currentAttributeIndex composedValueSelection');        	        	
        	
        	//this.attributes = ko.observableArray(this.attributeSource);        	
        	this.attributes = ko.observableArray([]);
        	for (var i = 0; i < this.attributeSource.length; i++)        	
        	{    
        		this.attributes.push(new AttributeItem(this.attributeSource[i].label, this.attributeSource[i].code, this.attributeSource[i].limit, this.attributeSource[i].postproc, this.attributeSource[i].description, this.attributeSource[i].magento, this.attributeSource[i].constant, this.attributeSource[i].eppav, this.attributeSource[i].convert, this.attributeSource[i].composed_value));
        	}          	
            return this;
        },        
        
        openSettings: function (attributeIndex) 
        {          
        	this.currentAttributeIndex(attributeIndex);        	
        	$('[data-role=attribute-settings-dialog]').trigger('openModal');
        	$('[data-role=modal] .settings-title-suffix').html( this.attributes()[ this.currentAttributeIndex()].label());
        },
        close: function () 
        {        	
            $('[data-role=attribute-settings-dialog]').trigger('closeModal');
        },                
        
        openInfo: function (attributeIndex) 
        {      
        	this.currentAttributeIndex(attributeIndex);        	
        	$('[data-role=attribute-info-dialog]').trigger('openModal');           
        },
        closeInfo: function () {        	
            $('[data-role=attribute-info-dialog]').trigger('closeModal');
        },
        
        openCustomAttributeSettings: function (attributeIndex) 
        {          
        	this.currentAttributeIndex(attributeIndex);
        	$('[data-role=custom-attribute-settings-dialog]').trigger('openModal');
        	$('[data-role=modal] .settings-title-suffix').html( this.attributes()[ this.currentAttributeIndex()].label());
        },
        closeCustomAttributeSettings: function () 
        {        	
            $('[data-role=custom-attribute-settings-dialog]').trigger('closeModal');
        },
        
        
        addConvertRow: function() {
        	this.attributes()[this.currentAttributeIndex()].convert.push(new ConvertItem('',''));            
        },
          
        removeConvertRow: function(row, self) 
        {        	
        	self.attributes()[self.currentAttributeIndex()].convert.remove(row); 
        },  
        
        addSelectionToComposedValue: function()
        {
        	if(!this.composedValueSelection())
        		return;
        	
        	var value = '';
        	if(this.attributes()[this.currentAttributeIndex()].composed_value())
        		value = this.attributes()[this.currentAttributeIndex()].composed_value();
        	
        	var suffix = this.composedValueSelection();
        	var isMacro = true;
        	if(suffix.indexOf("[") === -1 && suffix.indexOf("{") === -1)
        	{	
        		suffix = '{{' + suffix + '}}';
        		isMacro = false;        	
        	}
        	
        	var suffixAdded = false;
        	if(value.length >= 4 && !isMacro)
        	{
        		var endOfString = value.substring(value.length - 2,value.length);        		        		
        		if(endOfString == ']]')
        		{
        			suffix = suffix + endOfString;
        			value = value.slice(0, -2) + ' ' + suffix;
        			suffixAdded = true;
        		}
        	}
        	
        	if(!suffixAdded)
        	{	
        		if(value)
        			value = value + ' ';
        		value = value + suffix;
        	
        	}
        	
        	this.attributes()[this.currentAttributeIndex()].composed_value(value);
        },
        
        addCustomAttributeRow: function() 
        {
        	this.attributes.push(new AttributeItem('custom_attribute'+(this.attributes().length+1),'','',[],'','','','',[],''));    
        },
          
        removeCustomAttributeRow: function(row, self) 
        {        	
        	self.attributes.remove(row); 
        }, 
    });
    
    function AttributeItem(label, code, limit, postproc, description, magento, constant, eppav,convert,composed_value) {
        var self = this;
        self.label = ko.observable(label);
        self.code = ko.observable(code);
        self.limit = ko.observable(limit);        
        self.postproc = ko.observableArray(postproc);
              
        self.convert = ko.observableArray([]);
        if(convert)
        {	
        	for (var i = 0; i < convert.length; i++)        	
        	{    
        		self.convert.push(new ConvertItem(convert[i].from,convert[i].to));
        	}         
        }        
        	
        self.eppav = ko.observable(eppav);
        self.magento = ko.observable(magento);
        self.constant = ko.observable(constant);
        self.composed_value = ko.observable(composed_value);
        self.description = description;   
    };
    
    function ConvertItem(from, to) {
        var self = this;
        self.from = ko.observable(from);
        self.to = ko.observable(to);
    };
});
