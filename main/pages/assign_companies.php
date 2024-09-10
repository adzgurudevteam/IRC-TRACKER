<?php
function printContent()
{

    $auditorOption = '<option value="0" disabled selected>--- Select Auditor ---</option>';
    $companyOption = '<option value="0" disabled selected>--- Select Company ---</option>';
    $all_auditor_statics_data = '';
    $getAuditor = getData(Table::USERS, [
        Users::NAME,
        Users::ID
    ], [
        Users::USER_TYPE => EMPLOYEE,
        Users::CLIENT_ID => $_SESSION[CLIENT_ID],
        Users::ACTIVE => 1,
        Users::STATUS => ACTIVE_STATUS
    ]);
    $getCompanies = getData(Table::COMPANIES, [
        COMPANIES::ID,
        COMPANIES::COMPANY_NAME,
        COMPANIES::TAX_IDENTIFICATION_NUMBER
    ], [
        COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
        COMPANIES::STATUS => ACTIVE_STATUS,
        COMPANIES::ACTIVE => 1
    ]);
    if (count($getAuditor) > 0) {
        $asl=1;
        foreach ($getAuditor as $k => $v) {
            $getAuditAssignedData=getData(Table::COMPANY_ASSIGNED_DATA,[
                COMPANY_ASSIGNED_DATA::COMPANY_IDS,
                COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
            ],[
                COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS,
                COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
                COMPANY_ASSIGNED_DATA::AUDITOR_ID=>$v[Users::ID]
            ]);
            // rip($getAuditAssignedData);
            $primCompany=$secCompanies='';
            $primComArr=$secComArr=[];
            foreach ($getAuditAssignedData as $asd => $asv) {
                $getComName=getData(Table::COMPANIES,[COMPANIES::COMPANY_NAME],[COMPANIES::ID=>$asv[COMPANY_ASSIGNED_DATA::COMPANY_IDS],COMPANIES::STATUS=>ACTIVE_STATUS,COMPANIES::CLIENT_ID=>$_SESSION[CLIENT_ID]]);
                $comName = (count($getComName)>0)?$getComName[0][COMPANIES::COMPANY_NAME]:"";
                if ($asv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==1) {
                    $primComArr[]=$comName;
                }
                if ($asv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==2) {
                    $secComArr[]=$comName;
                }
            }
            if (count($secComArr)>0) {
                $secCompanies = implode(', ', $secComArr);
            }
            if (count($primComArr)>0) {
                $primCompany = implode(', ', $primComArr);
            }
            $audCount = '</br><small><b>Total Audits:</b> <b class="text-primary">'.(count($primComArr)+count($secComArr)).'</b></br> [ <b>Primary: </b><b class="text-danger">'.count($primComArr).'</b>, <b>Secondary: </b><b class="text-secondary">'.count($secComArr).'</b> ]</small>';
            $all_auditor_statics_data.='
            <tr>
                <td>'.$asl.'</td>
                <td>'.$v[Users::NAME].$audCount.'</td>
                <td>'.$primCompany.'</td>
                <td>'.$secCompanies.'</td>
            </tr>
            ';
            $auditorOption .= '<option value="' . $v[Users::ID] . '">' . $v[Users::NAME] . '</option>';
            $asl++;
        }
    } else {
        $all_auditor_statics_data='<tr class="animated fadeInDown">
            <td colspan="4">
                <div class="alert alert-danger" role="alert">
                    No Audits found !
                </div>
            </td>
        </tr>';
    }
    if (count($getCompanies) > 0) {
        foreach ($getCompanies as $ck => $cv) {
            $com_name = $cv[COMPANIES::COMPANY_NAME];
            $com_name .= ($cv[COMPANIES::TAX_IDENTIFICATION_NUMBER] != "") ? " ( TIN: " . $cv[COMPANIES::TAX_IDENTIFICATION_NUMBER] . " )" : "";
            $companyOption .= '<option value="' . $cv[COMPANIES::ID] . '">' . $com_name . '</option>';
        }
    }

    $com_table_rows = "";
    $showName = [];

    $getCompanyData = getData(Table::COMPANIES, ['*'], [
        COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
        COMPANIES::STATUS => ACTIVE_STATUS
      ]);

    if (count($getCompanyData) > 0) {

        foreach ($getCompanyData as $cdk => $cdv) {
            $c_industry_type = $c_audit_type = $c_tax_type = "";
            $primary_auditor=$secondary_auditors='<span class="badge badge-info">Yet to be assigned</span>';

            // $showName[] = $cdv[COMPANIES::COMPANY_NAME];
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
                COMPANY_ASSIGNED_DATA::STATUS
            ],[
                COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
                COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$cdv[COMPANIES::ID],
                COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS
            ]);
            if (count($getAssignmentData)>0) {
                $secAudIds=$secAudNames=[];
                foreach ($getAssignmentData as $aad => $aav) {
                    if ($aav[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 1) {
                        $getpAudName=getData(Table::USERS,[USERS::NAME],[USERS::ID=>$aav[COMPANY_ASSIGNED_DATA::AUDITOR_ID],USERS::ACTIVE=>1]);
                        $primary_auditor=(count($getpAudName)>0)?altRealEscape($getpAudName[0][USERS::NAME]):EMPTY_VALUE;
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
                    // echo count($ttype_arr);
                    // rip($ttype_arr);
                    // exit;
                    // if ((count($ttype_arr))>1) {
                    $getc_tax_type = getData(Table::TYPE_OF_TAX, [
                        TYPE_OF_TAX::TYPE_OF_TAX
                    ], [
                        TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
                        TYPE_OF_TAX::STATUS => ACTIVE_STATUS,
                    ], [
                        TYPE_OF_TAX::ID => $ttype_arr
                    ]);
                    // $c_tax_type = (count($getc_tax_type)>0) ? $getc_tax_type[0][TYPE_OF_TAX::TYPE_OF_TAX] : EMPTY_VALUE;
                    if (count($getc_tax_type) > 0) {
                        // rip($getc_tax_type);
                        foreach ($getc_tax_type as $ttypek => $ttypev) {
                            $c_tax_type .= altRealEscape($ttypev[TYPE_OF_TAX::TYPE_OF_TAX]);
                            $c_tax_type .= ($ttypek == ((count($getc_tax_type)) - 1)) ? '' : ', ';
                        }
                    }
                    // }
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
            $company_name .= ($cdv[COMPANIES::TAX_IDENTIFICATION_NUMBER] != "") ? " ( TIN: " . $cdv[COMPANIES::TAX_IDENTIFICATION_NUMBER] . " )" : "";
            $com_table_rows .= '
            <tr>
                <td>' . ($cdk + 1) . '</td>
                <td>' . $company_name . '</td>
                <td>' . $c_industry_type . '</td>
                <td>' . $c_audit_type . '</td>
                <td>' . $c_tax_type . '</td>
                <td>' . $primary_auditor . '</td>
                <td>' . $secondary_auditors . '</td>
                <td>' . $actions . '</td>
            </tr>';
        }
        // rip ($showName);
    } else {
        $com_table_rows = '
      <tr>
          <td colspan="8">
              <div class="alert alert-danger" role="alert">
                  No Companies found !
              </div>
          </td>
      </tr>';
    }

?>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 text-center">
            <h4 class="text-center main_heading">Assign Audits</h4>
            <h6 class="text-center sub_heading">Assign Audit to Auditors here</h6>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12 text-right" id="list_nav_btn">
            <button class="btn btn-sm btn-primary" name="add" onclick="changeNavigateBtn('assign_audit');"><i class="fas fa-plus"></i>&nbsp;Add</button>
        </div>
    </div>
    <div class="card mt-2">
        <div class="card-body">
            <div class="row" id="assign_audit_add_row" style="display: none;">
                <div class="col-md-4 col-lg-4 col-sm-6">
                    <div class="form-outline" style="margin-top: 30px;">
                        <label class="form_label" for="assign_auditor_select">Select Company</label>
                        <select id="assign_auditor_select" class="form-control" onchange="getAssignedCompanyDetails();">
                            <?= $companyOption; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-8 col-lg-8 col-sm-6" style="border-left: solid 1px;">
                    <?= getSpinner(true, "AssignedComLoader") ?>
                    <h6 class="text-center font-weight-bold">Assigned Audits Details</h6>
                    <input type="hidden" style="visibility: hidden;" value="0" id="is_update">
                    <div class="mt-3 assigned_result">
                        <div class="text-center">
                            <small id="passwordHelpInline" class="text-muted">
                                <i class="text-danger">*</i>&nbsp; Please select a company to proceed.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="assign_audit_view_row">
                <?=getSpinner(true,"auditAssignmentLoader");?>
                <div class="table-responsive mt-2">
                    <table class="table table-sm table-striped table-hover table-bordered text-center assigned_audit_table data-table">
                        <thead class="text-center table-warning">
                            <tr style="text-transform: uppercase; font-size: 12px;">
                                <th>Sl.</th>
                                <th>Company Name</th>
                                <th>Industry</th>
                                <th>Audit Type</th>
                                <th>Tax Type</th>
                                <th>Primary Auditor</th>
                                <th>Secondary Auditor(s)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=$com_table_rows;?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" id="all_auditor_statics_table_row">
                <div class="col-md-5 col-lg-5 col-sm-12 mt-2" id="CurrentAuditorStatCol">
                    <div class="card">
                        <div class="card-body">
                            <?=getSpinner(true, 'CurrAudStatLoader');?>
                            <h6 class="text-center font-weight-bold mt-3">Current Primary Auditor Information</h6>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <p class="text-primary currAudDetHeading">Current Auditor Details</p>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <span class="text-dark" id="CurrAudAuditorDetaildSpan">Details</span>
                                </div>
                            </div>
                            <div class="row currentAudDetailsRow mt-2">
                                <div class="col-md-12">
                                    <p class="text-primary currAudDetHeading">Primary Audits</p>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <span class="text-dark" id="CurrAudPrimAudDetaildSpan">Details</span>
                                </div>
                            </div>
                            <div class="row currentAudDetailsRow mt-2">
                                <div class="col-md-12">
                                    <p class="text-primary currAudDetHeading">Secondary Audits</p>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <span class="text-dark" id="CurrAudSecAudDetaildSpan">Details</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-lg-7 col-sm-12 mt-2" id="CurrentAuditorStatCol_sec">
                    <div class="card">
                        <div class="card-body">
                            <?=getSpinner(true, 'CurrAudStatLoader_sec');?>
                            <h6 class="text-center font-weight-bold mt-3">Current Secondary Auditor Information</h6>
                            <div class="row mt-3" id="currentSecondaryAudHeadingRow">
                                <div class="col-md-4 text-center">
                                    <p class="text-primary text-center currAudDetHeading">Current Auditor Details</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <p class="text-primary text-center currAudDetHeading">Primary Audits</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <p class="text-primary text-center currAudDetHeading">Secondary Audits</p>
                                </div>
                            </div>
                            <div id="currentSecondaryAudDataRow">
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <p class="text-dark mt-2" id="CurrAudAuditorDetaildSpan_sec">Details</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-dark mt-2" id="CurrAudPrimAudDetaildSpan_sec">Details</p>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-dark mt-2" id="CurrAudSecAudDetaildSpan_sec">Details</span>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row currentAudDetailsRow mt-2">
                            </div>
                            <div class="row currentAudDetailsRow mt-2">
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-12 col-sm-12 mt-2">
                    <h6 class="text-center font-weight-bold">Assigned Audits Details Under Each Auditors</h6>
                </div>
                <div class="col-md-12 col-lg-12 col-sm-12">
                    <?=getSpinner(true,"auditorsStatsLoader");?>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped table-hover table-bordered text-center all_auditor_statics_table">
                            <thead class="text-center table-warning">
                                <tr style="text-transform: uppercase; font-size: 12px;">
                                    <th>Sl.</th>
                                    <th>Auditor Details</th>
                                    <th>Primary Audits</th>
                                    <th>Secondary Audits</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?=$all_auditor_statics_data;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>