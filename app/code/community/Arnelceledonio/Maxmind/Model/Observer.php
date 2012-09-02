<?php

class Arnelceledonio_Maxmind_Model_Observer
{
	const CONFIG_MAXMIND_LICENSEKEY = 'maxmind/general/maxmind_licensekey';
	const CONFIG_MAXMIND_USERNAME = 'maxmind/general/maxmind_username';
	const CONFIG_MAXMIND_PASSWORD = 'maxmind/general/maxmind_password';
	const CONFIG_MAXMIND_USEDNS = 'maxmind/general/maxmind_usedns';
	const CONFIG_MAXMIND_SECURE = 'maxmind/general/maxmind_secure';
	const CONFIG_MAXMIND_TIMEOUT = 'maxmind/general/maxmind_timeout';
	const CONFIG_MAXMIND_DEBUG = 'maxmind/general/maxmind_debug';
	const CONFIG_MAXMIND_FRAUDSCORE = 'maxmind/general/maxmind_fraudscore';
	
	function checkOrders()
	{
			$licensekey = Mage::getStoreConfig(self::CONFIG_MAXMIND_LICENSEKEY);
			
			$issecure	= Mage::getStoreConfig(self::CONFIG_MAXMIND_SECURE);
			$timeout	= Mage::getStoreConfig(self::CONFIG_MAXMIND_TIMEOUT);
			$use_dns 	= Mage::getStoreConfig(self::CONFIG_MAXMIND_USEDNS);			
			$debug		= Mage::getStoreConfig(self::CONFIG_MAXMIND_DEBUG);
			$fraudscore		= Mage::getStoreConfig(self::CONFIG_MAXMIND_FRAUDSCORE);
			
			if ($timeout <=0) $timeout =10;
			
			$order = new Mage_Sales_Model_Order();
			//GET invoice Number
			$invoiceNumber = $incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
			
			//Get Order Details
			$order->loadByIncrementId($incrementId);
			$shipping_address = Mage::getModel('sales/order_address')->load($order->shipping_address_id);
			$billingId= $order->getData('billing_address_id');
			$billing_address = Mage::getModel('sales/order_address')->load($billingId);
			
			$remote_ip = $order->getData('remote_ip');
			$qnty=0;
			$items = $order->getAllItems();
			foreach ($items as $itemId => $item)
			{
				$qnty += round($item->qty_ordered);
				$proPrice = number_format($item->getPrice(),2);
				
				$taxAmt	= $item->getTaxAmount();
				
			}	
						
			require("lib/CreditCardFraudDetection.php");
			// Create a new CreditCardFraudDetection object
			$ccfs = new CreditCardFraudDetection;
			// Enter your license key here (Required)
			$h["license_key"] = $licensekey;
			// Required fields
			$h["i"] 		= $order->getData('remote_ip');             // set the client ip address
			$h["city"] 		= $billing_address->getCity();             //New York set the billing city
			$h["region"] 	= $billing_address->getRegionCode();                 //NY set the billing state
			$h["postal"] 	= $billing_address->getPostcode();              // set the billing zip code
			$h["country"] 	= $billing_address->getCountryId();                //US set the billing country
			
			// Recommended fields
			$emaildomain = explode('@',$order->getCustomerEmail());
			$h["domain"] = $emaildomain[1];		// Email domain		
			
			// Optional fields
			//$h["binName"] = "MBNA America Bank";	// bank name
			//$h["binPhone"] = "800-421-2110";	// bank customer service phone number on back of credit card
			$h["custPhone"] =  $shipping_address->getTelephone();		// Area-code and local prefix of customer phone number
			//$h["requested_type"] = "premium";	// Which level (free, city, premium) of CCFD to use
			$h["shipAddr"] = utf8_encode($shipping_address->getStreet(1).' '.$shipping_address->getStreet(2)); // Shipping Address
			$h["shipCity"] =  $shipping_address->getCity();  	// the City to Ship to
			$h["shipRegion"] = $shipping_address->getRegionCode();	// the Region to Ship to
			$h["shipPostal"] = $shipping_address->getPostcode(); // the Postal Code to Ship to
			$h["shipCountry"] = $shipping_address->getCountryId();	// the country to Ship to
			
			$h["txnID"] = $incrementId;			// Transaction ID
			//$h["sessionID"] = "abcd9876";		// Session ID
			
			
			// set the timeout to be five seconds
			$ccfs->timeout = $timeout;
			// if useDNS is 1 then use DNS, otherwise use ip addresses directly
			$ccfs->useDNS = $use_dns;
			$ccfs->isSecure = $issecure;
			
			// next we set up the input hash
			$ccfs->input($h);

			// then we query the server
			$ccfs->query();

			// then we get the result from the server
			$h = $ccfs->output();

			// then finally we print out the result
			$outputkeys = array_keys($h);
			//Mage::log($outputkeys);
			$numoutputkeys = count($h);
			$sql_query = "'".$incrementId."',";
			$sql_query='';
			for ($i = 0; $i < $numoutputkeys; $i++) 
			{
				$key = $outputkeys[$i];
				$value = $h[$key];
			if ((!empty($key)) && (!empty($value)) ):		
				 //Mage::log($key . " = " . $value);
				 if ($key == 'riskScore') $riskScore = $value;
				 $sql_query .= " '".$value."' ,";				
			endif;	
			}
			if ($sql_query)
			{
				// fetch write database connection that is used in Mage_Core module
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				// now $write is an instance of Zend_Db_Adapter_Abstract
				//Mage::log("insert into `maxmind_order_fraudcheck` values ( ". substr($sql_query,0,-1). ")");
				$write->query("insert into `maxmind_order_fraudcheck` values ( ". substr($sql_query,0,-1). ")");
				
				if ($riskScore <= $fraudscore)
				{
				$entity_id = $order->getData('entity_id');
				$temptable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_grid');
				$sql="UPDATE ".$temptable." SET `status` = 'fraud' WHERE `entity_id` ='".$entity_id."'";
				//$write->query($sql);
				}
			}//SQL QUERY
			
			
	
	}//function
	
}	