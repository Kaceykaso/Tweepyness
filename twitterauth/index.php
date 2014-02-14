<?php
session_start();
require_once("twitteroauth.php"); //Path to twitteroauth library
 
$twitteruser = "kaceykaso";
$notweets = 30;
$consumerkey = "CvAY9oUMMqOBJCxYBBYvpA";
$consumersecret = "0Y7HEiYUWJe5UueC6tb9HRsY2t3lW0HBkZ0tc0WE";
$accesstoken = "6277712-joPuOWtgksAvxdJzxwmd0JuAPLhWqfoWrLaiqHx9dK";
$accesstokensecret = "ehSn2CCMRl4KJYKb6Ua1PjkcuCHQVdo5RtJKMNNuqg";
 
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
