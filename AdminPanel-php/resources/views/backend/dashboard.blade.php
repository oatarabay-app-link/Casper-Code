@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <div class="row">
        <br/>
        <br/>

        <div class="col-sm-6 col-lg-10   ">
            <h4>{{$filter_info}}</h4>
        </div>
        <div class="col-sm-6 col-lg-2">

          <span style="float:right;">
              <button class="btn btn-info mb-1" type="button" data-toggle="modal" data-target="#infoModal"><i
                          class="icon-settings"></i> &nbsp;Filter</button>
          </span>
            <br>
            <br>
            <br>
            <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true" style="display: none;">
                <form method="get">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Filters</h4>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <h6>Select Platform</h6>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Platform</label>
                                    <div class="col-md-9 col-form-label">
                                        <div class="form-check  mr-1">
                                            <input class="form-check-input" id="inline-radio1" type="radio"
                                                   value="all" name="platform" checked>
                                            <label class="form-check-label" for="inline-radio1">All</label>
                                        </div>
                                        <div class="form-check  mr-1">
                                            <input class="form-check-input" id="inline-radio2" type="radio"
                                                   value="ios" name="platform">
                                            <label class="form-check-label" for="inline-radio2">IOS</label>
                                        </div>
                                        <div class="form-check mr-1">
                                            <input class="form-check-input" id="inline-radio3" type="radio"
                                                   value="android" name="platform">
                                            <label class="form-check-label" for="inline-radio3">Android</label>
                                        </div>

                                        <div class="form-check  mr-1">
                                            <input class="form-check-input" id="inline-radio3" type="radio"
                                                   value="bitcoin" name="platform">
                                            <label class="form-check-label" for="inline-radio3">Bitcoin</label>
                                        </div>
                                        <div class="form-check  mr-1">
                                            <input class="form-check-input" id="inline-radio3" type="radio"
                                                   value="futromo_mobile" name="platform">
                                            <label class="form-check-label" for="inline-radio3">Futromo Mobile Payments</label>
                                        </div>
                                    </div>
                                </div>

                                <h6>Date Range</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <label>Date From :</label>
                                        <input class="form-control" type="date" name="date_from" value="<?php echo date('Y-m-d'); ?>"/>
                                    </div>
                                    <div class="col-6">
                                        <label>Date To :</label>
                                        <input class="form-control" type="date" name="date_to" value="<?php echo date('Y-m-d'); ?>"/>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                                <button class="btn btn-info" type="submit">Filter!</button>
                            </div>
                        </div>
                        <!-- /.modal-content-->
                    </div>
                    <!-- /.modal-dialog-->
                </form>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-sm-6 col-lg-2   ">
                        <div class="card text-white bg-primary">
                            <div class="card-body pb-0">
                                <div class="text-value">{{$app_signups}}</div>
                                <div> App Signups</div>
                                <div><a href="{{route('admin.admin.reports.app-signups') }}"> more.</a></div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:100px;">
                                <canvas class="chart" id="card-signups" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <div class="card text-white bg-warning">
                            <div class="card-body pb-0">

                                <div class="text-value">{{$emails_delivered_total}}</div>
                                <div>Emails Opened</div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:70px;">
                                <canvas class="chart" id="card-emails_delivered" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <div class="card text-white bg-warning">
                            <div class="card-body pb-0">

                                <div class="text-value">{{$emails_not_delivered_total}}</div>
                                <div>Emails Problems</div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:70px;">
                                <canvas class="chart" id="card-emails_not_delivered" height="100"></canvas>
                            </div>
                        </div>
                    </div>



                    <div class="col-sm-6 col-lg-2">
                        <div class="card text-white bg-primary">
                            <div class="card-body pb-0">
                                <div class="text-value">{{$app_signedins}}</div>
                                <div>App Signed Ins</div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:100px;">
                                <canvas class="chart" id="card-signedins" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <div class="card text-white bg-primary">
                            <div class="card-body pb-0">
                                <div class="text-value">{{$app_first_time_connections_total}}</div>
                                <div>First Time Connections</div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:100px;">
                                <canvas class="chart" id="card-first_time" height="100"></canvas>
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body pb-0">

                                <div class="text-value">{{$sales_free_premium_trial_total}}</div>
                                <div>Free Premium/ Trails</div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:70px;">
                                <canvas class="chart" id="card-sales_free_premium_trial" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-info">
                            <div class="card-body pb-0">
                                <button class="btn btn-transparent p-0 float-right" type="button">
                                    <i class="icon-location-pin"></i>
                                </button>
                                <div class="text-value">{{$in_app_purchases_total}}</div>
                                <div>In App Purchases</div>
                            </div>
                            <div class="chart-wrapper mt-3 mx-3" style="height:100px;">
                                <canvas class="chart" id="card-in_app_purchases" height="100"></canvas>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body pb-0">

                                <div class="text-value">{{$sales_total}}</div>
                                <div>USD Sales</div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:70px;">
                                <canvas class="chart" id="card-sales" height="100"></canvas>
                            </div>
                        </div>
                    </div>











{{--                    <div class="col-sm-6 col-lg-3">--}}
{{--                    <div class="card text-white bg-danger">--}}
{{--                    <div class="card-body pb-0">--}}
{{--                    <div class="btn-group float-right">--}}
{{--                    <button class="btn btn-transparent dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                    <i class="icon-settings"></i>--}}
{{--                    </button>--}}
{{--                    <div class="dropdown-menu dropdown-menu-right">--}}
{{--                    <a class="dropdown-item" href="#">Action</a>--}}
{{--                    <a class="dropdown-item" href="#">Another action</a>--}}
{{--                    <a class="dropdown-item" href="#">Something else here</a>--}}
{{--                    </div>--}}
{{--                    </div>--}}
{{--                    <div class="text-value">9.823</div>--}}
{{--                    <div>Members online</div>--}}
{{--                    </div>--}}
{{--                    <div class="chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>--}}
{{--                    <canvas class="chart chartjs-render-monitor" id="card-chart4" height="70" width="216" style="display: block; width: 216px; height: 70px;"></canvas>--}}
{{--                    </div>--}}
{{--                    </div>--}}
{{--                    </div>--}}

                </div>


                <!-- row 1 end -->

                <!-- row 2 -->


                <div class="row">


                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body pb-0">
                                <div class="text-value">{{$subscriptions_total}}</div>
                                <div>New Subscribers.</div>
                            </div>
                            <div class="chart-wrapper mt-3 mx-3" style="height:70px;">
                                <canvas class="chart" id="card-subscriptions" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body pb-0">
                                <div class="btn-group float-right">
                                    <button class="btn btn-transparent dropdown-toggle p-0" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-settings"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                                <div class="text-value">0</div>
                                <div>Active Paying Subscribers</div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:70px;">
                                <div class="chartjs-size-monitor"
                                     style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                    <div class="chartjs-size-monitor-expand"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                    </div>
                                </div>
                                <canvas class="chart chartjs-render-monitor" id="card-chart3" height="70"
                                        width="248"
                                        style="display: block; width: 248px; height: 70px;"></canvas>
                                <div id="card-chart3-tooltip" class="chartjs-tooltip top"
                                     style="opacity: 0; left: 0px; top: 103.4px;">
                                    <div class="tooltip-header">
                                        <div class="tooltip-header-item">January</div>
                                    </div>
                                    <div class="tooltip-body">
                                        <div class="tooltip-body-item"><span class="tooltip-body-item-color"
                                                                             style="background-color: rgba(230, 230, 230, 0.2);"></span><span
                                                class="tooltip-body-item-label">My First dataset</span><span
                                                class="tooltip-body-item-value">78</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body pb-0">
                                <div class="btn-group float-right">
                                    <button class="btn btn-transparent dropdown-toggle p-0" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-settings"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                                <div class="text-value">0</div>
                                <div>Potential Recurring Subscribers</div>
                            </div>
                            <div class="chart-wrapper mt-3" style="height:70px;">
                                <div class="chartjs-size-monitor"
                                     style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                    <div class="chartjs-size-monitor-expand"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                    </div>
                                </div>
                                <canvas class="chart chartjs-render-monitor" id="card-chart3" height="70"
                                        width="248"
                                        style="display: block; width: 248px; height: 70px;"></canvas>
                                <div id="card-chart3-tooltip" class="chartjs-tooltip top"
                                     style="opacity: 0; left: 0px; top: 103.4px;">
                                    <div class="tooltip-header">
                                        <div class="tooltip-header-item">January</div>
                                    </div>
                                    <div class="tooltip-body">
                                        <div class="tooltip-body-item"><span class="tooltip-body-item-color"
                                                                             style="background-color: rgba(230, 230, 230, 0.2);"></span><span
                                                class="tooltip-body-item-label">My First dataset</span><span
                                                class="tooltip-body-item-value">78</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-info">
                            <div class="card-body pb-0">
                                <button class="btn btn-transparent p-0 float-right" type="button">
                                    <i class="icon-location-pin"></i>
                                </button>
                                <div class="text-value">0</div>
                                <div>Actual Recurring Subscribers</div>
                            </div>
                            <div class="chart-wrapper mt-3 mx-3" style="height:70px;">
                                <div class="chartjs-size-monitor"
                                     style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                    <div class="chartjs-size-monitor-expand"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                    </div>
                                </div>
                                <canvas class="chart chartjs-render-monitor" id="card-chart2" height="70"
                                        width="216"
                                        style="display: block; width: 216px; height: 70px;"></canvas>
                                <div id="card-chart2-tooltip" class="chartjs-tooltip bottom"
                                     style="opacity: 0; left: 22.5576px; top: 138.372px;">
                                    <div class="tooltip-header">
                                        <div class="tooltip-header-item">January</div>
                                    </div>
                                    <div class="tooltip-body">
                                        <div class="tooltip-body-item"><span class="tooltip-body-item-color"
                                                                             style="background-color: rgb(38, 203, 253);"></span><span
                                                    class="tooltip-body-item-label">My First dataset</span><span
                                                    class="tooltip-body-item-value">1</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body pb-0">
                                <div class="btn-group float-right">
                                    <button class="btn btn-transparent dropdown-toggle p-0" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-settings"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                                <div class="text-value">0</div>
                                <div>Subscription Activity</div>
                            </div>
                            <div class="chart-wrapper mt-3 mx-3" style="height:70px;">
                                <div class="chartjs-size-monitor"
                                     style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                    <div class="chartjs-size-monitor-expand"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                    </div>
                                </div>
                                <canvas class="chart chartjs-render-monitor" id="card-chart4" height="70"
                                        width="216"
                                        style="display: block; width: 216px; height: 70px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body pb-0">
                                <div class="btn-group float-right">
                                    <button class="btn btn-transparent dropdown-toggle p-0" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-settings"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                                <div class="text-value">0</div>
                                <div>Average Lifetime Value</div>
                            </div>
                            <div class="chart-wrapper mt-3 mx-3" style="height:70px;">
                                <div class="chartjs-size-monitor"
                                     style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                                    <div class="chartjs-size-monitor-expand"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                                    </div>
                                    <div class="chartjs-size-monitor-shrink"
                                         style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                        <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                                    </div>
                                </div>
                                <canvas class="chart chartjs-render-monitor" id="card-chart4" height="70"
                                        width="216"
                                        style="display: block; width: 216px; height: 70px;"></canvas>
                            </div>
                        </div>
                    </div>

                </div>


                <!-- row 2 end -->


                <!-- facebook -->

                {{--<div class="row">--}}
                    {{--<div class="col-sm-6 col-lg-3">--}}
                        {{--<div class="brand-card">--}}
                            {{--<div class="brand-card-header bg-facebook">--}}
                                {{--<i class="fa fa-facebook"></i>--}}
                                {{--<div class="chart-wrapper">--}}
                                    {{--<div class="chartjs-size-monitor"--}}
                                         {{--style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">--}}
                                        {{--<div class="chartjs-size-monitor-expand"--}}
                                             {{--style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">--}}
                                            {{--<div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>--}}
                                        {{--</div>--}}
                                        {{--<div class="chartjs-size-monitor-shrink"--}}
                                             {{--style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">--}}
                                            {{--<div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<canvas id="social-box-chart-1" height="96" width="248"--}}
                                            {{--class="chartjs-render-monitor"--}}
                                            {{--style="display: block; width: 248px; height: 96px;"></canvas>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="brand-card-body">--}}
                                {{--<div>--}}
                                    {{--<div class="text-value">89k</div>--}}
                                    {{--<div class="text-uppercase text-muted small">Impressions</div>--}}
                                {{--</div>--}}
                                {{--<div>--}}
                                    {{--<div class="text-value">459</div>--}}
                                    {{--<div class="text-uppercase text-muted small">...</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="col-sm-6 col-lg-3">--}}
                        {{--<div class="brand-card">--}}
                            {{--<div class="brand-card-header bg-twitter">--}}
                                {{--<i class="fa fa-twitter"></i>--}}
                                {{--<div class="chart-wrapper">--}}
                                    {{--<div class="chartjs-size-monitor"--}}
                                         {{--style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">--}}
                                        {{--<div class="chartjs-size-monitor-expand"--}}
                                             {{--style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">--}}
                                            {{--<div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>--}}
                                        {{--</div>--}}
                                        {{--<div class="chartjs-size-monitor-shrink"--}}
                                             {{--style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">--}}
                                            {{--<div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<canvas id="social-box-chart-2" height="96" width="248"--}}
                                            {{--class="chartjs-render-monitor"--}}
                                            {{--style="display: block; width: 248px; height: 96px;"></canvas>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="brand-card-body">--}}
                                {{--<div>--}}
                                    {{--<div class="text-value">973k</div>--}}
                                    {{--<div class="text-uppercase text-muted small">Product Page Views</div>--}}
                                {{--</div>--}}
                                {{--<div>--}}
                                    {{--<div class="text-value">1.792</div>--}}
                                    {{--<div class="text-uppercase text-muted small">...</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="col-sm-6 col-lg-3">--}}
                        {{--<div class="brand-card">--}}
                            {{--<div class="brand-card-header bg-linkedin">--}}
                                {{--<i class="fa fa-linkedin"></i>--}}
                                {{--<div class="chart-wrapper">--}}
                                    {{--<div class="chartjs-size-monitor"--}}
                                         {{--style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">--}}
                                        {{--<div class="chartjs-size-monitor-expand"--}}
                                             {{--style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">--}}
                                            {{--<div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>--}}
                                        {{--</div>--}}
                                        {{--<div class="chartjs-size-monitor-shrink"--}}
                                             {{--style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">--}}
                                            {{--<div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<canvas id="social-box-chart-3" height="96" width="248"--}}
                                            {{--class="chartjs-render-monitor"--}}
                                            {{--style="display: block; width: 248px; height: 96px;"></canvas>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="brand-card-body">--}}
                                {{--<div>--}}
                                    {{--<div class="text-value">500+</div>--}}
                                    {{--<div class="text-uppercase text-muted small">App Units</div>--}}
                                {{--</div>--}}
                                {{--<div>--}}
                                    {{--<div class="text-value">292</div>--}}
                                    {{--<div class="text-uppercase text-muted small">Downloads</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="col-sm-6 col-lg-3">--}}
                        {{--<div class="brand-card">--}}
                            {{--<div class="brand-card-header bg-google-plus">--}}
                                {{--<i class="fa fa-google-plus"></i>--}}
                                {{--<div class="chart-wrapper">--}}
                                    {{--<div class="chartjs-size-monitor"--}}
                                         {{--style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">--}}
                                        {{--<div class="chartjs-size-monitor-expand"--}}
                                             {{--style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">--}}
                                            {{--<div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>--}}
                                        {{--</div>--}}
                                        {{--<div class="chartjs-size-monitor-shrink"--}}
                                             {{--style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">--}}
                                            {{--<div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<canvas id="social-box-chart-4" height="96" width="248"--}}
                                            {{--class="chartjs-render-monitor"--}}
                                            {{--style="display: block; width: 248px; height: 96px;"></canvas>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="brand-card-body">--}}
                                {{--<div>--}}
                                    {{--<div class="text-value">894</div>--}}
                                    {{--<div class="text-uppercase text-muted small">Conversion Rate</div>--}}
                                {{--</div>--}}
                                {{--<div>--}}
                                    {{--<div class="text-value">92</div>--}}
                                    {{--<div class="text-uppercase text-muted small">...</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                {{--</div>--}}


                <!-- facebook -->


                <!-- widgets -->


                <div class="row">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card">
                            <div class="card-block" style="text-align: center; padding: 15px 10px 15px 10px;">
                                <div class="h4 m-0">{{$users_total}}</div>
                                <div>Total Users</div>
                                <div class="progress progress-xs my-1">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                         aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                            </div>
                        </div>
                    </div><!--/.col-->
                    <div class="col-sm-6 col-lg-4">
                        <div class="card">
                            <div class="card-block" style="text-align: center; padding: 15px 10px 15px 10px;">
                                <div class="h4 m-0">{{$users_active}}</div>
                                <div>Active Users</div>
                                <div class="progress progress-xs my-1">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{($users_active/$users_total)*100}}%"
                                         aria-valuenow="{{(($users_active / $users_total)*100)}}" aria-valuemin="0" aria-valuemax="{{$users_total}}"></div>
                                </div>

                            </div>
                        </div>
                    </div><!--/.col-->
                    <div class="col-sm-6 col-lg-4">
                        <div class="card">
                            <div class="card-block" style="text-align: center; padding: 15px 10px 15px 10px;">
                                <div class="h4 m-0">{{$servers_active}} </div>
                                <div>active out of {{$servers_total}} total servers</div>
                                <div class="progress progress-xs my-1">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{($servers_active/$servers_total)*100}}%"
                                         aria-valuenow="{{($servers_active / $servers_total)*100}}" aria-valuemin="0" aria-valuemax="{{$servers_total}}"></div>
                                </div>

                            </div>
                        </div>
                    </div><!--/.col-->



                </div><!--/.row-->


                <!-- widgets -->


                {{--<div class="row">--}}
                    {{--<div class="col-6 col-lg-4">--}}
                        {{--<div class="card">--}}
                            {{--<div class="card-block p-1 clearfix">--}}
                                {{--<i class="fa fa-laptop bg-info p-1 font-2xl mr-1 float-left"></i>--}}
                                {{--<div class="h5 text-info mb-0 mt-h">$1.999,50</div>--}}
                                {{--<div class="text-muted text-uppercase font-weight-bold font-xs">SignedUp User--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div><!--/.col-->--}}

                    {{--<div class="col-6 col-lg-4">--}}
                        {{--<div class="card">--}}
                            {{--<div class="card-block p-1 clearfix">--}}
                                {{--<i class="fa fa-moon bg-warning p-1 font-2xl mr-1 float-left"></i>--}}
                                {{--<div class="h5 text-warning mb-0 mt-h">$1.999,50</div>--}}
                                {{--<div class="text-muted text-uppercase font-weight-bold font-xs">Last SignedUp--}}
                                    {{--Users--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div><!--/.col-->--}}

                    {{--<div class="col-6 col-lg-4">--}}
                        {{--<div class="card">--}}
                            {{--<div class="card-block p-1 clearfix">--}}
                                {{--<i class="fa fa-bell bg-danger p-1 font-2xl mr-1 float-left"></i>--}}
                                {{--<div class="h5 text-danger mb-0 mt-h">$1.999,50</div>--}}
                                {{--<div class="text-muted text-uppercase font-weight-bold font-xs">Total Users--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div><!--/.col-->--}}
                {{--</div><!--/.row-->--}}


                <!-- table -->

{{--                <div class="table-responsive" style="background-color: white;">--}}
{{--                    <table class="table">--}}
{{--                        <h2></h2>--}}
{{--                        <thead>--}}
{{--                        <tr>--}}

{{--                            <th scope="col">Servers</th>--}}
{{--                            <th scope="col">Server Load</th>--}}
{{--                            <th scope="col">Connected Users</th>--}}

{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        <tr>--}}
{{--                            <th scope="row">1</th>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th scope="row">2</th>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th scope="row">3</th>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                        </tr>--}}


{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}

                <br><br>


{{--                <div class="table-responsive" style="background-color: white;">--}}

{{--                    <!-- up coming renewls  -->--}}
{{--                    <table class="table">--}}
{{--                        <h2>Upcoming Renewal</h2>--}}
{{--                        <thead>--}}
{{--                        <tr>--}}

{{--                            <th scope="col">Service</th>--}}
{{--                            <th scope="col">Service Renewal</th>--}}
{{--                            <th scope="col">Renewal date</th>--}}
{{--                            <th scope="col">Price</th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        <tr>--}}
{{--                            <th scope="row">1</th>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th scope="row">2</th>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th scope="row">3</th>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th scope="row">4</th>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                            <td></td>--}}
{{--                        </tr>--}}


{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                    <!-- up coming renewls  end  -->--}}
{{--                </div>--}}

                <br><br>

                <!-- table -->


{{--                <div class="row">--}}
{{--                    <div class="col-md-12">--}}
{{--                        <div class="card">--}}
{{--                            <div class="card-header">Traffic &amp; Sales</div>--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-sm-6">--}}
{{--                                        <div class="row">--}}
{{--                                            <div class="col-sm-6">--}}
{{--                                                <div class="callout callout-info">--}}
{{--                                                    <small class="text-muted">New Clients</small>--}}
{{--                                                    <br>--}}
{{--                                                    <strong class="h4">9,123</strong>--}}
{{--                                                    <div class="chart-wrapper">--}}
{{--                                                        <canvas id="sparkline-chart-1" width="100"--}}
{{--                                                                height="30"></canvas>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                            <div class="col-sm-6">--}}
{{--                                                <div class="callout callout-danger">--}}
{{--                                                    <small class="text-muted">Recuring Clients</small>--}}
{{--                                                    <br>--}}
{{--                                                    <strong class="h4">22,643</strong>--}}
{{--                                                    <div class="chart-wrapper">--}}
{{--                                                        <canvas id="sparkline-chart-2" width="100"--}}
{{--                                                                height="30"></canvas>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                        </div>--}}

{{--                                        <hr class="mt-0">--}}
{{--                                        <div class="progress-group mb-4">--}}
{{--                                            <div class="progress-group-prepend">--}}
{{--                                                <span class="progress-group-text">Monday</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                         style="width: 34%" aria-valuenow="34" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-danger" role="progressbar"--}}
{{--                                                         style="width: 78%" aria-valuenow="78" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group mb-4">--}}
{{--                                            <div class="progress-group-prepend">--}}
{{--                                                <span class="progress-group-text">Tuesday</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                         style="width: 56%" aria-valuenow="56" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-danger" role="progressbar"--}}
{{--                                                         style="width: 94%" aria-valuenow="94" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group mb-4">--}}
{{--                                            <div class="progress-group-prepend">--}}
{{--                                                <span class="progress-group-text">Wednesday</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                         style="width: 12%" aria-valuenow="12" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-danger" role="progressbar"--}}
{{--                                                         style="width: 67%" aria-valuenow="67" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group mb-4">--}}
{{--                                            <div class="progress-group-prepend">--}}
{{--                                                <span class="progress-group-text">Thursday</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                         style="width: 43%" aria-valuenow="43" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-danger" role="progressbar"--}}
{{--                                                         style="width: 91%" aria-valuenow="91" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group mb-4">--}}
{{--                                            <div class="progress-group-prepend">--}}
{{--                                                <span class="progress-group-text">Friday</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                         style="width: 22%" aria-valuenow="22" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-danger" role="progressbar"--}}
{{--                                                         style="width: 73%" aria-valuenow="73" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group mb-4">--}}
{{--                                            <div class="progress-group-prepend">--}}
{{--                                                <span class="progress-group-text">Saturday</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                         style="width: 53%" aria-valuenow="53" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-danger" role="progressbar"--}}
{{--                                                         style="width: 82%" aria-valuenow="82" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group mb-4">--}}
{{--                                            <div class="progress-group-prepend">--}}
{{--                                                <span class="progress-group-text">Sunday</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                         style="width: 9%" aria-valuenow="9" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-danger" role="progressbar"--}}
{{--                                                         style="width: 69%" aria-valuenow="69" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                    <div class="col-sm-6">--}}
{{--                                        <div class="row">--}}
{{--                                            <div class="col-sm-6">--}}
{{--                                                <div class="callout callout-warning">--}}
{{--                                                    <small class="text-muted">Pageviews</small>--}}
{{--                                                    <br>--}}
{{--                                                    <strong class="h4">78,623</strong>--}}
{{--                                                    <div class="chart-wrapper">--}}
{{--                                                        <canvas id="sparkline-chart-3" width="100"--}}
{{--                                                                height="30"></canvas>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                            <div class="col-sm-6">--}}
{{--                                                <div class="callout callout-success">--}}
{{--                                                    <small class="text-muted">Organic</small>--}}
{{--                                                    <br>--}}
{{--                                                    <strong class="h4">49,123</strong>--}}
{{--                                                    <div class="chart-wrapper">--}}
{{--                                                        <canvas id="sparkline-chart-4" width="100"--}}
{{--                                                                height="30"></canvas>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                        </div>--}}

{{--                                        <hr class="mt-0">--}}
{{--                                        <div class="progress-group">--}}
{{--                                            <div class="progress-group-header">--}}
{{--                                                <i class="icon-user progress-group-icon"></i>--}}
{{--                                                <div>Male</div>--}}
{{--                                                <div class="ml-auto font-weight-bold">43%</div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-warning" role="progressbar"--}}
{{--                                                         style="width: 43%" aria-valuenow="43" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group mb-5">--}}
{{--                                            <div class="progress-group-header">--}}
{{--                                                <i class="icon-user-female progress-group-icon"></i>--}}
{{--                                                <div>Female</div>--}}
{{--                                                <div class="ml-auto font-weight-bold">37%</div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-warning" role="progressbar"--}}
{{--                                                         style="width: 43%" aria-valuenow="43" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group">--}}
{{--                                            <div class="progress-group-header align-items-end">--}}
{{--                                                <i class="icon-globe progress-group-icon"></i>--}}
{{--                                                <div>Organic Search</div>--}}
{{--                                                <div class="ml-auto font-weight-bold mr-2">191.235</div>--}}
{{--                                                <div class="text-muted small">(56%)</div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-success" role="progressbar"--}}
{{--                                                         style="width: 56%" aria-valuenow="56" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group">--}}
{{--                                            <div class="progress-group-header align-items-end">--}}
{{--                                                <i class="icon-social-facebook progress-group-icon"></i>--}}
{{--                                                <div>Facebook</div>--}}
{{--                                                <div class="ml-auto font-weight-bold mr-2">51.223</div>--}}
{{--                                                <div class="text-muted small">(15%)</div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-success" role="progressbar"--}}
{{--                                                         style="width: 15%" aria-valuenow="15" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group">--}}
{{--                                            <div class="progress-group-header align-items-end">--}}
{{--                                                <i class="icon-social-twitter progress-group-icon"></i>--}}
{{--                                                <div>Twitter</div>--}}
{{--                                                <div class="ml-auto font-weight-bold mr-2">37.564</div>--}}
{{--                                                <div class="text-muted small">(11%)</div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-success" role="progressbar"--}}
{{--                                                         style="width: 11%" aria-valuenow="11" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="progress-group">--}}
{{--                                            <div class="progress-group-header align-items-end">--}}
{{--                                                <i class="icon-social-linkedin progress-group-icon"></i>--}}
{{--                                                <div>LinkedIn</div>--}}
{{--                                                <div class="ml-auto font-weight-bold mr-2">27.319</div>--}}
{{--                                                <div class="text-muted small">(8%)</div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress-group-bars">--}}
{{--                                                <div class="progress progress-xs">--}}
{{--                                                    <div class="progress-bar bg-success" role="progressbar"--}}
{{--                                                         style="width: 8%" aria-valuenow="8" aria-valuemin="0"--}}
{{--                                                         aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                </div>--}}

{{--                                <br>--}}


{{--                                <br>--}}
{{--                                <table class="table table-responsive-sm table-hover table-outline mb-0">--}}
{{--                                    <thead class="thead-light">--}}
{{--                                    <tr>--}}
{{--                                        <th class="text-center">--}}
{{--                                            <i class="icon-people"></i>--}}
{{--                                        </th>--}}
{{--                                        <th>User</th>--}}
{{--                                        <th class="text-center">Country</th>--}}
{{--                                        <th>Usage</th>--}}
{{--                                        <th class="text-center">Payment Method</th>--}}
{{--                                        <th>Activity</th>--}}
{{--                                    </tr>--}}
{{--                                    </thead>--}}
{{--                                    <tbody>--}}
{{--                                    <tr>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <div class="avatar">--}}
{{--                                                <img class="img-avatar" src="img/avatars/1.jpg"--}}
{{--                                                     alt="admin@bootstrapmaster.com">--}}
{{--                                                <span class="avatar-status badge-success"></span>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div>Yiorgos Avraamu</div>--}}
{{--                                            <div class="small text-muted">--}}
{{--                                                <span>New</span> | Registered: Jan 1, 2015--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="flag-icon flag-icon-us h4 mb-0" id="us" title="us"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="clearfix">--}}
{{--                                                <div class="float-left">--}}
{{--                                                    <strong>50%</strong>--}}
{{--                                                </div>--}}
{{--                                                <div class="float-right">--}}
{{--                                                    <small class="text-muted">Jun 11, 2015 - Jul 10, 2015--}}
{{--                                                    </small>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress progress-xs">--}}
{{--                                                <div class="progress-bar bg-success" role="progressbar"--}}
{{--                                                     style="width: 50%" aria-valuenow="50" aria-valuemin="0"--}}
{{--                                                     aria-valuemax="100"></div>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="fa fa-cc-mastercard" style="font-size:24px"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="small text-muted">Last login</div>--}}
{{--                                            <strong>10 sec ago</strong>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <div class="avatar">--}}
{{--                                                <img class="img-avatar" src="img/avatars/2.jpg"--}}
{{--                                                     alt="admin@bootstrapmaster.com">--}}
{{--                                                <span class="avatar-status badge-danger"></span>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div>Avram Tarasios</div>--}}
{{--                                            <div class="small text-muted">--}}
{{--                                                <span>Recurring</span> | Registered: Jan 1, 2015--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="flag-icon flag-icon-br h4 mb-0" id="br" title="br"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="clearfix">--}}
{{--                                                <div class="float-left">--}}
{{--                                                    <strong>10%</strong>--}}
{{--                                                </div>--}}
{{--                                                <div class="float-right">--}}
{{--                                                    <small class="text-muted">Jun 11, 2015 - Jul 10, 2015--}}
{{--                                                    </small>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress progress-xs">--}}
{{--                                                <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                     style="width: 10%" aria-valuenow="10" aria-valuemin="0"--}}
{{--                                                     aria-valuemax="100"></div>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="fa fa-cc-visa" style="font-size:24px"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="small text-muted">Last login</div>--}}
{{--                                            <strong>5 minutes ago</strong>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <div class="avatar">--}}
{{--                                                <img class="img-avatar" src="img/avatars/3.jpg"--}}
{{--                                                     alt="admin@bootstrapmaster.com">--}}
{{--                                                <span class="avatar-status badge-warning"></span>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div>Quintin Ed</div>--}}
{{--                                            <div class="small text-muted">--}}
{{--                                                <span>New</span> | Registered: Jan 1, 2015--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="flag-icon flag-icon-in h4 mb-0" id="in" title="in"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="clearfix">--}}
{{--                                                <div class="float-left">--}}
{{--                                                    <strong>74%</strong>--}}
{{--                                                </div>--}}
{{--                                                <div class="float-right">--}}
{{--                                                    <small class="text-muted">Jun 11, 2015 - Jul 10, 2015--}}
{{--                                                    </small>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress progress-xs">--}}
{{--                                                <div class="progress-bar bg-warning" role="progressbar"--}}
{{--                                                     style="width: 74%" aria-valuenow="74" aria-valuemin="0"--}}
{{--                                                     aria-valuemax="100"></div>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="fa fa-cc-stripe" style="font-size:24px"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="small text-muted">Last login</div>--}}
{{--                                            <strong>1 hour ago</strong>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <div class="avatar">--}}
{{--                                                <img class="img-avatar" src="img/avatars/4.jpg"--}}
{{--                                                     alt="admin@bootstrapmaster.com">--}}
{{--                                                <span class="avatar-status badge-secondary"></span>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div>Enéas Kwadwo</div>--}}
{{--                                            <div class="small text-muted">--}}
{{--                                                <span>New</span> | Registered: Jan 1, 2015--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="flag-icon flag-icon-fr h4 mb-0" id="fr" title="fr"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="clearfix">--}}
{{--                                                <div class="float-left">--}}
{{--                                                    <strong>98%</strong>--}}
{{--                                                </div>--}}
{{--                                                <div class="float-right">--}}
{{--                                                    <small class="text-muted">Jun 11, 2015 - Jul 10, 2015--}}
{{--                                                    </small>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress progress-xs">--}}
{{--                                                <div class="progress-bar bg-danger" role="progressbar"--}}
{{--                                                     style="width: 98%" aria-valuenow="98" aria-valuemin="0"--}}
{{--                                                     aria-valuemax="100"></div>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="fa fa-paypal" style="font-size:24px"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="small text-muted">Last login</div>--}}
{{--                                            <strong>Last month</strong>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <div class="avatar">--}}
{{--                                                <img class="img-avatar" src="img/avatars/5.jpg"--}}
{{--                                                     alt="admin@bootstrapmaster.com">--}}
{{--                                                <span class="avatar-status badge-success"></span>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div>Agapetus Tadeáš</div>--}}
{{--                                            <div class="small text-muted">--}}
{{--                                                <span>New</span> | Registered: Jan 1, 2015--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="flag-icon flag-icon-es h4 mb-0" id="es" title="es"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="clearfix">--}}
{{--                                                <div class="float-left">--}}
{{--                                                    <strong>22%</strong>--}}
{{--                                                </div>--}}
{{--                                                <div class="float-right">--}}
{{--                                                    <small class="text-muted">Jun 11, 2015 - Jul 10, 2015--}}
{{--                                                    </small>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress progress-xs">--}}
{{--                                                <div class="progress-bar bg-info" role="progressbar"--}}
{{--                                                     style="width: 22%" aria-valuenow="22" aria-valuemin="0"--}}
{{--                                                     aria-valuemax="100"></div>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="fa fa-google-wallet" style="font-size:24px"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="small text-muted">Last login</div>--}}
{{--                                            <strong>Last week</strong>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <div class="avatar">--}}
{{--                                                <img class="img-avatar" src="img/avatars/6.jpg"--}}
{{--                                                     alt="admin@bootstrapmaster.com">--}}
{{--                                                <span class="avatar-status badge-danger"></span>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div>Friderik Dávid</div>--}}
{{--                                            <div class="small text-muted">--}}
{{--                                                <span>New</span> | Registered: Jan 1, 2015--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="flag-icon flag-icon-pl h4 mb-0" id="pl" title="pl"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="clearfix">--}}
{{--                                                <div class="float-left">--}}
{{--                                                    <strong>43%</strong>--}}
{{--                                                </div>--}}
{{--                                                <div class="float-right">--}}
{{--                                                    <small class="text-muted">Jun 11, 2015 - Jul 10, 2015--}}
{{--                                                    </small>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="progress progress-xs">--}}
{{--                                                <div class="progress-bar bg-success" role="progressbar"--}}
{{--                                                     style="width: 43%" aria-valuenow="43" aria-valuemin="0"--}}
{{--                                                     aria-valuemax="100"></div>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <i class="fa fa-cc-amex" style="font-size:24px"></i>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            <div class="small text-muted">Last login</div>--}}
{{--                                            <strong>Yesterday</strong>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    </tbody>--}}
{{--                                </table>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                </div>--}}

            </div>


        </div><!--col-->
    </div><!--row-->



@endsection


@section('before_scripts')
{{--{!! script(asset('js/dashboard.js')) !!}--}}
    <script>

         var app_signups_chart_data ={!! $app_signups_chart !!};
         var app_signedins_chart_data ={!! $app_signedins_chart !!};
         var app_first_time_connections_chart_data ={!! $app_first_time_connections_chart !!};
         var in_app_purchases_chart_data ={!! $in_app_purchases_chart !!};
         var sales_chart_data ={!! $sales_chart !!};
         var sales_free_premium_trial_chart_data ={!! $sales_free_premium_trial_chart !!};
         var emails_delivered_chart_data ={!! $emails_delivered_chart !!};
         var emails_not_delivered_chart_data ={!! $emails_not_delivered_chart !!};
         var subscriptions_chart_data ={!! $subscriptions_chart !!};

    </script>


@endsection
