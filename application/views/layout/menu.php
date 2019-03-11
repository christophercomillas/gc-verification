        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="<?php echo base_url().'assets/dist/img/user.png'?>" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p><?php echo $this->session->userdata('gc_user'); ?></p>
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu">
                    <li class="header">MAIN NAVIGATION</li>
                    <li <?php echo $title=='Dashboard' ? "class='active'" :''; ?>>
                        <a href="<?php echo base_url(); ?>home/dashboard"><i class="fa fa-fw fa-bar-chart-o"></i> <span>Dashboard</span></a>
                    </li>                    
                    <?php if($this->session->userdata('gc_uroles')=='0'): ?>
                        <li <?php echo $title=='GC Verification' ? "class='active'" :''; ?>>
                            <a href="<?php echo base_url(); ?>transaction/verification"><i class="fa fa-circle-o"></i> GC Verification</a>
                        </li>
                        <li <?php echo $title=='GC Reverification' ? "class='active'" :''; ?>>
                            <a href="<?php echo base_url(); ?>transaction/reverification"><i class="fa fa-circle-o"></i> GC Reverification</a>
                        </li>
                        </li>
                        <?php if($this->session->userdata('gc_bng')): ?>
                            <li <?php echo $title=='Beam and Go Conversion' ? "class='active'" :''; ?>>
                                <a href="<?php echo base_url(); ?>transaction/beamandgoconversion"><i class="fa fa-tasks"></i> Beam And Go Conversion</a>
                            </li>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>
                    <li <?php echo $title=='Verified GC' ? "class='active'" :''; ?>>
                        <a href="<?php echo base_url(); ?>transaction/verifiedgc"><i class="fa fa-list"></i> <span>Verified GC</span></a>
                    </li>
                    <?php if($this->session->userdata('gc_uroles')=='3'): ?>
                        <li <?php echo $title=='Textfile EOD' ? "class='active'" :''; ?>>
                            <a href="<?php echo base_url(); ?>transaction/textfileeod"><i class="fa fa-fw fa-random"></i> <span>Textfile EOD</span></a>
                        </li>
                    <?php endif; ?>
                    <?php if($this->session->userdata('gc_uroles')=='0' && $this->session->userdata('gc_bng')): ?>
                        <li class="treeview <?php echo $title=='reportsales' ? 'active' :''; ?>">
                            <a href="#">
                            <i class="fa fa-dashboard"></i> <span>Report</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo base_url(); ?>report/beamandgoreport"><i class="fa fa-circle-o"></i> Beam And Go</a></li>
                                
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>
