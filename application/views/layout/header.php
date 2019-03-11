<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title; ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?php echo base_url().'assets/img/favicon.ico'?>" type="image/icon">
    <!-- Bootstrap 3.3.6 -->    
    <link rel="stylesheet" href="<?php echo base_url().'assets/bootstrap/css/bootstrap.min.css'?>">
    <link rel="stylesheet" href="<?php echo base_url().'assets/bootstrap-dialog/css/bootstrap-dialog.min.css'?>">
    <link rel="stylesheet" href="<?php echo base_url().'assets/css/jquery.dataTables.css'?>">   
    <link rel="stylesheet" href="<?php echo base_url().'assets/css/sweetalert.css'?>"> 
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url().'assets/font-awesome-4.7.0/css/font-awesome.min.css'?>">
    <!-- Ionicons -->    
    <link rel="stylesheet" href="<?php echo base_url().'assets/ionicons-2.0.1/css/ionicons.min.css'?>">
    <!-- daterange picker -->
    <link rel="stylesheet" href="<?php echo base_url().'assets/plugins/daterangepicker/daterangepicker.css'?>">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="<?php echo base_url().'assets/plugins/datepicker/datepicker3.css'?>">
    <!-- jvectormap -->
    <link rel="stylesheet" href="<?php echo base_url().'assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css'?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url().'assets/dist/css/AdminLTE.min.css'?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo base_url().'assets/dist/css/skins/_all-skins.min.css'?>">
    <link rel="stylesheet" href="<?php echo base_url().'assets/css/reset.css'?>">
    <style media="print" type="text/css">

        @media print{ 
            /*body div, body table {display: none;}  */
            .main-header, .content-wrapper, .main-footer { visibility: hidden; }
            #print-receipt-verify * { visibility: visible; }
            #print-receipt-verify 
            {   
/*                  display:block; 
                position: absolute; 
                top: 130px; 
                left: 0px; 
                font-size: 9px;*/
/*                    visibility: visible;*/
                display:block; 
                position: absolute; 
/*                    top:0px;*/
                top: 127px; 
/*                    left: 230px; */
                left: 5px;
                font-size: 10px;
            }
        }
    </style>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
        <input type="hidden" name="baseurl" value="<?php echo base_url(); ?>">
            <!-- Logo -->
            <a href="<?php echo base_url(); ?>" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>GC</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>GCMS</b></span>
            </a>

            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="pull-left" id="assigned">
                    <?php if($this->session->userdata('gc_usertype')==7): ?>
                        <?php echo "[".$this->session->userdata('gc_title').' - '.$this->session->userdata('gc_store').']'; ?>
                    <?php endif; ?>
                </div>
                <div class="navbar-custom-menu">

                    <ul class="nav navbar-nav">

                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?php echo base_url().'assets/dist/img/user.png'?>" class="user-image" alt="User Image">
                                <span class="hidden-xs"> <?php echo $this->session->userdata('gc_fullname'); ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="<?php echo base_url().'assets/dist/img/user.png'?>" class="img-circle" alt="User Image">
                                    <p>
                                        <?php echo $this->session->userdata('gc_fullname'); ?>
                                    </p>
                                </li>
                                <li class="user-change li-bot" onclick="changeUsername();">
                                    Change Username
                                </li>
                                <li class="user-change" onclick="changePassword();">
                                    Change Password
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href="#" data-toggle="control-sidebar" id="_logout"><i class="fa fa-sign-out"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>