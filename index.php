<?php
include 'functions.php';

if(count($_GET) === 1){
	foreach ($_GET as $key => $value) {
		if (validate_link_appearance_only($key)) {
				$l = $key;
				//include 'functions.php';

				$ref = (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : FALSE);
				process_hit($db, $l, $ref);
				die;
		}	
	}
}

session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>AntiPhishing.Link Main</title>
  <meta charset="UTF-8">
  <meta name="description" content="Free microservice to prevent phishing">
  <meta name="keywords" content="phishing,anti phishing,antiphishing,anti-phishing,email security">
  <meta name="author" content="daniel reece">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

<script src='https://www.google.com/recaptcha/api.js'></script>
<style>
* {
  box-sizing: border-box;
}

body {
  background-color: #474e5d;
  font-family: Helvetica, sans-serif;
}

/* The actual timeline (the vertical ruler) */
.timeline {
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
}
.dabumf {
	text-align: center;
	color: white;
}
q {
    font-style: italic;
}

input[type=text], select {
  width: 100%;
  padding: 12px 20px;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

input[type=submit] {
  width: 100%;
  background-color: #f57931;
  color: white;
  padding: 14px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type=submit]:hover {
  background-color: #c54f0a;
}

.button {
  background-color: #f57931;
  border: none;
  color: white;
  padding: 15px 25px;
  text-align: center;
  font-size: 16px;
  cursor: pointer;
  border-radius: 15px;
  
}
.smallbutton {
  background-color: #f57931;
  border: none;
  color: white;
  padding: 2px 8px;
  text-align: center;
  font-size: 16px;
  cursor: pointer;
  border-radius: 15px;
  
}

.smallbutton:hover {
  background-color: #c54f0a;
}
.button:hover {
  background-color: #c54f0a;
}

		.black_overlay{
			display: none;
			position: fixed;
			top: 0%;
			left: 0%;
			width: 100%;
			height: 100%;
			background-color: black;
			z-index:1001;
			-moz-opacity: 0.8;
			opacity:.80;
			filter: alpha(opacity=80);
		}
		.white_content {
			display: none;
			position: fixed;
			top: 25%;
			left: 25%;
			width: 50%;
			height: 60%;
			padding: 16px;
			border: 16px solid white;
			background-color: #474e5d;
			z-index:1002;
			overflow: auto;
			border-radius: 15px;
		}

/* The actual timeline (the vertical ruler) */
.timeline::after {
  content: '';
  position: absolute;
  width: 6px;
  background-color: white;
  top: 0;
  bottom: 0;
  left: 50%;
  margin-left: -3px;
}

/* Container around content */
.container {
  padding: 10px 40px;
  position: relative;
  background-color: inherit;
  width: 50%;
}

/* The circles on the timeline */
.container::after {
  content: '';
  position: absolute;
  width: 25px;
  height: 25px;
  right: -17px;
  background-color: white;
  border: 4px solid #FF9F55;
  top: 15px;
  border-radius: 50%;
  z-index: 1;
}

/* Place the container to the left */
.left {
  left: 0;
}

/* Place the container to the right */
.right {
  left: 50%;
}

/* Add arrows to the left container (pointing right) */
.left::before {
  content: " ";
  height: 0;
  position: absolute;
  top: 22px;
  width: 0;
  z-index: 1;
  right: 30px;
  border: medium solid white;
  border-width: 10px 0 10px 10px;
  border-color: transparent transparent transparent white;
}

h1{
  font-size: 77px;
  color:#f57931;
  text-align: center;
}
h3{
  font-size: 44px;
  color:white;
  text-align: center;
}

/* Add arrows to the right container (pointing left) */
.right::before {
  content: " ";
  height: 0;
  position: absolute;
  top: 22px;
  width: 0;
  z-index: 1;
  left: 30px;
  border: medium solid white;
  border-width: 10px 10px 10px 0;
  border-color: transparent white transparent transparent;
}

/* Fix the circle for containers on the right side */
.right::after {
  left: -16px;
}

/* The actual content */
.content {
  padding: 20px 30px;
  background-color: white;
  position: relative;
  border-radius: 6px;
}

/* Media queries - Responsive timeline on screens less than 600px wide */
@media screen and (max-width: 600px) {
  /* Place the timelime to the left */
  .timeline::after {
  left: 31px;
  }
  
  /* Full-width containers */
  .container {
  width: 100%;
  padding-left: 70px;
  padding-right: 25px;
  }
  
  /* Make sure that all arrows are pointing leftwards */
  .container::before {
  left: 60px;
  border: medium solid white;
  border-width: 10px 10px 10px 0;
  border-color: transparent white transparent transparent;
  }

  /* Make sure all circles are at the same spot */
  .left::after, .right::after {
  left: 15px;
  }
  
  /* Make all right containers behave like the left ones */
  .right {
  left: 0%;
  }
}

/* The alert message box */
.success {
  padding: 20px;
  background-color: #4CAF50; /* Green */
  color: white;
  margin-bottom: 15px;
}

.alert {
  padding: 20px;
  background-color: #f44336; /* Red */
  color: white;
  margin-bottom: 15px;
}


/* The close button */
.closebtn {
  margin-left: 15px;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

/* When moving the mouse over the close button */
.closebtn:hover {
  color: black;
}
</style>
</head>

<body>
<?php

if(!empty($_GET['verify']) && !is_array($_GET['verify'])){ 
	if (validate_link_appearance_only($_GET['verify'])) {
		//$verify 0=fail,1=success,-1=already-registered
		$verify_result = process_verify($db, $_GET['verify']);
		if ($verify_result == -1){
			echo '
			<div class="alert">
			  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
			  <strong>Error!</strong> Link already verified.
			</div>';
		}
		if ($verify_result == 1){
			echo '
			<div class="success">
			  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
			  <strong>Success!</strong> Link verified. We\'ll monitor it and let you know if we see anything phishy.
			</div>';
		}
		if ($verify_result == 0){
			echo '
			<div class="alert">
			  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
			  <strong>Error!</strong> Link invalid.
			</div>';
		}
	}
	else{
		$verify_result = 0;
		echo '
			<div class="alert">
			  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
			  <strong>Error!</strong> Link invalid.
			</div>';
	}
}

if (!empty($_SESSION["link"]) && !empty($_POST['email'])){

	$die = 0;
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		echo '
		<div class="alert">
		  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
		  <strong>Error!</strong> We couln\'t validate that email.
		</div>';
		$die = 1;
	}
	if (!empty($_POST['linkname'])){
		if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['linkname']) or strlen($_POST['linkname']) > 20){
			echo '
			<div class="alert">
			  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
			  <strong>Error!</strong> Link names can only contain letters and numbers up to 20 characters long.
			</div>';
			$die = 1;
		}
	}
	//include 'functions.php';
	if (get_link_info($db, $_SESSION["link"]) != FALSE){
	  echo '
		<div class="alert">
		  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
		  <strong>Error!</strong> That link is taken. (you pressed the reload button, didn\'t you)
		</div>';
	  $die = 1;
	}
	if(!validate_recaptcha()){
		echo '
		<div class="alert">
		  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
		  <strong>Error!</strong> Failed captcha.
		</div>';
	  $die = 1;
	}
	if ($die != 1){ 
		echo '
		<div class="success">
		  <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span> 
		  <strong>Thanks!</strong> <u>https://antiphishing.link/?' . $_SESSION["link"] . '</u> is yours. We\'ve sent you an email to active it.
		</div>';
		
		add_new_link($db, $_SESSION["link"],$_POST['email'], $_POST['linkname']);
		$_SESSION["link"] = generate_link();
	}
}
else{
	$_SESSION["link"] = generate_link();
}


?>


<h1>Anti-Phishing Link</h1>
<div class="dabumf">
Anti-Phishing Link is a free service to detect basic phishing attacks against your website in real-time.<p>
<u>In most cases, it does this before they have even started.</u><p>
It was created by computer security nerd Daniel Reece (@HBRN8) in two sleepless nights, and has so-far helped <font color="#f57931"><?php draw_unique_users($db);?></font> people fight their phishing attacks.
</div>
<h3>How does it work?</h3>
<div class="timeline">
  <div class="container left">
    <div class="content">
      <h2>15th Jan 2019 3:00pm</h2>
      <p>On a whim, you clicked <button class="smallbutton" href="javascript:void(0)" onclick="document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'">Generate My Anti-Phishing Link</button> on this site, and embedded it (as an image) into  your organisation's website pages. Maybe just a few of the sensitive ones. Like that 'Login' page.<p>All we asked you for was an email.</p>
    </div>
  </div>
  <div class="container right">
    <div class="content">
      <h2>3rd March 2019 1:00am</h2>
      <p><img src="https://i.kym-cdn.com/entries/icons/original/000/021/807/4d7.png" width="70" style="
float:left;padding:5px"><b>Hackerman</b> decides to phish your site. 'Login.aspx' seems the obvious choice.
He clones the page-source because, well, it looks like your site... why re-invent the wheel.</p>
    </div>
  </div>
  <div class="container right">
    <div class="content">
      <h2>3rd March 2019 1:30am</h2>
      <p><img src="https://i.kym-cdn.com/entries/icons/original/000/021/807/4d7.png" width="70" style="
float:left;padding:5px">He changes the form's action, mends some broken links, and the phishing page is g2g. He quickly checks it works and then goes to sleep.</p>
    </div>
  </div>
<div class="container left">
    <div class="content">
      <h2>3rd March 2019 1:31am</h2>
      <p>Moments later you recieve an email from <b>alert@antiphishing.link</b>:<p><img src="data:image/png;base64,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" width="70" style="
float:right;padding:5px">
	<q> A new Referer address of:
	<br>
	<b>http://hackerman.com/fakelogin/</b>
	<br><br>
	was observed on your link:
	<br>
	<b>TestLink1</b> </q></p>
    </div>
    </div>
<div class="container left">
    <div class="content">
      <h2>3rd March 2019 8:12am</h2>
      <p>Over your morning coffee, you block the referering domain from your organisation's mail. Just incase.</p>
    </div>
  </div> 
  <div class="container right">
    <div class="content">
      <h2>3rd March 2019 10:00am</h2>
      <p><img src="https://i.kym-cdn.com/entries/icons/original/000/021/807/4d7.png" width="50" style="
float:left;padding:5px">Feeling fresh, Hackerman emails a few victims his phishing link and waits in anticipation.</p>
    </div>
  </div>
  <div class="container right">
    <div class="content">
      <h2>3rd March 2019 12:00pm</h2>
      <p><img src="https://i.kym-cdn.com/entries/icons/original/000/021/807/4d7.png" width="50" style="
float:left;padding:5px">Nothing yet...</p>
    </div>
  </div>
 <div class="container left">
    <div class="content">
      <h2>3rd March 2019 12:15pm</h2>
      <p>You have sushi for lunch.</p>
    </div>
  </div>
  <div class="container right">
    <div class="content">
      <h2>3rd March 2019 2:00pm</h2>
      <p><img src="https://i.kym-cdn.com/entries/icons/original/000/021/807/4d7.png" width="50" style="
float:left;padding:5px">Hackerman is bored.</p>
    </div>
  </div>
  <div class="container right">
    <div class="content">
      <h2>3rd March 2019 5:00pm</h2>
      <p><img src="https://i.kym-cdn.com/entries/icons/original/000/021/807/4d7.png" width="50" style="
float:left;padding:5px">Hackerman doesn't want to play this game anymore.</p>
    </div>
  </div>

</div>
<h3>Sign-Up</h3>
<div class="dabumf">
	There is no sign-up. You give us an email when you generate a link... that's it.<p>
<button class="button" href="javascript:void(0)" onclick="document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'">Generate My Anti-Phishing Link</button>
</div>
<h3>FAQ</h3>
<div class="dabumf">
	• You can have as many links as you like (within reason).<br>
	• Referer information is stored as hashes, as are the links themselves.<br>
	• There are intentions to add more features, potentially for a fee, but basic link-monitoring will always be free.<br>
	• If you lose access to your email, your link is dead - just generate a new one.
</div>

		<div id="light" class="white_content">
			<h2 style="color:white">Done! Claim this link with just an email address:</h2>

			<h2 style="color:#f57931"><?php print("https://antiphishing.link/?" . $_SESSION["link"]);?></h2>

			<form method="post" action="">
			  <br>
			  <input type="text" name="email" placeholder="enter your email address:">
			  <br>
			  <br>
			  <input type="text" name="linkname" placeholder="enter a name for your link (optional):">
			  <br><br>
			  <div class="g-recaptcha" data-sitekey="6LcObYoUAAAAAJoQAS1OzcVhBZthth068xPfDFK-"></div>

			  <input type="submit" value="Submit">

			</form> 


			
			<a href = "javascript:void(0)" style="text-align: center;color: white;" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
			
		</div>
		
		<div id="fade" class="black_overlay"></div>

<footer>
</footer>
</body>
</html>
