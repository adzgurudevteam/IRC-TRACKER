<?php
function printContent()
{
    $com_options = '<option value="0" disabled selected>---Select Tax Payer----</option>';
    $table_tr=$objectionAlert='';
    $comInclause=$comids=[];
    if ($_SESSION[USER_TYPE]==EMPLOYEE) {
        $getAssignedComs=getData(Table::COMPANY_ASSIGNED_DATA,[COMPANY_ASSIGNED_DATA::COMPANY_IDS],[
            COMPANY_ASSIGNED_DATA::AUDITOR_ID=>$_SESSION[RID],
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY=>1,
            COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS
        ]);
        if (count($getAssignedComs)>0) {
            foreach ($getAssignedComs as $cidk => $cidv) {
                $comids[]=$cidv[COMPANY_ASSIGNED_DATA::COMPANY_IDS];
            }
            $comInclause=array_unique($comids);
        }
    }
    $getCompData = getData(Table::COMPANIES, ['*'], [
        COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
        COMPANIES::STATUS => ACTIVE_STATUS
    ],(count($comInclause)>0)?[COMPANIES::ID=>$comInclause]:[]);
    if (count($getCompData)>0) {
        $sl=1;
        foreach ($getCompData as $k => $v) {
            $getAssessmentData=getData(Table::AUDIT_ASSESSMENT_DATA,['*'],[
                AUDIT_ASSESSMENT_DATA::COMPANY_ID=>$v[COMPANIES::ID]
            ]);
            if (count($getAssessmentData)>0) {
                $name=altRealEscape($v[COMPANIES::COMPANY_NAME]);
                $name.=($v[COMPANIES::TAX_IDENTIFICATION_NUMBER] != "")?"(TIN: ".altRealEscape($v[COMPANIES::TAX_IDENTIFICATION_NUMBER]).")":"";
                $com_options.= '<option value="'.$v[COMPANIES::ID].'">'.$name.'</option>';
                $auditor=$assRefNo=$query_no=$date_of_issue=$days_count=$payment_date=$penaltyPayDate=$status=$tax_type=EMPTY_VALUE;
                $amount=$paidAmt=$pendingAmt=$penaltyAmount=$penaltyPaidAmt=$penaltyPendingAmt=DEFAULT_AMOUNT;
                $tax_typeArr=[];
                $getTaxTypeDataQuery="
                SELECT tt.".
                TYPE_OF_TAX::TYPE_OF_TAX ." FROM ".
                Table::TYPE_OF_TAX." AS tt INNER JOIN ".
                Table::QUERY_DATA." AS qdata ON (tt.".
                TYPE_OF_TAX::ID." = qdata.".
                QUERY_DATA::TAX_TYPE_ID.") LEFT JOIN ".
                Table::POSITION_PAPER_DATA." AS ppdata ON (qdata.".
                QUERY_DATA::ID." = ppdata.".
                POSITION_PAPER_DATA::QUERY_ID.") LEFT JOIN ".
                Table::AUDIT_ASSESSMENT_DATA." AS asmt ON (ppdata.".
                POSITION_PAPER_DATA::POSITION_PAPER_ID." = asmt.".
                AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID.") WHERE ppdata.".
                POSITION_PAPER_DATA::POSITION_PAPER_ID." = ".
                $getAssessmentData[0][AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID]." AND tt.".
                TYPE_OF_TAX::CLIENT_ID." = ".
                $_SESSION[CLIENT_ID]." AND ppdata.".
                POSITION_PAPER_DATA::QUERY_ID." = qdata.".QUERY_DATA::ID." GROUP BY tt.".
                TYPE_OF_TAX::TYPE_OF_TAX." ORDER BY qdata.".
                QUERY_DATA::ID." DESC;
                ";
                $getPosData=getData(Table::POSITION_PAPERS,[
                    POSITION_PAPERS::REFERENCE_NO,
                    POSITION_PAPERS::DATE_OF_ISSUE,
                    POSITION_PAPERS::OPEN_CLOSE_STATUS
                ],[
                    POSITION_PAPERS::ID=>$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID]
                ]);
                if (count($getPosData)>0) {
                    $posRefNo='<b>'.$getPosData[0][POSITION_PAPERS::REFERENCE_NO].'</b> <small>(Issued on: '.getFormattedDateTime($getPosData[0][POSITION_PAPERS::DATE_OF_ISSUE]).')</small>';
                    // $posRefNo.=($getPosData[0][POSITION_PAPERS::OPEN_CLOSE_STATUS]==1)?'&nbsp;<span class="badge alert-success">Open</span>':'&nbsp;<span class="badge alert-danger">Closed</span>';

                }
                $getTaxTypeData=getCustomData($getTaxTypeDataQuery);
                if (count($getTaxTypeData)>0) {
                    foreach ($getTaxTypeData as $ttqk => $ttqv) {
                        if ($ttqv[TYPE_OF_TAX::TYPE_OF_TAX]!="") {
                            $tax_typeArr[]=$ttqv[TYPE_OF_TAX::TYPE_OF_TAX];
                        }
                    }
                    $tax_type=(count($tax_typeArr)>0)?implode(', ',$tax_typeArr):$tax_type;
                }
                $getAud=getData(Table::COMPANY_ASSIGNED_DATA,[COMPANY_ASSIGNED_DATA::AUDITOR_ID,COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY],[
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$v[COMPANIES::ID],
                    COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
                    COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS
                ]);
                $getTaxPayData=getData(Table::TAX_COLLECTION_DATA,[
                    TAX_COLLECTION_DATA::TAX_AMOUNT,
                    TAX_COLLECTION_DATA::PAID_AMOUNT,
                    TAX_COLLECTION_DATA::PENDING_AMOUNT,
                    TAX_COLLECTION_DATA::LAST_PAYMENT_DATE,
                    TAX_COLLECTION_DATA::PENALTY_AMOUNT,
                    TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT,
                    TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT,
                    TAX_COLLECTION_DATA::PENALTY_LAST_PAYMENT_DATE,
                    TAX_COLLECTION_DATA::PAYMENT_STATUS,
                    TAX_COLLECTION_DATA::ID
                ],[
                    TAX_COLLECTION_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
                    TAX_COLLECTION_DATA::COMPANY_ID=>$v[COMPANIES::ID],
                    TAX_COLLECTION_DATA::ASSESSMENT_ID=>$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::ID]
                ]);
                $payment_date=$penaltyPayDate='<span class="badge badge-warning"><small>Not Paid</small></span>';
                $status='<span class="badge badge-danger"><small>Due</small></span>';
                if (count($getTaxPayData)>0) {
                    $taxData=$getTaxPayData[0];
                    if ($taxData[TAX_COLLECTION_DATA::LAST_PAYMENT_DATE]!="") {
                        $payment_date='<a href="javascript:void(0);"  onclick="showPaymentHistory(\'tax\','.$taxData[TAX_COLLECTION_DATA::ID].');" class="text-primary '.TOOLTIP_CLASS.'" title="Click to see payment history" style="text-decoration:underline;">'.getFormattedDateTime($taxData[TAX_COLLECTION_DATA::LAST_PAYMENT_DATE]).'</a>';
                        if ($taxData[TAX_COLLECTION_DATA::PENDING_AMOUNT]!=0) {
                            $payment_date.=($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[COMPANIES::ACTIVE]==1) ? '&nbsp;<span class="badge badge-success cursor-pointer" onclick="AddTaxPayment('.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::ID].','.$v[COMPANIES::ID].','.$taxData[TAX_COLLECTION_DATA::PENDING_AMOUNT].');"><small><i class="fas fa-plus"></i>Pay</small></span>' : '';
                        }
                    } else {
                        $payment_date.=($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[COMPANIES::ACTIVE]==1) ? '&nbsp;<span class="badge badge-success cursor-pointer" onclick="AddTaxPayment('.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::ID].','.$v[COMPANIES::ID].','.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT].');"><small><i class="fas fa-plus"></i>Pay</small></span>' : '';
                    }
                    if ($taxData[TAX_COLLECTION_DATA::PENALTY_LAST_PAYMENT_DATE]!="") {
                        $penaltyPayDate='<a href="javascript:void(0);"  onclick="showPaymentHistory(\'penalty\','.$taxData[TAX_COLLECTION_DATA::ID].');" class="text-primary '.TOOLTIP_CLASS.'" title="Click to see payment history" style="text-decoration:underline;">'.getFormattedDateTime($taxData[TAX_COLLECTION_DATA::PENALTY_LAST_PAYMENT_DATE]).'</a>';
                        if ($taxData[TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT]!=0) {
                            $penaltyPayDate.=($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[COMPANIES::ACTIVE]==1) ? '&nbsp;<span class="badge badge-success cursor-pointer" onclick="AddPenaltyPayment('.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::ID].','.$v[COMPANIES::ID].','.$taxData[TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT].');"><small><i class="fas fa-plus"></i>Pay</small></span>' : '';
                        }
                    } else {
                        //change here
                        $penaltyPayDate.=($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[COMPANIES::ACTIVE]==1) ? '&nbsp;<span class="badge badge-success cursor-pointer" onclick="AddPenaltyPayment('.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::ID].','.$v[COMPANIES::ID].','.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT].');"><small><i class="fas fa-plus"></i>Pay</small></span>' : '';
                    }
                    switch ($getTaxPayData[0][TAX_COLLECTION_DATA::PAYMENT_STATUS]) {
                        case 1:
                            $status='<span class="badge badge-danger"><small>Due</small></span>';
                            break;
                        case 2:
                            $status='<span class="badge badge-success"><small>Cleared</small></span>';
                            break;
                        case 3:
                            $status='<span class="badge badge-warning"><small>Partially Paid</small></span>';
                            break;
                    }
                    $paidAmt=($taxData[TAX_COLLECTION_DATA::PAID_AMOUNT]);
                    $pendingAmt=($taxData[TAX_COLLECTION_DATA::PENDING_AMOUNT]);
                    $penaltyPaidAmt=($taxData[TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT]);
                    $penaltyPendingAmt=($taxData[TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT]);
                } else {
                    $payment_date.=($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[COMPANIES::ACTIVE]==1) ? '&nbsp;<span class="badge badge-success cursor-pointer" onclick="AddTaxPayment('.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::ID].','.$v[COMPANIES::ID].','.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT].');"><small><i class="fas fa-plus"></i>Pay</small></span>' : '';
                    $penaltyPayDate.=($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[COMPANIES::ACTIVE]==1) ? '&nbsp;<span class="badge badge-success cursor-pointer" onclick="AddPenaltyPayment('.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::ID].','.$v[COMPANIES::ID].','.$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT].');"><small><i class="fas fa-plus"></i>Pay</small></span>' : '';
                }
                $saudNArr=[];
                $pnm='';
                if (count($getAud)>0) {
                    // rip($getAud);
                    foreach ($getAud as $psadk => $psadkv) {
                        $getAudName=getData(Table::USERS,[USERS::NAME],[USERS::ID=>$psadkv[COMPANY_ASSIGNED_DATA::AUDITOR_ID]]);
                        if ($psadkv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==1) {
                            // echo "Prime Auditor: ".$getAudName[0][USERS::NAME];
                            if (count($getAudName)>0) {
                                $pnm=altRealEscape($getAudName[0][USERS::NAME]);
                            }
                        } else {
                            if ($psadkv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==2) {
                                // echo "<br />Sec Auditor: ".$getAudName[0][USERS::NAME];
                                if (count($getAudName)>0) {
                                    $saudNArr[]=$getAudName[0][USERS::NAME];
                                }
                            }
                        }
                    }
                }
                // rip($saudNArr);
                if ($pnm!="") {
                    $auditor="<small><b>Prim: </b>".$pnm.'</small>';
                }
                if (count($saudNArr)>0) {
                    $auditor.= '<br><small><b>Sec: </b>'.implode(', ',$saudNArr).'</small>';
                }
                $getQueryDataQ="
                SELECT qdata.".
                QUERY_DATA::QUERY_NO." AS qno FROM ".
                Table::QUERY_DATA." AS qdata INNER JOIN ".
                Table::POSITION_PAPER_DATA." AS ppdata ON (qdata.".
                QUERY_DATA::ID."=ppdata.".
                POSITION_PAPER_DATA::QUERY_ID.") LEFT JOIN ".
                Table::AUDIT_ASSESSMENT_DATA." AS asmt ON (ppdata.".
                POSITION_PAPER_DATA::POSITION_PAPER_ID."=asmt.".
                AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID.") WHERE asmt.".
                AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID." = ".
                $getAssessmentData[0][AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID]." AND qdata.".
                QUERY_DATA::CLIENT_ID."=".$_SESSION[CLIENT_ID]." AND qdata.".
                QUERY_DATA::ID." = ppdata.".
                POSITION_PAPER_DATA::QUERY_ID." AND ppdata.".
                POSITION_PAPER_DATA::POSITION_PAPER_ID." = asmt.".
                AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID." GROUP BY qdata.".
                QUERY_DATA::ID." ORDER BY qdata.".
                QUERY_DATA::ID." ASC";
                // $getQueryData=getData(Table::QUERY_DATA,[QUERY_DATA::QUERY_NO],[QUERY_DATA::ID=>$getAssessmentData[0][AUDIT_ASSESSMENT_DATA::QUERY_ID]]);
                $getQueryData=getCustomData($getQueryDataQ);
                $taxQids=[];
                if (count($getQueryData)>0) {
                    foreach ($getQueryData as $gqdk => $gqdv) {
                        $taxQids[]=$gqdv['qno'];
                    }
                }
                $query_no=(count($taxQids)>0)?implode(', ',$taxQids):EMPTY_VALUE;
                $assRefNo=($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::REF_NO]!="")?altRealEscape($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::REF_NO]):EMPTY_VALUE;
                $date_of_issue=($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE]!="")?getFormattedDateTime($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE],LONG_DATE_FORMAT):EMPTY_VALUE;
                $days_count=($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE]!="")?((getDateDiff($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE],getToday(false))=="")?"0 Days":(getDateDiff($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE],getToday(false)))):EMPTY_VALUE;
                $amount=($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT]!=0)?($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT]):DEFAULT_AMOUNT;
                $penaltyAmount=($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT]!=0)?($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT]):DEFAULT_AMOUNT;
                if ($getAssessmentData[0][AUDIT_ASSESSMENT_DATA::ACTIVE]==2) {
                    $objectionAlert='
                        <br><span class="badge badge-danger"><small>Under Objection</small></span>
                    ';
                } else {
                    $objectionAlert='<br><span class="badge badge-success"><small>Collection</small></span>';
                }
                if ($amount!=0) {
                    $pendingAmt=($amount-$paidAmt);
                }
                if ($penaltyAmount!=0) {
                    $penaltyPendingAmt=($penaltyAmount-$penaltyPaidAmt);
                }
                $table_tr.='
                <tr>
                    <td>'.$sl.(($v[COMPANIES::ACTIVE]==0)?'<br><span class="badge badge-warning cursor-pointer '.TOOLTIP_CLASS.'" title="Company Inactive !"><i class="fas fa-exclamation"></i></span>':'').$objectionAlert.'</td>
                    <td>'.$name.'</td>
                    <td>'.$auditor.'</td>
                    <td>'.$assRefNo.'</td>
                    <td>'.$tax_type.'</td>
                    <td>'.$posRefNo.'</td>
                    <td>'.$date_of_issue.'</td>
                    <td>'.$days_count.'</td>
                    <td>'.$status.'</td>
                    <td>'.moneyFormatIndia($amount).'</td>
                    <td>'.moneyFormatIndia($paidAmt).'</td>
                    <td>'.moneyFormatIndia($pendingAmt).'</td>
                    <td style="display: flex; justify-content: space-evenly;">'.$payment_date.'</td>
                    <td>'.moneyFormatIndia($penaltyAmount).'</td>
                    <td>'.moneyFormatIndia($penaltyPaidAmt).'</td>
                    <td>'.moneyFormatIndia($penaltyPendingAmt).'</td>
                    <td style="display: flex; justify-content: space-evenly;">'.$penaltyPayDate.'</td>
                </tr>
                ';
            }
            $sl++;
        }
    }
?>

    <div class="row mt-2">
        <div class="col-sm-12 col-md-12 col-lg-12 text-center">
            <h4 class="text-center">Manage all Tax Collections here</h4>
        </div>
        <!-- <div class="col-sm-1 col-md-1 col-lg-1 text-right" id="list_nav_btn">
            <button class="btn btn-primary" name="add" style="cursor: not-allowed;" disabled onclick="changeNavigateBtn('tax-collection');">Add</button>
        </div> -->
    </div>

    <div class="card mt-5">
        <div class="card-body">
            <?= getSpinner(true, "TaxCollectionLoader"); ?>
            <div class="row mt-3 d-none">
                <div class="col-lg-7 col-md-7 col-sm-7">
                    <label class="form_label" for="tax_collection_company_select">Select Tax Payer</label><?= getAsterics(); ?>
                    <select class="form-control" id="tax_collection_company_select">
                        <?=$com_options;?>
                    </select>
                </div>
            </div>
            <div class="row mt-3" id="taxCollectionPrintDiv">
                <div class="col-md-12 text-center">
                    <h6 class="text-center main_heading">Tax Collection Report</h6>
                </div>
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-sm btn-info no-print" onclick="printDiv('taxCollectionPrintDiv')"><small><i class="fas fa-print"></i>&nbsp;Print</small></button>
                </div>
                <div class="col-md-12 mt-2">
                    <div class="table-responsive mt-2" id="tax_collection_table_div">
                        <table class="table table-sm table-striped table-hover table-bordered text-center tax_collection_table">
                            <thead class="text-center table-warning">
                                <tr>
                                    <th rowspan="2"><small><b>Current Status</b></small></th>
                                    <th rowspan="2">Tax Payer</th>
                                    <th rowspan="2">Auditor</th>
                                    <th rowspan="2">Asmt Ref. No.</th>
                                    <th rowspan="2">Tax Type</th>
                                    <th rowspan="2">Position Paper No.</th>
                                    <th rowspan="2">Date of Issue</th>
                                    <th rowspan="2">Days Count</th>
                                    <th rowspan="2">Status</th>
                                    <th colspan="4">Base Tax Collection(PGK) </th>

                                    <!-- <th rowspan="2">Tax Amt.</th>
                                    <th rowspan="2">Tax Paid Amt.</th>
                                    <th rowspan="2">Tax Pending Amt.</th>
                                    <th rowspan="2">Tax Payment Date</th> -->
                                    <th colspan="4">Penalty Collection (PGK)</th>

                                    <!-- <th rowspan="2">Penalty Amt.</th>
                                    <th rowspan="2">Penalty Paid Amt.</th>
                                    <th rowspan="2">Penalty Pending Amt.</th>
                                    <th rowspan="2">Penalty Payment Date</th> -->
                                </tr>
                                <tr style="text-transform:capitalize; font-size: 0.6rem !important;">
                                    <th colspan="1">Base Tax Claimed (PGK)</th>
                                    <th colspan="1">Paid Amt.</th>
                                    <th colspan="1">Pending Amt.</th>
                                    <th colspan="1">Payment Date</th>

                                    <th colspan="1">Penalty Claimed (PGK)</th>
                                    <th colspan="1">Paid Amt.</th>
                                    <th colspan="1">Pending Amt.</th>
                                    <th colspan="1">Payment Date</th>
                                <!-- <th>open/close</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?=$table_tr;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
}
?>