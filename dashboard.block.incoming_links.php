<?php if ( !defined( 'HABARI_PATH' ) ) { die( 'No direct access' ); } ?>
<ul class="items">
<?php if ( array_key_exists( 'error', $content->incoming_links ) ): ?>
	<li class="item clear">
		<span class="pct100"><?php _e( 'Oops, there was a problem with the links.', 'incoming_links' ) ?></span>
	</li>
	<li class="item clear">
		<span class="pct100"><?php echo $content->incoming_links['error']; ?></span>
	</li>
<?php elseif ( count( $content->incoming_links ) == 0 ): ?>
	<li class="item clear">
		<span class="pct100"><?php _e( 'No incoming links were found to this site.', 'incoming_links' ) ?></span>
	</li>
<?php else: ?>
	<?php foreach ( $content->incoming_links as $link ) : ?>
		<li class="item clear">
			<span class="pct100"><a href="<?php echo $link['href']; ?>" title="<?php echo $link['title']; ?>"><?php echo $link['title']; ?></a></span>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
</ul>
