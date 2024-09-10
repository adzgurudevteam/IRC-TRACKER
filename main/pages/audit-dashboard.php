<?php 
function printContent() {
    $total_no_of_audits=$total_audit_completed_per=$total_completed_audits=$total_pending_audits=0;
    $total_no_of_queries=$total_queries_submitted=$submitted_query_per=$pending_queries=0;
    $QchartScript=$noAudScript='';
    $noAudData=$noAudCategories=[];
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
    if ($_SESSION[USER_TYPE]==EMPLOYEE) {
        $getAuditData=getData(Table::AUDITS_DATA,[
            AUDITS_DATA::COMPANY_ID,
            AUDITS_DATA::ACTIVE,
        ],[
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDITS_DATA::STATUS=>ACTIVE_STATUS,
            AUDITS_DATA::USER_ID=>$_SESSION[RID]
        ],[AUDITS_DATA::COMPANY_ID=>$assignedCompanyIds]);
        $getQueryData=getData(Table::QUERY_DATA,[
            QUERY_DATA::QUERY_STATUS,
            QUERY_DATA::TOTAL_NO_OF_QUERY,
            QUERY_DATA::NO_OF_QUERY_SOLVED
        ],[
            QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            QUERY_DATA::USER_ID=>$_SESSION[RID]
        ],[QUERY_DATA::COMPANY_ID=>$assignedCompanyIds]);
    } else {
        $getAuditData=getData(Table::AUDITS_DATA,[
            AUDITS_DATA::COMPANY_ID,
            AUDITS_DATA::ACTIVE,
        ],[
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDITS_DATA::STATUS=>ACTIVE_STATUS
        ]);
        $getQueryData=getData(Table::QUERY_DATA,[
            QUERY_DATA::QUERY_STATUS,
            QUERY_DATA::TOTAL_NO_OF_QUERY,
            QUERY_DATA::NO_OF_QUERY_SOLVED
        ],[
            QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
    }
    if (count($getAuditData)>0) {
        $total_no_of_audits=count($getAuditData);
        foreach ($getAuditData as $adk => $adv) {
            if ($adv[AUDITS_DATA::ACTIVE] == 2) {
                $total_completed_audits++;
            }
            if ($adv[AUDITS_DATA::ACTIVE] == 1) {
                $total_pending_audits++;
            }
        }
        $total_audit_completed_per=(($total_completed_audits/$total_no_of_audits)*100);
    }
    if (count($getQueryData)>0) {
        // $total_no_of_queries = count($getQueryData);
        // rip($getQueryData);
        foreach ($getQueryData as $qdk => $qdv) {
            $total_no_of_queries += $qdv[QUERY_DATA::TOTAL_NO_OF_QUERY];
            // if (($qdv[QUERY_DATA::QUERY_STATUS] == 2) || ($qdv[QUERY_DATA::QUERY_STATUS] == 4)) {
            //     $total_queries_submitted++;
            // }
            $total_queries_submitted += $qdv[QUERY_DATA::NO_OF_QUERY_SOLVED];
            // if ($qdv[QUERY_DATA::QUERY_STATUS] == 1) {
            //     $pending_queries++;
            // }
        }
        $pending_queries = ($total_queries_submitted==0)?$total_no_of_queries:($total_no_of_queries-$total_queries_submitted);
        $submitted_query_per=floor(($total_queries_submitted/$total_no_of_queries)*100);
    }
    // $csql = "SELECT SUM(".QUERY_DATA::TOTAL_NO_OF_QUERY.") FROM ".Table::QUERY_DATA." WHERE ".QUERY_DATA::USER_ID." = ".$_SESSION[RID];
    // rip(getCustomData($csql));
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
                <h1 class="h3 mb-0 text-gray-800">Audit Dashboard</h1>
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
                <!-- Leave Applications (Recent) Card  -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total No of Audits</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$total_no_of_audits;?></div>
                                </div>
                                <div class="col-auto">
                                    <!-- <i class="fas fa-comments fa-2x text-gray-300"></i> -->
                                    <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Attendance Card start-->
                <div class="col-xl-3 col-lg-3 col-md-3 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Audit Completed
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?=floor($total_audit_completed_per)?>% (<?=$total_completed_audits?>)</div>
                                        </div>
                                        <div class="col">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: <?=floor($total_audit_completed_per)?>%" aria-valuenow="<?=floor($total_audit_completed_per)?>" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="text-xs font-weight-bold text-success text-uppercase" style="font-size: 11px;">
                                            Total Pending Audits</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$total_pending_audits;?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <!-- Attendance Card end-->
                
                <!-- Uploaded Payslip -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">No. of Queries Submitted
                                    </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?=$submitted_query_per?>% (<?=$total_queries_submitted;?>)</div>
                                        </div>
                                        <div class="col">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: <?=$submitted_query_per?>%" aria-valuenow="<?=$submitted_query_per?>" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="text-xs font-weight-bold text-success text-uppercase" style="font-size: 11px;">
                                            Total No. of Queries</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$total_no_of_queries?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Applications (Monthly) Card  -->
                <div class="col-xl-3 col-lg-3 col-md-3 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 14px;">
                                        No. of Queries Pending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"></i>&nbsp;<?=$pending_queries?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Active Employees -->
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
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                            <h6 class="m-0 font-weight-bold text-dark">Industry Wise Analysis</h6>
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
                            <div class="chart-area d-none">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                            <!-- <a href="" style="cursor: pointer;"><span class="badge badge-danger" style="vertical-align: super;">Click Here...</span></a> -->
                            <div class="row">
                                <!-- Industry type wise audit Card  -->
                                <div class="col-xl-12 col-md-12 mb-4">
                                    <div id="noAudChart"></div>
                                </div>
                                 <?php 
                                    $indtypeQsolUnsolHtml=$indTypeOptionsForTaxStat='';
                                    $indtypeQsolNo=$indtypeQUnsolNo=$totNoOfQuery=0;
                                    $indtypeQsolPer=$indtypeQUnsolPer=0;
                                    $getIndTypeData=getData(Table::COMPANY_INDUSTRY_TYPE,[
                                        COMPANY_INDUSTRY_TYPE::ID,
                                        COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
                                    ],[
                                        COMPANY_INDUSTRY_TYPE::STATUS=>ACTIVE_STATUS,
                                        COMPANY_INDUSTRY_TYPE::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                    ]);
                                    if (count($getIndTypeData)>0):
                                        foreach ($getIndTypeData as $cidk => $cidv):
                                            $indTypeOptionsForTaxStat.='<a class="dropdown-item" href="javascript:void(0);" onclick="getAuditDashIndWiseTaxReport('.$cidv[COMPANY_INDUSTRY_TYPE::ID].',\''.$cidv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE].'\')" id="tax_collection_indType_sel_'.$cidv[COMPANY_INDUSTRY_TYPE::ID].'">'.$cidv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE].'</a>';
                                            if ($_SESSION[USER_TYPE]==EMPLOYEE) {
                                                $getComIds = getData(Table::COMPANIES,[COMPANIES::ID],[
                                                    COMPANIES::INDUSTRY_TYPE_ID=>$cidv[COMPANY_INDUSTRY_TYPE::ID],
                                                    COMPANIES::STATUS=>ACTIVE_STATUS,
                                                    COMPANIES::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                ],[COMPANIES::ID=>$assignedCompanyIds]);
                                            } else {
                                                $getComIds = getData(Table::COMPANIES,[COMPANIES::ID],[
                                                    COMPANIES::INDUSTRY_TYPE_ID=>$cidv[COMPANY_INDUSTRY_TYPE::ID],
                                                    COMPANIES::STATUS=>ACTIVE_STATUS,
                                                    COMPANIES::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                ]);
                                            }
                                            $indComIds=[];
                                            $IndTotalNoAudits=0;
                                            if (count($getComIds)>0) {
                                                foreach ($getComIds as $gcidk => $gcidv) {
                                                   $indComIds[]=$gcidv[COMPANIES::ID];
                                                }
                                                $getIndComAuditData=getData(Table::AUDITS_DATA,[
                                                    AUDITS_DATA::ACTIVE
                                                ],[
                                                    AUDITS_DATA::STATUS=>ACTIVE_STATUS,
                                                    AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                ],[
                                                    AUDITS_DATA::COMPANY_ID=>$indComIds
                                                ]);
                                                $getIndComQueryData=getData(Table::QUERY_DATA,[
                                                    QUERY_DATA::QUERY_STATUS,
                                                    QUERY_DATA::TOTAL_NO_OF_QUERY,
                                                    QUERY_DATA::NO_OF_QUERY_SOLVED
                                                ],[
                                                    QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                ],[
                                                    QUERY_DATA::COMPANY_ID=>$indComIds
                                                ]);
                                                // rip($indComIds);
                                                if (count($getIndComAuditData)>0) {
                                                    foreach ($getIndComAuditData as $aaad => $aaav) {
                                                        if ($aaav[AUDITS_DATA::ACTIVE]==1) {
                                                            $IndTotalNoAudits++;
                                                        }

                                                    }
                                                }
                                                // rip($getIndComQueryData);
                                                if (count($getIndComQueryData)>0) {
                                                    foreach ($getIndComQueryData as $qqqd => $qqqv) {
                                                        // if ($qqqv[QUERY_DATA::QUERY_STATUS]==1) {
                                                            $totNoOfQuery += $qqqv[QUERY_DATA::TOTAL_NO_OF_QUERY];
                                                            // $indtypeQUnsolNo+= $qqqv[QUERY_DATA::NO_OF_QUERY_SOLVED];
                                                        // }
                                                        // if (($qqqv[QUERY_DATA::QUERY_STATUS]==2) || ($qqqv[QUERY_DATA::QUERY_STATUS]==4)) {
                                                            $indtypeQsolNo += $qqqv[QUERY_DATA::NO_OF_QUERY_SOLVED];
                                                        // }
                                                    }
                                                    $indtypeQUnsolNo = $totNoOfQuery-$indtypeQsolNo;
                                                    $indtypeQsolPer=floor((($indtypeQsolNo/$totNoOfQuery)*100));
                                                    $indtypeQUnsolPer=floor((($indtypeQUnsolNo/$totNoOfQuery)*100));
                                                } else {
                                                    $indtypeQsolNo=$indtypeQUnsolNo=$totNoOfQuery=$indtypeQsolPer=$indtypeQUnsolPer=0;
                                                }
                                            }
                                            // rip($indComIds);
                                            // echo "</ br>";
                                            $indtypeQsolUnsolHtml.='
                                            <div class="col-xl-6 col-md-6 mb-4">
                                                <div class="card border-left-warning shadow h-100 py-2">
                                                    <div class="card-body">
                                                        <div class="row no-gutters align-items-center cursor-pointer">
                                                            <div class="col mr-2">
                                                                <div class="text-xs font-weight-bold text-info mb-1" style="display:flex; justify-content:space-between;">
                                                                    <span>'.strtoupper($cidv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE]).'</span>
                                                                    <span class="text-dark">
                                                                        <small class="text-success"><strong>No. Query Solved: </strong>'.$indtypeQsolNo.'</small>
                                                                        </br>
                                                                        <small class="text-danger"><strong>No. Query Unsolved: </strong>'.$indtypeQUnsolNo.'</small>
                                                                    </span>
                                                                </div>
                                                                <div class="QsolvedUnsolvedChart" id="QsolvedUnsolvedChart_'.$cidv[COMPANY_INDUSTRY_TYPE::ID].'"></div>
                                                            </div>


                                                            <div class="col mr-2">
                                                                <!--<div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                                '.$cidv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE].'</div>-->
                                                                <h4 class="small font-weight-bold">Solved Queries <span
                                                                        class="float-right">'.$indtypeQsolPer.'% ('.$indtypeQsolNo.')</span></h4>
                                                                <div class="progress mb-4">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: '.$indtypeQsolPer.'%"
                                                                        aria-valuenow="'.$indtypeQsolPer.'" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <h4 class="small font-weight-bold">UnSolved Queries<span
                                                                        class="float-right">'.$indtypeQUnsolPer.'% ('.$indtypeQUnsolNo.')</span></h4>
                                                                <div class="progress mb-4">
                                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: '.$indtypeQUnsolPer.'%"
                                                                        aria-valuenow="'.$indtypeQUnsolPer.'" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                                    ?>
                                    
                                    <div class="col-xl-3 col-md-6 mb-4 d-none">
                                        <div class="card border-left-warning shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center cursor-pointer">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                        <?=$cidv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE]?></div>
                                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                        Total No of Audits</div>
                                                        <div class="h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$IndTotalNoAudits;?></div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <!-- <i class="fas fa-comments fa-2x text-gray-300"></i> -->
                                                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php
                                        $QchartScript.='
                                        //var options_'.$cidv[COMPANY_INDUSTRY_TYPE::ID].' = {
                                        //    chart: {
                                        //        type: "bar"
                                        //    },
                                        //    colors: ["#14A44D", "#dc3545"],
                                        //    series: [{
                                        //        name: "Queries",
                                        //        data: ['.$indtypeQsolPer.', '.$indtypeQUnsolPer.']
                                        //    }],
                                        //    xaxis: {
                                        //        categories: ["Solved(%)", "Unsolved(%)"]
                                        //    },
                                        //    yaxis: {
                                        //        title: {
                                        //        text: "Percentage"
                                        //        }
                                        //    }
                                        // }

                                        var colors = ["#15a934", "#e82700"];
                                        var options = {
                                            series: [{
                                            name: "Queries",
                                            data: ['.$indtypeQsolPer.', '.$indtypeQUnsolPer.']
                                            }],
                                            chart: {
                                            height: 350,
                                            type: "bar",
                                            events: {
                                                click: function(chart, w, e) {
                                                // console.log(chart, w, e)
                                                }
                                            }
                                            },
                                            colors: colors,
                                            plotOptions: {
                                            bar: {
                                                columnWidth: "45%",
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
                                            categories: ["Solved(%)", "Unsolved(%)"],
                                            labels: {
                                                style: {
                                                colors: colors,
                                                fontSize: "12px"
                                                }
                                            },
                                            title: {
                                                    text: "Percentage"
                                                }
                                            },
                                            yaxis: {
                                                title: {
                                                    text: "No. of Queries",
                                                }
                                            }
                                        };

                                        var options_'.$cidv[COMPANY_INDUSTRY_TYPE::ID].' = {
                                            series: ['.$indtypeQsolPer.', '.$indtypeQUnsolPer.'],
                                            chart: {
                                                width: 380,
                                                type: "donut",
                                            },
                                            labels: ["Solved(%)", "Unsolved(%)"],
                                            colors: colors,
                                            responsive: [{
                                                breakpoint: 480,
                                                options: {
                                                    chart: {
                                                        width: 200
                                                    },
                                                    legend: {
                                                        position: "bottom"
                                                    }
                                                }
                                            }]
                                        };

                                        var chart_'.$cidv[COMPANY_INDUSTRY_TYPE::ID].' = new ApexCharts(document.querySelector("#QsolvedUnsolvedChart_'.$cidv[COMPANY_INDUSTRY_TYPE::ID].'"), options_'.$cidv[COMPANY_INDUSTRY_TYPE::ID].');

                                        chart_'.$cidv[COMPANY_INDUSTRY_TYPE::ID].'.render();
                                        ';
                                        $noAudCategories[]=$cidv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE];
                                        $noAudData[]=$IndTotalNoAudits;
                                        endforeach;
                                    else:
                                    ?>
                                    <div class="col-xl-12 col-md-12 mb-4">
                                        <div class="alert alert-danger" role="alert">
                                            No Industry found !
                                        </div>
                                    </div>
                                    <?php
                                    $indTypeOptionsForTaxStat='<a class="dropdown-item" href="#" id="tax_collection_indType_sel_0">No Industry Found!</a>';
                                    $indtypeQsolUnsolHtml='
                                    <div class="col-xl-12 col-md-12 mb-4">
                                        <div class="alert alert-danger" role="alert">
                                            No Industry found !
                                        </div>
                                    </div>
                                    ';
                                    $QchartScript='';
                                    endif;
                                 ?>
                            </div>
                        </div>
                    </div>
                    <!-- Industry Wise Query Solved & Unsolved Status [start]-->
                    <div class="card shadow mb-4">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12">
                        <!-- Card Header - Dropdown -->
                        <div
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                            <h6 class="m-0 font-weight-bold text-primary" style="color: #1E1D1D;">Industry Wise Query Solved & Unsolved Status</h6>
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
                            <div class="chart-area d-none">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                            <!-- <a href="<?=HOST_URL?>employee-notices" style="cursor: pointer;"><span class="badge" style="vertical-align: super; background: #FF5637">Click Here...</span></a> -->
                            <div class="row">
                                <?=$indtypeQsolUnsolHtml;?>
                            </div>
                        </div>
                        </div>
                        
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                                    <h6 class="m-0 font-weight-bold text-primary" style="color: #1E1D1D;">Tax Claimed & Received Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-4">
                                        <!-- Project Card Example -->
                                        <div class="card shadow mb-4 w-40 p-3">
                                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                                                <h6 class="m-0 font-weight-bold text-primary" id="auditDashIndSelectText">Select an Industry</h6>
                                                <div class="dropdown no-arrow">
                                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                        aria-labelledby="dropdownMenuLink">
                                                        <div class="dropdown-header"><b>Select Industry:</b></div>
                                                        <?=$indTypeOptionsForTaxStat;?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <?=getSpinner(true,'audDashTaxReportLoader');?>
                                                <h4 class="small font-weight-bold"><span class="tax_report_heading">Tax Claimed</span><span
                                                        class="float-right tax_claimed_per_text">0%</span></h4>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar bg-warning tax_claimed_per_progress" role="progressbar" style="width: 0%"
                                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <h4 class="small font-weight-bold"><span class="tax_report_heading">Tax Recieved</span> <span
                                                        class="float-right tax_recieved_per_text">0%</span></h4>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar bg-success tax_recieved_per_progress" role="progressbar" style="width: 0%"
                                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <h4 class="small font-weight-bold"><span class="tax_report_heading">Penalty Claimed </span><span
                                                        class="float-right penalty_claimed_per_text">0%</span></h4>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar bg-danger penalty_claimed_per_progress" role="progressbar" style="width: 0%"
                                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <h4 class="small font-weight-bold"><span class="tax_report_heading">Penalty Recieved</span> <span
                                                        class="float-right penalty_recieved_per_text">0%</span></h4>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar bg-success penalty_recieved_per_progress" role="progressbar" style="width: 0%"
                                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    <!-- Industry Wise Query Solved & Unsolved Status [end]-->
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
 
<script type="text/javascript">
    <?=$QchartScript;?>
    // var options = {
    //     series: [{
    //     name: 'Total Number of Audits',
    //     data: <?=json_encode($noAudData);?>
    //     }],
    //     annotations: {
    //     points: [{
    //         x: 'Audits',
    //         seriesIndex: 0,
    //         label: {
    //         borderColor: '#775DD0',
    //         offsetY: 0,
    //         style: {
    //             color: '#fff',
    //             background: '#775DD0',
    //         },
    //         text: '',
    //         }
    //     }]
    //     },
    //     chart: {
    //     height: 350,
    //     type: 'bar',
    //     },
    //     plotOptions: {
    //     bar: {
    //         borderRadius: 10,
    //         columnWidth: '50%',
    //     }
    //     },
    //     dataLabels: {
    //     enabled: false
    //     },
    //     stroke: {
    //     width: 0
    //     },
    //     grid: {
    //     row: {
    //         colors: ['#fff', '#f2f2f2']
    //     }
    //     },
    //     xaxis: {
    //     labels: {
    //         rotate: -45
    //     },
    //     categories: <?=json_encode($noAudCategories);?>,
    //     tickPlacement: 'on'
    //     },
    //     yaxis: {
    //     title: {
    //         text: 'No. of Audits',
    //     },
    //     },
    //     fill: {
    //     type: 'gradient',
    //     gradient: {
    //         shade: 'light',
    //         type: "horizontal",
    //         shadeIntensity: 0.25,
    //         gradientToColors: undefined,
    //         inverseColors: true,
    //         opacityFrom: 0.85,
    //         opacityTo: 0.85,
    //         stops: [50, 0, 100]
    //     },
    // }
    // };
    var colors = ['#007bff','#28a745','#17a2b8','#ffc107','#dc3545'];
    var noAudChart_options = {
          series: [{
        name: 'Total Number of Audits',
          data: <?=json_encode($noAudData);?>
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
          categories:
            <?=json_encode($noAudCategories);?>,
          labels: {
            style: {
              colors: colors,
              fontSize: '12px'
            }
          },
          title: {
                text: 'Industry Types'
            }
        },
        yaxis: {
            title: {
                text: 'Total No. of Audits',
            }
        }
        };

    var noAudChart = new ApexCharts(document.querySelector("#noAudChart"), noAudChart_options);
    noAudChart.render();
</script>
<?php
}
?>