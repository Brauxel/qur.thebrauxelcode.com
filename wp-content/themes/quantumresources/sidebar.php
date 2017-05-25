			<aside>
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
			</aside>
