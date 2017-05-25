<?php
/*
 * Template name: Projects	
*/	
?>

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
					$parent_id = wp_get_post_parent_id( $post_ID );
					if ( $parent_id == 0 ):
						$mypages = get_pages( array( 'child_of' => $post->ID, 'sort_column' => 'menu_order', 'sort_order' => 'asc' ) );
					else:
						$mypages = get_pages( array( 'child_of' => $parent_id, 'sort_column' => 'menu_order', 'sort_order' => 'asc' ) );
					endif;
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
				</div>
			<!-- section.main-content ENDS -->
			</section>
			
			<?php get_sidebar(); ?>
			<div class="clear"></div>
			
			<section class="full-cms cms-content">
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
				
				<div class="res-sidebar">
					<div class="side-form">
						<h4>Receive our emails</h4>
						<p>Enter details below</p>
						<?php echo do_shortcode('[gravityform id="1" title="false" description="false" ajax="true"]'); ?>
					</div>

					<?php
					$feedUrl = "http://clients3.weblink.com.au/Clients/quantumresources/LatestHeadlineJson.aspx";
					$content = file_get_contents($feedUrl);
					$contentrens = json_decode($content);
					if (!empty($contentrens)):
					?>
					<h4>Recent Announcements</h4>
					<?php
					foreach($contentrens as $contentren):
						$obj = json_decode( '{"date":"'.$contentren->datetime.'"}' );
						$obj->date = preg_replace( '/[^0-9]/', '', $obj->date );
						$dateparse = date( 'F Y', ( $obj->date / 1000 ) );
						$dateTime = date( 'Y-m-d H:i', ( $obj->date / 1000 ) );
					?>
					<a href="<?php echo $contentren->pdfLink; ?>" target="_blank" class="button-1"><?php echo $contentren->HeadlineText; ?><br><time datetime="<?php echo $dateTime; ?>"><?php echo $dateparse; ?></time></a>
					<?php endforeach; ?>
					<p><br>For further announcements, click <a href="<?php bloginfo('url'); ?>/investors/announcements/"><u>here</u></a></p>
					<?php endif; ?>
				<!-- div.res-form ENDS -->
				</div>
			</section>
			
		</div>
		<div class="clear"></div>
	</main>
<?php get_footer(); ?>