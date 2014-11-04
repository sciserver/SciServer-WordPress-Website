<?php
/*
Plugin Name: Random image gallery with pretty photo zoom
Plugin URI: http://www.gopiplus.com/work/2011/12/12/wordpress-plugin-random-image-gallery-with-pretty-photo-zoom/
Description: This plugin which allows you to simply and easily show random image anywhere in your template files or using widgets with onclick pretty photo zoom effect. 
Author: Gopi Ramasamy
Version: 7.3
Author URI: http://www.gopiplus.com/work/2011/12/12/wordpress-plugin-random-image-gallery-with-pretty-photo-zoom/
Donate link: http://www.gopiplus.com/work/2011/12/12/wordpress-plugin-random-image-gallery-with-pretty-photo-zoom/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function rigwppz_show() 
{
	$rigwppz_dir_check = get_option('rigwppz_dir');
	if(is_dir($rigwppz_dir_check))
	{
			include_once("select-random-image.php");
			$rigwppz_theme = get_option('rigwppz_theme');
		
			if($rigwppz_theme == "")
			{
				$rigwppz_theme = "dark_rounded";
			}
			
			global $ScriptInserted;
			if (!isset($ScriptInserted) || $ScriptInserted !== true)
			{
			$ScriptInserted = true;
			?><script type="text/javascript" charset="utf-8">
			  jQuery(document).ready(function(){
				jQuery("a[rel^='prettyPhoto']").prettyPhoto({
			overlay_gallery: false, "theme": '<?php echo $rigwppz_theme; ?>', social_tools: false
			});
		  });
		</script><?php
		}
	}
	else
	{
		echo "Directory not exist: " . $rigwppz_dir_check;
	}
}

function rigwppz_install() 
{
	add_option('rigwppz_title', "Slideshow Pretty photo");
	add_option('rigwppz_width', "180");
	add_option('rigwppz_theme', "light_square");
	
	add_option('rigwppz_dir', "wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/");
	add_option('rigwppz_dir1', "wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/");
	add_option('rigwppz_dir2', "wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/");
	add_option('rigwppz_dir3', "wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/");
	add_option('rigwppz_dir4', "wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/");
	add_option('rigwppz_dir5', "wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/");
	add_option('rigwppz_title_yes', "YES");
}

add_shortcode( 'random-image-pp-zoom', 'rigwppz_shortcode' );

function rigwppz_shortcode( $atts ) 
{
	//[random-image-pp-zoom dir="DIR1" width="200" theme="1"]
	global $wpdb;
	$rigwfz = "";
	
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	$userdir = $atts['dir'];
	$rigwppz_width = $atts['width'];
	$rigwppz_theme = $atts['theme'];
	$userdir = strtoupper($userdir);
	
	switch ($rigwppz_theme) 
	{ 
		case 1: 
			$rigwppz_theme = "dark_rounded";
			break;
		case 2: 
			$rigwppz_theme = "dark_square";
			break;
		case 3: 
			$rigwppz_theme = "default";
			break;
		case 4: 
			$rigwppz_theme = "light_rounded";
			break;
		case 5: 
			$rigwppz_theme = "facebook";
			break;
		case 6: 
			$rigwppz_theme = "light_square";
			break;
		default:
			$rigwppz_theme = "dark_rounded";
	}
	
	if($userdir == "DIR1")
	{
		$rigwppz_dir = get_option('rigwppz_dir1');
	}
	elseif($userdir == "DIR2")
	{
		$rigwppz_dir = get_option('rigwppz_dir2');
	}
	elseif($userdir == "DIR3")
	{
		$rigwppz_dir = get_option('rigwppz_dir3');
	}
	elseif($userdir == "DIR4")
	{
		$rigwppz_dir = get_option('rigwppz_dir4');
	}
	elseif($userdir == "DIR5")
	{
		$rigwppz_dir = get_option('rigwppz_dir5');
	}
	elseif($userdir == "WIDGET")
	{
		$rigwppz_dir = get_option('rigwppz_dir');
	}
	else
	{
		$rigwppz_dir = get_option('rigwppz_dir');
	}
	
	if(!is_dir($rigwppz_dir))
	{
		$rigwfz = "Directory not exist:<br>" . $rigwppz_dir;
		return $rigwfz;	
	}
	
	$rigwppz_siteurl = get_option('siteurl') . "/" . $rigwppz_dir ;
	
	$imglist='';
	//$img_folder is the variable that holds the path to the banner images. Mine is images/tutorials/
	// see that you don't forget about the "/" at the end 
	$img_folder = $rigwppz_dir;
	
	mt_srand((double)microtime()*1000);
	
	//use the directory class
	if($img_folder =="")
	{
		$img_folder = "wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/";
	}
	$imgs = dir($img_folder);
	
	//read all files from the  directory, checks if are images and ads them to a list (see below how to display flash banners)
	while ($file = $imgs->read()) 
	{
		if(strpos(strtoupper($file), '.JPG') > 0 or strpos(strtoupper($file), '.GIF') >0 or strpos(strtoupper($file), '.GIF') > 0 )
		{
		$imglist .= "$file ";
		}
	} 
	closedir($imgs->handle);
	
	//put all images into an array
	$imglist = explode(" ", $imglist);
	$no = sizeof($imglist)-2;
	
	//generate a random number between 0 and the number of images
	$random = mt_rand(0, $no);
	$image = $imglist[$random];
	
	$mainsiteurl =	get_option('siteurl') . "/wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/";
	
	
	if(!is_numeric($rigwppz_width))
	{
		$rigwppz_width = 180;
	} 
	
	$rigwfz = $rigwfz . '<div>';
	$rigwfz = $rigwfz . '<a href="'.$rigwppz_siteurl . $image .'" rel="prettyPhoto" title="">';
	$rigwfz = $rigwfz . '<img src="'.$mainsiteurl.'crop-random-image.php?AC=YES&DIR='.$rigwppz_dir.'&IMGNAME='.$image.'&MAXWIDTH='.$rigwppz_width.'"> ';
	$rigwfz = $rigwfz . '</a>';
	$rigwfz = $rigwfz . '</div>';
	
	$prettyPhoto = "'prettyPhoto'";
	$rigwppz_theme = "'".$rigwppz_theme."'";

	global $ScriptInserted;
	if (!isset($ScriptInserted) || $ScriptInserted !== true)
	{
		$ScriptInserted = true;
		$rigwfz = $rigwfz . '<script type="text/javascript" charset="utf-8">jQuery(document).ready(function(){jQuery("a[rel^='.$prettyPhoto.']").prettyPhoto({overlay_gallery: false, "theme": '.$rigwppz_theme.', social_tools: false});});</script>';
	}
	
	return $rigwfz;	
}

function rigwppz_widget($args) 
{
	extract($args);
	echo $before_widget. $before_title;
	if(get_option('rigwppz_title_yes') == "YES") 
	{
		echo get_option('rigwppz_title');
	}
	echo $after_title;
	rigwppz_show();
	echo $after_widget;
}

function rigwppz_admin_option() 
{
	global $wpdb;
	?>
<div class="wrap">
  <div class="form-wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br>
    </div>
    <h2><?php _e('Random image gallery with pretty photo zoom (R I G W PP Z)','rigwppz'); ?></h2>
    <?php
	$rigwppz_title = get_option('rigwppz_title');
	$rigwppz_width = get_option('rigwppz_width');
	$rigwppz_theme = get_option('rigwppz_theme');
	$rigwppz_dir = get_option('rigwppz_dir');
	$rigwppz_title_yes = get_option('rigwppz_title_yes');
	
	$rigwppz_dir1 = get_option('rigwppz_dir1');
	$rigwppz_dir2 = get_option('rigwppz_dir2');
	$rigwppz_dir3 = get_option('rigwppz_dir3');
	$rigwppz_dir4 = get_option('rigwppz_dir4');
	$rigwppz_dir5 = get_option('rigwppz_dir5');
	
	if (isset($_POST['rigwppz_form_submit']) && $_POST['rigwppz_form_submit'] == 'yes')
	{
		//	Just security thingy that wordpress offers us
		check_admin_referer('rigwppz_form_setting');
			
		$rigwppz_title = stripslashes($_POST['rigwppz_title']);
		$rigwppz_width = stripslashes($_POST['rigwppz_width']);
		$rigwppz_theme = stripslashes($_POST['rigwppz_theme']);
		$rigwppz_dir = stripslashes($_POST['rigwppz_dir']);
		$rigwppz_title_yes = stripslashes($_POST['rigwppz_title_yes']);
		
		$rigwppz_dir1 = stripslashes($_POST['rigwppz_dir1']);
		$rigwppz_dir2 = stripslashes($_POST['rigwppz_dir2']);
		$rigwppz_dir3 = stripslashes($_POST['rigwppz_dir3']);
		$rigwppz_dir4 = stripslashes($_POST['rigwppz_dir4']);
		$rigwppz_dir5 = stripslashes($_POST['rigwppz_dir5']);
		
		update_option('rigwppz_title', $rigwppz_title );
		update_option('rigwppz_width', $rigwppz_width );
		update_option('rigwppz_theme', $rigwppz_theme );
		update_option('rigwppz_dir', $rigwppz_dir );
		update_option('rigwppz_title_yes', $rigwppz_title_yes );
		
		update_option('rigwppz_dir1', $rigwppz_dir1 );
		update_option('rigwppz_dir2', $rigwppz_dir2 );
		update_option('rigwppz_dir3', $rigwppz_dir3 );
		update_option('rigwppz_dir4', $rigwppz_dir4 );
		update_option('rigwppz_dir5', $rigwppz_dir5 );
		
		?>
		<div class="updated fade">
			<p><strong><?php _e('Details successfully updated.','rigwppz'); ?></strong></p>
		</div>
		<?php
	}
	?>
	<form name="rigwppz_form" method="post" action="">
		<h3><?php _e('Plugin configuration (Widget)','rigwppz'); ?></h3>
		<label for="tag-title"><?php _e('Widget title','rigwppz'); ?></label>
		<input name="rigwppz_title" type="text" id="rigwppz_title" size="70" maxlength="200" value="<?php echo $rigwppz_title; ?>" />
		<p><?php _e('Please enter your widget title.','rigwppz'); ?></p>
		
		<label for="tag-title"><?php _e('Width (Only for widget)','rigwppz'); ?></label>
		<input name="rigwppz_width" type="text" id="rigwppz_width" maxlength="3" value="<?php echo $rigwppz_width; ?>" />
		<p><?php _e('Please enter your width, this option is only for widget.','rigwppz'); ?></p>
		
		<label for="tag-title"><?php _e('Theme (Only for widget)','rigwppz'); ?></label>
		<select name="rigwppz_theme" id="rigwppz_theme">
            <option value='dark_rounded' <?php if($rigwppz_theme == 'dark_rounded') { echo 'selected' ; } ?>>Dark Rounded</option>
            <option value='dark_square' <?php if($rigwppz_theme == 'dark_square') { echo 'selected' ; } ?>>Dark Square</option>
            <option value='default' <?php if($rigwppz_theme == 'default') { echo 'selected' ; } ?>>Default</option>
            <option value='light_rounded' <?php if($rigwppz_theme == 'light_rounded') { echo 'selected' ; } ?>>Light Rounded</option>
			<option value='facebook' <?php if($rigwppz_theme == 'facebook') { echo 'selected' ; } ?>>Facebook</option>
			<option value='light_square' <?php if($rigwppz_theme == 'light_square') { echo 'selected' ; } ?>>Light Square</option>
          </select>
		<p><?php _e('Please select your theme, this option is only for widget.','rigwppz'); ?></p>
		
		<label for="tag-title"><?php _e('Display sidebar title','rigwppz'); ?></label>
		<select name="rigwppz_title_yes" id="rigwppz_title_yes">
			<option value='YES' <?php if($rigwppz_title_yes == 'YES') { echo 'selected="selected"' ; } ?>>Yes</option>
			<option value='NO' <?php if($rigwppz_title_yes == 'NO') { echo 'selected="selected"' ; } ?>>No</option>
		</select>
		<p><?php _e('Do you want to show title on your sidebar, this option is only for widget.','rigwppz'); ?></p>
		
		<label for="tag-title"><?php _e('Image directory (Widget)','rigwppz'); ?></label>
		<input name="rigwppz_dir" type="text" id="rigwppz_dir" size="110" value="<?php echo $rigwppz_dir; ?>" />
		<p><?php _e('Please enter your image directory, this option is only for widget.','rigwppz'); ?>
		(Example: wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/)
		</p>
		
		<h3><?php _e('Plugin configuration (Short Code)','rigwppz'); ?></h3>
		
		<label for="tag-title"><?php _e('Image directory','rigwppz'); ?> (DIR1)</label>
		<input name="rigwppz_dir1" type="text" id="rigwppz_dir1" size="110" value="<?php echo $rigwppz_dir1; ?>" />
		<p><?php _e('Please enter your image directory.','rigwppz'); ?> (DIR1) (Example: wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/random-gallery/)</p>
		
		<label for="tag-title"><?php _e('Image directory','rigwppz'); ?> (DIR2)</label>
		<input name="rigwppz_dir2" type="text" id="rigwppz_dir2" size="110" value="<?php echo $rigwppz_dir2; ?>" />
		<p><?php _e('Please enter your image directory.','rigwppz'); ?> (DIR2)</p>
		
		<label for="tag-title"><?php _e('Image directory','rigwppz'); ?> (DIR3)</label>
		<input name="rigwppz_dir3" type="text" id="rigwppz_dir3" size="110" value="<?php echo $rigwppz_dir3; ?>" />
		<p><?php _e('Please enter your image directory.','rigwppz'); ?> (DIR3)</p>
		
		<label for="tag-title"><?php _e('Image directory','rigwppz'); ?> (DIR4)</label>
		<input name="rigwppz_dir4" type="text" id="rigwppz_dir4" size="110" value="<?php echo $rigwppz_dir4; ?>" />
		<p><?php _e('Please enter your image directory.','rigwppz'); ?> (DIR4)</p>
		
		<label for="tag-title"><?php _e('Image directory','rigwppz'); ?> (DIR5)</label>
		<input name="rigwppz_dir5" type="text" id="rigwppz_dir5" size="110" value="<?php echo $rigwppz_dir5; ?>" />
		<p><?php _e('Please enter your image directory.','rigwppz'); ?> (DIR5)</p>
		
		<div style="height:10px;"></div>
		<input type="hidden" name="rigwppz_form_submit" value="yes"/>
		<input name="rigwppz_submit" id="rigwppz_submit" class="button add-new-h2" value="Submit" type="submit" />
		<?php wp_nonce_field('rigwppz_form_setting'); ?>
	</form>
  </div>
	<h3><?php _e('Plugin configuration option','rigwppz'); ?></h3>
	<ol>
		<li><?php _e('Add the plugin in the posts or pages using short code.','rigwppz'); ?></li>
		<li><?php _e('Add directly in to the theme using PHP code.','rigwppz'); ?></li>
		<li><?php _e('Drag and drop the widget to your sidebar.','rigwppz'); ?></li>
	</ol>
  <p class="description">
   <?php _e('Check official website for more information','rigwppz'); ?>
  <a target="_blank" href="http://www.gopiplus.com/work/2011/12/12/wordpress-plugin-random-image-gallery-with-pretty-photo-zoom/"><?php _e('click here','rigwppz'); ?></a>
  <br /><?php _e('Note: Dont upload your original images into the defult folder (or) Plugin folder.','rigwppz'); ?>
  </p>
</div>
	<?php
}

function rigwppz_control()
{
	echo '<p><b>';
	_e('Random image gallery', 'rigwppz');
	echo '.</b> ';
	_e('Check official website for more information', 'rigwppz');
	?> <a target="_blank" href="http://www.gopiplus.com/work/2011/12/12/wordpress-plugin-random-image-gallery-with-pretty-photo-zoom/"><?php _e('click here', 'rigwppz'); ?></a></p><?php
}

function rigwppz_widget_init() 
{
	if(function_exists('wp_register_sidebar_widget')) 	
	{
		wp_register_sidebar_widget('rigwfz', __('R I G W PP Z','rigwppz'), 'rigwppz_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 	
	{
		wp_register_widget_control('rigwfz', array(__('R I G W PP Z','rigwppz'), 'widgets'), 'rigwppz_control');
	} 
}

function rigwppz_deactivation() 
{
	// No action required.
}

function rigwppz_add_to_menu() 
{
	add_options_page(__('Random image gallery with pretty photo zoom - R I G W PP Z','rigwppz'), 
				__('R I G W PP Z','rigwppz'), 'manage_options', 'random-image-gallery-with-pretty-photo-zoom', 'rigwppz_admin_option' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'rigwppz_add_to_menu');
}

function rigwppz_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'prettyPhoto', get_option('siteurl').'/wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/css/prettyPhoto.css','','','screen');
		wp_enqueue_script( 'jquery.prettyPhoto', get_option('siteurl').'/wp-content/plugins/random-image-gallery-with-pretty-photo-zoom/js/jquery.prettyPhoto.js');
	}	
}

function rigwppz_textdomain()
{
	  load_plugin_textdomain( 'rigwppz', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('plugins_loaded', 'rigwppz_textdomain');
add_action('wp_enqueue_scripts', 'rigwppz_add_javascript_files');
add_action("plugins_loaded", "rigwppz_widget_init");
register_activation_hook(__FILE__, 'rigwppz_install');
register_deactivation_hook(__FILE__, 'rigwppz_deactivation');
add_action('init', 'rigwppz_widget_init');
?>