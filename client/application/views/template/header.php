<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" href="<?= base_url(); ?>favicon.ico" />
        <link rel="icon" href="<?= base_url(); ?>favicon.ico" type="image/x-icon" />

        <title><?= (isset($ptitle)) ? $ptitle . " / " : ""; ?>E-Accounting &copy;</title>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>css/default/style.css"  />
        <link rel="stylesheet" type="text/css" media="screen" href="<?= base_url(); ?>js/jquery-ui-1.8.23/themes/cupertino/jquery.ui.all.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?= base_url(); ?>js/jqGrid-4.5.2/css/ui.jqgrid.css" />
        <!--<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url(); ?>js/jqGrid-5.0.2/css/ui.jqgrid.css" />-->
        <link rel="stylesheet" type="text/css" media="screen" href="<?= base_url(); ?>assets/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url(); ?>assets/dtree/dtree.css"/>
		
        <script type="text/javascript" src="<?= base_url(); ?>js/jquery-1.8.1.js" ></script>
        <script type="text/javascript" src="<?= base_url(); ?>js/jquery-ui-1.8.23/ui/jquery-ui.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>js/jqGrid-4.5.2/js/i18n/grid.locale-en.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>js/jqGrid-4.5.2/js/jquery.jqGrid.src.js"></script>
        <!--<script type="text/javascript" src="<?= base_url(); ?>js/jqGrid-5.0.2/js/i18n/grid.locale-en.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>js/jqGrid-5.0.2/js/jquery.jqGrid.min.js"></script>-->
        <script type="text/javascript" src="<?= base_url(); ?>js/jquery.price_format.1.7.js" ></script>
        <script type="text/javascript" src="<?= base_url(); ?>lib/site/common.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery.number.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/plugin.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>js/jquery-ui-1.8.23/ui/jquery.ui.dialog.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/bootstrap/js/bootstrap.js"></script> 
        <script type="text/javascript" src="<?= base_url(); ?>assets/dtree/dtree.js"></script>
        <script type="text/javascript">
            var root = '<?= base_url(); ?>';
            var mod = '<?php if (isset($mod)) echo $mod; else if (isset($current_tab)) echo $current_tab; ?>';
            var h = parseInt($('html').height());
            //var nstyle = '.body,.sidenav{height:'+(h-160)+'px}';
            //if ($.cookie('closeNav'))
            //    nstyle += '.bodytop, .body{margin-left:6px} .sidenav, .sidenavtop{display:none} .sidetog{display:block}';
            //document.write("<style>"+nstyle+"</style>");
        </script>
    </head>
    <body>
        <div class="my_container fh">
            <div class="header">
                <div class="topnav">
                    <div class="navbar navbar-static" id="navbar-example">
                        <div class="navbar-inner">
                            <a href="#" class="brand">E-Accounting : <span id="label_domain_program"></span></a>
							<div style="float: right;" class="container">
                                <a href="#" class="brand"><label><?= $this->session->userdata('ba_username'); ?></label></a>
                                <ul role="navigation" class="nav">
                                    <li class="dropdown">
                                        <a data-toggle="dropdown" class="dropdown-toggle" role="button" href="#"><i class="icon-cog"></i> Config <b class="caret"></b></a>
                                        <ul aria-labelledby="drop1" role="menu" class="dropdown-menu">
                                            <li role="presentation"><a href="#" tabindex="-1" role="menuitem" id="gantidomain">Ganti Domain</a></li>
                                            <li class="divider" role="presentation"></li>
                                            <li role="presentation"><a href="#" tabindex="-1" role="menuitem">Setup</a></li>
                                        </ul>
                                    </li>
                                </ul>
                                <ul role="navigation" class="nav">
                                    <li class="dropdown">
                                        <a class="dropdown-toggle" href="<?= base_url() . "mod_user/user_logout"; ?>"><i class="icon-off"></i> Log Out</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidenavtop">
                    <div class="sidenav_title">Navigation</div>
                    <div class="sidenav_sub toolbar">
                        <a href="#" id="expandb" class="nleft" title="Expand All"><span><b class="expand"></b></span></a>
                        <a href="#" id="collapseb" class="nright" title="Collapse All"><span><b class="contract"></b></span></a>
                    </div>
                    <div class="closenav"></div>
                </div>

                <div class="bodytop">
                    <div class="body_tabs">
                        <div class="tab_slide">
                            <div class="tab_pane">
                                <?php $this->load->view('elements/tabs'); ?>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="subtabs"><b></b></a>
                    <a href="#" class="goleft"><b></b></a>
                    <a href="#" class="goright"><b></b></a>

                    <div class="tabdroplist">
                        <?php $this->load->view('elements/tabdroplist'); ?>
                    </div> 

                    <div class="body_sub">
                        <?= (!empty($toolbar)) ? $toolbar : ''; ?>
                        <?= (!empty($toolbars)) ? $toolbars : ''; ?>
                    </div>
                </div>
            </div>

            <div class="sidenav">
                <div class="subside">
                    <?php $this->load->view('elements/sidenav'); ?>
                </div>
            </div>

            <div class="body">				
                <div class="inbody">
                    <div class="ajax"></div>
                    <!--insert content here-->
