<!-- <?php 
  // echo '<pre>';
  // print_r($info);
  // exit();
?>  -->

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Invoice Print</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 4 -->

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?=asset_url();?>adminlte/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=asset_url();?>adminlte/dist/css/adminlte.min.css">

  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inconsolata&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inconsolata', monospace !important;
      /* font-family: initial !important;
      font-size: initial !important; */
      font-size: 0.8em !important;
    }

    table, th, td {
      border: 1px solid black;
    }
  </style>
</head>
<body onload="window.print()">
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-12">
            Taters Enterprises
            <small class="float-right"><?php echo date('F d, Y', strtotime($info->dateadded));?></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info" style="font-size: 1em;">
        <div class="col-sm-4 invoice-col">
        From
        <address>
          <strong><?php echo $info->store_name;?></strong><br>
          <?php echo $info->store_address;?><br>
          <!-- San Francisco, CA 94107<br> -->
          Contact Person: <?php echo $info->store_person;?><br>
          Contact Number: <?php echo $info->store_contact;?><br>
          Email: <?php echo $info->store_email;?>
        </address>
        </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        To
        <address>
        <strong><?php echo $info->fname.' '.$info->lname;?></strong><br>
        <?php echo $info->address;?><br>
        <!-- San Francisco, CA 94107<br> -->
        Phone: <?php echo $info->contact_number;?><br>
        Email: <?php echo $info->email;?>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        <strong>Delivery Receipt #</strong> <?php echo ($info->invoice_num == '') ? '<span class="badge badge-pill badge-info">Order for confirmation</span>' : $info->invoice_num;?><br>
        <strong>Tracking Number #</strong> <?php echo $info->tracking_no;?><br>
        <?php $status_paid = array(2,3,6,8,9);?>
        <?php if($info->payops == 1){ ?>
          <b>Payment Options:</b> BPI<br>
        <?php } ?>
        <?php if($info->payops == 2){ ?>
          <b>Payment Options:</b> BDO<br>
        <?php } ?>
        <?php if($info->payops == 3){ ?>
          <b>Payment Options:</b> CASH<br>
        <?php } ?>
        
        <?php if($info->payops == 3){ ?>
          <b>Payment Status:</b> <?php echo ($info->status == 6) ? 'Paid' : '-'?><br>
        <?php } ?>

        <?php if($info->payops != 3){ ?>
          <?php $status_paid = array(2,3,6,8,9);?>
          <b>Payment Status:</b> <?php echo ($info->payment_proof != '' && in_array($info->status,$status_paid)) ? 'Paid' : '-'?><br>
        <?php } ?>

        <b>Mode of Handling:</b> <?php echo ($info->moh == 1) ? 'Pick-up' : 'Delivery';?><br>
        <b>Voucher Code:</b> <?php  echo $info->voucher_code;?><br>
        <!-- <b>Account:</b> 968-34567 -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    
    <div class="border-top"></div>
    <div class="mb-2 row">
      <?php if($info->moh == 2):?>
        <div class="col-5">
          <h5>Delivery Information</h5>
          <b>Address:</b>&emsp;<?php echo $info->add_address;?><br>
          <b>Contact Person:</b>&emsp;<?php echo $info->add_name;?><br>
          <b>Contact Number:</b>&emsp;<?php echo $info->add_contact;?><br>
        </div>
      <?php endif;?>
    </div>
    <hr>
    <div class="mb-2 row">
      <div class="col-7">
          <b>Note:</b> <?php echo ($info->moh == 1) ? 'Delivery and/or Pick-up of items from Monday to Sunday (except holidays) between 10:00 am to 5:00 pm.<br>Delivery and/or Pick-up of product would be 1-2 days after the payment has been uploaded.' : 'Delivery and/or Pick-up of items from Monday to Sunday (except holidays) between 10:00 am to 5:00 pm.<br>Delivery and/or Pick-up of product would be 1-2 days after the payment has been uploaded.<br>You will be charged with a delivery fee depending on your location. <br>Delivery fee is already included in the Grand Total. Our Sales Representative will reach out to you via sms once orders are now ready for delivery/pick-up.';?>
      </div>
    </div>
    <hr>
    <!-- Table row -->
    <div class="row">
        <div class="col-12 table-responsive">
            <table class="table table-striped table-sm">
            <thead>
            <tr>
                <th style="width:5%;">ID</th>
                <th style="width:25%;">Product</th>
                <!-- <th style="width:20%;">Description</th> -->
                <th style="width:25%;">Remarks</th>
                <th style="width:5%;">Qty</th>
                <th style="width:15%;">Price</th>
                <?php echo ($info->moh == 1) ? '<th style="width:15%;">Subtotal</th>' : '<th style="width:15%;">Total</th>';?>
            </tr>
            </thead>
            <tbody>
            <?php $ctr = 1; $total_val=0;?>
            <?php foreach ($orders as $key => $value): ?>
                    <?php $price = ( isset($value->promo_id ) && $value->promo_id == 0) ? $value->product_price : (isset($value->promo_id)  ?  $value->promo_price : $value->product_price);?>
                    <?php 
                      $discount_percentage = $value->promo_discount_percentage;

                      if(isset($discount_percentage)){
                        $price = $price - ($price * $discount_percentage );
                      }
                    ?>
                    <?php $row_sum = $price * $value->quantity;?>
                    <?php if( isset($value->promo_id ) && $value->promo_id != 0){$have_promo=1;}?>
                    <tr>
                      <td style="text-align: center;"><?php echo $ctr;?></td>
                      <td>
                        <?php if(!isset($value->product_label)): ?>
                          <?php if(isset($value->alias)):?>
                            <span span class="text-xs text-secondary"><?php echo $value->alias;?></span><br>
                          <?php endif;?>
                          <?php echo $value->name;?><br>
                          <?php if(isset($value->add_details)):?>
                            <span span class="text-xs text-secondary"><?php echo $value->add_details;?></span>
                          <?php endif;?>
                            <?php if(isset($value->addon_base_product)):?>
                              <br>
                              <b>Add-on for:</b>
                              <?php echo $value->addon_base_product_name;?>
                            <?php endif;?>
                            <?php if($price == 0):?>
                              <br>
                              <b>FREE</b>
                            <?php endif;?>
                        <?php else: ?>
                          <?php echo $value->product_label.' '. $value->name;?><br><span class="text-xs text-secondary"><?php echo $value->add_details;?></span>
                          <?php if($value->addon_base_product != null):?>
                              <br>
                              <b>Add-on for:</b>
                              <?php echo $value->addon_base_product_name;?>
                          <?php endif;?>
                          <?php if($price == 0):?>
                              <br>
                              <b>FREE</b>
                            <?php endif;?>
                        <?php endif; ?>
                        
                        <?php if(isset($value->promo_id)): ?>
                          <?php echo ($value->promo_id == 0) ? '' : '<span class="badge badge-pill badge-warning">Promo Item</span>';?>
                        <?php endif;?>
                      </td>
                      <?php
                        $selected_flavors = json_decode($value->remarks);
                        $flavors = '';
                        if($selected_flavors != null){                 
                          foreach ($selected_flavors as $k => $val) {
                              if($k == 0){
                                  $flavors .= $val->name.' x'.$val->value;
                              }else{
                                  $flavors .= ', '.$val->name.' x'.$val->value;
                              }
                          }
                        }else{
                          $flavors = $value->remarks;
                        }
                      ?>
                      <td style="text-align: center;"><?php echo $flavors;?></td>
                      <td style="text-align: center;"><?php echo $value->quantity;?></td>
                      <td style="text-align: right;">
                        <?php if(isset($value->promo_id)):?>
                          <?php echo ( $value->promo_id == 0) ? '' : '<s class="text-success">'.number_format($value->price,2).'</s><br>';?>
                        <?php else:?>
                          <?php echo  number_format($price,2);?>
                        <?php endif;?>
                      </td>
                      <td style="text-align: right;"><?php echo number_format($row_sum,2);?></td>
                    </tr>
                    <?php $ctr++;?>
                    <?php $total_val +=$row_sum;?>
                  <?php endforeach; ?>
            </tbody>
            <tfoot> 
              <?php //if($info->moh == 2) { ?>
                <tr>
                  <th colspan="5" style="text-align:left;"><span >Total:</span></th>
                  <td style="text-align: right;"><span>₱</span><span style="padding-left: 30%; padding-right: 80%;"><?php echo number_format($info->purchase_amount,2);?></span></td>
                </tr>
                <?php if($info->reseller_id == 0){ ?>
                  <?php 
                    if($info->discount != ""){
                      $vdata = $info->discount;
                    }else{
                      $vdata = 0;
                    }

                  ?>
                  <tr>
                    <th colspan="5" style="text-align:left;"><span >Voucher Discount:</span></th>
                    <td style="text-align: right;"><span>₱</span><span style="padding-left: 30%; padding-right: 80%;"><?php echo number_format($vdata,2);?></span></td>
                  </tr>
                  <tr>  
                    <th colspan="5" style="text-align:left;"><span >SubTotal:</span></th>
                    <td style="text-align: right;"><span>₱</span><span style="padding-left: 30%; padding-right: 80%;"><?php echo number_format($info->purchase_amount - $vdata ,2);?></span></td>
                  </tr>
                  <?php if($info->moh == 2 && $info->cod_fee != 0){ ?>
                    <tr>  
                      <th colspan="5" style="text-align:left;"><span >COD Additional Charges:</span></th>
                      <td style="text-align: right;"><span>₱</span><span style="padding-left: 30%; padding-right: 80%;"><?php echo number_format($info->cod_fee,2);?></span></td>
                    </tr>
                  <?php } ?>
                  <tr>
                    <th colspan="5" style="text-align:left;"><span >Delivery Fee:</span></th>
                    <?php $distance_fee = (isset($info->distance_price) && $info->moh == 2) ? $info->distance_price : 0; ?>
                    <td style="text-align: right;"><span>₱</span><span style="padding-left: 30%; padding-right: 80%;"><?php echo number_format($distance_fee,2);?></span></td>
                <?php }else{ ?>
                  <?php 
                    $vdata2 = $info->reseller_discount;
                    $info_total = $info->purchase_amount;
                    $discount_check = number_format($vdata2,2) / number_format($info_total);
                    // echo $discount_check;
                  ?>
                  <tr>
                    <th style="width:60%"><h5 class="text-right">Reseller Discount (<?php echo ($discount_check == 0.2) ? "20%" : "25%" ?>):</h5></th>
                    <td style="width:40%"><h5>₱ <?php echo number_format($vdata2,2);?></h5></td>
                  </tr>
                <?php } ?>
              </tr>
              <?php if($info->reseller_id == 0){ ?>
                <tr>
                  <th colspan="5" style="text-align:left;"><span >Grand Total:</span></th>
                  <td style="text-align: right;"><span>₱</span><span style="padding-left: 30%; padding-right: 80%;"><?php echo number_format(($info->purchase_amount + $distance_fee + $info->cod_fee) - $vdata,2);?></span></td>
                </tr>
              <?php } else { ?>
                <tr>
                  <th style="width:60%"><h5 class="text-right">Grand Total:</h5></th>
                  <td style="width:40%"><h5>₱ <?php echo number_format(($info->purchase_amount) - $vdata2,2);?></h5></td>
                </tr>
              <?php } ?>
                    
              <?php //} else { ?>
                <!-- <tr>
                  <th colspan="3" style="width:60%"><span >Total:</span></th>
                  <td colspan="3" style="width:40%"><span>₱ <?php //echo number_format($info->purchase_amount,2);?></span></td>
                </tr> -->
              <?php //} ?>
            </tfoot>
            </table>
        </div>
    <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
      <!-- accepted payments column -->
      <div class="col-4">
        <p class="lead">Remarks:</p><b>
        <?php
          if($info->remarks != ''){
            $print_remarks = json_decode($info->remarks);
            $print = '';
            foreach ($print_remarks as $key => $value) {
              $print .= '   <div class="timeline-item" style="color: red;" >
                              <div class="timeline-body">
                              '.$value->message.'
                              </div>
                              <div class="timeline-footer">
                                <span class=""><i class="fas fa-user"></i> '.$value->user_name.'</span>
                                <span class="float-right time"><i class="fas fa-clock"></i> '.$value->dateadded.'</span>
                              </div>
                            </div>
                            <hr>
                        ';
            }
            echo $print;
          }else{
            echo "<p>--</p>";
          }
        ?>
        </b>
        <p class="shadow-none text-muted well well-sm" style="margin-top: 10px;"></p>
      </div>
      <!-- /.col -->
      <!-- <div class="col-6 d-none"> -->
        <!-- <p class="lead">Amount Due 2/22/2014</p> -->

        <!-- <div class="table-responsive">
          <table class="table table-sm"> -->
            <!-- <tr>
              <th style="width:50%">Subtotal:</th>
              <td>$250.30</td>
            </tr>
            <tr>
              <th>Tax (9.3%)</th>
              <td>$10.34</td>
            </tr>
            <tr>
              <th>Shipping:</th>
              <td>$5.80</td>
            </tr>
            <tr>
              <th>Total:</th>
              <td>$265.24</td>
            </tr> -->
            <!-- <tr>
                <th style="width:75%"><h4>Total:</h4></th>
                <td><h4>₱ <?php //echo number_format($info->purchase_amount,2);?></h4></td>
            </tr>
          </table>
        </div> -->
      <!-- </div> -->
      <!-- /.col -->
    </div>

    <div class="mt-5 row">
      <div class="col-12">
        <div class="table-responsive">
            <table class="table border table-sm border-dark">
              <tr>
                <th style="width:50%">Prepared by:</th>
                <th>Received in good order and condition by:</th>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <!-- <tr>
                <th>Shipping:</th>
                <td>$5.80</td>
              </tr>
              <tr>
                <th>Total:</th>
                <td>$265.24</td>
              </tr> -->
              <tr>
                  <td>Approved by:</td>
                  <td>Printed Name & Signature</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->

<!-- <script type="text/javascript"> 
  window.addEventListener("load", window.print());
</script> -->
<script type="text/javascript">       
    window.onafterprint = function(){
        window.close();
    }
</script>
</body>
</html>
