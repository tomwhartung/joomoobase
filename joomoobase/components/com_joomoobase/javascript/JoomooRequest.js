/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  JoomooBase
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */

/**
 * JoomooRequest.js: javascript functions used for DIY Ajax
 * --------------------------------------------------------
 * We are do-it-yourselfing because:
 * o  Current version of mootools is 1.2.2 but at this time joomla is using version 1.11
 * o  They changed the name of the Ajax class in 1.11 to Request in 1.2.*
 * o  I can't find documentation for 1.11's Ajax class at mootools.net
 * o  We are not doing anything complex here, just updating a single table
 *
 * This class is based on examples in the book "Javascript: The Definitive Guide" (O'Reilly)
 * We have changed the examples to suit our needs.
 *
 * Examples:
 * ---------
 * Use statements similar to these to create an object of this class and send an HttpRequest:
 *    var url = 'http://example.com/requests/helloWorld.php'
 *    function callback() { ... }       // event handler for successful request
 *    function errorHandler() { ... }   // event handler for request that causes an error
 *    var values = { };
 *    var myRequest;
 *    values.id = 4;
 *    values.name = 'value';
 *    myRequest = new JoomooRequest( url, callback, errorHandler );
 *    request = JoomooRequest.sendPostRequest( url, values, callback, errorHandler );
 */

/**
 * class constructor
 * url: web address to which to send request
 * onSuccess: function to call when request completes successfully
 * onFailure: function to call when an error occurs during request
 * onTimeout: optional function to call when request times out (takes too long)
 *            (specify null to use the default function and override timeoutMilliSecs only)
 * timeoutMilliSecs: optional number of milliseconds to wait before timing out request
 */
function JoomooRequest( url, onSuccess, onFailure, onTimeout, timeoutMilliSecs )
{
	this.url = url;
	this.onSuccess = onSuccess;
	this.onFailure = onFailure;
	this.request = this.getNewRequest();

	onTimeout ? this.onTimeout = onTimeout : this.onTimeout = JoomooRequest.onTimeout;
	timeoutMilliSecs ? this.timeoutMilliSecs = timeoutMilliSecs : this.timeoutMilliSecs = JoomooRequest.timeoutMilliSecs;

	this.setupPostRequest();
}

//
// list of fcns that create what we want on various platforms
//
JoomooRequest._factories = [
	function() { return new XMLHttpRequest(); },
	function() { return new ActiveXObject("Msxml2.XMLHTTP"); },
	function() { return new ActiveXObject("Microsoft.XMLHTTP"); }
];
JoomooRequest._factory = null;      // getNewRequest() sets this to one of the _factories
/**
 * Returns a request object that can be used to contact server
 */
JoomooRequest.prototype.getNewRequest = function()
{
	if ( this._factory != null )
		return this._factory;

	for ( var factNum = 0; factNum < JoomooRequest._factories.length; factNum++ )
	{
		try
		{
			var factory = JoomooRequest._factories[factNum];
			var request = factory();       // throws if factory no workee
			if ( request != null )         // we have a winner!
			{
				this._factory = factory;   // save for future reference
				return request;
			}
		}
		catch( exc )
		{
			continue;
		}
		//
		// No factory found: return fcn to throw an exc
		//
		this._factory = function () { throw new Error('XMLHttpRequest not supported.'); }
		this._factory(); // throws error
	}
}
/**
 * Sets up our request object so it is ready to send a request to the server
 */
JoomooRequest.prototype.setupPostRequest = function( )
{
	this.setEventHandlers( );
	this.request.open( "POST", this.url );        // asynchronous (doesn't block) by default
	this.setHeaders( );
}
/**
 * Sets success and error event handlers for our request
 */
JoomooRequest.prototype.setEventHandlers = function( )
{
	var thisRequest   = this.request;      // needed for use inside of thisRequest.onreadystatechange
	var thisOnSuccess = this.onSuccess;
	var thisOnFailure = this.onFailure;
	var thisOnTimeout = this.onTimeout;
	var timeoutTimer;

	timeoutTimer = setTimeout( function ( ) {
			thisRequest.abort();
			if ( thisOnTimeout )
			{
				thisOnTimeout();
			}
		}, this.timeoutMilliSecs
	);

	if ( this.onSuccess )
	{
		thisRequest.onreadystatechange = function ( ) {
			if ( thisRequest.readyState == 4 )
			{
				if ( timeoutTimer )
				{
					clearTimeout( timeoutTimer );
				}
				if ( thisRequest.status == 200 )
				{
					thisOnSuccess( JoomooRequest.getResponse(thisRequest) );
				}
				else
				{
					if ( this.onFailure )
					{
						this.onFailure( thisRequest.status, thisRequest.statusText );
					}
					else
					{
						thisOnSuccess(null);
					}
				}
			}
		}
	}
}
/**
 * Sets request headers for our request
 */
JoomooRequest.prototype.setHeaders = function ( )
{
	this.request.setRequestHeader("User-Agent", "XMLHttpRequest" );
	this.request.setRequestHeader("Accept-Language", "en" );
	this.request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded" );
	// request.setRequestHeader("If-Modified-Since", lastRequestTime.toString() );
}

/**
 * Sends a post request to the server
 * values: required values to send
 */
JoomooRequest.prototype.sendPostRequest = function( values )
{
	this.request.send( this.encodeFormData(values) );
}

/**
 * check the response header and process (or not) accordingly
 * @return string containing the response
 */
JoomooRequest.getResponse = function ( request )
{
	if ( request.status == 200 )
	{
		switch ( request.getResponseHeader("Content-Type") )
		{
			case "text/xml":
				return request.responseXML;   // use parsed Document object
			case "text/json":
			case "text/javascript":
			case "application/javascript":
			case "application/x-javascript":
				eval(request.responseText)    // evaluate js code (be sure we can trust it!)
			default:
				return request.responseText;  // return plain text as a string
		}
	}
	else
	{
		return 'Error ' + request.status + ': ' + request.statusText;
	}
}
/**
 * Default timeout period and function to handle case when the server does not respond to our request in a timely fashion
 * Override these by specifying optional arguments to the constructor
 */
JoomooRequest.timeoutMilliSecs = 15000;
JoomooRequest.timeoutMessage = 'The server seems to be having difficulty responding to your request.' + "\n" +
	'If it totally ignores your request, you may want to try again later.';
JoomooRequest.onTimeout = function( )
{
	alert( JoomooRequest.timeoutMessage );
}

/**
 * Encode property name/value pairs
 * data: object or array of name/value pairs
 */
JoomooRequest.prototype.encodeFormData = function(data)
{
	var pairs = [];
	var regexp = /%20/g;   // matches an encoded space

	for ( var name in data )
	{
		var value = data[name].toString();
		//
		// encode data and replace '%20' with '+'
		//
		var pair = encodeURIComponent(name).replace(regexp,"+") + '=' +
		           encodeURIComponent(value).replace(regexp,"+");
		pairs.push(pair);
	}
	return pairs.join('&');   // concatenate pairs separating each by an "&"
}

/**
 * display basic span for message received by server
 */
JoomooRequest.writeAjaxLog = function ( )
{ document.write( '<span id="ajax_log"></span>' + "\n" ); }

/**
 * display divs to help us get this working and debug it later if need be
 */
JoomooRequest.writeDebugDivs = function ( )
{
	document.write( '<div id="response_text">response_text div here, waiting for you to click...</div>' + "\n" );
	document.write( '<div id="debug_text_1">debug_text_1 div here...</div>' + "\n" );
	document.write( '<div id="debug_text_2">debug_text_2 div here...</div>' + "\n" );
	return;
}

