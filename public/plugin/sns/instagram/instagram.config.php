<?php

	$Dir="../../../../";
	include_once($Dir."conf/config.sns.php");
	// Setup class
	$instagram = new Instagram(array(
		'apiKey'      => $snsItConfig['clientId'],
		'apiSecret'   => $snsItConfig['clientSecret'],
		'apiCallback' => $snsItConfig['callbackUrl'] // must point to success.php
	));

?>