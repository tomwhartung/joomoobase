<?php
/*************************************************************/
/* Copyright (C) 2006-2010 Tom Hartung, All Rights Reserved. */
/*************************************************************/

/**
 * @version     $Id: constants.php,v 1.57 2008/11/12 22:51:49 tomh Exp tomh $
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoobase
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     TBD.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

//
// constants.php: constants and default values
// -------------------------------------------
//
/**
 * placeholder string indicating we want comments for this article
 * note we use a <br ...> tag so if plugin is missing or disabled we just get some whitespace
 * also note that if you change this you will also have to change all existing placeholders in your content!
 */
define ( 'JOOMOO_COMMENTS_PLACEHOLDER', '{joomoocomments-placeholder}" />' );
/**
 * placeholder string indicating where to put javascript for share this button for joomoosharethis plugin
 * note we use a <br ...> tag so if plugin is missing or disabled we just get some whitespace
 */
define ( 'JOOMOO_SHARETHIS_PLACEHOLDER', '{joomoosharethis-placeholder}' );
/**
 * placeholder string indicating we want a rating plugin for this article
 * note that if you change this you will also have to change all existing placeholders in your content!
 */
define ( 'JOOMOO_RATING_PLACEHOLDER', '{joomoorating-placeholder}' );
/**
 * placeholder string indicating we want a fixed rating displayed for this article
 * note that if you change this you will also have to change all existing placeholders in your content!
 */
define ( 'JOOMOO_FIXED_RATING_PLACEHOLDER', '{joomoofixedrating-placeholder}' );
/**
 * regular expression used to override default values for a fixed rating
 * E.g. to override value or subtitle or both use somthing like:
 *    '{joomoofixedrating-placeholder rating_value=7.3&rating_subtitle=Voting closed}' );
 */
define ( 'JOOMOO_FIXED_RATING_REGEX', '{joomoofixedrating-placeholder(.*)}' );
/**
 * delimiter for overrides matched by the '(.*)' in the regex above
 */
define ( 'JOOMOO_FIXED_OVERRIDES_DELIMITER', '&' );

/**
 * Possible values for some options defined and set in the back end.
 * A feature such as whether to display a captcha or log ip addrs
 * may be enabled for all users, no users, or anonymous users only
 */
define ( 'ALL_USERS', 'Y' );
define ( 'NO_USERS', 'N' );
define ( 'ANONYMOUS_USERS', 'A' );

/**
 * Values for whether to use ajax and/or post for comments, ratings (etc.?)
 */
define ( 'JOOMOO_USE_AJAX_ONLY', 'A' );
define ( 'JOOMOO_USE_FULL_ONLY', 'F' );
define ( 'JOOMOO_USE_AJAX_OR_FULL', 'E' );    // 'E'ither: allows using full when javascript is off

/**
 * Values for captcha type
 */
define ( 'CAPTCHA_TYPE_RECAPTCHA', 'R' );
define ( 'CAPTCHA_TYPE_OPENCAPTCHA', 'O' );

//
// constants for opencaptcha
// -------------------------
//
/**
 * version of the opencaptcha code we are using
 */
define ( 'OPENCAPTCHA_VERSION', 'opencaptcha-1' );
/**
 * name of file containing the opencaptcha code
 */
define ( 'OPENCAPTCHA_FILENAME', 'opencaptcha.php' );

/**
 * path to the code we use for opencaptcha
 */
if ( ! defined('JPATH_SITE') )
{
	$server_root = $_SERVER['DOCUMENT_ROOT'];
	// print '<br />Oh haii from joomoobase constants file where server_root = "' . $server_root . '"!<br />';
	define ( 'JPATH_SITE', $server_root );
}
define ( 'OPENCAPTCHA_FILE_PATH', JPATH_SITE.DS.'components'.DS.'com_joomoobase'.DS.'captcha'.DS.OPENCAPTCHA_VERSION.DS.OPENCAPTCHA_FILENAME );

/**
 * height of the opencaptcha image
 */
define ( 'OPENCAPTCHA_HEIGHT', "80" );
/**
 * height of the opencaptcha image
 */
define ( 'OPENCAPTCHA_WIDTH', "240" );
/**
 * size of the opencaptcha input text tag
 */
define ( 'OPENCAPTCHA_SIZE', "35" );

//
// constants for recaptcha - see http://recapthca.net
// --------------------------------------------------
//
/**
 * reCaptcha public key - sign up at http://recapthca.net to get your own
 */
define ( 'RECAPTCHA_PUBLIC_KEY', '6Lc4zwoAAAAAAHV57ckLKPAGZLUY89769o1eiHPG' );
/**
 * reCaptcha private key - sign up at http://recapthca.net to get your own
 */
define ( 'RECAPTCHA_PRIVATE_KEY', '6Lc4zwoAAAAAABJgHTXgE3QQO8s1Pa1-TblV_m_V' );
/**
 * reCaptcha dark background - set to True if form has a dark background, or False for light background
 * when javascript is disabled recaptcha displays instructions that are unreadable on dark backgrounds
 * set this to true to display instructions in a light font that the user can actually read
 */
define ( 'RECAPTCHA_DARK_BACKGROUND', True );
/**
 * version of the recaptcha code we are using
 */
define ( 'RECAPTCHA_VERSION', 'recaptcha-php-1.10' );
/**
 * name of the file containing the recaptcha code we are using
 */
define ( 'RECAPTCHA_FILENAME', 'recaptchalib.php' );
/**
 * path to the recaptcha code we use
 */
define ( 'RECAPTCHA_FILE_PATH', JPATH_SITE.DS.'components'.DS.'com_joomoobase'.DS.'captcha'.DS.RECAPTCHA_VERSION.DS.RECAPTCHA_FILENAME );

?>
