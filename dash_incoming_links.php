	<h2>Incoming Links<?php _e( 'Incoming Links' ); ?> (<a href="http://blogsearch.google.com/?scoring=d&amp;num=10&amp;q=link:<?php Site::out_url( 'hostname' ) ?>" title="<?php _e( 'More incoming links' ); ?>"><?php _e( 'more' ); ?></a> &raquo;)</h2><div class="handle">&nbsp;</div>
	<ul class="items">
	<?php if ( count( $incoming_links ) == 0 ): ?>
		<span class="pct100"><?php _e( 'No incoming links were found to this site.', 'incoming_links' ) ?></span>
	<?php else: ?>
	<?php foreach ( $incoming_links as $link ) : ?>
		<li class="item clear">
			<span class="pct100"><a href="<?php echo $link['href']; ?>" title="<?php echo $link['title']; ?>"><?php echo $link['title']; ?></a></span>
		<li>
	<?php endforeach; ?>
	<?php endif; ?>
	</ul>
