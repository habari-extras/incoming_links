<?php
class IncomingLinks extends Plugin
{
	/**
	 * Adds an incoming links module to the dashboard
	 *
	 */

	// Setting the expiry to 2 hours means it will never expire, because the cron job runs every hour.
	private $cache_expiry = 7200;

	/**
	 *
	 */
	public function action_plugin_activation( $file )
	{
		// Add a periodical execution event to be triggered hourly
		CronTab::add_hourly_cron( 'incoming_links', 'incoming_links', 'Find links to this blog.' );
	}

	function action_plugin_deactivation( $file )
	{
		// Remove the periodical execution event
		CronTab::delete_cronjob( 'incoming_links' );
		// Clear the cached links
		Cache::expire( 'incoming_links' );
	}

	/**
	 * Plugin incoming_links filter, executes for the cron job defined in action_plugin_activation()
	 * @param boolean $result The incoming result passed by other sinks for this plugin hook
	 * @return boolean True if the cron executed successfully, false if not.
	 */
	public function filter_incoming_links( $result )
	{
		$incoming_links = $this->get_incoming_links();
		Cache::set( 'incoming_links', $incoming_links, $this->cache_expiry );

		return $result;  // Only change a cron result to false when it fails.
	}

	/**
	 * Add the block this plugin provides to the list of available blocks
	 * @param array $block_list An array of block names, indexed by unique string identifiers
	 * @return array The altered array
	 */
	public function filter_dashboard_block_list($block_list)
	{
		$block_list['incoming_links'] = 'Incoming Links';
		$this->add_template( 'dashboard.block.incoming_links', __DIR__ . '/dashboard.block.incoming_links.php' );
		return $block_list;
	}

	/**
	 * Produce the content for the latest entries block
	 * @param Block $block The block object
	 * @param Theme $theme The theme that the block will be output with
	 */

	public function action_block_content_incoming_links($block, $theme)
	{
		$block->incoming_links = $this->theme_incoming_links();
		$block->link = "http://blogsearch.google.com/?scoring=d&amp;num=10&amp;q=link:" . Site::get_url( 'habari' );;
	}

	public function theme_incoming_links()
	{
		// There really should be something in the cache, CronJob should have put it there, but if there's not, go get the links now
		if ( Cache::has( 'incoming_links' ) ) {
			$incoming_links = Cache::get( 'incoming_links' );
		}
		else {
			$incoming_links = $this->get_incoming_links();
			Cache::set( 'incoming_links', $incoming_links, $this->cache_expiry );
		}

		return $incoming_links;
	}

	private function get_incoming_links()
	{
		$links = array();
		try {
			$search = new RemoteRequest( 'http://blogsearch.google.com/blogsearch_feeds?scoring=d&num=10&output=atom&q=link:' . Site::get_url( 'habari' ) );
			$search->set_timeout( 5 );
			$result = $search->execute();
			if ( Error::is_error( $result ) ) {
				throw $result;
			}
			$response = $search->get_response_body();
			if (mb_detect_encoding($response, 'UTF-8', true)) {
				$xml = new SimpleXMLElement( $response );
				foreach ( $xml->entry as $entry ) {
					//<!-- need favicon discovery and caching here: img class="favicon" src="http://skippy.net/blog/favicon.ico" alt="favicon" / -->
					$links[]= array( 'href' => (string)$entry->link['href'], 'title' => (string)$entry->title );
				}
			}
			else {
				EventLog::log( _t( 'The response had non-UTF-8 characters' ), 'err', 'plugin' );
			}

		} catch(Exception $e) {
			$links['error']= $e->getMessage();
		}
		return $links;
	}

}
?>
