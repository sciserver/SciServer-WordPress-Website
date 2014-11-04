<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>
			
		 <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

 
  <?php the_content(); ?>

<?php endwhile; else: ?>
  <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
	<footer class="text-center" style="height: 60px">
		<div class="footer2" style="-webkit-box-shadow: 0 -4px 0 rgba(180,180,184,.85);
box-shadow: 0 -4px 0 rgba(180,180,184,.85);">
			<div class="container">
				<div class="row">
				
<hr />
<div class="col-md-3 col-md-offset-3">
	<h4>This project is supported by</h4>
<img style="width: 150px;" alt="" src="http://upload.wikimedia.org/wikipedia/commons/thumb/8/87/NSF_Logo.PNG/600px-NSF_Logo.PNG" />
</div>
<div class="col-md-3">
	<h4>This project is administered by</h4>
<img src="/wp-content/uploads/2014/03/idies.logo_.small_.horizontal.black_.png" style="width: 300px; height:150px;" alt="logo">
	</div>

				</div>
				<div class="row">
					<p class="center">Copyright &copy; <?php echo date("Y") ?>
 SciServer</p>
				</div>
			</div><!-- end #footer2 container -->
		</div><!-- end #footer2 -->

		</footer>
<?php wp_footer(); ?>