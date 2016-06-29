<div class="tabbable">
    <ul class="nav nav-tabs" id="myTab">
        <li class="active">
            <a aria-expanded="true" data-toggle="tab" href="#options">
                <i class="ace-icon fa fa-file bigger-120"></i>
                Document Notification
            </a>
        </li>
        <!---
        <li class="">
            <a aria-expanded="false" data-toggle="tab" href="#messages">
                Messages
                <span class="badge badge-danger">4</span>
            </a>
        </li>

        <li class="dropdown">
            <a aria-expanded="false" data-toggle="dropdown" class="dropdown-toggle" href="#">
                Dropdown &nbsp;
                <i class="ace-icon fa fa-caret-down bigger-110 width-auto"></i>
            </a>

            <ul class="dropdown-menu dropdown-info">
                <li>
                    <a data-toggle="tab" href="#dropdown1">@fat</a>
                </li>

                <li>
                    <a data-toggle="tab" href="#dropdown2">@mdo</a>
                </li>
            </ul>
        </li>
        -->
    </ul>

    <div class="tab-content">
        <div id="options" class="tab-pane fade active in">
            <!--<p>*Notification for expiring documents</p>-->
            <div class="row">
                <div class="col-md-5 col-sm-4">
                    <div class="widget-box widget-color-green2">
                        <div class="widget-header">
                            <h4 class="widget-title">Documents</h4>
                        </div>

                        <div class="widget-body" id="employee-setting">
                            <div class="widget-main">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="alert alert-success hidden">


                                            <strong>
                                                <i class="ace-icon fa fa-check"></i>
                                                Success!
                                            </strong>
                                            <br>
                                            <span class="success-msg">The record has been saved.</span>
                                            <br>
                                        </div>
                                        <div class="alert alert-danger hidden">

                                            <strong>
                                                <i class="ace-icon fa fa-times"></i>
                                                Error!
                                            </strong>
                                            <br>
                                            <span class="err-msg"></span>
                                            <br>
                                        </div>
                                    </div>
                                </div>               
                                <br>
                                <div class="col-lg-12"></div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding-right" for="notifier">Notifier:</label>
                                            <div class="col-sm-10">
                                                <select class="form-control" id="notifier">
                                                    <?
                                                    foreach ($employees as $emp)
                                                        echo "<option value='" . $emp[5] . "'>" . $emp[0] . " - " . $emp[2] . " " . $emp[1] . "</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-12"><br></div>
                                
                                <div class="row">
                                <div class="col-sm-8">
                                    <span class="label label-warning"> Notify: </span>
                                    </div>
                                </div>
                                
                                <div class="col-lg-12"></div>
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding-right" for="passport">Passport:</label>
                                            <div class="col-sm-10">
                                                <select multiple="" class="form-control chosen-select " style="width:93.3%;" id="passport" data-placeholder="Expiring Notification" >
                                                    <?
                                                    foreach ($notif as $key => $val)
                                                        echo "<option value=" . $key . ">" . $val . "</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12"><br></div>
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding-right" for="aep">AEP:</label>
                                            <div class="col-sm-10">
                                                <select multiple="" class="form-control chosen-select " style="width:93.3%;" id="aep" data-placeholder="Expiring Notification" >
                                                    <?
                                                    foreach ($notif as $key => $val)
                                                        echo "<option value=" . $key . ">" . $val . "</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-12"><br></div>
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding-right" for="cwv">CWV:</label>
                                            <div class="col-sm-10">
                                                <select multiple="" class="form-control chosen-select " style="width:93.3%;" id="cwv" data-placeholder="Expiring Notification" >
                                                    <?
                                                    foreach ($notif as $key => $val)
                                                        echo "<option value=" . $key . ">" . $val . "</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12"><br></div>

    <!-- <p class="text-muted" id="schedule-pattern">e.g. March <span id="sched-day-from">1</span> to <span id="sched-month-to">March</span> <span id="sched-day-to">31</span></p> -->

                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <button type="button" class="btn btn-sm btn-success pull-right" id="docs-save">
                                            <i class="ace-icon fa fa-save bigger-110"></i>
                                            Save
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--
                <div id="messages" class="tab-pane fade">
                    <p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid.</p>
                </div>
        
                <div id="dropdown1" class="tab-pane fade">
                    <p>Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's organic lomo retro fanny pack lo-fi farm-to-table readymade.</p>
                </div>
        
                <div id="dropdown2" class="tab-pane fade">
                    <p>Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater. Fanny pack portland seitan DIY, art party locavore wolf cliche high life echo park Austin.</p>
                </div>
        -->
    </div>
</div>