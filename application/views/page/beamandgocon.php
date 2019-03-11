<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel with-nav-tabs panel-info">
                    <div class="panel-heading panel-heading-x">
                        <ul class="nav nav-tabs">
                            <li class="active" style="font-weight:bold">
                                <a href="#tab1default" data-toggle="tab">Beam And Go Conversion</a>
                            </li>
                            <li>
                                <a href="#tab2default" data-toggle="tab" style="font-weight:bold">Beam And Go GC</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default"> 
                                <div class="row form-container">
                                    <form method="POST" id="_bngTransaction" enctype="multipart/form-data" action="<?php echo base_url(); ?>transaction/savebngTransaction">
                                        <div class="col-sm-12">
                                            <div class="col-sm-3">
                                                <div class="form-group mm4">
                                                    <label class="nobot">Date</label>   
                                                    <input type="text" class="form form-control inptxt input-sm bot-6 bld" readonly="readonly" value="<?php echo _dateFormat(todays_date());?>">   
                                                </div>
                                                <div class="form-group mm4">
                                                    <label class="nobot"><span class="requiredf">*</span>Transaction #</label>   
                                                    <input type="text" class="form inptxt form-control bld" readonly="readonly" value="<?php echo $bngtrnum; ?>" id="trnum">
                                                </div>
                                                <div class="form-group mm4">
                                                    <label class="nobot"><span class="requiredf">*</span>Total Amount</label>   
                                                    <input type="text" class="form inptxt form-control bld" readonly="readonly" value="0" id="totamt">
                                                </div>
                                                <div class="form-group mm4">
                                                    <label class="nobot"><span class="requiredf">*</span>GC Scanned</label>   
                                                    <input type="text" class="form inptxt form-control bld" readonly="readonly" value="0" id="gcscanned">
                                                </div>
                                                <div class="form-group mm4">
                                                    <label class="nobot">Prepared By</label>   
                                                    <input type="text" class="form form-control inptxt input-sm bot-6 bld" readonly="readonly" value="<?php echo $this->session->userdata('gc_fullname'); ?>" name="" id="">  
                                                </div>   
                                                <div class="form-group mm4">
                                                    <button class="btn btn-block btn-info fordialog" id="_scangcbng" type="button"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Scan GC</button>
                                                </div>    
                                                <div class="form-group mm4">
                                                    <div class="file-upload">
                                                        <label for="upload" class="file-upload__label"> <span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span> Upload File</label>
                                                        <input id="upload" class="file-upload__input" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="file-upload">
                                                    </div>
                                                </div>                                       
                                                <div class="form-group">
                                                    <button class="btn btn-primary btn-block" id="btnBNGSub"><span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Submit</button>  
                                                </div>
                                                <div class="response">

                                                </div>

                                            </div> 
                                            <div class="col-sm-9" id="_bgnscangcdiv">       
                                            <div id="_loadingtable">
                                                <img src='<?php echo base_url(); ?>assets/img/ajax.gif'>
                                                <small class='text-danger'>please wait...</small>
                                            </div>                                                                  
                                                <table class="table" id="_bgnscangc">
                                                    <thead>
                                                        <tr>
                                                            <th>Serial #</th>
                                                            <th>Amount</th>
                                                            <th>Barcode</th> 
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>                                             
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab2default">
                                <div class="form-container">  
                                    <table class="table" id="_bgnscangclist">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Ref #</th>
                                                <th>Serial Number</th>
                                                <th>GC Barcode</th>
                                                <th>Amount</th>     
                                                <th>Beneficiary</th>                                        
                                            </tr>
                                        </thead>
                                    </table>
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
