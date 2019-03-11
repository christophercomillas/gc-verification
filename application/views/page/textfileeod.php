<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel with-nav-tabs panel-info">
                    <div class="panel-heading panel-heading-x">
                        <ul class="nav nav-tabs">
                            <li class="active" style="font-weight:bold">
                                <a href="#tab1default" data-toggle="tab">Textfile EOD</a>

                            </li>
                            <span class="pull-right">
                                <button class="btn btn-warning" onclick="checktextfiles();"><i class="fa fa-cog"></i> Checker</button>
                                <button class="btn btn-info" onclick="eodstorestextfiles();"><i class="fa fa-cog"></i> Process EOD</button>                                
                            </span>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default"> 
                                <div class="form-container">                                     
                                    <div class="col-sm-12">
                                        <table class="table" id="_gcforeod">                                    
                                            <thead>
                                                <tr>
                                                    <th>Barcode</th>
                                                    <th>Denomination</th>
                                                    <th>GC Type</th>
                                                    <th>Customer</th>
                                                    <th>Date Verified/Reverified</th>
                                                    <th>Verified/Reveried By</th>
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
