<?php

header("Content-type: text/xml");
function return_result($data)
{
	$xml_fields = "\n";
	foreach ($data as $name=>$value)
		$xml_fields .= "<".$name.">".$value."</".$name.">\n";

	$xml = '<?xml version="1.0" encoding="windows-1251"?>
<response>'.$xml_fields.'</response>';
	return $xml;
}

if (!isset($_GET["command"]))
{
	//Handling situation when 'command' parameter is not passed
	exit();
}

$secret_key='|q7;<@[W'; // secret key specified in the project settings,

$command = $_GET["command"]; //Request type (check/pay)
$account = $_GET["account"]; //Payment ID (main identification parameter)

//$qxt_    = $_GET["qxt_ "]; //Additional identification parameters specified in the project settings. These paramemters get tranfered with qxt_ prefix. All additional parameters, added in the project settings are being sent.

$test    = $_GET["test"]; //Only transferred in test transactions. If parameter test is present user should not get virtual currency transferred.


foreach($_GET as $get_param_name=>$get_param_value)
{
	if ($get_param_name!="sign" && $get_param_name!="command" && $get_param_name!="test")
	{
		$names_to_sign[] = $get_param_name;
	}
}

sort($names_to_sign); // String, that will be signed is formed from values of parameters, with alphabetical order of parameter names

$sign_string='';

foreach($names_to_sign as $name)
{
	$sign_string .= $_GET[$name];
}

$sign_string = $command.$sign_string.$secret_key;
$sign = md5($sign_string);

if ($_GET["sign"]!=$sign)
{
	$data = Array(
		"result" => 3, //Invalid md5
		"comment" => "Invalid MD5"
	);

	if ("pay" == $command)
	{
		$data["id"] = (int)$_GET["id"];
		$data["merchant_id"] = 0;
		$data["sum"] = 0;
	}

	$response = return_result($data);
	echo $response;
	return;
}

switch ($command)
{
	case "check":

		// Init values
		$your_comment = "";
		$your_result_code = 0;

		// Here you need to check payment parameters...
		// ...and prepare result code in $your_result_code variable (codes are listed below)
		// Also, you may provide a comment in $your_comment variable

		//  0	Payment identification parameters are correct. Payment can be done.
		//  2	Payment identification parameters are incorrect
		//  3	Invalid md5
		//  7	Payment with specified identification parameters cannot be done for technical reasons

		$response = return_result(Array(
		                          "result" => $your_result_code,
		                          "comment" => $your_comment
		                          ));
		echo $response;
		return;

		break;

	case "pay":
		$id               = $_GET["id"];//Qutella transaction ID
		$merchant_id      = $_GET["merchant_id"];//Projects transaction ID. Is only sent in repeating request. Is not sent in initial request or value sent is blank.
		$sum              = $_GET["sum"];//Payment sum to be converted into virtual currency
		$user_fee         = $_GET["user_fee"];//Transaction fee paid by user
		$client_sum       = $_GET["client_sum"];//Payment sum for the payout
		$fee              = $_GET["fee"];//Transaction fee paid by project
		$user_paid        = $_GET["user_paid"];//Payment sum paid by user
		$pay_system_id    = $_GET["pay_system_id"];//ID of payment method used
		$price            = $_GET["price"];//Price of the virtual currency unit on the moment of invoice creation
		$currency_id      = $_GET["currency_id"];//Payment currency ID
		$rate             = $_GET["rate"];//Currency exchange rate to the rate of virtual currency
		$product_amount   = $_GET["product_amount"];//Number of virtual currency units to be transferred to user
		$date             = $_GET["date"];//Date and time of payment


		// Init values
		$your_comment = "";
		$your_result_code = 0;
		$your_system_transaction_id = 0;

		// Here you need to process a transaction and transfer virtual product to customer
		// You need to return result code in variable $your_result_code (codes are listed below) and
		// transaction id in your system in $your_system_transaction_id variable
		// If 'merchant_id' parameter is provided, $your_system_transaction_id should be equal to it
		// Also, you may provide a comment in $your_comment variable

		//  0	Success
		//  1	Temporary error, please try again later
		//  2	Payment identification parameters are incorrect
		//  3	Invalid MD5
		//  4	Invalid request (invalid sum, all or some of required parameters are not present)
		//  5	Other error
		//  7	Payment with specified identification parameters cannot be done for technical reasons

		if (!isset($merchant_id)) $project_transaction_id = $your_system_transaction_id;
		else $project_transaction_id = $merchant_id;

		$response = return_result(Array(
		                          "id"=>(int)$id,
		                          "merchant_id"=>$project_transaction_id,
		                          "sum"=>$sum,
		                          "result"=>$your_result_code,
		                          "comment" => $your_comment
		                          ));

		echo $response;
		break;
}

?>
