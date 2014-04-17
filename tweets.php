<?php 
session_start();
require_once("twitterauth/twitteroauth.php"); //Path to twitteroauth library
 
//$twitteruser = "kaceykaso";
$twitteruser = html_entity_decode($_POST['screenname']);
$notweets = 30;

// add twitter auth keys here
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
//Go through tweets
foreach($response as $tweet)
{
  $time = "{$tweet->created_at}"; //Save latest time
  $tweets[] = "{$tweet->text}"; //Store tweet text
}
$tweets_count = count($tweets); //Count da tweets!
$reply = "We scored the last $tweets_count tweets by @$twitteruser, ending at $time:"; //Tell user whats what

// Import txt
ini_set("auto_detect_line_endings", 1);
$scores = array(); //this is where data from data.txt will be stored

$handle = fopen("AFIN-111.csv","r") or die("EPIC FAIL!"); //fixed filename CSV error

while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
	//$row = explode(",", $data);
	$word = trim(strtolower($row[0]));
	$scores[$word] = trim($row[1]);
}

fclose($handle);


$score = 0; //Starting score
$stream = "";

// Search for words in tweets
for($t=0;$t<$tweets_count;$t++) {
	$phrase = explode(" ", $tweets[$t]);
	$stream .= "<tr><td>";
	for ($i=0,$max=count($phrase);$i<$max;$i++) {
		if (array_key_exists(strtolower($phrase[$i]), $scores)) {
				$this_score = $scores[$phrase[$i]];
				$score = $score+$this_score;
				// Highlight word according to positive or negative score
				$stream .= "<span ";
				if ($this_score > 0) {
					$stream .= "class=\"text-success\"";
				} else if ($this_score == 0) {
					$stream .= "class=\"text-warning\"";
				} else {
					$stream .= "class=\"text-danger\"";
				}
				$stream .= "><strong>".$phrase[$i]."</strong></span> ";	
				
		} else {
			$stream .= $phrase[$i]." ";
		}
	}
	$stream .= "</td></tr>";
}
$stream .= "</tbody></table>";
$stream_head = "<table class=\"table table-hover table-condensed\"><thead><th>$reply</th></thead><tbody>";
$result_set = $stream_head.$stream;


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="">

    <title>Tweepyness</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/custom.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../js/html5shiv.js"></script>
      <script src="../js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
    	
    	<div class="row featurette">
        <div class="col-lg-7">
          <h2 class="featurette-heading">The verdict is in! <span class="text-muted">Your Tweepyness score is </span><strong class="score"><?php echo $score; ?></strong></h2>
          <p class="lead">
	          <?php $score = intval($score);
					
				switch($score) {
					case $score < -5:
						echo "Damn, lay off the negativity already! >:(<br>Less than negative 5 is pretty negative, no matter who you ask!";
						$picture = "img/meh.png";
						break;
					case $score > -5 && $score < 0:
						echo "Aww, you're kinda negative :-/<br>0 to negative 5 is getting down in the dumps, wouldn't you agree?";
						$picture = "img/meh.png";
						break;
					case $score >= 0 && $score < 5:
						echo "Not bad, seems like you're middle of the road.<br>1 to 5 is moderate no matter who you ask!";
						$picture = "img/ok.png";
						break;
					case $score >= 5 && $score < 10:
						echo "Hey, look at you, Mr. Sunshine! :D<br>5 to 10 is pretty positive on any scale!";
						$picture = "img/happy.png";
						break;
					case $score >= 10:
						echo "You the bomb! You should Monetize!<br><a href=\"//millennialmedia.com\" title=\"Millennialmedia\">Millennialmedia</a> can help";
						$picture = "img/happy.png";
						break;
				}
				?>
          </p>
          <p>
          	<a href="index.php"><button class="btn btn-info" value="Try Different Screenname">Try Different Screenname</button></a>
          </p>
        </div>
        <div class="col-lg-5 pull-right">
          <img class="featurette-image img-responsive" src="<?php echo $picture; ?>">
        </div>
        <br class="clearfix">
      </div>

      <hr class="featurette-divider">
    	
    	
    	<div class="row featurette">
    		<h3 class="inbetween text-center">The Proof is in the Pudding:</h3>
    		<div class="col-md-10 box">
			<?php 
				echo $result_set;
			?>
    		</div>
    	</div>
    	</div><!-- .row -->
    	
    	<div class="footer row">
      	<p class="text-center">
      		A <a href="//www.kaceycoughlin.com" title="Kacey Coughlin Web Design and Development">Kacey Coughlin</a> joint.
      	</p>
      </div>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/bootstrap.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
  </body>
</html>
