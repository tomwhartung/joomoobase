joomoobase
==========

JoomooBase: contains code shared by all Joomoo extensions


 JoomooBase
============
This extension contains two simple content plugins, but its main purpose
is to provide a library that contains code shared by one or more of the
other Joomoo extensions.

 Features
----------
Joomoobase contains PHP and Javascript code that:

o  Provides a PHP base class for interfacing with the Joomla database API
o  Supports the construction, sending, and processing of Ajax requests
o  Serves as a consistent interface for the handling of cookies
o  Provides a PHP class that interfaces with the Joomla JMail class
o  Allows the use of two types of CAPTCHA: OpenCaptcha and reCAPTCHA
   o  Each type has its advantages and disadvantages
   o  Site administrators can use the Joomla backend to easily switch
      between one and the other

 Plugins
---------
Joomoobase includes two very simple content plugins:

o  joomoosharethis: allows site administrators to put a placeholder in
   content articles that the plugin replaces with Javascript code for
   a "share this" button
   o  Site administrators can download the required Javascript from a
      site such as sharethis.com
   o  Administrators then use the Joomla backend to make this Javascript
      code available to the plugin
o  joomoodebug: displays parameters passed to Joomla content plugins,
   and is useful when designing and debugging these plugins

 JoomooShareThis Backend Parameter
-----------------------------------
sharethis_url
    URL (eg. from sharethis.com) that enables users to 'share-this' post
    Text field

