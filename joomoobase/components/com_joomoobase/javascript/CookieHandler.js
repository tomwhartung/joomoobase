/**
 * @version     $Id: CookieHandler.js,v 1.2 2009/04/28 21:06:50 tomh Exp tomh $
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoobase
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     GNU/GPL, see LICENSE.php
 */
/**
 * basic functions to support handling of cookies
 */
var CookieHandler = {};         // declare our class name in the global namespace
function CookieHandler () { };  // dummy constructor (singleton class)

/**
 * create and save a cookie to the user's browser
 * name: name of cookie to create (required)
 * value: value to save in cookie (required)
 * days: number of days to save it (optional: defaults to 365)
 */
CookieHandler.createCookie = function( name, value, days )
{
	var maxAge;  // how long to save cookie (in seconds)

	if (days)
	{
		maxAge = 60 * 60 * 24 * days;
	}
	else
	{
		maxAge = 60 * 60 * 24 * 365;
	}
	document.cookie = name + "=" + value + "; max-age=" + maxAge + "; path=/";
}
/**
 * get value associated with a cookie
 * if cookie not found, returns null
 * name: name of cookie to access
 */
CookieHandler.getCookieValue = function( name )
{
	var nameEquals = name + "=";
	var cookieArray = document.cookie.split(';');
	var aCookie;
	var theValue;

	for( var index =0; index < cookieArray.length; index ++ )
	{
		aCookie = cookieArray[index];

		while ( aCookie.charAt(0) == ' ' )
		{
			aCookie = aCookie.substring(1,aCookie.length);
		}
		if ( aCookie.indexOf(nameEquals) == 0 )
		{
			theValue = aCookie.substring(nameEquals.length,aCookie.length);
		//	document.write( "CookieHandler.getCookieValue: " + name + " = " + theValue + "<br />\n" );
			return theValue;
		}
	}
	return null;
}
/**
 * delete a cookie
 * name: name of cookie to delete
 */
CookieHandler.deleteCookie = function( name )
{
	document.cookie = name + "=; max-age=0; path=/";
}
