
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        <title><?= $page_title ?> - HRIS</title>
        <link rel="shortcut icon" href="<?= base_url("assets/img/favicon-child.ico") ?>" />

        <meta name="description" content="<?= $page_title ?> - HRIS" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

        <!-- bootstrap & fontawesome -->
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css" />

        <link rel="stylesheet" href="<?= base_url() ?>assets/font-awesome/4.4.0/css/font-awesome.min.css" />

        <!-- page specific plugin styles -->

        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/datepicker.css" />
        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap-timepicker.css" />
        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/daterangepicker.css" />

        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap-datetimepicker.min.css" />
        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/chosen.css" />

        <!-- text fonts -->
        <link rel="stylesheet" href="<?= base_url() ?>assets/fonts/fonts.googleapis.com.css" />

        <!-- ace styles -->
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/ace.min.css" id="main-ace-style" />

        <!--[if lte IE 9]>
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/ace-part2.min.css" />
        <![endif]-->
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/ace-skins.min.css" />
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/ace-rtl.min.css" />

        <!--[if lte IE 9]>
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/ace-ie.min.css" />
        <![endif]-->

<!-- <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/jquery.handsontable.full.min.css" />
<link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/jquery.handsontable.bootstrap.css" /> -->
        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/jquery-ui.min.css" />
        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/ui-bootstrap/jquery-ui.custom.css" />
        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/handsontable.full.min.css" />
        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/handsontable.bootstrap.css" />

        <link type="text/css" rel="stylesheet" href="<?= base_url() ?>assets/css/jquery.jgrowl.min.css" />

        <link rel="stylesheet" href="<?= base_url() ?>assets/css/common.css?v=<?= $rev ?>" />
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/jquery.gritter.css?v=<?= $rev ?>" />

        <!-- inline styles related to this page -->

        <!-- ace settings handler -->
        <script src="<?= base_url() ?>assets/js/ace-extra.min.js"></script>

        <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

        <!--[if lte IE 8]>
        <script src="<?= base_url() ?>assets/js/html5shiv.min.js"></script>
        <script src="<?= base_url() ?>assets/js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="no-skin">
        <div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar">
            <script type="text/javascript">
                try {
                    ace.settings.check('navbar', 'fixed')
                } catch (e) {
                }
            </script>

            <div class="navbar-container" id="navbar-container">
                <div class="navbar-header pull-left">
                    <a href="<?= base_url() ?>" class="navbar-brand">
                        <small>
                            H
                            R
                            <i class="fa fa-child"></i>
                            S
                        </small>
                    </a>

                    <button class="pull-right navbar-toggle navbar-toggle-img collapsed" type="button" data-toggle="collapse" data-target=".navbar-buttons,.navbar-menu">
                        <span class="sr-only">Toggle user menu</span>

                        <img class="user-photo nav-user-photo" src="<?= base_url() ?>assets/uploads/avatars/<?= $this->session->userdata('mb_no') ?>.jpg" alt="<?=sprintf(lang('header_avatar_alt'), $this->session->userdata('mb_nick'))?>" />
                    </button>

                    <button class="pull-right navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".sidebar">
                        <span class="sr-only">Toggle sidebar</span>

                        <span class="icon-bar"></span>

                        <span class="icon-bar"></span>

                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="navbar-buttons navbar-header pull-right  collapse navbar-collapse" role="navigation">
                    <ul class="nav ace-nav">

                        <li class="transparent" id="sms-panel">
                            <a data-toggle="dropdown" class="dropdown-toggle"href="#">
                                <i class="ace-icon fa fa-mobile"></i>
                                <span class="badge badge-important" id="sms_cnt"></span>
                            </a>
                            <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                                <li class="dropdown-footer">
                                    <a href="<?= base_url("sms/messages") ?>">
                                        <i class="ace-icon fa fa-info-circle"></i>Total SMS received today<i class="ace-icon fa fa-arrow-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <?php if (count($subordinates)): ?>
                            <li class="transparent tooltip-error" id="subordinates-panel" title="You have <?=$subords_pending?> subordinate<?=($subords_pending > 1 ? 's' : '')?> with incomplete scores">
                                <a href="#">
                                    <i class="ace-icon fa fa-sitemap<?= ($subords_pending ? ' icon-animated-vertical' : '') ?>"></i>
                                    <span class="badge badge-important"><?= ($subords_pending ? $subords_pending : '') ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="transparent" id="notif-panel">
                                <!-- <a href="#" id="top-pending-alert"<?= ($pending_count ? ' class="tooltip-error"' : '') ?>>
                                        <i class="ace-icon fa fa-bell<?= ($pending_count ? ' icon-animated-bell' : '-slash') ?>"></i>
                                        <span class="badge badge-important<?= ($pending_count ? '' : ' hidden') ?>"><?= $pending_count ?></span>
                                </a> -->
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <i class="ace-icon fa fa-bell <?php //icon-animated-bell   ?>"></i>
                                <span class="badge badge-important"></span>
                            </a>

                            <ul class="dropdown-menu-right dropdown-navbar <?php //navbar-pink   ?> dropdown-menu dropdown-caret dropdown-close">
                                <li class="dropdown-header">
                                    <i class="ace-icon fa <?php //fa-exclamation-triangle   ?> fa-check"></i>
                                    0 unread
                                </li>

                                <!-- <li>
                                        <a href="#">
                                                <div class="clearfix">
                                                                                                        <span class="pull-left">
                                                                                                                <i class="btn btn-xs no-hover btn-pink fa fa-comment"></i>
                                                                                                                New Comments
                                                                                                        </span>
                                                        <span class="pull-right badge badge-info">+12</span>
                                                </div>
                                        </a>
                                </li>
                
                                <li>
                                        <a href="#">
                                                <i class="btn btn-xs btn-primary fa fa-user"></i>
                                                Bob just signed up as an editor ...
                                        </a>
                                </li>
                
                                <li>
                                        <a href="#">
                                                <div class="clearfix">
                                                                                                        <span class="pull-left">
                                                                                                                <i class="btn btn-xs no-hover btn-success fa fa-shopping-cart"></i>
                                                                                                                New Orders
                                                                                                        </span>
                                                        <span class="pull-right badge badge-success">+8</span>
                                                </div>
                                        </a>
                                </li>
                
                                <li>
                                        <a href="#">
                                                <div class="clearfix">
                                                                                                        <span class="pull-left">
                                                                                                                <i class="btn btn-xs no-hover btn-info fa fa-twitter"></i>
                                                                                                                Followers
                                                                                                        </span>
                                                        <span class="pull-right badge badge-info">+11</span>
                                                </div>
                                        </a>
                                </li> -->

                                <li class="dropdown-footer">
                                    <a href="#" data-toggle="modal" data-target="#notifs-modal" data-backdrop="static">
                                        See all notifications
                                        <i class="ace-icon fa fa-arrow-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php //endif; ?>

                        <li class="light-blue user-min">
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                                <img class="user-photo nav-user-photo" src="<?= base_url() ?>assets/uploads/avatars/<?= $this->session->userdata('mb_no') ?>.jpg" alt="<?=sprintf(lang('header_avatar_alt'), $this->session->userdata('mb_nick'))?>" />
                                <span class="user-info">
                                    <small><?=lang('header_welcome_menu')?>,</small>
                                    <?= $this->session->userdata('mb_nick') ?>
                                </span>

                                <i class="ace-icon fa fa-caret-down"></i>
                            </a>

                            <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                                <!-- <li>
                                        <a href="#">
                                                <i class="ace-icon fa fa-cog"></i>
                                                Settings
                                        </a>
                                </li> -->

                                <li>
                                    <a href="<?= base_url() ?>employees/profile">
                                        <i class="ace-icon fa fa-user"></i>
                                        <?=lang('header_profile_menu')?>
                                    </a>
                                </li>

                                <li class="divider"></li>

                                <li>
                                    <a href="<?= base_url() ?>auth/logout">
                                        <i class="ace-icon fa fa-power-off"></i>
                                        <?=lang('header_logout_menu')?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <nav role="navigation" class="navbar-menu pull-left collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="http://10.120.10.125/intranet/dashboard" target="_blank">
                                Intranet
                                &nbsp;
                                <i class="ace-icon fa fa-angle-double-right bigger-110"></i>
                            </a>
                        </li>

                        <!-- <li>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="ace-icon fa fa-clock-o"></i>
                                        Recent
                                </a>
        
                                <ul class="dropdown-menu dropdown-navbar dropdown-menu">
                                        <li>
                                                <a href="#">
                                                        <i class="ace-icon fa fa-eye bigger-110 blue"></i>
                                                        Monthly Visitors
                                                </a>
                                        </li>
        
                                        <li>
                                                <a href="#">
                                                        <i class="ace-icon fa fa-user bigger-110 blue"></i>
                                                        Active Users
                                                </a>
                                        </li>
        
                                        <li>
                                                <a href="#">
                                                        <i class="ace-icon fa fa-cog bigger-110 blue"></i>
                                                        Settings
                                                </a>
                                        </li>
                                        <li class="dropdown-footer">
                                                <a href="inbox.html">
                                                        See all recent activities
                                                        <i class="ace-icon fa fa-arrow-right"></i>
                                                </a>
                                        </li>
                                </ul>
                        </li> -->
                    </ul>

                    <?php if ($this->session->userdata('mb_deptno') == 24): ?>
                        <form class="navbar-form navbar-left form-search" role="search" id="top-search-form">
                            <div class="form-group">
                                <input type="text" placeholder="<?=lang('header_search_placeholder')?>" id="top-search" />
                            </div>

                            <button type="button" class="btn btn-xs btn-info2" id="top-search-btn">
                                <i class="ace-icon fa fa-search icon-only bigger-110"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </nav>
            </div><!-- /.navbar-container -->
        </div>

        <div class="main-container" id="main-container">
            <script type="text/javascript">
                try {
                    ace.settings.check('main-container', 'fixed')
                } catch (e) {
                }
            </script>

            <div id="sidebar" class="sidebar      h-sidebar                navbar-collapse collapse">
                <script type="text/javascript">
                    //try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
                    ace.settings.sidebar_fixed(true);
                </script>

                <div class="sidebar-shortcuts" id="sidebar-shortcuts">
                    <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                        <button class="btn btn-success">
                            <i class="ace-icon fa fa-dashboard"></i>
                        </button>

                        <?php if ($this->session->userdata('mb_deptno') == 24): ?>
                            <button class="btn btn-info">
                                <i class="ace-icon fa fa-users"></i>
                            </button>

                            <button class="btn btn-warning">
                                <i class="ace-icon fa fa-ban"></i>
                            </button>

                            <button class="btn btn-danger">
                                <i class="ace-icon fa fa-file"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                        <span class="btn btn-success"></span>

                        <span class="btn btn-info<?= ($this->session->userdata('mb_deptno') == 24 ? '' : ' disabled') ?>"></span>

                        <span class="btn btn-warning<?= ($this->session->userdata('mb_deptno') == 24 ? '' : ' disabled') ?>"></span>

                        <span class="btn btn-danger<?= ($this->session->userdata('mb_deptno') == 24 ? '' : ' disabled') ?>"></span>
                    </div>
                </div><!-- /.sidebar-shortcuts -->

                <ul class="nav nav-list" id="main-nav">
                    <li class="<?= ($this->uri->segment(1, '') == '' ? 'active open ' : '') ?>hover">
                        <a href="<?= base_url() ?>">
                            <i class="menu-icon fa fa-tachometer"></i>
                            <span class="menu-text"> <?=lang('top_dashboard_menu')?> </span>
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <?php if ($this->session->userdata('mb_deptno') == 24): ?>
                        <li class="<?= ($this->uri->segment(1, '') == 'employees' ? 'active open ' : '') ?>hover">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-users"></i>
                                <span class="menu-text"> <?=lang('top_employees_menu')?> </span>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">
                                <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == '' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url() ?>employees">
                                        <i class="menu-icon fa fa-caret-right"></i>

                                        <?=lang('top_view_all_menu')?>
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu">

                                        <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>employees/options">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_settings_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>

        <!-- <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == 'expat' ? 'active open ' : '') ?>hover">
                <a href="<?= base_url() ?>employees/expat">
                        <i class="menu-icon fa fa-caret-right"></i>
                        
                        Expat records
                </a>
                
                <b class="arrow"></b>
        </li> -->

                                <li class="<?= ($this->uri->segment(1, '') == 'employees' && in_array($this->uri->segment(2, ''), array('tree', 'settings')) ? 'active open ' : '') ?>hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>

                                        <?=lang('top_hierarchy_menu')?>
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu">
                                        <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == 'tree' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>employees/tree">
                                                <i class="menu-icon fa fa-sitemap"></i>
                                                <?=lang('top_tree_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>

                                        <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>employees/settings">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_settings_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>

                                <li class="<?= ($this->uri->segment(1, '') == 'employees' && in_array($this->uri->segment(2, ''), array('HRMIS')) ? 'active open ' : '') ?>hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>

                                        KPI
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu">
                                        <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == 'tree' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>employees/kpi_hrmis">
                                                <i class="menu-icon fa fa-street-view"></i>
                                                HRMIS
                                            </a>

                                            <b class="arrow"></b>
                                        </li>
                                        <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == 'tree' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>employees/kpi_department">
                                                <i class="menu-icon fa fa-users"></i>
                                                <?=lang('top_dept_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>
                                        <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == 'tree' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>employees/kpi_hr_records">
                                                <i class="menu-icon fa fa-table"></i>
                                                HR <?=lang('top_records_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>
                                        <li class="<?= ($this->uri->segment(1, '') == 'employees' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>employees/kpi_settings">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_settings_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <li class="<?= ($this->uri->segment(1, '') == 'violations' ? 'active open ' : '') ?>hover">
                            <a href="#" class="dropdown-toggle">
                                <i class="menu-icon fa fa-ban"></i>
                                <span class="menu-text"> <?=lang('top_violations_menu')?> </span>

                                <b class="arrow fa fa-angle-down"></b>
                            </a>

                            <b class="arrow"></b>

                            <ul class="submenu">

                                <?php if ($this->session->userdata('mb_deptno') == 24): ?>
                                    <li class="hover">
                                        <a href="#violations-quick-add" data-toggle="modal" data-backdrop="static" role="button">
                                            <i class="menu-icon fa fa-caret-right"></i>

                                            <?=lang('top_quick_add_menu')?>
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <!-- <li class="hover">
                                            <a href="#violations-batch-entry" data-toggle="modal" data-backdrop="static" role="button">
                                                    <i class="menu-icon fa fa-caret-right"></i>
                                                    
                                                    Batch entry
                                            </a>
                    
                                            <b class="arrow"></b>
                                    </li> -->
                                <?php endif; ?>

                                <li class="<?= ($this->uri->segment(1, '') == 'violations' ? 'active open ' : '') ?>hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>

                                        <?=lang('top_manage_menu')?>
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu">
                                        <li class="<?= ($this->uri->segment(1, '') == 'violations' && $this->uri->segment(2) === false ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>violations">
                                                <i class="menu-icon fa fa-pencil-square-o"></i>
                                                <?=lang('top_records_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>

                                        <li class="<?= ($this->uri->segment(1, '') == 'violations' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>violations/settings">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_settings_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="<?= ($this->uri->segment(1, '') == 'cite' ? 'active open ' : '') ?>hover">

                        <a href="#" class="dropdown-toggle" id="cite-anchor">
                            <i class="menu-icon fa fa-file"></i>

                            <span class="menu-text"> <?=lang('top_cite_forms_menu')?><?= ($pending_count && $this->session->userdata('mb_deptno') == 24 ? '<span id="pending-cites" class="badge badge-danger tooltip-error" title="' . sprintf(lang('top_pending_cites_badge'), $pending_count, ($pending_count > 1 ? 's' : '')) . '" style="z-index: 1027;">' . $pending_count . '</span>' : '') ?> </span>

                            <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">


                            <li class="<?= ($this->uri->segment(1, '') == 'cite' && is_numeric($this->uri->segment(2, '')) ? 'active open ' : '') ?>hover">
                                <a href="<?= base_url('cite/' . $this->session->userdata('mb_no')) ?>" data-toggle="modal" data-backdrop="static" role="button">
                                    <i class="menu-icon fa fa-caret-right"></i>


                                    <?=lang('top_my_cites_menu')?>
                                </a>

                                <b class="arrow"></b>

                            </li>

                            <?php if ($this->session->userdata('mb_deptno') == 24): ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'cite' && in_array($this->uri->segment(2), array(false, 'settings')) ? 'active open ' : '') ?>hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>

                                        <?=lang('top_manage_menu')?>
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>

                                    <b class="arrow"></b>

                                    <ul class="submenu">
                                        <li class="<?= ($this->uri->segment(1, '') == 'cite' && $this->uri->segment(2) === false ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>cite">
                                                <i class="menu-icon fa fa-pencil-square-o"></i>
                                                <?=lang('top_records_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>

                                        <li class="<?= ($this->uri->segment(1, '') == 'cite' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url() ?>cite/settings">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_settings_menu')?>
                                            </a>

                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>


                    <li class="<?= ($this->uri->segment(1, '') == 'attendance' ? 'active open ' : '') ?>hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-clock-o"></i>
                            <span class="menu-text"> Attendance </span>
                            <span id="att_cnt" class="badge badge-danger" style="z-index: 1;"></span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <? if ($this->session->userdata("mb_no") == 114) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'attendance' && $this->uri->segment(2, '') == 'logs' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url("attendance/attendance_approval") ?>" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_attendance_approval_menu')?>
                                    </a>
                                </li>
                            <? } ?>

                            <li class="<?= ($this->uri->segment(1, '') == 'attendance' && $this->uri->segment(2, '') == 'attendance' ? 'active open ' : '') ?>hover">
                                <a href="<?= base_url("attendance") ?>" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?=lang('top_attendance_menu')?>
                                </a>
                            </li>


                            <li class="<?= ($this->uri->segment(1, '') == 'attendance' && $this->uri->segment(2, '') == 'logs' ? 'active open ' : '') ?>hover">
                                <a href="<?= base_url("attendance/logs") ?>" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?=lang('top_login_logout_menu')?>
                                </a>
                            </li>

                            <li class="<?= ($this->uri->segment(1, '') == 'breaktime' ? 'active open ' : '') ?>hover">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Break Mode
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>

                                <b class="arrow"></b>
                                <ul class="submenu">

                                    <? if (allowed_access_report(3)) { ?>
                                        <li class="<?= ($this->uri->segment(1, '') == 'breaktime' && $this->uri->segment(2, '') == '' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("breaktime") ?>" class="dropdown-toggle">
                                                <i class="menu-icon fa fa-caret-right"></i>
                                                <?=lang('top_breaktime_menu')?>
                                            </a>
                                        </li>
                                    <? } ?>
                                        
                                    <li class="<?= ($this->uri->segment(1, '') == 'breaktime' && $this->uri->segment(2, '') == 'breaklist' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("breaktime/breaklist") ?>" class="dropdown-toggle">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?=lang('top_break_list_menu')?>
                                        </a>
                                    </li>
                                    
                                    <li class="<?= ($this->uri->segment(1, '') == 'breaktime' && $this->uri->segment(2, '') == 'logs' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("breaktime/logs") ?>" class="dropdown-toggle">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?=lang('top_breaktime_logs_menu')?>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                    <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' ? 'active open ' : '') ?>hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-calendar"></i>
                            <span id="sched_cnt" class="badge badge-danger" style="z-index: 1; left: 0;"></span>
                            <span class="menu-text"> <?=lang('top_timekeeping_menu')?> </span>
                            <span id="cws_cnt" class="badge badge-success" style="z-index: 1;"></span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <? if ($this->session->userdata("tk_uploader")) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && $this->uri->segment(2, '') == 'upload_sched' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url("timekeeping/upload_sched") ?>" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_upload_sched_menu')?>
                                    </a>
                                </li>
                            <? } ?>
                            <? if ($this->session->userdata("tk_approver") || $this->session->userdata("cws_approver")) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && $this->uri->segment(2, '') == 'manage_approval' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url("timekeeping/manage_approval") ?>" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_scheds_for_approval_menu')?>
                                    </a>
                                </li>
                            <? } ?>
                            <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && $this->uri->segment(2, '') == 'view_schedules' ? 'active open ' : '') ?>hover">
                                <a href="<?= base_url("timekeeping/view_schedules") ?>" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?=lang('top_view_scheds_menu')?>
                                </a>
                            </li>
                            <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && $this->uri->segment(2, '') == 'change_schedule' ? 'active open ' : '') ?>hover">
                                <a href="<?= base_url("timekeeping/change_schedule") ?>" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?=lang('top_change_sched_menu')?>
                                </a>
                            </li>
                            <? if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(229))) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && in_array($this->uri->segment(2, ''), array('settings', 'approval_settings')) ? 'active open ' : '') ?>hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_manage_menu')?>
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("timekeeping/settings") ?>">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_general_settings_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                        <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("timekeeping/holidays") ?>">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_holiday_settings_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                        <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && $this->uri->segment(2, '') == 'approval_settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("timekeeping/approval_settings") ?>">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_approval_settings_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                        <li class="<?= ($this->uri->segment(1, '') == 'timekeeping' && $this->uri->segment(2, '') == 'special_upload' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("timekeeping/special_upload") ?>">
                                                <i class="menu-icon fa fa-upload"></i>
                                                <?=lang('top_special_upload_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>
                            <? } ?>
                        </ul>
                    </li>
                    <li class="<?= ($this->uri->segment(1, '') == 'leave' ? 'active open ' : '') ?>hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-suitcase"></i>
                            <span class="menu-text"> <?=lang('top_leave_menu')?> </span>
                            <span id="lv_cnt" class="badge badge-danger" style="z-index: 1;"></span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <? if ($this->session->userdata("lv_approver")) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'leave' && $this->uri->segment(2, '') == 'manage_approval' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url("leave/manage_approval") ?>" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_leaves_for_approval_menu')?>
                                    </a>
                                </li>
                            <? } ?>
                            <? if (in_array($this->session->userdata("mb_deptno"), array(24))) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'leave' && $this->uri->segment(2, '') == 'mc' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url("leave/mc") ?>" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_medical_certificates_menu')?>
                                    </a>
                                </li>
                            <? } ?>
                            <li class="<?= ($this->uri->segment(1, '') == 'leave' && $this->uri->segment(2, '') == 'submit_leave' ? 'active open ' : '') ?>hover">
                                <a href="<?= base_url("leave/submit_leave") ?>" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?=lang('top_submit_leave_menu')?>
                                </a>
                            </li>
                            <? if (in_array($this->session->userdata("mb_deptno"), array(24)) || management_access()) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'leave' && in_array($this->uri->segment(2, ''), array('leave_balances', 'settings')) ? 'active open ' : '') ?>hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_manage_menu')?>
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li class="<?= ($this->uri->segment(1, '') == 'leave' && $this->uri->segment(2, '') == 'leave_balances' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("leave/balances") ?>">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_balances_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                        <? if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(229))) { ?>
                                            <li class="<?= ($this->uri->segment(1, '') == 'leave' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                                <a href="<?= base_url("leave/settings") ?>">
                                                    <i class="menu-icon fa fa-gear"></i>
                                                    <?=lang('top_general_settings_menu')?>
                                                </a>
                                                <b class="arrow"></b>
                                            </li>
                                            <li class="<?= ($this->uri->segment(1, '') == 'leave' && $this->uri->segment(2, '') == 'approval_settings' ? 'active open ' : '') ?>hover">
                                                <a href="<?= base_url("leave/approval_settings") ?>">
                                                    <i class="menu-icon fa fa-gear"></i>
                                                    <?=lang('top_approval_settings_menu')?>
                                                </a>
                                                <b class="arrow"></b>
                                            </li>
                                        <? } ?>
                                    </ul>
                                </li>
                            <? } ?>
                        </ul>
                    </li>
                    <li class="<?= ($this->uri->segment(1, '') == 'overtime' ? 'active open ' : '') ?>hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-laptop"></i>
                            <span class="menu-text"> <?=lang('top_overtime_menu')?> </span>
                            <span id="ot_cnt" class="badge badge-danger" style="z-index: 1;"></span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <? if ($this->session->userdata("ot_approver")) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'overtime' && $this->uri->segment(2, '') == 'manage_approval' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url("overtime/manage_approval") ?>" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_overtime_for_approval_menu')?>
                                    </a>
                                </li>
                            <? } ?>
                            <li class="<?= ($this->uri->segment(1, '') == 'overtime' && $this->uri->segment(2, '') == 'submit_leave' ? 'active open ' : '') ?>hover">
                                <a href="<?= base_url("overtime/submit_overtime") ?>" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?=lang('top_submit_overtime_menu')?>
                                </a>
                            </li>
                            <? if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(229))) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'overtime' && in_array($this->uri->segment(2, ''), array('leave_balances', 'settings')) ? 'active open ' : '') ?>hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_manage_menu')?>
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li class="<?= ($this->uri->segment(1, '') == 'overtime' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("overtime/settings") ?>">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_general_settings_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                        <li class="<?= ($this->uri->segment(1, '') == 'overtime' && $this->uri->segment(2, '') == 'approval_settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("overtime/approval_settings") ?>">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_approval_settings_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>

                                    </ul>
                                </li>
                            <? } ?>
                        </ul>
                    </li>
                    <li class="<?= ($this->uri->segment(1, '') == 'obt' ? 'active open ' : '') ?>hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-send"></i>
                            <span class="menu-text"> OBT </span>
                            <span id="obt_cnt" class="badge badge-danger" style="z-index: 1;"></span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <? if ($this->session->userdata("obt_approver")) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'obt' && $this->uri->segment(2, '') == 'manage_approval' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url("obt/manage_approval") ?>" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_obt_for_approval_menu')?>
                                    </a>
                                </li>
                            <? } ?>
                            <li class="<?= ($this->uri->segment(1, '') == 'obt' && $this->uri->segment(2, '') == 'submit_obt' ? 'active open ' : '') ?>hover">
                                <a href="<?= base_url("obt/submit_obt") ?>" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?=lang('top_submit_obt_menu')?>
                                </a>
                            </li>
                            <? if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(229))) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'obt' && in_array($this->uri->segment(2, ''), array('approval_settings')) ? 'active open ' : '') ?>hover">
                                    <a href="#" class="dropdown-toggle">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        Manage
                                        <b class="arrow fa fa-angle-down"></b>
                                    </a>
                                    <b class="arrow"></b>
                                    <ul class="submenu">
                                        <li class="<?= ($this->uri->segment(1, '') == 'obt' && $this->uri->segment(2, '') == 'approval_settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("obt/approval_settings") ?>">
                                                <i class="menu-icon fa fa-gear"></i>
                                                <?=lang('top_approval_settings_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                    </ul>
                                </li>
                            <? } ?>
                        </ul>
                    </li>

                    <?php if ($this->session->userdata('mb_deptno') == 24 || management_access() || (count($this->session->userdata("reports")) > 0)) { ?>
                        <li class="<?= ($this->uri->segment(1, '') == 'reports' ? 'active open ' : '') ?>hover">

                            <a href="#">
                                <i class="menu-icon fa fa-files-o"></i>
                                <span class="menu-text"> <?=lang('top_reports_menu')?> </span>
                            </a>

                            <b class="arrow"></b>
                            <ul class="submenu">

                                <? if ($this->session->userdata('mb_deptno') == 24 || management_access() || allowed_access_report(1)) { ?>
                                    <li class="<?= ($this->uri->segment(1, '') == 'reports' && $this->uri->segment(2, '') == 'attendance_list' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("reports/attendance_list") ?>" class="dropdown-toggle">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?=lang('top_attendance_report_menu')?>
                                        </a>
                                    </li> 
                                <?php } ?>  

                                <? if ($this->session->userdata('mb_deptno') == 24 || management_access()) { ?>
                                    <li class="<?= ($this->uri->segment(1, '') == 'reports' && $this->uri->segment(2, '') == 'leave_list' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("reports/leave_list") ?>" class="dropdown-toggle">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?=lang('top_leave_filings_menu')?>
                                        </a>
                                    </li>
                                <?php } ?>


                                <? if ($this->session->userdata('mb_deptno') == 24) { ?>
                                    <li class="<?= ($this->uri->segment(1, '') == 'reports' && $this->uri->segment(2, '') == 'overtime_list' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("reports/overtime_list") ?>" class="dropdown-toggle">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?=lang('top_ot_filings_menu')?>
                                        </a>
                                    </li>
                                <? } ?> 

                                <? if ($this->session->userdata('mb_deptno') == 24) { ?>
                                    <li class="<?= ($this->uri->segment(1, '') == 'reports' && $this->uri->segment(2, '') == 'obt_list' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("reports/obt_list") ?>" class="dropdown-toggle">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?=lang('top_obt_filings_menu')?>
                                        </a>
                                    </li>
                                <? } ?> 

                                <? if ($this->session->userdata('mb_deptno') == 24) { ?>
                                    <li class="<?= ($this->uri->segment(1, '') == 'reports' && $this->uri->segment(2, '') == 'cws_list' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("reports/cws_list") ?>" class="dropdown-toggle">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?=lang('top_cws_filings_menu')?>
                                        </a>
                                    </li>
                                <? } ?> 

                                <? if ($this->session->userdata('mb_deptno') == 24) { ?>
                                    <li class="<?= ($this->uri->segment(1, '') == 'reports' && $this->uri->segment(2, '') == 'schedule_list' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("reports/schedule_list") ?>" class="dropdown-toggle">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            <?=lang('top_sched_uploads_menu')?>
                                        </a>
                                    </li>
                                <? } ?> 

                            </ul>
                        </li>
                    <?php } ?>
                    <!-- <li class="hover">
                            <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-calculator"></i>
                                    <span class="menu-text"> Rules </span>
                    
                                    <b class="arrow fa fa-angle-down"></b>
                            </a>
                    
                            <b class="arrow"></b>
                    
                            <ul class="submenu">
                                    <li class="hover">
                                            <a href="tables.html">
                                                    <i class="menu-icon fa fa-caret-right"></i>
                                                    Simple &amp; Dynamic
                                            </a>
                    
                                            <b class="arrow"></b>
                                    </li>
                    
                                    <li class="hover">
                                            <a href="jqgrid.html">
                                                    <i class="menu-icon fa fa-caret-right"></i>
                                                    jqGrid plugin
                                            </a>
                    
                                            <b class="arrow"></b>
                                    </li>
                            </ul>
                    </li> -->

                    <li class="<?= ($this->uri->segment(1, '') == 'guide' ? 'active open ' : '') ?>hover">
                        <a href="<?= base_url("guide") ?>" target="_blank">
                            <i class="menu-icon fa fa-book"></i>
                            <span class="menu-text"><?=lang('top_user_guide_menu')?></span>
                        </a>

                        <b class="arrow"></b>
                    </li>


                    <?php //if((in_array($this->session->userdata('mb_deptno'), array(24, 29))) || ($this->session->userdata('mb_deptno') == 23 && group_access() )) { ?>
                    <li class="<?= (in_array($this->uri->segment(1, ''), array('condo', 'groups', 'sms')) ? 'active open ' : '') ?>hover">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-stack-exchange"></i>
                            <span class="menu-text"> <?=lang('top_others_menu')?> </span>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                            <?php if ((in_array($this->session->userdata('mb_deptno'), array(24, 29)))) { ?>
                                <li class="<?= ($this->uri->segment(1, '') == 'condo' ? 'active open ' : '') ?>hover">
                                    <a href="<?= base_url("condo") ?>">
                                        <i class="menu-icon fa fa-caret-right"></i>
                                        <?=lang('top_condominium_menu')?>
                                    </a>
                                    <b class="arrow"></b>
                                </li>
                            <?php } ?>
                            <?php //if($this->session->userdata('mb_deptno') == 24 || ($this->session->userdata('mb_deptno') == 23 && group_access() )) { ?>
                            <li class="<?= ($this->uri->segment(1, '') == 'groups' ? 'active open ' : '') ?>hover">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    <?=lang('top_group_menu')?>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>

                                <b class="arrow"></b>

                                <ul class="submenu">
                                    <?php if (allowed_group_access()) {//if($this->session->userdata('mb_deptno') == 23 && $this->session->userdata("tk_uploader") ) { ?>
                                        <li class="<?= ($this->uri->segment(1, '') == 'groups' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("groups/settings") ?>">

                                                <?=lang('top_group_settings_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                    <?php } ?>
                                    <?php if (allowed_group_access()) {//if($this->session->userdata('mb_deptno') == 23 && $this->session->userdata("tk_uploader") ) { ?>
                                        <li class="<?= ($this->uri->segment(1, '') == 'groups' && $this->uri->segment(2, '') == 'members' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("groups/members") ?>">

                                                <?=lang('top_group_members_menu')?>
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                    <?php } ?>
                                    <li class="<?= ($this->uri->segment(1, '') == 'groups' && $this->uri->segment(2, '') == 'schedule' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("groups/schedule") ?>">

                                            <?=lang('top_group_sched_menu')?>
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>
                            <?php //} ?>
                            <li class="<?= ($this->uri->segment(1, '') == 'sms' ? 'active open ' : '') ?>hover">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    SMS
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <b class="arrow"></b>
                                <ul class="submenu">
                                    <li class="<?= ($this->uri->segment(1, '') == 'sms' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                        <a href="<?= base_url("sms/messages") ?>">
                                            <?=lang('top_sms_messages_menu')?>
                                        </a>
                                        <b class="arrow"></b>
                                    </li>
                                    <!--
                                    <li class="<?= ($this->uri->segment(1, '') == 'sms' && $this->uri->segment(2, '') == 'settings' ? 'active open ' : '') ?>hover">
                                            <a href="<?= base_url("sms/settings") ?>">
                                                    SMS Settings
                                            </a>
                                            <b class="arrow"></b>
                                    </li>
                                    -->
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <?php //} ?>
                </ul><!-- /.nav-list -->

                <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
                    <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
                </div>

                <script type="text/javascript">
                    try {
                        ace.settings.check('sidebar', 'collapsed')
                    } catch (e) {
                    }
                </script>
            </div>

            <div class="main-content">
                <div class="page-content">
                    <div class="ace-settings-container hidden" id="ace-settings-container">
                        <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                            <i class="ace-icon fa fa-cog bigger-150"></i>
                        </div>

                        <div class="ace-settings-box clearfix" id="ace-settings-box">
                            <div class="pull-left width-50">
                                <div class="ace-settings-item">
                                    <div class="pull-left">
                                        <select id="skin-colorpicker" class="hide">
                                            <option data-skin="no-skin" value="#438EB9">#438EB9</option>
                                            <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                                            <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                                            <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                                        </select>
                                    </div>
                                    <span>&nbsp; Choose Skin</span>
                                </div>

                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" />
                                    <label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
                                </div>

                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
                                    <label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
                                </div>

                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />
                                    <label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
                                </div>

                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />
                                    <label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
                                </div>

                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />
                                    <label class="lbl" for="ace-settings-add-container">
                                        Inside
                                        <b>.container</b>
                                    </label>
                                </div>
                            </div><!-- /.pull-left -->

                            <div class="pull-left width-50">
                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" />
                                    <label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
                                </div>

                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" />
                                    <label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
                                </div>

                                <div class="ace-settings-item">
                                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" />
                                    <label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
                                </div>
                            </div><!-- /.pull-left -->
                        </div><!-- /.ace-settings-box -->
                    </div><!-- /.ace-settings-container -->

                    <div class="page-content-area">
                        <div class="page-header">
                            <h1><?= $page_title ?><?php if (count($breadcrumbs)): ?><small><?php foreach ($breadcrumbs as $breadcrumb): ?><i class="ace-icon fa fa-angle-double-right"></i>&nbsp;<?= $breadcrumb ?>&nbsp;<?php endforeach; ?></small><?php endif; ?></h1>
                        </div><!-- /.page-header -->

                        <div class="row">
                            <div class="col-xs-12" id="main-body">
                                <!-- PAGE CONTENT BEGINS -->