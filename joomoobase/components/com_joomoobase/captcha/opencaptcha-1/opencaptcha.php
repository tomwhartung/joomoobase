<?php
/*************************************************************/
/* Copyright (C) 2006-2010 Tom Hartung, All Rights Reserved. */
/*************************************************************/

/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  joomoobase
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5
 * @license     TBD.
 */

//
// Functions to implement OpenCaptcha
// Adapted from code found at http://opencaptcha.com/
//
// We are not making this a class because recapturelib.php is not a class so
// it seems intuitive to follow suit for consistency's sake
//
/**
 * Gets the html for the captcha image and input tag for the solution
 * @return string html for image and input tag
 */
function getOpencaptchaHtml()
{
	$date = date("Ymd");
	$rand = rand(0,9999999999999);
	$height = OPENCAPTCHA_HEIGHT;
	$width  = OPENCAPTCHA_WIDTH;
	$size   = OPENCAPTCHA_SIZE;

	$img    = "$date$rand-$height-$width.jpgx";

	$captchaString  = '';
	$captchaString .= '<input type="hidden" name="img" value="' . $img . '">' . "\n";
	$captchaString .= '<a href="http://www.opencaptcha.com">' . "\n";
	$captchaString .= ' <img src="http://www.opencaptcha.com/img/' . $img . '" ' .
		'height="' . $height . '" alt="captcha" width="' . $width . '" border="0" /></a><br />' . "\n";
	$captchaString .= '<label for="joomoocaptcha_code">Text in the image:' . "\n";
	$captchaString .= ' <input type="text" name="code" id="joomoocaptcha_code" ' .
		'size="' . $size . '" /></label>' . "\n";
	$captchaString .= "<br />";

	return $captchaString;
}

/**
 * checks the user's input for the captcha
 * @return boolean True if anser is correct else False
 */
function checkOpenCaptchaResponse()
{
	if ( file_get_contents("http://www.opencaptcha.com/validate.php?ans=".$_POST['code']."&img=".$_POST['img'])=='pass' )
	{
		$answerOk = True;
	}
	else
	{
		$answerOk = False;
	}

	return $answerOk;
}

?>
