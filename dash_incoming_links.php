<h2><?php _e( 'Incoming Links' ); ?> (<a href="http://blogsearch.google.com/?scoring=d&amp;num=10&amp;q=link:<?php Site::out_url( 'habari' ) ?>" title="<?php _e( 'More incoming links' ); ?>"><?php _e( 'more' ); ?></a> &raquo;)</h2><div class="handle">&nbsp;</div>
<ul class="items">
<?php if ( array_key_exists( 'error', $incoming_links ) ): ?>
	<li class="item clear">
		<span class="pct100"><?php _e( 'Oops, there was a problem with the links.', 'incoming_links' ) ?></span>
	</li>
	<li class="item clear">
		<span class="pct100"><?php echo $incoming_links['error']; ?></span>
	</li>
<?php elseif ( count( $incoming_links ) == 0 ): ?>
	<li class="item clear">
		<span class="pct100"><?php _e( 'No incoming links were found to this site.', 'incoming_links' ) ?></span>
	</li>
<?php else: ?>
	<?php foreach ( $incoming_links as $link ) : ?>
		<li class="item clear">
			<span class="pct100"><a href="<?php echo $link['href']; ?>" title="<?php echo $link['title']; ?>"><?php echo $link['title']; ?></a></span>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
</ul>
