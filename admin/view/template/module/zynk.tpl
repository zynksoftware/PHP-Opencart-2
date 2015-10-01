<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-information" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit_module; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-information" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="zynk_status" id="input-status" class="form-control">
                <?php if ($zynk_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          <h2>Orders</h2>
          <table id="module" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left" width="30%"><?php echo $text_option; ?></td>
                <td class="text-left" width="70%"><?php echo $text_status; ?></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-left"><?php echo $entry_download_orders; ?></td>
                <td class="text-left">
                  <select name="zynk_download_orders" class="form-control">
                    <?php if ($zynk_download_orders) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Bring orders down into Sage</i>
                </td>                
              </tr>
              <tr>
                <td class="text-left"><?php echo $entry_download_type; ?></td>
                <td class="text-left">
                  <select name="zynk_download_type" class="form-control">
                    <?php if ($zynk_download_type == 'sales_order') { ?>
                      <option value="sales_order" selected="selected"><?php echo $text_sales_order; ?></option>
                    <?php } else { ?>
                      <option value="sales_order"><?php echo $text_sales_order; ?></option>
                    <?php } ?>
                    <?php if ($zynk_download_type == 'invoice') { ?>
                      <option value="invoice" selected="selected"><?php echo $text_invoice; ?></option>
                    <?php } else { ?>
                      <option value="invoice"><?php echo $text_invoice; ?></option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Bring orders down into Sage in this format.</i>
                </td>               
              </tr> 
              <tr>
                  <td class="text-left"><?php echo $entry_download_stage; ?></td>
                  <td class="text-left">
                      <select name="zynk_download_stage" class="form-control">
                      <?php
                          foreach ($order_status as $status)
                          {
                              $selected = '';
                              if ($zynk_download_stage == $status['order_status_id'])
                              {
                                  $selected = 'selected="selected"';
                              }

                              echo('<option value="'.$status['order_status_id'].'" '.$selected.'>'.$status['name'].'</option>');
                          }
                      ?>
                      </select><i>&nbsp;&nbsp;&nbsp;Bring only those orders down into Sage that are at the specified Stage.</i>
                </td>
              </tr>  
              <tr>
                  <td class="text-left"><?php echo $entry_notify_stage; ?></td>
                  <td class="text-left">
                      <select name="zynk_notify_stage" class="form-control">
                      <?php
                          foreach ($order_status as $status)
                          {
                              $selected = '';
                              if ($zynk_notify_stage == $status['order_status_id'])
                              {
                                  $selected = 'selected="selected"';
                              }

                              echo('<option value="'.$status['order_status_id'].'" '.$selected.'>'.$status['name'].'</option>');
                          }
                      ?>
                      </select><i>&nbsp;&nbsp;&nbsp;The stage that an order is set to once it has been successfully posted into Sage.</i>
                </td>
              </tr>
              <tr>
                <td class="text-left"><?php echo $entry_taken_by; ?></td>
                <td class="text-left"><input type="text" class="form-control" id="zynk_taken_by" value="<?php echo($zynk_taken_by); ?>" /><i>&nbsp;&nbsp;&nbsp;The value for the order taken by field within Sage.</i> 
              </tr>
              <tr>
                <td class="text-left"><?php echo $entry_download_limit; ?></td>
                <td class="text-left"><input type="text" class="form-control" id="zynk_download_limit" value="<?php echo($zynk_download_limit); ?>" /><i>&nbsp;&nbsp;&nbsp;The value for the order taken by field within Sage.</i> 
              </tr>  
              <tr>
                <td class="text-left"><?php echo $entry_shipping_as_item_line; ?></td>
                <td class="text-left">
                  <select name="zynk_shipping_as_item_line" class="form-control">
                    <?php if ($zynk_shipping_as_item_line) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Bring the shipping down as an individual line item, or as carriage on the order.</i>
                </td>                
              </tr>                          
            </tbody>
          </table>   
          <h2>Products</h2>
          <table id="module" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left" width="30%"><?php echo $text_option; ?></td>
                <td class="text-left" width="70%"><?php echo $text_status; ?></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-left"><?php echo $entry_download_products; ?></td>
                <td class="text-left">
                  <select name="zynk_download_products" class="form-control">
                    <?php if ($zynk_download_products) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Download products into Sage from the website.</i>
                </td>                
              </tr>   
              <tr>
                <td class="text-left"><?php echo $entry_download_cost_price; ?></td>
                <td class="text-left">
                  <select name="zynk_download_cost_price" class="form-control">
                    <?php if ($zynk_download_cost_price) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Download Cost Prices from the website to Sage (will NOT work with automatic product create/update)</i>
                </td>                
              </tr> 
              <tr>
                <td class="text-left"><?php echo $entry_upload_products; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_products" class="form-control">
                    <?php if ($zynk_upload_products) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload products from Sage into the website.</i>
                </td>                
              </tr>   
              <tr>
                <td class="text-left"><?php echo $entry_upload_product_images; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_product_images" class="form-control">
                    <?php if ($zynk_upload_product_images) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload a single image per product from Sage to the website</i>
                </td>                
              </tr> 
              <tr>
                <td class="text-left"><?php echo $entry_default_product_image_directory; ?></td>
                <td class="text-left"><input <?php if (!$zynk_upload_product_images) { ?> <?php } ?> type="text" id="zynk_default_product_image_directory" name="zynk_default_product_image_directory" class="form-control" value="<?php echo($zynk_default_product_image_directory); ?>" /><i>&nbsp;&nbsp;&nbsp;The default location for images to assign to products.</i>
              </tr>   
              <tr>
                <td class="text-left"><?php echo $entry_upload_product_descriptions; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_product_descriptions" class="form-control">
                    <?php if ($zynk_upload_product_descriptions) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload Product Descriptions from Sage to the website</i>
                </td>                
              </tr> 
              <tr>
                <td class="text-left"><?php echo $entry_upload_product_quantities; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_product_quantities" class="form-control">
                    <?php if ($zynk_upload_product_quantities) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload Product Quantities from Sage to the website</i>
                </td>                
              </tr>        
              <tr>
                <td class="text-left"><?php echo $entry_upload_product_price_breaks; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_product_price_breaks" class="form-control">
                    <?php if ($zynk_upload_product_price_breaks) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload Product Price Breaks from Sage to the website</i>
                </td>                
              </tr> 
              <tr>
                <td class="text-left"><?php echo $entry_upload_product_special_offer; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_product_special_offer" class="form-control">
                    <?php if ($zynk_upload_product_special_offer) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload Product Special Offers from Sage to the website/i>
                </td>                
              </tr>     
              <tr>
                <td class="text-left"><?php echo $entry_upload_cost_price; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_cost_price" class="form-control">
                    <?php if ($zynk_upload_cost_price) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;<i>&nbsp;&nbsp;&nbsp;Upload Cost Prices from Sage to the website (cost_price field must exist)</i>.</i>
                </td>                
              </tr>   
              <tr>
                <td class="text-left"><?php echo $entry_upload_pricelists; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_pricelists" class="form-control">
                    <?php if ($zynk_upload_pricelists) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload Price Lists from Sage to the website</i>
                </td>                
              </tr>  
              <tr>
                <td class="text-left"><?php echo $entry_upload_categories; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_categories" class="form-control">
                    <?php if ($zynk_upload_categories) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload Categories from Sage to the website.</i>
                </td>                
              </tr>
              <tr>
                <td class="text-left"><?php echo $entry_default_category; ?></td>
                <td class="text-left">
                  <select name="zynk_default_category" class="form-control">
                    <option value="" <?php if ($zynk_default_category == '0') { echo('selected="selected"'); } ?>> -- None -- </option>
                    <?php
                      foreach ($categories as $category)
                      {
                        echo('<option value="' . $category['category_id'] . '"');
                        if ($zynk_default_category = $category['category_id']) { echo('selected="selected"'); }
                        echo ('>' . $category['name'] . '</option>');
                      }
                    ?>
                  </select><i>&nbsp;&nbsp;&nbsp;The default category to assign to products when NOT uploading categories from Sage. This will ONLY be visible within the admin area.</i>
                </td>
              </tr>                                                                                                                                             
            </tbody>
          </table>     
          <h2>Customers</h2>          
          <table id="module" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left" width="30%"><?php echo $text_option; ?></td>
                <td class="text-left" width="70%"><?php echo $text_status; ?></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-left"><?php echo $entry_download_customers; ?></td>
                <td class="text-left">
                  <select name="zynk_download_customers" class="form-control">
                    <?php if ($zynk_download_customers) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Download customers to as individual accounts into Sage, otherwise they will all be allocated a single Account Reference, defined below.</i>
                </td>                 
              </tr>
              <tr>
                <td class="text-left"><?php echo $entry_upload_customers; ?></td>
                <td class="text-left">
                  <select name="zynk_upload_customers" class="form-control">
                    <?php if ($zynk_upload_customers) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Upload Customers from Sage to the website.</i>
                </td>                 
              </tr>  
              <tr>
                <td><?php echo $entry_account_reference; ?></td>
                <td><input type="text" id="zynk_account_reference" name="zynk_account_reference" class="form-control" value="<?php echo($zynk_account_reference); ?>" /><i>&nbsp;&nbsp;&nbsp;If 'Download Customers' is NOT selected or a customer cannot be found when downloading the order then the specified Account Reference for Sage will be used.</i>
              </tr>                          
            </tbody>
          </table>  
          <h2>Tax</h2>
          <table id="module" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left" width="30%"><?php echo $text_option; ?></td>
                <td class="text-left" width="70%"><?php echo $text_status; ?></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php echo $entry_default_tax_code; ?></td>
                <td><input type="text" id="zynk_default_tax_code" class="form-control" name="zynk_default_tax_code" value="<?php echo($zynk_default_tax_code); ?>" /><i>&nbsp;&nbsp;&nbsp;The default tax code to use.</i>
              </tr>
              <tr>
                <td><?php echo $entry_default_tax_rate; ?></td>
                <td><input type="text" class="form-control" id="zynk_default_tax_rate" name="zynk_default_tax_rate" value="<?php echo($zynk_default_tax_rate); ?>" /><i>&nbsp;&nbsp;&nbsp;The default tax rate to use.</i>
              </tr> 
              <tr>
                <td class="text-left"><?php echo $entry_vatable_taxclass; ?></td>
                <td class="text-left">
                  <select name="zynk_vatable_taxclass" class="form-control">
                    <option value="" <?php if ($zynk_vatable_taxclass == '0') { echo('selected="selected"'); } ?>> -- None -- </option>
                    <?php
                      foreach ($tax_classes as $tax_class)
                      {
                        echo('<option value="' . $tax_class['tax_class_id'] . '"');
                        if ($zynk_vatable_taxclass = $tax_class['tax_class_id']) { echo('selected="selected"'); }
                        echo ('>' . $tax_class['title'] . '</option>');
                      }
                    ?>
                  </select><i>&nbsp;&nbsp;&nbsp;The tax class denoting taxable products upon the website.</i>
                </td>
              </tr>  
              <tr>
                <td class="text-left"><?php echo $entry_nonvatable_taxclass; ?></td>
                <td class="text-left">
                  <select name="zynk_nonvatable_taxclass" class="form-control">
                    <option value="" <?php if ($zynk_nonvatable_taxclass == '0') { echo('selected="selected"'); } ?>> -- None -- </option>
                    <?php
                      foreach ($tax_classes as $tax_class)
                      {
                        echo('<option value="' . $tax_class['tax_class_id'] . '"');
                        if ($zynk_nonvatable_taxclass = $tax_class['tax_class_id']) { echo('selected="selected"'); }
                        echo ('>' . $tax_class['title'] . '</option>');
                      }
                    ?>
                  </select><i>&nbsp;&nbsp;&nbsp;The tax class denoting non taxable products upon the website.</i>
                </td>
              </tr>
              <tr>
                <td class="text-left"><?php echo $entry_exempt_taxclass; ?></td>
                <td class="text-left">
                  <select name="zynk_exempt_taxclass" class="form-control">
                    <option value="" <?php if ($zynk_exempt_taxclass == '0') { echo('selected="selected"'); } ?>> -- None -- </option>
                    <?php
                      foreach ($tax_classes as $tax_class)
                      {
                        echo('<option value="' . $tax_class['tax_class_id'] . '"');
                        if ($zynk_exempt_taxclass = $tax_class['tax_class_id']) { echo('selected="selected"'); }
                        echo ('>' . $tax_class['title'] . '</option>');
                      }
                    ?>
                  </select><i>&nbsp;&nbsp;&nbsp;The tax class denoting tax exempt products upon the website.</i>
                </td>
              </tr>    
              <tr>
                <td><?php echo $entry_vatable_taxcode; ?></td>
                <td><input type="text" class="form-control" id="zynk_vatable_taxcode" name="zynk_vatable_taxcode" value="<?php echo($zynk_vatable_taxcode); ?>" /><i>&nbsp;&nbsp;&nbsp;The tax code to use for taxable products within Sage.</i></td>
              </tr> 
              <tr>
                <td><?php echo $entry_nonvatable_taxcode; ?></td>
                <td><input type="text" class="form-control" id="zynk_nonvatable_taxcode" name="zynk_nonvatable_taxcode" value="<?php echo($zynk_nonvatable_taxcode); ?>" /><i>&nbsp;&nbsp;&nbsp;The tax code to use for non taxable products within Sage.</i></td>
              </tr>      
              <tr>
                <td><?php echo $entry_exempt_taxcode; ?></td>
                <td><input type="text" class="form-control" id="zynk_exempt_taxcode" name="zynk_exempt_taxcode" value="<?php echo($zynk_exempt_taxcode); ?>" /><i>&nbsp;&nbsp;&nbsp;The tax code to use for tax exempt products within Sage.</i></td>
              </tr>                                                                                     
            </tbody>
          </table>
          <h2>Payments</h2>
          <table id="module" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left" width="30%"><?php echo $text_option; ?></td>
                <td class="text-left" width="70%"><?php echo $text_status; ?></td>
              </tr>
            </thead>      
            <tbody>
              <tr>
                <td class="text-left"><?php echo $entry_download_payments; ?></td>
                <td class="text-left">
                  <select name="zynk_download_payments" class="form-control">
                    <?php if ($zynk_download_payments) { ?>
                      <option value="1" selected="selected">Yes</option>
                      <option value="0">No</option>
                    <?php } else { ?>
                      <option value="1">Yes</option>
                      <option value="0" selected="selected">No</option>
                    <?php } ?>
                  </select><i>&nbsp;&nbsp;&nbsp;Download payments from the website to Sage.</i>
                </td>                
              </tr>
              <tr>
                <td><?php echo $entry_bank_account; ?></td>
                <td><input type="text" id="zynk_bank_account" class="form-control" name="zynk_bank_account" value="<?php echo($zynk_bank_account); ?>" /><i>&nbsp;&nbsp;&nbsp;Default Bank account for payments to be allocated to.</i>
              </tr>                            
            </tbody>
          </table>   
          </div>    
        </form>
      </div>
    </div>
  </div>
</div>