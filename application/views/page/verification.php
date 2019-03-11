<div id="print-receipt-verify">       

</div>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel with-nav-tabs panel-info">
                    <div class="panel-heading panel-heading-x">
                        <ul class="nav nav-tabs">
                            <li class="active" style="font-weight:bold">
                                <a href="#tab1default" data-toggle="tab">GC Verification</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default"> 
                                <div class="form-container">  
                                    <form class="form-horizontal" method="POST" action="<?php echo base_url(); ?>transaction/gcVerificationOffline" id="_verifygc2">
                                        <input type="hidden" name="isreprint" id="isreprint" value="0">
                                        <input type="hidden" name="isreverified" id="isreverified" value="0">  
                                        <input type="hidden" name="isrecreatetxtfile" id="isrecreatetxtfile" value="0">  
                                        <div class="col-sm-7">
                                            <div class="form-group">
                                                <label class="col-xs-4 control-label">Date:</label>
                                                <div class="col-xs-4"><input type="text" class="form inptxt form-control input-l bld" readonly="readonly" value="<?php echo $tdate; ?>"></div>
                                            </div><!-- end of form-group -->

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Pay to:</label>
                                                <div class="col-xs-8">
                                                    <select class="form form-control inptxt input-md bld" name="payto" id="payto" required>
                                                        <option value="">- Select -</option>
                                                        <option value="STORE DEPARTMENT">Store Department</option>
                                                        <option value="WHOLESALE">Wholesale</option>
                                                    </select>
                                                </div>
                                            </div><!-- end of form-group -->                                            


                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Amount:</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form form-control input-md bld" name="denomination" id="denomination" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" name="data" autocomplete="off" autofocus="" value="0.00" required>                                                                 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Amount in words:</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form form-control" readonly="readonly" id="amtinwords"></textarea>                     
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">GC Type:</label>
                                                <div class="col-sm-5">
                                                    <select class="form form-control input-md bld" name="gctype" id="gctype" required>
                                                        <option value="">-Select-</option>
                                                        <option value="1">Regular</option>
                                                        <option value="3">Special External</option>
                                                        <option value="4">Promo</option>
                                                        <option value="6">Beam And Go</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-xs-4 control-label control-label-lg">GC Barcode Number:</label>
                                                <div class="col-xs-8">
                                                    <input data-inputmask="'alias': 'numeric', 'groupSeparator': '', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '', 'placeholder': ''" class="form form-control input-lg input-lg-o" id="gcbarcodever" name="gcbarcode" autocomplete="off" maxlength="13" autofocus required>
                                                </div>
                                            </div><!-- end of form-group -->
                                            <div class="form-group">
                                                <label class="col-xs-offset-3 col-xs-4 control-label">Verified by:</label>
                                                <div class="col-xs-5">
                                                    <input type="text" class="form inptxt form-control input-l bld" readonly="readonly" value="<?php echo $this->session->userdata('gc_fullname'); ?>">
                                                </div>
                                            </div><!-- end of form-group -->
                                            <div class="form-group">                  
                                                <div class="col-xs-offset-8 col-xs-4">
                                                    <button type="submit" class="btn btn-block btn-primary verifybtn">
                                                        <span class="glyphicon glyphicon-share" aria-hidden="true"></span>
                                                        Submit
                                                    </button>
                                                </div>
                                            </div><!-- end of form-group -->
                                            <div class="response">
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                                <?php if(trim($txfilestatus)!=''): ?>
                                                    <div class="callout callout-danger lead"><span class="callout-error"><?php echo $txfilestatus; ?></span></div>
                                                <?php endif; ?>
                                                <div class="form-group">
                                                    <label class="col-sm-12 control-label" style="text-align:left;">Customer Search:</label>
                                                </div>
                                                <div class="form-group bot16">                                                    
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <input name="checked" type="text" class="form-control inptxt-l" autocomplete="off" required="required" id="_vercussearch">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-info btn-find btn-md" type="button">
                                                                    <span class="glyphicon glyphicon-search"></span>
                                                                </button>
                                                            </span>
                                                        </div><!-- input group -->
                                                        <span class="xcus">
                                                            <ul>
                                                            </ul>
                                                        </span>                               
                                                    </div>
                                                </div>
                                                <div class="form-group bot16">
                                                    <div class="col-xs-12">
                                                        <button type="button" class="btn btn-block btn-info fordialog" onclick="addNewCustomer();"><i class="fa fa-user-plus"></i>
                                                        Add New Customer </button>
                                                    </div>
                                                </div><!-- end of form-group -->
                                            <div class="customerdetails">
                                                <i class="fa fa-user"></i>
                                                Customer Details
                                            </div>
                                            <div class="customerdetails-container">
                                                <input type="hidden" name="cus-id" value="" id="cid">
                                                <div class="form-group">
                                                    <label class="col-xs-5 control-label">First Name:</label>
                                                    <div class="col-xs-7">
                                                        <input type="text" class="form-control inptxt input-xs" id="fname" readonly="readonly">                      
                                                    </div>
                                                </div><!-- end of form-group -->
                                                <div class="form-group">
                                                    <label class="col-xs-5 control-label">Last Name:</label>
                                                    <div class="col-xs-7">
                                                        <input type="text" class="form-control inptxt input-xs" id="lname" readonly="readonly">                      
                                                    </div>
                                                </div><!-- end of form-group -->
                                                <div class="form-group">
                                                    <label class="col-xs-5 control-label">Middle Name:</label>
                                                        <div class="col-xs-7">
                                                            <input type="text" class="form-control inptxt input-xs" id="mname" readonly="readonly">                      
                                                        </div>
                                                </div><!-- end of form-group -->
                                                <div class="form-group">
                                                    <label class="col-xs-5 control-label">Name Ext:</label>
                                                    <div class="col-xs-7">
                                                        <input type="text" class="form-control inptxt input-xs" id="next" readonly="readonly">                      
                                                    </div>
                                                </div><!-- end of form-group --> 
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-offset-6 col-xs-6">
                                                    <button class="btn btn-block btn-danger" type="button" onclick="reprintVerification(<?php echo $_SESSION['gc_id']; ?>)"><i class="fa fa-key"></i> Manager Key</button>
                                                </div>
                                            </div>
                                        </div>    
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>

<!-- /.content-wrapper -->
