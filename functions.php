<?php

$debug = TRUE;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";

if ($debug === TRUE){
  $username = "root";
  $password = "";
}
else{
  $username = "phpmyadmin";
  $password = "Bf7wgmZxUYzZ";
}


$db = mysqli_connect($servername, $username, $password, 'antiphishinglink');

if (!$db) {
    die();
    //die("Connection failed: " . mysqli_connect_error());
}

function validate_link_appearance_only($link){
  if (ctype_xdigit($link) && strlen($link) % 2 == 0) {
      if (strlen(hex2bin($link)) == 16){
        return TRUE;  
      }
      return FALSE;
  }
  return FALSE;
}

function process_verify($db, $l){

if (!($stmt = $db->prepare("SELECT antiphishinglink_id, active FROM `antiphishinglinks` WHERE `verifykey` = ?"))) {
    echo "Prepare failed: (" . $db->errno . ") " . $db->error;
}
$stmt->bind_param('s',$l);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $active);
$stmt->fetch();

if($active == 1){
  return -1;
}
elseif ($active == 0) {

  $sql = "UPDATE `antiphishinglinks` SET `active` = '1' WHERE `antiphishinglinks`.`antiphishinglink_id` = '".$id."'";
  if (!($result = $db->query($sql))) {
    echo "Prepare failed: (" . $db->errno . ") " . $db->error;
  }
    return 1;
}
}


function draw_unique_users($db){

$sql = "SELECT count( DISTINCT(`antiphishinglink_email`) ) FROM `antiphishinglinks` AS `asd`";
if ($result = $db->query($sql)) {

    $row = $result->fetch_row(); // Fetch the first row
    echo $row[0]; // Print the 1st column
    /* free result set */
    $result->close();
}
}

function generate_link(){
  return bin2hex(openssl_random_pseudo_bytes(16));
}

function validate_recaptcha(){
  $debug = TRUE;
  if ($debug === TRUE){ return TRUE;}

$captcha = $_POST["g-recaptcha-response"];
$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcObYoUAAAAABPAR_bKH44fN1yc5hJw3OqR7_Oc&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
        if($response['success'] == TRUE)
        {
          return TRUE;
        }
        else
        {
          return FALSE;
        }
}

function process_hit($db, $l, $referer){

$data = (get_link_info($db, $l));

if ($data['active'] === 1){
  $id = $data['id'];
  $email = $data['email'];
  $name = $data['name'];

  if (!is_known_referer($db, $id, $referer)){
    
    //print ('you must be new here!');
    add_referer_entry($db, $id, $referer);
    send_alert_via_email($email,htmlentities($referer),$name);
  }
}else{
  print('link not active');
}
}

function send_verifylink_via_email($email,$name, $v){

  $to = $email;
  $subject = 'Anti-Phishing Link Verification';
  $headers = 'From: AntiPhishing.Link <noreply@AntiPhishing.Link>' . "\r\n";
  
  $message = "
  A new Anti-Phishing Link was generated using your email. To activate this link, click below:\r\n\r\n

  https://AntiPhishing.Link/?verify=".$v." . \r\n\r\n

  If you did not generate this link, you can ignore this email.
  ";

mail($to, $subject, $message, $headers);

}

function send_alert_via_email($email,$referer,$name){

  $to = $email;
  $subject = 'Anti-Phishing Alert';
  $headers = 'From: AntiPhishing.Link <alert@AntiPhishing.Link>' . "\r\n";
  
  $message = "
  A new Referer address of: \r\n
  ".$referer."  \r\n
  was observed on your link: \r\n
  ".$name ."
  ";

mail($to, $subject, $message, $headers);

}

function add_referer_entry($db, $id, $referer){

if (!($stmt = $db->prepare("INSERT INTO hits(antiphishinglink_id, referer) VALUES (?,?)"))) {
    echo "Prepare failed: (" . $db->errno . ") " . $db->error;
}
$stmt->bind_param('ds',$id, $referer);
$stmt->execute();
}



function is_known_referer($db, $id, $referer){

if (!($stmt = $db->prepare("SELECT EXISTS(SELECT * FROM `hits` WHERE `antiphishinglink_id` = ? AND `referer` = ?)"))) {
    echo "Prepare failed: (" . $db->errno . ") " . $db->error;
}
$stmt->bind_param('ds',$id, $referer);
$stmt->execute();
$stmt->bind_result($result);
$stmt->fetch();

if($result){
  return TRUE;
}
else{
  return FALSE;
}
}


function add_new_link($db, $l, $e, $n){

$v = generate_link();

if (!($stmt = $db->prepare("INSERT INTO antiphishinglinks(antiphishinglink, antiphishinglink_email, antiphishinglink_name, verifykey ) VALUES (?,?,?,?)"))) {
    echo "Prepare failed: (" . $db->errno . ") " . $db->error;
}
$stmt->bind_param('ssss',$l, $e, $n, $v);
$stmt->execute();

send_verifylink_via_email($e, $n, $v);

}

function get_link_info($db, $l){

if (!($stmt = $db->prepare("SELECT antiphishinglink_id, antiphishinglink_email, antiphishinglink_name, active  FROM `antiphishinglinks` WHERE `antiphishinglink` = ?"))) {
    echo "Prepare failed: (" . $db->errno . ") " . $db->error;
}
$stmt->bind_param('s',$l);
$stmt->execute();
$stmt->bind_result($id, $email, $name, $active);
$stmt->fetch();

if($id){
  $out['id'] = $id;
  $out['email'] = $email;
  $out['name'] = $name;
  $out['active'] = $active;
  return $out;
}
else{
  return FALSE;
}
}

function process_observed_link(){

}

?>