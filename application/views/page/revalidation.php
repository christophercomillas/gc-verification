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
                                <a href="#tab1default" data-toggle="tab">GC Revalidation</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default"> 
                                <div class="form-container">  
                                    
                                    <div class="col-md-3">
                                        <form class="form-horizontal" method="POST" action="<?php echo base_url(); ?>transaction/scanforrevalidation" id="_scanrevalgc">
                                            <div class="form-group">
                                                <label class="nobot">Date</label> 
                                                <input type="text" class="form form-control inptxt-l"  readonly="readonly" value="<?php echo $tdate; ?>">                                 
                                            </div>  
                                            <div class="form-group">
                                                <label class="nobot">Barcode #</label>
                                                <input type="text" class="form form-control inptxt-l" value="" name="barcode" id="barcode" autocomplete="off" autofocus maxlength="13">
                                            </div>
                                            <div class="response-scan">
                                        <!--    <div class="form-group">
                                                    <div class="alert alert-danger">
                                                        Sample
                                                    </div>
                                                </div> -->
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-block btn-bold" type="submit" id="btnscanreval"><i class="fa fa-barcode" aria-hidden="true"></i> Scan GC</button>
                                            </div>                                      
                                            
                                        </form>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="nobot">Revalidation Payment</label>
                                            <input type="text" class="form form-control inptxt-l" value="<?php echo $revalpayment; ?> per GC" readonly="readonly" autocomplete="off">
                                        </div>   
                                        <div class="form-group">
                                            <label class="nobot">GC Scanned Count</label>
                                            <input type="text" class="form form-control inptxt-l" value="0" readonly="readonly" id="gcscancou" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <label class="nobot">Total Revalidation Payment</label>
                                            <input type="text" class="form form-control inptxt-l" value="0.00" readonly="readonly" value="" name="totalrevalpayment" id="totalrevalpayment" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <label class="nobot">Payment Received</label>
                                            <input type="text" class="form form-control input-md bld" name="paymentreceived" id="paymentreceived" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" name="data" autocomplete="off" autofocus="" value="0.00" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" id="submitstatus" value="1">
                                            <button class="btn btn-success btn-block btn-bold" type="button" id="btnReval"> <i class="fa fa-save" aria-hidden="true"></i> Submit</button>
                                        </div>
                                        <div class="response">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table" id="revalidationtable">
                                            <thead>
                                                <tr>
                                                    <th>Barcode</th>
                                                    <th>Denomination</th>
                                                    <th>Payment</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>

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
