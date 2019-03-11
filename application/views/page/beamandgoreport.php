<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Beam and Go to GC Accountability Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-5">
                <form method="POST" action="<?php echo base_url(); ?>Report/checkbngreport" id="_querybngdata">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Date</h3>
                        </div>
                        <div class="box-body">
                            <!-- Date range -->
                            <div class="form-group">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="datepicker" name="date" autocomplete="off">
                                </div>
                                    <!-- /.input group -->
                            </div>
                            <div class="response">                                
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="button" class="btn btn-info pull-right" id="btn-bngreport">Query</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
