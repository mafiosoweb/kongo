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
    'Magento_Ui/js/modal/alert'
], function (Component, $, ko, magAlert) {
    'use strict';    
    
    var self;

    return Component.extend({
    	
    	defaults: {
            items: [],
            loadUrl: "",
            path: "Not adjusted yet",
            feedfile: "",
        },
        
        initObservable: function () 
        {   
        	self = this;
        	
        	this._super().observe('path');
        	
        	this.items = ko.observableArray([]);        	   
        	
        	$('#test_connection').click( function() {
        		self.testFtpConnection();
        	});
        	
        	$('#protocol').change( function() {
        		self.changeFtpProtocol( this);
        	});
        	
        	this.load();
        	
            return this;
        }, 
        
        applyData: function( data) {
        	
        	self.path( data.path);
			
			self.items.removeAll();
			
    		for (var i = 0; i < data.list.length; i++)        	
        	{    
    			data.list[i]['feedfile'] = ( data.list[i].name == this.feedfile);
    			
        		self.items.push( data.list[i]);
        	}
    		
    		$( '#ftp_client_table').show();
        },
        
        
        
        load: function( index) {        	
        	        	
        	var postData = this.getFormData();
        	if( postData['feed[ftp][hostname]'] == "" || postData['feed[ftp][username]'] == "" || postData['feed[ftp][password]'] == "") {
        		return false;
        	}
        	
        	if( index != undefined) {
        		var row = self.items()[ index];
        		
        		// file is downloaded
        		if( row.type == 'file') {
        			self.download( row);
        			return true;
        		}
        		
        		if( row.path) {
        			postData.path = row.path;
        		} else {
        			postData.path = self.path() + '/' + row.name;
        		}        		
        	}        	
        	
        	$.ajax({
            	method: 'get',
        	    url: self.loadUrl,
        	    data: postData,
        	    showLoader: true, // enable loader
        	    dataType: 'json'
        	}).done(function( data ) {
        		if( data.error) {
        			magAlert( {title: "FTP Client Error",content: data.message});
        		} else {        			
        			
        			self.applyData( data);
        		}        	        		
    	    }).fail( function( jqXHR, textStatus, errorThrown) {
    	    	magAlert( {title: "FTP Client Error",content: errorThrown});
    	    });
        },
        download: function( row) {
        	
        	var filename = self.path() + '/' + row.name;
        	var url = self.loadUrl + '?file=' + filename;
        	
        	var win = window.open( url, '_blank');
      	  	win.focus();
        },
        
        testFtpConnection: function() {
        	
        	var postData = this.getFormData();
        	postData.test = true;
    		
        	$.ajax({
            	method: 'post',
        	    url: self.loadUrl,
        	    data: postData,
        	    showLoader: true
        	}).done(function( data ) {
        		if( data.error) {
        			magAlert({
        	            title: "FTP Connection Error",
        	            content: data.message,
        	        });
        		} else {    			
        			magAlert({
        	            title: "FTP Connection Test",
        	            content: data.message,
        	        });
        			
        			self.applyData( data);        			
        		}
    	    }).fail( function( jqXHR, textStatus, errorThrown) {
    	    	magAlert( {title: "FTP Client Error",content: errorThrown});
    	    });
    	},
    	
    	changeFtpProtocol:  function( select) {
    		if( $(select).val() == 'SFTP') {
    			$("#port").val(22);
    			$('.field-passive_mode').hide();
    		} else {
    			$("#port").val(21);
    			$('.field-passive_mode').show();
    		}
    	},
    	
    	getFormData: function() {
        	
        	return this.serializeObject($("[name^='feed[ftp]']"));
        },
    	
    	serializeObject: function( form) {
	    	var paramObj = {};
	    	$.each( form.serializeArray(), function(_, kv) {
	    	  if (paramObj.hasOwnProperty(kv.name)) {
	    	    paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
	    	    paramObj[kv.name].push(kv.value);
	    	  }
	    	  else {
	    	    paramObj[kv.name] = kv.value;
	    	  }
	    	});
	    	
	    	return paramObj;
    	}
        
    });        
});
