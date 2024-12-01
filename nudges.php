<?php
// minute(s), hour(s), day(s), month(s), day(s) of week (Sun = 0)
$nudges = array(
	array( 'cron' => '00 09,11,14,16 * * 1-5', 'exp'  => '30', 'desc' => 'HYDRATE!' ),
	array( 'cron' => '00,05,10,15,20,25,30,35,40,45,50,55 * * * *', 'exp'  => '15', 'desc' => 'Every 5mins' ),
);
