<?php
session_start();
require_once("twitteroauth.php"); //Path to twitteroauth library
 
$twitteruser = "kaceykaso";
$notweets = 30;

// stick auth details here
$consumerkey = "";
$consumersecret = "";
$accesstoken = "";
$accesstokensecret = "";
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
 
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
 
$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);
 
//echo json_encode($tweets);
$encode = json_encode($tweets);

//Parse request
$response = json_decode($encode);

//Global vars
$time = '';
$tweets = array();
foreach($response as $tweet)
{
  $time = "{$tweet->created_at}";
  $tweets[] = "{$tweet->text}";
  //echo "{$tweet->text}<br><br>";
}
$tweets_count = count($tweets);

echo "Tweets: ".$tweets_count." by @$twitteruser, ending at $time";
?>
