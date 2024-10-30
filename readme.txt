=== JinX - The Javascript Includer ===
Contributors: jqueryin
Donate link: http://www.jqueryin.com/
Tags: javascript, js, add javascript, include javascript, post javascript, page javascript
Requires at least: 2.6.3
Tested up to: 3.0
Stable tag: 1.1.2

JinX gives you the ability to separate any javascript you may have from your blog posts and pages.  It provides a separate textarea 
for adding javascript code which will not be stripped or sanitized.

== Description ==

JinX gives you the ability to separate any javascript you may have from your blog posts and pages.  It provides a separate textarea 
for adding javascript code which will not be stripped or sanitized. To combat the loosened security, we have implemented a role-based
access control list (ACL) in the options section. This ACL gives you the capability of deciding exactly which user roles should be able
to utilize the plugin.  JinX also applies the markItUp! jQuery plugin to the JinX textarea for easier adding, editing, and viewing. The 
plugin gives you the ability to properly use tabs and allows the textarea to auto-expand.

Credit goes out to Jay Salvat (http://www.jaysalvat.com) for his markItUp! jQuery plugin that was used in this plugin. For more information on
this markup editor, please visit http://markitup.jaysalvat.com/home/.

*Related Links:*

* <a href="http://www.jqueryin.com/projects/jinx-javascript-includer-wordpress-plugin/" title="JinX WordPress Plugin">Official Plugin Homepage</a>
* <a href="http://wordpress.org/tags/jinx">JinX Support Forum</a>

*Change Log*

* 1.1.2 - Fixed minor bug in the code with the new ACL restrictions.
* 1.1.0 - Added an administrative options page to reduce the set of user roles allowed to use JinX from the publish pages. This has the effect of locking down JinX to only privileged users to get around the issue of no entity encoding, escaping, or filtering of input.
* 1.0	- Support for Firefox 2/3, IE 6/7, Safari 3/4, class support

== Installation ==

Installing JinX is as easy as following a few simple steps:

1.  Download the JinX plugin from the WordPress Plugin Repository.
2.  Extract the .zip file to the `/wp-content/plugins/` directory, preserving the `jinx-the-javascript-includer` directory
3.  Browse to the Plugins page from your WordPress admin
4.  Activate JinX from the list of inactive plugins on the plugins menu page.
5.  Refer to the official plugin page for further documentation

== Frequently Asked Questions ==

= Why is JinX useful? =

JinX allows you to both include javascript in your pages and post as well as separate page content from scripting.

== Screenshots ==

1. This screen shot shows JinX being displayed below the edit post textarea. (screenshot-1.jpg)
