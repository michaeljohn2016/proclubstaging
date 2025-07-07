<?
	// Load up the PayPal REST API SDK
	require 'vendor/autoload.php';
	define('PP_CONFIG_PATH', __DIR__);
	use PayPal\Auth\OAuthTokenCredential;
	use PayPal\Api\Payment;
	use PayPal\Api\Payer;
	use PayPal\Api\Amount;
	use PayPal\Api\Transaction;
	use PayPal\Api\RedirectUrls;
	use PayPal\Rest\ApiContext;
	use PayPal\Api\FundingInstrument;
	session_start();
	
	$clientID = "AWZlMBB8obtzln7DyHNWAUo_CgrMViv66QSitshhf2mD9WfhIdL2zc2MV5f7";
	// Obtain access token for making a payment call
	$oauthCredential = new OAuthTokenCredential($clientID, "EFG2eRDKGeFtBipT4N7eY4amFXmDgD8dnpdDK8Ju64I_95I3XRXH3arKhXDm");
	$accessToken = $oauthCredential->getAccessToken(array('mode' => 'sandbox'));
	

	// Set up the PayPal payment
	$payer = new Payer();
	$payer->setPayment_method('paypal');

	// ### Amount
	$amount = new Amount();
	$amount->setCurrency("USD");
	$amount->setTotal("1.00");
	
	// ### Transaction
	$transaction = new Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription("This is the payment description.");
	
	// ### Redirect urls
	$baseUrl = getBaseUrl();
	$redirectUrls = new RedirectUrls();
	$redirectUrls->setReturn_url("$baseUrl/test-store.php?success=true");
	$redirectUrls->setCancel_url("$baseUrl/test-store.php?success=false");
	
	// ### Payment
	$payment = new Payment();
	$payment->setIntent("sale");
	$payment->setPayer($payer);
	$payment->setRedirect_urls($redirectUrls);
	$payment->setTransactions(array($transaction));
	
	// ### Api Context
	// Pass in a `ApiContext` object to authenticate 
	// the call and to send a unique request id 
	// (that ensures idempotency). The SDK generates
	// a request id if you do not pass one explicitly. 
	$apiContext = new ApiContext($oauthCredential, 'Request' . time());
	
	// ### Create Payment
	// Create a payment by posting to the APIService
	// using a valid apiContext
	// The return object contains the status and the
	// url to which the buyer must be redirected to
	// for payment approval
	try {
		$payment->create($apiContext);
	} catch (\PPConnectionException $ex) {
		echo "Exception: " . $ex->getMessage() . PHP_EOL;
		var_dump($ex->getData());	
		exit(1);
	}
	
	// ### Redirect buyer to paypal
	// Retrieve buyer approval url from the `payment` object.
	foreach($payment->getLinks() as $link) {
		if($link->getRel() == 'approval_url') {
			$redirectUrl = $link->getHref();
		}
	}
	// It is not really a great idea to store the payment id
	// in the session. In a real world app, please store the
	// payment id in a database.
	$_SESSION['paymentId'] = $payment->getId();
	if(isset($redirectUrl)) {
		header("Location: $redirectUrl");
		exit;
	}
	
	//print_r($payment);
	
	function getBaseUrl() {

	$protocol = 'http';
	if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')) {
		$protocol .= 's';
		$protocol_port = $_SERVER['SERVER_PORT'];
	} else {
		$protocol_port = 80;
	}

	$host = $_SERVER['HTTP_HOST'];
	$port = $_SERVER['SERVER_PORT'];
	$request = $_SERVER['PHP_SELF'];
	return dirname($protocol . '://' . $host . ($port == $protocol_port ? '' : ':' . $port) . $request);
}

?>