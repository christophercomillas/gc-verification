<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel with-nav-tabs panel-info">
                    <div class="panel-heading panel-heading-x">
                        <ul class="nav nav-tabs">
                            <li class="active" style="font-weight:bold">
                                <a href="#tab1default" data-toggle="tab">Pending GC Request</a>
                            </li>
                                <span class="pull-right">
                                    <button class="btn btn-info" id="updateServerBut" onclick="updateServerPendingGCRequest()"><i class="fa fa-cog"></i> Update Server</button>
                                </span>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default">
                                <table class="table" id="list">
                                    <thead>
                                        <tr>
                                            <th>Request No.</th>
                                            <th>Date Requested</th>
                                            <th>Retail Store</th>
                                            <th>Requested By</th>
                                            <th>Date Needed</th>
                                            <th>Action</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($list as $l): ?> 
                                            <tr>
                                                <td><?php echo $l->sgc_num; ?></td>                                                
                                                <td><?php echo _dateFormat($l->sgc_date_request); ?></td>
                                                <td><?php echo $l->store_name; ?></td>
                                                <td><?php echo $l->reqby; ?></td>
                                                <td><?php echo _dateFormat($l->sgc_date_needed); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle defaction" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                            <i class="fa fa-cog" aria-hidden="true"></i>
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu " aria-labelledby="dropdownMenu1">
                                                            <li class="sused" data-id="<?php echo $l->sgc_id; ?>"><a href="#"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a></li>
                                                            <li class="strans" data-id="<?php echo $l->sgc_id; ?>"><a href="#"><i class="fa fa-file-text" aria-hidden="true"></i> View</a></li>
                                                            <li class="strans" data-id="<?php echo $l->sgc_id; ?>"><a href="#"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a></li>
                                                        </ul>
                                                    </div>
                                                    
                                                <td>
                                                    <span class="label label-<?php echo $l->sgc_serversave=='main' ? 'success' : 'danger' ?>"><?php echo $l->sgc_serversave ?></span>                                                    
                                                </td>
                                            </tr>
                                        <?php endforeach;?>                                        
                                    </tbody>
                                </table>

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

