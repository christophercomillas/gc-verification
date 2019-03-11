<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel with-nav-tabs panel-info">
                    <div class="panel-heading panel-heading-x">
                        <ul class="nav nav-tabs">
                            <li class="active" style="font-weight:bold">
                                <a href="#tab1default" data-toggle="tab">GC Request</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default"> 
                                <div class="form-container">  
                                    <form method="POST" action="<?php echo base_url(); ?>transaction/gcrequestvalidation" id="_gcrequest" enctype="multipart/form-data">
                                        <div class="row">                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="nobot">Transaction #</label>
                                                    <input type="text" class="form form-control inptxt-l" readonly="readonly" value="<?php echo $requestnum; ?>" name="reqnum" id="reqnum">
                                                </div>
                                                <div class="form-group">
                                                    <label class="nobot">Retail Store</label> 
                                                    <input type="text" class="form form-control inptxt-l"  readonly="readonly" value="<?php echo $this->session->userdata('gc_store'); ?>">                                 
                                                </div>  
                                                <div class="form-group">
                                                    <label class="nobot">Date Requested</label> 
                                                    <input type="text" class="form form-control inptxt-l"  readonly="readonly" value="<?php echo $tdate; ?>">                                 
                                                </div>     
                                                <div class="form-group">
                                                    <label class="nobot"><span class="requiredf">*</span>Date Needed</label> 
                                                    <div class="input-group date">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" class="form-control pull-right input.inptxt-l" id="datepicker" name="dateneed" required>
                                                    </div>                 
                                                </div>       
                                                <div class="form-group">
                                                    <label class="nobot">Upload Document</label> 
                                                    <input type="file" id="file" name="doc" accept="image/*" class="form form-control inptxt" />                                 
                                                </div> 
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="col-sm-6" style="text-align:right">Denomination</label>
                                                        <label class="col-sm-6"><span class="requiredf">*</span>Qty</label>
                                                    </div>

                                                    <?php foreach ($denoms as $d): ?>
                                                        <div class="form-group">
                                                            <label class="col-sm-6 control-label">&#8369 <?php echo number_format($d->denomination,2); ?></label>
                                                            <div class="col-sm-5">
                                                                <input type="hidden" id="m<?php echo $d->denom_id; ?>" value="<?php echo $d->denomination; ?>"/>
                                                                <input type="text" class="form form-control inptxt-l" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" id="dennum<?php echo $d->denom_id; ?>" name="denoms<?php echo $d->denom_id; ?>" autocomplete="off" autofocus value="0"/>
                                                            </div>
                                                        </div><!-- end of form-group -->
                                                    <?php endforeach; ?>
                                                </div>
                                                <div class="labelinternaltot">
                                                    <label>Total Amount: <span id="internaltot">0.00</span></label>
                                                </div>
                                                <div class="labelinternaltot">
                                                    <label>Total Qty: <span id="totgcreqqty">0</span></label>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="nobot"><span class="requiredf">*</span>Remarks</label>
                                                    <textarea class="form form-control inptxt-l" name="remarks" required></textarea>
                                                </div>
                                                <div class="response">
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-offset-5 col-sm-7">
                                                        <button type="submit" class="btn btn-block btn-primary btn-flat" id="externalbtn"> <span class="glyphicon glyphicon-share" aria-hidden="true"></span> Submit</button>
                                                    </div>
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
