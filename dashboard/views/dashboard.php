<?php
$result = wp_remote_get('https://www.bcswebsiteservices.com/index.php?option=com_jmap&view=sitemap&format=rss');
    	if (!is_wp_error($result)) {
	    	if ($result['response']['code'] == 200) {
	    		$xml = simplexml_load_string($result['body']);
	    		$rssPosts = $xml->channel;
	    	}
	    	
	    	include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/dashboard/views/dashboard-data.php');
    	} else {
    		include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/dashboard/views/dashboard-nodata.php');
    	}
?>