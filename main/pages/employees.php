<?php 
function printContent()
{
    global $action;
    $getdname = getData(Table::DESIGNATIONS, [
        DESIGNATIONS::DESIGNATION_TITLE,
        DESIGNATIONS::ID
    ],[
        DESIGNATIONS::CLIENT_ID => $_SESSION[CLIENT_ID],
        DESIGNATIONS::STATUS => ACTIVE_STATUS,
        DESIGNATIONS::ACTIVE => 1
    ]);
    $getDeptData = getData(Table::DEPARTMENTS,[
        DEPARTMENTS::ID,
        DEPARTMENTS::DEPARTMENT_NAME
    ], [
        DEPARTMENTS::CLIENT_ID => $_SESSION[CLIENT_ID],
        DEPARTMENTS::STATUS => ACTIVE_STATUS
    ]);
    $designations = '<option value="0" disabled selected>Select Designations</option>';
    $departments = '<option value="0" disabled selected>Select Departments</option>';
    if (count($getdname)>0) {
        foreach ($getdname as $key => $dval) {
            $designations .= '<option value="'.$dval[DESIGNATIONS::ID].'">'. altRealEscape($dval[DESIGNATIONS::DESIGNATION_TITLE]) .'</option>';
        }
    }
    if (count($getDeptData)>0) {
        foreach ($getDeptData as $key => $v) {
            $departments .= '<option value="'. $v[DEPARTMENTS::ID] .'">'. altRealEscape($v[DEPARTMENTS::DEPARTMENT_NAME]) .'</option>';
        }
    }
    $not_in = [];
    $userTypeIn = [SADMIN, ADMIN, MANAGER];
    $reporting_manager = '<option value="0" selected disabled>--- Select Reporting Manager ---</option>';
    $reporting_times = '<option value="0" selected disabled>--- Select Reporting Time ---</option>';
    // echo $_SESSION[USER_TYPE];
    switch ($_SESSION[USER_TYPE]) {
        case ADMIN:
            //get Not In array
            $getnotin = getData(Table::USERS, [
                Users::EMPLOYEE_ID
            ], [
                Users::CLIENT_ID => $_SESSION[CLIENT_ID],
                Users::ACTIVE => 1,
                Users::STATUS => ACTIVE_STATUS,
                Users::USER_TYPE => ADMIN
            ]);
            // rip($getnotin);
            // echo '<br>'. $getnotin['sql'];
            // exit;
            if (count($getnotin)>0) {
                foreach ($getnotin as $k => $v) {
                    $not_in[] = $v[Users::EMPLOYEE_ID];
                }
            }
            break;
        case SADMIN:

            //get Not In array
            $getnotin = getData(Table::USERS, [
                Users::EMPLOYEE_ID
            ], [
                Users::CLIENT_ID => $_SESSION[CLIENT_ID],
                Users::ACTIVE => 1,
                Users::STATUS => ACTIVE_STATUS,
                Users::USER_TYPE => ($action == 'sadmin-admin-management') ? EMPLOYEE : ADMIN
            ]);
            // rip($getnotin);
            // echo '<br>'. $getnotin['sql'];
            // exit;
            if (count($getnotin)>0) {
                foreach ($getnotin as $k => $v) {
                    $not_in[] = $v[Users::EMPLOYEE_ID];
                }
            }
            break;
        default:
            // $not_in = [];
            break;
    }
    // rip($not_in);
    // Assigning Reporting Manager
    $getReportManagerSql = "SELECT ". 
    Users::ID .", ".
    Users::EMPLOYEE_ID .", ".
    Users::NAME .", ".
    Users::USER_TYPE." FROM ".
    Table::USERS." WHERE ". 
    Users::CLIENT_ID ." = ". 
    $_SESSION[CLIENT_ID]. " AND ".
    Users::STATUS ." = '". ACTIVE_STATUS ."' AND ". 
    Users::ACTIVE ." = 1 AND ". 
    Users::USER_TYPE ." IN (". implode(", ", $userTypeIn). ") AND ". 
    Users::EMPLOYEE_ID ." NOT IN (0)";
    $getReportManager = getCustomData($getReportManagerSql);
    // echo $getReportManagerSql.'<br>';
    // rip($getReportManager);
    // exit;
    if (count($getReportManager)>0) {
        foreach ($getReportManager as $k => $v) {

            $reporting_manager .= '<option value="'. $v[Users::ID] .'">'. $v[Users::NAME] .' -- ['. USERS[$v[Users::USER_TYPE]] .']</option>';
        }
    }
    if (count(EMPLOYEE_REPORTING_TIMES)>0) {
        for ($i=0; $i < count(EMPLOYEE_REPORTING_TIMES); $i++) {
            $value = EMPLOYEE_REPORTING_TIMES[$i] .':00 ';
            $value .= (EMPLOYEE_REPORTING_TIMES[$i] == '12') ? 'PM' : 'AM';
            $reporting_times .= '<option value="'. EMPLOYEE_REPORTING_TIMES[$i] .'">'. $value .'</option>';
        }
    }
// $getEmpData = getData(Table::EMPLOYEE_DETAILS, [
//     EMPLOYEE_DETAILS::ID,
//     EMPLOYEE_DETAILS::EMPLOYEE_NAME,
//     EMPLOYEE_DETAILS::EMPLOYEE_MOBILE,
//     EMPLOYEE_DETAILS::EMPLOYEE_EMAIL,
//     EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_BIRTH,
//     EMPLOYEE_DETAILS::EMPLOYEE_FATHER_NAME,
//     EMPLOYEE_DETAILS::EMPLOYEE_MOTHER_NAME,
//     EMPLOYEE_DETAILS::EMPLOYEE_BLOOD_GROUP,
//     EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING,
//     EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL,
//     EMPLOYEE_DETAILS::EMPLOYEE_DESIGNATION_ID,
//     EMPLOYEE_DETAILS::REMARKS,
//     EMPLOYEE_DETAILS::ACTIVE,
//     EMPLOYEE_DETAILS::CREATION_DATE,
//     EMPLOYEE_DETAILS::AADHAAR_NUMBER,
//     EMPLOYEE_DETAILS::CURRENT_ADDRESS,
//     EMPLOYEE_DETAILS::DEPARTMENT_ID,
//     EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_MOBILE_NUMBER,
//     EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME,
//     EMPLOYEE_DETAILS::EMPLOYEE_EXPERIENCE_DURATION,
//     EMPLOYEE_DETAILS::EMPLOYEE_ID,
//     EMPLOYEE_DETAILS::ESIC_IP_NUMBER,
//     EMPLOYEE_DETAILS::LAST_UPDATE_DATE,
//     EMPLOYEE_DETAILS::PAN_NUMBER,
//     EMPLOYEE_DETAILS::PERMANENT_ADDRESS,
//     EMPLOYEE_DETAILS::SALARY_ACCOUNT_IFSC_CODE,
//     EMPLOYEE_DETAILS::SALARY_ACCOUNT_NUMBER,
//     EMPLOYEE_DETAILS::SALARY_AMOUNT,
//     EMPLOYEE_DETAILS::UAN_NUMBER,
//     EMPLOYEE_DETAILS::WEBMAIL_ADDRESS,
//     EMPLOYEE_DETAILS::LAST_WORKING_DAY
// ], [
//     EMPLOYEE_DETAILS::CLIENT_ID => $_SESSION[CLIENT_ID],
//     EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS
// ],[],[],[EMPLOYEE_DETAILS::EMPLOYEE_ID], "ASC");
$getEmpDatasql = "SELECT ".EMPLOYEE_DETAILS::ID.", ".
EMPLOYEE_DETAILS::EMPLOYEE_NAME.", ".
EMPLOYEE_DETAILS::EMPLOYEE_MOBILE.", ".
EMPLOYEE_DETAILS::EMPLOYEE_EMAIL.", ".
EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_BIRTH.", ".
EMPLOYEE_DETAILS::EMPLOYEE_FATHER_NAME.", ".
EMPLOYEE_DETAILS::EMPLOYEE_MOTHER_NAME.", ".
EMPLOYEE_DETAILS::EMPLOYEE_BLOOD_GROUP.", ".
EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING.", ".
EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL.", ".
EMPLOYEE_DETAILS::EMPLOYEE_DESIGNATION_ID.", ".
EMPLOYEE_DETAILS::REMARKS.", ".
EMPLOYEE_DETAILS::ACTIVE.", ".
EMPLOYEE_DETAILS::CREATION_DATE.", ".
EMPLOYEE_DETAILS::AADHAAR_NUMBER.", ".
EMPLOYEE_DETAILS::CURRENT_ADDRESS.", ".
EMPLOYEE_DETAILS::DEPARTMENT_ID.", ".
EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_MOBILE_NUMBER.", ".
EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME.", ".
EMPLOYEE_DETAILS::EMPLOYEE_EXPERIENCE_DURATION.", ".
EMPLOYEE_DETAILS::EMPLOYEE_ID.", ".
EMPLOYEE_DETAILS::ESIC_IP_NUMBER.", ".
EMPLOYEE_DETAILS::LAST_UPDATE_DATE.", ".
EMPLOYEE_DETAILS::PAN_NUMBER.", ".
EMPLOYEE_DETAILS::PERMANENT_ADDRESS.", ".
EMPLOYEE_DETAILS::SALARY_ACCOUNT_IFSC_CODE.", ".
EMPLOYEE_DETAILS::SALARY_ACCOUNT_NUMBER.", ".
EMPLOYEE_DETAILS::SALARY_AMOUNT.", ".
EMPLOYEE_DETAILS::UAN_NUMBER.", ".
EMPLOYEE_DETAILS::WEBMAIL_ADDRESS.", ".
EMPLOYEE_DETAILS::REPORTING_TIME.", ".
EMPLOYEE_DETAILS::INACTIVE_REASON.", ".
EMPLOYEE_DETAILS::LAST_WORKING_DAY." FROM ". 
Table::EMPLOYEE_DETAILS ." WHERE ".
EMPLOYEE_DETAILS::CLIENT_ID." = ".$_SESSION[CLIENT_ID]." AND ".
EMPLOYEE_DETAILS::STATUS." = '".ACTIVE_STATUS."'";
if (count($not_in)>0) {
    $getEmpDatasql .= " AND ".
    EMPLOYEE_DETAILS::ID." NOT IN (".implode(", ", $not_in).")";
}
$getEmpDatasql .= " ORDER BY ".
EMPLOYEE_DETAILS::EMPLOYEE_ID." ASC";
// echo $getEmpDatasql;
// exit;
$getEmpData = getCustomData($getEmpDatasql);
// rip($getEmpData);
// exit;
$tr = '';
if (count($getEmpData)>0) {
    foreach ($getEmpData as $key => $value) {
        $user_id = 0;
        $user_type = 0;
        $user_password = '';
        $reporting_manager_id = 0;
        $getUserDetails = getData(Table::USERS, [
            Users::ID,
            Users::USER_TYPE,
            Users::PASSWORD
        ], [
            Users::EMPLOYEE_ID => $value[EMPLOYEE_DETAILS::ID],
            Users::CLIENT_ID => $_SESSION[CLIENT_ID],
        ]);
        $getRprtManagerId = getData(Table::EMPLOYEE_REPORTING_MANAGER, [
            EMPLOYEE_REPORTING_MANAGER::ID,
            EMPLOYEE_REPORTING_MANAGER::REPORTING_MANAGER_USER_ID,
            EMPLOYEE_REPORTING_MANAGER::ASSIGN_DATE
        ], [
            EMPLOYEE_REPORTING_MANAGER::CLIENT_ID => $_SESSION[CLIENT_ID],
            EMPLOYEE_REPORTING_MANAGER::EMPLOYEE_ID => $value[EMPLOYEE_DETAILS::ID],
            EMPLOYEE_REPORTING_MANAGER::STATUS => ACTIVE_STATUS
        ]);
        if (count($getUserDetails)>0) {
            $user_type = $getUserDetails[0][Users::USER_TYPE];
            $user_password = $getUserDetails[0][Users::PASSWORD];
            $user_id = $getUserDetails[0][Users::ID];
        }
        if (count($getRprtManagerId)>0) {
            $reporting_manager_id = $getRprtManagerId[0][EMPLOYEE_REPORTING_MANAGER::REPORTING_MANAGER_USER_ID];
        }
        $data = [
            EMPLOYEE_DETAILS::ID  =>  $value[EMPLOYEE_DETAILS::ID],
            EMPLOYEE_DETAILS::EMPLOYEE_NAME  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_NAME],
            EMPLOYEE_DETAILS::EMPLOYEE_MOBILE  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_MOBILE],
            EMPLOYEE_DETAILS::EMPLOYEE_EMAIL  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_EMAIL],
            EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_BIRTH  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_BIRTH],
            EMPLOYEE_DETAILS::EMPLOYEE_FATHER_NAME  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_FATHER_NAME],
            EMPLOYEE_DETAILS::EMPLOYEE_MOTHER_NAME  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_MOTHER_NAME],
            EMPLOYEE_DETAILS::EMPLOYEE_BLOOD_GROUP  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_BLOOD_GROUP],
            EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING],
            EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL],
            EMPLOYEE_DETAILS::EMPLOYEE_DESIGNATION_ID  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_DESIGNATION_ID],
            EMPLOYEE_DETAILS::REMARKS  =>  $value[EMPLOYEE_DETAILS::REMARKS],
            EMPLOYEE_DETAILS::ACTIVE  =>  $value[EMPLOYEE_DETAILS::ACTIVE],
            EMPLOYEE_DETAILS::INACTIVE_REASON  =>  $value[EMPLOYEE_DETAILS::INACTIVE_REASON],
            EMPLOYEE_DETAILS::CREATION_DATE  =>  $value[EMPLOYEE_DETAILS::CREATION_DATE],
            EMPLOYEE_DETAILS::AADHAAR_NUMBER  =>  $value[EMPLOYEE_DETAILS::AADHAAR_NUMBER],
            EMPLOYEE_DETAILS::CURRENT_ADDRESS  =>  $value[EMPLOYEE_DETAILS::CURRENT_ADDRESS],
            EMPLOYEE_DETAILS::DEPARTMENT_ID  =>  $value[EMPLOYEE_DETAILS::DEPARTMENT_ID],
            EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_MOBILE_NUMBER  =>  $value[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_MOBILE_NUMBER],
            EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME  =>  $value[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME],
            EMPLOYEE_DETAILS::EMPLOYEE_EXPERIENCE_DURATION  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_EXPERIENCE_DURATION],
            EMPLOYEE_DETAILS::EMPLOYEE_ID  =>  $value[EMPLOYEE_DETAILS::EMPLOYEE_ID],
            EMPLOYEE_DETAILS::ESIC_IP_NUMBER  =>  $value[EMPLOYEE_DETAILS::ESIC_IP_NUMBER],
            EMPLOYEE_DETAILS::LAST_UPDATE_DATE  =>  getFormattedDateTime($value[EMPLOYEE_DETAILS::LAST_UPDATE_DATE], LONG_DATE_TIME_FORMAT),
            EMPLOYEE_DETAILS::PAN_NUMBER  =>  $value[EMPLOYEE_DETAILS::PAN_NUMBER],
            EMPLOYEE_DETAILS::PERMANENT_ADDRESS  =>  $value[EMPLOYEE_DETAILS::PERMANENT_ADDRESS],
            EMPLOYEE_DETAILS::SALARY_ACCOUNT_IFSC_CODE  =>  $value[EMPLOYEE_DETAILS::SALARY_ACCOUNT_IFSC_CODE],
            EMPLOYEE_DETAILS::SALARY_ACCOUNT_NUMBER  =>  $value[EMPLOYEE_DETAILS::SALARY_ACCOUNT_NUMBER],
            EMPLOYEE_DETAILS::SALARY_AMOUNT  =>  $value[EMPLOYEE_DETAILS::SALARY_AMOUNT],
            EMPLOYEE_DETAILS::UAN_NUMBER  =>  $value[EMPLOYEE_DETAILS::UAN_NUMBER],
            EMPLOYEE_DETAILS::WEBMAIL_ADDRESS  =>  $value[EMPLOYEE_DETAILS::WEBMAIL_ADDRESS],
            EMPLOYEE_DETAILS::LAST_WORKING_DAY  =>  (!empty($value[EMPLOYEE_DETAILS::LAST_WORKING_DAY])) ? getFormattedDateTime($value[EMPLOYEE_DETAILS::LAST_WORKING_DAY], LONG_DATE_FORMAT) : "",
            Users::USER_TYPE => $user_type,
            Users::PASSWORD => $user_password,
            EMPLOYEE_DETAILS::REPORTING_TIME => $value[EMPLOYEE_DETAILS::REPORTING_TIME],
            EMPLOYEE_REPORTING_MANAGER::REPORTING_MANAGER_USER_ID => $reporting_manager_id
        ];
        $getDesignation = getData(Table::DESIGNATIONS, [
            DESIGNATIONS::DESIGNATION_TITLE
        ], [
            DESIGNATIONS::ID => $value[EMPLOYEE_DETAILS::EMPLOYEE_DESIGNATION_ID],
            DESIGNATIONS::CLIENT_ID => $_SESSION[CLIENT_ID],
            DESIGNATIONS::STATUS => ACTIVE_STATUS
        ]);
        $department = EMPTY_VALUE;
        if (count($getDeptData)>0) {
            foreach ($getDeptData as $k => $dept_v) {
                if ($dept_v[DEPARTMENTS::ID] == $value[EMPLOYEE_DETAILS::DEPARTMENT_ID]) {
                    $department = altRealEscape($dept_v[DEPARTMENTS::DEPARTMENT_NAME]);
                }
            }
        }
        $designation = (count($getDesignation)>0) ? altRealEscape($getDesignation[0][DESIGNATIONS::DESIGNATION_TITLE]) : EMPTY_VALUE;
        $slno = ($key + 1);
        $emp_id = EMPLOYEE_ID_PREFIX.$value[EMPLOYEE_DETAILS::EMPLOYEE_ID];
        $name = (!empty($value[EMPLOYEE_DETAILS::EMPLOYEE_NAME])) ? altRealEscape($value[EMPLOYEE_DETAILS::EMPLOYEE_NAME]) : EMPTY_VALUE;
        $mobile = (!empty($value[EMPLOYEE_DETAILS::EMPLOYEE_MOBILE])) ? altRealEscape($value[EMPLOYEE_DETAILS::EMPLOYEE_MOBILE]) : EMPTY_VALUE;
        $email = (!empty($value[EMPLOYEE_DETAILS::EMPLOYEE_EMAIL])) ? altRealEscape($value[EMPLOYEE_DETAILS::EMPLOYEE_EMAIL]) : EMPTY_VALUE;
        $webmail = (!empty($value[EMPLOYEE_DETAILS::WEBMAIL_ADDRESS])) ? altRealEscape($value[EMPLOYEE_DETAILS::WEBMAIL_ADDRESS]) : EMPTY_VALUE;
        $uan = (!empty($value[EMPLOYEE_DETAILS::UAN_NUMBER])) ? altRealEscape($value[EMPLOYEE_DETAILS::UAN_NUMBER]) : EMPTY_VALUE;
        $esic = (!empty($value[EMPLOYEE_DETAILS::ESIC_IP_NUMBER])) ? altRealEscape($value[EMPLOYEE_DETAILS::ESIC_IP_NUMBER]) : EMPTY_VALUE;
        $contact = $uan_details = "";

        if ($mobile != EMPTY_VALUE) {
            $contact .= "<strong>Mobile: </strong>".$mobile."<br>";
        }
        if ($email != EMPTY_VALUE) {
            $contact .= "<strong>Email: </strong>".$email."<br>";
        }
        if ($webmail != EMPTY_VALUE) {
            $contact .= "<strong>Webmail: </strong>".$webmail;
        }
        if ($uan != EMPTY_VALUE) {
            $uan_details .= "<strong>UAN Number: </strong>".$uan."<br>";
        }
        if ($esic != EMPTY_VALUE) {
            $uan_details .= "<strong>ESIC IP Number: </strong>".$esic;
        }
        
        
        $payroll = PAYROLL_OPTIONS[$value[EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL]];
        $salary = (!empty($value[EMPLOYEE_DETAILS::SALARY_AMOUNT])) ? moneyFormatIndia($value[EMPLOYEE_DETAILS::SALARY_AMOUNT]) : EMPTY_VALUE;
        
        $date_of_joinning = (($value[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING]) != "") ? getFormattedDateTime($value[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING], LONG_DATE_FORMAT) : EMPTY_VALUE;
        $dact = (($value[EMPLOYEE_DETAILS::ACTIVE]) == 1) ? "checked" : "";
        $dact_status = (($value[EMPLOYEE_DETAILS::ACTIVE]) == 1) ? '<span class="text-success">A</span>' : '<span class="text-danger">D</span>';
        $actions = '
        <div class="" style="display:flex; justify-content: space-between;">
            <div class="text-success" onclick=\'updateEmployee('. json_encode($data) .');\'><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
            <div class="text-danger cursor-pointer" onclick="initiateDelete('. $value[EMPLOYEE_DETAILS::ID] .', \'employee_list\')"><i style="font-size:15px; cursor: pointer" class="fas fa-trash-alt"></i></div>
        </div>
        ';
        $tr .= '<tr id="employee_list_'.$value[EMPLOYEE_DETAILS::ID].'">
        <td>'.$slno.'</td>
        <td>'.$emp_id.'</td>
        <td>'.ucwords($name).'</td>
        <td>'.$designation.'</td>
        <td>'.$department.'</td>
        <td class="text-left">'.$contact.'</td>
        <td style="cursor: pointer;">
        <div class="custom-control custom-switch noselect">
            <input type="checkbox" '.$dact.' class="custom-control-input" id="employee_active_'.$value[EMPLOYEE_DETAILS::ID].'" onclick="changeActiveStatus(\'employee\','.$value[EMPLOYEE_DETAILS::ID].', \'employee_loader\');">
            <label class="custom-control-label text-success" for="employee_active_'.$value[EMPLOYEE_DETAILS::ID].'"></label>
        </div>
        </td>
        <td>'.$actions.'</td>
    </tr>';
    }
} else {
    $tr = '<tr class="animated fadeInDown">
    <td colspan="14">
        <div class="alert alert-danger" role="alert">
            No Employees found ! <a style="text-decoration: underline; font-weight: bold;" href="'.HOST_URL.(($_SESSION[USER_TYPE]==SADMIN)?'sadmin-add-auditor':'add-auditors').'">Click Here</a> to add Employees First.
        </div>
    </td>
    </tr>';
}
$user_types = '';
if (count(USERS)>0) {
    foreach (USERS as $k => $v) {
        if ($_SESSION[USER_TYPE] == ADMIN) {
            if (($k == ADMIN) || ($k == SADMIN)) {
                continue;
            }
        }
        if (($_SESSION[USER_TYPE] == SADMIN)||($_SESSION[USER_TYPE] == IT_ADMIN)) {
            if ($k == SADMIN) {
                continue;
            }
        }
        $selected = '';
        switch ($_SESSION[USER_TYPE]) {
            case ADMIN:
                if ($k == EMPLOYEE) {
                    $selected = 'selected';
                }
                break;
            case SADMIN:
                if ($k == ADMIN) {
                    $selected = 'selected';
                }
                break;
        }
        $user_types .= '<option '.$selected.' value="'.$k.'">'.ucwords(strtolower($v)).'</option>';
    }
}
$add_link='';
switch ($_SESSION[USER_TYPE]) {
    case SADMIN:
        $add_link='sadmin-add-auditor';
        break;
    case ADMIN:
        $add_link='add-auditors';
        break;
    case IT_ADMIN:
        $add_link='it-add-auditor';
        break;
}
?>


<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 text-right">
        <h2 class="text-center"><?php echo (($action == 'sadmin-admin-management') ? 'Admins' : 'Auditors');?></h2>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-12 text-right" id="list_nav_btn">
        <button class="btn btn-sm btn-warning" name="list" onclick="window.location.href='<?=HOST_URL?><?=$add_link?>'"><small><i class="fas fa-plus"></i>&nbsp;Create New</small></button>
    </div>
</div>

<div class="card mt-5">
    <?=getSpinner(true, "employee_loader");?>
    <div class="card_body" style="padding: 15px;">
        <div class="row" id="list_employee_row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover text-center employee_list_table" style="font-size:14px;" id="example">
                        <thead class="text-center table-warning">
                            <tr style="text-transform: uppercase; font-size: 12px;">
                                <th class="cursor-pointer">SL.</th>
                                <th class="cursor-pointer">Employee ID</th>
                                <th class="cursor-pointer">Name</th>
                                <th class="cursor-pointer">Designation</th>
                                <th class="cursor-pointer">Department</th>
                                <!-- <th class="cursor-pointer">Date Joinning</th> -->
                                <!-- <th class="cursor-pointer">Payroll</th> -->
                                <!-- <th class="cursor-pointer">Salary</th> -->
                                <!-- <th class="cursor-pointer">UAN Details</th> -->
                                <th class="cursor-pointer">Contact</th>
                                <th class="cursor-pointer">Active</th>
                                <th class="cursor-pointer">Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 12px;">
                            <?=$tr?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row" id="edit_employee_row" style="display:none;">
            <div class="col-12">
                <div class="row" id="last_update_display">
                    <div class="col-md-6 col-lg-6 col-sm-12 text-left" style="display: none;">
                        <span style="font-weight: bold;" id="last_working_day_span">Last Working Day : <span class="text-primary"></span></span>
                    </div>
                    <div class="col-md-6 col-lg-6 col-sm-12 text-right">
                        <span style="font-weight: bold;" id="last_update_date_span">Last Updated On : <span class="text-primary"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-12" style="margin-bottom: 20px;">
                <span style="font-weight: bold;" id="emp_active_status">Status : <span class="text-secondary"></span></span>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
            <?php include FORM_UI_PATH.'employee_form.php'; ?>
            <input type="hidden" style="visibility: hidden; display:none;" value="<?=$user_id?>" id="employee_user_id" />
                <div class="row mt-4">
                    <div class="col-lg-6 col-md-6 col-sm-12 noselect">
                        
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                        <button type="button" class="btn btn-sm btn-success sbmt" data-id="" id="emp_update_btn">
                            <i class="fas fa-history"></i>&nbsp;Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="emp_inactive_status_update" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="emp_inactive_status_updateLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="emp_inactive_status_updateLabel">Update Employee Inactive Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12">
                <input type="hidden" id="emp_inactive_id">
                <label class="form_label" for="emp_inactive_reason">Select Reason</label>
                <select class="form-control" id="emp_inactive_reason" onchange="checkEmpInactiveReasons();">
                    <option value="0" selected disabled>Select Reason</option>
                    <?php 
                        foreach (EMPLOYEE_INACTIVE_REASONS as $key => $value) :
                            // $key = $key+1;
                    ?>
                    <option value="<?=$key;?>"><?=ucwords(strtolower($value));?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12">
                <label class="form_label" for="emp_last_working_day">Last Working Day</label>
                <input type="date" class="form-control" id="emp_last_working_day" />
            </div>
            <div class="col-md-12 col-lg-12 col-sm-12 mt-2" id="emp_inactive_other_reason_section">
                <input type="text" class="form-control" id="emp_inactive_other_reason" placeholder="Write Reason..." />
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;Close</button>
        <button type="button" class="btn btn-sm btn-primary" id="emp_modal_update_btn"><i class="fas fa-history"></i>&nbsp;Update</button>
      </div>
    </div>
  </div>
</div>

<?php 
} 
?>