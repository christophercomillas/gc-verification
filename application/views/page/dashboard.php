<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php if(trim($txfilestatus)!=''): ?>
            <div class="row">
                <div class="col-lg-12">                                                    
                    <div class="callout callout-danger lead"><span class="callout-error"><?php echo $txfilestatus; ?></span></div>                                    
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $vercount; ?></h3>

                        <p>Verified GC</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>                    
                    <a href="<?php echo base_url(); ?>transaction/verifiedgc" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <?php if($this->session->userdata('gc_bng')): ?>
                <div class="col-lg-3 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3><?php echo number_format($bngcount); ?></h3>
                            <p>Beam and Go GC</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo base_url(); ?>transaction/beamandgoconversion" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>



    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
