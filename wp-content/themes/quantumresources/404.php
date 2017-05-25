<?php get_header(); ?>
	<main id="two-col-right">
		<div class="wrap">
			<?php
			$feedUrl = "http://clients3.weblink.com.au/Clients/quantumresources/pricejson.asmx/getQuote?";
			$content = file_get_contents($feedUrl);
			$contentrens = json_decode($content);

			if (!empty($contentrens)):
				foreach($contentrens as $contentren):
			?>
			<p class="pre-content">
				<span>QUANTUM RESOURCES SHARE PRICE ASX: QUR $<?php echo $contentren[0]->wllastprice; ?></span>
			</p>
			<?php endforeach; endif; ?>
			
			<section class="main-content">
				<?php
					$mypages = get_pages( array( 'parent' => 0, 'sort_column' => 'menu_order', 'sort_order' => 'asc'  ) );
				?>
				<div id="side-nav">
					<ul>
					<?php foreach( $mypages as $mypage ): ?>
						<li<?php if ( $mypage->ID == get_the_ID() ): ?> class="current-menu-item"<?php endif; ?>><a href="<?php echo get_the_permalink( $mypage->ID ); ?>"><?php echo $mypage->post_title; ?></a></li>
					<?php endforeach; ?>
					</ul>
				<!-- div.side-nav ENDS -->
				</div>
				
				<div class="cms-content content">
					<?php the_post_thumbnail(); ?>
					<h1>404 - Page not found</h1>
					<p>Oops! You seem to have taken a wrong turn somewhere.</p>
					<p>Please click <a href="<?php bloginfo('url'); ?>">here</a> to return to the home page.</p>
				</div>
			<!-- section.main-content ENDS -->
			</section>
			
			<?php get_sidebar(); ?>
			
		</div>
		<div class="clear"></div>
	</main>
<?php get_footer(); ?>