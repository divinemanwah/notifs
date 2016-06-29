<?php
$curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));
?>
<div class="row">
    <?php
    if ($last_login):

        $dt = new DateTime(null, new DateTimeZone('Asia/Manila'));

        $dt->setTimestamp($last_login);

        $clone = clone $dt;

        if ($dt->format('D, j M Y') == $curr_date->format('D, j M Y'))
            $_last_login = lang('dashboard_today_label') . ', ' . $dt->format('H:m:i');
        elseif ($clone->setTime(0, 0, 0)->getTimestamp() == strtotime('yesterday'))
            $_last_login = lang('dashboard_yesterday_label') . ', ' . $dt->format('H:m:i');
        else
            $_last_login = $dt->format('D, j M Y H:m:i');
        ?>
        <div class="alert alert-block alert-success">
            <button type="button" class="close" data-dismiss="alert">
                <i class="ace-icon fa fa-times"></i>
            </button>

            <i class="ace-icon fa fa-check green"></i>

            <?=sprintf(lang('dashboard_welcome_label'), '<strong class="green">' . $this->session->userdata('mb_nick') . '</strong>', '<strong class="green">' . $_last_login . '</strong>')?>
        </div>
        <div class="space-6"></div>
    <?php endif; ?>

    <div class="col-sm-6 infobox-container">
        <div class="infobox infobox-green">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-plus"></i>
            </div>

            <div class="infobox-data">
                <span class="infobox-data-number"><?= count($new_hires) ?></span>
                <div class="infobox-content"><?=lang('dashboard_new_hires_label')?></div>
            </div>

            <div class="stat stat-success"><?= round((count($new_hires) / $active_employees) * 100) ?>%</div>
        </div>

        <div class="infobox infobox-blue2">
            <div class="infobox-progress">
                <div class="easy-pie-chart percentage" data-percent="<?= $reg_rate ?>" data-size="46">
                    <span class="percent"><?= $reg_rate ?></span>%
                </div>
            </div>

            <div class="infobox-data">
                <span class="infobox-text"><?=lang('dashboard_regularization_label')?></span>

                <div class="infobox-content">
                    <span class="bigger-110">~</span>
                    <?=lang('dashboard_rounded_label')?>, <?= (intval($curr_date->format('Y')) - 1) ?>&ndash;&lsquo;<?= $curr_date->format('y') ?>
                </div>
            </div>
        </div>

        <div class="infobox infobox-green2">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-bar-chart"></i>
            </div>

            <div class="infobox-data">
                <span class="infobox-data-number"><?= $logged_in_count ?></span>
                <div class="infobox-content"><?=sprintf(lang('dashboard_logged_in_label'), ($logged_in_count > 1 ? 's' : ''))?></div>
            </div>
        </div>

        <div class="infobox infobox-grey">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-minus"></i>
            </div>

            <div class="infobox-data">
                <span class="infobox-data-number"><?= $inactive_count ?></span>
                <div class="infobox-content"><?=lang('dashboard_inactive_accounts_label')?></div>
            </div>
        </div>

        <div class="infobox infobox-orange">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-ban"></i>
            </div>

            <div class="infobox-data">
                <span class="infobox-data-number"><?= ($violation_ave == null ? 0 : $violation_ave) ?></span>
                <div class="infobox-content"><?=lang('dashboard_avg_violations_label')?></div>
            </div>
        </div>

        <div class="infobox infobox-red">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-file"></i>
            </div>

            <div class="infobox-data">
                <span class="infobox-data-number"><?= ($offense_ave == null ? 0 : $offense_ave) ?></span>
                <div class="infobox-content"><?=lang('dashboard_avg_cites_label')?></div>
            </div>
        </div>

        <div class="space-6"></div>

        <div class="infobox infobox-green infobox-small infobox-dark">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-users"></i>
            </div>

            <div class="infobox-data">
                <div class="infobox-content"><?=lang('dashboard_active_label')?></div>
                <div class="infobox-content"><?= $active_employees ?></div>
            </div>
        </div>

        <div class="infobox infobox-blue infobox-small infobox-dark">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-user"></i>
            </div>

            <div class="infobox-data">
                <div class="infobox-content"><?=lang('dashboard_local_label')?></div>
                <div class="infobox-content"><?= $local_employees ?></div>
            </div>
        </div>

        <div class="infobox infobox-orange2 infobox-small infobox-dark">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-user"></i>
            </div>

            <div class="infobox-data">
                <div class="infobox-content"><?=lang('dashboard_expat_label')?></div>
                <div class="infobox-content"><?= $expat_employees ?></div>
            </div>
        </div>

        <div class="infobox infobox-pink infobox-small infobox-dark">
            <div class="infobox-icon">
                <i class="ace-icon fa fa-user"></i>
            </div>

            <div class="infobox-data">
                <div class="infobox-content"><?=lang('dashboard_outsource_label')?></div>
                <div class="infobox-content"><?= $korean_employees ?></div>
            </div>
        </div>
    </div>

    <div class="vspace-12-sm"></div>

    <div class="col-sm-6">
        <div class="widget-box">
            <div class="widget-header widget-header-flat widget-header-small">
                <h5 class="widget-title">
                    <i class="ace-icon fa fa-pie-chart"></i>
                    <?=lang('dashboard_offenses_label')?>
                </h5>

                <div class="widget-toolbar">
                    <div class="inline dropdown-hover">
                        <!-- <button class="btn btn-minier btn-primary">
                        <?php
                        $curr_month = $curr_date->format('F');

// echo $curr_month;
                        ?>
                                <i class="ace-icon fa fa-angle-down icon-on-right bigger-110"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
                        <?php
                        $info = cal_info(0);

                        // foreach($info['months'] as $month)
                        // echo '
                        // <li' . ($month == $curr_month ? ' class="active"' : '') . '>
                        // <a href="#"' . ($month == $curr_month ? ' class="blue"' : '') . '>
                        // <i class="ace-icon fa fa-caret-right bigger-110' . ($month == $curr_month ? '' : ' invisible') . '">&nbsp;</i>
                        // ' . $month . '
                        // </a>
                        // </li>
                        // ';
                        ?>
                        </ul> -->
                        <span class="label label-info">
                            <?=lang('dashboard_' . $curr_month . '_label')?>
                        </span>
                    </div>
                </div>
                <div class="widget-toolbar no-border">
                    <?php
                    $inc_dec = round((($curr_prev_year[0] - $curr_prev_year[1]) / ($curr_prev_year[1] ? $curr_prev_year[1] : $curr_prev_year[1] + 1)) * 100);
                    ?>
                    <div class="badge badge-<?= ($curr_prev_year[0] > $curr_prev_year[1] ? 'danger' : 'success') ?>" id="offenses-increase-decrease" title="<?=sprintf(lang('dashboard_offense_tooltip'), abs($inc_dec), ($curr_prev_year[0] > $curr_prev_year[1] ? 'increase' : 'decrease'))?>">
                        <?= ($curr_prev_year[0] > $curr_prev_year[1] ? '+' : '') . $inc_dec ?>%
                        <i class="ace-icon fa fa-arrow-<?= ($curr_prev_year[0] > $curr_prev_year[1] ? 'up' : 'down') ?>"></i>
                    </div>
                </div>
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-sm-6">
                            <div id="piechart-placeholder"><?=lang('dashboard_loading_label')?>&hellip;</div>
                        </div>
                        <div class="col-sm-6 pie-legend"></div>
                    </div>

                    <div class="hr hr8 hr-double"></div>

                    <div class="clearfix">
                        <div class="grid3">
                            <span class="grey">
                                <i class="ace-icon fa fa-calendar fa-2x"></i>
                                &nbsp; <?= lang('dashboard_' . $curr_date->format('M') . '_label') ?>
                            </span>
                            <h4 class="bigger pull-right"><?= $curr_prev_year[0] ?></h4>
                        </div>

                        <div class="grid3"<?= (intval($curr_date->format('m')) - 1 ? '' : ' style="visibility: hidden;"') ?>>
                            <span class="grey">
                                <i class="ace-icon fa fa-calendar fa-2x"></i>
                                &nbsp; <?= (intval($curr_date->format('m')) - 1 ? lang('dashboard_' . $info['abbrevmonths'][intval($curr_date->format('m')) - 1] . '_label') : '') ?>
                            </span>
                            <h4 class="bigger pull-right"><?= $curr_prev_year[1] ?></h4>
                        </div>

                        <div class="grid3">
                            <span class="grey">
                                <i class="ace-icon fa fa-calendar fa-2x"></i>
                                &nbsp; <?= $curr_date->format('Y') ?>
                            </span>
                            <h4 class="bigger pull-right"><?= $curr_prev_year[2] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="hr hr32 hr-dotted"></div>

<div class="row">
    <div class="col-sm-6">
        <div class="widget-box transparent">
            <div class="widget-header widget-header-flat">
                <h4 class="widget-title lighter">
                    <i class="ace-icon fa fa-star orange"></i>
                    <?=sprintf(lang('dashboard_new_hires_title'), $curr_month)?>
                </h4>

                <!-- <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                                <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                </div> -->
            </div>

            <div class="widget-body">
                <div class="widget-main no-padding">
                    <table class="table table-bordered table-striped">
                        <thead class="thin-border-bottom">
                            <tr>
                                <th>
                                    <?=lang('dashboard_name_header')?>
                                </th>

                                <th>
                                    <?=lang('dashboard_nickname_header')?>
                                </th>

                                <th >
                                    <?=lang('dashboard_dept_header')?>
                                </th>

                                <th >
                                    <?=lang('dashboard_date_header')?>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
// foreach(array_slice($new_hires, 0, 10) as $new_hire)
                            foreach ($new_hires as $new_hire)
                                echo '
										<tr>
											<td>' . $new_hire->mb_name . '</td>
											<td>' . $new_hire->mb_nick . '</td>
											<td>' . $new_hire->dept_name . '</td>
											<td>' . $new_hire->hire_date . '</td>
										</tr>
									';

                            if (!count($new_hires))
                                echo '
										<tr>
											<td colspan="4">' . lang('dashboard_no_entries_label') . '</td>
										</tr>
									';
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <div class="col-sm-6">
        <div class="widget-box transparent">
            <div class="widget-header widget-header-flat">
                <h4 class="widget-title lighter">
                    <i class="ace-icon fa fa-user grey"></i>
                    <?=lang('dashboard_resigned_title')?>
                </h4>

                <!-- <div class="widget-toolbar">
                        <a href="#" data-action="collapse">
                                <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                </div> -->
            </div>

            <div class="widget-body">
                <div class="widget-main no-padding">
                    <table class="table table-bordered table-striped">
                        <thead class="thin-border-bottom">
                            <tr>
                                <th>
                                    <?=lang('dashboard_name_header')?>
                                </th>

                                <th>
                                    <?=lang('dashboard_nickname_header')?>
                                </th>

                                <th >
                                    <?=lang('dashboard_dept_header')?>
                                </th>

                                <th >
                                    <?=lang('dashboard_date_header')?>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
// foreach(array_slice($new_hires, 0, 10) as $new_hire)
                            foreach ($new_resigned as $new_resign)
                                echo '
										<tr>
											<td>' . $new_resign->mb_name . '</td>
											<td>' . $new_resign->mb_nick . '</td>
											<td>' . $new_resign->dept_name . '</td>
											<td>' . $new_resign->mb_resign_date . '</td>
										</tr>
									';

                            if (!count($new_resigned))
                                echo '
										<tr>
											<td colspan="4">' . lang('dashboard_no_resigned_label') . '</td>
										</tr>
									';
                            ?>
                        </tbody>
                    </table>
                    <div class="space-6"></div>
                    <div class="col-sm-12">
                        <div class="hr hr8 hr-double"></div>
                        <div class="col-sm-2"></div>
                        <div class="col-sm-9">
                            <div class="clearfix">
                                <div class="grid3">
                                    <span class="grey">
                                        <i class="ace-icon fa fa-signal fa-2x blue"></i>
                                        &nbsp; <?=lang('dashboard_total_label')?>
                                    </span>
                                        <h4 class="bigger pull-right"><?= $total_local[0]->total+$total_expat[0]->total ?></h4>
                                </div>

                                <div class="grid3">
                                    <span class="grey">
                                        <i class="ace-icon fa fa-user fa-2x purple"></i>
                                        &nbsp; <?=lang('dashboard_local_label')?>
                                    </span>
                                    <h4 class="bigger pull-right"><?= $total_local[0]->total ?></h4>
                                </div>

                                <div class="grid3">
                                    <span class="grey">
                                        <i class="ace-icon fa fa-user fa-2x red"></i>
                                        &nbsp; <?=lang('dashboard_expat_label')?>
                                    </span>
                                    <h4 class="bigger pull-right"><?= $total_expat[0]->total ?></h4>
                                </div>
                            </div>
                            <!--
                            <div class="infobox infobox-green infobox-small infobox-dark">
                                <div class="infobox-icon"> 
                                    <i class="ace-icon fa fa-signal" ></i>
                                </div>

                                <div class="infobox-data">
                                    <div class="infobox-content">Total</div>

                                </div>
                            </div>
                            <div class="infobox infobox-blue infobox-small infobox-dark"> 
                                <div class="infobox-icon"> 
                                    <i class="ace-icon fa fa-user"></i> 
                                </div> 
                                <div class="infobox-data"> 
                                    <div class="infobox-content">Local</div> 
                                    <div class="infobox-content"><?= $total_local[0]->total ?></div> 
                                </div> 
                            </div>
                            <div class="infobox infobox-orange2 infobox-small infobox-dark"> 
                                <div class="infobox-icon"> 
                                    <i class="ace-icon fa fa-user"></i> 
                                </div> 
                                <div class="infobox-data"> 
                                    <div class="infobox-content">Expat</div> 
                                    <div class="infobox-content"><?= $total_expat[0]->total ?></div> 
                                </div> 
                            </div>
                            
                            -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!---
        <div class="col-sm-6">
                <div class="widget-box transparent">
                        <div class="widget-header widget-header-flat">
                                <h4 class="widget-title lighter">
                                        <i class="ace-icon fa fa-line-chart"></i>
                                        Department statistics
                                </h4>


                        </div>

                        <div class="widget-body">
                                <div class="widget-main padding-4">
                                        <div id="dept-kpi-stats">Please wait&hellip;</div>
                                </div>
                        </div>
                </div>
        </div>
        -->    


    </div>

    <script type="text/javascript">
        var cite_pie_data = <?= $cites_pie_data ?>;
    </script>