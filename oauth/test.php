<?php
/**
 * Super simple two-legged oauth client example.
 * Based on https://code.google.com/p/oauth-php/wiki/ConsumerHowTo#Two-legged_OAuth
 *
 * Usage: php test.php
 *
 * Expected output:
 * <?xml version="1.0" encoding="utf-8"?>
 <result><uid>27</uid><name>copelandia</name><theme></theme><signature></signature><signature_format>filtered_html</signature_format><created>1367789256</created><access>0</access><login>0</login><status>1</status><timezone>America/New_York</timezone><language></language><picture/><data></data><roles is_array="true"><item>authenticated user</item></roles><rdf_mapping><rdftype is_array="true"><item>sioc:UserAccount</item></rdftype><name><predicates is_array="true"><item>foaf:name</item></predicates></name><homepage><predicates is_array="true"><item>foaf:page</item></predicates><type>rel</type></homepage></rdf_mapping></result>
 */

//http://oauth.net/code/
//http://oauth.googlecode.com/svn/code/php/OAuth.php
require 'oauth.php';

// Obtain key and secret by adding a consumer to your Drupal user.
// See http://copelandia.lulladev.com/user/5/oauth/consumer/1 for an example.

$key = '';
$secret = '';
$consumer = new OAuthConsumer($key, $secret);
$sig_method = new OAuthSignatureMethod_HMAC_SHA1;

//call this file
$api_endpoint = 'http://copelandia.local/api/user/youruseruid';

//handle request in 'server' block above
$parameters = array('server'=>'true');

//use oauth lib to sign request
$req = OAuthRequest::from_consumer_and_token($consumer, null, "GET", $api_endpoint, $parameters);
$sig_method = new OAuthSignatureMethod_HMAC_SHA1();
$req->sign_request($sig_method, $consumer, null);//note: double entry of token

//get data using signed url
$ch = curl_init($req->to_url());
curl_exec($ch);
curl_close($ch);
