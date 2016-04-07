<?php
/*
shopify callback script
*/
require_once ("config.php");
//recevice original pmt notification
$json = file_get_contents('php://input');
$temp = json_decode($json,true);

//recover all saved data form the order
$db = mysqli_init();
$link = $db->real_connect ($db_host, $db_username,$db_password,$db_name);
if (!$link)
{
    die ('Connect error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error() . "\n");
}
$db->set_charset("utf8");
$sql="SELECT * FROM `payments` where x_reference = '".$temp['data']['order_id']."' order by id desc limit 1";

if ($result = $db->query($sql)) {
  if ($myrow = $result->fetch_array(MYSQLI_ASSOC)) {
      $data =$myrow;
  }
}else{
  die("SQL error ".$db->error);
}
//Paga+Tarde notification validation
$signature_check = sha1($data['secret_key'].$data['public_key'].$temp['api_version'].$temp['event'].$temp['data']['id']);
if ($signature_check != $temp['signature'] ){
  die("Hack detected");
}




// x_response_code must = 1 for the cart to update with an approved sale, your script should determine this before hand
$x_response_code = '1';
// x_response_reason_code must = 1  for the cart to update with an approved sale, your script should determine this before hand
$x_response_reason_code = '1';
// your script needs to obtain the banks reference number and put that into this variable
// change banks reference to the reference from the merchant
$x_trans_id = $temp['data']['id'];
// your script needs to supply the original invoice number that you requested from ecwid at the beginning of the process
// change invoice number to the original number you received
$x_invoice_num = $data['x_reference'];
// change total paid to the total paid. Please note it must match the original total that ecwid sent at the beginning
$x_amount = $data['x_amount'];

// Do not change anything below this line, other than your_store_id_#
$hash_value='1234567890';
$x_login = $data['public_key']. '@'. $data['secret_key'];
$string = $hash_value.$x_login.$x_trans_id.$x_amount;
$x_MD5_Hash = md5($string);
$datatopost = array (
"x_response_code" => $x_response_code,
"x_response_reason_code" => $x_response_reason_code,
"x_trans_id" => $x_trans_id,
"x_invoice_num" => $x_invoice_num,
"x_amount" => $x_amount,
"x_MD5_Hash" => $x_MD5_Hash,
);

if ($temp['event'] ==  "charge.created")
{
    $url = $data['x_url_callback'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datatopost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}

$i = strpos($response,'href=');
$j = strpos($response,'"',$i+6);
echo $j;

$url = substr ($response,$i+6, $j-($i+6) );

$sql="UPDATE `payments` set x_return_url = '".$url."' where x_reference = '".$temp['data']['order_id']."'";
if ($db->query($sql) === TRUE) {

}else{
  die("SQL error ".$db->error);
}



?>
