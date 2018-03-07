=== WordPress Creation Kit === 

Contributors: cozmoslabs, reflectionmedia, madalin.ungureanu, sareiodata, adispiac
Donate link: http://www.cozmoslabs.com/wordpress-creation-kit-sale-page/
Tags: custom fields, custom field, wordpress custom fields, advanced custom fields, custom post type, custom post types, post types, repeater fields, repeater, repeatable, meta box, meta boxes, metabox, taxonomy, taxonomies, custom taxonomy, custom taxonomies, custom, custom fields creator, post meta, meta, get_post_meta, post creator, cck, content types, types

Requires at least: 3.1
Tested up to: 4.9.2
Stable tag: 2.5.2

A must have tool for creating custom fields, custom post types and taxonomies, fast and without any programming knowledge.


== Description ==

**Like this plugin?** Consider leaving a [5 star review](https://wordpress.org/support/view/plugin-reviews/wck-custom-fields-and-custom-post-types-creator?filter=5).

**WordPress Creation Kit** consists of three tools that can help you create and maintain custom post types, custom taxonomies and most importantly, custom fields and metaboxes for your posts, pages or CPT's.

**WCK Custom Fields Creator** offers an UI for setting up custom meta boxes for your posts, pages or custom post types. Uses standard custom fields to store data.

**WCK Custom Post Type Creator** facilitates creating custom post types by providing an UI for most of the arguments of register_post_type() function.

**WCK Taxonomy Creator** allows you to easily create and edit custom taxonomies for WordPress without any programming knowledge. It provides an UI for most of the arguments of register_taxonomy() function.

= Custom Fields =
* Custom fields types: wysiwyg editor, upload, text, textarea, select, checkbox, radio
* Easy to create custom fields for any post type.
* Support for **Repeater Fields** and **Repeater Groups**.
* Drag and Drop to sort the Repeater Fields.
* Support for all input fields: text, textarea, select, checkbox, radio.
* Image / File upload supported via the WordPress Media Uploader.
* Possibility to target only certain page-templates, target certain custom post types and even unique ID's.
* All data handling is done with ajax
* Data is saved as postmeta

= Custom Post Types and Taxonomy =
* Create and edit Custom Post Types from the Admin UI
* Advanced Labeling Options
* Attach built in or custom taxonomies to post types
* Create and edit Custom Taxonomies from the Admin UI
* Attach the taxonomies to built in or custom post types

= WCK PRO =
  The [PRO version](http://www.cozmoslabs.com/wordpress-creation-kit-sale-page/) offers:
  
* Front-end Posting - form builder for content creation and editing
* Premium Email Support for your project
  
 [See complete list of features](http://www.cozmoslabs.com/wordpress-creation-kit-sale-page/)

= Website =
http://www.cozmoslabs.com/wordpress-creation-kit/

= Announcement Post and Video =
http://www.cozmoslabs.com/3747-wordpress-creation-kit-a-sparkling-new-custom-field-taxonomy-and-post-type-creator/

= Documentation =
http://www.cozmoslabs.com/wordpress-creation-kit/custom-fields-creator/

= Bug Submission and Forum Support =
http://www.cozmoslabs.com/forums/forum/wordpresscreationkit/

== Installation ==

1. Upload the wordpress-creation-kit folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Then navigate to WCK => Custom Fields Creator tab and start creating your custom fields, or navigate to WCK => Post Type Creator tab and start creating your custom post types or navigate to WCK => Taxonomy Creator tab and start creating your taxonomies.

== Frequently Asked Questions ==

= How do I display my custom fields in the front end? =

Let's consider we have a meta box with the following arguments:
- Meta name: books
- Post Type: post
And we also have two fields defined:
- A text custom field with the Field Title: Book name
- And another text custom field with the Field Title: Author name

You will notice that slugs will automatically be created for the two text fields. For 'Book name' the slug will be 'book-name' and for 'Author name' the slug will be 'author-name'

Let's see what the code for displaying the meta box values in single.php of your theme would be:

`<?php $books = get_post_meta( $post->ID, 'books', true ); 
foreach( $books as $book){
	echo $book['book-name'] . '<br/>';
	echo $book['author-name'] . '<br/>';
}?>`

So as you can see the Meta Name 'books' is used as the $key parameter of the function get_post_meta() and the slugs of the text fields are used as keys for the resulting array. Basically CFC stores the entries as custom fields in a multidimensional array. In our case the array would be:

`<?php array( array( "book-name" => "The Hitchhiker's Guide To The Galaxy", "author-name" => "Douglas Adams" ),  array( "book-name" => "Ender's Game", "author-name" => "Orson Scott Card" ) );?>`

This is true even for single entries.

= How to query by post type in the front end? =

You can create new queries to display posts from a specific post type. This is done via the 'post_type' parameter to a WP_Query.

Example:

`<?php $args = array( 'post_type' => 'product', 'posts_per_page' => 10 );
$loop = new WP_Query( $args );
while ( $loop->have_posts() ) : $loop->the_post();
	the_title();
	echo '<div class="entry-content">';
	the_content();
	echo '</div>';
endwhile;?>`

This simply loops through the latest 10 product posts and displays the title and content of them. 

= How do I list the taxonomies in the front end? =

If you want to have a custom list in your theme, then you can pass the taxonomy name into the the_terms() function in the Loop, like so:

`<?php the_terms( $post->ID, 'people', 'People: ', ', ', ' ' ); ?>`

That displays the list of People attached to each post.

= How do I query by taxonomy in the frontend? =

Creating a taxonomy generally automatically creates a special query variable using WP_Query class, which we can use to retrieve posts based on. For example, to pull a list of posts that have 'Bob' as a 'person' taxomony in them, we will use:

`<?php $query = new WP_Query( array( 'person' => 'bob' ) ); ?>`

==Screenshots==
1. Creating custom post types and taxonomies
2. Creating custom fields and meta boxes
3. List of Meta boxes
4. Meta box with custom fields
5. Defined custom fields
6. Meta box arguments
7. Post Type Creator UI
8. Post Type Creator UI and listing
9. Taxonomy Creator UI
10. Taxonomy listing

== Changelog ==
1.1.0
Added Options Page Creator.
Added Datepicker, Country Select and User Select field types.
Fixed Menu Position argument for Custom Post Type Creator.
Added filter for default_value.
Fixed Template Select dropdown for Custom Fields Creator.
Fixed a bug in Custom Fields Creator that prevented Options field in the process of creating custom fields from appearing.

1.1.1
Fixed bugs when form names in Frontend Posting were containing UTF8 characters (like hebrew, chirilic...)
Fixed Sortable field in Custom Fields Creator that wasn't clickable

1.1.2
Added cpt select extra field.
Improved the display of user select extra field.

1.1.3
Implemented Front End Posting editing of default labels, submit button text and success messages text
Fixed warnings and notices
Fixed bug when multiple country selects appeared on the same page
Fixed FEP Dashboard css bug

1.1.4
Fixed FEP label changes compatibility with php 5.2

1.1.5
Added Custom Fields Api
Added option to enable/disable WCK tools(CFC, CPTC, FEP...) that you want/don't want to use 
Labels of required fields turn red when empty
Added in Custom Taxonomy Creator support for show_admin_column argument that allows automatic creation of taxonomy columns on associated post-types
Improved visibility of WCK Help tab
Fixed bug in Frontend Posting that didn't allow sorting on repeaters when editing posts
Fixed bug in Frontend Posting that when editing posts the upload attach to post didn't work
We no longer get js error when deregistering wysiwig init script 

1.1.6
Fixed error in 1.1.5 for require_once

2.0.0
Added Swift Templates
WCK menu now only appears for administrators only

2.0.1
Fixed a bug in Swift Templates. Wrong logic applying templates to single posts.

2.0.2
WordPress 3.8 small compatibility tweeks
Featured Image now avaiable in Swift Templates
Fixed some notices regarding serial number
Removed files from codemirror library we weren't using

2.0.3
Support for Taxonomy Terms inside Swift Templates
Fixed bug where only one Swift Template could filter the content, other single templates weren't applied
Refactored a function the function that generates the Swift Tags in the backend

2.0.4
Added support for including CPT Select inside Swift Templates. You'll get acess to everything inside the selected custom post type.
Swift Templates now handles errors in a user friendly manner whithout throwing fatal errors
Swift Templates now supports shortcodes
Added filter to FEP for setting up default values in forms
Custom fields can now be added to non public custom post types registered with WCK
Fixed bug in Tiny MCE that converted urls to use relative path
Options Page Creator eliminated the redirect message in the backend

2.0.5
Upload Field now uses the media manager added in WP 3.5
Added progress icon on forms in Front End Posting
Now we prevent "Meta Field" and "Field Title" to be named "content" or "action" in Custom Fields Creator to prevent conflicts with existing WordPress Fields
Fixed bug in Front End Posting where a filter for posts_where wasn't removed correctly
Fixed bug in Custom Fields Creator that didn't display "0" values
Fixed buf in Front End Posting that didn't displayed the right values if the Taxonomy had the label "Categories" (regardless of it's slug). Now it won't list the default Categories in WP

2.0.6
Replaced wysiwyg editor from tinymce to ckeditor to fix compatibility issues with WordPress 3.9

2.0.7
Added filter for the arguments passed to the register_post_type() funtion when creating a Custom Post Type. ( "wck_cptc_register_post_type_args" )
Fixed the missing datepicker css 404 error. 
Removed notices  
Fixed "Attach upload to post" option for the upload field.
Fixed issue in FEP where "Admin Approval" option wasn't displayed correctly in the backend.
Fixed a javascript error in FEP when trying to sort after adding fields in the "Form Fields" metabox in the backend 
Fixed issue in FEP that wasn't displying taxonomies when they were attached to multiple posts. It duplicated the above post.

2.0.8
Fixed some notices and warnings in FEP
Now we can add the same metabox from CFC on multiple ids
Fixed bug in Swift Templates that was preventing Codemirror to display correctly when it was loading in a closed metabox
Added filter for the arguments passed to the register_taonomy() funtion when creating a Custom Taxonomy. ( "wck_ctc_register_taxonomy_args" )
Fixed bug that was executing  shortcodes inside escaped shortcodes [[shortcode]]
Fixed problem in CPTC that was setting the 'publicly_queryable' argument as true
Change version of Codemirror library
Front End Posting registration form takes into consideration the general WordPress settings regarding User Registration
Front End Posting now takes into consideration the WordPress global settings for comments

2.0.9

Ability to add multiple Front End Posting forms on the same page
Changed deprecated jquery live() function with on() function
Now the upload.js files only loads on the pages where we have Front End Posting forms
Added filters for metabox context and priority as well as for loading or not a metabox
Added filter that allows us to include wck scripts everywhere in the backend
Add filter for metabox content headers and footers. Also remove a closing div that wasn't needed.
Fixed bug: preview posts and preview drafts not working when we enabled swift template on single
Prevent post form submission when hitting enter on wck text inputs
Removed notice from swift templates redirect
Fixed bug that was causing unwanted slashes to appear when saving options
Fixed bug that wasn't loading swift templates scripts on all custom post types
Fixed sorting bug: when editing a field inside the metabox and then canceling the action the sorting wasn't working
Fixed some bugs that were preventing proper sorting of the metabox fields

2.1.0

Added support for shortcodes in swift generated single pages
Now only the author of a post can edit that post using FEP
Front End Posting Dashboard no longer lists edit links for post types on which we don't have FEP forms
Added filters which we can use to modify the text on metabox buttons in the backend (ex. Add Entry)
Fixed a bug that when we had unserialized fields enabled and we deleted some items in the metabox they still remained in the database
Created filters for before and after elements in Front End Posting Forms
Fixed some PHP Warnings and Notices


2.1.1
Fixed problem with upload field in Front End Posting forms when you were logged in as a subscriber
Fixed some Warnings and Notices
Added new filters: 'wck_delete_button', 'wck_edit_button', 'wck_after_adding_form_{$meta}', 'wck_select_{$meta}_{$field_name}_option_{$i}'

2.1.2
Added support in Swift templates for multiple file sizes for images
Wysiwyg editor fields no longer strips html tags
Hooks from wck-static-metabox class no longer execute on frontend or when loading with ajax
Changed the design of the upload buttons in frontend posting
Implemented Serial Number Notices
Changes to WCK deactivate function so it doesn't throw notices

2.1.3
Changed the way Single Forms are displayed and saved.
Added 'slug' parameter to API and we use it so we can translate labels
Added filter for taxonomy term name
Added support for search in media library for the upload field
Add support for the link in the listed upload fields
Add support for link on image/icon that points to attachement page in backend
Changed the order of the CKEDITOR.config.allowedContent = true to be above the call to initialized the textarea
Now metaboxes or pages don't appear for users that shouldn't

2.1.4
Fixed major issue that prevented publishing new metaboxes (CFC)
Added a footer message asking users to leave a review if they enjoyed WCK

2.1.5
Aligned "Help" wit "WCK" in contextual help red button
Fixed some issues with translations
Fixed issue with checkbox not saving in single Options Metaboxes
Fixed issue with updating profile without password in FEP dashboard that removed password
Add support for 'post_author_id' in Swift Templates
Removed pro banner from sidebar on the pro version
We now run the Custom Post Type and  Custom Taxonomy register function sooner on the init hook
Add CKEditor Justify addon for enabling the align buttons


2.1.6
Select field can now display lables when outputting values
Minor security improvements
We no longer get .js errors when a Select field has no options
Renamed class Mustache_Autoloader to WCK_Mustache_Autoloader to avoid class redeclaration error from other plugins
Change names of some variables in Swift Templates .js to fix conflict with the Avada theme builder
Added global filter for a form element output
Added a filter in WCK Front End Posting dashboard for posts query
Fixed typo in Meta Box Creator

2.1.7
We now allow  Custom Post Types and Custom Taxonomies to be available via REST API by adding 'show_in_rest' attribute

2.1.8
Fixed issue with Front End Posting that deleted post title, content or excerpt if the field wasn't present in the Front End Posting edit form
Fixed typo from 'Chose' to 'Choose'


2.1.9
We now display error message when meta name contains uppercase letters
We now display error when taxonomy name contains uppercase letters or spaces
Nopriv ajax actions now are available only when Front End Posting  is enabled
Fixed issues with post thumbnail and themes that added thumbnail support for specific post types in Custom Post Types Creator
Removed notice when WPML was active in certain cases
Users with the capability to 'edit_others_posts' can now edit them from the frontend in Front End Posting as well


2.2.0
When renaming a taxonomy we now make sure the terms get ported as well
We now make sure we have jquery when loading the FEP form
Fixed a issue where a Mustache class sometimes reported that it was already loaded


2.2.1
Added additional labels to Post Type Creator and Taxonomy Creator
We now check the post type name to not have spaces, capital letters or hyphens
When changing a custom post type name the existing posts get ported as well

2.2.2
Minor security improvements
Added filter for the 'rewrite' argument in the Custom Taxonomy Creator: 'wck_ctc_register_taxonomy_rewrite_arg'
Added hooks in WCK_Page_Creator api to allow extra content before and after metaboxes: 'wck_page_creator_before_meta_boxes' and 'wck_page_creator_after_meta_boxes'
Added wp_nonce to edit post links in Front End Posting Dashboard
Swift Templates divs now have a unique class to allow customization per template

2.2.3
We now load the translation files from the theme first if they exist in the folder:local_wck_lang
Now in Custom Fields Creator the Options field for selects,radios and checkboxes is required so you can't create those field without any options
Single forms now keep their values when form throws alert required message so you don't have to input the values again
Swift Templates: rewritten the meta queries. Reduced them by a factor of 8 which should greatly improve performance on large databases
Swift Templates: fixed upload field image metadata for repeater field and alt text for featured image

2.2.4
Changed way we make sure swift templates is not run in the header by mistake because of Yoast SEO
Added new filter for registration errors

2.2.5
Swift template post content now attempts to embed automatically embedable links
Front End Posting form now can only edit posts of the set post type in the form arguments
Small change in saving single metaboxes
Fixed a possible conflict with ACF Pro

2.2.6
Added Heading field type
Added Colorpicker field type
Added Currency field type
Added number of rows and readonly options to the textarea field
Added error notice for users with a php version lower than 5.3.0 on the settings page
Front End Posting field labels can now be translated with WPML

2.2.7
Added Phone field type
Added HTML field type
Added Time Picker field type
Added Default Text for textarea field instead of Default Value

2.2.8
Added Map field type
Added Lables field in Custom Fields Creator next to Options for checkboxes, selects and radios
Fixed a bug with the datepicker field and repeaters

2.2.9
Fixed bug in cfc when updating post
Security fixes

2.3.0
Fixed Front end Posting problem with anonymous posting and repeater custom fields
Fixed preview draft not showing the correct custom fields in certain conditions
Fixed a fatal error that was happening in certain conditions when adding a new Custom Fields Creator Meta Box

2.3.1
Added Number field type
Removed notice regarding post thumbnail on certain themes
Fixed and error with the Map field
New branding to match website

2.3.2
Added date format option for Datepicker Field
Fixed notices when multiple single boxes were present and the first one had a required error
New menu icon

2.3.3
We now save every custom field in it's own separate post meta with an editable meta_key
UI improvements to repeater sortable table

2.3.4
Fixed issue with Custom Fields Creator when fields had the same name as the meta name

2.3.5
Fixed an issue with the unserialized conversion page when fields had same names

2.3.6
Fixed some issues with the unserialized fields conversion
Changed per batch variable from 100 to 30 to try to reduce timeouts on sites with a lot of entries
Fixed warnings regarding Number field
Fixed a issue with FEP and label change that contained special characters on some servers

2.3.7
Added sortable taxonomy admin column support for Taxonomy Creator
Added show_in_quick_edit argument support for Taxonomy Creator

2.3.8
Changes to the unserialized fields: we can now handle fields from other sources
Improvements to javascript speed in the admin interface

2.3.9
Fixed a problem with Front End Posting and some custom fields not showing up on edit
Fixed a bug in Front End Posting admin interface when values in the fields list would disappear
We now display default values in custom fields on front end forms from Front End Posting
Fixed an issue with Swift Templates and serialized fields when the serialized meta was not present
Fixed an issue with fields that had their slug changed and didn't appear sometimes
Modifications to upload button so that it disappears when we already have something uploaded
Added 2 new currencies in the Currency Select field
Small modifications to the generate slug function

2.4.0
Compatibility with php version 7.1
Fixed an issue with Swift Templates and repeater fields that were not displaying properly

2.4.1
Map fields and Wysiwyg Editors now have three mustaches in Swift Templates
Fixed an issue with Swift Templates and fields that had their slug changed from the default one
Changed the serial number field to a password field
We now check for reserved names on Custom Post Types and Taxonomy Creator
Added a filter to change input type: wck_text_input_type_attribute_{$meta}_{$field_slug}
Fixed a potential notice in Custom Fields Creator

2.4.2
Security improvements
Small css change for labels in metaboxes
Small PHP 7 compatibility change

2.4.3
All WCK meta keys are now protected so they do not appear in WordPress Custom Fields box which fixes some issues
We now can translate WCK labels with string translation in WPML (this includes Front end Posting labels)
Fixed a small css bug
We now save the taxonomy slug instead of label in Front End Posting so we are compatible with Multilingual
We now save in unserialized form the single custom fields in Front End Posting
Added ISO 8601 basic format to the Datepicker field.

2.4.4
Added seamless display mode option to Custom Fields Creator boxes
Taxonomies in Front End Posting now have a slug so we can translate the titles

2.4.5
Added multiple select field type
Change query args from month to monthnum as they should be in Swift Templates

2.4.6
Added filter 'wck_extra_field_attributes' which with you can add extra attributes to fields
Fixed the start page css
Fixed an issue with swift template admin interface
Added translation function to wrap Register string on login form

2.4.7
Improved speed on sites with a lot of Custom Fields Metaboxes defined
Fixed and issue with Swift Templates and the 0 value for some custom fields
Fixed some php notices that appeared in Front End Posting

2.4.8
Added a filter so we can add a metabox to multiple meta boxes: wck_filter_add_meta_box_screens
Fixed issue with PageBuilder from SiteOrigin plugin and CodeMirror
Refactored the way Swift Templates rewrite rules are being loaded

2.4.9
Fixed a notice regarding the Custom Fields Creator introduced in the last version

2.5.0
Important security fix. Please update

2.5.1
Improved speed by at least 100% in most cases for the interface
Small visual and functionality interface tweaks

2.5.2
Added 'rewrite' and 'rewrite slug' advanced options for custom taxonomy creator
Added a plugin notification class
Put back the yellow background on rows when editing an entry
Fixed an issue with the view more jquery dialog in Swift Templates view more image tags
