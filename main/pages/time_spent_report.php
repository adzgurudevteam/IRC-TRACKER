<?php
function printContent()
{
  $oldestYear = 0;
  $year = $yrExt= 5;
  $yearOptions='<option value="0" disabled>----Select Year----</option>';
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
      $ysel='';
      if ($year==getToday(false,'Y')) {
        $ysel='selected';
      }
      $yearOptions.='<option value="'.$year.'" '.$ysel.'>'.$year.'</option>';
  endfor;
  $company_row = '';
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
    COMPANIES::STATUS => ACTIVE_STATUS,
    COMPANIES::ACTIVE => 1
  ]);
  if (count($getCompData) > 0) {
    foreach ($getCompData as $k => $v) {
      switch ($_SESSION[USER_TYPE]) {
        case EMPLOYEE:
          if (in_array($v[COMPANIES::ID], $assignedCompanyIds)) {
            $audit_closed = false;
            $audit_btn = '<button class="btn btn-sm btn-success" type="button" onclick="StartAudit(' . $v[COMPANIES::ID] . ');">Start</button>';
            $action_btn = '<span class="badge badge-info"><small>Audit not started!<small></span>';
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
                      <span class="badge badge-white cursor-pointer" onclick="viewMemoInfo(' . $v[COMPANIES::ID] . ')"><i class="fas fa-2x fa-info-circle"></i></span>
                    ';
                if (count($getAuditData) > 0) {
                  if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
                    $audit_btn .= '
                        &nbsp;
                        <button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>
                      ';
                    $getTimeReportData = getData(Table::AUDIT_TIME_SPENT_DATA, [
                      AUDIT_TIME_SPENT_DATA::TIME_IN_HRS
                    ], [
                      AUDIT_TIME_SPENT_DATA::DATE => getToday(false),
                      AUDIT_TIME_SPENT_DATA::AUDIT_ID => $getAuditData[0][AUDITS_DATA::ID],
                      AUDIT_TIME_SPENT_DATA::COMPANY_ID => $v[COMPANIES::ID],
                      AUDIT_TIME_SPENT_DATA::AUDITOR_ID => $_SESSION[RID],
                      AUDIT_TIME_SPENT_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                    ]);
                    if (count($getTimeReportData) > 0) {
                      $action_btn = '
                      <div class="auditorHoursInputDiv">
                                        <input type="number" class="form-control-sm" id="audit_time_input_' . $v[COMPANIES::ID] . '" disabled readonly value="' . $getTimeReportData[0][AUDIT_TIME_SPENT_DATA::TIME_IN_HRS] . '" placeholder="Write Hours..."/>
                                      </div>
                                        ';
                    } else {
                      $action_btn = '
                      <div class="auditorHoursInputDiv">                                        
                        <input type="number" class="form-control-sm" id="audit_time_input_' . $v[COMPANIES::ID] . '" placeholder="Write Hours..."/>
                        <span onclick="saveTimeReportAuditor(' . $v[COMPANIES::ID] . ',' . $getAuditData[0][AUDITS_DATA::ID] . ');" class="badge badge-success cursor-pointer"><small>save</small></span>
                      </div>
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
                    $audit_btn = '<button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>';
                  } else {
                    $audit_btn = '<span class="badge badge-info"><small>Audit Closed!<small></span>';
                    $action_btn = '<span class="badge badge-info"><small>Audit Closed!<small></span>';
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
                  $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
                  $audit_btn .= '<br><button class="btn btn-sm btn-danger" type="button" onclick="closeAudit(' . $v[COMPANIES::ID] . ');">Close</button>';
                  $getTimeReportData = getData(Table::AUDIT_TIME_SPENT_DATA, [
                    AUDIT_TIME_SPENT_DATA::TIME_IN_HRS
                  ], [
                    AUDIT_TIME_SPENT_DATA::DATE => getToday(false),
                    AUDIT_TIME_SPENT_DATA::AUDIT_ID => $getAuditData[0][AUDITS_DATA::ID],
                    AUDIT_TIME_SPENT_DATA::COMPANY_ID => $v[COMPANIES::ID],
                    AUDIT_TIME_SPENT_DATA::AUDITOR_ID => $_SESSION[RID],
                    AUDIT_TIME_SPENT_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                  ]);
                  if (count($getTimeReportData) > 0) {
                    $action_btn = '<div class="auditorHoursInputDiv">
                          <input type="number" class="form-control-sm" id="audit_time_input_' . $v[COMPANIES::ID] . '" disabled readonly value="' . $getTimeReportData[0][AUDIT_TIME_SPENT_DATA::TIME_IN_HRS] . '" placeholder="Write Hours..."/>
                        </div>
                                          ';
                  } else {
                    $action_btn = '
                    <div class="auditorHoursInputDiv">                                        
                      <input type="number" class="form-control-sm" id="audit_time_input_' . $v[COMPANIES::ID] . '" placeholder="Write Hours..."/>
                      <span onclick="saveTimeReportAuditor(' . $v[COMPANIES::ID] . ',' . $getAuditData[0][AUDITS_DATA::ID] . ');" class="badge badge-success cursor-pointer"><small>save</small></span>
                    </div>
                    ';
                  }
                } else {
                  // check & show close status here
                  if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 2) {
                    $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
                    $audit_btn .= '<br/><span><strong class="text-danger">Closed: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_END_DATE]) . '</small></span>';
                    $action_btn = '<span><strong class="text-danger">Closed: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_END_DATE]) . '</small></span>';
                    $action_btn .= '
                    <div class="auditorHoursInputDiv">                                        
                      <input type="number" class="form-control-sm" readonly disabled id="audit_time_input_' . $v[COMPANIES::ID] . '" placeholder="Write Hours..."/>
                    </div>
                    ';
                  } else {
                    $audit_btn = '<button class="btn btn-sm btn-success" type="button" onclick="StartAudit(' . $v[COMPANIES::ID] . ');">Start</button>';
                    $action_btn = '<span class="badge badge-info"><small>Audit not started!<small></span>';
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
            // $action_btn='
            //   <input type="text" class="form-control form-control-sm" id="audit_time_input" placeholder="Write Hours..."/>
            // ';

            $company_row .= '<tr id="company_list_' . $v[COMPANIES::ID] . '">
                                <td>' . ($k + 1) . '</td>
                                <td class="text-left">' . $com_name . '</td>
                                <td class="audit_btn">
                                  ' . $action_btn . '
                                </td>
                            </tr>';
          }
          break;
      }
    }
  } else {
    $company_row = '<tr class="animated fadeInDown">
              <td colspan="5">
                  <div class="alert alert-danger" role="alert">
                      No Companies found !
                  </div>
              </td>
            </tr>';
  }






  //Getting Auditor Attendance Full view data

  $auditor_id=$_SESSION[RID];
  $month=getToday(false,'m');
  $year=getToday(false,'Y');

  $tableHead='
  <thead class="text-center table-warning">
      <tr>
          <th>sl.</th>
          <th>Taxpayer</th>
          <th>date allocated</th>';
  $fullHtml='
  <tbody>
  ';
  $otherActHtml='';
  $leaveHtml='
  <tr>
      <td colspan="3">LEAVE (incl. Pub. Hols: Excl. Cas. Leave)</td>
  ';
  $trainingHtml='
  <tr>
      <td colspan="3">TRAINING (given or received)</td>
  ';
  $otherDutyHtml='
  <tr>
      <td colspan="3">OTHER DUTIES ( Description in diary)</td>
  ';
  $otherAuditHtml='
  ';
  $cur_year = date("Y");
  $cur_month = date("m");
  $dateObj            = DateTime::createFromFormat('!m', $cur_month);
  $bill_monthName     = $dateObj->format('F');
  // if ($year > $cur_year) {
  //     $response['error'] = true;
  //     $response['message'] = 'You Cannot Select Year Greater Than ' . $cur_year;
  //     sendRes();
  // } elseif (($month > $cur_month)) {
  //     if ($year >= $cur_year) {
  //         $response['error'] = true;
  //         $response['message'] = 'You Cannot Select Month Greater Than ' . $bill_monthName . ' In The Year ' . $cur_year . '';
  //         sendRes();
  //     }
  // }
  // $month = ($month < 10) ? '0' . $month : $month;
  $firstday = date($year . '-' . $month . '-01');
  $response['firstday'] = $firstday;
  $lastday = "";
  if ($month == date("m") && $year == date('Y')) {
      $lastday = date($year . '-' . $month . "-d");
  } else {
      // $lastday = date_format(date_create(date(date('t', strtotime($firstday)) . '-' . $month . '-' . $year)), "Y-m-d");
      $dt = DateTime::createFromFormat("Y-m-d", $firstday);
      $lastday = date($year . '-' . $month . '-t', $dt->getTimestamp());
  }
  $singleHrDataArr=[];
  $colspan=3;
  // while (strtotime($firstday) <= strtotime($lastday)) {
  //     $day_num = date('d/m/Y', strtotime($firstday));
  //     $id_date = date('Ymd', strtotime($firstday));
  //     // $table_tr_id['id_date'] = $id_date;
  //     $curr_date = date("Y-m-d", strtotime($firstday));
  //     $day_name = date('l', strtotime($firstday));
  //     $firstday = date("Y-m-d", strtotime("+1 day", strtotime($firstday)));
  //     $tableHead.='
  //     <th>' . substr($day_name, 0, 3) . '(' . getFormattedDateTime($curr_date,'d') . ')</th>
  //     ';
  //     $singleHrDataArr[$curr_date]=substr($day_name, 0, 3);
  //     $colspan++;
  // }
  while (strtotime($lastday) >= strtotime($firstday)) {
      $day_num = date('d/m/Y', strtotime($lastday));
      $id_date = date('Ymd', strtotime($lastday));
      // $table_tr_id['id_date'] = $id_date;
      $curr_date = date("Y-m-d", strtotime($lastday));
      $day_name = date('l', strtotime($lastday));
      $lastday = date("Y-m-d", strtotime("-1 day", strtotime($lastday)));
      $tableHead.='
      <th>' . substr($day_name, 0, 3) . '(' . getFormattedDateTime($curr_date,'d') . ')</th>
      ';
      $singleHrDataArr[$curr_date]=substr($day_name, 0, 3);
      $colspan++;
  }
  $getCompData = getData(Table::COMPANIES, [
      COMPANIES::COMPANY_NAME,
      COMPANIES::TAX_IDENTIFICATION_NUMBER,
      COMPANIES::CASE_CODE,
      COMPANIES::CREATED_AT,
      COMPANIES::ID
  ], [
      COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
      COMPANIES::STATUS => ACTIVE_STATUS
  ]);
  $durrExactMonArr=[];
  $hrsArr=$leaveArr=$trainArr=$othDutyArr=[];
  if (count($getCompData) > 0) {
      $sl=1;
      $greater12Months=$greater6Months=$less6Months=$durrMonth=$durrExactMon=$totOwnCase=$totOtherCase=0;
      $leaveHours=$trainingHours=$othDutyHours=0;
      $ccount=1;
      foreach ($getCompData as $k => $v) {
        if (in_array($v[COMPANIES::ID], $assignedCompanyIds)) {
          $name = altRealEscape($v[COMPANIES::COMPANY_NAME]);
          $name .= ($v[COMPANIES::TAX_IDENTIFICATION_NUMBER]!=null)?'<br><small>[ <b>TIN: </b>'.altRealEscape($v[COMPANIES::TAX_IDENTIFICATION_NUMBER]).' ]</small>':'';
          $ccode = ($v[COMPANIES::CASE_CODE]!=null)?altRealEscape($v[COMPANIES::CASE_CODE]):EMPTY_VALUE;
          $date_allocated=$date_commence=$auditsDuration=EMPTY_VALUE;
          $auditTax=$auditPenalty=$auditHours=$auditStartYear=$auditEndYear=$dayHr=0;
          $auditDur=0;
          $getAuditAssignData=getData(Table::COMPANY_ASSIGNED_DATA,[
              COMPANY_ASSIGNED_DATA::AUDITOR_ID,
              COMPANY_ASSIGNED_DATA::COMPANY_IDS,
              COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
              "DATE(".COMPANY_ASSIGNED_DATA::CREATED_AT.") as auditAssignedDate",
              "YEAR(".COMPANY_ASSIGNED_DATA::CREATED_AT.") as auditAssignedYear",
              COMPANY_ASSIGNED_DATA::USER_ID
          ],[
              COMPANY_ASSIGNED_DATA::AUDITOR_ID=>$auditor_id,
              COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$v[COMPANIES::ID],
              COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
              COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS
          ]);
          $getAuditHoursData=getData(Table::AUDIT_TIME_SPENT_DATA,[
              AUDIT_TIME_SPENT_DATA::TIME_IN_HRS,
              AUDIT_TIME_SPENT_DATA::DATE,
              AUDIT_TIME_SPENT_DATA::COMPANY_ID,
              // "SUM(".AUDIT_TIME_SPENT_DATA::TIME_IN_HRS.") as totalHrs"
          ],[
              AUDIT_TIME_SPENT_DATA::COMPANY_ID=>$v[COMPANIES::ID],
              AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$auditor_id,
              "YEAR(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$year,
              "MONTH(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$month,
              AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
          ]);
          if($ccount==1){
              $getLeaveHrs=getData(Table::AUDIT_TIME_SPENT_DATA,[
                  AUDIT_TIME_SPENT_DATA::LEAVE_HRS,
                  AUDIT_TIME_SPENT_DATA::TRAINING_HRS,
                  AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS,
                  AUDIT_TIME_SPENT_DATA::DATE
                  // "SUM(".AUDIT_TIME_SPENT_DATA::TIME_IN_HRS.") as totalHrs"
              ],[
                  AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$auditor_id,
                  "YEAR(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$year,
                  "MONTH(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$month,
                  AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
              ]);
              // rip($getAuditHoursData);
              if (count($getLeaveHrs)>0) {
                  foreach ($getLeaveHrs as $lhrsdk => $lhrsdv) {
                      $leaveHours+=$lhrsdv[AUDIT_TIME_SPENT_DATA::LEAVE_HRS];
                      $trainingHours+=$lhrsdv[AUDIT_TIME_SPENT_DATA::TRAINING_HRS];
                      $othDutyHours+=$lhrsdv[AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS];

                      $lshr=$lhrsdv[AUDIT_TIME_SPENT_DATA::DATE];
                      $leaveArr[$lshr]=$lhrsdv[AUDIT_TIME_SPENT_DATA::LEAVE_HRS];
                      $trainArr[$lshr]=$lhrsdv[AUDIT_TIME_SPENT_DATA::TRAINING_HRS];
                      $othDutyArr[$lshr]=$lhrsdv[AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS];
                  }
              }
          }
          if (count($getAuditHoursData)>0) {
              foreach ($getAuditHoursData as $hrsdk => $hrsdv) {
                  $auditHours+=$hrsdv[AUDIT_TIME_SPENT_DATA::TIME_IN_HRS];
                  $shr=$hrsdv[AUDIT_TIME_SPENT_DATA::DATE];
                  $cid=$hrsdv[AUDIT_TIME_SPENT_DATA::COMPANY_ID];
                  $hrsArr[$cid][$shr]=$hrsdv[AUDIT_TIME_SPENT_DATA::TIME_IN_HRS];
              }
          }
          if (count($getAuditAssignData)>0) {
              foreach ($getAuditAssignData as $aadk => $aadv) {
                  if ($aadv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==1) {
                      $date_allocated = getFormattedDateTime($aadv["auditAssignedDate"]);
                      $fullHtml.='
                      <tr>
                      <td>'.$sl.'</td>
                      <td>'.$name.'</td>
                      <td>'.$date_allocated.'</td>';
                      foreach ($singleHrDataArr as $shdk => $shdv) {
                          $dayHr=$dayLeave=$dayTrain=$dayOthDuty=0;
                          if (isset($hrsArr[$aadv[COMPANY_ASSIGNED_DATA::COMPANY_IDS]])) {
                              // echo $shdk;
                              $x=$hrsArr[$aadv[COMPANY_ASSIGNED_DATA::COMPANY_IDS]];
                              if (isset($x[$shdk])) {
                                  $dayHr=$hrsArr[$aadv[COMPANY_ASSIGNED_DATA::COMPANY_IDS]][$shdk];
                              }
                          } else {
                              $dayHr=0;
                          }
                          if($ccount==1){
                              if(isset($leaveArr[$shdk])){
                                  $dayLeave=$leaveArr[$shdk];
                              } else {
                                  $dayLeave=0;
                              }
                              if(isset($trainArr[$shdk])){
                                  $dayTrain=$trainArr[$shdk];
                              } else {
                                  $dayTrain=0;
                              }
                              if(isset($othDutyArr[$shdk])){
                                  $dayOthDuty=$othDutyArr[$shdk];
                              } else {
                                  $dayOthDuty=0;
                              }
                          }
                          // echo $dayHr.'<br>';
                          $bg_color='';
                          if (($shdv=='Sat')||($shdv=='Sun')) {
                              $bg_color='alert-success';
                          }
                          $fullHtml.='
                          <td class="'.$bg_color.'">'.$dayHr.'</td>
                          ';
                          if($ccount==1){
                              $leaveHtml.='
                                  <td class="'.$bg_color.'">'.$dayLeave.'</td>
                              ';
                              $trainingHtml.='
                                  <td class="'.$bg_color.'">'.$dayTrain.'</td>
                              ';
                              $otherDutyHtml.='
                                  <td class="'.$bg_color.'">'.$dayOthDuty.'</td>
                              ';
                          }
                      }
                      $fullHtml.='
                          <td class="alert-warning">'.$auditHours.'</td>
                      </tr>
                      ';
                      if($ccount==1){
                          $leaveHtml.='
                              <td class="alert-warning">'.$leaveHours.'</td>
                          </tr>
                          ';
                          $trainingHtml.='
                              <td class="alert-warning">'.$trainingHours.'</td>
                          </tr>
                          ';
                          $otherDutyHtml.='
                              <td class="alert-warning">'.$othDutyHours.'</td>
                          </tr>
                          ';
                      }
                      $totOwnCase+=$auditHours;
                  } else {
                      $otherAuditHtml.='
                      <tr>
                          <td>'.$sl.'</td>
                          <td>'.$name.'</td>
                          <td>'.$date_allocated.'</td>
                      ';
                      foreach ($singleHrDataArr as $shdk => $shdv) {
                          $dayHr=$dayLeave=$dayTrain=$dayOthDuty=0;
                          if (isset($hrsArr[$aadv[COMPANY_ASSIGNED_DATA::COMPANY_IDS]])) {
                              // echo $shdk;
                              $x=$hrsArr[$aadv[COMPANY_ASSIGNED_DATA::COMPANY_IDS]];
                              if (isset($x[$shdk])) {
                                  $dayHr=$hrsArr[$aadv[COMPANY_ASSIGNED_DATA::COMPANY_IDS]][$shdk];
                              }
                          } else {
                              $dayHr=0;
                          }
                          if($ccount==1){
                              if(isset($leaveArr[$shdk])){
                                  $dayLeave=$leaveArr[$shdk];
                              } else {
                                  $dayLeave=0;
                              }
                              if(isset($trainArr[$shdk])){
                                  $dayTrain=$trainArr[$shdk];
                              } else {
                                  $dayTrain=0;
                              }
                              if(isset($othDutyArr[$shdk])){
                                  $dayOthDuty=$othDutyArr[$shdk];
                              } else {
                                  $dayOthDuty=0;
                              }
                          }
                          // echo $dayHr.'<br>';
                          $bg_color='';
                          if (($shdv=='Sat')||($shdv=='Sun')) {
                              $bg_color='alert-success';
                          }
                          $otherAuditHtml.='
                              <td class="'.$bg_color.'">'.$dayHr.'</td>
                          ';
                          if($ccount==1){
                              $leaveHtml.='
                                  <td class="'.$bg_color.'">'.$dayLeave.'</td>
                              ';
                              $trainingHtml.='
                                  <td class="'.$bg_color.'">'.$dayTrain.'</td>
                              ';
                              $otherDutyHtml.='
                                  <td class="'.$bg_color.'">'.$dayOthDuty.'</td>
                              ';
                          }
                      }
                      $otherAuditHtml.='
                          <td class="alert-warning">'.$auditHours.'</td>
                      </tr>
                      ';
                      if($ccount==1){
                          $leaveHtml.='
                              <td class="alert-warning">'.$leaveHours.'</td>
                          </tr>
                          ';
                          $trainingHtml.='
                              <td class="alert-warning">'.$trainingHours.'</td>
                          </tr>
                          ';
                          $otherDutyHtml.='
                              <td class="alert-warning">'.$othDutyHours.'</td>
                          </tr>
                          ';
                      }
                      $totOtherCase+=$auditHours;
                  }
              }
          }
          $sl++;
          $ccount++;
        }
      }
      $tableHead.='
      <th>Total hours</th>
          </tr>
      </thead>';
      $fullHtml.='        
      <tr>
          <td colspan="'.($colspan+1).'" class="text-center"><h6><b>Assisting Other Auditors</b></h6></td>
      </tr>'.$otherAuditHtml;
      $fullHtml.='
      <tr class="bg-light"><td colspan="'.($colspan+1).'"><hr></td></tr>'.
      $leaveHtml.$trainingHtml.$otherDutyHtml.'
          <tr>
              <td colspan="'.$colspan.'"><h6><b>Total Time :</b></h6></td>
              <td>'.(($totOwnCase+$totOtherCase+$othDutyHours+$trainingHours)-($leaveHours)).'</td>
          </tr>
      </tbody>
      ';
      // $otherAuditHtml.='
      // </tbody>
      // ';
  } else {
      $fullHtml.='
      <tr class="animated fadeInDown">
          <td colspan="'.($colspan+1).'">
              <div class="alert alert-danger" role="alert">
                  No data found !
              </div>
          </td>
      </tr>
      ';
  }
?>
  <h4 class="text-center main_heading">Daily Report</h4>
  <h6 class="text-center sub_heading">View / input your daily hours record here</h6>

  <div class="card mt-5">
    <div class="card-body">
      <?= getSpinner(true, 'time_spent_loader'); ?>
      <div class="accordion" id="accordionExample">
        <div class="card">
          <div class="card-header" id="headingOne">
            <div class="row">
              <div class="col-md-6">
                <h6 class="mb-0">
                  <!-- <button class="btn btn-link btn-block text-left collapsed collapseCtrlBtn" style="font-size: 14px !important;" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> -->
                    <b class="text-danger"><?=getFormattedDateTime(getToday(false),LONG_DATE_FORMAT);?></b>
                  <!-- </button> -->
                </h6>
              </div>
              <div class="col-md-6 text-right">
              Click to open the input panel &nbsp;<i class="fas fa-long-arrow-alt-right"></i>
              <button class="btn btn-light collapsed collapseCtrlBtn" style="font-size: 14px !important;" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <i class="fas fa-sort-down"></i>
              </button>
              </div>
            </div>
          </div>

          <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
                <fieldset class="fldset">
                  <legend>Date Select</legend>
                  <div class="row">
                    <div class="col-md-6">
                      <label class="form_label" for="auditorAttDateSelect">Select Date</label><?=getAsterics()?>
                      <input type="date" class="form-control" id="auditorAttDateSelect" value="<?=getToday(false);?>" onchange="auditorDateChangeFunc();" />
                    </div>
                    <div class="col-md-6">
                      <div class="pt-3">
                        <small id="passwordHelpInline" class="text-muted">
                            <b class="text-danger">NOTE*</b>&nbsp; In the case of record <b>Leave, Training, or Other Duties,</b> change the date as needed. Otherwise, leave it as it is.
                        </small>
                      </div>
                    </div>
                  </div>
                </fieldset>
                <div class="row mt-2">
                  <div class="table-responsive mt-4">
                    <table class="table table-sm table-striped table-hover table-bordered text-center time_spent_table">
                      <thead class="text-center table-warning">
                        <tr style="text-transform: uppercase; font-size: 12px;">
                          <th>Sl.</th>
                          <th>Company Name</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?= $company_row; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="row mt-2" id="auditorOtherActInputArea">
                  <?= getSpinner(true, 'time_spent_input_loader'); ?>
                  <div class="col-md-12">
                    <fieldset class="fldset">
                      <legend>Record Other Activities</legend>
                      <div class="row">
                        <div class="col-md-6 text-right mt-2">
                          <label class="form_label" for="auditorLeaveInput">LEAVE <small>(incl. Pub. Hols: Excl. Cas. Leave)</small></label>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control" id="auditorLeaveInput" />
                        </div>
                        <div class="col-md-6 text-right mt-2">
                          <label class="form_label" for="auditorTrainingInput">TRAINING <small>(given or received)</small></label>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control" id="auditorTrainingInput" />
                        </div>
                        <div class="col-md-6 text-right mt-2">
                          <label class="form_label" for="auditorOtherDutyInput">OTHER DUTIES <small>( Description in diary)</small></label>
                        </div>
                        <div class="col-md-6">
                          <input type="text" class="form-control" id="auditorOtherDutyInput" />
                        </div>
                        <div class="col-md-12 text-right mt-2">
                            <button type="button" class="btn btn-sm btn-primary" id="saveAuditorOtherActBtn"><i class="fas fa-save"></i>&nbsp;Save</button>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                </div>


            </div>
          </div>
        </div>
      </div>

    <fieldset class="fldset mt-3">
      <?=getSpinner(true,'AudDailyReportLoader');?>
      <legend>Daily Report Data</legend>
      <div class="row">
        <div class="col-md-4">
            <label class="form_label" for="AudDailyReportMonthSelect">Select Month</label><?=getAsterics();?>
            <select class="form-control AudSelfDailyReportCritSelect" id="AudDailyReportMonthSelect">
              <option value="0" disabled>----Select Month----</option>
              <?php 
                foreach (ALL_MONTHS_NAME as $mk => $mv) {
                  $msel='';
                  $mval=($mk < 10) ? '0' . $mk : $mk;
                  if ($mk==getToday(false,'m')) {
                    $msel='selected';
                  }
                  echo '<option value="'.$mval.'" '.$msel.'>'.$mv.'</option>';
                }
              ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form_label" for="AudDailyReportYearSelect">Select Year</label><?=getAsterics();?>
            <select class="form-control AudSelfDailyReportCritSelect" id="AudDailyReportYearSelect">
                <?=$yearOptions;?>
            </select>
        </div>
      </div>
      <div class="row mt-3" id="AudDailyReportViewArea">
          <div class="col-md-12 text-right">
              <button type="button" class="btn btn-sm btn-info no-print" style="display: none;" onclick="printDiv('AudSelfDailyReport')"><small><i class="fas fa-print"></i>&nbsp;Print</small></button>
          </div>
          <div class="col-md-12">
          <h6 class="text-center main_heading">LTO  AUDITOR'S DIARY</h6>
              <div class="table-responsive mt-3 parentTableDiv">
                  <table class="table table-sm table-striped table-hover table-bordered text-center" id="AudSelfDailyReport">
                      <?=$tableHead;?>
                      <?=$fullHtml;?>
                  </table>
              </div>
          </div>
      </div>
    </fieldset>
    </div>
  </div>
<?php } ?>