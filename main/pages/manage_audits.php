<?php
function printContent()
{
  $company_row = '';
  $active_audit_companies = '<option value="0" disabled selected>--- Select Company ---</option>';
  $auditTypeOptions = '<option value="0" disabled selected>--- Select Audit Type ---</option>';
  $taxTypeOptions = '<option value="0" disabled selected>--- Select Tax Type ---</option>';
  $queryOptions = '<option value="0" disabled>--- Select Query ---</option>';
  $assignedCompanyIds = [];
  $getAssignedCompanies = getData(Table::COMPANY_ASSIGNED_DATA, [
    COMPANY_ASSIGNED_DATA::COMPANY_IDS,
    COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
  ], [
    COMPANY_ASSIGNED_DATA::AUDITOR_ID => $_SESSION[RID],
    COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
    COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS
  ]);
  if (count($getAssignedCompanies) > 0) {
    // if ($getAssignedCompanies[0][COMPANY_ASSIGNED_DATA::COMPANY_IDS] != "") {
    //     $assignedCompanyIds = explode(',', $getAssignedCompanies[0][COMPANY_ASSIGNED_DATA::COMPANY_IDS]);
    // }
    foreach ($getAssignedCompanies as $ack => $acv) {
      $assignedCompanyIds[] = $acv[COMPANY_ASSIGNED_DATA::COMPANY_IDS];
    }
  }
  $getCompData = getData(Table::COMPANIES, ['*'], [
    COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
    COMPANIES::STATUS => ACTIVE_STATUS
  ]);
  $getAuditTypes = getData(Table::AUDIT_TYPES, [
    AUDIT_TYPES::ID,
    AUDIT_TYPES::AUDIT_TYPE
  ], [
    AUDIT_TYPES::STATUS => ACTIVE_STATUS,
    AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID]
  ]);
  $getTaxTypes = getData(Table::TYPE_OF_TAX, [
    TYPE_OF_TAX::TYPE_OF_TAX,
    TYPE_OF_TAX::ID
  ], [
    TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
    TYPE_OF_TAX::STATUS => ACTIVE_STATUS
  ]);
  if (count($getCompData) > 0) {
    foreach ($getCompData as $k => $v) {
      switch ($_SESSION[USER_TYPE]) {
        case SADMIN:
        case ADMIN:
          $audit_closed=false;
          $audit_btn = '<span class="text-info"><small style="font-weight: bold;">Not Started yet !</small></span>';
          $checkPrimarySecondary = getData(Table::COMPANY_ASSIGNED_DATA, [
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
          ], [
            COMPANY_ASSIGNED_DATA::COMPANY_IDS => $v[COMPANIES::ID],
            COMPANY_ASSIGNED_DATA::AUDITOR_ID => $_SESSION[RID],
          ]);
          // rip($checkPrimarySecondary);
          // echo "end 1st loop <br>";
          // if ($checkPrimarySecondary[0][COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 2) {
          // $getMemoData = getData(Table::AUDIT_MEMO_DATA, [AUDIT_MEMO_DATA::MEMO_NO], [
          //   AUDIT_MEMO_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
          //   AUDIT_MEMO_DATA::COMPANY_ID => $v[COMPANIES::ID],
          //   AUDIT_MEMO_DATA::SECONDARY_AUDITOR_ID => $_SESSION[RID],
          //   AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS
          // ]);
          // if (count($getMemoData)>0) {
          //   $audit_btn = '
          //   <div class="audit_memo_btn">
          //     <span class="badge badge-white cursor-pointer" onclick="viewMemoInfo('. $v[COMPANIES::ID] .')"><i class="fas fa-2x fa-info-circle"></i></span>
          //     &nbsp;
          //     <button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>
          //   </div>
          //   ';
          // } else {
          //   $audit_btn = '<button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>';
          // }
          // } else {
          $getAuditData = getData(Table::AUDITS_DATA, [
            AUDITS_DATA::ID,
            AUDITS_DATA::AUDIT_START_DATE,
            AUDITS_DATA::AUDIT_END_DATE,
            AUDITS_DATA::ACTIVE
          ], [
            AUDITS_DATA::STATUS => ACTIVE_STATUS,
            AUDITS_DATA::COMPANY_ID => $v[COMPANIES::ID]
          ]);
          if (count($getAuditData) > 0) {
            if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
                $getApprovalData=getData(Table::AUDIT_CLOSE_REQUEST_DATA,['*'],[
                  AUDIT_CLOSE_REQUEST_DATA::AUDIT_ID=>$getAuditData[0][AUDITS_DATA::ID],
                  AUDIT_CLOSE_REQUEST_DATA::ACTIVE=>1,
                  AUDIT_CLOSE_REQUEST_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
              ]);
              $closeRequested=$closeApproved=false;
              if (count($getApprovalData)>0) {
                  $closeRequested=true;
                  if ($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS]==1) {
                  $closeApproved=true;
                  }
              }
              $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
              if ($closeRequested && !$closeApproved) {
                  $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong>Close Requested on: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::REQUEST_DATE]).'<small></span></br><button class="btn btn-sm btn-success" type="button" onclick="approveCloseAudit(' . $getAuditData[0][AUDITS_DATA::ID] . ',\''.$getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::REASON].'\');">Approve</button>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
              } else {
                  if ($closeRequested && $closeApproved) {
                      $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong>Close Request Approved: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_DATE]).'<small></span>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                  }
              }
              // $audit_btn .= '<br><button class="btn btn-sm btn-danger" type="button">Close</button>';
              $active_audit_companies .= '
              <option value="' . $v[COMPANIES::ID] . '">' . $v[COMPANIES::COMPANY_NAME] . '</option>
              ';
            } else {
              // check & show closed status here
              if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 2) {
                $audit_closed=true;
                $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
                $audit_btn .= '<br/><span><strong class="text-danger">Closed: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_END_DATE]) . '</small></span>';
              } else {
                $audit_closed=false;
                $audit_btn = '<span class="text-info"><small style="font-weight: bold;">Not Started yet !</small></span>';
              }
            }
          }
          // }
          $industry_type = $audit_type = $tax_type = EMPTY_VALUE;
          //getting Audit & Tax Type History [start]
          $getAuditTaxTypeHistory = getData(Table::AUDIT_TAX_TYPE_HISTORY, [
            AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID,
            AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID,
            AUDIT_TAX_TYPE_HISTORY::START_DATE
          ], [
            AUDIT_TAX_TYPE_HISTORY::ACTIVE => 1,
            AUDIT_TAX_TYPE_HISTORY::STATUS => ACTIVE_STATUS,
            AUDIT_TAX_TYPE_HISTORY::COMPANY_ID => $v[COMPANIES::ID],
            AUDIT_TAX_TYPE_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
          ]);
          $getIndustryType = getData(Table::COMPANY_INDUSTRY_TYPE, [
            COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
          ], [
            COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS,
            COMPANY_INDUSTRY_TYPE::ID => $v[COMPANIES::INDUSTRY_TYPE_ID]
          ]);
          if (count($getIndustryType) > 0) {
            $industry_type = ucwords(altRealEscape($getIndustryType[0][COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE]));
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
            $audit_type = (count($getc_audit_type) > 0) ? $getc_audit_type[0][AUDIT_TYPES::AUDIT_TYPE] : EMPTY_VALUE;
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
                $tax_type = "";
                foreach ($getc_tax_type as $ttypek => $ttypev) {
                  $tax_type .= $ttypev[TYPE_OF_TAX::TYPE_OF_TAX];
                  $tax_type .= ($ttypek == ((count($getc_tax_type)) - 1)) ? '' : ', ';
                }
              }
              // }
            }
          }
          //getting Audit & Tax Type History [end]
          $com_name = '<span>' . ucwords($v[COMPANIES::COMPANY_NAME]);
          $com_name .= ($v[COMPANIES::TAX_IDENTIFICATION_NUMBER] != null) ? ' (<strong>TIN: </strong>' . $v[COMPANIES::TAX_IDENTIFICATION_NUMBER] . ')' : '';
          $com_name .= '</span>';
          $case_code = ($v[COMPANIES::CASE_CODE] != null) ? altRealEscape($v[COMPANIES::CASE_CODE]) : EMPTY_VALUE;
          $company_row .= '<tr id="company_list_' . $v[COMPANIES::ID] . '">
            <td>' . ($k + 1) . '</td>
            <td class="text-left">' . $com_name . '</td>
            <td>' . $industry_type . '</td>
            <td>' . $case_code . '</td>
            <td>' . $audit_type . '</td>
            <td>' . $tax_type . '</td>
            <td class="audit_btn">
              ' . $audit_btn . '
            </td>
        </tr>';
          break;
        case EMPLOYEE:
          if (in_array($v[COMPANIES::ID], $assignedCompanyIds)) {
            $audit_closed=false;
            $audit_btn = ($v[COMPANIES::ACTIVE]==1)?'<button class="btn btn-sm btn-success" type="button" onclick="enterExpectedCompleteDate(' . $v[COMPANIES::ID] . ');">Start</button>':'<span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
            $checkPrimarySecondary = getData(Table::COMPANY_ASSIGNED_DATA, [
              COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
            ], [
              COMPANY_ASSIGNED_DATA::COMPANY_IDS => $v[COMPANIES::ID],
              COMPANY_ASSIGNED_DATA::AUDITOR_ID => $_SESSION[RID],
            ]);
            // rip($checkPrimarySecondary);
            // echo "end 1st loop <br>";
            $getAuditData = getData(Table::AUDITS_DATA, [
              AUDITS_DATA::ID,
              AUDITS_DATA::AUDIT_START_DATE,
              AUDITS_DATA::AUDIT_END_DATE,
              AUDITS_DATA::ACTIVE
            ], [
              AUDITS_DATA::STATUS => ACTIVE_STATUS,
              AUDITS_DATA::COMPANY_ID => $v[COMPANIES::ID]
            ]);
            // if (count($getAuditData) > 0) {
            //   if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
            //   } else {
            //     // check & show close status here
            //     if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 2) {

            //     } else {

            //     }
            //   }
            // }
            if ($checkPrimarySecondary[0][COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 2) {
              $getMemoData = getData(Table::AUDIT_MEMO_DATA, [AUDIT_MEMO_DATA::MEMO_NO], [
                AUDIT_MEMO_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                AUDIT_MEMO_DATA::COMPANY_ID => $v[COMPANIES::ID],
                AUDIT_MEMO_DATA::SECONDARY_AUDITOR_ID => $_SESSION[RID],
                AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS
              ]);
              if (count($getMemoData) > 0) {
                $audit_btn = '
                <div class="audit_memo_btn">
                  <span class="badge badge-white cursor-pointer '.TOOLTIP_CLASS.'" title="Click to see Memo Details" onclick="viewMemoInfo(' . $v[COMPANIES::ID] . ')"><i class="fas fa-2x fa-info-circle"></i></span>
                ';
                if (count($getAuditData) > 0) {
                  if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
                    if ($v[COMPANIES::ACTIVE]==1) {
                    $audit_btn .= '
                      &nbsp;
                      <button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>
                    ';
                    } else {
                      $audit_btn .= '
                      &nbsp;
                      <span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>
                    ';
                    }
                  }
                }
                $audit_btn .= '
                </div>
                ';
              } else {
                if (count($getAuditData) > 0) {
                  if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
                    $audit_btn = ($v[COMPANIES::ACTIVE]==1)?'<button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>':'<span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                  } else {
                    $audit_btn = '<span class="badge badge-info"><small>Audit Closed!<small></span>';
                  }
                }
                // $audit_btn = '<button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>';
              }
            } else {
              $getAuditData = getData(Table::AUDITS_DATA, [
                AUDITS_DATA::ID,
                AUDITS_DATA::AUDIT_START_DATE,
                AUDITS_DATA::AUDIT_END_DATE,
                AUDITS_DATA::ACTIVE
              ], [
                AUDITS_DATA::STATUS => ACTIVE_STATUS,
                AUDITS_DATA::COMPANY_ID => $v[COMPANIES::ID]
              ]);
              if (count($getAuditData) > 0) {
                if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
                  $getApprovalData=getData(Table::AUDIT_CLOSE_REQUEST_DATA,['*'],[
                      AUDIT_CLOSE_REQUEST_DATA::AUDIT_ID=>$getAuditData[0][AUDITS_DATA::ID],
                      AUDIT_CLOSE_REQUEST_DATA::ACTIVE=>1,
                      AUDIT_CLOSE_REQUEST_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                  ]);
                  $closeRequested=$closeApproved=false;
                  if (count($getApprovalData)>0) {
                    $closeRequested=true;
                    if ($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS]==1) {
                      $closeApproved=true;
                    }
                  }
                  $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';                  
                  if (!$closeRequested && !$closeApproved) {
                    $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><button class="btn btn-sm btn-danger" type="button" onclick="closeAudit(' . $v[COMPANIES::ID] . ');">Close</button>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                  }
                  if ($closeRequested && !$closeApproved) {
                    $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong>Close Requested on: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::REQUEST_DATE]).'<small></span>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                  } else {
                    if ($closeRequested && $closeApproved) {
                      $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong>Close Request Approved: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_DATE]).'<small></span><br><button class="btn btn-sm btn-danger" type="button" onclick="closeAudit(' . $v[COMPANIES::ID] . ');">Close</button>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                    }
                  }
                  $active_audit_companies .= '
                  <option value="' . $v[COMPANIES::ID] . '">' . $v[COMPANIES::COMPANY_NAME] . '</option>
                  ';
                } else {
                  // check & show close status here
                  if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 2) {
                    $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
                    $audit_btn .= '<br/><span><strong class="text-danger">Closed: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_END_DATE]) . '</small></span>';
                  } else {
                    $audit_btn = ($v[COMPANIES::ACTIVE]==1)?'<button class="btn btn-sm btn-success" type="button" onclick="enterExpectedCompleteDate(' . $v[COMPANIES::ID] . ');">Start</button>':'<span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                  }
                }
              }
            }
            $industry_type = $audit_type = $tax_type = EMPTY_VALUE;
            //getting Audit & Tax Type History [start]
            $getAuditTaxTypeHistory = getData(Table::AUDIT_TAX_TYPE_HISTORY, [
              AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID,
              AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID,
              AUDIT_TAX_TYPE_HISTORY::START_DATE
            ], [
              AUDIT_TAX_TYPE_HISTORY::ACTIVE => 1,
              AUDIT_TAX_TYPE_HISTORY::STATUS => ACTIVE_STATUS,
              AUDIT_TAX_TYPE_HISTORY::COMPANY_ID => $v[COMPANIES::ID],
              AUDIT_TAX_TYPE_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
            ]);
            $getIndustryType = getData(Table::COMPANY_INDUSTRY_TYPE, [
              COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
            ], [
              COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
              COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS,
              COMPANY_INDUSTRY_TYPE::ID => $v[COMPANIES::INDUSTRY_TYPE_ID]
            ]);
            if (count($getIndustryType) > 0) {
              $industry_type = ucwords(altRealEscape($getIndustryType[0][COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE]));
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
              $audit_type = (count($getc_audit_type) > 0) ? $getc_audit_type[0][AUDIT_TYPES::AUDIT_TYPE] : EMPTY_VALUE;
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
                  $tax_type = "";
                  foreach ($getc_tax_type as $ttypek => $ttypev) {
                    $tax_type .= $ttypev[TYPE_OF_TAX::TYPE_OF_TAX];
                    $tax_type .= ($ttypek == ((count($getc_tax_type)) - 1)) ? '' : ', ';
                  }
                }
                // }
              }
            }
            //getting Audit & Tax Type History [end]
            $com_name = '<span>' . ucwords($v[COMPANIES::COMPANY_NAME]);
            $com_name .= ($v[COMPANIES::TAX_IDENTIFICATION_NUMBER] != null) ? ' (<strong>TIN: </strong>' . $v[COMPANIES::TAX_IDENTIFICATION_NUMBER] . ')' : '';
            $com_name .= '</span>';
            $case_code = ($v[COMPANIES::CASE_CODE] != null) ? altRealEscape($v[COMPANIES::CASE_CODE]) : EMPTY_VALUE;
            $company_row .= '<tr id="company_list_' . $v[COMPANIES::ID] . '">
                <td>' . ($k + 1) . '</td>
                <td class="text-left">' . $com_name . '</td>
                <td>' . $industry_type . '</td>
                <td>' . $case_code . '</td>
                <td>' . $audit_type . '</td>
                <td>' . $tax_type . '</td>
                <td class="audit_btn">
                  ' . $audit_btn . '
                </td>
            </tr>';
          }
          break;
      }
    }
  } else {
    $company_row = '<tr class="animated fadeInDown">
      <td colspan="7">
          <div class="alert alert-danger" role="alert">
              No Audits found !
          </div>
      </td>
    </tr>';
  }
  if (count($getAuditTypes) > 0) {
    foreach ($getAuditTypes as $atk => $atv) {
      $auditTypeOptions .= '<option value="' . $atv[AUDIT_TYPES::ID] . '">' . $atv[AUDIT_TYPES::AUDIT_TYPE] . '</option>';
    }
  }
  if (count($getTaxTypes) > 0) {
    foreach ($getTaxTypes as $ttk => $ttv) {
      $taxTypeOptions .= '<option value="' . $ttv[TYPE_OF_TAX::ID] . '">' . $ttv[TYPE_OF_TAX::TYPE_OF_TAX] . '</option>';
    }
  }
?>

  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item pr-2" role="presentation">
      <button class="nav-link audit_nav active" id="audit-tab" data-toggle="tab" data-target="#audit" type="button" role="tab" aria-controls="audit" aria-selected="true">Audits</button>
    </li>
    <li class="nav-item pr-2" role="presentation">
      <button class="nav-link audit_nav" id="query-tab" data-toggle="tab" data-target="#query" type="button" role="tab" aria-controls="query" aria-selected="false">Query</button>
    </li>
    <li class="nav-item pr-2" role="presentation">
      <button class="nav-link audit_nav" id="notice-tab" data-toggle="tab" data-target="#notice" type="button" role="tab" aria-controls="notice" aria-selected="false">Notice</button>
    </li>
    <li class="nav-item pr-2" role="presentation">
      <button class="nav-link audit_nav" id="position-paper-tab" data-toggle="tab" data-target="#position-paper" type="button" role="tab" aria-controls="position-paper" aria-selected="false">Position Papers</button>
    </li>
    <li class="nav-item pr-2" role="presentation">
      <button class="nav-link audit_nav" id="assessment-tab" data-toggle="tab" data-target="#assessment" type="button" role="tab" aria-controls="assessment" aria-selected="false">Assessment Section</button>
    </li>
    <li class="nav-item pr-2" role="presentation">
      <button class="nav-link audit_nav" id="memo-tab" data-toggle="tab" data-target="#memo" type="button" role="tab" aria-controls="memo" aria-selected="false">Memo Section</button>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">
    <!-- Audit Companies list starts -->
    <div class="tab-pane fade" id="memo" role="tabpanel" aria-labelledby="memo-tab">
      <div class="card mt-5">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <?= getSpinner(true, "memo_table_loader"); ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover text-center memo_table" id="memo_table">
                  <thead class="text-center table-warning">
                    <tr style="text-transform: uppercase; font-size: 12px;">
                      <th>Sl.</th>
                      <th>Memo No</th>
                      <th>Total No. Of Query</th>
                      <th>Date of Issue</th>
                      <th>Days to reply</th>
                      <th>Due Reply Date</th>
                    </tr>
                  </thead>
                  <tbody>
                      <tr class="animated fadeInDown">
                        <td colspan="6">
                            <div class="alert alert-danger" role="alert">
                                No Memos found !
                            </div>
                        </td>
                      </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade show active" id="audit" role="tabpanel" aria-labelledby="audit-tab">
      <div class="card mt-5">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <?= getSpinner(true, "audits_table_loader"); ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover text-center audits_table data-table" id="audits_table">
                  <thead class="text-center table-warning">
                    <tr style="text-transform: uppercase; font-size: 12px;">
                      <th>Sl.</th>
                      <th>Tax Payer</th>
                      <th>Industry</th>
                      <th>case code</th>
                      <th>Audit Type</th>
                      <th>Tax Type</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?= $company_row; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Audit Companies list ends -->
    <!-- Query Section starts -->
    <div class="tab-pane fade" id="query" role="tabpanel" aria-labelledby="query-tab">
      <div class="card mt-5">
        <div class="card-body">
          <div class="row pt-3">
            <div class="col-lg-7 col-md-7 col-sm-7">
              <!-- <label class="form_label">Select Company</label> -->
              <select class="form-control select_company" id="query_section_company_select">
                <?= $active_audit_companies; ?>
              </select>
            </div>
            <?php if($_SESSION[USER_TYPE]==EMPLOYEE): ?>
            <div class="col-lg-5 col-md-5 col-sm-5 text-right query_nav_btn">
              <button type="button" class="btn btn-sm btn-primary" style="cursor: not-allowed !important;" id="add_query_btn" onclick="addQueryNav();" disabled>
                <small><i class="fas fa-plus"></i>&nbsp;Add Query</small>
              </button>
            </div>
            <?php endif; ?>
          </div>
          <div class="row mt-5">
            <div class="col-md-12 audit_closed_alert text-center" style="display:none;">
              <div class="alert alert-danger" role="alert">
                  Audit Closed !
              </div>
            </div>
            <div class="col-md-12 col-sm-12 col-lg-12 query_table_col" id="query_table_col" style="display: none;">
              <?= getSpinner(true, 'query_table_loader') ?>
              <div class="row print_div mb-2" id="query_print_div">
                <div class="col-md-12 text-center">
                  <h6 class="text-center main_heading">Query Report</h6>
                </div>
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-sm alert-info no-print refresh_btn" onclick="getQueryTableData();getAllSectionStats();"><small><i class="fas fa-sync"></i>&nbsp;Refresh</small></button>
                  <button type="button" class="btn btn-sm btn-info no-print" onclick="printDiv('query_table_col')"><small><i class="fas fa-print"></i>&nbsp;Print</small></button>
                </div>
              </div>
              <div class="table-responsive parentTableDiv" id="query_table_div">
                <table class="table table-sm table-striped table-hover table-bordered text-center query_table">
                  <thead class="text-center table-warning">
                    <tr style="text-transform: uppercase;">
                      <th>Sl.</th>
                      <th>Query No.</th>
                      <th>Total No. of Query</th>
                      <th>Audit Type</th>
                      <th>Tax Type</th>
                      <th>Date of Issue</th>
                      <th>Date of Reply</th>
                      <th>Due Reply date</th>
                      <th>Ext. by Days</th>
                      <th>Reply Status</th>
                      <th>No. of Query Solved</th>
                      <th>Query Status</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 col-lg-12 add_query_col" style="display: none;">
              <?= getSpinner(true, "queryLoader") ?>
              <fieldset class="fldset mt-3">
                <legend>Query Details</legend>
                <div class="row query_memo_select_section">
                  <div class="col-md-7"></div>
                  <div class="col-md-3 text-left" id="query_memo_view_section"></div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-outline">
                      <label class="form_label" for="query_no">Query No.</label><?= getAsterics(); ?>
                      <input type="text" id="query_no" class="form-control" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-outline">
                      <label class="form_label" for="total_no_of_query">Total No. of Query</label><?= getAsterics(); ?>
                      <input type="text" id="total_no_of_query" class="form-control" />
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-4">
                    <div class="form-outline">
                      <label class="form_label" for="date_of_issue">Date of Issue</label><?= getAsterics(); ?>
                      <input type="date" id="date_of_issue" class="form-control" value="<?= getToday(false); ?>" readonly disabled onchange="getLastDateReply();" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-outline">
                      <label class="form_label" for="days_to_reply">Days to Reply</label><?= getAsterics(); ?>
                      <input type="number" class="form-control" id="days_to_reply" onkeyup="getLastDateReply();" />
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-outline">
                      <label class="form_label" for="end_date_of_reply">Last date of Reply</label><?= getAsterics(); ?>
                      <input type="date" class="form-control" id="end_date_of_reply" readonly disabled />
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-6">
                    <div class="form-outline" id="query_audit_type_field">
                      <label class="form_label" for="audit_type_id">Audit Type</label><?=getAsterics();?>
                      <select id="audit_type_id" class="form-control">
                        <?= $auditTypeOptions ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-outline" id="query_tax_type_field">
                      <label class="form_label" for="type_of_tax_id">Tax Type</label><?=getAsterics();?>
                      <select id="type_of_tax_id" class="form-control">
                        <?= $taxTypeOptions ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-12 col-lg-12 col-sm-12 text-right">
                    <button class="btn btn-sm btn-success" id="save_query_btn"><i class="fas fa-plus"></i>&nbsp;Add</button>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Query Section ends -->
    <!-- Notice Section starts -->
    <div class="tab-pane fade" id="notice" role="tabpanel" aria-labelledby="notice-tab">
      <div class="card mt-5">
        <div class="card-body">
          <div class="row mt-3">
            <div class="col-lg-7 col-md-7 col-sm-7">
              <label class="form_label" for="notice_section_company_select">Select Tax Payer</label><?=getAsterics();?>
              <select class="form-control select_company" id="notice_section_company_select">
                <?= $active_audit_companies; ?>
              </select>
            </div>
          </div>
          <?php if($_SESSION[USER_TYPE]==EMPLOYEE): ?>
          <div class="row mt-2">
            <div class="col-sm-10 col-md-10 col-lg-10 text-right">
              <!-- <h4 class="text-center">Issue & Manage Notices Here</h4> -->
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 text-right" id="list_nav_btn">
              <button class="btn btn-sm btn-primary" name="add" style="cursor: not-allowed;" disabled onclick="changeNavigateBtn('notice');"><small><i class="fas fa-plus"></i>&nbsp;Add</small></button>
            </div>
          </div>
          <?php endif; ?>
          <?= getSpinner(true, "NoticeSecLoader"); ?>
          <div id="add_notice_sec" class="mt-2" style="display: none;">
            <fieldset class="fldset">
              <legend>Issue Notice</legend>
              <div class="notice_form_section">
                <div class="row mt-2">
                  <div class="col-md-6 col-lg-6 col-sm-6">
                    <div class="form-outline notice_query_select_sec">
                      <label class="form_label" for="notice_query_select">Select Query</label><?= getAsterics(); ?>
                      <select id="notice_query_select" class="form-control multiple" name="notice_query_select[]" multiple="multiple">
                        <option value="0" disabled>--- Please Select Company First ---</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-sm-6">
                    <label class="form_label" for="notice_no">Notice No.</label><?= getAsterics(); ?>
                    <input type="text" class="form-control" name="notice_no" id="notice_no" />
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-4 col-lg-4 col-sm-4">
                    <label class="form_label" for="notice_date">Notice Issue Date.</label><?= getAsterics(); ?>
                    <input type="date" class="form-control" name="notice_date" id="notice_date" readonly disabled value="<?= getToday(false) ?>" onchange="getLastDateNoticeReply();" />
                  </div>
                  <div class="col-md-4 col-lg-4 col-sm-4">
                    <label class="form_label" for="notice_reply_days">Days to Reply</label><?= getAsterics(); ?>
                    <input type="text" class="form-control" name="notice_reply_days" id="notice_reply_days" onkeyup="getLastDateNoticeReply();" />
                  </div>
                  <div class="col-md-4 col-lg-4 col-sm-4">
                    <label class="form_label" for="notice_reply_date">Last Date of Reply</label><?= getAsterics(); ?>
                    <input type="date" class="form-control" name="notice_reply_date" id="notice_reply_date" readonly disabled value="<?= getToday(false) ?>" />
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-12 col-lg-12 col-sm-12 text-right">
                    <button class="btn btn-sm btn-success" style="cursor: not-allowed;" type="button" id="add_notice_btn" disabled>
                      &nbsp;<i class="fas fa-save"></i><small>Save</small>
                    </button>
                  </div>
                </div>
              </div>
            </fieldset>
          </div>
            <div class="col-md-12 audit_closed_alert text-center mt-2" style="display:none;">
              <div class="alert alert-danger" role="alert">
                  Audit Closed !
              </div>
            </div>
          <div id="view_notice_sec" style="display: none;" class="mt-3">
            <div class="col-md-12 text-center mt-2">
              <h6 class="text-center main_heading">NOTICE REPORT</h6>
            </div>
            <div class="col-md-12 text-right mt-2">
              <button type="button" class="btn btn-sm alert-info no-print refresh_btn" onclick="getNoticeTable();getAllSectionStats();"><small><i class="fas fa-sync"></i>&nbsp;Refresh</small></button>
              <button type="button" class="btn btn-sm btn-info no-print" onclick="printDiv('view_notice_sec')"><small><i class="fas fa-print"></i>&nbsp;Print</small></button>
            </div>
            <div class="table-responsive parentTableDiv mt-2">
              <table class="table table-sm table-striped table-hover table-bordered text-center notice_table">
                <thead class="text-center table-warning">
                  <tr style="text-transform: uppercase;">
                    <th>Sl.</th>
                    <th>Notice No.</th>
                    <th>Queries</th>
                    <th>Date of Issue</th>
                    <th>Due Reply date</th>
                    <th>Days to Reply</th>
                    <th>Date of Reply</th>
                    <th>Reply Status</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Notice Section ends -->
    <!-- Position-paper Section starts -->
    <div class="tab-pane fade" id="position-paper" role="tabpanel" aria-labelledby="position-paper-tab">
      <div class="card mt-5">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-12">
              <label class="form_label">Select Tax Payer</label>
              <select class="form-control select_company" id="position_section_company_select">
                <?= $active_audit_companies; ?>
              </select>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12" id="position_paper_query_select_sec"></div>
          </div>
          <?php if($_SESSION[USER_TYPE]==EMPLOYEE): ?>
          <div class="row mt-2">
            <div class="col-sm-10 col-md-10 col-lg-10 text-right">
              <!-- <h4 class="text-center">Issue & Manage Notices Here</h4> -->
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 text-right list_nav_btn" id="position-list-nav">
              <button class="btn btn-sm btn-primary" name="add" disabled style="cursor: not-allowed;" onclick="changePositionNavigateBtn();"><small><i class="fas fa-plus"></i>&nbsp;Issue</small></button>
            </div>
          </div>
          <?php endif;?>
          <div id="info-position-sec" class="mt-2">
              <!-- <h6><em>Please Select a Company First <?=getAsterics();?></em></h6> -->
          </div>
          <?=getSpinner(true, 'positionPaperLoader')?>
          <div id="query_force_close_sec" style="display: none;"></div>
          <div class="col-md-12 audit_closed_alert text-center mt-2" style="display:none;">
            <div class="alert alert-danger" role="alert">
                Audit Closed !
            </div>
          </div>
          <div id="view-position-sec" class="mt-2" style="display: none;">
              <div class="row print_div mb-2" id="query_print_div">
                <div class="col-md-12 text-center">
                  <h6 class="text-center main_heading">POSITION PAPERS REPORT</h6>
                </div>
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-sm alert-info no-print refresh_btn" onclick="getPositionTableData();getAllSectionStats();"><small><i class="fas fa-sync"></i>&nbsp;Refresh</small></button>
                  <button type="button" class="btn btn-sm btn-info no-print" onclick="printDiv('view-position-sec')"><small><i class="fas fa-print"></i>&nbsp;Print</small></button>
                </div>
              </div>
              <div class="table-responsive mt-2 parentTableDiv" id="position_table_print_div">
              <table class="table table-sm table-striped table-hover table-bordered text-center position_table">
                <thead class="text-center table-warning">
                  <tr style="text-transform: uppercase;">
                    <th>Sl.</th>
                    <th>Ref. No.</th>
                    <th>Query No.</th>
                    <th>Date of Issue</th>
                    <th>Due Reply date</th>
                    <th>Days to Reply</th>
                    <th>Date of Reply</th>
                    <th>EXT. BY DAYS</th>
                    <th>REPLY STATUS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td><?=getToday(false, 'd/m/Y');?></td>
                    <td><?=getToday(false, '30/m/Y');?></td>
                    <td>15</td>
                    <td><span class="badge badge-danger">Pending</span></td>
                    <td><span class="badge badge-primary cursor-pointer" onclick="alert('Under Development');"><i class="fas fa-plus"></i>Add Ext.</span></td>
                    <td><span class="badge badge-danger">Pending</span>&nbsp;<span class="badge badge-primary cursor-pointer" onclick="alert('Under Development');"><i class="fas fa-plus"></i>Add Rep.</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div id="add-position-sec" class="mt-2" style="display: none;">
              <fieldset class="fldset">
                <legend>Position Paper Details</legend>
                <div class="row mt-2">
                  <div class="col-md-4">
                    <label class="form_label" for="position_paper_ref_no">Ref. No.</label><?=getAsterics();?>
                    <input type="text" class="form-control" id="position_paper_ref_no" value="" placeholder="Position Paper Reference No." />
                  </div>
                  <div class="col-md-4">
                    <label class="form_label" for="position_paper_issue_date">Date of Issue</label><?=getAsterics();?>
                    <input type="date" class="form-control" id="position_paper_issue_date" readonly disabled value="<?=getToday(false)?>" />
                  </div>
                  <div class="col-md-4">
                    <label class="form_label" for="position_paper_submit_date">Submission Date</label><?=getAsterics();?>
                    <input type="date" class="form-control" id="position_paper_submit_date" />
                  </div>
                  <div class="col-md-12 text-right">
                    <button class="btn btn-sm btn-primary mt-4" type="button" id="position_paper_add_btn"><i class="fas fa-upload"></i>&nbsp;Issue</button>
                  </div>
                </div>
              </fieldset>
          </div>
        </div>
      </div>
    </div>
    <!-- Position-paper Section ends -->
    <!-- Assessment Section starts -->
    <div class="tab-pane fade" id="assessment" role="tabpanel" aria-labelledby="assessment-tab">
      <div class="card mt-5">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-7">
              <label class="form_label">Select Tax Payer</label>
              <select class="form-control select_company" id="assessment_section_company_select">
                <?= $active_audit_companies; ?>
              </select>
            </div>
            <div id="assessment_query_select_sec" class="col-lg-5 col-md-5 col-sm-5"></div>
          </div>
          <?php if($_SESSION[USER_TYPE]==EMPLOYEE): ?>
          <div class="row mt-2">
            <div class="col-sm-10 col-md-10 col-lg-10 text-right">
              <!-- <h4 class="text-center">Issue & Manage Notices Here</h4> -->
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 text-right list_nav_btn" id="assessment-list-nav">
              <button class="btn btn-sm btn-primary" name="add" disabled style="cursor: not-allowed;" onclick="changeAssessmentNavigateBtn();"><small><i class="fas fa-plus"></i>&nbsp;Issue</small></button>
            </div>
          </div>
          <?php endif; ?>
          <div id="info-assessment-sec" class="mt-2">
              <!-- <h6><em>Please Select a Company First <?=getAsterics();?></em></h6> -->
          </div>
          <?=getSpinner(true, 'assessmentLoader')?>
          <div class="col-md-12 audit_closed_alert text-center mt-2" style="display:none;">
            <div class="alert alert-danger" role="alert">
                Audit Closed !
            </div>
          </div>
          <div id="add-assessment-sec" class="mt-2" style="display: none;">
            <fieldset class="fldset">
                <legend>Assessment Details</legend>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form_label" for="assessment_ref_no">Ref. No.</label><?=getAsterics();?>
                        <input type="text" class="form-control" id="assessment_ref_no" required />
                    </div>
                    <div class="col-md-4">
                      <label class="form_label" for="assessment_date_of_issue">Date of Issue</label><?=getAsterics();?>
                      <input type="date" class="form-control" id="assessment_date_of_issue" readonly disabled required value="<?=getToday(false);?>" />
                    </div>
                    <div class="col-md-4">
                      <label class="form_label" for="assessment_claimable_tax_amount">Base Tax Claimed (PGK)</label><?=getAsterics();?>
                      <input type="text" class="form-control" id="assessment_claimable_tax_amount" required value="<?=DEFAULT_AMOUNT?>" />
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-md-6">
                      <label class="form_label" for="assessment_claimable_penalty_amount">Penalty Claimed (PGK)</label><?=getAsterics();?>
                      <input type="text" class="form-control" id="assessment_claimable_penalty_amount" required value="<?=DEFAULT_AMOUNT?>" />
                    </div>
                    <div class="col-md-6">
                      <label class="form_label" for="assessment_omitted_income_amount">Omitted income</label><?=getAsterics();?>
                      <input type="text" class="form-control" id="assessment_omitted_income_amount" required value="<?=DEFAULT_AMOUNT?>" />
                    </div>
                  </div>
                  <div class="row mt-1">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-sm btn-primary mt-4" id="assessment_issue_btn"><i class="fas fa-upload"></i>&nbsp;Issue</button>
                    </div>
                </div>
            </fieldset>
          </div>
          <div id="view-assessment-sec" class="mt-2" style="display: none;">
              <div class="row print_div mb-2" id="assessment_print_div">
                <div class="col-md-12 text-center">
                  <h6 class="text-center main_heading">ASSESSMENTS REPORT</h6>
                </div>
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-sm alert-info no-print refresh_btn" onclick="getAssessmentTable();getAllSectionStats();"><small><i class="fas fa-sync"></i>&nbsp;Refresh</small></button>
                  <button type="button" class="btn btn-sm btn-info no-print" onclick="printDiv('view-assessment-sec')"><small><i class="fas fa-print"></i>&nbsp;Print</small></button>
                </div>
              </div>
                <div class="table-responsive mt-2" id="assessment_table_div">
                <table class="table table-sm table-striped table-hover table-bordered text-center assessment_table">
                  <thead class="text-center table-warning">
                    <tr style="text-transform: uppercase;">
                      <th>Sl.</th>
                      <th>Ref. No.</th>
                      <th>Position Paper</th>
                      <th>Query No.</th>
                      <th>Base Tax Claimed (PGK)</th>
                      <th>Penalty Claimed (PGK)</th>
                      <th>Omitted Income</th>
                      <th>Date of Issue</th>
                      <th>Days Count</th>
                      <th>STATUS</th>
                      <!-- <th>open/close</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>1</td>
                      <td>REF1525875645</td>
                      <td><?=getToday(false, '30/m/Y');?></td>
                      <td><span class="badge badge-danger">Open</span></td>
                      <td style="cursor: pointer;">
                        <div class="custom-control custom-switch noselect">
                            <input type="checkbox" checked="" class="custom-control-input" id="assessment_active_11" onclick="alert('Under Development');">
                            <label class="custom-control-label text-success" for="assessment_active_11"></label>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Assessment Section ends -->
    <!-- Objection Section starts -->
    <div class="tab-pane fade" id="objection" role="tabpanel" aria-labelledby="objection-tab">
      <div class="card mt-5">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-7">
              <label class="form_label">Select Tax Payer</label>
              <select class="form-control select_company" id="objection_section_company_select">
                <?= $active_audit_companies; ?>
              </select>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-sm-10 col-md-10 col-lg-10 text-right">
              <!-- <h4 class="text-center">Issue & Manage Objection Here</h4> -->
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 text-right list_nav_btn" id="objection-list-nav">
              <button class="btn btn-sm btn-primary" name="add" disabled style="cursor: not-allowed;" onclick="changeObjectionNavigateBtn();"><i class="fas fa-upload"></i>&nbsp;Issue</button>
            </div>
          </div>
          <div id="info-objection-sec" class="mt-2">
              <!-- <h6><em>Please Select a Company First <?=getAsterics();?></em></h6> -->
          </div>
          <?=getSpinner(true, 'objectionLoader')?>
          <div id="view-objection-sec" class="mt-2" style="display: none;">
              <div class="table-responsive mt-2">
                <table class="table table-sm table-striped table-hover table-bordered text-center objection_table">
                  <thead class="text-center table-warning">
                    <tr style="text-transform: uppercase;">
                      <th>Sl.</th>
                      <th>Date of Objection</th>
                      <th>Claimable Amount</th>
                      <th>Amount payable date</th>
                      <th>pending Amount</th>
                      <th>Amount received date</th>
                      <th>Amount Date of Transfer</th>
                      <th>STATUS</th>
                      <th>open/close</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
          </div>
          <div id="add-objection-sec" class="mt-2" style="display: none;">
            <fieldset class="fldset">
                <legend>Objection Details</legend>
                <div class="row">
                    <div class="col-md-4">
                      <label class="form_label" for="objection_date">Date of Objection</label><?=getAsterics();?>
                      <input type="date" class="form-control" id="objection_date" required value="<?=getToday(false);?>" />
                    </div>
                    <div class="col-md-4">
                        <label class="form_label" for="objection_amt">Claimable Amount.</label><?=getAsterics();?>
                        <input type="text" class="form-control" id="objection_amt" required />
                    </div>
                    <div class="col-md-4">
                      <label class="form_label" for="objection_amaount_payable_date">Amount Payable</label><?=getAsterics();?>
                      <input type="date" class="form-control" id="objection_amaount_payable_date" required value="<?=getToday(false);?>" />
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-sm btn-primary mt-4" id="objection_issue_btn"><i class="fas fa-upload"></i>&nbsp;Issue</button>
                    </div>
                </div>
            </fieldset>
          </div>

        </div>
      </div>
    </div>
    <!-- Objection Section ends -->
  </div>

  <div class="card mt-3" id="auditStatCard">
    <div class="card-body">
      <?=getSpinner(true,'auditAllSectionStatLoader');?>
      <div class="row">
        <div class="col-md-12">
          <h6 class="text-left sub_heading font-weight-bold">Audit Overview&nbsp;&nbsp;<i class="fas fa-chart-bar text-primary"></i>&nbsp;<i class="fas fa-chart-line text-success"></i></h6>
        </div>
        <div class="col-md-12 mt-4" id="auditStatCard_data">
          
        </div>
      </div>
    </div>
  </div>

  <!-- Audit Close modal [For Auditors]-->
<div class="modal animated shake" id="auditCloseReqAuditor_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="staticBackdropLabel">Reason Behind the Close Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#auditCloseReqAuditor_modal').modal('hide');">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=getSpinner(true, 'auditCloseReqAuditorLoader');?>
                <div class="row">
                  <div class="col-md-12">
                    <h6 class="text-center">No Assessment paper found on this audit ! If you still want to close the audit, Raise a audit close request.</h6>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-12">
                    <input type="hidden" id="audit_id_for_close" style="visibility: hidden; display: none;" value="0" />
                    <input type="text" class="form-control" id="auditCloseReqAuditorReason" placeholder="Write a valid reason ..." required />
                  </div>
                  <div class="col-md-12 text-right mt-2">
                    <button class="btn btn-sm btn-primary" type="button" id="auditCloseReqAuditorSubmit"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Submit</button>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php } ?>