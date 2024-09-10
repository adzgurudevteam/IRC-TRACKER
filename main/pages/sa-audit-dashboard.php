<?php 
function printContent() {
    $total_no_of_audits=$total_audit_completed_per=$total_completed_audits=$total_pending_audits=0;
    $total_no_of_queries=$total_queries_submitted=$submitted_query_per=$pending_queries=0;
    $QchartScript=$noAudScript=$com_table_rows="";
    $noAudData=$noAudCategories=$showName=$assignedCompanyIds=[];
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
        foreach ($getQueryData as $qdk => $qdv) {
            $total_no_of_queries += $qdv[QUERY_DATA::TOTAL_NO_OF_QUERY];
            $total_queries_submitted += $qdv[QUERY_DATA::NO_OF_QUERY_SOLVED];
        }
        $pending_queries = ($total_queries_submitted==0)?$total_no_of_queries:($total_no_of_queries-$total_queries_submitted);
        $submitted_query_per=floor(($total_queries_submitted/$total_no_of_queries)*100);
    }
    // $csql = "SELECT SUM(".QUERY_DATA::TOTAL_NO_OF_QUERY.") FROM ".Table::QUERY_DATA." WHERE ".QUERY_DATA::USER_ID." = ".$_SESSION[RID];
    // rip(getCustomData($csql));
    $getCompanyData = getData(Table::COMPANIES, ['*'], [
        COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
        COMPANIES::STATUS => ACTIVE_STATUS
      ],[],[],[],"ASC",[0,10]);
    if (count($getCompanyData) > 0) {
        foreach ($getCompanyData as $cdk => $cdv) {
            $c_industry_type = $c_audit_type = $c_tax_type = "";
            $assignedDate=$exptCloseDate=$closeDate=EMPTY_VALUE;
            $primary_auditor=$secondary_auditors='<span class="badge alert-info"><small><b>Yet to be assigned</b></small></span>';
            if ($cdv[COMPANIES::INDUSTRY_TYPE_ID] != 0) {
                $getIndustryName = getData(Table::COMPANY_INDUSTRY_TYPE, [
                    COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
                ], [
                    COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
                    COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS,
                    COMPANY_INDUSTRY_TYPE::ID => $cdv[COMPANIES::INDUSTRY_TYPE_ID]
                ]);
                $c_industry_type = (count($getIndustryName) > 0) ? altRealEscape($getIndustryName[0][COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE]) : EMPTY_VALUE;
            }
            $getAuditTaxTypeHistory = getData(Table::AUDIT_TAX_TYPE_HISTORY, [
                AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID,
                AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID,
                AUDIT_TAX_TYPE_HISTORY::START_DATE
            ], [
                AUDIT_TAX_TYPE_HISTORY::ACTIVE => 1,
                AUDIT_TAX_TYPE_HISTORY::STATUS => ACTIVE_STATUS,
                AUDIT_TAX_TYPE_HISTORY::COMPANY_ID => $cdv[COMPANIES::ID],
                AUDIT_TAX_TYPE_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
            ]);
            $getAssignmentData=getData(Table::COMPANY_ASSIGNED_DATA,[
                COMPANY_ASSIGNED_DATA::AUDITOR_ID,
                COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
                COMPANY_ASSIGNED_DATA::STATUS,
                COMPANY_ASSIGNED_DATA::CREATED_AT
            ],[
                COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
                COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$cdv[COMPANIES::ID],
                COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS
            ]);
            if (count($getAssignmentData)>0) {
                $secAudIds=$secAudNames=[];
                foreach ($getAssignmentData as $aad => $aav) {
                    if ($aav[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 1) {
                        $getAudData=getData(Table::AUDITS_DATA,[
                            AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE,
                            AUDITS_DATA::AUDIT_END_DATE
                        ],[
                            AUDITS_DATA::COMPANY_ID=>$cdv[COMPANIES::ID]
                        ]);
                        $getpAudName=getData(Table::USERS,[USERS::NAME],[USERS::ID=>$aav[COMPANY_ASSIGNED_DATA::AUDITOR_ID],USERS::ACTIVE=>1]);
                        $primary_auditor=(count($getpAudName)>0)?altRealEscape($getpAudName[0][USERS::NAME]):EMPTY_VALUE;
                        $assignedDate=getFormattedDateTime($aav[COMPANY_ASSIGNED_DATA::CREATED_AT]);
                        if (count($getAudData)>0) {
                            if ($getAudData[0][AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE]!=null) {
                                $exptCloseDate=getFormattedDateTime($getAudData[0][AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE]);
                            } else {
                                $exptCloseDate=EMPTY_VALUE;
                            }
                            if ($getAudData[0][AUDITS_DATA::AUDIT_END_DATE]!=null) {
                                $closeDate=getFormattedDateTime($getAudData[0][AUDITS_DATA::AUDIT_END_DATE]);
                            } else {
                                $closeDate=EMPTY_VALUE;
                            }
                        }
                    }
                    if ($aav[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 2) {
                        $secAudIds[]=$aav[COMPANY_ASSIGNED_DATA::AUDITOR_ID];
                    }
                }
                $getSecAudName=getData(Table::USERS,[USERS::NAME],[
                    USERS::CLIENT_ID=>$_SESSION[CLIENT_ID],
                    USERS::ACTIVE=>1,
                    USERS::STATUS=>ACTIVE_STATUS
                ],[USERS::ID=>$secAudIds]);
                if (count($getSecAudName)>0) {
                    foreach ($getSecAudName as $sank => $sanv) {
                        $secAudNames[]=$sanv[USERS::NAME];
                    }
                    $secondary_auditors=(count($secAudNames)>0)?implode(", ", $secAudNames):'<span class="badge badge-warning"><small>Not Found!<small></span>';
                }
            }
            if (count($getAuditTaxTypeHistory) > 0) {
                $atype = $getAuditTaxTypeHistory[0][AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID];
                $ttype = $getAuditTaxTypeHistory[0][AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID];
                $getc_audit_type = getData(Table::AUDIT_TYPES, [
                    AUDIT_TYPES::AUDIT_TYPE
                ], [
                    AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID],
                    AUDIT_TYPES::ID => $atype,
                    AUDIT_TYPES::STATUS => ACTIVE_STATUS
                ]);
                $c_audit_type = (count($getc_audit_type) > 0) ? $getc_audit_type[0][AUDIT_TYPES::AUDIT_TYPE] : EMPTY_VALUE;
                if ($ttype != "") {
                    $ttype_arr = explode(",", $ttype);
                    $getc_tax_type = getData(Table::TYPE_OF_TAX, [
                        TYPE_OF_TAX::TYPE_OF_TAX
                    ], [
                        TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
                        TYPE_OF_TAX::STATUS => ACTIVE_STATUS,
                    ], [
                        TYPE_OF_TAX::ID => $ttype_arr
                    ]);
                    if (count($getc_tax_type) > 0) {
                        foreach ($getc_tax_type as $ttypek => $ttypev) {
                            $c_tax_type .= altRealEscape($ttypev[TYPE_OF_TAX::TYPE_OF_TAX]);
                            $c_tax_type .= ($ttypek == ((count($getc_tax_type)) - 1)) ? '' : ', ';
                        }
                    }
                }
            }
            $actions = '
            <div class="" style="display:flex; justify-content: space-evenly;">
                <div class="cursor-pointer text-success '.TOOLTIP_CLASS.'" title="Click to Reassign" onclick="editAuditorAssignment(' . $cdv[COMPANIES::ID] . ');"><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
            </div>
            ';
            $checked = ($cdv[COMPANIES::ACTIVE] == 1) ? 'checked' : '';
            $activeInactive = '
            <div class="custom-control custom-switch noselect" style="cursor:pointer;">
                <input type="checkbox" ' . $checked . ' class="custom-control-input" id="company_active_' . $cdv[COMPANIES::ID] . '" onclick="makeCompanyActive(' . $cdv[COMPANIES::ID] . ');" style="cursor:pointer;" />
                <label class="custom-control-label text-success" for="company_active_' . $cdv[COMPANIES::ID] . '"></label>
            </div>
            ';
            $company_name = $cdv[COMPANIES::COMPANY_NAME];
            $company_name .= ($cdv[COMPANIES::TAX_IDENTIFICATION_NUMBER] != "") ? "<br>(TIN: " . $cdv[COMPANIES::TAX_IDENTIFICATION_NUMBER] . ")" : "";
            $getAuditCloseData=getData(Table::AUDITS_DATA,[
                AUDITS_DATA::ACTIVE,
                AUDITS_DATA::AUDIT_END_DATE
            ],[
                AUDITS_DATA::COMPANY_ID=>$cdv[COMPANIES::ID]
            ]);
            $auditClosed=false;
            if (count($getAuditCloseData)>0) {
                if (($getAuditCloseData[0][AUDITS_DATA::ACTIVE]==2)&&($getAuditCloseData[0][AUDITS_DATA::AUDIT_END_DATE]!=null)) {
                    $auditClosed=true;
                }
            }
            if ($auditClosed) {
                $com_table_rows .= '
                <tr>
                    <td>' . ($cdk + 1) . '</td>
                    <td>' . $company_name . '</td>
                    <td>' . $c_tax_type . '</td>
                    <td>' . $primary_auditor . '</td>
                    <td>' . $assignedDate . '</td>
                    <td>' . $exptCloseDate . '</td>
                    <td>' . $closeDate . '</td>
                </tr>';
            }
        }
        // rip ($showName);
        if (($com_table_rows=="")&&(count($getCompanyData)>0)) {
            $com_table_rows = '
            <tr>
                <td colspan="7">
                    <div class="alert alert-danger" role="alert">
                        No Completed Audits found !
                    </div>
                </td>
            </tr>';
        }
    } else {
        $com_table_rows = '
      <tr>
          <td colspan="7">
              <div class="alert alert-danger" role="alert">
                  No Companies found !
              </div>
          </td>
      </tr>';
    }
    //generating tax collection data [start]
    $oldestYear=$taxClaimed=$taxCollected=$penaltyRaised=$penaltyClollected=$year=0;
    $yrExt= 5;
    $taxIndWiseThead=$taxIndWiseTbody='';
    $getoldestYear = getData(Table::AUDITS_DATA,[
        "YEAR(".AUDITS_DATA::CREATED_AT.") as yr"
    ],[
        AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
    ],[],[],[AUDITS_DATA::CREATED_AT],"DESC",[0,1]);
    if (count($getoldestYear)>0) {
        $oldestYear = $getoldestYear[0]["yr"];
    } else {
        $oldestYear = getToday(false,"Y");
    }
    if ($oldestYear!==0) {
        $yrExt = ($oldestYear-4);
    }
    $getTaxCollLastAudYr=getData(Table::TAX_COLLECTION_DATA,[
        "SUM(".TAX_COLLECTION_DATA::TAX_AMOUNT.") as total_tax_amt",
        "SUM(".TAX_COLLECTION_DATA::PAID_AMOUNT.") as total_tax_paid_amt",
        "SUM(".TAX_COLLECTION_DATA::PENALTY_AMOUNT.") as total_penalty_amt",
        "SUM(".TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT.") as total_penalty_paid_amt"
    ],[
        "YEAR(".TAX_COLLECTION_DATA::CREATED_AT.")" => $oldestYear,
        TAX_COLLECTION_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
    ]);
    if (count($getTaxCollLastAudYr)>0) {
        $taxClaimed=($getTaxCollLastAudYr[0]["total_tax_amt"]!="")?moneyFormatIndia($getTaxCollLastAudYr[0]["total_tax_amt"]):$taxClaimed;
        $taxCollected=($getTaxCollLastAudYr[0]["total_tax_paid_amt"]!="")?moneyFormatIndia($getTaxCollLastAudYr[0]["total_tax_paid_amt"]):$taxCollected;
        $penaltyRaised=($getTaxCollLastAudYr[0]["total_penalty_amt"]!="")?moneyFormatIndia($getTaxCollLastAudYr[0]["total_penalty_amt"]):$penaltyRaised;
        $penaltyClollected=($getTaxCollLastAudYr[0]["total_penalty_paid_amt"]!="")?moneyFormatIndia($getTaxCollLastAudYr[0]["total_penalty_paid_amt"]):$penaltyClollected;
    }
    $taxIndWiseThead='
        <tr>
            <th>Industry Name</th>
    ';
    $getTaxIndTypeData=getData(Table::COMPANY_INDUSTRY_TYPE,[
        COMPANY_INDUSTRY_TYPE::ID,
        COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
    ],[
        COMPANY_INDUSTRY_TYPE::STATUS=>ACTIVE_STATUS,
        COMPANY_INDUSTRY_TYPE::CLIENT_ID=>$_SESSION[CLIENT_ID]
    ],[],[],[COMPANY_INDUSTRY_TYPE::ID],"ASC",[0,10]);
    if (count($getTaxIndTypeData)>0) {
        foreach ($getTaxIndTypeData as $indtk => $indtv) {
            $totalTaxAmt=0;
            $taxIndWiseTbody.='
            <tr>
                <td>'.$indtv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE].'</td>
            ';
            $gettaxColComIds = getData(Table::COMPANIES,[COMPANIES::ID],[
                COMPANIES::INDUSTRY_TYPE_ID=>$indtv[COMPANY_INDUSTRY_TYPE::ID],
                COMPANIES::STATUS=>ACTIVE_STATUS,
                COMPANIES::CLIENT_ID=>$_SESSION[CLIENT_ID]
            ]);
            $taxIndComIds=[];
            if (count($gettaxColComIds)>0) {
                foreach ($gettaxColComIds as $gcidk => $gcidv) {
                    $taxIndComIds[]=$gcidv[COMPANIES::ID];
                }
            }
            for ($i=1; $i <= 5; $i++) {
                if ($i==1) {
                    if ($oldestYear!==0) {
                        $year = $yrExt;
                    }
                } else {
                    $year++;
                }
                $getTaxCollYr=getData(Table::TAX_COLLECTION_DATA,[
                    "SUM(".TAX_COLLECTION_DATA::PAID_AMOUNT.") as total_tax_paid_amt"
                ],[
                    "YEAR(".TAX_COLLECTION_DATA::CREATED_AT.")" => $year,
                    TAX_COLLECTION_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ],[
                    TAX_COLLECTION_DATA::COMPANY_ID=>$taxIndComIds
                ]);
                $taxIndWiseTbody.='
                    <td>'.($getTaxCollYr[0]["total_tax_paid_amt"]!=""?moneyFormatIndia($getTaxCollYr[0]["total_tax_paid_amt"]):moneyFormatIndia(0)).'</td>
                ';
                if ($getTaxCollYr[0]["total_tax_paid_amt"]!="") {
                    $totalTaxAmt+=$getTaxCollYr[0]["total_tax_paid_amt"];
                }
            }
            $taxIndWiseTbody.='
                <td>'.moneyFormatIndia($totalTaxAmt).'</td>
            </tr>
            ';
        }
    }
    $taxColYrOnYrData=$taxColYrOnYrLabel=[];
    $taxColYrOnYrThead=$taxColYrOnYrTbody="";
    $prevColl=0;
    for ($i=1; $i <= 5; $i++) {
        if ($i==1) {
            if ($oldestYear!==0) {
                $year = $yrExt;
            }
        } else {
            $year++;
        }
        $growth=0;
        $taxColYrOnYrLabel[]=$year;
        $getTaxCollYrOnYr=getData(Table::TAX_COLLECTION_DATA,[
            "SUM(".TAX_COLLECTION_DATA::PAID_AMOUNT.") as total_tax_paid_amt"
        ],[
            "YEAR(".TAX_COLLECTION_DATA::CREATED_AT.")" => $year,
            TAX_COLLECTION_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if ($getTaxCollYrOnYr[0]["total_tax_paid_amt"]!="") {
            $taxColYrOnYrData[]=moneyFormatIndia($getTaxCollYrOnYr[0]["total_tax_paid_amt"]);
            $growth=floor(calculateYearOnYearGrowth($getTaxCollYrOnYr[0]["total_tax_paid_amt"],$prevColl));
            $prevColl=$getTaxCollYrOnYr[0]["total_tax_paid_amt"];
        } else {
            $taxColYrOnYrData[]=moneyFormatIndia(0);
        }
        $taxIndWiseThead.='
        <th>'.$year.'</th>
        ';
        $taxColYrOnYrTbody.='
        <tr>
            <td>'.$year.'</td>
            <td>'.($getTaxCollYrOnYr[0]["total_tax_paid_amt"]!=""?moneyFormatIndia($getTaxCollYrOnYr[0]["total_tax_paid_amt"]):moneyFormatIndia(0)).'</td>
            <td class="'.(($growth>=0)?"text-success":"text-danger").'">'.$growth.'%</td>
        </tr>
        ';
    }
    $taxIndWiseThead.='
            <th>Total</th>
        </tr>
    ';
    $taxCollTaxTypeWiseThtml='';
    $getTaxTypeData=getData(Table::TYPE_OF_TAX,[
        TYPE_OF_TAX::TYPE_OF_TAX,
        TYPE_OF_TAX::ID
    ],[
        TYPE_OF_TAX::STATUS=>ACTIVE_STATUS,
        TYPE_OF_TAX::CLIENT_ID=>$_SESSION[CLIENT_ID]
    ]);
    $taxTypeLabels=$taxCollectedData=$penaltyCollectedData=[];
    if (count($getTaxTypeData)>0) {
        foreach ($getTaxTypeData as $ttk => $ttv) {
            $taxTypeLabels[]=$ttv[TYPE_OF_TAX::TYPE_OF_TAX];
            $getTTwiseTaxColComIds = getData(Table::AUDIT_TAX_TYPE_HISTORY,[
                AUDIT_TAX_TYPE_HISTORY::COMPANY_ID,
                AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID
            ],[
                AUDIT_TAX_TYPE_HISTORY::STATUS=>ACTIVE_STATUS,
                AUDIT_TAX_TYPE_HISTORY::ACTIVE=>1,
                AUDIT_TAX_TYPE_HISTORY::CLIENT_ID=>$_SESSION[CLIENT_ID]
            ]);
            if (count($getTTwiseTaxColComIds)>0) {
                $taxIndComIds=[];
                foreach ($getTTwiseTaxColComIds as $gttidk => $gttidv) {
                    $sttid=explode(',',$gttidv[AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID]);
                    if (in_array($ttv[TYPE_OF_TAX::ID],$sttid)) {
                        $taxIndComIds[]=$gttidv[AUDIT_TAX_TYPE_HISTORY::COMPANY_ID];
                    }
                }
                // rip(array_unique($taxIndComIds));
                $getTaxCollTTwiseData=getData(Table::TAX_COLLECTION_DATA,[
                    "SUM(".TAX_COLLECTION_DATA::TAX_AMOUNT.") as total_tax_amt",
                    "SUM(".TAX_COLLECTION_DATA::PAID_AMOUNT.") as total_tax_paid_amt",
                    "SUM(".TAX_COLLECTION_DATA::PENALTY_AMOUNT.") as total_penalty_amt",
                    "SUM(".TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT.") as total_penalty_paid_amt"
                ],[
                    TAX_COLLECTION_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ],[
                    TAX_COLLECTION_DATA::COMPANY_ID=>array_unique($taxIndComIds)
                ]);
                $total_tax_amt = ($getTaxCollTTwiseData[0]['total_tax_amt']!="")?moneyFormatIndia($getTaxCollTTwiseData[0]['total_tax_amt']):moneyFormatIndia(DEFAULT_AMOUNT);
                $total_tax_paid_amt = ($getTaxCollTTwiseData[0]['total_tax_paid_amt']!="")?moneyFormatIndia($getTaxCollTTwiseData[0]['total_tax_paid_amt']):moneyFormatIndia(DEFAULT_AMOUNT);
                $total_penalty_amt = ($getTaxCollTTwiseData[0]['total_penalty_amt']!="")?moneyFormatIndia($getTaxCollTTwiseData[0]['total_penalty_amt']):moneyFormatIndia(DEFAULT_AMOUNT);
                $total_penalty_paid_amt = ($getTaxCollTTwiseData[0]['total_penalty_paid_amt']!="")?moneyFormatIndia($getTaxCollTTwiseData[0]['total_penalty_paid_amt']):moneyFormatIndia(DEFAULT_AMOUNT);
                $taxCollectedData[]=$total_tax_paid_amt;
                $penaltyCollectedData[]=$total_penalty_paid_amt;
                $taxCollTaxTypeWiseThtml.='
                <tr>
                    <td>'.$ttv[TYPE_OF_TAX::TYPE_OF_TAX].'</td>
                    <td>'.$total_tax_amt.'</td>
                    <td>'.$total_tax_paid_amt.'</td>
                    <td>'.$total_penalty_amt.'</td>
                    <td>'.$total_penalty_paid_amt.'</td>
                </tr>';
            }
        }
    } else {
        $taxCollTaxTypeWiseThtml='
        <tr>
            <td colspan="5">
                <div class="alert alert-danger" role="alert">
                  No Tax Type found !
                </div>
            </td>
        </tr>';
    }
    //generating tax collection data [end]
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
                <div class="col-md-12 col-lg-12 col-sm-12">
                    <div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
                        Tax Collection Analysis&nbsp;[<b>Last Audited Year (<?=$oldestYear?>)</b>] <a href="<?=HOST_URL?>sadmin-tax-collection/"><i class="fas fa-external-link-alt"></i></a>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <!-- Tax Collection Data (Last Audited Year) [start] -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- <div class="card border-left-warning shadow h-100 py-2"> -->
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2 text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Tax Claimed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$taxClaimed;?></div>
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- <div class="card border-left-warning shadow h-100 py-2"> -->
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2 text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Tax Collected</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$taxCollected;?></div>
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- <div class="card border-left-warning shadow h-100 py-2"> -->
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2 text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Penalty Raised</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$penaltyRaised;?></div>
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <!-- <div class="card border-left-warning shadow h-100 py-2"> -->
                        <div class="card-body">
                            <div class="row no-gutters align-items-center cursor-pointer">
                                <div class="col mr-2 text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Penalty Collected</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800 blinking">&nbsp;<?=$penaltyClollected;?></div>
                                </div>
                            </div>
                        </div>
                    <!-- </div> -->
                </div>
                <!-- Tax Collection Data (Last Audited Year) [end] -->
            </div>
            <div class="row">
                <div class="col-md-12 col-lg-12 col-sm-12">
                    <div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
                        Tax Collection (Year on Year) <a href="<?=HOST_URL?>sadmin-tax-collection/"><i class="fas fa-external-link-alt"></i></a>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div id="taxCollectYrOnYrChart"></div>
                </div>
                <div class="col-md-7">
                    <div class="table-responsive mt-2">
                        <table class="table table-striped table-hover table-bordered text-center">
                            <thead class="text-center table-warning">
                                <tr>
                                    <th>Financial Year</th>
                                    <th>Tax Collected</th>
                                    <th>Growth (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?=$taxColYrOnYrTbody;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 col-lg-12 col-sm-12">
                    <div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
                        Tax Collection from Industries <a href="<?=HOST_URL?>sadmin-tax-collection/"><i class="fas fa-external-link-alt"></i></a>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped table-hover table-bordered text-center">
                            <thead class="text-center table-warning">
                                <?=$taxIndWiseThead;?>
                            </thead>
                            <tbody>
                                <?=$taxIndWiseTbody;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 col-lg-12 col-sm-12">
                    <div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
                        Tax Collection from Tax Type <a href="<?=HOST_URL?>sadmin-tax-collection/"><i class="fas fa-external-link-alt"></i></a>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="col-lg-7 col-md-7 col-sm-12">
                    <div id="taxPenaltyCollTTwiseChart"></div>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-12">
                    <div class="table-responsive mt-5">
                        <table class="table table-striped table-hover table-bordered text-center">
                            <thead class="text-center table-warning">
                                <tr>
                                    <th>Tax Type</th>
                                    <th>Tax Claimed</th>
                                    <th>Tax Collected</th>
                                    <th>Penalty Claimed</th>
                                    <th>Penalty Collected</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?=$taxCollTaxTypeWiseThtml;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Cards end -->

            <!-- Content Row -->
            <div class="row">
                <!-- Content Column -->
                <div class="col-lg-4 col-md-4 col-sm-12 mb-4">
                    <?php
                    //Audits & Queries Status Card
                    ?>
                    <div class="card shadow mb-4 w-40 p-3">
                        <!-- <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Projects</h6>
                        </div> -->
                        <div class="card-body" style="padding: 0 !important;">
                            <h4 class="small font-weight-bold" style="font-size: 16px;">Total No of Audits <span
                                    class="float-right" style="font-size: 16px !important;"><?=$total_no_of_audits;?></span></h4>
                            <hr />
                            <h4 class="small font-weight-bold">Total Audit Completed<span
                                    class="float-right"><?=floor($total_audit_completed_per)?>% (<?=$total_completed_audits?>)</span></h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?=floor($total_audit_completed_per)?>%"
                                    aria-valuenow="<?=floor($total_audit_completed_per)?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <hr />
                            <h4 class="small font-weight-bold">Total Pending Audits<span
                                    class="float-right"><?=floor(($total_pending_audits/$total_no_of_audits)*100).'% ('.$total_pending_audits.')';?></span></h4>
                            <div class="progress mb-4">
                                <div class="progress-bar" role="progressbar" style="width: <?=floor(($total_pending_audits/$total_no_of_audits)*100);?>%"
                                    aria-valuenow="<?=floor(($total_pending_audits/$total_no_of_audits)*100);?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <hr style="border: none; height: 2px; color: #333; background-color: #333;" />
                            <h4 class="small font-weight-bold" style="font-size: 16px;">Total No. of Queries<span
                                    class="float-right" style="font-size: 16px !important;"><?=$total_no_of_queries?></span></h4>
                            <hr />
                            <h4 class="small font-weight-bold">No. of Queries Submitted<span
                                    class="float-right"><?=floor($submitted_query_per)?>% (<?=$total_queries_submitted;?>)</span></h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?=floor($submitted_query_per)?>%"
                                    aria-valuenow="<?=floor($submitted_query_per)?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <hr />
                            <h4 class="small font-weight-bold">No. of Queries Pending<span
                                    class="float-right"><?=floor(($pending_queries/$total_no_of_queries)*100);?>% (<?=$pending_queries?>)</span></h4>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?=floor(($pending_queries/$total_no_of_queries)*100);?>%"
                                    aria-valuenow="<?=floor(($pending_queries/$total_no_of_queries)*100);?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12 mb-4 mt-2">
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    Audit Completed&NonBreakingSpace;<a href="<?=HOST_URL?>sadmin-audits/"><i class="fas fa-external-link-alt"></i></a>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped table-hover table-bordered text-center">
                            <thead class="text-center table-warning">
                                <tr style="text-transform: uppercase; font-size: 12px;">
                                    <th>Sl.</th>
                                    <th>Company Name</th>
                                    <th>Tax Type</th>
                                    <th>Auditor</th>
                                    <th>Allocation Date</th>
                                    <th>Scheduled Closing Date</th>
                                    <th>Actual Closing Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?=$com_table_rows;?>
                            </tbody>
                        </table>
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
                                <div class="col-xl-7 col-md-7 col-sm-12 mb-4">
                                    <div id="noAudChart"></div>
                                </div>
                                 <?php 
                                    $indtypeQsolUnsolHtml=$indTypeOptionsForTaxStat=$indWiseAuditThtml='';
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
                                            $IndTotalNoAudits=$openAudits=$closedAudits=0;
                                            $auditStage=EMPTY_VALUE;
                                            if (count($getComIds)>0) {
                                                foreach ($getComIds as $gcidk => $gcidv) {
                                                   $indComIds[]=$gcidv[COMPANIES::ID];
                                                }
                                                $getIndComAuditData=getData(Table::AUDITS_DATA,[
                                                    AUDITS_DATA::ACTIVE,
                                                    AUDITS_DATA::COMPANY_ID
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
                                                        // if ($aaav[AUDITS_DATA::ACTIVE]==1) {
                                                            $IndTotalNoAudits++;
                                                        // }
                                                        if ($aaav[AUDITS_DATA::ACTIVE]==1) {
                                                            $openAudits++;
                                                        } else {
                                                            $closedAudits++;
                                                        }
                                                        $getQstage=getData(Table::QUERY_DATA,[
                                                            QUERY_DATA::QUERY_STATUS
                                                        ],[
                                                            QUERY_DATA::COMPANY_ID=>$aaav[AUDITS_DATA::COMPANY_ID],
                                                            QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                        ]);
                                                        if (count($getQstage)>0) {
                                                            $auditStage='Query';
                                                        }
                                                        $getNstage=getData(Table::COMPANY_NOTICE_DATA,[
                                                            COMPANY_NOTICE_DATA::NOTICE_STATUS
                                                        ],[
                                                            COMPANY_NOTICE_DATA::COMPANY_ID=>$aaav[AUDITS_DATA::COMPANY_ID],
                                                            COMPANY_NOTICE_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                        ]);
                                                        if (count($getNstage)>0) {
                                                            $auditStage='Notice';
                                                        }
                                                        $getPPstage=getData(Table::POSITION_PAPERS,[
                                                            POSITION_PAPERS::OPEN_CLOSE_STATUS
                                                        ],[
                                                            POSITION_PAPERS::COMPANY_ID=>$aaav[AUDITS_DATA::COMPANY_ID],
                                                            POSITION_PAPERS::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                        ]);
                                                        if (count($getPPstage)>0) {
                                                            $auditStage='Position Paper';
                                                        }
                                                        $getAsmtStage=getData(Table::AUDIT_ASSESSMENT_DATA,[
                                                            AUDIT_ASSESSMENT_DATA::ACTIVE
                                                        ],[
                                                            AUDIT_ASSESSMENT_DATA::COMPANY_ID=>$aaav[AUDITS_DATA::COMPANY_ID],
                                                            AUDIT_ASSESSMENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                        ]);
                                                        if (count($getAsmtStage)>0) {
                                                            $auditStage='Assessment';
                                                        }
                                                        $getTCstage=getData(Table::TAX_COLLECTION_DATA,[
                                                            TAX_COLLECTION_DATA::COMPANY_ID
                                                        ],[
                                                            TAX_COLLECTION_DATA::COMPANY_ID=>$aaav[AUDITS_DATA::COMPANY_ID],
                                                            TAX_COLLECTION_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                                        ]);
                                                        if (count($getTCstage)>0) {
                                                            $auditStage='Tax Collection';
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
                                            $indWiseAuditThtml.='
                                            <tr>
                                                <td>'.$cidv[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE].'</td>
                                                <td>'.$IndTotalNoAudits.'</td>
                                                <td>'.$openAudits.'</td>
                                                <td>'.$closedAudits.'</td>
                                                <td>'.$auditStage.'</td>
                                            </tr>
                                            ';
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
                                <div class="col-xl-5 col-md-5 col-sm-12 mb-4">
                                    <div class="table-responsive mt-2">
                                        <table class="table table-striped table-hover table-bordered text-center">
                                            <thead class="text-center table-warning">
                                                <tr style="text-transform: uppercase; font-size: 12px;">
                                                    <th>Industry Name</th>
                                                    <th>Total No. of Audits</th>
                                                    <th>Closed</th>
                                                    <th>Open</th>
                                                    <th>Stage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?=$indWiseAuditThtml?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
    var taxCollYoyOptions = {
          series: [{
            name: 'Total Amount of Tax Collected',
          data: <?=json_encode($taxColYrOnYrData);?>
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
            <?=json_encode($taxColYrOnYrLabel);?>,
          labels: {
            style: {
              colors: colors,
              fontSize: '12px'
            }
          },
          title: {
                text: 'Audited Years'
            }
        },
        yaxis: {
            title: {
                text: 'Total Amount of Tax Collected',
            }
        }
        };

    var taxCollectYrOnYrChart = new ApexCharts(document.querySelector("#taxCollectYrOnYrChart"), taxCollYoyOptions);
    taxCollectYrOnYrChart.render();
    $taxTypeLabels=$taxCollectedData=$penaltyCollectedData=[];
    var taxPenaltyCollTTwiseChart_options = {
          series: [{
            name: 'Tax Collected',
          data: [92000,85000,68000,85000,69000]
        }, {
            name: 'Penalty Collected',
          data: [52000,45000,28000,75000,89000]
        }],
          chart: {
          type: 'bar',
          height: 430
        },
        plotOptions: {
          bar: {
            horizontal: true,
            dataLabels: {
              position: 'top',
            },
          }
        },
        dataLabels: {
          enabled: true,
          offsetX: -6,
          style: {
            fontSize: '12px',
            colors: ['#fff']
          }
        },
        stroke: {
          show: true,
          width: 1,
          colors: ['#fff']
        },
        tooltip: {
          shared: true,
          intersect: false
        },
        xaxis: {
          categories: <?=json_encode($taxTypeLabels);?>,
        },
        };

        var taxPenaltyCollTTwiseChart = new ApexCharts(document.querySelector("#taxPenaltyCollTTwiseChart"), taxPenaltyCollTTwiseChart_options);
        taxPenaltyCollTTwiseChart.render();
</script>
<?php
}
?>