<?php
class IncomingLinks extends Plugin
{
	/**
	 * Adds an incoming links module to the dashboard
	 *
	 */

	/**
	 * Required Plugin Informations
	 */
	public function info()
	{
		return array(
			'name' => 'IncomingLinks',
			'version' => '1.0',
			'url' => 'http://habariproject.org/',
			'author' =>	'Habari Community',
			'authorurl' => 'http://habariproject.org/',
			'license' => 'Apache License 2.0',
			'description' => 'Incoming Links Dashboard Module',
			'copyright' => '2008'
		);
	}

	/**
	 *
	 */
	public function action_plugin_activation( $file )
	{
		if ( realpath( $file ) == __FILE__ ) {
			Modules::add( 'Incoming Links' );
		}
	}

	function action_plugin_deactivation( $file )
	{
		if( Plugins::id_from_file($file) == Plugins::id_from_file(__FILE__) ) {
			// remove the module from the dash if it is active
			Modules::remove_by_name( 'Incoming Links' );
		}
	}

	public function filter_dash_modules( $modules )
	{
		$modules[]= 'Incoming Links';
		$this->add_template( 'dash_incoming_links', dirname( __FILE__ ) . '/dash_incoming_links.php' );
		return $modules;
	}

	public function filter_dash_module_incoming_links( $module, $module_id, $theme )
	{
		$theme->incoming_links= $this->theme_incoming_links();

		$module['content']= $theme->fetch( 'dash_incoming_links' );
		return $module;
	}

	public function theme_incoming_links()
	{
		if ( Cache::has( 'incoming_links' ) ) {
			$incoming_links= Cache::get( 'incoming_links' );
		}
		else {
			$incoming_links= $this->get_incoming_links();
			//Cache::set( 'incoming_links', $incoming_links );
		}

		return $incoming_links;
	}

	private function get_incoming_links()
	{
		$links = array();
		try {
			$search= new RemoteRequest( 'http://blogsearch.google.com/blogsearch_feeds?scoring=d&num=10&output=atom&q=link:' . Site::get_url( 'hostname' ) );
			$search->set_timeout( 5 );
			$result= $search->execute();
			if ( Error::is_error( $result ) ) {
				throw $result;
			}
			$response = $search->get_response_body();
			$xml = new SimpleXMLElement( $response );
			foreach( $xml->entry as $entry ) { 
				//<!-- need favicon discovery and caching here: img class="favicon" src="http://skippy.net/blog/favicon.ico" alt="favicon" / -->
				$links[]= array( 'href' => $entry->link['href'], 'title' => $entry->title );
			}
		} catch(Exception $e) {
			//print '<p>' . $e->getMessage() . "</p>\r\n";
		}
		return $links;
	}

	/**
	* Enable update notices to be sent using the Habari beacon
	*/
	public function action_update_check() {
		Update::add( 'IncomingLinks', 'f33b2428-facb-43b7-bb44-a8d78cb3ff9d',  $this->info->version );
	}

}
?>
