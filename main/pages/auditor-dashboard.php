<?php 
function printContent() {
    $totalAuditors=$totalAudits=$totalAuditsPending=$totalAuditActive=0;
    $audSelectOptions='<option value="0" disabled '.($_SESSION[USER_TYPE]!=EMPLOYEE ? 'selected' :'').'>---Select Auditor----</option>';
    $indSelectOptions='<option value="0" disabled selected>---Select Industry----</option>';
    $auditSelectOptions='<option value="0" disabled selected>---Select Audit----</option>';
    $assignedCompanyIds = [];
    if ($_SESSION[USER_TYPE]==EMPLOYEE) {
        $getAssignedCompanies = getData(Table::COMPANY_ASSIGNED_DATA, [
            COMPANY_ASSIGNED_DATA::COMPANY_IDS,
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
        ], [
            COMPANY_ASSIGNED_DATA::AUDITOR_ID => $_SESSION[RID],
            COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS
        ]);
        if (count($getAssignedCompanies) > 0) {
            foreach ($getAssignedCompanies as $ack => $acv) {
                $assignedCompanyIds[] = $acv[COMPANY_ASSIGNED_DATA::COMPANY_IDS];
            }
            $assignedCompanyIds = array_unique($assignedCompanyIds);
        }
    }
    $getAuditorData=getData(Table::USERS,[USERS::NAME,USERS::ID,USERS::EMPLOYEE_ID],[USERS::ACTIVE=>1,USERS::STATUS=>ACTIVE_STATUS,USERS::USER_TYPE=>EMPLOYEE,USERS::CLIENT_ID=>$_SESSION[CLIENT_ID]]);
    $getAuditData=getData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE,AUDITS_DATA::COMPANY_ID],[AUDITS_DATA::STATUS=>ACTIVE_STATUS,AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]]);
    if ($_SESSION[USER_TYPE]==EMPLOYEE) {
        $getAuditData=getData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE,AUDITS_DATA::COMPANY_ID],[AUDITS_DATA::USER_ID=>$_SESSION[RID], AUDITS_DATA::STATUS=>ACTIVE_STATUS,AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]],[AUDITS_DATA::COMPANY_ID=>$assignedCompanyIds]);
    }
    $getIndTypeData=getData(Table::COMPANY_INDUSTRY_TYPE,[
        COMPANY_INDUSTRY_TYPE::ID,
        COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
    ],[
        COMPANY_INDUSTRY_TYPE::STATUS=>ACTIVE_STATUS,
        COMPANY_INDUSTRY_TYPE::CLIENT_ID=>$_SESSION[CLIENT_ID]
    ]);
    // $totalAuditors=count($getAuditorData);
    // rip($assignedCompanyIds);
    // echo "<br> User ID: ".$_SESSION[RID];
    $totalAudits=($_SESSION[USER_TYPE]==EMPLOYEE)?count($assignedCompanyIds):count($getAuditData);
    if (count($getAuditData)>0) {
        foreach ($getAuditData as $audk => $audv) {
            $getComName=getData(Table::COMPANIES,[COMPANIES::COMPANY_NAME],[COMPANIES::ID=>$audv[AUDITS_DATA::COMPANY_ID]]);
            $auditSelectOptions.='<option value="'.$audv[AUDITS_DATA::COMPANY_ID].'">'.$getComName[0][COMPANIES::COMPANY_NAME].'</option>';
            if ($audv[AUDITS_DATA::ACTIVE]==1) {
                $totalAuditsPending++;
            } else {
                if ($audv[AUDITS_DATA::ACTIVE]==2) {
                    $totalAuditActive++;
                }
            }
        }
    }
    if (count($getAuditorData)>0) {
        foreach ($getAuditorData as $auk => $auv) {
            $AudSel=($_SESSION[USER_TYPE]==EMPLOYEE && $_SESSION[RID] == $auv[USERS::ID]) ? 'selected' : '';
            $audSelectOptions.='<option '.$AudSel.' value="'.$auv[USERS::ID].'">'.$auv[USERS::NAME].'</option>';
            $getEmpData=getData(Table::EMPLOYEE_DETAILS,[EMPLOYEE_DETAILS::ACTIVE],[EMPLOYEE_DETAILS::STATUS=>ACTIVE_STATUS,EMPLOYEE_DETAILS::CLIENT_ID=>$_SESSION[CLIENT_ID],EMPLOYEE_DETAILS::ID=>$auv[USERS::EMPLOYEE_ID]]);
            if (count($getEmpData)>0) {
                $totalAuditors++;
            }
        }
    }
    if (count($getIndTypeData)>0) {
        foreach ($getIndTypeData as $indk => $indv) {
            $indSelectOptions.='<option value="'.$indv[COMPANY_INDUSTRY_TYPE::ID].'">'.$indv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE].'</option>';
        }
    }
?>
<!-- Page Wrapper -->
<div id="wrapper">

        
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column mr-0">

    <!-- Main Content -->
    <div id="content mt-0"> 
        
        <!-- Begin Page Content -->
        <div class="container-fluid pr-1">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Auditor Dashboard</h1>
                <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                        class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> -->
            </div>
        <?php if(getToday(false) == SOFTWARE_UPDATE_DATE): ?>

        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Check it out! Software has been <b>updated.</b>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>
            <!-- Content Row -->
            <div class="row">
                <!-- Total No. of Auditors Card  -->
                <div class="col-xl-4 col-lg-4 col-md-4 mb-4 <?php if($_SESSION[USER_TYPE]==EMPLOYEE):?>d-none<?php endif; ?>">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total No. of Auditors</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$totalAuditors;?></div>
                                </div>
                                <div class="col-auto">
                                    <!-- <i class="fas fa-comments fa-2x text-gray-300"></i> -->
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total No. of Audits Card  -->
                <div class="col-xl-4 col-lg-4 col-md-4 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2" style="cursor: pointer;">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total No. of Audits</div>
                                    <div class="dashboard_chat_count h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$totalAudits;?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                    <!-- <i class="fas fa-calendar-day fa-2x text-gray-300"></i> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Audits Pending Card  -->
                <div class="col-xl-4 col-lg-4 col-md-4 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 14px;">
                                    Total Audits Pending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"></i>&nbsp;<?=$totalAuditsPending;?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 mb-4">
                    <div id="auditorDashAuditorChart"></div>
                </div>
                <!-- Active Auditors -->
                <!-- <div class="col-xl-3 col-lg-3 col-md-3 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Active Auditors</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 11px;">
                                        Total Auditors</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Cards end -->

            <!-- Content Row -->
            <div class="row">
                <!-- hidden cards start here -->
                <!-- Content Column -->
                <div class="col-lg-4 col-md-4 col-sm-12 mb-4 d-none">

                    <!-- Project Card Example -->
                    <div class="card shadow mb-4 w-40 p-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Projects</h6>
                        </div>
                        <div class="card-body">
                            <h4 class="small font-weight-bold">Server Migration <span
                                    class="float-right">20%</span></h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 20%"
                                    aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <h4 class="small font-weight-bold">Sales Tracking <span
                                    class="float-right">40%</span></h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                                    aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <h4 class="small font-weight-bold">Customer Database <span
                                    class="float-right">60%</span></h4>
                            <div class="progress mb-4">
                                <div class="progress-bar" role="progressbar" style="width: 60%"
                                    aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <h4 class="small font-weight-bold">Payout Details <span
                                    class="float-right">80%</span></h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                                    aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <h4 class="small font-weight-bold">Account Setup <span
                                    class="float-right">Complete!</span></h4>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4 d-none">

                    <!-- Color System -->
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-primary text-white shadow">
                                <div class="card-body">
                                    Primary
                                    <div class="text-white-50 small">#4e73df</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-success text-white shadow">
                                <div class="card-body">
                                    Success
                                    <div class="text-white-50 small">#1cc88a</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-info text-white shadow">
                                <div class="card-body">
                                    Info
                                    <div class="text-white-50 small">#36b9cc</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-warning text-white shadow">
                                <div class="card-body">
                                    Warning
                                    <div class="text-white-50 small">#f6c23e</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-danger text-white shadow">
                                <div class="card-body">
                                    Danger
                                    <div class="text-white-50 small">#e74a3b</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-secondary text-white shadow">
                                <div class="card-body">
                                    Secondary
                                    <div class="text-white-50 small">#858796</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-light text-black shadow">
                                <div class="card-body">
                                    Light
                                    <div class="text-black-50 small">#f8f9fc</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-dark text-white shadow">
                                <div class="card-body">
                                    Dark
                                    <div class="text-white-50 small">#5a5c69</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- hidden cards end here -->
                 <!-- Industry & Auditor Select [Start] -->
                 <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div
                                class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                                <h6 class="m-0 font-weight-bold text-primary">Industry & Auditor Select</h6>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="auditorDashIndSelect" class="form_label">Select Industry</label>
                                        <select id="auditorDashIndSelect" class="form-control auditorDashAudIndSelect">
                                            <?=$indSelectOptions;?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 <?php if($_SESSION[USER_TYPE]==EMPLOYEE):?>d-none<?php endif; ?>">
                                        <label for="auditorDashAuditorSelect" class="form_label">Select Auditor</label>
                                        <select id="auditorDashAuditorSelect" class="form-control auditorDashAudIndSelect">
                                            <?=$audSelectOptions;?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                 <!-- Industry & Auditor Select [end] -->
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                            <h6 class="m-0 font-weight-bold text-dark">Industry Type wise-Auditor Wise Audit Status</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                    aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Dropdown Header:</div>
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <?=getSpinner(true,'auditStatusLoader');?>
                            <!-- <a href="" style="cursor: pointer;"><span class="badge badge-danger" style="vertical-align: super;">Click Here...</span></a> -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-hover table-bordered text-center" style="font-size:12px;" id="auditorDashAuditorTable">
                                            <thead class="text-center table-warning">
                                                <tr style="text-transform: uppercase; font-size:12px;">
                                                    <th>Auditor</th>
                                                    <th>Audits Count(No.)</th>
                                                </tr>
                                            </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4">
                                                            <div class="alert alert-danger" role="alert">
                                                                Please select the above options to generate data !
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="indwiseAudWiseAuditChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                            <h6 class="m-0 font-weight-bold text-dark" style="color: #1E1D1D;">Auditor wise Query Resolved-Pending status</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                    aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Dropdown Header:</div>
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                        <?=getSpinner(true,'queryStatusLoader');?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover table-bordered text-center" style="font-size:12px;" id="auditorDashQueryTable">
                                        <thead class="text-center table-warning">
                                            <tr style="text-transform: uppercase; font-size:12px;">
                                                <th>Auditor</th>
                                                <th>Total Query</th>
                                            </tr>
                                        </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="alert alert-danger" role="alert">
                                                            Please select the above options to generate data !
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="indwiseAudWiseQueryChart"></div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                            <h6 class="m-0 font-weight-bold text-dark" style="color: #1E1D1D;">Auditor wise Notice Issued-Pending status</h6>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                        <?=getSpinner(true,'NoticeStatusLoader');?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover table-bordered text-center" style="font-size:12px;" id="auditorDashNoticeTable">
                                        <thead class="text-center table-warning">
                                            <tr style="text-transform: uppercase; font-size:12px;">
                                                <th>Auditor</th>
                                                <th>Total Notice</th>
                                            </tr>
                                        </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="alert alert-danger" role="alert">
                                                            Please select the above options to generate data !
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="indwiseAudWiseNoticeChart"></div>
                            </div>
                        </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">Audit Select to view Time Spent Analysis</h6>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="row">
                                <?=getSpinner(true,"AuditorTimeSpentLoader");?>
                                <div class="col-md-6">
                                    <label for="auditorDashAudSelect" class="form_label">Select Audit</label>
                                    <select id="auditorDashAudSelect" class="form-control">
                                        <?=$auditSelectOptions;?>
                                    </select>
                                </div>
                                <div class="col-md-6" id="auditorInfoSec" style="display: none;">

                                </div>
                                <div class="col-md-12">
                                    <div id="AuditorTimeSpentChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <footer class="sticky-footer bg-white" style="position: fixed; bottom:0; padding-bottom:10px; width:100vw;">
        <div class="container my-auto">
            <div class="copyright text-left my-auto">
                <span><?=getToday(false,'Y')?> &copy; Copyright <?=COMPANY_BUSINESS_NAME?> </span>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->


<!-- Delete modal -->
<div class="modal animated shake" id="earlyLoggOffReason_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="staticBackdropLabel">Reason Behind the Early Log Off</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('.modal').hide();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var colors = ['#007bff','#28a745','#ffc107'],
    colors2 = ['#4acb68', '#ffc107'];
     var options = {
          series: [{
        name: 'Total Number',
          data: [<?=$totalAuditors;?>,<?=$totalAudits?>,<?=$totalAuditsPending?>]
        }],
          chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(chart, w, e) {
              // console.log(chart, w, e)
            }
          }
        },
        colors: colors,
        plotOptions: {
          bar: {
            columnWidth: '45%',
            distributed: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        legend: {
          show: false
        },
        xaxis: {
          categories: [
            // ['John', 'Doe'],
            // 'Amber',
            // ['David', 'Wilson'],
            ['Total No. of', 'Auditors'],
            ['Total No. of', 'Audits'],
            ['Total', 'Audits Pending']
          ],
          labels: {
            style: {
              colors: colors,
              fontSize: '12px'
            }
          },
          title: {
                text: 'Auditors & Audits'
            }
        },
        yaxis: {
            title: {
                text: 'Total Number',
            }
        }
    };
    var auditorDashAuditorChart = new ApexCharts(document.querySelector("#auditorDashAuditorChart"), options);
    auditorDashAuditorChart.render();
    var aud_options = {
            series: [{
                name: 'Total Number',
                data: [0, 0]
            }],
            chart: {
                height: 350,
                type: 'bar'
            },
            colors: colors2,
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    distributed: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            xaxis: {
                categories: [
                    ['Completed', 'Audits'],
                    ['Pending', 'Audits']
                ],
                labels: {
                    style: {
                        colors: colors2,
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Audit Status'
                }
            },
            yaxis: {
                title: {
                    text: 'Total Number'
                }
            }
        };
        var query_options = {
            series: [{
                name: 'Total Number',
                data: [0, 0]
            }],
            chart: {
                height: 350,
                type: 'bar'
            },
            colors: colors,
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    distributed: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            xaxis: {
                categories: [
                    ['Resolved', 'Queries'],
                    ['Pending', 'Queries']
                ],
                labels: {
                    style: {
                        colors: colors,
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Query Status'
                }
            },
            yaxis: {
                title: {
                    text: 'Total Number'
                }
            }
        };
        var auditor_time_options = {
            series: [{
                name: 'Total Hour(s)',
                data: [0, 0]
            }],
            chart: {
                height: 350,
                type: 'bar'
            },
            colors: colors,
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    distributed: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            xaxis: {
                categories: [
                    ['Primary', 'Auditors'],
                    ['Secondary', 'Auditors']
                ],
                labels: {
                    style: {
                        colors: colors,
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Auditors Name'
                }
            },
            yaxis: {
                title: {
                    text: 'Total Hour(s)'
                }
            }
        };
        var indwiseAudWiseAuditChart = new ApexCharts(document.querySelector("#indwiseAudWiseAuditChart"), aud_options);
        indwiseAudWiseAuditChart.render();
        var indwiseAudWiseQueryChart = new ApexCharts(document.querySelector("#indwiseAudWiseQueryChart"), query_options);
        indwiseAudWiseQueryChart.render();
        var AuditorTimeSpentChart = new ApexCharts(document.querySelector("#AuditorTimeSpentChart"), auditor_time_options);
        AuditorTimeSpentChart.render();
</script>
<?php
}
?>