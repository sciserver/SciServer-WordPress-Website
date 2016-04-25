=== RSSImport ===
Contributors: Bueltge, inpsyde, took77
Plugin URI: http://bueltge.de/wp-rss-import-plugin/55/
Author: Frank Bültge
Author URI: http://bueltge.de/
Donate link: http://bueltge.de/wunschliste/
Tags: rss, post, content, post, feed
Requires at least: 1.5
Tested up to: 4.5
Stable tag: 4.4.17

Display feeds on your blog, using PHP in your templates or Shortcode in your posts and pages.

== Description ==
Display feeds in your blog, using PHP, a Widget or Shortcode. The plugin uses only standard WordPress functionality, so no external libraries are required. For older versions of Wordpress, the built-in [MagpieRSS library](http://magpierss.sourceforge.net/) is used. For WordPress 2.8 and newer, there is a setting available to enable the built-in [SimplePie library](http://simplepie.org/) for parsing feeds.

As with all other content you publish, make sure you are allowed to (re-)publish the content of the feeds you are about to import!

You can insert the following code into a PHP plugin or in a template, for example `sidebar.php` or `single.php`:


_Example:_
`&lt;?php RSSImport(10, 'http://bueltge.de/feed/'); ?&gt;`

This is the minimal code needed for using the plugin. The plugin accepts a number of parameters for customizing the feed content import. See below for the full list of available parameters. You can also use any of the parameters with Shortcode in posts and pages.

_Example Shortcode:_
`[RSSImport display="5" feedurl="http://bueltge.de/feed/" use_simplepie="true"]`

For all (bool) parameters you can either use the strings `true` and `false` or the integer values `1` and `0`.


1. `display` - The number of items to display. Default is `5`.
1. `feedurl` - The feed address. Default is `http://bueltge.de/feed/`.
1. `before_desc` - The HTML or string to insert before the description. Default is `empty`. You can use some variables which will be replaced, see below.
1. `displaydescriptions` - (bool) When set to true, the description for each entry will be displayed. Default is `false`.
1. `after_desc` - The HTML or string to insert after the description. Default is `empty`. You can use some variables which will be replaced, see below.
1. `html` - (bool) When set to true, the description can include HTML tags. Default is `false`.
1. `truncatedescchar` - The maximum number of characters allowed in descriptions. If the description is longer than this length, it will be truncated to the given length. Default is `200`, set the value to empty quotes `''` to never truncate descriptions.
1. `truncatedescstring` - The HTML or string to insert at the end of a description after it has been truncated. Default is ` ... `
1. `truncatetitlechar` - The maximum number of characters allowed in titles. If the title is longer than this value, it will be truncated to the given length. Default is `''`, which means never truncate titles.
1. `truncatetitlestring` - The HTML or string to insert at the end of a title after it has been truncated. Default is `' ... '`.
1. `before_date` - The HTML or string to insert before the date. Default is ` <small>`.
1. `date` - (bool) If true, display the date of the item. Default is `false`.
1. `after_date` - The HTML or string to insert after the date. Default is `</small>`.
1. `date_format`- The date format string. Leave empty to use the format of your WordPress installation. If a different date format is desired, specify a PHP date string, for example: `F j, Y`. See also [the date and time formatting page in the WordPress Codex](http://codex.wordpress.org/Formatting_Date_and_Time).
1. `before_creator` - The HTML or string to insert before the creator of the item. Default is ` <small>`.
1. `creator` - (bool) If true, display the creator of the item. Default is `false`.
1. `after_creator` - The HTML or string to insert after creator of the item. Default is `</small>`.
1. `start_items` - The HTML or string to insert before the list of items. Default is `<ul>`.
1. `end_items` - The HTML or string to insert after the list of items. Default is `</ul>`.
1. `start_item` - The HTML or string to insert before each item. Default is `<li>`. You can use some variables which will be replaced, see below.
1. `end_item` - The HTML or string to insert after each item. Default is `</li>`. You can use some variables which will be replaced, see below.
1. `target` - The string to use for the `target` attribute on links. Default is `empty`. Valid options are `blank`, `self`, `parent`, `top`.
1. `rel` - The string to use for the `rel` attribute on links. Default is `empty`. Valid options are `nofollow` and `follow`.
1. `desc4title` - The description to use in the `title` attribute on item title links. Default is `false`.
1. `charsetscan` - (bool) If true, scan the feed content for the correct character set. This may cause the content to load more slowly. Use this option if you're having problems with feed content being displayed with stranged characters. Default is `false`.
1. `debug` - (bool) If true, activate debug-mode, which will echo the Magpie object as an array. Default is `false`. Only use this option for debugging.
1. `before_noitems` - The HTML or string to insert before the no items message. Default is `<p>`.
1. `noitems`- The message to display when the feed is empty. Default is `No items, feed is empty.`.
1. `after_noitems` - The HTML or string to insert before the no items message. Default is `</p>`.
1. `before_error` - The HTML or string to insert before the error message. Default is `<p>`.
1. `error` - Error message displayed when there is an error loading or displaying the feed. Default is `Error: Feed has an error or is not valid`.
1. `after_error` - The HTML or string to insert before the error message. Default is `</p>`.
1. `paging` - (bool) If true, enable pagination. Default is `false`.
1. `prev_paging_link` - The name of the previous page link. Default is `&laquo; Previous`.
1. `next_paging_link` - The name next page link. Default is `Next &raquo;`.
1. `prev_paging_title` - The title attribute of the previous page link. Default is `more items`.
1. `next_paging_title` - The title attribute of the next page link. Default is `more items`.
1. `use_simplepie` - (bool) If true, use SimplePie to parse the feed. SimplePie is included in WordPress 2.8 and newer and can parse both RSS and ATOM feeds. Default is `false` if used with Shortcode, `true` if used with the PHP function.
1. `view` - (bool) If true, calling the `RSSImport()` function will print the rendered HTML directly to the output. If false, the rendered HTML will be returned by the function as a string value and nothing will be output. Default when using PHP code is `true`. Default when using Shortcode is `false`.

The parameters `before_desc`, `after_desc`, `start_item` and `end_item` accepts the following variables which will be replaced:


1. `%title%` for the title of the entry
1. `%href%` for the entry's URL
1. `%picture_url%` for the URL of a thumbnail image for the entry if available. To use this variable, SimplePie is required to be enabled (`use_simplepie="true"`)


If pagination is enabled, it adds a `div` with the class `rsspaging` to enable easier styling with CSS. You can also style the previous and next links, which have the classes: `rsspaging_prev` and `rsspaging_next`.

You can use any of the parameters in the php function `RSSImport()` in your templates or with the Shortcode `[RSSImport]` in posts and pages.

= Examples =
_Using the PHP function with many parameters:_

	RSSImport(
		$display = 5, $feedurl = 'http://bueltge.de/feed/', 
		$before_desc = '', $displaydescriptions = false, $after_desc = '', $html = false, $truncatedescchar = 200, $truncatedescstring = ' ... ', 
		$truncatetitlechar = '', $truncatetitlestring = ' ... ', 
		$before_date = ' <small>', $date = false, $after_date = '</small>', 
		$before_creator = ' <small>', $creator = false, $after_creator = '</small>', 
		$start_items = '<ul>', $end_items = '</ul>', 
		$start_item = '<li>', $end_item = '</li>'
	);

Please note that for the PHP function the parameters are expected in the order in which they are defined in the above list. Thus if you skip one parameter, you will also have to skip all of the subsequent parameters.

_Using Shortcode with several parameters:_

	[RSSImport display="10" feedurl="http://your_feed_url/" 
	displaydescriptions="true" html="true" 
	start_items="<ol>" end_items="</ol>" paging="true" use_simplepie="true"]

_Add a "more" link to the output:_

	RSSImport(
		$display = 5,
		$feedurl = 'http://bueltge.de/feed/', 
		$before_desc = '',
		$displaydescriptions = true,
		$after_desc = ' <a href="%href%" target="_blank">show more</a>'
	);

or

	[RSSImport feedurl="http://www.ichdruck3d.de/feed/" after_desc=" <a href='%href%' target='_blank'>show more</a>" displaydescriptions="true" use_simplepie="true"]

_Enable Thumbnail Pictures:_

	RSSImport(
		$display = 5,
		$feedurl = 'http://www.thestage.co.uk/opinion/shenton/feed/',
		$before_desc = '<img src="%picture_url%" alt="">',
		$displaydescriptions = true
	);

or

	[RSSImport feedurl="http://www.thestage.co.uk/opinion/shenton/feed/" displaydescriptions="true" before_desc="<div><img src='%picture_url%' width='50px' alt='' style='float:left;' />" after_desc="</div>" use_simplepie="true"]

== Installation ==
1. Unpack the download package.
1. Upload all files to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Create a new site in WordPress or edit your template.
1. Copy the code into the site content or add it to your templates.


== Screenshots ==
1. Widget support

== Changelog ==
= v4.4.17 (2016-04-17) =
* Fix for usage under php 7*
* Code Formatting, WP Codex
* *You should always use SimplePie library as Feed Parser - check your parameter.*

= v4.4.16 (09/24/2015) =
* Fix the widget PHP4 style

= v4.4.15 (08/22/2014) =
* Added `%picture_url%` for `before_desc`, `after_desc`, `start_item` and `end_item`

= v4.4.14 (07/08/2014) =
* Fix typo in readme
* Fix a bug with `%href%` and `%title%` in after_desc
* Shortened some over-long lines in the code

= v4.4.13 (08/22/2013) =
* Set simplepie to default settings; is the default way of WP since lot of versions
* Fix for Quicktag on Post/Page Editor; use now the core functions, there we have since WP 3.3

= v4.4.12 (04/02/2012) =
* Bugfix: restored RSSImport QuickTag for Wordpress 3.3 and later
* Improvement: avoid PHP-notice when description is missing for an item
* TODO: add parameter to allow prefix of url (see http://wordpress.org/support/topic/plugin-rssimport-fix-for-headline-links-without-full-paths)
* TODO: check documentation of call to function (PHP), see http://wordpress.org/support/topic/plugin-rssimport-change-feed-display
* Documentation: corrected 'after_desc' (thanks to elricky for reporting)

= v4.4.11 (13/12/2011) =
* Bugfix: noitems string display is back
* Improvement: html_entity_decode feedurl when using shortcodes
* Maintenance: Add romanian language files

= v4.4.10 (01/12/2011) =
* Bugfix: add param desc4title on shortcodes
* Bugfix: Filter Feed-Url vor masked `&`; now works Yahoo Pipes feeds
* Maintenance: Translate strings from options

= v4.4.9 (09/16/2010) =
* Feature: add new param `desc4title` to add the description to title-attribut on title-links
* Bugfix: target parameter in widget
* Maintenance: rescan/rewrite de_DE language file
* Maintenance: rescan .pot

= v4.4.8 (06/04/2010) =
* small changes for better debugging
* change metadata for WordPress
* multilanguage plugin-description
* change error-handling on feeds; use WP-Error

= v4.4.7 (05/20/2010) =
* bugfix widget parameter for description
* small changes on source

= v4.4.6 (07/10/2009) =
* add function for WordPress lower version 2.8
* add option for format the date

= v4.4.5 (30/09/2009) =
* bugfix Widget-title
* include class SimpliePie for alternative parse
* new parameter `$use_simplepie` for active parse with class SimplePie
* change for boolean type, possible to use `true` or `false` and `1` or `0`

= v4.4.4 (15/09/2009) =
* change updatenotice to standard WP

= v4.4.3 (14/09/2009) =
* add strings %title% and %href% to replace in after-desc-option

= v4.4.2 (07/09/2009) =
* Bugfix for utl-value on shortcode
* change clean the title-attribut an links for multilanguage-support

= v4.4.1 (14/07/2009) =

* add rel attribut for links
* add widget support, WP 2.8 and higher

See on [the official website](http://bueltge.de/wp-rss-import-plugin/55/#historie "RSSImport Changelog") for older entries on changelog.


== Other Notes ==
= Acknowledgements =
Thanks to [Dave Wolf](http://www.davewolf.net, "Dave Wolf") for the idea, to [Thomas Fischer](http://www.securityfocus.de "Thomas Fischer") and [Gunnar Tillmann](http://www.gunnart.de "Gunnar Tillmann") for better code and Ilya Shindyapin, http://skookum.com for the idea and solution of pagination.

= Localizations =
* German language files by me [Frank B&uuml;ltge](http://bueltge.de/) ;-) 
* Russian translation by Fat Cow
* Ukrainian translation by [WordPress Ukraine](http://wpp.pp.ua/)
* French translation by [Martin Korolczuk](http://petitnuage.fr)
* Hungarian translation by [Feriman](http://feriman.com)
* Hindi translation by [Ashish Jha](http://outshinesolutions.com)
* Italian translation by [Gianni Diurno](http://gidibao.net/)
* Romanian language files, thanks to [Alexander Ovsov](http://webhostinggeeks.com/)
* Lithuanian translation files by [Vincent G](http://www.host1plus.com)
* Portuguese translation files by [Miguel Patricio Angelo](http://www.miguelpatricio.com/)

= License =
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me by leaving a [small donation](http://bueltge.de/wunschliste/ "Wish lists and donations") for the time I've spent writing and supporting this plugin. And I really don't want to know how many hours of my life this plugin has already eaten ;)

= Translations =
The plugin comes with various translations, please refer to the [WordPress Codex](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") for more information about activating the translation. If you want to help to translate the plugin to your language, please have a look at the sitemap.pot file which contains all defintions and may be used with a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) (Windows).
