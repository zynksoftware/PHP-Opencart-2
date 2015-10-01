<?php

    // Make sure we always set a default here...
    define('zynk_download_orders_default',                  '0');
    define('zynk_download_products_default',                '0');
    define('zynk_download_type_default',                    'sales_order');
    define('zynk_download_stage_default',                   '1');
    define('zynk_notify_stage_default',                     '2');
    define('zynk_status_default',                           '1');
    define('zynk_download_customers_default',               '0');
    define('zynk_upload_customers_default',                 '0');
    define('zynk_upload_products_default',                  '1');
    define('zynk_upload_product_images_default',            '1');
    define('zynk_upload_product_descriptions_default',      '1');
    define('zynk_upload_product_quantities_default',        '1');
	define('zynk_upload_product_price_breaks_default',		'0');
	define('zynk_upload_product_special_offer_default',		'0');
    define('zynk_upload_categories_default',                '0');
    define('zynk_upload_pricelists_default',                '0');
    define('zynk_download_payments_default',                '0');
    define('zynk_account_reference_default',                'WEBSALES');
    define('zynk_bank_account_default',                     '1200');
    define('zynk_taken_by_default',                         'ZYNK_WEB');
    define('zynk_default_category_default',                 '0');
    define('zynk_default_product_image_directory_default',  'data/products');
    define('zynk_default_tax_code_default',                 '1');
    define('zynk_default_tax_rate_default',                 '20');
    define('zynk_shipping_as_item_line_default',            '0');
    define('zynk_download_limit_default',                   '100');
    define('zynk_vatable_taxclass_default',                 '0');
    define('zynk_nonvatable_taxclass_default',              '0');
    define('zynk_exempt_taxclass_default',                  '0');
    define('zynk_vatable_taxcode_default',                  '1');
    define('zynk_nonvatable_taxcode_default',               '0');
    define('zynk_exempt_taxcode_default',                   '2');

    define('zynk_download_cost_price_default',              '0');
    define('zynk_upload_cost_price_default',                '0');
	

class ControllerModuleZynk extends Controller
{
    private $error = array();

    // As we set form items to invalid to prevent access to these they are not pushed through in the form post
    // revalidate the form and set the missing data to the defaults
    public function validatePost()
    {
        if (!isset($this->request->post['zynk_download_orders'])) { $this->request->post['zynk_download_orders'] = zynk_download_orders_default; }
        if (!isset($this->request->post['zynk_download_products'])) { $this->request->post['zynk_download_products'] = zynk_download_products_default; }
        if (!isset($this->request->post['zynk_download_type'])) { $this->request->post['zynk_download_type'] = zynk_download_type_default; }
        if (!isset($this->request->post['zynk_download_stage'])) { $this->request->post['zynk_download_stage'] = zynk_download_stage_default; }
        if (!isset($this->request->post['zynk_notify_stage'])) { $this->request->post['zynk_notify_stage'] = zynk_notify_stage_default; }
        if (!isset($this->request->post['zynk_status'])) { $this->request->post['zynk_status'] = zynk_status_default; }
        if (!isset($this->request->post['zynk_download_customers'])) { $this->request->post['zynk_download_customers'] = zynk_download_customers_default; }
        if (!isset($this->request->post['zynk_upload_customers'])) { $this->request->post['zynk_upload_customers'] = zynk_upload_customers_default; }
        if (!isset($this->request->post['zynk_upload_products'])) { $this->request->post['zynk_upload_products'] = zynk_upload_products_default; }
        if (!isset($this->request->post['zynk_upload_product_images'])) { $this->request->post['zynk_upload_product_images'] = zynk_upload_product_images_default; }
        if (!isset($this->request->post['zynk_upload_product_descriptions'])) { $this->request->post['zynk_upload_product_descriptions'] = zynk_upload_product_descriptions_default; }
        if (!isset($this->request->post['zynk_upload_product_quantities'])) { $this->request->post['zynk_upload_product_quantities'] = zynk_upload_product_quantities_default; }
        if (!isset($this->request->post['zynk_upload_categories'])) { $this->request->post['zynk_upload_categories'] = zynk_upload_categories_default; }
        if (!isset($this->request->post['zynk_upload_pricelists'])) { $this->request->post['zynk_upload_pricelists'] = zynk_upload_pricelists_default; }
		if (!isset($this->request->post['zynk_upload_product_price_breaks'])) { $this->request->post['zynk_upload_product_price_breaks'] = zynk_upload_product_price_breaks_default; }
		if (!isset($this->request->post['zynk_upload_product_special_offer'])) { $this->request->post['zynk_upload_product_special_offer'] = zynk_upload_product_special_offer_default; }
        if (!isset($this->request->post['zynk_download_payments'])) { $this->request->post['zynk_download_payments'] = zynk_download_payments_default; }
        if (!isset($this->request->post['zynk_account_reference'])) { $this->request->post['zynk_account_reference'] = zynk_account_reference_default; }
        if (!isset($this->request->post['zynk_bank_account'])) { $this->request->post['zynk_bank_account'] = zynk_bank_account_default; }
        if (!isset($this->request->post['zynk_taken_by'])) { $this->request->post['zynk_taken_by'] = zynk_taken_by_default; }
        if (!isset($this->request->post['zynk_default_category'])) { $this->request->post['zynk_default_category'] = zynk_default_category_default; }
        if (!isset($this->request->post['zynk_default_product_image_directory'])) { $this->request->post['zynk_default_product_image_directory'] = zynk_default_product_image_directory_default; }
        if (!isset($this->request->post['zynk_default_tax_code'])) { $this->request->post['zynk_default_tax_code'] = zynk_default_tax_code_default; }
        if (!isset($this->request->post['zynk_default_tax_rate'])) { $this->request->post['zynk_default_tax_rate'] = zynk_default_tax_rate_default; }
        if (!isset($this->request->post['zynk_shipping_as_item_line'])) { $this->request->post['zynk_shipping_as_item_line'] = zynk_shipping_as_item_line_default; }
        if (!isset($this->request->post['zynk_download_limit'])) { $this->request->post['zynk_download_limit'] = zynk_download_limit_default; }
        if (!isset($this->request->post['zynk_vatable_taxclass'])) { $this->request->post['zynk_vatable_taxclass'] = zynk_vatable_taxclass_default; }
        if (!isset($this->request->post['zynk_exempt_taxclass'])) { $this->request->post['zynk_exempt_taxclass'] = zynk_exempt_taxclass_default; }
        if (!isset($this->request->post['zynk_nonvatable_taxclass'])) { $this->request->post['zynk_nonvatable_taxclass'] = zynk_nonvatable_taxclass_default; }
        if (!isset($this->request->post['zynk_vatable_taxcode'])) { $this->request->post['zynk_vatable_taxcode'] = zynk_vatable_taxcode_default; }
        if (!isset($this->request->post['zynk_nonvatable_taxcode'])) { $this->request->post['zynk_nonvatable_taxcode'] = zynk_nonvatable_taxcode_default; }
        if (!isset($this->request->post['zynk_exempt_taxcode'])) { $this->request->post['zynk_exempt_taxcode'] = zynk_exempt_taxcode_default; }
        if (!isset($this->request->post['zynk_download_cost_price'])) { $this->request->post['zynk_download_cost_price'] = zynk_download_cost_price_default; }
        if (!isset($this->request->post['zynk_upload_cost_price'])) { $this->request->post['zynk_upload_cost_price'] = zynk_upload_cost_price_default; }
    }

    public function index() {

        $this->load->language('module/zynk');
        $this->load->model('setting/setting');

        // Save into DB
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
        
            $this->validatePost();
            $this->model_setting_setting->editSetting('zynk', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title']                              = $this->language->get('heading_title');

        $data['text_enabled']                         = $this->language->get('text_enabled');
        $data['text_disabled']                        = $this->language->get('text_disabled');
        $data['text_left']                            = $this->language->get('text_left');
        $data['text_right']                           = $this->language->get('text_right');
        $data['text_sales_order']                     = $this->language->get('text_sales_order');
        $data['text_invoice']                         = $this->language->get('text_invoice');
        $data['text_none']                            = $this->language->get('text_none');
        $data['text_edit_module']                     = $this->language->get('text_edit_module');
        $data['text_option']                          = $this->language->get('text_option');
        $data['text_status']                          = $this->language->get('text_status');

        $data['entry_download_orders']                = $this->language->get('entry_download_orders');
        $data['entry_download_products']              = $this->language->get('entry_download_products');
        $data['entry_download_type']                  = $this->language->get('entry_download_type');
        $data['entry_download_stage']                 = $this->language->get('entry_download_stage');
        $data['entry_notify_stage']                   = $this->language->get('entry_notify_stage');
        $data['entry_status']                         = $this->language->get('entry_status');
        $data['entry_download_customers']             = $this->language->get('entry_download_customers');
        $data['entry_upload_customers']               = $this->language->get('entry_upload_customers');
        $data['entry_upload_products']                = $this->language->get('entry_upload_products');
        $data['entry_upload_product_images']          = $this->language->get('entry_upload_product_images');
        $data['entry_upload_product_descriptions']    = $this->language->get('entry_upload_product_descriptions');
        $data['entry_upload_product_quantities']      = $this->language->get('entry_upload_product_quantities');
		$data['entry_upload_product_price_breaks']    = $this->language->get('entry_upload_product_price_breaks');
		$data['entry_upload_product_special_offer']    = $this->language->get('entry_upload_product_special_offer');
        $data['entry_upload_categories']              = $this->language->get('entry_upload_categories');
        $data['entry_upload_pricelists']              = $this->language->get('entry_upload_pricelists');
        $data['entry_download_payments']              = $this->language->get('entry_download_payments');
        $data['entry_account_reference']              = $this->language->get('entry_account_reference');
        $data['entry_bank_account']                   = $this->language->get('entry_bank_account');
        $data['entry_taken_by']                       = $this->language->get('entry_taken_by');
        $data['entry_default_category']               = $this->language->get('entry_default_category');
        $data['entry_default_product_image_directory'] = $this->language->get('entry_default_product_image_directory');
        $data['entry_default_tax_code']               = $this->language->get('entry_default_tax_code');
        $data['entry_default_tax_rate']               = $this->language->get('entry_default_tax_rate');
        $data['entry_shipping_as_item_line']          = $this->language->get('entry_shipping_as_item_line');
        $data['entry_download_limit']                 = $this->language->get('entry_download_limit');
        $data['entry_vatable_taxclass']               = $this->language->get('entry_vatable_taxclass');
        $data['entry_nonvatable_taxclass']            = $this->language->get('entry_nonvatable_taxclass');
        $data['entry_exempt_taxclass']                = $this->language->get('entry_exempt_taxclass');
        $data['entry_vatable_taxcode']                = $this->language->get('entry_vatable_taxcode');
        $data['entry_nonvatable_taxcode']             = $this->language->get('entry_nonvatable_taxcode');
        $data['entry_exempt_taxcode']                 = $this->language->get('entry_exempt_taxcode');

        $data['entry_download_cost_price']            = $this->language->get('entry_download_cost_price');
        $data['entry_upload_cost_price']              = $this->language->get('entry_upload_cost_price');
        
        $data['button_save']                          = $this->language->get('button_save');
        $data['button_cancel']                        = $this->language->get('button_cancel');

        if (isset($this->error['warning']))
        {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }  

        if (isset($this->error['code']))
        {
            $data['error_code'] = $this->error['code'];
        } else {
            $data['error_code'] = '';
        }
        
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('module/zynk', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->load->model('catalog/category');
        $categories = new ModelCatalogCategory($this->registry);
        $data['categories'] = $categories->getCategories($data);
        
        $this->load->model('localisation/tax_class');
        $tax_classes = new ModelLocalisationTaxClass($this->registry);
        $data['tax_classes'] = $tax_classes->getTaxClasses();

        $data['action'] = $this->url->link('module/zynk', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        // are we downloading orders . . .
        if (isset($this->request->post['zynk_download_orders']))
        {
            $data['zynk_download_orders'] = $this->request->post['zynk_download_orders'];
        }
        else
        {
            $data['zynk_download_orders'] = $this->config->get('zynk_download_orders');
        }

        // are we downloading products . . .
        if (isset($this->request->post['zynk_download_products']))
        {
            $data['zynk_download_products'] = $this->request->post['zynk_download_products'];
        }
        else
        {
            $data['zynk_download_products'] = $this->config->get('zynk_download_products');
        }

        // download orders as . . .
        if (isset($this->request->post['zynk_download_type']))
        {
            $data['zynk_download_type'] = $this->request->post['zynk_download_type'];
        }
        else
        {
            $data['zynk_download_type'] = $this->config->get('zynk_download_type');
        }

        $this->load->model('localisation/order_status');
        $data['order_status']	= $this->model_localisation_order_status->getOrderStatuses();
        // download orders at stage . . .
        if (isset($this->request->post['zynk_download_stage']))
        {
            $data['zynk_download_stage'] = $this->request->post['zynk_download_stage'];
        }
        else
        {
            $data['zynk_download_stage'] = $this->config->get('zynk_download_stage');
        }

        // notify orders to stage . . .
        if (isset($this->request->post['zynk_notify_stage']))
        {
            $data['zynk_notify_stage'] = $this->request->post['zynk_notify_stage'];
        }
        else
        {
            $data['zynk_notify_stage'] = $this->config->get('zynk_notify_stage');
        }

        // are we downloading customers . . .
        if (isset($this->request->post['zynk_download_customers']))
        {
            $data['zynk_download_customers'] = $this->request->post['zynk_download_customers'];
        }
        else
        {
            $data['zynk_download_customers'] = $this->config->get('zynk_download_customers');
        }

        // are we uploading customers . . .
        if (isset($this->request->post['zynk_upload_customers']))
        {
            $data['zynk_upload_customers'] = $this->request->post['zynk_upload_customers'];
        }
        else
        {
            $data['zynk_upload_customers'] = $this->config->get('zynk_upload_customers');
        }

        // are we uploading products . . .
        if (isset($this->request->post['zynk_upload_products']))
        {
            $data['zynk_upload_products'] = $this->request->post['zynk_upload_products'];
        }
        else
        {
            $data['zynk_upload_products'] = $this->config->get('zynk_upload_products');
        }

        // are we uploading product images . . .
        if (isset($this->request->post['zynk_upload_product_images']))
        {
            $data['zynk_upload_product_images'] = $this->request->post['zynk_upload_product_images'];
        }
        else
        {
            $data['zynk_upload_product_images'] = $this->config->get('zynk_upload_product_images');
        }

        // are we uploading product descriptions . . .
        if (isset($this->request->post['zynk_upload_product_descriptions']))
        {
            $data['zynk_upload_product_descriptions'] = $this->request->post['zynk_upload_product_descriptions'];
        }
        else
        {
            $data['zynk_upload_product_descriptions'] = $this->config->get('zynk_upload_product_descriptions');
        }
        // are we uploading product quantities . . .
        if (isset($this->request->post['zynk_upload_product_quantities']))
        {
            $data['zynk_upload_product_quantities'] = $this->request->post['zynk_upload_product_quantities'];
        }
        else
        {
            $data['zynk_upload_product_quantities'] = $this->config->get('zynk_upload_product_quantities');
        }
		
		// are we uploading product quantity breaks . . .
        if (isset($this->request->post['zynk_upload_product_price_breaks']))
        {
            $data['zynk_upload_product_price_breaks'] = $this->request->post['zynk_upload_product_price_breaks'];
        }
        else
        {
            $data['zynk_upload_product_price_breaks'] = $this->config->get('zynk_upload_product_price_breaks');
        }
		
		// are we uploading product special offer (if a product has special offer checked in Sage, then will be added to Special in opencart; but with sale price)
        if (isset($this->request->post['zynk_upload_product_special_offer']))
        {
            $data['zynk_upload_product_special_offer'] = $this->request->post['zynk_upload_product_special_offer'];
        }
        else
        {
            $data['zynk_upload_product_special_offer'] = $this->config->get('zynk_upload_product_special_offer');
        }
        
        // are we uploading categories . . .
        if (isset($this->request->post['zynk_upload_categories']))
        {
            $data['zynk_upload_categories'] = $this->request->post['zynk_upload_categories'];
        }
        else
        {
            $data['zynk_upload_categories'] = $this->config->get('zynk_upload_categories');
        }

        // are we uploading pricelists . . .
        if (isset($this->request->post['zynk_upload_pricelists']))
        {
            $data['zynk_upload_pricelists'] = $this->request->post['zynk_upload_pricelists'];
        }
        else
        {
            $data['zynk_upload_pricelists'] = $this->config->get('zynk_upload_pricelists');
        }

        // are we downloading payments . . .
        if (isset($this->request->post['zynk_download_payments']))
        {
            $data['zynk_download_payments'] = $this->request->post['zynk_download_payments'];
        }
        else
        {
            $data['zynk_download_payments'] = $this->config->get('zynk_download_payments');
        }

        // using single account reference . . .
        $zynk_account_reference = $this->config->get('zynk_account_reference');
        if (isset($this->request->post['zynk_account_reference']))
        {
            $data['zynk_account_reference'] = $this->request->post['zynk_account_reference'];
        }
        elseif (!isset($zynk_account_reference))
        {
            $data['zynk_account_reference'] = zynk_account_reference_default;
        }
        else
        {
            $data['zynk_account_reference'] = $zynk_account_reference;
        }

        // bank account for payments . . .
        $zynk_bank_account = $this->config->get('zynk_bank_account');
        if (isset($this->request->post['zynk_bank_account']))
        {
            $data['zynk_bank_account'] = $this->request->post['zynk_bank_account'];
        }
        elseif (!isset($zynk_bank_account))
        {
            $data['zynk_bank_account'] = zynk_bank_account_default;
        }
        else
        {
            $data['zynk_bank_account'] = $zynk_bank_account;
        }

        // taken by field . . .
        $zynk_taken_by = $this->config->get('zynk_taken_by');
        if (isset($this->request->post['zynk_taken_by']))
        {
            $data['zynk_taken_by'] = $this->request->post['zynk_taken_by'];
        }
        elseif (!isset($zynk_taken_by))
        {
            $data['zynk_taken_by'] = zynk_taken_by_default;
        }
        else
        {
            $data['zynk_taken_by'] = $zynk_taken_by;
        }

        // default category field . . .
        $zynk_default_category = $this->config->get('zynk_default_category');
        if (isset($this->request->post['zynk_default_category']))
        {
            $data['zynk_default_category'] = $this->request->post['zynk_default_category'];
        }
        elseif (!isset($zynk_default_category))
        {
            $data['zynk_default_category'] = zynk_default_category_default;
        }
        else
        {
            $data['zynk_default_category'] = $zynk_default_category;
        }

        // default product image directory . . .
        $zynk_default_product_image_directory = $this->config->get('zynk_default_product_image_directory');
        if (isset($this->request->post['zynk_default_product_image_directory']))
        {
            $data['zynk_default_product_image_directory'] = $this->request->post['zynk_default_product_image_directory'];
        }
        elseif (!isset($zynk_default_product_image_directory))
        {
            $data['zynk_default_product_image_directory'] = zynk_default_product_image_directory_default;
        }
        else
        {
            $data['zynk_default_product_image_directory'] = $zynk_default_product_image_directory;
        }

        // default tax code
        $zynk_default_tax_code = $this->config->get('zynk_default_tax_code');
        if (isset($this->request->post['zynk_default_tax_code']))
        {
            $data['zynk_default_tax_code'] = $this->request->post['zynk_default_tax_code'];
        }
        elseif (!isset($zynk_default_tax_code))
        {
            $data['zynk_default_tax_code'] = zynk_default_tax_code_default;
        }
        else
        {
            $data['zynk_default_tax_code'] = $zynk_default_tax_code;
        }

        // default tax rate
        $zynk_default_tax_rate = $this->config->get('zynk_default_tax_rate');
        if (isset($this->request->post['zynk_default_tax_rate']))
        {
            $data['zynk_default_tax_rate'] = $this->request->post['zynk_default_tax_rate'];
        }
        elseif (!isset($zynk_default_tax_rate))
        {
            $data['zynk_default_tax_rate'] = zynk_default_tax_rate_default;
        }
        else
        {
            $data['zynk_default_tax_rate'] = $zynk_default_tax_rate;
        }

        // shipping as item line
        $zynk_shipping_as_item_line = $this->config->get('zynk_shipping_as_item_line');
        if (isset($this->request->post['zynk_shipping_as_item_line']))
        {
            $data['zynk_shipping_as_item_line'] = $this->request->post['zynk_shipping_as_item_line'];
        }
        elseif (!isset($zynk_shipping_as_item_line))
        {
            $data['zynk_shipping_as_item_line'] = zynk_shipping_as_item_line_default;
        }
        else
        {
            $data['zynk_shipping_as_item_line'] = $this->config->get('zynk_shipping_as_item_line');
        }

        // data download limit
        $zynk_download_limit = $this->config->get('zynk_download_limit');
        if (isset($this->request->post['zynk_download_limit']))
        {
            $data['zynk_download_limit'] = $this->request->post['zynk_download_limit'];
        }
        elseif (!isset($zynk_download_limit))
        {
            $data['zynk_download_limit'] = zynk_download_limit_default;
        }
        else
        {
            $data['zynk_download_limit'] = $zynk_download_limit;
        }
        
        // default vatable taxclass
        $zynk_vatable_taxclass = $this->config->get('zynk_vatable_taxclass');
        if (isset($this->request->post['zynk_vatable_taxclass']))
        {
            $data['zynk_vatable_taxclass'] = $this->request->post['zynk_vatable_taxclass'];
        }
        elseif (!isset($zynk_vatable_taxclass))
        {
            $data['zynk_vatable_taxclass'] = zynk_vatable_taxclass_default;
        }
        else
        {
            $data['zynk_vatable_taxclass'] = $zynk_vatable_taxclass;
        }
        
        // default nonvatable taxclass
        $zynk_nonvatable_taxclass = $this->config->get('zynk_nonvatable_taxclass');
        if (isset($this->request->post['zynk_nonvatable_taxclass']))
        {
            $data['zynk_nonvatable_taxclass'] = $this->request->post['zynk_nonvatable_taxclass'];
        }
        elseif (!isset($zynk_nonvatable_taxclass))
        {
            $data['zynk_nonvatable_taxclass'] = zynk_nonvatable_taxclass_default;
        }
        else
        {
            $data['zynk_nonvatable_taxclass'] = $zynk_nonvatable_taxclass;
        }

        // default exempt taxclass
        $zynk_exempt_taxclass = $this->config->get('zynk_exempt_taxclass');
        if (isset($this->request->post['zynk_exempt_taxclass']))
        {
            $data['zynk_exempt_taxclass'] = $this->request->post['zynk_exempt_taxclass'];
        }
        elseif (!isset($zynk_exempt_taxclass))
        {
            $data['zynk_exempt_taxclass'] = zynk_exempt_taxclass_default;
        }
        else
        {
            $data['zynk_exempt_taxclass'] = $zynk_exempt_taxclass;
        }
        
        // default vatable taxcode
        $zynk_vatable_taxcode = $this->config->get('zynk_vatable_taxcode');
        if (isset($this->request->post['zynk_vatable_taxcode']))
        {
            $data['zynk_vatable_taxcode'] = $this->request->post['zynk_vatable_taxcode'];
        }
        elseif (!isset($zynk_vatable_taxcode))
        {
            $data['zynk_vatable_taxcode'] = zynk_vatable_taxcode_default;
        }
        else
        {
            $data['zynk_vatable_taxcode'] = $zynk_vatable_taxcode;
        }

        // default nonvatable taxcode
        $zynk_nonvatable_taxcode = $this->config->get('zynk_nonvatable_taxcode');
        if (isset($this->request->post['zynk_nonvatable_taxcode']))
        {
            $data['zynk_nonvatable_taxcode'] = $this->request->post['zynk_nonvatable_taxcode'];
        }
        elseif (!isset($zynk_nonvatable_taxcode))
        {
            $data['zynk_nonvatable_taxcode'] = zynk_nonvatable_taxcode_default;
        }
        else
        {
            $data['zynk_nonvatable_taxcode'] = $zynk_nonvatable_taxcode;
        }

        // default exempt taxcode
        $zynk_exempt_taxcode = $this->config->get('zynk_exempt_taxcode');
        if (isset($this->request->post['zynk_exempt_taxcode']))
        {
            $data['zynk_exempt_taxcode'] = $this->request->post['zynk_exempt_taxcode'];
        }
        elseif (!isset($zynk_exempt_taxcode))
        {
            $data['zynk_exempt_taxcode'] = zynk_exempt_taxcode_default;
        }
        else
        {
            $data['zynk_exempt_taxcode'] = $zynk_exempt_taxcode;
        }

        // module status - enabled / disabled
        $zynk_status = $this->config->get('zynk_status');
        if (isset($this->request->post['zynk_status']))
        {
            $data['zynk_status'] = $this->request->post['zynk_status'];
        }
        elseif (!isset($zynk_status))
        {
            $data['zynk_status'] = zynk_status_default;
        }
        else
        {
            $data['zynk_status'] = $this->config->get('zynk_status');
        }

        // are we downloading cost prices . . .
        if (isset($this->request->post['zynk_download_cost_price']))
        {
            $data['zynk_download_cost_price'] = $this->request->post['zynk_download_cost_price'];
        }
        else
        {
            $data['zynk_download_cost_price'] = $this->config->get('zynk_download_cost_price');
        }

        // are we downloading cost prices . . .
        if (isset($this->request->post['zynk_upload_cost_price']))
        {
            $data['zynk_upload_cost_price'] = $this->request->post['zynk_upload_cost_price'];
        }
        else
        {
            $data['zynk_upload_cost_price'] = $this->config->get('zynk_upload_cost_price');
        }

        $this->template = 'module/zynk.tpl';
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/zynk.tpl', $data), $this->config->get('config_compression'));
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'module/zynk'))
        {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}
?>