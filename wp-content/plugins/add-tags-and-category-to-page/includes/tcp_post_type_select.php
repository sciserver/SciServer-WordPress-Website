<?php 
if ($_POST['submit-tcp-select']){ 
	$tcp_add_tag 		= @join(',',$_POST['tcp_add_tag']);
	$tcp_add_category 	= @join(',',$_POST['tcp_add_category']);
	update_option('tcp_add_tag', $tcp_add_tag);
	update_option('tcp_add_category', $tcp_add_category);
	$tcp_save_MSG		= 'Updated Succesfully';	
}
$tcp_add_tag 				= get_option('tcp_add_tag');
$tcp_add_category 			= get_option('tcp_add_category');
$tcp_add_tag_array 			= explode(',',$tcp_add_tag);
$tcp_add_category_array 	= explode(',',$tcp_add_category);
?>
<?php if (!empty($tcp_save_MSG)):?>
	<div class="updated" id="message"><p><?php echo $tcp_save_MSG ?></p></div>
<?php endif; ?>

<table class="wp-list-table widefat fixed bookmarks">
    <thead>
        <tr>
            <th>Select Post Types</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>
			<h4>Please tick/untick the checkbox to enable/disable tags and category in your post types.</h4>
            <?php
            $args = array(
   				'_builtin' => false
			);
			$post_types['page']	= 'page';
			$post_types	 		= array_merge($post_types, get_post_types($args));
			?>
            
            <form action="" method="post">
            <table cellspacing="0" class="wp-list-table widefat fixed bookmarks">
                <thead>
                    <tr>
                        <th width="30">Sn</th>
                        <th>Post Type</th>
                        <th>Add Tags ?</th>
                        <th>Add Category ?</th>
                    </tr>
                </thead>
                
                <tbody>
                   <?php 
				   $cnt=0;
				   	foreach ( $post_types as $post_type ): 
					$cnt++;
				   ?>
                        <tr>
                            <td><?php echo $cnt; ?></td>
                            <td><?php echo ucfirst(str_replace('-',' ',($post_type))); ?></td>
                            <td><input type="checkbox" name="tcp_add_tag[]" value="<?php echo $post_type; ?>" <?php echo in_array($post_type,$tcp_add_tag_array)?'checked="checked"':'';  ?> /></td>
                            <td><input type="checkbox" name="tcp_add_category[]" value="<?php echo $post_type; ?>" <?php echo in_array($post_type,$tcp_add_category_array)?'checked="checked"':'';  ?>/></td>
                        </tr>
                   <?php endforeach; ?>
                   
                </tbody>
                </form>
                
            </table><br/>
            
            <p align="center"><input type="submit" name="submit-tcp-select" class="button-primary" value="Save Changes" /></p>
            
<br/>
</td>
</tr>
</tbody>
</table><br/>