<?php
/*
ecwid redirect script
*/

require_once ("config.php");

//method to calculate pmt hash
$account_seperator = '@';
$settings= explode($account_seperator,$_POST['x_login']);
$public_key = $settings[0];
$secret_key = $settings[1];

$base_url = '';
// account hardcoding. Needed to set up the urls
require_once ("accounts.php");

$order_id = $_POST['x_invoice_num'];
$amount = $_POST['x_amount']*100;
$currency = $_POST['x_currency_code'];
$desciption = $_POST['x_description'];
$ok_url='https://demoshop.pagamastarde.com/ecwid/return.php?orderId='.$order_id;
$ko_url=$base_url . '/#!/~/cart';
$callback_url='https://demoshop.pagamastarde.com/ecwid/callback.php';
$full_name = $_POST['x_first_name']." ".$_POST['x_last_name'];
$email = $_POST['x_email'];
$street = $_POST['x_address'];
$city = $_POST['x_city'];
$province = $_POST['x_state'];
$zipcode = $_POST['x_zip'];
$phone = $_POST['x_phone'];
$test = $_POST['x_test_request'];
$dataToEncode = $secret_key . $public_key . $order_id . $amount . $currency . $ok_url . $ko_url . $callback_url;
//pmt hash
$signature = sha1($dataToEncode);

//in order to send proper values of the callback, we need to save values in database to grab it on return.
$db = mysqli_init();
$link = $db->real_connect ($db_host, $db_username,$db_password,$db_name);
if (!$link)
{
    die ('Connect error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error() . "\n");
}
$db->set_charset("utf8");
$sql="INSERT INTO `ecwid`.`payments` (`ID`, `insert_date`, `x_reference`, `public_key`, `secret_key`, `x_test`, `x_url_callback`, `x_amount`, `x_currency`,`base_url` ) VALUES
(NULL,
CURRENT_TIMESTAMP,
'".$order_id."',
'".$public_key."',
'".$secret_key."',
'".$test."',
'".$_POST['x_relay_url']."',
'".$_POST['x_amount']."',
'".$_POST['x_currency']."',
'".$base_url."')";
if ($db->query($sql) === TRUE) {

}else{
  die("SQL error ".$db->error);
}

?>
<!-- pmt form -->
<form action="https://pmt.pagantis.com/v1/installments" method="post" id="form">
  <!-- datos de la transacción -->
<input name="order_id" type="hidden" value="<?php echo $order_id; ?>" />
<input name="amount" type="hidden" value="<?php echo $amount; ?>" />
<input name="currency" type="hidden" value="<?php echo $currency; ?>" />
<input name="description" type="hidden" value="<?php echo $desciption; ?>" />

<!-- URLs de retorno -->
<input name="ok_url" type="hidden" value="<?php echo $ok_url; ?>" />
<input name="nok_url" type="hidden" value="<?php echo $ko_url; ?>" />


<!-- datos del usuario -->
<input name="full_name" type="hidden" value="<?php echo $full_name; ?>">
<input name="email" type="hidden" value="<?php echo $email; ?>">

<!-- direccion del usuario, opcional -->
<input name="address[street]" type="hidden" value="<?php echo $street; ?>">
<input name="address[city]" type="hidden" value="<?php echo $city; ?>">
<input name="address[province]" type="hidden" value="<?php echo $province; ?>">
<input name="address[zipcode]" type="hidden" value="<?php echo $zipcode; ?>">

<!-- télefono móvil, opcional -->
<input name="mobile_phone" type="hidden" value="<?php echo $phone; ?>">

<!-- callback, opcional -->
<input name="callback_url" type="hidden" value="<?php echo $callback_url; ?>">

<!-- firma de la operación -->
<input name="account_id" type="hidden" value="<?php echo $public_key; ?>" />
<input name="signature" type="hidden" value="<?php echo $signature; ?>" />

<!-- automatically redirect to pmt -->
Redirecting to Paga+Tarde... if the page do not reload automaticaly, click <a href="javascript:document.getElementById('form').submit();">here</a>


</form>
<script>
  setTimeout(function(){
    document.getElementById('form').submit();
  },1000);  // 2000 is the delay in milliseconds
</script>
