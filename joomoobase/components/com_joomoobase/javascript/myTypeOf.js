/**
 * @author      Tom Hartung <webmaster@tomhartung.com>
 * @package     Joomla
 * @subpackage  JoomooBase
 * @copyright   Copyright (C) 2010 Tom Hartung. All rights reserved.
 * @since       1.5 
 * @license     GNU/GPL, see LICENSE.php
 */

/**
 * Looking to develp a rather thorough way of trying to figure out wtf the unknown argument is
 */
function myTypeOf(unknown)
{
    var myType = "undetermined?!?";

    if ( unknown == null )
    {
      myType = "null";
    }
    else
    {
      myType = typeof( unknown );
      if ( myType == "object" )
      {
        var myToString;   // try to run toString()
        var myCtorName;   // try to get name of constructor
        try
        {
          myToString = "[object].toString() = '" + unknown.toString() + "'<br />";
        }
        catch(exc)
        {
          myToString = "unknown.toString() threw an error: " + exc + "<br />";
        }
        myCtorName = "Unable to determine c'tor name";
        if ( unknown && unknown.constructor && unknown.constructor.toString )
        {
          var arr = unknown.constructor.toString().match( /function\s*(\w+)/ );
          if (arr && arr.length == 2)
          {
            myCtorName = "C'tor name = '" + arr[1] + "'";
          }
        }
        myType = "object:<br />" + myToString + myCtorName;
      }
    }
    return myType;
}

