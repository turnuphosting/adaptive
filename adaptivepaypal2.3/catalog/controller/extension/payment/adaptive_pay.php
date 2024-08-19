<?php
class ControllerExtensionPaymentAdaptivePay extends Controller {
   function generateCharacter () {
		$possible = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		return $char;
	}

	function generateTrackingID () {
		$GUID = $this->generateCharacter().$this->generateCharacter().$this->generateCharacter().$this->generateCharacter().$this->generateCharacter();
		$GUID .= $this->generateCharacter().$this->generateCharacter().$this->generateCharacter().$this->generateCharacter();
		return $GUID;
	}
	
	
public function CallPay( $actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray,
						$receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl,
						$memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId )
	{
		/* Gather the information to make the Pay call.
			The variable nvpstr holds the name value pairs
		*/
		
		// required fields
		$nvpstr = "actionType=" . urlencode($actionType) . "&currencyCode=" . urlencode($currencyCode);
		$nvpstr .= "&returnUrl=" . urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);

		if (0 != count($receiverAmountArray))
		{
			reset($receiverAmountArray);
			while (list($key, $value) = each($receiverAmountArray))
			{
				if ("" != $value)
				{
					$nvpstr .= "&receiverList.receiver(" . $key . ").amount=" . urlencode($value);
				}
			}
		}

		if (0 != count($receiverEmailArray))
		{
			reset($receiverEmailArray);
			while (list($key, $value) = each($receiverEmailArray))
			{
				if ("" != $value)
				{
					$nvpstr .= "&receiverList.receiver(" . $key . ").email=" . urlencode($value);
				}
			}
		}

		if (0 != count($receiverPrimaryArray))
		{
			reset($receiverPrimaryArray);
			while (list($key, $value) = each($receiverPrimaryArray))
			{
				if ("" != $value)
				{
					$nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").primary=" . urlencode($value);
				}
			}
		}

		if (0 != count($receiverInvoiceIdArray))
		{
			reset($receiverInvoiceIdArray);
			while (list($key, $value) = each($receiverInvoiceIdArray))
			{
				if ("" != $value)
				{
					$nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").invoiceId=" . urlencode($value);
				}
			}
		}
	
		// optional fields
		if ("" != $feesPayer)
		{
			$nvpstr .= "&feesPayer=" . urlencode($feesPayer);
		}

		if ("" != $ipnNotificationUrl)
		{
			$nvpstr .= "&ipnNotificationUrl=" . urlencode($ipnNotificationUrl);
		}

		if ("" != $memo)
		{
			$nvpstr .= "&memo=" . urlencode($memo);
		}

		if ("" != $pin)
		{
			$nvpstr .= "&pin=" . urlencode($pin);
		}

		if ("" != $preapprovalKey)
		{
			$nvpstr .= "&preapprovalKey=" . urlencode($preapprovalKey);
		}

		if ("" != $reverseAllParallelPaymentsOnError)
		{
			$nvpstr .= "&reverseAllParallelPaymentsOnError=" . urlencode($reverseAllParallelPaymentsOnError);
		}

		if ("" != $senderEmail)
		{
			$nvpstr .= "&senderEmail=" . urlencode($senderEmail);
		}

		if ("" != $trackingId)
		{
			$nvpstr .= "&trackingId=" . urlencode($trackingId);
		}

		/* Make the Pay call to PayPal */
		$resArray = $this->hash_call("Pay", $nvpstr);

		/* Return the response array */
		return $resArray;
	}

	public function deformatNVP($nvpstr)
	{
		$intial=0;
	 	$nvpArray = array();

		while(strlen($nvpstr))
		{
			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
	     }
		return $nvpArray;
	}
		
	function hash_call($methodName, $nvpStr)
	{
	
	   
		//declaring of global variables
		global $API_Endpoint, $API_UserName, $API_Password, $API_Signature, $API_AppID;
		global $USE_PROXY, $PROXY_HOST, $PROXY_PORT;
		
		$API_UserName = $this->config->get('adaptive_pay_username');
        $API_Password = $this->config->get('adaptive_pay_password');
        $API_Signature =$this->config->get('adaptive_pay_signature');
		
		
		$PROXY_HOST = '127.0.0.1';
		$PROXY_PORT = '808';

		$Env = $this->config->get('adaptive_pay_test');
		
		$API_AppID = "APP-80W284485P519543T";
		$API_Endpoint = "";

		if ($Env == 1) 
		{
		$API_Endpoint = "https://svcs.sandbox.paypal.com/AdaptivePayments";
		}
		else
		{
		$API_Endpoint = "https://svcs.paypal.com/AdaptivePayments";
		}

		$USE_PROXY = false;
	
	

		$API_Endpoint .= "/" . $methodName;
		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// Set the HTTP Headers
		curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
		'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
		'X-PAYPAL-RESPONSE-DATA-FORMAT: NV',
		'X-PAYPAL-SECURITY-USERID: ' . $API_UserName,
		'X-PAYPAL-SECURITY-PASSWORD: ' .$API_Password,
		'X-PAYPAL-SECURITY-SIGNATURE: ' . $API_Signature,
		'X-PAYPAL-SERVICE-VERSION: 1.3.0',
		'X-PAYPAL-APPLICATION-ID: APP-80W284485P519543T'
		));
	
	    //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
		//Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
		if($USE_PROXY)
			curl_setopt ($ch, CURLOPT_PROXY, $PROXY_HOST. ":" . $PROXY_PORT); 

		// RequestEnvelope fields
		$detailLevel	= urlencode("ReturnAll");	// See DetailLevelCode in the WSDL for valid enumerations
		$errorLanguage	= urlencode("en_US");		// This should be the standard RFC 3066 language identification tag, e.g., en_US

		// NVPRequest for submitting to server
		$nvpreq = "requestEnvelope.errorLanguage=$errorLanguage&requestEnvelope.detailLevel=$detailLevel";
		$nvpreq .= "&$nvpStr";

		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		//getting response from server
		$response = curl_exec($ch);

		//converting NVPResponse to an Associative Array
		$nvpResArray=$this->deformatNVP($response);
		$nvpReqArray=$this->deformatNVP($nvpreq);
		$_SESSION['nvpReqArray']=$nvpReqArray;

		if (curl_errno($ch)) 
		{
			// moving to display page to display curl errors
			  $_SESSION['curl_error_no']=curl_errno($ch) ;
			  $_SESSION['curl_error_msg']=curl_error($ch);

			  //Execute the Error handling module to display errors. 
		} 
		else 
		{
			 //closing the curl
		  	curl_close($ch);
		}

		return $nvpResArray;
	}

	
	
	public function index() {
		$this->language->load('extension/payment/adaptive_pay');
		$data['text_testmode'] = $this->language->get('text_testmode');		
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['testmode'] = $this->config->get('adaptive_pay_test');
		$API_AppID = $this->config->get('adaptive_pay_application_payid');
		
		if (!$this->config->get('adaptive_pay_test')) {
			$Env="";
		} else {
			$Env="sandbox.";
			
		}
		
		$PROXY_HOST = '127.0.0.1';
		$PROXY_PORT = '808';

		
		$API_Endpoint = "";

		if ($Env == "sandbox") 
		{
	     $API_Endpoint = "https://svcs.sandbox.paypal.com/AdaptivePayments";
		}
		else
		{
		$API_Endpoint = "https://svcs.paypal.com/AdaptivePayments";
		}

		$USE_PROXY = false;

		//Default App ID for Sandbox    
		$API_AppID = "APP-80W284485P519543T";

		$API_RequestFormat = "NV";
		$API_ResponseFormat = "NV";
		$actionType			= "PAY";
		$cancelUrl			= $this->url->link('checkout/checkout', '', 'SSL');	// TODO - If you are not executing the Pay call for a preapproval,
		//        then you must set a valid cancelUrl for the web approval flow
		//        that immediately follows this Pay call
		$returnUrl			= $this->url->link('checkout/success');	// TODO - If you are not executing the Pay call for a preapproval,
		//        then you must set a valid returnUrl for the web approval flow
		//        that immediately follows this Pay call
		$currencyCode		=$this->session->data['currency'];	
		// Request specific optional fields
		//   Provide a value for each field that you want to include in the request, if left as an empty string the field will not be passed in the request
		$senderEmail					= "";		// TODO - If you are executing the Pay call against a preapprovalKey, you should set senderEmail
									//        It is not required if the web approval flow immediately follows this Pay call
		$feesPayer						= "";
		$ipnNotificationUrl				= $this->url->link('extension/payment/adaptive_pay/callback&custom='.$this->session->data['order_id'], 'SSL');
		$memo							= "";		// maxlength is 1000 characters
		$pin							= "";		// TODO - If you are executing the Pay call against an existing preapproval
									//        the requires a pin, then you must set this
		$preapprovalKey					= "";		// TODO - If you are executing the Pay call against an existing preapproval, set the preapprovalKey here
		$reverseAllParallelPaymentsOnError	= "";	// TODO - Set this to "true" if you would like each parallel payment to be reversed if an error occurs
									//        defaults to "false" if you don't specify
		$trackingId						= $this->generateTrackingID();	// generateTrackingID function is found in paypalplatform.php

			
		
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$this->load->model('checkout/order');
$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$finalcart=array();
		//$zipcod = $this->session->data['shipping_address']['postcode'];
		$admin_amount = 0;
		$total = 0;
		$pro_details = array();
		foreach($this->cart->getProducts() as $product)
		{
				if(isset($pro_details[$product['seller_id']]))
				{
					$pro_details[$product['seller_id']]['price'] += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
					
				}else{
					$pro_details[$product['seller_id']]['price'] = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
					
				}	
		}
		$i=0;

		$shiptotal = 0;

		if ($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) {
			

			if ($this->session->data['shipping_method']['tax_class_id']) {
				$tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

				$ttaxes = 0;

				foreach ($tax_rates as $tax_rate) {
					
						$ttaxes += $tax_rate['amount'];
					
				}
			}

			$shiptotal += $this->session->data['shipping_method']['cost'] + $ttaxes;
		}

	

		
		foreach($pro_details as $key => $pro_det)
		{
				
				 if($key > 0){
						$res = $this->db->query("SELECT c2c.seller_id as oc_patner_id,c.commission,c2c.paypal_email as paypalid FROM " . DB_PREFIX . "sellers c2c LEFT JOIN " . DB_PREFIX . "commission c ON (c.commission_id= c2c.commission_id) WHERE c2c.seller_id = '$key'");
						$result = $res->row;
						$tax = 0 ;

						$total = $pro_det['price'];
						if(isset($result['commission'])){
							$commission_partner = ($total*$result['commission'])/100;
						}
						else{
							$commission_partner = 0;
						}
						$total = $total - $commission_partner + $tax;
						if(isset($result['paypalid'])){
							$finalcart[] = array('paypalid'=> $result['paypalid'],
													'price'=> $total,
								                     'seller_id'=> $key
											);
						}else{
							$admin_amount = $admin_amount + $total ;
						}
						$admin_amount = $admin_amount + $commission_partner;
				}else{
					 	$admin_amount = $admin_amount + $pro_det['price'];
				}
		}

	

		$tax_admin = 0;
		$admin_total_amount = $admin_amount + $tax_admin + $shiptotal;
		$finalcart[] = array(
				'paypalid'=> $this->config->get('adaptive_pay_adminemail'),
				'price'=> $admin_total_amount,
			     'seller_id'=> 0
		);

		
		$i=0;

		$receiverInvoiceIdArray = array();
		$receiverEmailArray = array();
		$receiverPrimaryArray = array();

		foreach($finalcart as $partner){
		
		       
				$receiverEmailArray[]= $partner['paypalid'];
				
				$receiverAmountArray[]= $partner['price'];

				

					$receiverInvoiceIdArray[]= $partner['price'];


			
				
		}
			
		
		
			
			$resArray = $this->CallPay ($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray,
							$receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray,
							$feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey,
							$reverseAllParallelPaymentsOnError, $senderEmail, $trackingId
			);

			$ack = strtoupper($resArray["responseEnvelope.ack"]);

			
			
			
				if($ack=="SUCCESS")
					{
					
					  
					
							if (!$this->config->get('adaptive_pay_test')) {

							$payPalURL = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey=" . $resArray["payKey"];

							}else{


							$payPalURL = "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=" . $resArray["payKey"];

							}
						
											   	
	                        	$data['action']=$payPalURL;
                              
						
						
						$data['paykey']=$resArray["payKey"];
						
				 }else{
						
						$ErrorCode = urldecode($resArray["error(0).errorId"]);
						$ErrorMsg = urldecode($resArray["error(0).message"]);
						$ErrorDomain = urldecode($resArray["error(0).domain"]);
						$ErrorSeverity = urldecode($resArray["error(0).severity"]);
						$ErrorCategory = urldecode($resArray["error(0).category"]);

						echo "Preapproval API call failed. ";
						echo "Detailed Error Message: " . $ErrorMsg;
						echo "Error Code: " . $ErrorCode;
						echo "Error Severity: " . $ErrorSeverity;
						echo "Error Domain: " . $ErrorDomain;
						echo "Error Category: " . $ErrorCategory;
				 }

				 $data['text_loading'] = $this->language->get('text_loading');

			
			$data['custom'] = $this->session->data['order_id'];

			
			return $this->load->view('extension/payment/adaptive_pay', $data);



			
	}

	
	
	public function callback() {

		
		if (isset($this->request->get['custom'])) {
			$order_id = $this->request->get['custom'];
		} else {
			$order_id = 0;
		}		
		
		
		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			$request = 'cmd=_notify-validate';
		
			foreach ($this->request->post as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}
			
			if (!$this->config->get('adaptive_pay_test')) {
				$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
			} else {
				$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}

			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					
			$response = curl_exec($curl);

			//$paypal_data = serialize($this->request);

			
			
			if (!$response) {
				$this->log->write('ADAPTIVE_PAY :: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
			}
					
			if ($this->config->get('adaptive_pay_debug')) {
				$this->log->write('ADAPTIVE_PAY :: IPN REQUEST: ' . $request);
				$this->log->write('ADAPTIVE_PAY :: IPN RESPONSE: ' . $response);
			}
						
			if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($this->request->post['payment_status'])) {
				$order_status_id = $this->config->get('config_order_status_id');

				switch($this->request->post['payment_status']) {
					case 'Canceled_Reversal':
						$order_status_id = $this->config->get('adaptive_pay_canceled_reversal_status_id');
						break;
					case 'Completed':
						if ((strtolower($this->request->post['receiver_email']) == strtolower($this->config->get('adaptive_pay_email'))) && ((float)$this->request->post['mc_gross'] == $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false))) {
							$order_status_id = $this->config->get('adaptive_pay_completed_status_id');

							$this->db->query("UPDATE " . DB_PREFIX . "order_product
							SET seller_paid_status = '5' WHERE order_id = '" . (int)$order_id . "'");


							$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product`  WHERE order_id = '" . (int)$order_id . "' AND seller_id>0");

							if($query->num_rows){
									


								  foreach($query->rows as $re){

									  if($re['seller_total']>0){

										$this->db->query("INSERT INTO " . DB_PREFIX . "seller_transaction SET seller_id = '" . (int)$re['seller_id'] . "',
										description = 'Paypal Adaptive Payment', 
										transaction_status = '5',
										order_id = '" . (int)$order_id . "',
										amount = '-" . (float)$re['seller_total']. "',
										date_added = NOW()");

											$this->db->query("INSERT INTO " . DB_PREFIX . "seller_payment SET seller_id = '" . (int)$re['seller_id'] . "', 
											payment_info = 'Paypal Adaptive Payment', payment_amount = '" . (float)$re['seller_total'] . "', payment_status = '5', payment_date = Now()");

									  }


								  }

							}
			

						} else {
							$this->log->write('ADAPTIVE_PAY :: RECEIVER EMAIL MISMATCH! ' . strtolower($this->request->post['receiver_email']));
						}
						break;
					case 'Denied':
						$order_status_id = $this->config->get('adaptive_pay_denied_status_id');
						break;
					case 'Expired':
						$order_status_id = $this->config->get('adaptive_pay_expired_status_id');
						break;
					case 'Failed':
						$order_status_id = $this->config->get('adaptive_pay_failed_status_id');
						break;
					case 'Pending':
						$order_status_id = $this->config->get('adaptive_pay_order_status_id');
						break;
					case 'Processed':
						$order_status_id = $this->config->get('adaptive_pay_processed_status_id');
						break;
					case 'Refunded':
						$order_status_id = $this->config->get('adaptive_pay_refunded_status_id');
						break;
					case 'Reversed':
						$order_status_id = $this->config->get('adaptive_pay_reversed_status_id');
						break;
					case 'Voided':
						$order_status_id = $this->config->get('adaptive_pay_voided_status_id');
						break;

				}
				
				
					$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
					
				
			} else {
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'));

				$this->db->query("UPDATE " . DB_PREFIX . "order_product
							SET seller_paid_status = '5' WHERE order_id = '" . (int)$order_id . "'");

					


							$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product`  WHERE order_id = '" . (int)$order_id . "' AND seller_id>0");

							if($query->num_rows){
									


								  foreach($query->rows as $re){

									  if($re['seller_total']>0){

										$this->db->query("INSERT INTO " . DB_PREFIX . "seller_transaction SET seller_id = '" . (int)$re['seller_id'] . "',
										description = 'Paypal Adaptive Payment', 
										amount = '-" . (float)$re['seller_total']. "',
										order_id = '" . (int)$order_id . "',
										date_added = NOW()");

											$this->db->query("INSERT INTO " . DB_PREFIX . "seller_payment SET seller_id = '" . (int)$re['seller_id'] . "', 
											payment_info = 'Paypal Adaptive Payment', payment_amount = '" . (float)$re['seller_total'] . "', payment_status = '5', payment_date = Now()");

									  }


								  }

							}


			}
			
			curl_close($curl);
		}	
	}
}
?>