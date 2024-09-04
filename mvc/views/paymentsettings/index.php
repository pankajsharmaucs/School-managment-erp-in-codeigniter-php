<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-paymentsettings"></i> <?=$this->lang->line('panel_title')?></h3>       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_paymentsettings')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <?php if(inicompute($payment_gateways)) {
                            $i = 0;
                            foreach ($payment_gateways as $payment_gateway) { ?>
                                <li class="<?php if($i == 0) echo 'active'; ?>"><a data-toggle="tab" href="#payment-gateway<?=$payment_gateway->id?>" aria-expanded="true">
                                    <?=$payment_gateway->name?>
                                </a></li>
                        <?php $i++; } } ?>
                    </ul>

                    <div class="tab-content">
                        <?php if(inicompute($payment_gateways)) {
                            $i = 0;
                            foreach ($payment_gateways as $payment_gateway) { ?>
                                <div class="tab-pane <?=($i==0) ? 'active' : ''?>" id="payment-gateway<?=$payment_gateway->id?>" role="tabpanel">
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <form class="form-horizontal" role="form" method="POST">
                                                <input style="display:none" type="text" value="<?=$payment_gateway->slug?>" name="payment_type">
                                                <?php if(isset($payment_gateway_options[$payment_gateway->id]) && inicompute($payment_gateway_options[$payment_gateway->id])) {
                                                    $options = $payment_gateway_options[$payment_gateway->id];
                                                    foreach ($options as $option) {
                                                        $optionLang = $option->payment_option;
                                                        if($option->type == 'text') { ?>
                                                            <div class="form-group <?=form_error($option->payment_option) ? 'text-danger' : ''?>">
                                                                <label for="<?=$option->payment_option?>" class="col-sm-2 control-label">
                                                                    <?=$this->lang->line($optionLang)?> 
                                                                    <span class="text-red">*</span>
                                                                </label>
                                                                <div class="col-sm-5">
                                                                    <input type="text" class="form-control <?=form_error($option->payment_option) ? 'is-invalid' : ''?>" id="<?=$option->payment_option?>" name="<?=$option->payment_option?>"  value="<?=set_value($option->payment_option, $option->payment_value)?>">
                                                                </div>
                                                                <span class="col-sm-4 control-label">
                                                                    <?=form_error($option->payment_option)?>
                                                                </span>
                                                            </div>
                                                        <?php } else if($option->type == 'select') { 
                                                            $activityArr = json_decode($option->activities, true); 
                                                            if(inicompute($activityArr)) { ?>
                                                                <div class="form-group <?=form_error($option->payment_option) ? 'text-danger' : ''?>">
                                                                    <label class="col-sm-2 control-label" for="<?=$option->payment_option?>">
                                                                        <?=$this->lang->line($optionLang)?> 
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="col-sm-5">
                                                                        <select class="form-control" name="<?=$option->payment_option?>" id="<?=$option->payment_option?>">
                                                                            <?php
                                                                            foreach($activityArr as $key => $select) {
                                                                                $optionSelected = '';
                                                                                if(!set_value($option->payment_option)) {
                                                                                    if($option->payment_value == $key) {
                                                                                        $optionSelected = 'selected';
                                                                                    }
                                                                                }
                                                                                else {
                                                                                    $optionSelected = 'selected';
                                                                                }

                                                                                echo '<option value="' . $key . '" ' . $optionSelected . '>' . $select . '</option>';
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <span class="col-sm-4 control-label">
                                                                        <?=form_error($option->payment_option)?>
                                                                    </span>
                                                                </div>
                                                    <?php } } } } ?>
                                                
                                                    <div class="form-group">
                                                        <div class="col-sm-offset-2 col-sm-8">
                                                            <input type="submit" class="btn btn-success" value="Save">
                                                        </div>
                                                    </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                        <?php $i++; } } ?>
                    </div>
                </div> <!-- nav-tabs-custom -->

            </div>
        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->