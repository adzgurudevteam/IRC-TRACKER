<?php 
function printContent() {
    $taxTypeOptions='<option value="0" disabled selected>---Select Tax Type----</option>';
    $CompanySelectOptions='<option value="0" disabled selected>---Select Company----</option>';
    $taxTypeWiseQueryData=$taxTypeWiseQueryCategories=[];
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
    $getCompanyData=getData(Table::COMPANIES,[
        COMPANIES::COMPANY_NAME,
        COMPANIES::ID,
        COMPANIES::TAX_IDENTIFICATION_NUMBER
    ],[
        COMPANIES::STATUS=>ACTIVE_STATUS,
        COMPANIES::CLIENT_ID=>$_SESSION[CLIENT_ID]
    ],($_SESSION[USER_TYPE]==EMPLOYEE)?[COMPANIES::ID=>$assignedCompanyIds]:[]);
    if (count($getCompanyData)>0) {
        foreach ($getCompanyData as $cdk => $cdv) {
            $name = $cdv[COMPANIES::COMPANY_NAME];
            $name .= ($cdv[COMPANIES::TAX_IDENTIFICATION_NUMBER]!="")?'&nbsp;(<strong>TIN: </strong>'.$cdv[COMPANIES::TAX_IDENTIFICATION_NUMBER].')':'';
            $CompanySelectOptions.='<option value="'.$cdv[COMPANIES::ID].'">'.$name.'</option>';
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
                <h1 class="h3 mb-0 text-gray-800">Tax Payer Dashboard</h1>
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
                <div class="col-md-12">
                <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-light">
                            <h6 class="m-0 font-weight-bold text-primary" style="color: #1E1D1D;">Tax Type Wise Analysis</h6>
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
                                <div class="col-xl-12 col-lg-12 col-md-12 mb-4">
                                    <div id="taxTypeWiseQueryChart"></div>
                                </div>
                            <?php 
                                $totNoQuery=0;
                                $getTaxTypeData=getData(Table::TYPE_OF_TAX,[
                                    TYPE_OF_TAX::TYPE_OF_TAX,
                                    TYPE_OF_TAX::ID
                                ],[
                                    TYPE_OF_TAX::STATUS=>ACTIVE_STATUS,
                                    TYPE_OF_TAX::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                ]);
                                if(count($getTaxTypeData)>0):
                                    foreach ($getTaxTypeData as $ttk => $ttv):
                                        $taxTypeOptions.='<option value="'.$ttv[TYPE_OF_TAX::ID].'">'.$ttv[TYPE_OF_TAX::TYPE_OF_TAX].'</option>';
                                        $getTTQueryData=getData(Table::QUERY_DATA,[
                                            QUERY_DATA::QUERY_STATUS,
                                            QUERY_DATA::ID
                                        ],[
                                            QUERY_DATA::TAX_TYPE_ID => $ttv[TYPE_OF_TAX::ID],
                                            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                                        ],($_SESSION[USER_TYPE]==EMPLOYEE)?[QUERY_DATA::ID=>$assignedCompanyIds]:[]);
                                        $totNoQuery = count($getTTQueryData);
                                        $taxTypeWiseQueryData[]=$totNoQuery;
                                        $taxTypeWiseQueryCategories[]=$ttv[TYPE_OF_TAX::TYPE_OF_TAX];
                            ?>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2" style="cursor: pointer;">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    <?=$ttv[TYPE_OF_TAX::TYPE_OF_TAX]?></div>
                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Total No. of Query</div>
                                                <div class="dashboard_chat_count h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$totNoQuery;?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-comments fa-2x text-gray-300"></i>
                                                <!-- <i class="fas fa-calendar-day fa-2x text-gray-300"></i> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            endforeach;
                                else:
                            ?>
                            <div class="col-xl-12 col-md-12 mb-4">
                                <div class="alert alert-danger" role="alert">
                                    No Tax Type found !
                                </div>
                            </div>
                            <?php 
                                endif;
                            ?>
                            </div>
                        </div>
                    </div>
                </div>
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
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-danger">Tax type wise Tax Payer Standing</h6>
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
                                <div class="col-md-6">
                                    <label for="com_dash_com_select" class="form_label">Select Tax Payer</label><?=getAsterics();?>
                                    <select class="form-control" id="com_dash_com_select" onchange="getCompanyDashTaxData();">
                                        <?=$CompanySelectOptions;?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="com_dash_tax_type_select" class="form_label">Select Tax Type</label><?=getAsterics();?>
                                    <select class="form-control" id="com_dash_tax_type_select" onchange="getCompanyDashTaxData();">
                                        <?=$taxTypeOptions;?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2" id="company_status_table_section">
                                <?=getSpinner(true, 'company_status_table_section_loader');?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover table-bordered text-center company_status_table" style="font-size:12px;">
                                        <thead class="text-center table-warning">
                                            <tr style="text-transform: uppercase; font-size:12px;">
                                                <th>Tax Target Claimed</th>
                                                <th>Tax Target Achieved</th>
                                                <th>Total Queries</th>
                                                <th>Q Solved</th>
                                                <th>Q Unsolved</th>
                                                <th>Notice Issued</th>
                                                <th>Notice Pending</th>
                                                <th>Position Paper issued</th>
                                                <th>Position Paper pending</th>
                                                <th>Assessment issued</th>
                                                <th>Assessment pending</th>
                                            </tr>
                                        </thead>
                                            <tbody style="cursor: pointer;">
                                                <tr class="animated fadeInDown">
                                                    <td colspan="11">
                                                        <div class="alert alert-danger" role="alert">
                                                            Please select the above options to generate data!
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Tax Type wise Company's Audit Pending Ageing [start]-->
                             <div class="row mt-2" id="companyTaxWiseStatsSection">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="sub_heading" style="font-size: 14px;">Company's Audit Pending Ageing</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="taxtypeWiseComAgingChart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card" id="taxPenaltyStatCard">
                                        <div class="card-header">
                                            <h6 class="sub_heading" style="font-size: 14px;">Tax Claimed-Achieved status</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="taxPenaltyStatChart"></div>
                                        </div>
                                        <div class="card-footer"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="sub_heading" style="font-size: 14px;">Query Solved-Unsolved status</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="qsolvedUnsolvedComChart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="sub_heading" style="font-size: 14px;">Notice Issued-pending status</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="noticeIssuedPendingComChart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="sub_heading" style="font-size: 14px;">Position Paper Issued-pending status</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="ppIssuedPendingComChart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="sub_heading" style="font-size: 14px;">Assessment Issued-pending status</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="asmtIssuedPendingComChart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tax Type wise Company's Audit Pending Ageing [end]-->
                        </div>
                    </div>
                    <div class="card shadow mb-4 d-none">
                        <!-- Card Header - Dropdown -->
                        <div
                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold" style="color: #1E1D1D;">Position Paper Issued & Pending Status</h6>
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
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover text-center" style="font-size:12px;">
                                    <thead class="text-center table-warning">
                                        <tr style="text-transform: uppercase; font-size:12px;">
                                            <th>SL.</th>
                                            <th>Subject</th>
                                            <th>File</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                        <tbody style="cursor: pointer;">
                                        
                                        </tbody>
                                </table>
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

<script type="text/javascript">
     var colors = ['#007bff','#28a745','#17a2b8','#ffc107','#dc3545'];
     var options = {
          series: [{
        name: 'Total Number of Queries',
          data: <?=json_encode($taxTypeWiseQueryData);?>
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
            // ['John', 'Doe'],
            // ['Joe', 'Smith'],
            // ['Jake', 'Williams'],
            // 'Amber',
            // ['Peter', 'Brown'],
            // ['Mary', 'Evans'],
            // ['David', 'Wilson'],
            // ['Lily', 'Roberts'], 
            <?=json_encode($taxTypeWiseQueryCategories);?>,
          labels: {
            style: {
              colors: colors,
              fontSize: '12px'
            }
          },
          title: {
                text: 'Type of Tax'
            }
        },
        yaxis: {
            title: {
                text: 'Total No. of Queries',
            }
        }
    };

    var taxTypeWiseQueryChart = new ApexCharts(document.querySelector("#taxTypeWiseQueryChart"), options);
    taxTypeWiseQueryChart.render();
</script>
<?php
}
?>