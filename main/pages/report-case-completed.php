<?php 
function printContent()
{
    $oldestYear = 0;
    $year = $yrExt= 5;
    $yearOptions='<option value="0" selected disabled>----Select Year----</option>';
    $audSelectOptions='<option value="0" disabled selected>---Select Auditor----</option>';
    $getAuditorData=getData(Table::USERS,[USERS::NAME,USERS::ID,USERS::EMPLOYEE_ID],[USERS::ACTIVE=>1,USERS::STATUS=>ACTIVE_STATUS,USERS::USER_TYPE=>EMPLOYEE,USERS::CLIENT_ID=>$_SESSION[CLIENT_ID]]);
    $getoldestYear = getData(Table::AUDITS_DATA,[
        "YEAR(".AUDITS_DATA::CREATED_AT.") as yr"
    ],[
        AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
    ],[],[],[AUDITS_DATA::CREATED_AT],"ASC",[0,1]);
    if (count($getoldestYear)>0) {
        $oldestYear = $getoldestYear[0]["yr"];
    }
    if ($oldestYear!==0) {
        $yrExt = ((getToday(false,"Y")-$oldestYear)+5);
    }
    for ($i=1; $i <= $yrExt; $i++) :
        if ($i==1) {
            if ($oldestYear!==0) {
                $year = $oldestYear;
            } else {
                $year = (getToday(false, 'Y')-2);
            }
        } else {
            $year++;
        }

        $yearOptions.='<option value="'.$year.'">'.$year.'</option>';
    endfor;
    if (count($getAuditorData)>0) {
        foreach ($getAuditorData as $auk => $auv) {
            $audSelectOptions.='<option value="'.$auv[USERS::ID].'">'.$auv[USERS::NAME].'</option>';
        }
    }
?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 text-center">
        <h4 class="text-center main_heading">Case Completed</h4>
        <h6 class="text-center sub_heading">Detailed Completed Case Report Here</h6>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
    <?=getSpinner(true, 'casesCompletedLoader');?>
        <div class="row">
            <div class="col-md-4">
                <label class="form_label" for="casesCompletedAuditorSelect">Select Auditor</label><?=getAsterics();?>
                <select class="form-control casesCompletedCritSelect" id="casesCompletedAuditorSelect">
                    <?=$audSelectOptions;?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form_label" for="casesCompletedMonthSelect">Select Month</label><?=getAsterics();?>
                <select class="form-control casesCompletedCritSelect" id="casesCompletedMonthSelect">
                    <option value="0" selected disabled>----Select Month----</option>
                    <?=ALL_MONTHS;?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form_label" for="casesCompletedYearSelect">Select Year</label><?=getAsterics();?>
                <select class="form-control casesCompletedCritSelect" id="casesCompletedYearSelect">
                    <?=$yearOptions;?>
                </select>
            </div>
        </div>
        <div class="row mt-3" id="casesCompletedViewSection">
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-sm btn-info no-print" style="display: none;" onclick="printDiv('casesCompletedViewSection')"><small><i class="fas fa-print"></i>&nbsp;Print</small></button>
            </div>
            <div class="col-md-12">
            <h6 class="text-center main_heading">MONTHLY REPORT OF COMPLETED CASES</h6>
                <div class="table-responsive mt-3 parentTableDiv">
                    <table class="table table-sm table-striped table-hover table-bordered text-center" id="casesCompletedTable">
                        <thead class="text-center table-warning">
                            <tr>
                                <th>sl.</th>
                                <th>Taxpayer</th>
                                <th>case code</th>
                                <th>date allocated</th>
                                <th>date commence</th>
                                <th>date completed</th>
                                <th>TOT TIME (HRS)</th>
                                <th>OMITTED INCOME</th>
                                <th>tax avoided</th>
                                <th>PENALTY</th>
                                <th>TOTAL T & P</th>
                                <th>YEARS UNDER AUDIT</th>
                                <th>TYPE OF ASSMT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="animated fadeInDown">
                                <td colspan="13">
                                    <div class="alert alert-success" role="alert">
                                        Fetching ...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <table class="table table-sm table-striped table-hover table-bordered text-center" id="casesCompletedTotal"></table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-striped table-hover table-bordered text-center" id="auditHoursTotal"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>