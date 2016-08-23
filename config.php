<?php
/*
configuration parameters for ECWID integration

CREATE TABLE IF NOT EXISTS `payments` (
  `ID` int(11) NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `x_reference` bigint(15) NOT NULL,
  `public_key` varchar(100) NOT NULL,
  `secret_key` varchar(100) NOT NULL,
  `x_test` varchar(20) NOT NULL,
  `x_url_callback` varchar(200) NOT NULL,
  `x_amount` float NOT NULL,
  `x_currency` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

 */
$db_host='127.0.0.1';
$db_username='ecwid';
$db_password='secret';
$db_name='ecwid';

?>
