<?php

require_once 'HTTP/OAuth/Consumer.php';
require 'Services/Twitter.php';

class k_twitter extends Services_Twitter {
	public function loadAPI() {
		parent::loadAPI();
		$this->api['statuses']['update_with_media'] = new SimpleXMLElement('<endpoint name="update_with_media" method="POST" auth_required="true">
            <formats>xml,json</formats>
            <param name="status" type="string" max_length="140" required="true"/>
            <param name="media[]" type="image" required="true"/>
            <param name="possibly_sensitive" type="boolean" required="false"/>
            <param name="in_reply_to_status_id" type="integer" required="false"/>
            <param name="lat" type="lat" required="false"/>
            <param name="long" type="long" required="false"/>
            <param name="place_id" type="string" required="false"/>
            <param name="display_coordinates" type="boolean" required="false"/>
        </endpoint>');
	}
}