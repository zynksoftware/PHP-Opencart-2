<?php

ini_set('memory_limit', '128M');

define('__DIR__', DIR_APPLICATION.'controller/module');

define('DEBUG', false);

class ControllerModuleZynk extends Controller
{
    var $devkit;

    private function _startup()
    {
        /*
        if ( ! version_compare(phpversion(), '5.3', ">="))
        {
            die('This integration requires PHP v5.3+.');
        }
        */

        // check that devkit is available
        if (file_exists(__DIR__.'/devkit.php'))
        {
            include(__DIR__.'/devkit.php');
            $this->devkit = new DevKit();
        }
        else
        {
            die('DevKit file not present.');
        }

        // check that the module is enabled
        if( ! $this->config->get('zynk_status'))
        {
            die('Module is not enabled.');
        }

        // check that the bank account is set
        if( $this->config->get('zynk_download_payments') & !$this->config->get('zynk_bank_account'))
        {
            die("You have chosen to download Payments, therfore you must specify a Bank Account.</br> Please login to the admin area and set the option 'Bank Account'.");
        }

        // check that the Account Reference is set
        if( !$this->config->get('zynk_download_customers') & !$this->config->get('zynk_account_reference'))
        {
            die("You have not chosen to download customers, therefore you must specify a default Account Reference.</br> Please login to the admin area and set the option 'Account Reference'.");
        }

        // include other files required
        /*if (file_exists('../admin/model/sale/coupon.php'))
        {
            include(DIR_APPLICATION . '../admin/model/sale/coupon.php');
            $this->model_sale_coupon        = new ModelSaleCoupon($this->registry);
        }
        if (file_exists('../admin/model/sale/affiliate.php'))
        {
            include(DIR_APPLICATION . '../admin/model/sale/affiliate.php');
            $this->model_sale_affiliate     = new ModelSaleAffiliate($this->registry);
        }
        if (file_exists('../admin/model/catalog/product.php'))    
        {
            include(DIR_APPLICATION . '../admin/model/catalog/product.php');
            $this->model_catalog_product    = new ModelCatalogProduct($this->registry);
        }
        if (file_exists('../admin/model/catalog/category.php'))    
        {
            include(DIR_APPLICATION.'../admin/model/catalog/category.php');
            $this->model_catalog_category    = new ModelCatalogCategory($this->registry);
        }
        if (file_exists('../admin/model/catalog/tax_class.php'))   
        {
            include(DIR_APPLICATION.'../admin/model/localisation/tax_class.php');
            $this->model_tax_class          = new ModelLocalisationTaxClass($this->registry);
        }*/
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        include(DIR_APPLICATION.'../admin/model/sale/order.php');
        include(DIR_APPLICATION.'../admin/model/sale/customer.php');
        include(DIR_APPLICATION.'../admin/model/sale/voucher.php');
        include(DIR_APPLICATION.'../admin/model/sale/shipping.php');
        $this->model_sale_order         = new ModelSaleOrder($this->registry);
        $this->model_sale_customer      = new ModelSaleCustomer($this->registry);
        $this->model_sale_voucher       = new ModelSaleVoucher($this->registry);
        $this->model_sale_shipping      = new ModelSaleShipping($this->registry);

        // Include front end models
        include(DIR_APPLICATION.'model/account/order.php');
        $this->model_account_order = new ModelAccountOrder($this->registry);

        include_once(__DIR__.'/zynk_products.php');
        include_once(__DIR__.'/zynk_pricelists.php');
        include_once(__DIR__.'/zynk_customers.php');
        $this->products     = new ZynkProducts($this->registry);
       	$this->pricelists   = new ZynkPricelists($this->registry);
        $this->customers    = new ZynkCustomers($this->registry);
    }

    public function index()
    {
		//die(print_r($this));
        $this->download();
    }

    public function download()
    {
        $this->_startup();

        // get raw http post
        if (isset($_GET['file']))
        {
            $filename = $_GET['file'];
            $post = file_get_contents("$filename");
        }
        else
        {
            $post = file_get_contents("php://input");
        }

        // check if we are dealing with a notify or download
        if (!empty($post))
        {
            $this->devkit->Upload($post);

            foreach($this->devkit->Invoices->Get() as $invoice)
            {
                $data = array
                (
                    'notify'            => false,
                    'order_status_id'   => $this->config->get('zynk_notify_stage'),
                    'comment'           => 'Order saved to Sage.'
                );
                $this->addOrderHistory($invoice->Id, $data);
            }

            foreach($this->devkit->SalesOrders->Get() as $sales_order)
            {
                $data = array
                (
                    'notify'            => false,
                    'order_status_id'   => $this->config->get('zynk_notify_stage'),
                    'comment'           => 'Order saved to Sage.'
                );
                $this->addOrderHistory($sales_order->Id, $data);
            }

            foreach($this->devkit->Customers->Get() as $customer)
            {
                $this->customers->UpdateCustomerAccountReference($customer->Id, $customer->AccountReference);
            }
        }
        elseif ($this->config->get('zynk_download_orders') OR $this->config->get('zynk_download_products'))
        {
            if ($this->config->get('zynk_download_orders')  & !isset($_GET['products']))
            {
                // Limit by OrderID
                if (isset($_GET['orderid']))
                {
                    $data = array
                    (
                        'filter_order_id'           => $_GET['orderid']
                    );
                }
                else
                {
                    $data = array
                    (
                        'filter_order_status_id'    => $this->config->get('zynk_download_stage'),
                        'limit'                     => $this->config->get('zynk_download_limit'),
                        'start'                     => 0
                    );
					
                    //print_r($this->config->get('zynk_download_stage'));

					// Allow orders with a total of £0 and status 'Downloaded' to download
					$data2 = array
                    (
                        'filter_order_status_id'    => 17, // Downloaded
						'filter_total'				=> '0.0000',
                        'limit'                     => $this->config->get('zynk_download_limit'),
                        'start'                     => 0
                    );
                }

                $results = $this->model_sale_order->getOrders($data);
                $type = ($this->config->get('zynk_download_type') == 'invoice') ? 'Invoice' : 'SalesOrder';
                foreach($results as $order_header)
                {
                    $this->download_order($order_header['order_id'], $type);
                }
				
				if (isset($data2))
				{
					$results = $this->model_sale_order->getOrders($data2);
					foreach($results as $order_header)
					{
						$this->download_order($order_header['order_id'], $type);
					}
				}
            }

            if ($this->config->get('zynk_download_products') & isset($_GET['products']))
            {
                // Limit by OrderID
                if (isset($_GET['modified_date']))
                {
                    $data = array
                    (
                        'modified_date'             => $_GET['modified_date']
                    );
                }
                else
                {
                    $data = array
                    (
                        'limit'                     => $this->config->get('zynk_download_limit'),
                        'start'                     => 0
                    );
                }

                $this->download_products($data);
            }

            $this->devkit->Download();
        }
        else
        {
            echo("Download not enabled");
        }
    }

    public function upload()
    {
        $this->_startup();

        // Read from post or file
        if (isset($_GET['file']))
        {
            $filename = $_GET['file'];
            $post = file_get_contents("$filename");
        }
        else
        {
            $post = file_get_contents("php://input");
        }

        // check if we are dealing with a notify or download
        if (!empty($post))
        {
            $this->devkit->Upload($post);

            // Create or Update Products
            if ($this->config->get('zynk_upload_products'))
            {
                $this->screenOutput("</br></br><b>Products</b></br>", 2);
                $this->products->UpdateProducts($this->devkit->Products);
                //$this->products->SetOptionedProductPricing();
                //$this->products->CleanupProductOptions();
            }else if($this->config->get('zynk_upload_product_quantities'))
			{
				$this->screenOutput("</br></br><b>Products</b></br>", 2);
				$this->products->updateStockLevel($this->devkit->Products);
			}
			
			// Create or Update Product Quantity Price Breaks
            if ($this->config->get('zynk_upload_product_price_breaks'))
            {
                $this->screenOutput("</br></br><b>Product Quantity Breaks</b></br>", 2);
                $this->products->UpsertQuantityPriceBreaks($this->devkit->Products);
            }
			
			$truncatePrices = false;
			if (isset($_GET['truncate_prices']))
			{
				$this->screenOutput("</br></br><b>Truncate Prices value passed through to script.</b></br>", 2);
				if ($_GET['truncate_prices']=='true')
				{
					$this->screenOutput("</br></br><b>Truncate Prices value true.</b></br>", 2);
					$truncatePrices = true;
					
				}
			}
            // Create Pricelists
            if ($this->config->get('zynk_upload_pricelists'))
            {
                $this->screenOutput("</br></br><b>Pricelists</b></br>", 2);
                $this->pricelists->UploadPricelists($this->devkit->PriceLists, $truncatePrices);
            }

            // Create or Update Customers
            if ($this->config->get('zynk_upload_customers'))
            {
                $this->screenOutput("</br></br><b>Customers</b></br>", 2);
                $this->customers->UploadCustomers($this->devkit->Customers);
            }

            // Assign Customers to Pricelists & Pricelists to tax groups
            if ($this->config->get('zynk_upload_pricelists'))
            {
                $this->screenOutput("</br></br><b>Customers to Pricelists</b></br>", 2);
                $this->customers->AssignCustomersToPricelists($this->devkit->Customers);
                $this->screenOutput("</br></br><b>Pricelists to TaxClasses</b></br>", 2);
                $this->pricelists->AssignPricelistsToTaxClasses();
            }
			
			//Assign Products as Specials based on special offer flag in Sage
			if ($this->config->get('zynk_upload_product_special_offer'))
            {
                $this->screenOutput("</br></br><b>Setting up special offer products</b></br>", 2);
                $this->products->UpsertSpecialOfferProducts($this->devkit->Products);
            }

        }
        else
        {
            echo("No data sent.");
        }
    }

    private function download_order($order_id, $order_type = 'SalesOrder')
    {
		global $voucherAmount;
		$voucherAmount = 0;
	
        $order = $this->getOrder($order_id);
        $type = 'DK'.$order_type;

        // sales order header
        $sales_order                            = $this->devkit->{$order_type.'s'}->Add(new $type($order['order_id']));
        $sales_order->CustomerId                = $order['customer_id'];
        $sales_order->{$order_type.'Number'}    = $order['order_id'];
        $sales_order->CustomerOrderNumber       = $order['order_id'];
        $sales_order->{$order_type.'Date'}      = date('Y-m-d', strtotime($order['date_added'])) . "T00:00:00";
        //$sales_order->SalesOrderDate          = date('c', strtotime($order['date_added']));
        $sales_order->Notes1                    = substr($order['comment'], 0, 60);
        $sales_order->ForeignRate               = $order['currency_value'];
        //$sales_order->Currency                  = $order['currency_code'];
        $sales_order->CurrencyUsed              = true;
        
        if ($this->config->get('zynk_vat_inclusive_prices'))
        {
            $sales_order->VatInclusive = true;
        }
        
        // sales order address
        $sales_order_address                    = $sales_order->{$order_type.'Address'} = new DKContact($order['customer_id']);
        $sales_order_address->Forename          = ucwords($order['payment_firstname']);
        $sales_order_address->Surname           = ucwords($order['payment_lastname']);
        if (!empty($order['payment_company']))  $sales_order_address->Company = ucwords($order['payment_company']);
        $sales_order_address->Address1          = ucwords($order['payment_address_1']);
        $sales_order_address->Address2          = ucwords($order['payment_address_2']);
        $sales_order_address->Town              = ucwords($order['payment_city']);
        $sales_order_address->County            = ucwords($order['payment_zone']);
        $sales_order_address->Country           = ucwords($order['payment_country']);
        $sales_order_address->Postcode          = strtoupper($order['payment_postcode']);
        $sales_order_address->Email             = $order['email'];
        $sales_order_address->Telephone         = $order['telephone'];
        $sales_order_address->Fax               = $order['fax'];

        // sales order delivery address
        $sales_delivery_address                 = $sales_order->{$order_type.'DeliveryAddress'} = new DKContact($order['customer_id']);
        $sales_delivery_address->Forename       = ucwords($order['shipping_firstname']);
        $sales_delivery_address->Surname        = ucwords($order['shipping_lastname']);
        if (!empty($order['shipping_company'])) $sales_delivery_address->Company = ucwords($order['shipping_company']);
        $sales_delivery_address->Address1       = ucwords($order['shipping_address_1']);
        $sales_delivery_address->Address2       = ucwords($order['shipping_address_2']);
        $sales_delivery_address->Town           = ucwords($order['shipping_city']);
        $sales_delivery_address->County         = ucwords($order['shipping_zone']);
        $sales_delivery_address->Country        = ucwords($order['shipping_country']);
        $sales_delivery_address->Postcode       = strtoupper($order['shipping_postcode']);
        $sales_delivery_address->Email          = $order['email'];
        $sales_delivery_address->Telephone      = $order['telephone'];
        $sales_delivery_address->Fax            = $order['fax'];
		
        $sales_order->TakenBy                   = $this->config->get('zynk_taken_by');
		
        // products
        $product_collection                     = $sales_order->{$order_type.'Items'} = new Collection('DKItem');
        $products                               = $this->model_sale_order->getOrderProducts($order['order_id']);
        
        foreach ($products as $product)
        {
            $product_collection->Add($this->get_item($order_id, $product));
        }
        
        // Add gift vouchers purchased as products
        $vouchers                               = $this->model_sale_order->getOrderVouchers($order['order_id']);
        foreach ($vouchers as $voucher)
		{
            $product_collection->Add($this->get_gift_voucher($voucher));
			//$product_collection->Add($this->get_order_voucher_payment_comment_line($voucher));
			//$this->add_order_voucher_payment_transactions($voucher, $order_id, $sales_order->{$order_type.'Date'});
        }
		
        // Is the shipping an item line?
        if ($this->config->get('zynk_shipping_as_item_line'))
        {
            $product_collection->Add($this->get_carriageItem($order));
        }
        else
        {
            $sales_order->Carriage = $this->get_carriage($order);
            $product_collection->Add($this->get_carriage_message_line($order));
        }
		
		// Add any vouchers used as payment for the order as a comment line and journal transaction
		$giftVoucherPayments = $this->get_order_voucher_payments($order['order_id']);
        $totalVoucherPayment = 0;
        if (!is_null($giftVoucherPayments))
        {
            foreach ($giftVoucherPayments as $voucherPayment)
            {          
                if (isset($voucherPayment["id"]))
                {
                    $totalVoucherPayment += abs($voucherPayment["value"]);
                    //$product_collection->Add($this->get_order_voucher_payment_comment_line($voucherPayment));
                    $this->add_order_voucher_payment_transactions($voucherPayment, $order_id, $sales_order->{$order_type.'Date'});
    				//$this->add_order_voucher_payment_transactions_when_used($order_id, $sales_order->{$order_type.'Date'});
                }
    			//echo $voucherPayment;
    		}
        }
		
		/*foreach ($giftVoucherPayments as $voucherPayments)
		{
			if (isset($voucherPayments["id"]))
			{
				$totalVoucherPayment += abs($voucherPayments["value"]);
				$this->add_order_voucher_payment_transactions_when_used($voucherPayments, $order_id, $sales_order->{$order_type.'Date'});
			}
		}*/
		// Add any coupons as a net value discount
		$coupons = $this->get_order_coupons($order_id);
        //print_r($coupons);
        //$sales_order->NetValueDiscount = $voucherAmount;
        $sales_order->NetValueDiscountDescription = "";
        if ($coupons->num_rows > 0)
        {
    		foreach ($coupons as $coupon)
    		{
                if ($sales_order->NetValueDiscountDescription != "") $sales_order->NetValueDiscountDescription.", ";
                $sales_order->NetValueDiscountDescription .= $coupon['name'];
                $sales_order->NetValueDiscount += abs($coupon['amount']) / 1.2; /* / (1 + ($this->config->get('zynk_default_tax_rate') / 100))*/
    			//$couponAmount += abs($coupon['amount']);
    		}
        }

        // payment details
        if ($this->config->get('zynk_download_payments'))
        {
            $sales_order->PaymentRef            = $order['order_id'];
            $sales_order->PaymentAmount         = $order['total'] + $totalVoucherPayment; //order total has payments by voucher deducted
            $sales_order->BankAccount           = $this->config->get('zynk_bank_account');
        }

        $sales_order->NetValueDiscountDescription = substr($sales_order->NetValueDiscountDescription, 0, 25); // Truncate to 25 chars (max length in sage 50)
        
        // and finally the customer
        if ($this->config->get('zynk_download_customers'))
        {
            $this->download_customer($order['customer_id'], $sales_order_address, $sales_delivery_address, $sales_order->{$order_type.'Date'});
        }else{
            $sales_order->AccountReference = $this->config->get('zynk_account_reference');
		}
    }

    private function get_item($order_id, $product)
    {
        global $item_vat;
		global $couponAmount;
        $p = $this->model_catalog_product->getProduct($product['product_id']);
		
        $item               = new DKItem(Guid());
        $item->Id           = $product['product_id'];
        //$item->Sku          = ($product['product_id'] == 0) ? $product['model'] : $p['sku'];
        $item->Sku          = $p['sku'];
        $item->Name         = html_entity_decode(trim($product['name']), ENT_QUOTES);
        $item->QtyOrdered   = $product['quantity'];
        $item->UnitPrice 	= $product['price'];
        
        // Blissimi want to use tax settings from Sage
        //$item->TaxCode      = $this->TaxClassToTaxCodeMap($p['tax_class_id']); 
        $item->TotalNet     = $product['total'];
        $item->TotalTax     = $product['tax'];
        $item->Total        = round($item->TotalNet + $item->TotalTax, 2);

        // Add any coupons for this product as a unit discount
        $coupon = $this->get_order_item_coupons($order_id, $item->Id);
		//$item->UnitDiscountPercentage = $coupon->row['amount'];
        /*if ($coupon->num_rows)
        {
            //$item->UnitDiscountAmount = $coupon->row["amount"] / $item->QtyOrdered;
			$item->UnitDiscountPercentage = $coupon->row["amount"];
        }*/
        
        $options = $this->model_account_order->getOrderOptions($product['order_id'], $product['order_product_id']);
        
        if ($options)
        {
            $item->Name .= ': ';
            $options_short = '';
            foreach ($options as $option)
            {
                //$item->Name .= html_entity_decode($option['name'], ENT_QUOTES) . ' - ' .$option['value'] . ', ';
                $options_short .= $option['value'].', '; 
            }

            //if(strlen($item->Name) > 80)
            //{
				$item->Sku	= $product['model'];
                $item->Name = $product['name'] .': '.$options_short;
            //}

            $item->Name = substr($item->Name, 0, strlen($item->Name) - 2);

            //$productOption = $this->products->GetProductOptionValue($option['product_option_value_id']);

            //if ($productOption)
            //{
                //$item->Sku  = $productOption->row['ob_sku'];
            //}
        }

        $item_vat = $item_vat + $item->TotalTax;
		unset($item->TotalNet);
		unset($item->TotalTax);
		unset($item->Total);
        return $item;
    }
    
	// Returns a gift voucher purchased on the order as an S2 item
    private function get_gift_voucher($voucher, $orderId, $date)
    {
		global $voucherTotal;
		global $voucherId;
        // Blissimi want to download gift vouchers as an S2 item
        $item               = new DKItem(Guid());
        $item->Id           = "V-".$voucher['voucher_id'];
        $item->Sku          = "S2";
        $item->Name         = "Voucher - ".$voucher['code'];
        $item->Description  = $voucher['message'];
        $item->QtyOrdered   = 1;
        $item->UnitPrice    = $voucher['amount'];
        $item->TaxCode      = 0;
        $item->NominalCode  = GiftVoucherNominal;
		//$item->VoucherStatus = "Purchased";
		
		$voucherTotal += abs($voucher['amount']);
		$voucherId	= $voucher['voucher_id'];
        return $item;
    }
    
    // Returns coupons that apply to the total of an order
    private function get_order_coupons($order_id)
    {
        $coupons = $this->db->query("SELECT c.coupon_id, c.name, c.code, ch.amount
                                     FROM `" . DB_PREFIX . "coupon_history` ch
                                     JOIN `" . DB_PREFIX . "coupon` c ON c.coupon_id = ch.coupon_id
                                     LEFT JOIN `" . DB_PREFIX . "coupon_product` cp ON ch.coupon_id = cp.coupon_id
                                     WHERE ch.order_id = '" . $order_id . "' AND cp.product_id IS NULL");
        return $coupons;
    }
	
	// Returns the vouchers used as payment for the order
	private function get_order_voucher_payments($order_id)
    {
		return $voucher_query = $this->db->query("SELECT order_total_id AS id, title, value FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' AND code = 'voucher'");
	}
    
    private function get_order_voucher_payment_comment_line($voucher)
    {		
		global $voucherAmount;
        //if ($sales_order->NetValueDiscountDescription != "") $sales_order->NetValueDiscountDescription = "Gift Voucher";
        //    $sales_order->NetValueDiscount += $giftVoucherTotal  / (1 + ($this->config->get('zynk_default_tax_rate') / 100));
            
        $item               = new DKItem($voucher['id']);
        $item->Sku          = "M";
        $item->Name         = $voucher['title']." ".$voucher['value'];
        $item->QtyOrdered   = 1;
        $item->UnitPrice    = 0;
		//$item->VoucherStatus = "Used";
        $voucherAmount += abs($voucher['value']);
        return $item;
    }
    
    private function add_order_voucher_payment_transactions($voucher, $orderId, $date)
    {
		global $voucherTotal;
		global $voucherId;
		
		/*$jcTransaction = new DKTransaction("JC-".$orderId);
		$jcTransaction->TransactionType = "JournalCredit";
		$jcTransaction->TransactionDate = $date;
		$jcTransaction->AccountReference = $this->config->get('zynk_account_reference');
		$jcTransaction->NetAmount = abs($voucher['value']);//$jdTransaction->NetAmount;
		$jcTransaction->TaxRate = 0;
		$jcTransaction->Details = "Payment of order $orderId with JC-".$voucherId;
		$jcTransaction->NominalCode = 1205;//GiftVoucherNominal;
		$jcTransaction->BankReference = 2101;//GiftVoucherNominal;
			
		$this->devkit->Transactions->Add($jdTransaction);*/
		
        $jdTransaction = new DKTransaction("JC-".strip_tags($voucher['title']));
        $jdTransaction->TransactionType = "JournalCredit";
        $jdTransaction->TransactionDate = $date;
        $jdTransaction->AccountReference = $this->config->get('zynk_account_reference');
        $jdTransaction->NetAmount = abs($voucher['value']);//$voucherTotal;//abs($voucher['amount']);
        $jdTransaction->TaxRate = 0;
        $jdTransaction->Details = "Payment of order $orderId with ".strip_tags($voucher['title']);
        $jdTransaction->NominalCode = 2101;//$this->config->get('zynk_bank_account');
        $jdTransaction->BankReference = 1205;//$this->config->get('zynk_bank_account');
        
        $this->devkit->Transactions->Add($jdTransaction);
		
        $jcTransaction = new DKTransaction("JD-".strip_tags($voucher['title']));
        $jcTransaction->TransactionType = "JournalDebit";
        $jcTransaction->TransactionDate = $date;
        $jcTransaction->AccountReference = $this->config->get('zynk_account_reference');
        $jcTransaction->NetAmount = abs($voucher['value']); //$jdTransaction->NetAmount;
        $jcTransaction->TaxRate = $jdTransaction->TaxRate;
		$jcTransaction->Details = "Payment of order $orderId with ".strip_tags($voucher['title']); 
        $jcTransaction->NominalCode = 2101;//GiftVoucherNominal;
        $jcTransaction->BankReference = 1205;//GiftVoucherNominal;
        
        $this->devkit->Transactions->Add($jcTransaction);
		
	}
	
	/*private function add_order_voucher_payment_transactions_when_used($voucher, $orderId, $date)    
	{
		if ($voucher['text'] > $voucher['value']) 
		{
			global $voucherTotal;
			global $voucherId;
			$jcTransaction = new DKTransaction("JD-".$orderId);
			$jcTransaction->TransactionType = "JournalDebit";
			$jcTransaction->TransactionDate = $date;
			$jcTransaction->AccountReference = $this->config->get('zynk_account_reference');
			$jcTransaction->NetAmount = abs($voucher['value']);//$jdTransaction->NetAmount;
			$jcTransaction->TaxRate = 0;
			$jcTransaction->Details = "Payment of order $orderId with JD-".$voucherId;
			$jcTransaction->NominalCode = 1205;//GiftVoucherNominal;
			$jcTransaction->BankReference = 2101;//GiftVoucherNominal;
			
			$this->devkit->Transactions->Add($jcTransaction);
		}
        //$this->config->get('zynk_bank_account')
    }*/
    
    // Returns coupons that apply to a particular product on an order
    private function get_order_item_coupons($order_id, $product_id)
    {
        $coupons = $this->db->query("SELECT c.coupon_id, c.name, c.code, ch.amount
                                     FROM `" . DB_PREFIX . "coupon_history` ch
                                     JOIN `" . DB_PREFIX . "coupon` c ON c.coupon_id = ch.coupon_id
                                     JOIN `" . DB_PREFIX . "coupon_product` cp ON ch.coupon_id = cp.coupon_id
                                     WHERE ch.order_id = '" . $order_id . "' AND cp.product_id = '" . $product_id . "'");
        return $coupons;
    }

    private function get_carriage($order)
    {
        global $item_vat;

        $shippingNet        = $this->model_sale_shipping->getShippingNet($order['order_id']);
        $shippingVat        = $this->model_sale_shipping->getShippingVat($order['order_id'], $item_vat);

        $item               = new DKItem(Guid());
        $item->Id           = 0;
        $item->Sku          = substr($order['shipping_method'], 0, 20);
        $item->Name         = substr($order['shipping_method'], 0, 20);
        $item->QtyOrdered   = 1;
        $item->UnitPrice    = (isset($shippingNet['value'])) ? $shippingNet['value'] : 0;
        
        // Carriage is always vatable
        $item->TaxRate      = 20;
        $item->TaxCode      = 1;
        
        $item->NominalCode  = 4905;
        
        return $item;
    }

    private function get_carriageItem($order)
    {
        global $item_vat;

        $ShippingTaxCode    = $this->config->get('shipping_sort_order');
        $shippingNet        = $this->model_sale_shipping->getShippingNet($order['order_id']);
        $shippingVat        = $this->model_sale_shipping->getShippingVat($order['order_id'], $item_vat);
        $item               = new DKItem(Guid());
        $item->Id           = 0;
        $item->Sku          = $order['shipping_method'];
        $item->Name         = $order['shipping_method'];
        $item->QtyOrdered   = 1;
        $item->UnitPrice    = $shippingNet['value'];
        $item->TaxRate      = ($ShippingTaxCode > 0) ? $this->config->get('zynk_default_tax_rate') : 0;
        $item->TaxCode      = ($ShippingTaxCode > 0) ? $this->config->get('zynk_default_tax_code') : 0;
        $item->NominalCode  = 4905;

        return $item;
    }
    
    private function get_carriage_message_line($order)
    {
        $item               = new DKItem(Guid());
        $item->Id           = 0;
        $item->Sku          = "M";
        $item->Name         = $order['shipping_method'];
        $item->QtyOrdered   = 1;
        $item->UnitPrice    = 0;
        
        return $item;
    }
    
    public function download_products($data)
    {
        $products               = $this->model_catalog_product->getProducts($data);
        $add_product            = true;

        foreach ($products as $product)
        {
            if (!empty($data['modified_date']))
            {
                if ( ($product['date_added'] > $data['modified_date']) OR ($product['date_modified'] > $data['modified_date']) )
                {
                    $add_product    = true;
                }
                else
                {
                    $add_product    = false;
                }
            }

            if ($add_product)
            {
                $product_options = $this->model_catalog_product->getProductOptions($product['product_id']);
                if(is_array($product_options) && count($product_options) > 0)
                {

                    // Here we have all of the options for this product.
                    //We need to get just the ones with a sku.
                    foreach($product_options as $option)
                    {
                        foreach($option['product_option_value'] as $option_value)
                        {
                            if(!empty($option_value['sku']))
                            {
                                $p                  = new DKProduct(Guid());
                                $p->Id              = Guid();//$product['product_id'];
                                //$p->Sku             = ($product['sku'] == '') ? $product['model'] : $product['sku'];
                                $p->Sku             = $option_value['sku'];
                                $name = str_replace('  ', ' ', html_entity_decode(trim($product['name']), ENT_QUOTES).' '.html_entity_decode(trim($option_value['name']), ENT_QUOTES));
                                $p->Name            = $name;
                                $p->Description     = '<![CDATA['.$product['name'] .' '.$option_value['name'].']]>';
                                $p->LongDescription = '<![CDATA['.$product['description'].']]>';

                                $p->SupplierPartNo  = $product['upc'];
                                //$p->TaxRate       = $product['tax'];
                                $p->TaxCode         = $this->TaxClassToTaxCodeMap($product['tax_class_id']);
                                
                                if($option_value['price_prefix'] == '-')
                                {
                                    $p->SalePrice = ($product['price'] - $option_value['price']);
                                }else if($option_value['price_prefix'] == '+'){
                                    $p->SalePrice = ($product['price'] + $option_value['price']);
                                }else{
                                    $p->SalePrice = $option_value['price'];
                                }
                                
                                if($option_value['weight_prefix'] == '-')
                                {
                                    $p->UnitWeight = $product['weight'] - $option_value['weight']; 
                                }else{
                                    $p->UnitWeight = $product['weight'] + $option_value['weight'];
                                }

                                $p->Location        = $product['location'];
                                $p->ImageURL        = "http://".$_SERVER['HTTP_HOST'].'/image/'.$product['image'];
                                $p->ImageName       = str_replace("data/", "", $product['image']); //@TODO: This is the full image path, simply derive filename from this
                                $p->Publish         = $product['status'];
                                //$p->DateCreated     = date("Y-m-d\TH:i:s", strtotime($product['date_added']));
                                //$p->DateModified    = date("Y-m-d\TH:i:s", strtotime($product['date_modified']));

                                $this->devkit->Products->Add($p);
                            }
                        }
                    }
                }else{
                    $p                  = new DKProduct(Guid());
                    $p->Id              = Guid();//$product['product_id'];
                    //$p->Sku             = ($product['sku'] == '') ? $product['model'] : $product['sku'];
                    $p->Sku             = $product['sku'];
                    $p->Name            = html_entity_decode(trim($product['name']), ENT_QUOTES);
                    $p->Description     = '<![CDATA['.$product['name'].']]>';
                    $p->LongDescription = '<![CDATA['.$product['description'].']]>';
                    $p->SupplierPartNo  = $product['upc'];
                    //$p->TaxRate       = $product['tax'];
                    $p->TaxCode         = $this->TaxClassToTaxCodeMap($product['tax_class_id']);
                    //$p->Tax = $product['tax'];
                    $p->SalePrice = $product['price'];

                    if( $this->config->get('zynk_download_cost_price') )
                    {
                        // Downloading Cost Prices
                        $p->LastCostPrice   = $product['cost_price'];
                    }

                    $p->UnitWeight      = $product['weight'];
                    $p->Location        = $product['location'];
                    $p->ImageURL        = "http://".$_SERVER['HTTP_HOST'].'/image/'.$product['image'];
                    $p->ImageName       = str_replace("data/", "", $product['image']); //@TODO: This is the full image path, simply derive filename from this
                    $p->Publish         = $product['status'];
                    //$p->DateCreated     = date("Y-m-d\TH:i:s", strtotime($product['date_added']));
                    //$p->DateModified    = date("Y-m-d\TH:i:s", strtotime($product['date_modified']));

                    $this->devkit->Products->Add($p);
                }
            }

            $add_product            = true;
        }
    }
    
    private function download_customer($customer_id, $sales_order_address, $sales_delivery_address, $order_date)
    {
        $customer                       = $this->model_sale_customer->getCustomer($customer_id);

        if ($customer)
        {
            $cust                       = $this->devkit->Customers->Add(new DKCustomer($customer['customer_id']));
            $cust->AccountReference     = $customer['AccountReference'];
			$address					= $this->model_sale_customer->getAddress($customer['address_id']);
			$cust->CompanyName			= $address['company'];
			if (strlen($cust->CompanyName)==0)
			{
				$cust->CompanyName          = ucwords($customer['firstname'].' '.$customer['lastname']);
			}
        }
        else
        {
            $cust                       = $this->devkit->Customers->Add(new DKCustomer($customer_id));
            $cust->AccountReference     = $this->config->get('zynk_account_reference');
            $cust->CompanyName          = "Guest Account: " . $customer_id;
        }

        $cust->CustomerInvoiceAddress   = $sales_order_address;
        $cust->CustomerDeliveryAddress  = $sales_delivery_address;
        //$cust->TaxCode                  = $this->GetTaxCode($this->customers->GetIsoCodeFromCountry($sales_order_address->Country));
        $cust->TermsAgreed              = 1;

    }

    // Return taxcode based on location
    public function GetTaxCode($country_code)
    {
        // Variables should really be defined in config...
        $TaxCode_UK     = $this->config->get('zynk_default_tax_code');
        $TaxCode_EU     = 1;
        $TaxCode_ROW    = 0;

        $taxCode        = $this->config->get('zynk_default_tax_code');

        $CountryCode_UK = "GB";
        $CountryCode_EU = "BE,BG,CZ,DK,DE,EE,IE,EL,ES,FR,IT,CY,LV,LT,LU,HU,MT,NL,AT,PL,PT,RO,SI,SK,FI,SE";

        if ($country_code == $CountryCode_UK)
        {
            $taxCode = $TaxCode_UK;
        }
        else
        {
            $countrycode_array = explode(",", $CountryCode_EU);

            if (in_array($country_code, $countrycode_array))
            {
                $taxCode = $TaxCode_EU;
            }
            else
            {
                $taxCode = $TaxCode_ROW;
            }
        }

        return $taxCode;
    }


    // Return TaxCode from a given Tax Class
    public function TaxClassToTaxCodeMap($tax_class_id)
    {
        $taxCode = $this->config->get('zynk_default_tax_code');

        switch ($tax_class_id)
        {
        case $this->config->get('zynk_vatable_taxclass'):
            $taxCode = $this->config->get('zynk_vatable_taxcode');
            break;
        case $this->config->get('zynk_nonvatable_taxclass'):
            $taxCode = $this->config->get('zynk_nonvatable_taxcode');
            break;
        case $this->config->get('zynk_exempt_taxclass'):
            $taxCode = $this->config->get('zynk_exempt_taxcode');
            break;
        default:
            $taxCode = $this->config->get('zynk_nonvatable_taxcode');
            break;
        }

        return $taxCode;
    }

    public function GetTaxClassData($taxClassId)
    {
        return $this->model_tax_class->getTaxClass($taxClassId);
    }

	public function addOrderHistory($order_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");

		$order_info = $this->getOrder($order_id);

        // @TODO: Should we implement this?
		// Send out any gift voucher mails
        /*
		if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
			$this->load->model('sale/voucher');

			$results = $this->model_sale_voucher->getVouchersByOrderId($order_id);

			foreach ($results as $result) {
				$this->model_sale_voucher->sendVoucher($result['voucher_id']);
			}
		}*/

      	if ($data['notify']) {
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_filename']);
			$language->load('mail/order');

			$subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);

			$message  = $language->get('text_order') . ' ' . $order_id . "\n";
			$message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

			if ($order_status_query->num_rows) {
				$message .= $language->get('text_order_status') . "\n";
				$message .= $order_status_query->row['name'] . "\n\n";
			}

			if ($order_info['customer_id']) {
				$message .= $language->get('text_link') . "\n";
				$message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
			}

			if ($data['comment']) {
				$message .= $language->get('text_comment') . "\n\n";
				$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
			}

			$message .= $language->get('text_footer');

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($order_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject($subject);
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND order_status_id > '0'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
		} else {
			return false;
		}
	}

    public function screenOutput($input, $log_level)
    {
        $debugOutput = true;

        if ($debugOutput)
        {
            switch ($log_level)
            {
            case 0: //DEBUG
                echo($input);
                break;
            case 1: //ERROR
                echo("<FONT COLOR='RED'>");
                echo($input);
                echo("</FONT>");
                break;
            case 2: //INFO
                echo($input);
                break;
            case 3: //WARN
                echo($input);
                break;
            default:
                echo($input);
            }
        }
    }

}
