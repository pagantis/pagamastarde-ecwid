<?php
/*
shopify callback script
http://demoshop.pagamastarde.com/ecwid/return.php
*/
require_once ("config.php");
//recevice original pmt notification

//recover all saved data form the order
$db = mysqli_init();
$link = $db->real_connect ($db_host, $db_username,$db_password,$db_name);
if (!$link)
{
    die ('Connect error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error() . "\n");
}
$db->set_charset("utf8");
$sql="SELECT * FROM `payments` where x_reference = '".$_REQUEST['orderId']."' order by id desc limit 1";

if ($result = $db->query($sql)) {
  if ($myrow = $result->fetch_array(MYSQLI_ASSOC)) {
      $data =$myrow;
  }
}else{
  die("SQL error ".$db->error);
}

header("Location: ".htmlspecialchars_decode($data['x_return_url']));


?>
