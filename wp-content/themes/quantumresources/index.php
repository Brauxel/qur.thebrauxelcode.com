<?php get_header(); ?>

	<main id="home-page">
		<div class="slider-container">
			<div class="slide-content">
				<a class="logo-banner" href="<?php bloginfo('url'); ?>">Quantum Resources</a>
				
				<div class="heading-holder">
				<h1 class="one show">Agreement With Newmont Tanami Pty Ltd Covering The Officer Hill Tenement In The Northern Territory</h1>
				<h1 class="two">Agreement With Newmont Tanami Pty Ltd Covering The Officer Hill Tenement In The Northern Territory</h1>
				<h1 class="three">Agreement With Newmont Tanami Pty Ltd Covering The Officer Hill Tenement In The Northern Territory</h1>
				</div>
				
				<?php
				$feedUrl = "http://clients3.weblink.com.au/Clients/quantumresources/pricejson.asmx/getQuote?";
				$content = file_get_contents($feedUrl);
				$contentrens = json_decode($content);

				if (!empty($contentrens)):
					foreach($contentrens as $contentren):
				?>
				<div class="share-price-holder">
					<p class="heading-4">Share Price</p>
					<p><a href="<?php bloginfo('url'); ?>/investors/share-price-chart/">ASX: QUR $<?php echo $contentren[0]->wllastprice; ?></a></p>
					<a href="<?php bloginfo('url'); ?>/investors/share-price-chart/"><img src="<?php bloginfo('template_url'); ?>/images/asx-light.png" width="24" height="39" alt="ASX" title="ASX"></a>
				</div>
				<?php endforeach; endif; ?>
			</div>
			
			<div class="wrap">
				<div class="slider">
					<div data-rel="one" class="slide" style="background-image: url(<?php bloginfo('template_url'); ?>/images/slider/QUR-1.png);">&nbsp;</div>
					
					<div data-rel="two" class="slide" style="background-image: url(<?php bloginfo('template_url'); ?>/images/slider/QUR-2.png);">&nbsp;</div>
					
					<div data-rel="three" class="slide" style="background-image: url(<?php bloginfo('template_url'); ?>/images/slider/QUR-3.png);">&nbsp;</div>
				</div>
			</div>
		</div>
		
		<div class="wrap">
			<section class="home-content cms-content">
				<h2 style="display: none;">Quantum Resources</h2>
				<article>
					<h2 style="display: none;">Quantum Resources</h2>
					<p>Quantum Resources Limited engages in mineral exploration activities in Manitoba, Canada and in the top end of Australia. It primarily explores for a range of commodities, including lithium, gold and base metals. The company has a 95% interest in the Thompson Brothers Lithium Project in Wekusko Lake, Manitoba, whilst its principal Australian project tenements cover an area of approximately 20,000 square kilometers in the Northern Territory and Western Australia. The company was formerly known as Helm Resources Limited and changed its name to Quantum Resources Limited in December 1987. Quantum Resources Limited was incorporated in 1987 and is headquartered in Melbourne, Australia.</p>
				</article>
			</section>
			
			<div class="home-sidebar">
				<div class="two-columns">
					<div class="column">
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
						<p><br>For further announcements, click <a href="<?php bloginfo('url'); ?>/investors/announcements/">here</a></p>
						<?php endif; ?>
					</div>

					<div class="column">
						<h4>Receive our emails</h4>
						<p>Enter details below</p>
						<?php echo do_shortcode('[gravityform id="1" title="false" description="false" ajax="true"]'); ?>
					</div>
				</div>
			</div>
		</div>
	</main>

<?php get_footer(); ?>