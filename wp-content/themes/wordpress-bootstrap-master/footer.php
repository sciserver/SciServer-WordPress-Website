					</div> <!-- end content #container -->
				</div> <!-- end wrap div -->
			<div id="footer">

		<div class="footer1">
			<div class="container">
				<div id="inner-footer" class="clearfix">
		          <div id="widget-footer" class="clearfix row">
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer1') ) : ?>
		            <?php endif; ?>
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer2') ) : ?>
		            <?php endif; ?>
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer3') ) : ?>
		            <?php endif; ?>
		          </div>
				</div> <!-- end #inner-footer -->	
			</div> <!-- end footer #container -->
		</div><!-- end #footer1 -->

		<div class="footer2">
			<div class="container">
				<div class="row">
					<div class="col-lg-3">
						<p>Copyright &copy; <?php echo date("Y") ?> SciServer</p>
					</div>
					<div class="col-lg-9">
					<nav class="clearfix">
						<?php wp_bootstrap_footer_links(); // Adjust using Menus in Wordpress Admin ?>
					</nav>
				</div>
				</div>
			</div><!-- end #footer2 container -->
		</div><!-- end #footer2 -->
				
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>
		</div> <!-- end footer -->
	</body>

</html>