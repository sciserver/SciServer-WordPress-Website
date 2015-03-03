=== WordPress Creation Kit === 

Contributors: reflectionmedia, madalin.ungureanu, sareiodata
Donate link: http://www.cozmoslabs.com/wordpress-creation-kit-sale-page/
Tags: custom fields, custom field, wordpress custom fields, advanced custom fields, custom post type, custom post types, post types, repeater fields, repeater, repeatable, meta box, meta boxes, metabox, taxonomy, taxonomies, custom taxonomy, custom taxonomies, custom, custom fields creator, post meta, meta, get_post_meta, post creator, cck, content types, types

Requires at least: 3.1
Tested up to: 4.0
Stable tag: 2.1.2

A must have tool for creating custom fields, custom post types and taxonomies, fast and without any programming knowledge.


== Description ==

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