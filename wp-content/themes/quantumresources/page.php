<?php get_header(); ?>

<main>
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
		
		<div class="cms-content">
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>