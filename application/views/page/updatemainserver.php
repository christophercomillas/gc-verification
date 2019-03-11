<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel with-nav-tabs panel-info">
                    <div class="panel-heading panel-heading-x">
                        <ul class="nav nav-tabs">
                            <li class="active" style="font-weight:bold">
                                <a href="#tab1default" data-toggle="tab">Data For Import</a>
                            </li>
                            <li>
                                <a href="#tab2default" data-toggle="tab" style="font-weight:bold">Imported Data</a>
                            </li>
                            <span class="pull-right">
                                <button class="btn btn-warning" onclick="checktextfiles();"><i class="fa fa-cog"></i> Connection Checker</button>
                                <button class="btn btn-info" onclick="eodstorestextfiles();"><i class="fa fa-cog"></i> Server Update</button>                                
                            </span>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default"> 
                                <div class="form-container">                                     
                                    <div class="col-sm-12">
                                        <table class="table" id="_gcformigration">                                    
                                            <thead>
                                                <tr>
                                                    <th>Barcode</th>
                                                    <th>Denom</th>
                                                    <th>GC Type</th>
                                                    <th>Customer</th>
                                                    <th>Date</th>
                                                    <th>Verified By</th>
                                                    <th>EOD Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab2default">
                                <div class="form-container">  
                                    <table class="table list" id="_importedgc">                                    
                                        <thead>
                                            <tr>
                                                <th>Barcode</th>
                                                <th>Denomination</th>
                                                <th>GC Type</th>
                                                <th>Customer</th>
                                                <th>Imported By</th>
                                                <th>Date Imported</th>
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

    </section>
    <!-- /.content -->
</div>

<!-- /.content-wrapper -->
