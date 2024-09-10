<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=utf-8');
// Constant defiend in config.php 
// If this one true then send the data as json format
// Or echo with json_encode if false [plain text format]
## IMPORTANT:- [For now do not make this true. Or the code will break. For testing purpose
## The rest of the response are sent as plain text format. The following condition 
## is useless right now. But can be activated in the future.]
## DO NOT ALTER THIS LINE
if (IS_CONTENT_TYPE_JSON) {
    header('Content-Type:application/json');
}
// The main array for sending the response
// Please use this variable to send the response
## By default denoting that error is true
## and the user requesting is logged in
$response = ['error' => true, 'empty' => false, 'tab' => false, 'message' => '', 'login' => 1];
$method = $_SERVER['REQUEST_METHOD'];
$ajax_form_data = $_REQUEST;
switch ($method) {
    case 'GET':
        $ajax_form_data = $_GET;
        break;
    case 'POST':
        $ajax_form_data = $_POST;
        break;
    default:
        $ajax_form_data = $_REQUEST;
        break;
}
if (!isset($ajax_form_data[AJAX_REQUEST])) {
    $response['message'] = 'Invalid Request Supplied';
    $response['login'] = isUserLoggedIn();
    sendRes();
    exit();
}
// $response['login'] = (isUserLoggedIn()) ? 1 : 0;
function sendRes()
{
    global $response;
    echo json_encode($response);
    exit();
}
$ajax_action = $ajax_form_data[AJAX_REQUEST];
if ((!isset($_SESSION[CLIENT_ID])) && ($ajax_action != 'LOGIN')) {
    $response['login'] = 0;
    sendRes();
}
switch ($ajax_action) {
    case 'LOGIN':
        // rip($ajax_form_data);
        // exit();
        $email = (!empty($ajax_form_data['em'])) ? altRealEscape($ajax_form_data['em']) : "";
        $pass = (!empty($ajax_form_data['ep'])) ? altRealEscape($ajax_form_data['ep']) : "";
        $rtype = (!empty($ajax_form_data['rt'])) ? altRealEscape($ajax_form_data['rt']) : "";
        if (($email == "") || ($pass == "")) {
            $response["message"] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $pass_hash = hash('sha256', $pass);
        if (($pass_hash == "") || (empty($pass_hash))) {
            $response["message"] = ERROR_1;
            sendRes();
        }
        $wh = [];
        switch ($rtype) {
            case 'e':
                $wh = [
                    Users::EMAIL => $email
                ];
                break;
            case 'p':
                $wh = [
                    Users::MOBILE => $email
                ];
                break;
        }
        // $wh[Users::ACTIVE] = 1;
        $wh[Users::STATUS] = (string)ACTIVE_STATUS;
        $db_data = getData(Table::USERS, [
            Users::CLIENT_ID,
            Users::EMAIL,
            Users::MOBILE,
            Users::NAME,
            Users::ID,
            Users::PASS_HASH,
            Users::PASSWORD,
            Users::USER_TYPE,
            Users::EMPLOYEE_ID,
            Users::ACTIVE
        ], $wh);
        // rip($db_data);
        // exit();
        if (!count($db_data) > 0) {
            $response['message'] = "No Registered User Found";
            sendRes();
        }
        if ($db_data[0][USERS::ACTIVE] != 1) {
            $response['message'] = "You are currently inactive !";
            sendRes();
        }
        $db_pass_hash = $db_data[0][Users::PASS_HASH];
        $db_pass = $db_data[0][Users::PASSWORD];
        if ($db_pass === $db_pass) {
            if ($pass_hash === $db_pass_hash) {
                $db_cid = $db_data[0][Users::CLIENT_ID];
                $db_email = $db_data[0][Users::EMAIL];
                $db_mobile = $db_data[0][Users::MOBILE];
                $db_name = $db_data[0][Users::NAME];
                $db_id = $db_data[0][Users::ID];
                $db_user_type = $db_data[0][Users::USER_TYPE];
                $user_id = $email;
                $emp_id = $db_data[0][Users::EMPLOYEE_ID];

                if (($emp_id != 0) && ($db_user_type != SADMIN)) {
                    $getEmpData = getData(Table::EMPLOYEE_DETAILS, [
                        EMPLOYEE_DETAILS::ACTIVE
                    ], [
                        EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS,
                        EMPLOYEE_DETAILS::ID => $emp_id,
                        EMPLOYEE_DETAILS::ACTIVE => 1
                    ]);
                    if (count($getEmpData) <= 0) {
                        $response['message'] = "Currently you are Inactive. Please contact your HR Department";
                        sendRes();
                    }
                }

                $_SESSION[LOGGEDIN] = true;
                $_SESSION[USERNAME] = $db_name;
                $_SESSION[USER_ID] = $user_id;
                $_SESSION[RID] = $db_id;
                $_SESSION[CLIENT_ID] = $db_cid;
                $_SESSION[USER_TYPE] = $db_user_type;
                $_SESSION[EMPLOYEE_ID] = $db_data[0][Users::EMPLOYEE_ID];
                $response['error'] = false;
                $response['message'] = 'Login Successful';
                // $_SESSION['success_msg'] = $response['message'];
                // setSessionMsg($response['message']);
                $lastLoginTime=$lastLogoutTime=$lastLoginDate=$lastLogoutDate=$lastIPaddr='';
                $lastSessDataRid=0;
                $getLastSessionData=getData(Table::USER_LAST_SESSION_DATA,['*'],[
                    USER_LAST_SESSION_DATA::CLIENT_ID=>$db_cid
                ]);
                if (count($getLastSessionData)>0) {
                    $sdata = $getLastSessionData[0];
                    $lastLoginTime = $sdata[USER_LAST_SESSION_DATA::LOGIN_TIME];
                    $lastLogoutTime = $sdata[USER_LAST_SESSION_DATA::LOGOUT_TIME];
                    $lastLoginDate = $sdata[USER_LAST_SESSION_DATA::LOGIN_DATE];
                    $lastLogoutDate = $sdata[USER_LAST_SESSION_DATA::LOGOUT_DATE];
                    $lastIPaddr = $sdata[USER_LAST_SESSION_DATA::IP_ADDRESS];
                    $lastSessDataRid = $sdata[USER_LAST_SESSION_DATA::ID];

                    $_SESSION[LAST_LOGIN_DATE] = $lastLoginDate;
                    $_SESSION[LAST_LOGIN_TIME] = $lastLoginTime;
                    $_SESSION[LAST_LOGOUT_DATE] = $lastLogoutDate;
                    $_SESSION[LAST_LOGOUT_TIME] = $lastLogoutTime;
                    $_SESSION[LAST_SESSION_IP] = $lastIPaddr;
                    $_SESSION[LAST_SESSION_DATA_ROW_ID] = $lastSessDataRid;

                    $updateNewData = updateData(Table::USER_LAST_SESSION_DATA,[
                        USER_LAST_SESSION_DATA::USER_ID => $db_id,
                        USER_LAST_SESSION_DATA::LOGIN_TIME => date('H:i:s'),
                        USER_LAST_SESSION_DATA::LOGIN_DATE => getToday(false),
                        USER_LAST_SESSION_DATA::IP_ADDRESS => getUserIpAddr(),
                        USER_LAST_SESSION_DATA::INFOTEXT => getInfoText(true)
                    ],[
                        USER_LAST_SESSION_DATA::ID => $lastSessDataRid,
                        USER_LAST_SESSION_DATA::CLIENT_ID => $db_cid
                    ]);
                    if(!$updateNewData['res']){
                        logError("Unabled to update last login data.",$updateNewData['error']);
                    }
                } else {
                    $_SESSION[LAST_LOGIN_DATE] = getToday(false);
                    $_SESSION[LAST_LOGIN_TIME] = date('H:i:s');
                    $_SESSION[LAST_LOGOUT_DATE] = '';
                    $_SESSION[LAST_LOGOUT_TIME] = '';
                    $_SESSION[LAST_SESSION_IP] = getUserIpAddr();
                    $lastRid=0;
                    $saveNewSessData = setData(Table::USER_LAST_SESSION_DATA,[
                        USER_LAST_SESSION_DATA::CLIENT_ID=>$db_cid,
                        USER_LAST_SESSION_DATA::USER_ID=>$db_id,
                        USER_LAST_SESSION_DATA::LOGIN_TIME => date('H:i:s'),
                        USER_LAST_SESSION_DATA::LOGIN_DATE => getToday(false),
                        USER_LAST_SESSION_DATA::IP_ADDRESS => getUserIpAddr(),
                        USER_LAST_SESSION_DATA::INFOTEXT => getInfoText(true)
                    ]);
                    if(!$saveNewSessData['res']){
                        logError("Unabled to insert new last login data.",$saveNewSessData['error']);
                    } else {
                        $lastRid = $saveNewSessData['id'];
                    }
                    $_SESSION[LAST_SESSION_DATA_ROW_ID] = $lastRid;
                }
            } else {
                $response['message'] = "Incorrect Password";
                sendRes();
            }
        } else {
            $response['message'] = "Incorrect Password";
            sendRes();
        }
        sendRes();
        exit();
        break;
    case 'CHANGE_USER_PASSWORD':
        $old_pass=isset($ajax_form_data['op'])?altRealEscape($ajax_form_data['op']):"";
        $new_pass=isset($ajax_form_data['np'])?altRealEscape($ajax_form_data['np']):"";
        $con_pass=isset($ajax_form_data['cp'])?altRealEscape($ajax_form_data['cp']):"";
        if (($old_pass=="")||($new_pass=="")||($con_pass=="")) {
            $response['error']=true;
            $response['message']=EMPTY_FIELD_ALERT;
            sendRes();
        }
        if ($new_pass!==$con_pass) {
            $response['error']=true;
            $response['message']="New Password & Confirm Password is not matching !";
            sendRes();
        }
        $oldPassHash=hash('sha256',$old_pass);
        $newPassHash=hash('sha256',$con_pass);
        $getOldData=getData(Table::USERS,[
            Users::PASSWORD,
            Users::PASS_HASH
        ],[Users::ID=>$_SESSION[RID],Users::CLIENT_ID=>$_SESSION[CLIENT_ID],Users::ACTIVE=>1,Users::STATUS=>ACTIVE_STATUS]);
        if(count($getOldData)==0){
            $response['message']="Opps! Soemthing went wrong. Could not find User Data!";
            sendRes();
        }
        $dbPass=$getOldData[0][Users::PASSWORD];
        $dbPassHash=$getOldData[0][Users::PASS_HASH];
        if(
            ($dbPass!==$old_pass)||
            ($dbPassHash!==$oldPassHash)
        ){
            $response['message']="Please enter the correct old password";
            sendRes();
        }
        if(
            ($con_pass===$dbPass)||
            ($newPassHash===$dbPassHash)
        )
        {
            $response['message']="Old password & New Password cannot be same";
            sendRes();
        }
        $update=updateData(Table::USERS,[
            Users::PASSWORD=>$con_pass,
            Users::PASS_HASH=>$newPassHash,
            Users::PASSWORD_UPDATED_AT=>getToday(),
            Users::UPDATED_AT=>getToday()
        ],[
            Users::ID=>$_SESSION[RID],
            Users::CLIENT_ID=>$_SESSION[CLIENT_ID],
            Users::ACTIVE=>1,
            Users::STATUS=>ACTIVE_STATUS
        ]);
        if(!$update['res'])
        {
            logError("Unabled to update user password, User ID: ".$_SESSION[RID].", New Password: ".$con_pass, $update['error']);
            $response['message']=ERROR_1;
            sendRes();
        }
        $response['error']=false;
        $response['message']="Password Changed Successfully !";
        sendRes();
        break;
    case 'ADD_DESIGNATION':
        $desig_name = (!empty($ajax_form_data['dname'])) ? altRealEscape($ajax_form_data['dname']) : "";
        $desig_resp = (!empty($ajax_form_data['dres'])) ? altRealEscape($ajax_form_data['dres']) : "";
        $desig_active = (!empty($ajax_form_data['dact'])) ? altRealEscape($ajax_form_data['dact']) : "";
        $desig_exp = (!empty($ajax_form_data['dexp'])) ? altRealEscape($ajax_form_data['dexp']) : "";
        if ($desig_name == "") {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $cols = [
            DESIGNATIONS::CLIENT_ID => $_SESSION[CLIENT_ID],
            DESIGNATIONS::DESIGNATION_TITLE => $desig_name,
            DESIGNATIONS::ADDED_BY => $_SESSION[RID],
            DESIGNATIONS::CREATION_DATE => getToday(true),
            DESIGNATIONS::LAST_UPDATE_DATE => getToday(true)
        ];
        if (!empty($desig_resp)) {
            $cols[DESIGNATIONS::RESPONSIBILITIES] = $desig_resp;
        }
        if (!empty($desig_exp)) {
            $cols[DESIGNATIONS::EXPERIENCE_REQUIRED] = $desig_exp;
        }
        $getdname = getData(Table::DESIGNATIONS, [DESIGNATIONS::DESIGNATION_TITLE], [
            DESIGNATIONS::CLIENT_ID => $_SESSION[CLIENT_ID],
            DESIGNATIONS::STATUS => ACTIVE_STATUS
        ]);
        if (count($getdname) > 0) {
            foreach ($getdname as $key => $value) {
                if ($value[DESIGNATIONS::DESIGNATION_TITLE] == $desig_name) {
                    $response["message"] = "Designation already exists";
                    sendRes();
                }
            }
        }
        $add_desig = setData(Table::DESIGNATIONS, $cols);
        if (!$add_desig['res']) {
            $response['message'] = ERROR_3 . ' ' . $add_desig['error'];
            logError("Unabled to Save Designation Data", $add_desig['error']);
        } else {
            $response['error'] = false;
            $response['message'] = $desig_name . " Designation added successfully";
        }
        sendRes();
        break;
    case 'ADD_EMPLOYEE':
        $emp_name = (!empty($ajax_form_data['enm'])) ? altRealEscape($ajax_form_data['enm']) : '';
        $emp_date_of_birth = (!empty($ajax_form_data['edb'])) ? altRealEscape($ajax_form_data['edb']) : '';
        $emp_mother_name = (!empty($ajax_form_data['emn'])) ? altRealEscape($ajax_form_data['emn']) : '';
        $emp_father_name = (!empty($ajax_form_data['efn'])) ? altRealEscape($ajax_form_data['efn']) : '';
        $emp_mobile = (!empty($ajax_form_data['emb'])) ? altRealEscape($ajax_form_data['emb']) : '';
        $emp_email = (!empty($ajax_form_data['eeml'])) ? altRealEscape($ajax_form_data['eeml']) : '';
        $blood_group = (!empty($ajax_form_data['ebg'])) ? altRealEscape($ajax_form_data['ebg']) : '';
        $emp_payroll = (!empty($ajax_form_data['eprl'])) ? altRealEscape($ajax_form_data['eprl']) : '';
        $emp_date_of_join = (!empty($ajax_form_data['edtj'])) ? altRealEscape($ajax_form_data['edtj']) : '';
        $emp_remarks = (!empty($ajax_form_data['ermk'])) ? altRealEscape($ajax_form_data['ermk']) : '';
        $emp_exp = (!empty($ajax_form_data['exp'])) ? altRealEscape($ajax_form_data['exp']) : '';
        $emp_desig_id = (!empty($ajax_form_data['edgn'])) ? altRealEscape($ajax_form_data['edgn']) : 0;

        $emp_id = (!empty($ajax_form_data['emp_id'])) ? altRealEscape($ajax_form_data['emp_id']) : '';
        $emp_department = (!empty($ajax_form_data['emp_department'])) ? $ajax_form_data['emp_department'] : 0;
        $emp_salary = (!empty($ajax_form_data['emp_salary'])) ? altRealEscape($ajax_form_data['emp_salary']) : '';
        $emp_webmail = (!empty($ajax_form_data['emp_webmail'])) ? altRealEscape($ajax_form_data['emp_webmail']) : '';
        $emergeny_contact_name = (!empty($ajax_form_data['emergeny_contact_name'])) ? altRealEscape($ajax_form_data['emergeny_contact_name']) : '';
        $emergeny_contact_mobile = (!empty($ajax_form_data['emergeny_contact_mobile'])) ? altRealEscape($ajax_form_data['emergeny_contact_mobile']) : '';
        $current_address = (!empty($ajax_form_data['current_address'])) ? altRealEscape($ajax_form_data['current_address']) : '';
        $permanent_address = (!empty($ajax_form_data['permanent_address'])) ? altRealEscape($ajax_form_data['permanent_address']) : '';
        $emp_aadhaar = (!empty($ajax_form_data['emp_aadhaar'])) ? altRealEscape($ajax_form_data['emp_aadhaar']) : '';
        $emp_pan = (!empty($ajax_form_data['emp_pan'])) ? altRealEscape($ajax_form_data['emp_pan']) : '';
        $emp_salary_ac_number = (!empty($ajax_form_data['emp_salary_ac_number'])) ? altRealEscape($ajax_form_data['emp_salary_ac_number']) : '';
        $emp_salary_ac_ifsc = (!empty($ajax_form_data['emp_salary_ac_ifsc'])) ? altRealEscape($ajax_form_data['emp_salary_ac_ifsc']) : '';
        $emp_uan = (!empty($ajax_form_data['emp_uan'])) ? altRealEscape($ajax_form_data['emp_uan']) : '';
        $emp_esic_ip_number = (!empty($ajax_form_data['emp_esic_ip_number'])) ? altRealEscape($ajax_form_data['emp_esic_ip_number']) : '';

        $employee_user_type = ($ajax_form_data['utype'] != 0) ? altRealEscape($ajax_form_data['utype']) : 0;
        $user_password = (!empty($ajax_form_data['upass'])) ? altRealEscape($ajax_form_data['upass']) : '';

        $employee_report_manager = (($ajax_form_data['rpt_mngr'] != 0) && ($ajax_form_data['rpt_mngr'] != "")) ? $ajax_form_data['rpt_mngr'] : 0;
        $employee_report_time = (($ajax_form_data['employee_report_time'] != 0) && ($ajax_form_data['employee_report_time'] != "")) ? $ajax_form_data['employee_report_time'] : 0;


        if (($emp_name == "") || ($emp_id == "") || ($user_password == "") || ($employee_user_type == 0)) {
            $response["message"] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $employee_id = 0;
        $getEmpData = getData(Table::EMPLOYEE_DETAILS, [
            EMPLOYEE_DETAILS::EMPLOYEE_NAME,
            EMPLOYEE_DETAILS::EMPLOYEE_ID
        ], [
            EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS,
            EMPLOYEE_DETAILS::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (count($getEmpData) > 0) {
            foreach ($getEmpData as $key => $dbEmpData) {
                if ($emp_name == $dbEmpData[EMPLOYEE_DETAILS::EMPLOYEE_NAME]) {
                    $response["message"] = "Employee: " . $emp_name . " already exists";
                    sendRes();
                }
                if ($emp_id == $dbEmpData[EMPLOYEE_DETAILS::EMPLOYEE_ID]) {
                    $response["message"] = "Employee ID: " . EMPLOYEE_ID_PREFIX . $emp_id . " already exists";
                    sendRes();
                }
            }
        }
        $cols = [
            EMPLOYEE_DETAILS::CLIENT_ID => $_SESSION[CLIENT_ID],
            EMPLOYEE_DETAILS::EMPLOYEE_NAME => $emp_name,
            EMPLOYEE_DETAILS::EMPLOYEE_DESIGNATION_ID => $emp_desig_id,
            EMPLOYEE_DETAILS::ACTIVE => 1,
            EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS,
            EMPLOYEE_DETAILS::CREATION_DATE => getToday(),
            EMPLOYEE_DETAILS::LAST_UPDATE_DATE => getToday(),
            EMPLOYEE_DETAILS::EMPLOYEE_ADDED_BY => $_SESSION[RID],
            EMPLOYEE_DETAILS::EMPLOYEE_ID => $emp_id,
            EMPLOYEE_DETAILS::REPORTING_TIME => $employee_report_time
        ];
        if (!empty($emp_date_of_birth) || ($emp_date_of_birth != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_BIRTH] = $emp_date_of_birth;
        }
        if (!empty($emp_mother_name) || ($emp_mother_name != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_MOTHER_NAME] = $emp_mother_name;
        }
        if (!empty($emp_father_name) || ($emp_father_name != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_FATHER_NAME] = $emp_father_name;
        }
        if (!empty($emp_mobile) || ($emp_mobile != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_MOBILE] = $emp_mobile;
        }
        if (!empty($emp_email) || ($emp_email != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_EMAIL] = $emp_email;
        }
        if (!empty($blood_group) || ($blood_group != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_BLOOD_GROUP] = $blood_group;
        }
        if (!empty($emp_payroll) || ($emp_payroll != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL] = $emp_payroll;
        }
        if (!empty($emp_date_of_join) || ($emp_date_of_join != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING] = $emp_date_of_join;
        }
        if (!empty($emp_remarks) || ($emp_remarks != "")) {
            $cols[EMPLOYEE_DETAILS::REMARKS] = $emp_remarks;
            $cols[EMPLOYEE_DETAILS::REMARK_BY] = $_SESSION[RID];
        }
        if (!empty($emp_exp) || ($emp_exp != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_EXPERIENCE_DURATION] = $emp_exp;
        }
        if (!empty($emp_department) || ($emp_department != "")) {
            $cols[EMPLOYEE_DETAILS::DEPARTMENT_ID] = $emp_department;
        }
        if (!empty($emp_salary) || ($emp_salary != "")) {
            $cols[EMPLOYEE_DETAILS::SALARY_AMOUNT] = $emp_salary;
        }
        if (!empty($emp_webmail) || ($emp_webmail != "")) {
            $cols[EMPLOYEE_DETAILS::WEBMAIL_ADDRESS] = $emp_webmail;
        }
        if (!empty($emergeny_contact_name) || ($emergeny_contact_name != "")) {
            $cols[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME] = $emergeny_contact_name;
        }
        if (!empty($emergeny_contact_mobile) || ($emergeny_contact_mobile != "")) {
            $cols[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_MOBILE_NUMBER] = $emergeny_contact_mobile;
        }
        if (!empty($current_address) || ($current_address != "")) {
            $cols[EMPLOYEE_DETAILS::CURRENT_ADDRESS] = $current_address;
        }
        if (!empty($permanent_address) || ($permanent_address != "")) {
            $cols[EMPLOYEE_DETAILS::PERMANENT_ADDRESS] = $permanent_address;
        }
        if (!empty($emp_aadhaar) || ($emp_aadhaar != "")) {
            $cols[EMPLOYEE_DETAILS::AADHAAR_NUMBER] = $emp_aadhaar;
        }
        if (!empty($emp_pan) || ($emp_pan != "")) {
            $cols[EMPLOYEE_DETAILS::PAN_NUMBER] = $emp_pan;
        }
        if (!empty($emp_salary_ac_number) || ($emp_salary_ac_number != "")) {
            $cols[EMPLOYEE_DETAILS::SALARY_ACCOUNT_NUMBER] = $emp_salary_ac_number;
        }
        if (!empty($emp_salary_ac_ifsc) || ($emp_salary_ac_ifsc != "")) {
            $cols[EMPLOYEE_DETAILS::SALARY_ACCOUNT_IFSC_CODE] = $emp_salary_ac_ifsc;
        }
        if (!empty($emp_uan) || ($emp_uan != "")) {
            $cols[EMPLOYEE_DETAILS::UAN_NUMBER] = $emp_uan;
        }
        if (!empty($emp_esic_ip_number) || ($emp_esic_ip_number != "")) {
            $cols[EMPLOYEE_DETAILS::ESIC_IP_NUMBER] = $emp_esic_ip_number;
        }
        $save = setData(Table::EMPLOYEE_DETAILS, $cols);
        if (!$save['res']) {
            logError("Employee Details Save Error, employee name: " . $emp_name, $save['error']);
            $response['message'] = $save['error'];
            sendRes();
        }
        //saving as user
        $employee_id = $save['id'];
        $pass_hash = hash('sha256', $user_password);
        $saveUser = setData(Table::USERS, [
            Users::CLIENT_ID => $_SESSION[CLIENT_ID],
            Users::EMPLOYEE_ID => $employee_id,
            Users::USER_TYPE => $employee_user_type,
            Users::NAME => $emp_name,
            Users::EMAIL => $emp_email,
            Users::MOBILE => $emp_mobile,
            Users::PASSWORD => $user_password,
            Users::PASS_HASH => $pass_hash,
            Users::CREATION_DATE => getToday()
        ]);
        if (!$saveUser['res']) {
            logError("Failed to save User for the Employee: " . $employee_id . ", Name: " . $emp_name, $saveUser['error']);
            $response['message'] = "Failed to Assign as User for the Employee: " . $emp_name;
            sendRes();
        }
        //saving reporting manager
        $saveReportManager = setData(Table::EMPLOYEE_REPORTING_MANAGER, [
            EMPLOYEE_REPORTING_MANAGER::CLIENT_ID => $_SESSION[CLIENT_ID],
            EMPLOYEE_REPORTING_MANAGER::EMPLOYEE_ID => $employee_id,
            EMPLOYEE_REPORTING_MANAGER::REPORTING_MANAGER_USER_ID => $employee_report_manager,
            EMPLOYEE_REPORTING_MANAGER::ASSIGNED_BY_USER_ID => $_SESSION[RID],
            EMPLOYEE_REPORTING_MANAGER::ASSIGN_DATE => getToday(false),
            EMPLOYEE_REPORTING_MANAGER::STATUS => ACTIVE_STATUS,
            EMPLOYEE_REPORTING_MANAGER::CREATION_DATE => getToday()
        ]);
        if (!$saveReportManager['res']) {
            logError("Unabled to assign reporting manager for the employee ID: " . $employee_id . ".", $saveReportManager['error']);
            $response['message'] = "Unabled to assign Reporting Manager";
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Saved Successfully";
        sendRes();
        break;
    case 'UPDTAE_EMPLOYEE':
        $emp_name = (!empty($ajax_form_data['enm'])) ? altRealEscape($ajax_form_data['enm']) : '';
        $emp_date_of_birth = (!empty($ajax_form_data['edb'])) ? altRealEscape($ajax_form_data['edb']) : '';
        $emp_mother_name = (!empty($ajax_form_data['emn'])) ? altRealEscape($ajax_form_data['emn']) : '';
        $emp_father_name = (!empty($ajax_form_data['efn'])) ? altRealEscape($ajax_form_data['efn']) : '';
        $emp_mobile = (!empty($ajax_form_data['emb'])) ? altRealEscape($ajax_form_data['emb']) : '';
        $emp_email = (!empty($ajax_form_data['eeml'])) ? altRealEscape($ajax_form_data['eeml']) : '';
        $blood_group = (!empty($ajax_form_data['ebg'])) ? altRealEscape($ajax_form_data['ebg']) : '';
        $emp_payroll = (!empty($ajax_form_data['eprl'])) ? altRealEscape($ajax_form_data['eprl']) : 0;
        $emp_date_of_join = (!empty($ajax_form_data['edtj'])) ? altRealEscape($ajax_form_data['edtj']) : '';
        $emp_remarks = (!empty($ajax_form_data['ermk'])) ? altRealEscape($ajax_form_data['ermk']) : '';
        $emp_exp = (!empty($ajax_form_data['exp'])) ? altRealEscape($ajax_form_data['exp']) : '';
        $emp_desig_id = (!empty($ajax_form_data['edgn'])) ? altRealEscape($ajax_form_data['edgn']) : 0;
        $emp_row_id = altRealEscape($ajax_form_data['emp_row_id']);

        $emp_id = (!empty($ajax_form_data['emp_id'])) ? altRealEscape($ajax_form_data['emp_id']) : '';
        $emp_department = (!empty($ajax_form_data['emp_department'])) ? $ajax_form_data['emp_department'] : 0;
        $emp_salary = (!empty($ajax_form_data['emp_salary'])) ? altRealEscape($ajax_form_data['emp_salary']) : '';
        $emp_webmail = (!empty($ajax_form_data['emp_webmail'])) ? altRealEscape($ajax_form_data['emp_webmail']) : '';
        $emergeny_contact_name = (!empty($ajax_form_data['emergeny_contact_name'])) ? altRealEscape($ajax_form_data['emergeny_contact_name']) : '';
        $emergeny_contact_mobile = (!empty($ajax_form_data['emergeny_contact_mobile'])) ? altRealEscape($ajax_form_data['emergeny_contact_mobile']) : '';
        $current_address = (!empty($ajax_form_data['current_address'])) ? altRealEscape($ajax_form_data['current_address']) : '';
        $permanent_address = (!empty($ajax_form_data['permanent_address'])) ? altRealEscape($ajax_form_data['permanent_address']) : '';
        $emp_aadhaar = (!empty($ajax_form_data['emp_aadhaar'])) ? altRealEscape($ajax_form_data['emp_aadhaar']) : '';
        $emp_pan = (!empty($ajax_form_data['emp_pan'])) ? altRealEscape($ajax_form_data['emp_pan']) : '';
        $emp_salary_ac_number = (!empty($ajax_form_data['emp_salary_ac_number'])) ? altRealEscape($ajax_form_data['emp_salary_ac_number']) : '';
        $emp_salary_ac_ifsc = (!empty($ajax_form_data['emp_salary_ac_ifsc'])) ? altRealEscape($ajax_form_data['emp_salary_ac_ifsc']) : '';
        $emp_uan = (!empty($ajax_form_data['emp_uan'])) ? altRealEscape($ajax_form_data['emp_uan']) : '';
        $emp_esic_ip_number = (!empty($ajax_form_data['emp_esic_ip_number'])) ? altRealEscape($ajax_form_data['emp_esic_ip_number']) : '';
        $employee_report_time = (!empty($ajax_form_data['emp_rprt_tm'])) ? altRealEscape($ajax_form_data['emp_rprt_tm']) : 0;


        if (($emp_name == "") || ($emp_id == "")) {
            $response["message"] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $getEmpData = getData(Table::EMPLOYEE_DETAILS, [
            EMPLOYEE_DETAILS::EMPLOYEE_NAME,
            EMPLOYEE_DETAILS::EMPLOYEE_ID
        ], [
            EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS,
            EMPLOYEE_DETAILS::CLIENT_ID => $_SESSION[CLIENT_ID],
            EMPLOYEE_DETAILS::ID => $emp_row_id
        ]);
        if (count($getEmpData) <= 0) {
            $response["message"] = "No Employee was found against the Employee ID";
            sendRes();
        }
        $cols = [
            EMPLOYEE_DETAILS::EMPLOYEE_NAME => $emp_name,
            EMPLOYEE_DETAILS::EMPLOYEE_DESIGNATION_ID => $emp_desig_id,
            EMPLOYEE_DETAILS::LAST_UPDATE_DATE => getToday(),
            EMPLOYEE_DETAILS::EMPLOYEE_ID => $emp_id,
            EMPLOYEE_DETAILS::REPORTING_TIME => $employee_report_time
        ];
        if (!empty($emp_date_of_birth) || ($emp_date_of_birth != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_BIRTH] = $emp_date_of_birth;
        }
        $cols[EMPLOYEE_DETAILS::EMPLOYEE_MOTHER_NAME] = $emp_mother_name;
        $cols[EMPLOYEE_DETAILS::EMPLOYEE_FATHER_NAME] = $emp_father_name;
        $cols[EMPLOYEE_DETAILS::EMPLOYEE_MOBILE] = $emp_mobile;
        $cols[EMPLOYEE_DETAILS::EMPLOYEE_EMAIL] = $emp_email;
        $cols[EMPLOYEE_DETAILS::EMPLOYEE_BLOOD_GROUP] = $blood_group;
        if (!empty($emp_payroll) || ($emp_payroll != 0)) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL] = $emp_payroll;
        }
        if (!empty($emp_date_of_join) || ($emp_date_of_join != "")) {
            $cols[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING] = $emp_date_of_join;
        }
        $cols[EMPLOYEE_DETAILS::REMARKS] = $emp_remarks;
        $cols[EMPLOYEE_DETAILS::REMARK_BY] = $_SESSION[RID];
        $cols[EMPLOYEE_DETAILS::EMPLOYEE_EXPERIENCE_DURATION] = $emp_exp;
        if (!empty($emp_department) || ($emp_department != 0)) {
            $cols[EMPLOYEE_DETAILS::DEPARTMENT_ID] = $emp_department;
        }
        $cols[EMPLOYEE_DETAILS::SALARY_AMOUNT] = $emp_salary;
        $cols[EMPLOYEE_DETAILS::WEBMAIL_ADDRESS] = $emp_webmail;
        $cols[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME] = $emergeny_contact_name;
        $cols[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_MOBILE_NUMBER] = $emergeny_contact_mobile;
        $cols[EMPLOYEE_DETAILS::CURRENT_ADDRESS] = $current_address;
        $cols[EMPLOYEE_DETAILS::PERMANENT_ADDRESS] = $permanent_address;
        $cols[EMPLOYEE_DETAILS::AADHAAR_NUMBER] = $emp_aadhaar;
        $cols[EMPLOYEE_DETAILS::PAN_NUMBER] = $emp_pan;
        $cols[EMPLOYEE_DETAILS::SALARY_ACCOUNT_NUMBER] = $emp_salary_ac_number;
        $cols[EMPLOYEE_DETAILS::SALARY_ACCOUNT_IFSC_CODE] = $emp_salary_ac_ifsc;
        $cols[EMPLOYEE_DETAILS::UAN_NUMBER] = $emp_uan;
        $cols[EMPLOYEE_DETAILS::ESIC_IP_NUMBER] = $emp_esic_ip_number;

        $update = updateData(Table::EMPLOYEE_DETAILS, $cols, [
            EMPLOYEE_DETAILS::ID => $emp_row_id,
            EMPLOYEE_DETAILS::CLIENT_ID => $_SESSION[CLIENT_ID],
            EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS
        ]);
        if (!$update['res']) {
            logError("Failed to update employee details. employee row id: " + $emp_row_id, $update['error']);
            $response['message'] = ERROR_2;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Updated Successfully";
        sendRes();
        break;
    case 'ADD_DEPARTMENT':
        $department_name = (!empty($ajax_form_data['dnm'])) ? altRealEscape($ajax_form_data['dnm']) : "";

        if (($department_name == "") || ($department_name == null) || (empty($department_name))) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }

        $getDepartmentData = getData(Table::DEPARTMENTS, [
            DEPARTMENTS::DEPARTMENT_NAME
        ], [
            DEPARTMENTS::CLIENT_ID => $_SESSION[CLIENT_ID],
            DEPARTMENTS::STATUS => ACTIVE_STATUS
        ]);
        if (count($getDepartmentData) > 0) {
            foreach ($getDepartmentData as $key => $v) {
                if ($v[DEPARTMENTS::DEPARTMENT_NAME] == $department_name) {
                    $response['message'] = $department_name . " Already Exists";
                    sendRes();
                }
            }
        }
        $save = setData(Table::DEPARTMENTS, [
            DEPARTMENTS::CLIENT_ID => $_SESSION[CLIENT_ID],
            DEPARTMENTS::DEPARTMENT_NAME => $department_name,
            DEPARTMENTS::ADDED_BY => $_SESSION[RID],
            DEPARTMENTS::STATUS => ACTIVE_STATUS,
            DEPARTMENTS::CREATION_DATE => getToday(true)
        ]);
        if (!$save['res']) {
            logError("Department addition failed. Name: " . $department_name, $save['error']);
            $response['message'] = ERROR_2;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Saved Successfully";
        sendRes();
        break;
    case 'EMPLOYEE_ACTIVE':
        $row_id = $ajax_form_data['row_id'];
        $act_status = $ajax_form_data['act_status'];
        $inactiveReason = isset($ajax_form_data['oth'])?altRealEscape($ajax_form_data['oth']):"";
        $emp_last_working_day = (isset($ajax_form_data['lwd'])) ? altRealEscape($ajax_form_data['lwd']) : "";

        $response['error'] = false;
        $getdata = getData(Table::EMPLOYEE_DETAILS, [EMPLOYEE_DETAILS::ACTIVE], [
            EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS,
            EMPLOYEE_DETAILS::CLIENT_ID => $_SESSION[CLIENT_ID],
            EMPLOYEE_DETAILS::ID => $row_id
        ]);

        if (count($getdata) > 0) {
            if ($act_status == $getdata[0][EMPLOYEE_DETAILS::ACTIVE]) {
                // $response['message'] = "Requested status is already matching with the database";
                $response['message'] = "Request updated successfully";
                sendRes();
            }
            $empCols=[
                EMPLOYEE_DETAILS::ACTIVE => $act_status,
                EMPLOYEE_DETAILS::LAST_WORKING_DAY => $emp_last_working_day
            ];
            if (($act_status==OTHER_REASON)&&($inactiveReason!="")) {
                $empCols[EMPLOYEE_DETAILS::INACTIVE_REASON] = $inactiveReason;
            }
            $change = updateData(Table::EMPLOYEE_DETAILS, $empCols, [
                EMPLOYEE_DETAILS::ID => $row_id,
                EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS,
                EMPLOYEE_DETAILS::CLIENT_ID => $_SESSION[CLIENT_ID]
            ]);
            if (!$change['res']) {
                logError("Failed to change Employee active status for employee id: " + $row_id + ", Active Status: " + $act_status, $change['error']);
                $response['error'] = true;
                $response['message'] = ERROR_2;
                sendRes();
            }
            $changeUserStatus=updateData(Table::USERS,[
                USERS::ACTIVE=>($act_status!=1)?0:1
            ],[
                USERS::EMPLOYEE_ID=>$row_id,
                USERS::CLIENT_ID=>$_SESSION[CLIENT_ID]
            ]);
            if (!$changeUserStatus['res']) {
                logError("Failed to change Employee active status on the Users Table for employee id: " + $row_id + ", Active Status: " + $act_status, $changeUserStatus['error']);
                $response['error'] = true;
                $response['message'] = ERROR_2;
                sendRes();
            }
            $response['error'] = false;
            $response['message'] = "Status Updated";
            sendRes();
        } else {
            $response['error'] = true;
            $response['message'] = "No Employee Found for the selected row";
            sendRes();
        }
        break;
    case 'UPDATE_PROFILE':
        $name = (!empty($ajax_form_data['nm'])) ? altRealEscape($ajax_form_data['nm']) : "";
        $email = (!empty($ajax_form_data['em'])) ? altRealEscape($ajax_form_data['em']) : "";
        $mobile = (!empty($ajax_form_data['mb'])) ? altRealEscape($ajax_form_data['mb']) : "";
        if (($name == "") || ($email == "") || ($mobile == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        // $getdata = getData(Table::USERS, [
        //     Users::NAME,
        //     Users::EMAIL,
        //     Users::MOBILE
        // ], [
        //     Users::STATUS => ACTIVE_STATUS,
        //     Users::CLIENT_ID => $_SESSION[CLIENT_ID]
        // ]);
        $sql = 'SELECT ' . Users::NAME . ',' .
            Users::EMAIL . ',' .
            Users::MOBILE . ' FROM ' .
            Table::USERS . ' WHERE ' .
            Users::STATUS . ' = "' .
            ACTIVE_STATUS . '" AND ' .
            Users::CLIENT_ID . ' = ' .
            $_SESSION[CLIENT_ID] . ' AND ' .
            Users::ID . ' NOT IN ("' .
            $_SESSION[RID] . '")';
        $getdata = getCustomData($sql);
        if (count($getdata) > 0) {
            foreach ($getdata as $k => $v) {
                if ($v[Users::NAME] == $name) {
                    $response['message'] = "Please choose a different Name";
                    sendRes();
                }
                if ($v[Users::EMAIL] == $email) {
                    $response['message'] = "Please choose a different Email address";
                    sendRes();
                }
                if ($v[Users::MOBILE] == $mobile) {
                    $response['message'] = "Please insert a different Mobile Number";
                    sendRes();
                }
            }
        } else {
            logError("Failed to find any user on the database to update profile.", "");
            $response['message'] = ERROR_1;
            sendRes();
        }
        $update = updateData(Table::USERS, [
            Users::NAME => $name,
            Users::EMAIL => $email,
            Users::MOBILE => $mobile
        ], [
            Users::ID => $_SESSION[RID],
            Users::CLIENT_ID => $_SESSION[CLIENT_ID],
            Users::STATUS => ACTIVE_STATUS
        ]);
        if (!$update['res']) {
            logError("Failed to update user profile for the user ID: " . $_SESSION[RID], $update['error']);
            $response['message'] = ERROR_2;
            sendRes();
        }
        $_SESSION[USERNAME] = $name;
        $_SESSION[USER_ID] = $email;
        $response['error'] = false;
        $response['message'] = "Updated Successfully";
        sendRes();
        break;
    case 'DELETE_ITEM':
        $action = altRealEscape($ajax_form_data['action']);
        $row_id = $ajax_form_data['id'];
        if ((empty($action)) || ($row_id == 0) || (empty($row_id))) {
            $response['message'] = ERROR_1;
            sendRes();
        }
        $table = $tb = "";
        switch ($action) {
            case 'employee_list':
                $table = Table::EMPLOYEE_DETAILS;
                $tb = 'EMPLOYEE_DETAILS';
                break;
            case 'designation':
                $table = Table::DESIGNATIONS;
                $tb = 'DESIGNATIONS';
                break;
            case 'department':
                $table = Table::DEPARTMENTS;
                $tb = 'DEPARTMENTS';
                break;
            case 'tax_type':
                $table = Table::TYPE_OF_TAX;
                $tb = 'TYPE_OF_TAX';
                break;
            case 'audit_type':
                $table = Table::AUDIT_TYPES;
                $tb = 'AUDIT_TYPES';
                break;
            case 'company_industry':
                $table = Table::COMPANY_INDUSTRY_TYPE;
                $tb = 'COMPANY_INDUSTRY_TYPE';
                break;
            case 'company':
                // $table = Table::COMPANY_INDUSTRY_TYPE;
                // $tb = 'COMPANY_INDUSTRY_TYPE';
                $response['message']="Under Maintenance";
                sendRes();
                break;
        }

        $delete = updateData($table, [
            $tb::STATUS => DEACTIVE_STATUS
        ], [
            $tb::ID => $row_id,
            $tb::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);

        if (!$delete['res']) {
            logError("Unable to delete item from " . $action . ", for row id: " . $row_id . ".", $delete['error']);
            $response['message'] = ERROR_3;
            sendRes();
        }
        if ($action=='employee_list') {
            $deleteUser = updateData(Table::USERS,[
                USERS::STATUS => DEACTIVE_STATUS
            ], [
                USERS::EMPLOYEE_ID=>$row_id,
                USERS::CLIENT_ID => $_SESSION[CLIENT_ID]
            ]);
            if (!$deleteUser['res']) {
                logError("Unable to delete user from users table, for employee id: " . $row_id . ".", $deleteUser['error']);
                // $response['message'] = ERROR_3;
                // sendRes();
            }
        }

        $response['error'] = false;
        $response['message'] = "Deleted Successfully";
        sendRes();
        break;
    case 'START_AUDIT':
        $company_id = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $expected_dt = isset($ajax_form_data['ed']) ? altRealEscape($ajax_form_data['ed']) : "";
        if (($company_id == 0) || ($expected_dt == "")) {
            $response['message'] = ERROR_1;
            sendRes();
        }
        $getAuditData = getData(Table::AUDITS_DATA, [
            AUDITS_DATA::ACTIVE
        ], [
            AUDITS_DATA::STATUS => ACTIVE_STATUS,
            AUDITS_DATA::COMPANY_ID => $company_id
        ]);
        if (count($getAuditData) > 0) {
            if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
                $response['message'] = "The Audit you are trying to start, is already started !";
                sendRes();
            }
        }
        $cols = [
            AUDITS_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDITS_DATA::COMPANY_ID => $company_id,
            AUDITS_DATA::USER_ID => $_SESSION[RID],
            AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE => $expected_dt,
            AUDITS_DATA::AUDIT_START_DATE => getToday(false),
            AUDITS_DATA::ACTIVE => 1,
            AUDITS_DATA::STATUS => ACTIVE_STATUS,
            AUDITS_DATA::CREATED_AT => getToday(),
            AUDITS_DATA::UPDATED_AT => getToday()
        ];
        $save = setData(Table::AUDITS_DATA, $cols);
        if (!$save['res']) {
            logError("Unabled to start the Audit for the company: " . $company_id, $save['error']);
            $response['message'] = ERROR_3;
            sendRes();
        }
        $response['error'] = false;
        $response['st_date'] = getFormattedDateTime(getToday());
        $response['message'] = "Audit Started";
        sendRes();
        break;
    case 'CLOSE_AUDIT':
        $company_id = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $approvalRequired = false;
        $audit_id = 0;
        if (($company_id == 0) || ($company_id == "")) {
            $response['error']=true;
            $response['message'] = ERROR_1;
            sendRes();
        }
        $getAuditData = getData(Table::AUDITS_DATA, [
            AUDITS_DATA::ID,
            AUDITS_DATA::ACTIVE,
        ], [
            AUDITS_DATA::STATUS => ACTIVE_STATUS,
            AUDITS_DATA::COMPANY_ID => $company_id
        ]);
        if (count($getAuditData) > 0) {
            $audit_id = $getAuditData[0][AUDITS_DATA::ID];
            if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 2) {
                $response['message'] = "The Audit you are trying to close, is already closed !";
                $response['error']=true;
                sendRes();
            }
        }
        $getAssessmentData=getData(Table::AUDIT_ASSESSMENT_DATA,[AUDIT_ASSESSMENT_DATA::ACTIVE],[
            AUDIT_ASSESSMENT_DATA::COMPANY_ID=>$company_id,
            AUDIT_ASSESSMENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if (count($getAssessmentData)==0) {
            $getApprovalData=getData(Table::AUDIT_CLOSE_REQUEST_DATA,[
                AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS
            ],[
                AUDIT_CLOSE_REQUEST_DATA::AUDIT_ID=>$audit_id,
                AUDIT_CLOSE_REQUEST_DATA::ACTIVE=>1,
                AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS=>1,
                AUDIT_CLOSE_REQUEST_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
            ]);
            if (count($getApprovalData)>0) {
                $approvalRequired = false;
            } else {
                $approvalRequired = true;
                $response['error']=false;
                $response['audit_id']=$audit_id;
                $response['approvalRequired'] = $approvalRequired;
                $response['message'] = "No assessment paper issued against this audit!";
                sendRes();
            }
        } else {
            $response['approvalRequired'] = false;
        }
        $updateData=updateData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE=>2,AUDITS_DATA::AUDIT_END_DATE=>getToday(false)],[
            AUDITS_DATA::COMPANY_ID=>$company_id,
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if (!$updateData['res']) {
            logError("Unabled to close the Audit for the company: " . $company_id, $updateData['error']);
            $response['error']=true;
            $response['message'] = ERROR_3;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Audit Closed";
        $response['approvalRequired'] = $approvalRequired;
        $response['audit_id']=$audit_id;
        sendRes();
        break;
    case 'AUDIT_CLOSE_REQUEST_SUBMIT':
        $audit_id = isset($ajax_form_data['audid']) ? $ajax_form_data['audid'] : 0;
        $reason = isset($ajax_form_data['reason']) ? altRealEscape($ajax_form_data['reason']) : "";
        if (($audit_id ==0)||($reason=="")) {
            $response['error']=true;
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $setInactivePrevReq=updateData(Table::AUDIT_CLOSE_REQUEST_DATA,[
            AUDIT_CLOSE_REQUEST_DATA::ACTIVE=>0
        ],[
            AUDIT_CLOSE_REQUEST_DATA::AUDIT_ID=>$audit_id,
            AUDIT_CLOSE_REQUEST_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if(!$setInactivePrevReq['res']){
            logError("Unabled to set inactive status to all previous audit close requests, Audit id: ".$audit_id,$setInactivePrevReq['error']);
            $response["message"]=ERROR_1;
            sendRes();
        }
        $save = setData(Table::AUDIT_CLOSE_REQUEST_DATA,[
            AUDIT_CLOSE_REQUEST_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDIT_CLOSE_REQUEST_DATA::AUDIT_ID=>$audit_id,
            AUDIT_CLOSE_REQUEST_DATA::REASON=>$reason,
            AUDIT_CLOSE_REQUEST_DATA::AUDITOR_ID=>$_SESSION[RID],
            AUDIT_CLOSE_REQUEST_DATA::REQUEST_DATE=>getToday(),
            AUDIT_CLOSE_REQUEST_DATA::CREATED_AT=>getToday(),
            AUDIT_CLOSE_REQUEST_DATA::UPDATED_AT=>getToday()
        ]);
        if(!$save['res']){
            logError("Unabled to insert Audit Close data, Audit ID: ".$audit_id.", Auditor ID: ".$_SESSION[RID], $save['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error']=false;
        $response['message']="Request raised successfully";
        sendRes();
        break;
    case 'GET_ACTIVE_AUDIT_COMPANIES':
        $active_audit_companies = '<option value="0" disabled selected>--- Select Tax Payer ---</option>';
        $getCompData = getData(Table::COMPANIES, [
            COMPANIES::COMPANY_NAME,
            COMPANIES::CREATED_AT,
            COMPANIES::ID
        ], [
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANIES::STATUS => ACTIVE_STATUS
        ]);
        if (count($getCompData) > 0) {
            foreach ($getCompData as $k => $v) {
                $getAuditData = getData(Table::AUDITS_DATA, [
                    AUDITS_DATA::ID,
                    AUDITS_DATA::AUDIT_START_DATE,
                    AUDITS_DATA::AUDIT_END_DATE,
                    AUDITS_DATA::ACTIVE
                ], [
                    AUDITS_DATA::STATUS => ACTIVE_STATUS,
                    AUDITS_DATA::COMPANY_ID => $v[COMPANIES::ID],
                    AUDITS_DATA::USER_ID => $_SESSION[RID]
                ]);
                switch ($_SESSION[USER_TYPE]) {
                    case SADMIN:
                    case ADMIN:
                        $active_audit_companies .= '
                            <option value="' . $v[COMPANIES::ID] . '">' . $v[COMPANIES::COMPANY_NAME] . '</option>
                        ';
                        break;
                    case EMPLOYEE:
                        if (count($getAuditData) > 0) {
                            // rip($getAuditData);
                            // exit;
                            foreach ($getAuditData as $ak => $av) {
                                // if ($av[AUDITS_DATA::ACTIVE] == 1) {
                                    $active_audit_companies .= '
                                        <option value="' . $v[COMPANIES::ID] . '">' . $v[COMPANIES::COMPANY_NAME] . '</option>
                                    ';
                                // }
                            }
                        }
                        break;
                }
            }
        } else {
            $active_audit_companies = '
                <option value="0" disabled selected>No Companies Found !</option>
            ';
        }
        $response['error'] = false;
        $response['active_audit_companies'] = $active_audit_companies;
        sendRes();
        break;
    case 'GET_QUERY_DETAILS':
        $company_id = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $query_row = '';
        $q_cols = [];
        $audit_closed=$company_inactive=false;
        $getAuditActiveStatus=getData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE],[
            AUDITS_DATA::COMPANY_ID=>$company_id,
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDITS_DATA::STATUS=>ACTIVE_STATUS
        ]);
        $getComActiveStat=getData(Table::COMPANIES,[COMPANIES::ACTIVE],[COMPANIES::ID=>$company_id]);
        if (count($getComActiveStat)>0) {
            if($getComActiveStat[0][COMPANIES::ACTIVE]==0){
                $company_inactive=true;
            } else {
                $company_inactive=false;
            }
        }
        if(count($getAuditActiveStatus)>0){
            if($getAuditActiveStatus[0][AUDITS_DATA::ACTIVE]==2){
                $audit_closed=true;
            }
        }
        switch ($_SESSION[USER_TYPE]) {
            case SADMIN:
            case ADMIN:
                $q_cols = [
                    QUERY_DATA::COMPANY_ID => $company_id,
                    QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                ];
                break;
            case EMPLOYEE:
                $q_cols = [
                    QUERY_DATA::COMPANY_ID => $company_id,
                    QUERY_DATA::USER_ID => $_SESSION[RID],
                    QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                ];
                break;
        }
        $getQueryData = getData(Table::QUERY_DATA, ['*'], $q_cols);
        // rip($getQueryData);
        // exit();
        if (count($getQueryData) > 0) {
            foreach ($getQueryData as $k => $v) {
                $reply_status = $query_status = $date_of_reply = $reply_extension = $tax_type= $audit_type = '<span class="badge badge-light"><small>'.EMPTY_VALUE.'</small></span>';
                $rextDate = '';
                if ($v[QUERY_DATA::AUDIT_TYPE_ID]!=0) {
                    $getAuditType=getData(Table::AUDIT_TYPES,[AUDIT_TYPES::AUDIT_TYPE],[
                        AUDIT_TYPES::ID=>$v[QUERY_DATA::AUDIT_TYPE_ID],
                        AUDIT_TYPES::CLIENT_ID=>$_SESSION[CLIENT_ID],
                        AUDIT_TYPES::STATUS=>ACTIVE_STATUS
                    ]);
                    if (count($getAuditType)>0) {
                        $audit_type=$getAuditType[0][AUDIT_TYPES::AUDIT_TYPE];
                    } else {
                        $audit_type=EMPTY_VALUE;
                    }
                }  else {
                    $audit_type=EMPTY_VALUE;
                }
                if ($v[QUERY_DATA::TAX_TYPE_ID]!=0) {
                    $getTaxType=getData(Table::TYPE_OF_TAX,[TYPE_OF_TAX::TYPE_OF_TAX],[
                        TYPE_OF_TAX::ID=>$v[QUERY_DATA::TAX_TYPE_ID],
                        TYPE_OF_TAX::CLIENT_ID=>$_SESSION[CLIENT_ID],
                        TYPE_OF_TAX::STATUS=>ACTIVE_STATUS
                    ]);
                    if (count($getTaxType)>0) {
                        $tax_type=$getTaxType[0][TYPE_OF_TAX::TYPE_OF_TAX];
                    } else {
                        $tax_type=EMPTY_VALUE;
                    }
                } else {
                    $tax_type=EMPTY_VALUE;
                }
                $checkIfPositionIssued = getData(Table::POSITION_PAPER_DATA, [
                    POSITION_PAPER_DATA::ACTIVE,
                    POSITION_PAPER_DATA::POSITION_PAPER_ID
                ], [
                    POSITION_PAPER_DATA::QUERY_ID => $v[QUERY_DATA::ID],
                    POSITION_PAPER_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                ]);
                $ppref='';
                if(count($checkIfPositionIssued)>0){
                    $getPPRef=getData(Table::POSITION_PAPERS,[
                        POSITION_PAPERS::REFERENCE_NO
                    ],[
                        POSITION_PAPERS::ID=>$checkIfPositionIssued[0][POSITION_PAPER_DATA::POSITION_PAPER_ID]
                    ]);
                    $ppref=$getPPRef[0][POSITION_PAPERS::REFERENCE_NO];
                }
                switch ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED]) {
                    case 0:
                    case 3:
                        $reply_status = '<span class="badge badge-light"><small>'.EMPTY_VALUE.'</small></span>';
                        if (count($checkIfPositionIssued) > 0) {
                            // $reply_status .= '&nbsp;<span class="badge badge-danger"><small>Position Paper Issued #'.$ppref.'</small></span>';
                        } else {
                            if ($v[QUERY_DATA::QUERY_STATUS] == 4) {
                                // $reply_status .= '&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>';
                            } else {
                                $reply_status .= (($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[QUERY_DATA::QUERY_STATUS] != 4) && (count($checkIfPositionIssued) == 0) && (!$audit_closed) && (!$company_inactive)) ? '&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddReply(' . $v[QUERY_DATA::ID] . ')"><i class="fas fa-plus"></i><small>Add Rep.</small></span>' : '';
                            }
                        }
                        break;
                    case 1:
                        $reply_status = '<span class="badge badge-success"><small>Submitted</small></span>';
                        break;
                    case 2:
                        $reply_status = '<span class="badge badge-danger"><small>Overdue</small></span>';
                        if (count($checkIfPositionIssued) > 0) {
                            // $reply_status .= '&nbsp;<span class="badge badge-danger"><small>Position Paper Issued #'.$ppref.'</small></span>';
                        } else {
                            if ($v[QUERY_DATA::QUERY_STATUS] == 4) {
                                // $reply_status .= '&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>';
                            } else {
                                $reply_status .= (($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[QUERY_DATA::QUERY_STATUS] != 4) && (count($checkIfPositionIssued) == 0) && (!$audit_closed) && (!$company_inactive)) ? '&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddReply(' . $v[QUERY_DATA::ID] . ')"><i class="fas fa-plus"></i><small>Add Rep.</small></span>' : '';
                            }
                        }
                        break;
                    case 4:
                        $reply_status = '<span class="badge badge-warning"><small>Partially Submitted</small></span>';
                        if (count($checkIfPositionIssued) > 0) {
                            // $reply_status .= '&nbsp;<span class="badge badge-danger"><small>Position Paper Issued #'.$ppref.'</small></span>';
                        } else {
                            if ($v[QUERY_DATA::QUERY_STATUS] == 4) {
                                // $reply_status .= '&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>';
                            } else {
                                $reply_status .= (($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[QUERY_DATA::QUERY_STATUS] != 4) && (count($checkIfPositionIssued) == 0) && (!$audit_closed) && (!$company_inactive)) ? '&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddReply(' . $v[QUERY_DATA::ID] . ')"><i class="fas fa-plus"></i><small>Add Rep.</small></span>' : '';
                            }
                        }
                        break;
                }
                switch ($v[QUERY_DATA::QUERY_STATUS]) {
                    case 1:
                        $query_status = '<span class="badge badge-success"><small>Open</small></span>';
                        break;
                    case 2:
                        $query_status = '<span class="badge badge-danger"><small>Close</small></span>';
                        break;
                    case 4:
                        $query_status = '<span class="badge badge-danger"><small>Force Closed</small></span>';
                        break;
                }
                $noticeQids =[];
                $nRefno='';
                $getNoticeRefNoQ="
                SELECT ".
                COMPANY_NOTICE_DATA::NOTICE_NO ." FROM ".
                Table::COMPANY_NOTICE_DATA." WHERE ". 
                COMPANY_NOTICE_DATA::QUERY_IDS ." LIKE '%".
                $v[QUERY_DATA::ID]."%' AND ".
                COMPANY_NOTICE_DATA::COMPANY_ID."="
                .$v[QUERY_DATA::COMPANY_ID]." AND ".
                COMPANY_NOTICE_DATA::CLIENT_ID."="
                .$_SESSION[CLIENT_ID];
                $getNoticeRefNo=getCustomData($getNoticeRefNoQ);
                if(count($getNoticeRefNo)>0){
                    $nRefno=$getNoticeRefNo[0][COMPANY_NOTICE_DATA::NOTICE_NO];
                }
                $checkIfNoticeIssued = getData(Table::COMPANY_NOTICE_DATA, [
                    COMPANY_NOTICE_DATA::QUERY_IDS,
                    COMPANY_NOTICE_DATA::NOTICE_NO
                ], [
                    COMPANY_NOTICE_DATA::COMPANY_ID => $v[QUERY_DATA::COMPANY_ID],
                    COMPANY_NOTICE_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                ]);
                if (count($checkIfNoticeIssued) > 0) {
                    foreach ($checkIfNoticeIssued as $cink => $cinv) {
                        $noticeQids[] = explode(',', $cinv[COMPANY_NOTICE_DATA::QUERY_IDS]);
                    }
                }
                // rip(array_unique(array_merge(...$noticeQids)));
                if ((count($noticeQids) > 0) && (in_array($v[QUERY_DATA::ID], array_unique(array_merge(...$noticeQids))))) {
                    $query_status .= '&nbsp<span class="badge badge-info"><small>Notice Issued #'.$nRefno.'</small></span>';
                }
                if ($v[QUERY_DATA::NOTICE_NO] == "") {
                    $notice = '<span class="badge badge-primary" style="cursor:pointer;"><i class="fas fa-plus"></i>Add</span>';
                } else {
                    $notice = '<span class="badge badge-info" style="cursor:pointer;"><i class="fas fa-info-circle"></i></span>';
                }
                if ($v[QUERY_DATA::DATE_OF_REPLY] == "") {
                    $date_of_reply = '<span class="badge badge-light"><small>'.EMPTY_VALUE.'</small></span>';
                } else {
                    $getAllReplyDt = getData(Table::QUERY_REPLY, [
                        QUERY_REPLY::DATE_OF_REPLY,
                        QUERY_REPLY::NO_OF_QUERY_SOLVED
                    ], [
                        QUERY_REPLY::QUERY_ID => $v[QUERY_DATA::ID],
                        QUERY_REPLY::CLIENT_ID => $_SESSION[CLIENT_ID]
                    ]);
                    $date_of_reply = getFormattedDateTime($v[QUERY_DATA::DATE_OF_REPLY]);
                    if (count($getAllReplyDt) > 0) {
                        $date_of_reply .= '&nbsp;<span class="badge badge-white cursor-pointer ' . TOOLTIP_CLASS . '" title="Click to See History" onclick="viewQueryReplyDates(' . $v[QUERY_DATA::ID] . ');"><i class="fas fa-info-circle"></i></span>';
                    }
                }

                $last_date_reply = ($v[QUERY_DATA::LAST_DATE_OF_REPLY] != "") ? getFormattedDateTime($v[QUERY_DATA::LAST_DATE_OF_REPLY]) : EMPTY_VALUE;
                $getExtData = getData(Table::QUERY_EXTENSION_DATES, [
                    QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED,
                    QUERY_EXTENSION_DATES::EXTENTION_END_DATE,
                    QUERY_EXTENSION_DATES::EXTENSION_DAYS,
                    QUERY_EXTENSION_DATES::ACTIVE,
                    QUERY_EXTENSION_DATES::ID
                ], [
                    QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
                    QUERY_EXTENSION_DATES::ACTIVE => 1,
                    QUERY_EXTENSION_DATES::QUERY_ID => $v[QUERY_DATA::ID]
                ]);
                if (count($getExtData) > 0) {
                    if (
                        ($getExtData[0][QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED] == 1) &&
                        ($getExtData[0][QUERY_EXTENSION_DATES::ACTIVE] == 1)
                    ) {
                        $rextDate = $getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE];
                        // $response['rextDateeee']=$rextDate;
                        if (getToday(false) <= getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                            $reply_extension = '<span class="badge badge-white"><small>' . $getExtData[0][QUERY_EXTENSION_DATES::EXTENSION_DAYS] . ' Days (<b>Due Date:</b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                            $last_date_reply = getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]);
                            
                        } else {
                            $ldd = (getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'd') + 1);
                            $ldr = getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-' . $ldd);
                            // $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) ? ((count($checkIfPositionIssued) > 0) ? '&nbsp;<span class="badge badge-danger"><small>Position Paper Issued #'.$ppref.'</small></span>' : '&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>') : (((!$audit_closed) && ($_SESSION[USER_TYPE]==EMPLOYEE) && (!$company_inactive)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[QUERY_DATA::ID] . ', \'' . $getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE] . '\');"><i class="fas fa-plus"></i><small>Add Ext.</small></span>':'');
                            $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) && ((count($checkIfPositionIssued) > 0)) ? $reply_extension : (((!$audit_closed) && ($_SESSION[USER_TYPE]==EMPLOYEE) && (!$company_inactive) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[QUERY_DATA::ID] . ', \'' . $getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE] . '\');"><i class="fas fa-plus"></i><small>Add Ext.</small></span>':$reply_extension);
                            if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                                $reply_extension = '<span class="badge badge-white"><small><span class="text-danger">Ext. Exceeded !</span> (<b>Due Date:</b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                            }
                        }
                    }
                    if (
                        ($getExtData[0][QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED] == 0) &&
                        ($getExtData[0][QUERY_EXTENSION_DATES::ACTIVE] == 1)
                    ) {
                        if (getToday(false) <= getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                            $reply_extension = '<span class="badge badge-white"><small> <span class="text-danger">Awating for Approval !</span> (<b>Req. Ext. Date: </b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                            if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                                // $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) ? ((count($checkIfPositionIssued) > 0) ? '&nbsp;<span class="badge badge-danger"><small>Position Paper Issued #'.$ppref.'</small></span>' : '&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>') : (((!$audit_closed) && (!$company_inactive)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExtApproval(' . $getExtData[0][QUERY_EXTENSION_DATES::ID] . ', ' . $v[QUERY_DATA::ID] . ');"><i class="fas fa-plus"></i><small>Add Approval.</small></span>':'').'<span class="text-dark"><small>(<b>Req. Date: </b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                                $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) && (count($checkIfPositionIssued) > 0) ? $reply_extension : (((!$audit_closed) && (!$company_inactive) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExtApproval(' . $getExtData[0][QUERY_EXTENSION_DATES::ID] . ', ' . $v[QUERY_DATA::ID] . ');"><i class="fas fa-plus"></i><small>Add Approval.</small></span>':$reply_extension).'<span class="text-dark"><small>(<b>Req. Date: </b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                            }
                        } else {
                            $ldd = (getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'd') + 1);
                            $ldr = getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-' . $ldd);
                            // $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) ? '<span class="badge badge-danger"><small>Query Force Closed</small></span>' : (((!$audit_closed) && ($_SESSION[USER_TYPE] == EMPLOYEE) && (!$company_inactive)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[QUERY_DATA::ID] . ', \'' . $v[QUERY_DATA::LAST_DATE_OF_REPLY] . '\');"><i class="fas fa-plus"></i><small>Add Ext.</small></span>':'');
                            $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) ? $reply_extension : (((!$audit_closed) && ($_SESSION[USER_TYPE] == EMPLOYEE) && (!$company_inactive) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[QUERY_DATA::ID] . ', \'' . $v[QUERY_DATA::LAST_DATE_OF_REPLY] . '\');"><i class="fas fa-plus"></i><small>Add Ext.</small></span>':$reply_extension);
                            if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                                $reply_extension = '<span class="badge badge-white"><small><span class="text-danger">Ext. Exceeded !</span> (<b>Due Date:</b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                            }
                        }
                    }
                    if (
                        ($getExtData[0][QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED] == 2) &&
                        ($getExtData[0][QUERY_EXTENSION_DATES::ACTIVE] == 1)
                    ) {
                        $reply_extension = '<span class="badge badge-white"><small> <span class="text-danger">Extension Rejected !</span> (<b>Req. Ext. Date: </b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                        $ldd = (getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'd') + 1);
                        $ldr = getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-' . $ldd);
                        $reply_extension .= ($v[QUERY_DATA::QUERY_STATUS] == 4) ? '' : (((!$audit_closed) && ($_SESSION[USER_TYPE] == EMPLOYEE) && (!$company_inactive) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) ? '<br><span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[QUERY_DATA::ID] . ', \'' . $v[QUERY_DATA::LAST_DATE_OF_REPLY] . '\');"><i class="fas fa-plus"></i><small>Add Ext.</small></span>':'');

                        // if (getToday(false) <= getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                        //     $reply_extension = '<span class="badge badge-white"><small> <span class="text-danger">Awating for Approval !</span> (<b>Req. Ext. Date: </b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                        //     if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                        //         $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) ? ((count($checkIfPositionIssued) > 0) ? '&nbsp;<span class="badge badge-danger"><small>Position Paper Issued</small></span>' : '&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>') : (((!$audit_closed) && (!$company_inactive)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExtApproval(' . $getExtData[0][QUERY_EXTENSION_DATES::ID] . ', ' . $v[QUERY_DATA::ID] . ');"><i class="fas fa-plus"></i><small>Add Approval.</small></span>':'').'<span class="text-dark"><small>(<b>Req. Date: </b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                        //     }
                        // } else {
                        //     $ldd = (getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'd') + 1);
                        //     $ldr = getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-' . $ldd);
                        //     $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) ? '<span class="badge badge-danger"><small>Query Force Closed</small></span>' : (((!$audit_closed) && ($_SESSION[USER_TYPE] == EMPLOYEE) && (!$company_inactive)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[QUERY_DATA::ID] . ', \'' . $v[QUERY_DATA::LAST_DATE_OF_REPLY] . '\');"><i class="fas fa-plus"></i><small>Add Ext.</small></span>':'');
                        //     if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                        //         $reply_extension = '<span class="badge badge-white"><small><span class="text-danger">Ext. Exceeded !</span> (<b>Due Date:</b> ' . getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                        //     }
                        // }
                    }
                } else {
                    $ldd = (getFormattedDateTime($v[QUERY_DATA::LAST_DATE_OF_REPLY], 'd') + 1);
                    $ldr = getFormattedDateTime($v[QUERY_DATA::LAST_DATE_OF_REPLY], 'Y-m-' . $ldd);
                    $reply_extension = ($v[QUERY_DATA::QUERY_STATUS] == 4) && (count($checkIfPositionIssued) > 0) ? $reply_extension : (((!$audit_closed) && ($_SESSION[USER_TYPE] == EMPLOYEE) && (!$company_inactive) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) ? '<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[QUERY_DATA::ID] . ', \'' . $v[QUERY_DATA::LAST_DATE_OF_REPLY] . '\');"><i class="fas fa-plus"></i><small>Add Ext.</small></span>':$reply_extension);
                    // if($v[QUERY_DATA::QUERY_STATUS] == 4){
                    //     if((count($checkIfPositionIssued) > 0)){
                    //         // $reply_extension='&nbsp;<span class="badge badge-danger"><small>Position Paper Issued #'.$ppref.'</small></span>';
                    //     } else {
                    //         // $reply_extension='&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>';
                    //      }
                    // } else {
                    //     if((!$audit_closed) && ($_SESSION[USER_TYPE] == EMPLOYEE) && (!$company_inactive)){
                    //         $reply_extension='<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[QUERY_DATA::ID] . ', \'' . $v[QUERY_DATA::LAST_DATE_OF_REPLY] . '\');"><i class="fas fa-plus"></i><small>Add Ext.</small></span>';
                    //     }
                    // }
                }
                if ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] == 1) {
                    // $reply_extension = '<span class="badge badge-white"><small><span class="text-success">Already Submitted !</span></small></span>';
                }
                $response['rextDateeee']=$rextDate;
                if ($v[QUERY_DATA::LAST_DATE_OF_REPLY] < getToday(false)) {
                    $getExtData = getData(Table::QUERY_EXTENSION_DATES, [
                        QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED,
                        QUERY_EXTENSION_DATES::EXTENTION_END_DATE,
                        QUERY_EXTENSION_DATES::EXTENSION_DAYS,
                        QUERY_EXTENSION_DATES::ACTIVE,
                        QUERY_EXTENSION_DATES::ID
                    ], [
                        QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
                        QUERY_EXTENSION_DATES::ACTIVE => 1,
                        QUERY_EXTENSION_DATES::QUERY_ID => $v[QUERY_DATA::ID]
                    ]);
                    if (count($getExtData) > 0) {
                        if (
                            ($getExtData[0][QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED] == 1) &&
                            ($getExtData[0][QUERY_EXTENSION_DATES::ACTIVE] == 1)
                        ) {
                            $rextDate = $getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE];
                        }
                    }
                    if (($rextDate != "") && ($rextDate < getToday(false))) {
                        if ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1) {
                            $reply_status = '<span class="badge badge-danger"><small>Overdue</small></span>';
                        }
                        if (count($checkIfPositionIssued) > 0) {
                            // $reply_status.='&nbsp;<span class="badge badge-danger"><em>Position Paper Issued</em></span>';
                        } else {
                            if (($v[QUERY_DATA::QUERY_STATUS] == 4) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) {
                            // if (($v[QUERY_DATA::QUERY_STATUS] == 4) && (!in_array($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED], [0, 3, 2, 4]))) {
                                // $reply_status .= '&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>';
                            } else {
                                $reply_status .= (($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[QUERY_DATA::QUERY_STATUS] != 4) && (count($checkIfPositionIssued) == 0) && (!$audit_closed) && (!$company_inactive) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) ? '&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddReply(' . $v[QUERY_DATA::ID] . ')"><i class="fas fa-plus"></i><small>Add Rep.</small></span>' : '';
                            }
                        }
                    } else {
                        if (($v[QUERY_DATA::QUERY_STATUS] == 1)&&($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) {
                            $reply_status = (($rextDate < getToday(false)))?'<span class="badge badge-danger"><small>Overdue</small></span>':'';
                        }
                        if (count($checkIfPositionIssued) > 0) {
                            // $reply_status.='&nbsp;<span class="badge badge-danger"><em>Position Paper Issued</em></span>';
                        } else {
                            if (($v[QUERY_DATA::QUERY_STATUS] == 4) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) {
                            // if (($v[QUERY_DATA::QUERY_STATUS] == 4) && (!in_array($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED], [0, 3, 2, 4]))) {
                                // $reply_status .= '&nbsp;<span class="badge badge-danger"><small>Query Force Closed</small></span>';
                            } else {
                                switch ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED]) {
                                    case 0:
                                    case 3:
                                        $reply_status .= '&nbsp;<span class="badge badge-light"><small>'.EMPTY_VALUE.'</small></span>';
                                        break;
                                    case 4:
                                        $reply_status .= '&nbsp;<span class="badge badge-warning"><small>Partially Submitted</small></span>';
                                        break;
                                }
                                $reply_status .= (($_SESSION[USER_TYPE] == EMPLOYEE) && ($v[QUERY_DATA::QUERY_STATUS] != 4) && (count($checkIfPositionIssued) == 0) && (!$audit_closed) && (!$company_inactive) && ($v[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] != 1)) ? '&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddReply(' . $v[QUERY_DATA::ID] . ')"><i class="fas fa-plus"></i><small>Add Rep.</small></span>' : '';
                            }
                        }
                    }
                }
                $query_row .= '<tr>
                    <td>' . ($k + 1) . '</td>
                    <td>' . $v[QUERY_DATA::QUERY_NO] . '</td>
                    <td>' . $v[QUERY_DATA::TOTAL_NO_OF_QUERY] . '</td>
                    <td>' . $audit_type . '</td>
                    <td>' . $tax_type . '</td>
                    <td>' . getFormattedDateTime($v[QUERY_DATA::DATE_OF_ISSUE]) . '</td>
                    <td>' . $date_of_reply . '</td>
                    <td>' . $last_date_reply . '</td>
                    <td>' . $reply_extension . '</td>
                    <td>' . $reply_status . '</td>
                    <td>' . $v[QUERY_DATA::NO_OF_QUERY_SOLVED] . '</td>
                    <td>' . $query_status . '</td>
                </tr>';
            }
        } else {
            $query_row = '<tr class="animated fadeInDown">
                <td colspan="12">
                    <div class="alert alert-danger" role="alert">
                        No Query found !
                    </div>
                </td>
            </tr>';
        }
        $response['error'] = false;
        $response['audit_closed'] = $audit_closed;
        $response['company_inactive'] = $company_inactive;
        $response['query_row'] = $query_row;
        sendRes();
        break;
    case 'ADD_QUERY':
        $query_no = (isset($ajax_form_data['qno'])) ? altRealEscape($ajax_form_data['qno']) : "";
        $case_code = (isset($ajax_form_data['cc'])) ? altRealEscape($ajax_form_data['cc']) : "";
        $total_no_of_query = (isset($ajax_form_data['noq'])) ? altRealEscape($ajax_form_data['noq']) : "";
        $date_of_issue = (isset($ajax_form_data['doi'])) ? altRealEscape($ajax_form_data['doi']) : "";
        $audit_type_id = (isset($ajax_form_data['atype'])) ? altRealEscape($ajax_form_data['atype']) : 0;
        $type_of_tax_id = (isset($ajax_form_data['ttype'])) ? altRealEscape($ajax_form_data['ttype']) : 0;
        $days_to_reply = (isset($ajax_form_data['dtr'])) ? altRealEscape($ajax_form_data['dtr']) : "";
        $end_date_of_reply = (isset($ajax_form_data['ldor'])) ? altRealEscape($ajax_form_data['ldor']) : "";
        $company_id = (isset($ajax_form_data['com'])) ? altRealEscape($ajax_form_data['com']) : 0;
        $memo_id = (isset($ajax_form_data['mid'])) ? $ajax_form_data['mid'] : 0;

        if (
            ($query_no == "") ||
            ($date_of_issue == "") ||
            ($company_id == "") ||
            ($company_id == 0) ||
            ($audit_type_id == 0) ||
            ($type_of_tax_id == 0)
        ) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $audit_closed=false;
        $getAuditActiveStatus=getData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE],[
            AUDITS_DATA::COMPANY_ID=>$company_id,
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDITS_DATA::STATUS=>ACTIVE_STATUS
        ]);
        if(count($getAuditActiveStatus)>0){
            if($getAuditActiveStatus[0][AUDITS_DATA::ACTIVE]==2){
                $audit_closed=true;
                $response['message']="Audit has been closed!";
                $response['audit_closed']=$audit_closed;
                sendRes();
            }
        }
        $getExistingQuery = getData(Table::QUERY_DATA, [
            QUERY_DATA::QUERY_REPLY_IS_SUBMITTED,
            QUERY_DATA::AUDIT_TYPE_ID,
            QUERY_DATA::TAX_TYPE_ID,
            QUERY_DATA::QUERY_NO,
            QUERY_DATA::COMPANY_ID
        ], [
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (count($getExistingQuery) > 0) {
            foreach ($getExistingQuery as $eck => $ecv) {
                if (($ecv[QUERY_DATA::QUERY_NO] == $query_no)&&($company_id == $ecv[QUERY_DATA::COMPANY_ID])) {
                    $response['message'] = "Query No. should be unique !";
                    sendRes();
                }
                if (
                    ($company_id == $ecv[QUERY_DATA::COMPANY_ID]) &&
                    ($type_of_tax_id == $ecv[QUERY_DATA::TAX_TYPE_ID]) &&
                    (($ecv[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] == 1) || ($ecv[QUERY_DATA::QUERY_REPLY_IS_SUBMITTED] == 3))
                ) {
                    $response['message'] = "Already active query found with this tax type !";
                    sendRes();
                }
            }
        }
        $cols = [
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_DATA::USER_ID => $_SESSION[RID],
            QUERY_DATA::QUERY_NO => $query_no,
            QUERY_DATA::DATE_OF_ISSUE => $date_of_issue,
            QUERY_DATA::COMPANY_ID => $company_id,
            QUERY_DATA::AUDIT_TYPE_ID => $audit_type_id,
            QUERY_DATA::TAX_TYPE_ID => $type_of_tax_id
        ];
        if (($total_no_of_query != "") || ($total_no_of_query != null)) {
            $cols[QUERY_DATA::TOTAL_NO_OF_QUERY] = $total_no_of_query;
        }
        if ($memo_id != 0) {
            $cols[QUERY_DATA::MEMO_ID] = $memo_id;
        }
        if (($days_to_reply != "") || ($days_to_reply != null)) {
            $cols[QUERY_DATA::DAYS_TO_REPLY] = $days_to_reply;
            $cols[QUERY_DATA::LAST_DATE_OF_REPLY] = $end_date_of_reply;
        }
        $save = setData(Table::QUERY_DATA, $cols);
        if (!$save['res']) {
            logError("Unabled to insert query data: Qno.: " . $query_no, $save['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['message'] = "Query Inserted Successfully!";
        $response['error'] = false;
        sendRes();
        break;
    case 'SAVE_COMPANY_INDUSTRY':
        $com_industry_name = (isset($ajax_form_data['ci_nm'])) ? altRealEscape($ajax_form_data['ci_nm']) : "";

        if ($com_industry_name == "") {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $getInd = getData(Table::COMPANY_INDUSTRY_TYPE, [
            COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
        ], [
            COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE => $com_industry_name,
            COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS
        ]);
        if (count($getInd) > 0) {
            if ($getInd[0][COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE] == $com_industry_name) {
                $response['message'] = "Industry already exists !";
                sendRes();
            }
        }
        $save = setData(Table::COMPANY_INDUSTRY_TYPE, [
            COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE => $com_industry_name,
            COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_INDUSTRY_TYPE::USER_ID => $_SESSION[RID],
            COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS,
            COMPANY_INDUSTRY_TYPE::CREATED_AT => getToday(),
            COMPANY_INDUSTRY_TYPE::UPDATED_AT => getToday()
        ]);
        if (!$save['res']) {
            logError("Unabled to insert Company Industry: " . $com_industry_name, $save['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $getIndustry = getData(Table::COMPANY_INDUSTRY_TYPE, [
            COMPANY_INDUSTRY_TYPE::ID,
            COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
        ], [
            COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS
        ]);
        $table = "";
        if (count($getIndustry) > 0) {
            foreach ($getIndustry as $k => $v) {
                $actions = '
                <div class="" style="display:flex; justify-content: space-evenly;">
                    <div class="text-success" onclick="updateIndustry(' . $v[COMPANY_INDUSTRY_TYPE::ID] . ',\'' . $v[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE] . '\');"><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
                    <div class="text-danger cursor-pointer" onclick="initiateDelete(' . $v[COMPANY_INDUSTRY_TYPE::ID] . ', \'company_industry\')"><i style="font-size:15px; cursor: pointer" class="fas fa-trash-alt"></i></div>
                </div>
                ';
                $table .= '<tr id="industry_row_' . $v[COMPANY_INDUSTRY_TYPE::ID] . '">
                    <td>' . ($k + 1) . '</td>
                    <td class="industry_name_td">' . $v[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE] . '</td>
                    <td>' . $actions . '</td>
                </tr>';
            }
        } else {
            $table = '
            <tr class="animated fadeInDown">
                <td colspan="3">
                    <div class="alert alert-danger" role="alert">
                        No Industry found !
                    </div>
                </td>
            </tr>';
        }
        $response['message'] = "Industry Added Successfully!";
        $response['table'] = $table;
        $response['error'] = false;
        sendRes();
        break;
    case 'SAVE_AUDIT_TYPE':
        $audit_type_name = (isset($ajax_form_data['ci_nm'])) ? altRealEscape($ajax_form_data['ci_nm']) : "";

        if ($audit_type_name == "") {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $getInd = getData(Table::AUDIT_TYPES, [
            AUDIT_TYPES::AUDIT_TYPE
        ], [
            AUDIT_TYPES::AUDIT_TYPE => $audit_type_name,
            AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_TYPES::STATUS => ACTIVE_STATUS
        ]);
        if (count($getInd) > 0) {
            if ($getInd[0][AUDIT_TYPES::AUDIT_TYPE] == $audit_type_name) {
                $response['message'] = "Already exists !";
                sendRes();
            }
        }
        $save = setData(Table::AUDIT_TYPES, [
            AUDIT_TYPES::AUDIT_TYPE => $audit_type_name,
            AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_TYPES::USER_ID => $_SESSION[RID],
            AUDIT_TYPES::STATUS => ACTIVE_STATUS,
            AUDIT_TYPES::CREATED_AT => getToday(),
            AUDIT_TYPES::UPDATED_AT => getToday()
        ]);
        if (!$save['res']) {
            logError("Unabled to insert audit type data: " . $audit_type_name, $save['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $table = '';
        $getAuditTypeData = getData(Table::AUDIT_TYPES, ['*'], [AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID], AUDIT_TYPES::STATUS => ACTIVE_STATUS]);
        if (count($getAuditTypeData) > 0) {
            $sl = 1;
            foreach ($getAuditTypeData as $k => $v) {
                $actions = '
                <div class="" style="display:flex; justify-content: space-evenly;">
                    <div class="text-success" onclick="updateAuditType(' . $v[AUDIT_TYPES::ID] . ',\'' . $v[AUDIT_TYPES::AUDIT_TYPE] . '\');"><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
                    <div class="text-danger cursor-pointer" onclick="initiateDelete(' . $v[AUDIT_TYPES::ID] . ', \'audit_type\')"><i style="font-size:15px; cursor: pointer" class="fas fa-trash-alt"></i></div>
                </div>
                ';
                $table .= '
                <tr id="audit_type_' . $v[AUDIT_TYPES::ID] . '">
                    <td>' . $sl . '</td>
                    <td>' . altRealEscape($v[AUDIT_TYPES::AUDIT_TYPE]) . '</td>
                    <td>' . $actions . '</td>
                </tr>
                ';
                $sl++;
            }
        } else {
            $table = '
            <tr class="animated fadeInDown">
                <td colspan="3">
                    <div class="alert alert-danger" role="alert">
                        No Audit Type found !
                    </div>
                </td>
            </tr>';
        }
        $response['message'] = "Added Successfully!";
        $response['table'] = $table;
        $response['error'] = false;
        sendRes();
        break;
    case 'SAVE_TAX_TYPE':
        $tax_type_name = (isset($ajax_form_data['ci_nm'])) ? altRealEscape($ajax_form_data['ci_nm']) : "";

        if ($tax_type_name == "") {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $getInd = getData(Table::TYPE_OF_TAX, [
            TYPE_OF_TAX::TYPE_OF_TAX
        ], [
            TYPE_OF_TAX::TYPE_OF_TAX => $tax_type_name,
            TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
            TYPE_OF_TAX::STATUS => ACTIVE_STATUS
        ]);
        if (count($getInd) > 0) {
            if ($getInd[0][TYPE_OF_TAX::TYPE_OF_TAX] == $tax_type_name) {
                $response['message'] = "Already exists !";
                sendRes();
            }
        }
        $save = setData(Table::TYPE_OF_TAX, [
            TYPE_OF_TAX::TYPE_OF_TAX => $tax_type_name,
            TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
            TYPE_OF_TAX::USER_ID => $_SESSION[RID],
            TYPE_OF_TAX::STATUS => ACTIVE_STATUS,
            TYPE_OF_TAX::CREATED_AT => getToday(),
            TYPE_OF_TAX::UPDATED_AT => getToday()
        ]);
        if (!$save['res']) {
            logError("Unabled to insert tax type data: " . $tax_type_name, $save['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $table = '';
        $getTaxTypeData = getData(Table::TYPE_OF_TAX, ['*'], [TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID], TYPE_OF_TAX::STATUS => ACTIVE_STATUS]);
        if (count($getTaxTypeData) > 0) {
            $sl = 1;
            foreach ($getTaxTypeData as $k => $v) {
                $actions = '
                <div class="" style="display:flex; justify-content: space-evenly;">
                    <div class="text-success" onclick="updateTaxType(' . $v[TYPE_OF_TAX::ID] . ',\'' . $v[TYPE_OF_TAX::TYPE_OF_TAX] . '\');"><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
                    <div class="text-danger cursor-pointer" onclick="initiateDelete(' . $v[TYPE_OF_TAX::ID] . ', \'tax_type\')"><i style="font-size:15px; cursor: pointer" class="fas fa-trash-alt"></i></div>
                </div>
                ';
                $table .= '
                <tr id="tax_type_' . $v[TYPE_OF_TAX::ID] . '">
                    <td>' . $sl . '</td>
                    <td>' . altRealEscape($v[TYPE_OF_TAX::TYPE_OF_TAX]) . '</td>
                    <td>' . $actions . '</td>
                </tr>
                ';
                $sl++;
            }
        } else {
            $table = '
            <tr class="animated fadeInDown">
                <td colspan="3">
                    <div class="alert alert-danger" role="alert">
                        No Tax Type found !
                    </div>
                </td>
            </tr>';
        }
        $response['message'] = "Added Successfully!";
        $response['table'] = $table;
        $response['error'] = false;
        sendRes();
        break;
    case 'UPDATE_TAX_TYPE':
        $tax_id = isset($ajax_form_data['ttid']) ? $ajax_form_data['ttid'] : 0;
        $tax_type = isset($ajax_form_data['nm']) ? altRealEscape($ajax_form_data['nm']) : "";
        if (($tax_id == 0) || ($tax_type == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $updateTTdata = updateData(Table::TYPE_OF_TAX, [TYPE_OF_TAX::TYPE_OF_TAX => $tax_type, TYPE_OF_TAX::UPDATED_AT => getToday()], [
            TYPE_OF_TAX::ID => $tax_id,
            TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$updateTTdata['res']) {
            logError("Unabled to update tax type data, Tax type id: " . $tax_id, $updateTTdata['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Updated Successfully";
        sendRes();
        break;
    case 'UPDATE_DEPARTMENT':
        $tax_id = isset($ajax_form_data['ttid']) ? $ajax_form_data['ttid'] : 0;
        $tax_type = isset($ajax_form_data['nm']) ? altRealEscape($ajax_form_data['nm']) : "";
        if (($tax_id == 0) || ($tax_type == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $updateTTdata = updateData(Table::DEPARTMENTS, [DEPARTMENTS::DEPARTMENT_NAME => $tax_type], [
            DEPARTMENTS::ID => $tax_id,
            DEPARTMENTS::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$updateTTdata['res']) {
            logError("Unabled to update Department data, Department id: " . $tax_id, $updateTTdata['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Updated Successfully";
        sendRes();
        break;
    case 'UPDATE_DESIGNATION':
        $tax_id = isset($ajax_form_data['ttid']) ? $ajax_form_data['ttid'] : 0;
        $tax_type = isset($ajax_form_data['nm']) ? altRealEscape($ajax_form_data['nm']) : "";
        if (($tax_id == 0) || ($tax_type == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $updateTTdata = updateData(Table::DESIGNATIONS, [DESIGNATIONS::DESIGNATION_TITLE => $tax_type], [
            DESIGNATIONS::ID => $tax_id,
            DESIGNATIONS::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$updateTTdata['res']) {
            logError("Unabled to update Designation data, Designation id: " . $tax_id, $updateTTdata['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Updated Successfully";
        sendRes();
        break;
    case 'UPDATE_AUDIT_TYPE':
        $audit_type_id = isset($ajax_form_data['ttid']) ? $ajax_form_data['ttid'] : 0;
        $audit_type_name = isset($ajax_form_data['nm']) ? altRealEscape($ajax_form_data['nm']) : "";
        if (($audit_type_id == 0) || ($audit_type_name == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $updateTTdata = updateData(Table::AUDIT_TYPES, [AUDIT_TYPES::AUDIT_TYPE => $audit_type_name, AUDIT_TYPES::UPDATED_AT => getToday()], [
            AUDIT_TYPES::ID => $audit_type_id,
            AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$updateTTdata['res']) {
            logError("Unabled to update Audit type data, Audit type id: " . $audit_type_id, $updateTTdata['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Updated Successfully";
        sendRes();
        break;
    case 'UPDATE_INDUSTRY_TYPE':
        $audit_type_id = isset($ajax_form_data['ttid']) ? $ajax_form_data['ttid'] : 0;
        $audit_type_name = isset($ajax_form_data['nm']) ? altRealEscape($ajax_form_data['nm']) : "";
        if (($audit_type_id == 0) || ($audit_type_name == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $updateTTdata = updateData(Table::COMPANY_INDUSTRY_TYPE, [COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE => $audit_type_name, COMPANY_INDUSTRY_TYPE::UPDATED_AT => getToday()], [
            COMPANY_INDUSTRY_TYPE::ID => $audit_type_id,
            COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$updateTTdata['res']) {
            logError("Unabled to update Industry type data, Industry type id: " . $audit_type_id, $updateTTdata['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Updated Successfully";
        sendRes();
        break;
    case 'SAVE_COMPANY':
        $company_name       = (isset($ajax_form_data['cname']))  ? altRealEscape($ajax_form_data['cname'])  : "";
        $company_tin        = (isset($ajax_form_data['ctin']))   ? altRealEscape($ajax_form_data['ctin'])   : "";
        $company_industry   = (isset($ajax_form_data['citype'])) ? altRealEscape($ajax_form_data['citype']) : "";
        $company_code       = (isset($ajax_form_data['ccode']))  ? altRealEscape($ajax_form_data['ccode'])  : "";
        $company_case_code  = (isset($ajax_form_data['ccase']))  ? altRealEscape($ajax_form_data['ccase'])  : "";
        $company_audit_type = (isset($ajax_form_data['atype']))  ? altRealEscape($ajax_form_data['atype'])  : "";
        $company_tax_type   = (isset($ajax_form_data['ttype']))  ? ($ajax_form_data['ttype'])  : "";

        // echo "Get Type: ".gettype($company_audit_type);
        // echo "<br />";
        // echo implode(',', $company_tax_type);
        // echo rip($ajax_form_data);
        // echo "Data: ".$company_tax_type;
        // exit();

        if (($company_name == "") || ($company_tin == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $getCom = getData(Table::COMPANIES, [
            COMPANIES::ID,
            COMPANIES::COMPANY_NAME,
            COMPANIES::CASE_CODE,
            COMPANIES::COMPANY_CODE,
            COMPANIES::TAX_IDENTIFICATION_NUMBER
        ], [
            COMPANIES::STATUS => ACTIVE_STATUS,
            COMPANIES::ACTIVE => 1,
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (count($getCom) > 0) {
            foreach ($getCom as $cck => $ccv) {
                if (
                    ($ccv[COMPANIES::COMPANY_NAME] == $company_name) ||
                    ($ccv[COMPANIES::TAX_IDENTIFICATION_NUMBER] == $company_tin)
                ) {
                    $response['message'] = "Company already exists ! Company name or TIN cannot be the same.";
                    sendRes();
                }
                if ($company_code != "" && ($company_code == $ccv[COMPANIES::COMPANY_CODE])) {
                    $response['message'] = "Company already exists ! Company code cannot be the same.";
                    sendRes();
                }
                if ($company_case_code != "" && ($company_case_code == $ccv[COMPANIES::CASE_CODE])) {
                    $response['message'] = "Company already exists ! Company case code cannot be the same.";
                    sendRes();
                }
            }
        }
        $cols = [
            COMPANIES::COMPANY_NAME => $company_name,
            COMPANIES::TAX_IDENTIFICATION_NUMBER => $company_tin,
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANIES::STATUS => ACTIVE_STATUS,
            COMPANIES::ACTIVE => 1,
            COMPANIES::CREATED_AT => getToday(),
            COMPANIES::UPDATED_AT => getToday()
        ];
        if (($company_industry != "") || ($company_industry != 0)) {
            $cols[COMPANIES::INDUSTRY_TYPE_ID] = $company_industry;
        }
        if (($company_code != "") || ($company_code != null)) {
            $cols[COMPANIES::COMPANY_CODE] = $company_code;
        }
        if (($company_case_code != "") || ($company_case_code != null)) {
            $cols[COMPANIES::CASE_CODE] = $company_case_code;
        }
        $save_company = setData(Table::COMPANIES, $cols);
        if (!$save_company['res']) {
            logError("Unabled to save company: " . $company_name, $save_company['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $company_id = $save_company['id'];
        $resp = "Company Added";
        if (
            (
                ($company_audit_type != 0)
            ) &&
            (
                ($company_tax_type != 0)
            )
        ) {
            $audit_tax_type_col = [
                AUDIT_TAX_TYPE_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
                AUDIT_TAX_TYPE_HISTORY::COMPANY_ID => $company_id,
                AUDIT_TAX_TYPE_HISTORY::ACTIVE => 1,
                AUDIT_TAX_TYPE_HISTORY::STATUS => ACTIVE_STATUS,
                AUDIT_TAX_TYPE_HISTORY::START_DATE => getToday(),
                AUDIT_TAX_TYPE_HISTORY::CREATED_AT => getToday(),
                AUDIT_TAX_TYPE_HISTORY::UPDATED_AT => getToday()
            ];
            if (
                ($company_audit_type != null)  ||
                ($company_audit_type != 0)
            ) {
                $audit_tax_type_col[AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID] = $company_audit_type;
            }
            if (
                ($company_tax_type != null)  ||
                ($company_tax_type != 0)
            ) {
                $tt = "";
                if (gettype($company_tax_type) == 'array') {
                    $tt = implode(',', $company_tax_type);
                } else {
                    $tt = $company_tax_type;
                }
                $audit_tax_type_col[AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID] = $tt;
            }
            $audit_tax_type_save = setData(Table::AUDIT_TAX_TYPE_HISTORY, $audit_tax_type_col);
            if (!$audit_tax_type_save['res']) {
                logError("Unabled to save audit tax type history for the company: " . $company_id, $audit_tax_type_save['error']);
                $resp .= " Without Tax Type Data";
            } else {
                $resp .= " With Tax Type Data";
            }
        }
        // $response['message'] = $resp;
        $response['message'] = 'Company Details Successfully Added As Per Request';
        $response['error'] = false;
        sendRes();
        break;
    case 'UPDATE_COMPANY':
        $cid                = (isset($ajax_form_data['cid']))    ? $ajax_form_data['cid']                   : 0;
        $company_name       = (isset($ajax_form_data['cname']))  ? altRealEscape($ajax_form_data['cname'])  : "";
        $company_tin        = (isset($ajax_form_data['ctin']))   ? altRealEscape($ajax_form_data['ctin'])   : "";
        $company_industry   = (isset($ajax_form_data['citype'])) ? altRealEscape($ajax_form_data['citype']) : "";
        $company_code       = (isset($ajax_form_data['ccode']))  ? altRealEscape($ajax_form_data['ccode'])  : "";
        $company_case_code  = (isset($ajax_form_data['ccase']))  ? altRealEscape($ajax_form_data['ccase'])  : "";
        $company_audit_type = (isset($ajax_form_data['atype']))  ? altRealEscape($ajax_form_data['atype'])  : "";
        $company_tax_type   = (isset($ajax_form_data['ttype']))  ? ($ajax_form_data['ttype'])  : "";

        // echo "Get Type: ".gettype($company_audit_type);
        // echo "<br />";
        // echo implode(',', $company_tax_type);
        // echo rip($ajax_form_data);
        // echo "Data: ".$company_tax_type;
        // exit();

        if (($company_name == "") || ($company_tin == "") || ($cid==0)) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $getCom = getData(Table::COMPANIES, [
            COMPANIES::ID,
            COMPANIES::COMPANY_NAME,
            COMPANIES::CASE_CODE,
            COMPANIES::COMPANY_CODE,
            COMPANIES::TAX_IDENTIFICATION_NUMBER
        ], [
            COMPANIES::STATUS => ACTIVE_STATUS,
            COMPANIES::ACTIVE => 1,
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        // if (count($getCom) > 0) {
        //     foreach ($getCom as $cck => $ccv) {
        //         if (
        //             ($ccv[COMPANIES::ID] != $cid) &&
        //             (($ccv[COMPANIES::COMPANY_NAME] == $company_name) ||
        //             ($ccv[COMPANIES::TAX_IDENTIFICATION_NUMBER] == $company_tin))
        //         ) {
        //             $response['message'] = "Company Already Exists !";
        //             sendRes();
        //         }
        //     }
        // }
        if (count($getCom) > 0) {
            foreach ($getCom as $cck => $ccv) {
                if (
                    ($ccv[COMPANIES::ID] != $cid) &&
                    (($ccv[COMPANIES::COMPANY_NAME] == $company_name) ||
                    ($ccv[COMPANIES::TAX_IDENTIFICATION_NUMBER] == $company_tin))
                ) {
                    $response['message'] = "Company already exists ! Company name or TIN cannot be the same.";
                    sendRes();
                }
                if (
                    ($ccv[COMPANIES::ID] != $cid) &&
                    ($company_code != "" && ($company_code == $ccv[COMPANIES::COMPANY_CODE]))
                ) {
                    $response['message'] = "Company already exists ! Company code cannot be the same.";
                    sendRes();
                }
                if (
                    ($ccv[COMPANIES::ID] != $cid) &&
                    ($company_case_code != "" && ($company_case_code == $ccv[COMPANIES::CASE_CODE]))
                ) {
                    $response['message'] = "Company already exists ! Company case code cannot be the same.";
                    sendRes();
                }
            }
        }
        $cols = [
            COMPANIES::COMPANY_NAME => $company_name,
            COMPANIES::TAX_IDENTIFICATION_NUMBER => $company_tin,
            COMPANIES::UPDATED_AT => getToday(),
            COMPANIES::INDUSTRY_TYPE_ID => $company_industry,
            COMPANIES::COMPANY_CODE => $company_code,
            COMPANIES::CASE_CODE => $company_case_code,
        ];
        // if (($company_industry != "") || ($company_industry != 0)) {
        //     $cols[COMPANIES::INDUSTRY_TYPE_ID] = $company_industry;
        // }
        // if (($company_code != "") || ($company_code != null)) {
        //     $cols[COMPANIES::COMPANY_CODE] = $company_code;
        // }
        // if (($company_case_code != "") || ($company_case_code != null)) {
        //     $cols[COMPANIES::CASE_CODE] = $company_case_code;
        // }
        $updateCom = updateData(Table::COMPANIES, $cols,[
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANIES::ID => $cid
        ]);
        if (!$updateCom['res']) {
            logError("Unabled to update company: " . $company_name, $updateCom['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $company_id = $cid;
        $resp = "Company Updated";
        if (
            (
                ($company_audit_type != 0)
            ) &&
            (
                ($company_tax_type != 0)
            )
        ) {
            $audit_tax_type_col = [
                AUDIT_TAX_TYPE_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
                AUDIT_TAX_TYPE_HISTORY::COMPANY_ID => $company_id,
                AUDIT_TAX_TYPE_HISTORY::ACTIVE => 1,
                AUDIT_TAX_TYPE_HISTORY::STATUS => ACTIVE_STATUS,
                AUDIT_TAX_TYPE_HISTORY::START_DATE => getToday(),
                AUDIT_TAX_TYPE_HISTORY::CREATED_AT => getToday(),
                AUDIT_TAX_TYPE_HISTORY::UPDATED_AT => getToday()
            ];
            if (
                ($company_audit_type != null)  ||
                ($company_audit_type != 0)
            ) {
                $audit_tax_type_col[AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID] = $company_audit_type;
            }
            if (
                ($company_tax_type != null)  ||
                ($company_tax_type != 0)
            ) {
                $tt = "";
                if (gettype($company_tax_type) == 'array') {
                    $tt = implode(',', $company_tax_type);
                } else {
                    $tt = $company_tax_type;
                }
                $audit_tax_type_col[AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID] = $tt;
            }
            $checkPreData=getData(Table::AUDIT_TAX_TYPE_HISTORY,[
                AUDIT_TAX_TYPE_HISTORY::ACTIVE
            ],[
                AUDIT_TAX_TYPE_HISTORY::COMPANY_ID=>$cid,
                AUDIT_TAX_TYPE_HISTORY::ACTIVE=>1,
                AUDIT_TAX_TYPE_HISTORY::STATUS=>ACTIVE_STATUS,
                AUDIT_TAX_TYPE_HISTORY::CLIENT_ID=>$_SESSION[CLIENT_ID]
            ]);
            if (count($checkPreData)>0) {
                $inactivePreviousData=updateData(Table::AUDIT_TAX_TYPE_HISTORY,[
                    AUDIT_TAX_TYPE_HISTORY::END_DATE=>getToday(false),
                    AUDIT_TAX_TYPE_HISTORY::UPDATED_AT=>getToday(),
                    AUDIT_TAX_TYPE_HISTORY::ACTIVE=>0
                ],[
                    AUDIT_TAX_TYPE_HISTORY::COMPANY_ID=>$cid,
                    AUDIT_TAX_TYPE_HISTORY::ACTIVE=>1,
                    AUDIT_TAX_TYPE_HISTORY::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ]);
                if (!$inactivePreviousData['res']) {
                    logError('Unabled deleting previous audit & tax type history before inserting new data on company updation, Company id: '.$cid,$inactivePreviousData['error']);
                }
            }
            $audit_tax_type_save = setData(Table::AUDIT_TAX_TYPE_HISTORY, $audit_tax_type_col);
            if (!$audit_tax_type_save['res']) {
                logError("Unabled to save audit tax type history for the company: " . $company_id." on company updation", $audit_tax_type_save['error']);
                $resp .= " Without Tax Type Data";
            } else {
                $resp .= " With Tax Type Data";
            }
        }
        // $response['message'] = $resp;
        $response['message'] = "Company Details Successfully Updated As Per Request";
        $response['error'] = false;
        sendRes();
        break;
    case 'EDIT_COMPANY':
        $cid = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $html=$ind_selected=$aud_type_selected=$tax_type_selected='';
        $taxTypeIds=[];
        $isTaxTypeMultiple=false;
        $com_ind_opt = '<option value="0" disabled>---- Select Industry ----</option>';
        $auditTypeOptions = '<option value="0" disabled>--- Select Audit Type ---</option>';
        $taxTypeOptions = '<option value="0" disabled>--- Select Tax Type ---</option>';
        if ($cid == 0) {
            $response['message'] = "Company Not Found!";
            sendRes();
        }
        $getCompanyData = getData(Table::COMPANIES, ['*'], [
            COMPANIES::ID => $cid,
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANIES::STATUS => ACTIVE_STATUS
        ]);
        $getIndustry = getData(Table::COMPANY_INDUSTRY_TYPE, [
            COMPANY_INDUSTRY_TYPE::ID,
            COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
          ], [
            COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS
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
          $getAuditTaxTypeHistory=getData(Table::AUDIT_TAX_TYPE_HISTORY,[
            AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID,
            AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID
          ],[
            AUDIT_TAX_TYPE_HISTORY::COMPANY_ID=>$cid,
            AUDIT_TAX_TYPE_HISTORY::ACTIVE=>1,
            AUDIT_TAX_TYPE_HISTORY::STATUS=>ACTIVE_STATUS
          ]);
        $cdata = $getCompanyData[0];
        if (count($getIndustry) > 0) {
            foreach ($getIndustry as $cik => $civ) {
                if ($civ[COMPANY_INDUSTRY_TYPE::ID]==$cdata[COMPANIES::INDUSTRY_TYPE_ID]) {
                    $ind_selected='selected';
                } else {
                    $ind_selected='';
                }
                $com_ind_opt .=
                '<option '.$ind_selected.' value="' . $civ[COMPANY_INDUSTRY_TYPE::ID] . '">' . altRealEscape($civ[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE]) . '</option>';
            }
        }
        if (count($getAuditTypes) > 0) {
            foreach ($getAuditTypes as $atk => $atv) {
                if (count($getAuditTaxTypeHistory)>0) {
                    if ($getAuditTaxTypeHistory[0][AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID]==$atv[AUDIT_TYPES::ID]) {
                        $aud_type_selected='selected';
                        if ($atv[AUDIT_TYPES::AUDIT_TYPE]=='Comprehensive audit') {
                            $isTaxTypeMultiple=true;
                        }
                    } else {
                        $aud_type_selected='';
                    }
                }
                $auditTypeOptions .= '<option '.$aud_type_selected.' value="' . $atv[AUDIT_TYPES::ID] . '">' . $atv[AUDIT_TYPES::AUDIT_TYPE] . '</option>';
            }
        }
        if (count($getTaxTypes) > 0) {
            foreach ($getTaxTypes as $ttk => $ttv) {
                if (count($getAuditTaxTypeHistory)>0) {
                    if ($getAuditTaxTypeHistory[0][AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID]!="") {
                        $taxTypeIds=explode(',',$getAuditTaxTypeHistory[0][AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID]);
                        // rip($taxTypeIds);
                        if (in_array($ttv[TYPE_OF_TAX::ID],$taxTypeIds)) {
                            $aud_type_selected='selected';
                        } else {
                            $aud_type_selected='';
                        }
                        if (count($taxTypeIds)>1) {
                            $isTaxTypeMultiple=true;
                        }
                    }
                }
                $taxTypeOptions .= '<option '.$aud_type_selected.' value="' . $ttv[TYPE_OF_TAX::ID] . '">' . $ttv[TYPE_OF_TAX::TYPE_OF_TAX] . '</option>';
            }
        }
        $html = '
        <div class="col-md-12 company_add_section">
          <fieldset class="fldset mt-3">
            <legend>Company Details</legend>
            <div class="row">
              <div class="col-md-4 col-lg-4 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="com_name">Company Name</label>'.getAsterics().'
                  <input type="text" id="com_name" class="form-control" value="' . $cdata[COMPANIES::COMPANY_NAME] . '" />
                </div>
              </div>
              <div class="col-md-4 col-lg-4 col-sm-12">
                <div class="form-outline">
                <label class="form_label" for="com_industry_type_select">Industry Type</label>
                    <select id="com_industry_type_select" class="form-control">
                        '.$com_ind_opt.'
                    </select>
                </div>
              </div>
              <div class="col-md-4 col-lg-4 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="com_tin_number">TIN Number</label>'.getAsterics().'
                  <input type="text" id="com_tin_number" class="form-control" value="' . $cdata[COMPANIES::TAX_IDENTIFICATION_NUMBER] . '" />
                </div>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="company_code">Company Code</label>
                  <input type="text" id="company_code" class="form-control" value="' . $cdata[COMPANIES::COMPANY_CODE] . '" />
                </div>
              </div>
              <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="com_case_code">Case Code</label>
                  <input type="text" id="com_case_code" class="form-control" value="' . $cdata[COMPANIES::CASE_CODE] . '" />
                </div>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="audit_type_id">Audit Type</label>'.getAsterics().'
                  <select id="audit_type_id" class="form-control" onchange="setTaxtype();">
                    '.$auditTypeOptions.'
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="type_of_tax_id">Tax Type</label>'.getAsterics().'
                  <div class="tax_type_select_div">
                    <select id="type_of_tax_id" class="form-control '.(($isTaxTypeMultiple)?'multiple':'').'" '.(($isTaxTypeMultiple)?'name="type_of_tax_id[]" multiple=""':'').'>
                      '.$taxTypeOptions.'
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <input type="hidden" id="company_update_id" value="'.$cdata[COMPANIES::ID].'" style="visibility: hidden; display:none;" />
            <div class="row mt-2">
              <div class="col-md-12 col-lg-12 col-sm-12 text-right pt-4 action_btn">
                <button class="btn btn-sm btn-success" type="button" id="update_company_btn" onclick="updateSubmitCompany('.$cdata[COMPANIES::ID].');"><i class="fas fa-history"></i>&nbsp;Update</button>
                <button class="btn btn-sm btn-secondary" type="button" id="cancel_company_btn" onclick="location.reload();"><i class="fas fa-window-close"></i>&nbsp;Cancel</button>
              </div>
            </div>
          </fieldset>
        </div>
        ';
        $response['html']=$html;
        sendRes();
        break;
    case 'GET_TAX_TYPE_OPTIONS':
        $taxTypeOptions = '';
        $getTaxTypes = getData(Table::TYPE_OF_TAX, [
            TYPE_OF_TAX::TYPE_OF_TAX,
            TYPE_OF_TAX::ID
        ], [
            TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
            TYPE_OF_TAX::STATUS => ACTIVE_STATUS
        ]);
        if (count($getTaxTypes) > 0) {
            foreach ($getTaxTypes as $ttk => $ttv) {
                $taxTypeOptions .= '<option value="' . $ttv[TYPE_OF_TAX::ID] . '">' . $ttv[TYPE_OF_TAX::TYPE_OF_TAX] . '</option>';
            }
        }
        $response['error'] = false;
        $response['taxTypeOptions'] = $taxTypeOptions;
        sendRes();
        break;
    case 'GET_ASSIGNED_COMPANIES':
        $auditor_id = (isset($ajax_form_data['aud'])) ? $ajax_form_data['aud'] : 0;
        $company_id = (isset($ajax_form_data['aud'])) ? $ajax_form_data['aud'] : 0;

        $assigned = "";
        if (($auditor_id == 0) || ($auditor_id == "")) {
            $response['message'] = "Company not found !";
            sendRes();
        }
        $getComs = getData(Table::COMPANY_ASSIGNED_DATA, [
            COMPANY_ASSIGNED_DATA::ID,
            COMPANY_ASSIGNED_DATA::COMPANY_IDS,
            COMPANY_ASSIGNED_DATA::AUDITOR_ID,
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
        ], [
            COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
            COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id
        ]);
        if (count($getComs) > 0) {
            // $data = $getComs[0];
            $primary_auditor_id = 0;
            $secondary_auditor_id = [];
            $primary_auditor = $secondary_auditor = "";
            foreach ($getComs as $cdk => $data) {
                // $assigned_cids = explode(',',$data[COMPANY_ASSIGNED_DATA::COMPANY_IDS]);
                // $getAssignedNames = getData(Table::COMPANIES, [
                //     COMPANIES::COMPANY_NAME,
                //     COMPANIES::TAX_IDENTIFICATION_NUMBER,
                //     COMPANIES::UPDATED_AT
                // ], [
                //     COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
                //     COMPANIES::STATUS => ACTIVE_STATUS
                // ]);
                if ($data[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 1) {
                    $primary_auditor_id = $data[COMPANY_ASSIGNED_DATA::AUDITOR_ID];
                }
                if ($data[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 2) {
                    $secondary_auditor_id[] = $data[COMPANY_ASSIGNED_DATA::AUDITOR_ID];
                }
            }
            // rip($secondary_auditor_id);
            // exit;
            $getAuditor = getData(Table::USERS, [
                Users::NAME,
                Users::ID
            ], [
                Users::USER_TYPE => EMPLOYEE,
                Users::CLIENT_ID => $_SESSION[CLIENT_ID],
                Users::ACTIVE => 1,
                Users::STATUS => ACTIVE_STATUS
            ]);
            $primary_auditorOption = '<option value="0" disabled>--- Select Primary Auditor ---</option>';
            $primarySelectReadOnly = ($primary_auditor_id != 0) ? "readonly disabled" : "";
            $primaryAudFound=$secAudFound=true;
            if (count($getAuditor) > 0) {
                foreach ($getAuditor as $ak => $av) {
                    $selected = $audName='';
                    if (($primary_auditor_id != 0) && ($primary_auditor_id == $av[Users::ID])) {
                        $selected = 'selected';
                        $primaryAudFound=true;
                        $audName=$av[Users::NAME];
                    } else {
                        // $primarySelectReadOnly='';
                        $selected = '';
                        $audName='Auditor Not Found / Inactive';
                        $primaryAudFound=false;
                    }
                    $primary_auditorOption .= '<option value="' . $av[Users::ID] . '" ' . $selected . '>' . $audName . '</option>';
                }
                // if(!$primaryAudFound){
                //     $primary_auditorOption = '<option value="0" disabled selected>--- No Primary Auditor Found---</option>';   
                // }
            }
            $assigned .= "
            <div class='row'>
                <div class='col-md-6'>
                <fieldset class='fldset'>
                    <legend>Primary Auditor</legend>
                    <div class='form-outline'>
                        <label class='form_label' for='primary_auditor_select'>Select Primary Auditor</label>
                        <select id='primary_auditor_select' class='form-control' " . $primarySelectReadOnly . ">
                            " . $primary_auditorOption . "
                        </select>
                    </div>
                </fieldset>
                </div>
                <div class='col-md-6'>
                <fieldset class='fldset'>
                    <legend>Secondary Auditors</legend>
                    <p class='mt-2 mb-2 font-weight-bold'>Secondary Auditors list</p>
            ";
            if (count($secondary_auditor_id) > 0) {
                foreach ($secondary_auditor_id as $sak => $sav) {
                    $getAssignedNames = getData(Table::USERS, [
                        Users::NAME
                    ], [
                        Users::CLIENT_ID => $_SESSION[CLIENT_ID],
                        Users::STATUS => ACTIVE_STATUS,
                        Users::ACTIVE => 1,
                        Users::ID => $sav
                    ]);

                    $sa_name = (count($getAssignedNames) > 0) ? $getAssignedNames[0][Users::NAME] : '';
                    $assigned .= "<p class='mt-2 mb-2'>";
                    $assigned .= "<span>" . $sa_name;
                    $assigned .= "</span>";
                    $assigned .= "</p>";
                }
            } else {
                $assigned .= "
                <p style='margin-top: 20px !important;'>
                    <small class='text-danger'>No Secondary Auditors Found !</small>
                </p>
                ";
            }
            $assigned .= "
                </fieldset>
                </div>
                <div class='col-md-12 text-right'>
                    <p style='margin-top: 10px !important;'>
                        <a class='text-light badge badge-info p-2' href='#' onclick='updateAssignedCompanies();'>&nbsp;<i class='far fa-edit'></i>&nbsp;Edit or <i class='fas fa-plus'></i>&nbsp;Add more Auditors ?</a>
                    </p>
                </div>
            </div>";
        } else {
            $assigned = '
            </br>
            <p class="text-center">No Auditors are assigned yet ! &nbsp;<small><a class="text-primary font-weight-bold" style="text-decoration:underline;" href="#" onclick="assignCompanySection();"><i class="fas fa-plus"></i>&nbsp;Assign?</a></small></p>';
        }
        $response['error'] = false;
        $response['assigned'] = $assigned;
        sendRes();
        break;
    case 'GET_COMPANY_LIST_TO_ASSIGN':
        $auditor_id = $ajax_form_data['aud'];
        $company_id = $ajax_form_data['aud'];
        $primary_aud_id = 0;
        $sec_aud_id = [];
        $getAuditor = getData(Table::USERS, [
            Users::NAME,
            Users::ID
        ], [
            Users::USER_TYPE => EMPLOYEE,
            Users::CLIENT_ID => $_SESSION[CLIENT_ID],
            Users::ACTIVE => 1,
            Users::STATUS => ACTIVE_STATUS
        ]);
        $getAssignedData = getData(Table::COMPANY_ASSIGNED_DATA, [
            COMPANY_ASSIGNED_DATA::AUDITOR_ID,
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
        ], [
            COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
            COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS
        ]);
        $primary_auditorOption = '<option value="0" disabled>--- Select Primary Auditor ---</option>';
        $sec_auditorOption = '<option value="0" disabled>--- Select Secondary Auditor ---</option>';
        $primary_selected = $sec_selected = $primarySelectDisabled = '';
        if (count($getAssignedData) > 0) {
            foreach ($getAssignedData as $asdk => $asdv) {
                if ($asdv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 1) {
                    $primary_aud_id = $asdv[COMPANY_ASSIGNED_DATA::AUDITOR_ID];
                    // $primary_selected='selected';
                }
            }
        }
        if (count($getAuditor) > 0) {
            foreach ($getAuditor as $ak => $av) {
                if ($primary_aud_id == $av[Users::ID]) {
                    $primary_selected = 'selected';
                    $checkActivity = getData(Table::AUDITS_DATA, [
                        AUDITS_DATA::ACTIVE
                    ], [
                        AUDITS_DATA::COMPANY_ID => $company_id,
                        AUDITS_DATA::USER_ID => $primary_aud_id,
                        AUDITS_DATA::ACTIVE => 1,
                        AUDITS_DATA::STATUS => ACTIVE_STATUS,
                        AUDITS_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                    ]);
                    if (count($checkActivity) > 0) {
                        $primarySelectDisabled = 'disabled';
                    } else {
                        $primarySelectDisabled = '';
                    }
                } else {
                    $primary_selected = '';
                }
                $primary_auditorOption .= '<option ' . $primary_selected . ' value="' . $av[Users::ID] . '">' . $av[Users::NAME] . '</option>';
                $checkSecAud = getData(Table::COMPANY_ASSIGNED_DATA, [
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID
                ], [
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
                    COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                    COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY => 2,
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID => $av[Users::ID]

                ]);
                if ($primary_aud_id != $av[Users::ID]) {
                    $sec_selected = '';
                    if (count($checkSecAud) > 0) {
                        $sec_selected = 'selected';
                    }
                    $sec_auditorOption .= '<option ' . $sec_selected . ' value="' . $av[Users::ID] . '">' . $av[Users::NAME] . '</option>';
                }
            }
        }
        $list = '
        <div class="row">
            <div class="col-md-6">
                <fieldset class="fldset">
                    <legend>Primary Auditor</legend>
                    <div class="form-outline">
                        <label class="form_label" for="primary_auditor_select">Select Primary Auditor</label>
                        <select id="primary_auditor_select" class="form-control" ' . $primarySelectDisabled . ' onchange="getCurrentAuditorStat();">
                            ' . $primary_auditorOption . '
                        </select>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset class="fldset">
                    <legend>Secondary Auditors</legend>
                    <div class="form-outline">
                        <label class="form_label" for="secondary_auditor_select">Select Secondary Auditor</label>
                        <select id="secondary_auditor_select" class="form-control multiple" name="secondary_auditor_select[]" multiple="multiple" onchange="getCurrentSecAuditorStat();">
                            ' . $sec_auditorOption . '
                        </select>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-right">
                <button class="btn btn-sm btn-success" id="assign_auditors_btn" type="button" onclick="assignAuditors();"><i class="fas fa-sync"></i>&nbsp;Assign</button>
            </div>
        </div>';

        // $getComs = getData(Table::COMPANIES, [
        //     COMPANIES::ID,
        //     COMPANIES::COMPANY_NAME,
        //     COMPANIES::TAX_IDENTIFICATION_NUMBER
        // ], [
        //     COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
        //     COMPANIES::STATUS => ACTIVE_STATUS,
        // ]);
        // $getAssignedCompanies = getData(Table::COMPANY_ASSIGNED_DATA, [
        //     COMPANY_ASSIGNED_DATA::COMPANY_IDS
        // ], [
        //     COMPANY_ASSIGNED_DATA::AUDITOR_ID => $auditor_id,
        //     COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
        //     COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS
        // ]);
        // $alreadyAssComIds = [];
        // if (count($getAssignedCompanies)>0) {
        //     if ($getAssignedCompanies[0][COMPANY_ASSIGNED_DATA::COMPANY_IDS] != "") {
        //         $alreadyAssComIds = explode(',', $getAssignedCompanies[0][COMPANY_ASSIGNED_DATA::COMPANY_IDS]);
        //     }
        // }
        // if (count($getComs)>0) {
        //     foreach ($getComs as $key => $value) {
        //         if (count($alreadyAssComIds)>0) {
        //             if (!in_array($value[COMPANIES::ID], $alreadyAssComIds)) {
        //                 $list .= '
        //                     <option value="'.$value[COMPANIES::ID].'">'.$value[COMPANIES::COMPANY_NAME];
        //                 $list .= ($value[COMPANIES::TAX_IDENTIFICATION_NUMBER] != null) ? ' (TIN: '. $value[COMPANIES::TAX_IDENTIFICATION_NUMBER] .')' : '';
        //                 $list .= '</option>';
        //             }
        //         } else {
        //             $list .= '
        //                 <option value="'.$value[COMPANIES::ID].'">'.$value[COMPANIES::COMPANY_NAME];
        //             $list .= ($value[COMPANIES::TAX_IDENTIFICATION_NUMBER] != null) ? ' (TIN: '. $value[COMPANIES::TAX_IDENTIFICATION_NUMBER] .')' : '';
        //             $list .= '</option>';
        //         }
        //     }
        // }
        // $list .= '</select>';
        $response['error'] = false;
        $response['list'] = $list;
        $response['primary_aud_id'] = $primary_aud_id;
        $response['primary_selected'] = $primary_selected;
        sendRes();
        break;
    case 'ASSIGN_COMPANIES':
        $auditor_id = (isset($ajax_form_data['aud'])) ? $ajax_form_data['aud'] : 0;
        $company_id = (isset($ajax_form_data['coms'])) ? $ajax_form_data['coms'] : 0;
        $update = $ajax_form_data['update'];

        if (
            ($auditor_id == "") ||
            ($auditor_id == 0) ||
            (count($company_id) == 0)
        ) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        // getting already assigned companies start
        $getAssignedCompanies = getData(Table::COMPANY_ASSIGNED_DATA, [
            COMPANY_ASSIGNED_DATA::COMPANY_IDS
        ], [
            COMPANY_ASSIGNED_DATA::AUDITOR_ID => $auditor_id,
            COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS
        ]);
        $alreadyAssComIds = [];
        $all_cids = $company_id;
        if (count($getAssignedCompanies) > 0) {
            if ($getAssignedCompanies[0][COMPANY_ASSIGNED_DATA::COMPANY_IDS] != "") {
                $alreadyAssComIds = explode(',', $getAssignedCompanies[0][COMPANY_ASSIGNED_DATA::COMPANY_IDS]);
                $all_cids = array_merge($alreadyAssComIds, $company_id);
            }
        }
        // getting already assigned companies end
        $cids = implode(',', $all_cids);
        switch ($update) {
            case 0:
                $cols = [
                    COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                    COMPANY_ASSIGNED_DATA::USER_ID => $_SESSION[RID],
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID => $auditor_id,
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS => $cids,
                    COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                    COMPANY_ASSIGNED_DATA::CREATED_AT => getToday(),
                    COMPANY_ASSIGNED_DATA::UPDATED_AT => getToday()
                ];
                $save = setData(Table::COMPANY_ASSIGNED_DATA, $cols);
                if (!$save['res']) {
                    logError("Unabled to assign companies for the auditor id: " . $auditor_id . ", company ids: " . $cids, $save['error']);
                    $response['message'] = ERROR_3;
                    sendRes();
                }
                $response['error'] = false;
                $response['message'] = "Assigned Successfully !";
                sendRes();
                break;
            case 1:
                $update_table = updateData(Table::COMPANY_ASSIGNED_DATA, [
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS => $cids,
                    COMPANY_ASSIGNED_DATA::UPDATED_AT => getToday()
                ], [
                    COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID => $auditor_id,
                    COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                ]);
                if (!$update_table['res']) {
                    logError("Unabled to update the assignment of companies for the auditor: " . $auditor_id . ", company ids: " . $cids, $update_table['error']);
                    $response['message'] = ERROR_3;
                    sendRes();
                }
                $response['error'] = false;
                $response['message'] = "Updated Successfully !";
                sendRes();
                break;
            default:
                $cols = [
                    COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                    COMPANY_ASSIGNED_DATA::USER_ID => $_SESSION[RID],
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID => $auditor_id,
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS => $cids,
                    COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                    COMPANY_ASSIGNED_DATA::CREATED_AT => getToday(),
                    COMPANY_ASSIGNED_DATA::UPDATED_AT => getToday()
                ];
                $save = setData(Table::COMPANY_ASSIGNED_DATA, $cols);
                if (!$save['res']) {
                    logError("Unabled to assign companies for the auditor id: " . $auditor_id . ", company ids: " . $cids, $save['error']);
                    $response['message'] = ERROR_3;
                    sendRes();
                }
                $response['error'] = false;
                $response['message'] = "Assigned Successfully !";
                sendRes();
                break;
        }
        break;
    case 'GET_REPLY_MODAL':
        $query_id = $ajax_form_data['qid'];
        $no_of_query = $no_of_query_solved = 0;
        $getQueryDetails = getData(Table::QUERY_DATA, [
            QUERY_DATA::TOTAL_NO_OF_QUERY,
            QUERY_DATA::NO_OF_QUERY_SOLVED
        ], [
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_DATA::QUERY_STATUS => 1,
            QUERY_DATA::ID => $query_id
        ]);
        if (count($getQueryDetails) > 0) {
            $no_of_query = $getQueryDetails[0][QUERY_DATA::TOTAL_NO_OF_QUERY];
            $no_of_query_solved = $getQueryDetails[0][QUERY_DATA::NO_OF_QUERY_SOLVED];
        }
        if (
            (($no_of_query_solved != "") &&
                ($no_of_query > $no_of_query_solved)) ||
            ($no_of_query_solved == "")
        ) {
            $qnoToRep=($no_of_query_solved == "") ? $no_of_query : ($no_of_query-$no_of_query_solved);
            $htm = '
            <div class="row">
                <div class="col-md-12 col-lg-12 col-sm-12">
                    <fieldset class="fldset mt-3">
                        <legend>Query Reply</legend>
                        <strong class="text-primary">Total no. of Query: </strong>' . $no_of_query.'
                        <div class="row mt-2">
                            <div class="col-md-6 col-lg-6 col-sm-6">
                                <label for="no_of_query_solved">No. of Query attached</label>
                                <input type="text" class="form-control" id="no_of_query_solved" value="' . $qnoToRep . '" />
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-6">
                                <label for="query_date_of_reply">Reply Date</label>
                                <input type="date" class="form-control" id="query_date_of_reply" value="' . getToday(false) . '" readonly disabled />
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12 col-lg-12 col-sm-12 text-right">
                                <button type="button" class="btn btn-sm btn-success" id="query_add_reply_btn" onclick="SaveReply(' . $query_id . ');"><i class="fas fa-save"></i>&nbsp;Add Reply</button>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            ';
        } else {
            $htm = '
            <div class="row">
                <div class="col-md-12 col-lg-12 col-sm-12">
                    <div class="alert alert-danger" role="alert">
                        No Query found for reply!
                    </div>
                </div>
            </div>
            ';
        }
        $response['htm'] = $htm;
        $response['error'] = false;
        sendRes();
        break;
    case 'SAVE_QUERY_REPLY':
        $query_id = $ajax_form_data['qid'];
        $no_of_query_solved = altRealEscape($ajax_form_data['noqs']);
        $query_date_of_reply = altRealEscape($ajax_form_data['dor']);
        $totalno_of_query = $totalno_of_query_solved = 0;

        $getQueryDetails = getData(Table::QUERY_DATA, [
            QUERY_DATA::TOTAL_NO_OF_QUERY,
            QUERY_DATA::NO_OF_QUERY_SOLVED
        ], [
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_DATA::QUERY_STATUS => 1,
            QUERY_DATA::ID => $query_id
        ]);
        if (count($getQueryDetails) > 0) {
            $totalno_of_query = $getQueryDetails[0][QUERY_DATA::TOTAL_NO_OF_QUERY];
            $totalno_of_query_solved = $getQueryDetails[0][QUERY_DATA::NO_OF_QUERY_SOLVED];
        }
        if (
            ($no_of_query_solved > $totalno_of_query) ||
            (($totalno_of_query - $totalno_of_query_solved) < $no_of_query_solved)
        ) {
            $response['message'] = "Please insert an appropriate No. of Query attached";
            sendRes();
        }
        $reply_cols = [
            QUERY_REPLY::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_REPLY::QUERY_ID => $query_id,
            QUERY_REPLY::DATE_OF_REPLY => $query_date_of_reply,
            QUERY_REPLY::NO_OF_QUERY_SOLVED => $no_of_query_solved,
            QUERY_REPLY::STATUS => ACTIVE_STATUS,
            QUERY_REPLY::CREATED_AT => getToday()
        ];
        $save_reply = setData(Table::QUERY_REPLY, $reply_cols);
        if (!$save_reply['res']) {
            logError("Unabled to save query reply table data", $save_reply['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $solved_query = (($totalno_of_query_solved != "") && ($totalno_of_query_solved != 0)) ? ($totalno_of_query_solved + $no_of_query_solved) : $no_of_query_solved;
        $unsolved_query = (($totalno_of_query_solved != "") && ($totalno_of_query_solved != 0)) ? ($totalno_of_query - ($totalno_of_query_solved + $no_of_query_solved)) : ($totalno_of_query - $no_of_query_solved);

        $update_query_table = updateData(Table::QUERY_DATA, [
            QUERY_DATA::NO_OF_QUERY_SOLVED => $solved_query,
            QUERY_DATA::NO_OF_QUERY_UNSOLVED => $unsolved_query,
            QUERY_DATA::DATE_OF_REPLY => $query_date_of_reply,
            QUERY_DATA::QUERY_STATUS => ($unsolved_query == 0) ? 2 : 1,
            QUERY_DATA::QUERY_REPLY_IS_SUBMITTED => ($totalno_of_query == $solved_query) ? 1 : 4,
            QUERY_DATA::UPDATED_AT => getToday()
        ], [
            QUERY_DATA::ID => $query_id,
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$update_query_table['res']) {
            logError("unabled to update query table on adding reply", $update_query_table['error']);
            $response['message'] = ERROR_2;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Reply added successfully !";
        sendRes();
        break;
    case 'ASSIGN_AUDITORS':
        $company_id = $ajax_form_data['cid'];
        $primary_auditor = $ajax_form_data['pid'];
        $secondary_auditor = isset($ajax_form_data['sid']) ? $ajax_form_data['sid'] : [];
        $update = isset($ajax_form_data['is_update']) ? $ajax_form_data['is_update'] : 0;

        if (
            ($primary_auditor == "") || ($primary_auditor == 0) ||
            ($company_id == "") || ($company_id == 0)
        ) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        if (count($secondary_auditor) > 0) {
            if (in_array($primary_auditor, $secondary_auditor)) {
                $response['message'] = "Primary & Secondary Auditor cannot be same";
                sendRes();
            }
        }
        if ($update == 1) {
            $updatePrimaryAud = updateData(Table::COMPANY_ASSIGNED_DATA, [
                COMPANY_ASSIGNED_DATA::AUDITOR_ID => $primary_auditor,
                COMPANY_ASSIGNED_DATA::UPDATED_AT => getToday()
            ], [
                COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY => 1,
                COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
                COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
            ]);
            if (!$updatePrimaryAud['res']) {
                logError('unabled to update primary auditor in company assigned data, company id: ' . $company_id, $updatePrimaryAud['error']);
                $response['message'] = ERROR_3;
                sendRes();
            }
        } else {
            $savePrimAud = setData(Table::COMPANY_ASSIGNED_DATA, [
                COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                COMPANY_ASSIGNED_DATA::USER_ID => $_SESSION[RID],
                COMPANY_ASSIGNED_DATA::AUDITOR_ID => $primary_auditor,
                COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
                COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY => 1,
                COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                COMPANY_ASSIGNED_DATA::CREATED_AT => getToday(),
                COMPANY_ASSIGNED_DATA::UPDATED_AT => getToday()
            ]);
            if (!$savePrimAud) {
                logError("Unabled to save primary auditor for company id: " . $company_id, $savePrimAud['error']);
                $response['message'] = ERROR_1;
                sendRes();
            }
        }
        // $sec_cols = [];
        $checkExisSecAud = getData(Table::COMPANY_ASSIGNED_DATA, [
            COMPANY_ASSIGNED_DATA::AUDITOR_ID
        ], [
            COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY => 2,
            COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS
        ]);
        if (count($secondary_auditor) > 0) {
            if ($update == 1) {
                if (count($checkExisSecAud) > 0) {
                    foreach ($checkExisSecAud as $cak => $cav) {
                        $checkActivity = getData(Table::AUDIT_MEMO_DATA, [
                            AUDIT_MEMO_DATA::STATUS
                        ], [
                            AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS,
                            AUDIT_MEMO_DATA::SECONDARY_AUDITOR_ID => $cav[COMPANY_ASSIGNED_DATA::AUDITOR_ID],
                            AUDIT_MEMO_DATA::COMPANY_ID => $company_id
                        ]);
                        if ((count($checkActivity) > 0) && (!in_array($cav[COMPANY_ASSIGNED_DATA::AUDITOR_ID], $secondary_auditor))) {
                            $response['message'] = "Secondary auditors cannot be reassigned as they have started working.";
                            sendRes();
                        }
                        $deletePriviousSecAud = deleteData(Table::COMPANY_ASSIGNED_DATA, [
                            COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
                            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY => 2,
                            COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                            COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                            COMPANY_ASSIGNED_DATA::AUDITOR_ID => $cav[COMPANY_ASSIGNED_DATA::AUDITOR_ID]
                        ]);
                    }
                }
            }
            foreach ($secondary_auditor as $sk => $sv) {
                $sec_cols[] = [
                    COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                    COMPANY_ASSIGNED_DATA::USER_ID => $_SESSION[RID],
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID => $sv,
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
                    COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY => 2,
                    COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                    COMPANY_ASSIGNED_DATA::CREATED_AT => getToday(),
                    COMPANY_ASSIGNED_DATA::UPDATED_AT => getToday()
                ];
            }
            // rip($sec_cols);
            // exit;
            $save_sec = setMultipleData(Table::COMPANY_ASSIGNED_DATA, $sec_cols);
            if (!$save_sec['res']) {
                logError("Unabled to save secondary auditors for company id: " . $company_id, $save_sec['error']);
                $response['message'] = "Primary Auditor saved but secondary Auditors are not saved";
                sendRes();
            }
        } else {
            if (count($checkExisSecAud) > 0) {
                foreach ($checkExisSecAud as $cak => $cav) {
                    $checkActivity = getData(Table::AUDIT_MEMO_DATA, [
                        AUDIT_MEMO_DATA::STATUS
                    ], [
                        AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS,
                        AUDIT_MEMO_DATA::SECONDARY_AUDITOR_ID => $cav[COMPANY_ASSIGNED_DATA::AUDITOR_ID],
                        AUDIT_MEMO_DATA::COMPANY_ID => $company_id
                    ]);
                    if ((count($checkActivity) > 0) && (!in_array($cav[COMPANY_ASSIGNED_DATA::AUDITOR_ID], $secondary_auditor))) {
                        $response['message'] = "Secondary auditors cannot be removed as they have started working.";
                        sendRes();
                    }
                    $deletePriviousSecAud = deleteData(Table::COMPANY_ASSIGNED_DATA, [
                        COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
                        COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY => 2,
                        COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                        COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
                        COMPANY_ASSIGNED_DATA::AUDITOR_ID => $cav[COMPANY_ASSIGNED_DATA::AUDITOR_ID]
                    ]);
                }
            }
        }
        $response['error'] = false;
        $response['message'] = ($update == 1) ? "Reassigned Successfully!" : "Assigned Successfully!";
        sendRes();
        break;
    case 'GET_QUERIES_FOR_NOTICE':
        $company_id = $ajax_form_data['cid'];
        $queryOptions = '
        <label class="form_label" for="notice_query_select">Select Query</label>
        <select id="notice_query_select" class="form-control multiple" name="notice_query_select[]" multiple="multiple">
        <option value="0" disabled>--- Select Query ---</option>';
        $getQueries = getData(Table::QUERY_DATA, [
            QUERY_DATA::ID,
            QUERY_DATA::QUERY_NO,
            QUERY_DATA::LAST_DATE_OF_REPLY
        ], [
            QUERY_DATA::COMPANY_ID => $company_id,
            QUERY_DATA::QUERY_STATUS => 1,
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (count($getQueries) > 0) {
            foreach ($getQueries as $k => $v) {
                $noticeQids = $nqids = $nd = [];
                $getQExtData=getData(Table::QUERY_EXTENSION_DATES,[
                    QUERY_EXTENSION_DATES::EXTENTION_END_DATE
                ],[
                    QUERY_EXTENSION_DATES::QUERY_ID=>$v[QUERY_DATA::ID],
                    QUERY_EXTENSION_DATES::ACTIVE=>1,
                    QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED=>1
                ]);
                if(count($getQExtData)>0){
                    if($getQExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]<getToday(false)){
                        $checkIfNoticeIssued = getData(Table::COMPANY_NOTICE_DATA, [
                            COMPANY_NOTICE_DATA::QUERY_IDS
                        ], [
                            COMPANY_NOTICE_DATA::COMPANY_ID => $company_id,
                            COMPANY_NOTICE_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                        ]);
                        // rip(array_combine($checkIfNoticeIssued));
                        if (count($checkIfNoticeIssued) > 0) {
                            foreach ($checkIfNoticeIssued as $key => $value) {
                                $noticeQids[] = explode(',', $value[COMPANY_NOTICE_DATA::QUERY_IDS]);
                            }
                        }
                        $flatArray =  array_merge(...$noticeQids);
                        $uArray = array_unique($flatArray);
                        // rip($flatArray);
                        if (count($noticeQids) > 0) {
                            // $nqids=call_user_func_array('array_merge', $noticeQids);
                            // $nqids=var_export(getArrayValuesRecursively($noticeQids), true) . PHP_EOL;
                            if (!in_array($v[QUERY_DATA::ID], $uArray)) {
                                $queryOptions .= '<option value="' . $v[QUERY_DATA::ID] . '">' . $v[QUERY_DATA::QUERY_NO] . '</option>';
                            }
                        } else {
                            $queryOptions .= '<option value="' . $v[QUERY_DATA::ID] . '">' . $v[QUERY_DATA::QUERY_NO] . '</option>';
                        }
                    }
                }else{
                    if($v[QUERY_DATA::LAST_DATE_OF_REPLY]<getToday(false)){
                        $checkIfNoticeIssued = getData(Table::COMPANY_NOTICE_DATA, [
                            COMPANY_NOTICE_DATA::QUERY_IDS
                        ], [
                            COMPANY_NOTICE_DATA::COMPANY_ID => $company_id,
                            COMPANY_NOTICE_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                        ]);
                        // rip(array_combine($checkIfNoticeIssued));
                        if (count($checkIfNoticeIssued) > 0) {
                            foreach ($checkIfNoticeIssued as $key => $value) {
                                $noticeQids[] = explode(',', $value[COMPANY_NOTICE_DATA::QUERY_IDS]);
                            }
                        }
                        $flatArray =  array_merge(...$noticeQids);
                        $uArray = array_unique($flatArray);
                        // rip($flatArray);
                        if (count($noticeQids) > 0) {
                            // $nqids=call_user_func_array('array_merge', $noticeQids);
                            // $nqids=var_export(getArrayValuesRecursively($noticeQids), true) . PHP_EOL;
                            if (!in_array($v[QUERY_DATA::ID], $uArray)) {
                                $queryOptions .= '<option value="' . $v[QUERY_DATA::ID] . '">' . $v[QUERY_DATA::QUERY_NO] . '</option>';
                            }
                        } else {
                            $queryOptions .= '<option value="' . $v[QUERY_DATA::ID] . '">' . $v[QUERY_DATA::QUERY_NO] . '</option>';
                        }
                    }
                }
                
            }
        }
        $queryOptions .= '
        </select>';
        $response['error'] = false;
        $response['queryOptions'] = $queryOptions;
        sendRes();
        break;
    case 'CHECK_OPEN_QURIES_FOR_NOTICE':
        $company_id = $ajax_form_data['cid'];
        $getQueries = getData(Table::QUERY_DATA, [
            QUERY_DATA::ID
        ], [
            QUERY_DATA::COMPANY_ID => $company_id,
            QUERY_DATA::QUERY_STATUS => 1,
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (count($getQueries) == 0) {
            $response['message'] = "No active query found !";
            sendRes();
        }
        $response['error'] = false;
        sendRes();
        break;
    case 'SAVE_NOTICE':
        $company_id = (isset($ajax_form_data['cid'])) ? $ajax_form_data['cid'] : 0;
        $Queries = (isset($ajax_form_data['qry'])) ? $ajax_form_data['qry'] : [];
        $notice_no = (isset($ajax_form_data['notno'])) ? altRealEscape($ajax_form_data['notno']) : "";
        $notice_date = (isset($ajax_form_data['notdate'])) ? altRealEscape($ajax_form_data['notdate']) : "";
        $notice_rep_days = (isset($ajax_form_data['repdays'])) ? altRealEscape($ajax_form_data['repdays']) : "";
        $notice_rep_date = (isset($ajax_form_data['repdate'])) ? altRealEscape($ajax_form_data['repdate']) : "";

        if (
            ($company_id == 0) ||
            (count($Queries) == 0) ||
            ($notice_no == "") ||
            ($notice_date == "") ||
            ($notice_rep_days == "") ||
            ($notice_rep_date == "")
        ) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $q_ids = implode(',', $Queries);
        $save = setData(Table::COMPANY_NOTICE_DATA, [
            COMPANY_NOTICE_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_NOTICE_DATA::COMPANY_ID => $company_id,
            COMPANY_NOTICE_DATA::QUERY_IDS => $q_ids,
            COMPANY_NOTICE_DATA::NOTICE_NO => $notice_no,
            COMPANY_NOTICE_DATA::DATE_OF_NOTICE_ISSUE => $notice_date,
            COMPANY_NOTICE_DATA::DAYS_TO_REPLY_NOTICE => $notice_rep_days,
            COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY => $notice_rep_date,
            COMPANY_NOTICE_DATA::NOTICE_STATUS => 2,
            COMPANY_NOTICE_DATA::CREATED_AT => getToday(),
            COMPANY_NOTICE_DATA::UPDATED_AT => getToday()
        ]);
        if (!$save['res']) {
            logError("Unabled to insert notice data, notice no.: " . $notice_no . ", company_id: " . $company_id, $save['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Saved Successfully !";
        sendRes();
        break;
    case 'GET_NOTICE_TABLE':
        $company_id = $ajax_form_data['cid'];
        $trows = '';
        $audit_closed=$company_inactive=false;
        $getAuditActiveStatus=getData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE],[
            AUDITS_DATA::COMPANY_ID=>$company_id,
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDITS_DATA::STATUS=>ACTIVE_STATUS
        ]);
        $getComActiveStat=getData(Table::COMPANIES,[COMPANIES::ACTIVE,COMPANIES::ACTIVE_INACTIVE_DATE],[COMPANIES::ID=>$company_id]);
        if (count($getComActiveStat)>0) {
            if($getComActiveStat[0][COMPANIES::ACTIVE]==0){
                $company_inactive=true;
            } else {
                $company_inactive=false;
            }
        }
        if(count($getAuditActiveStatus)>0){
            if($getAuditActiveStatus[0][AUDITS_DATA::ACTIVE]==2){
                $audit_closed=true;
            }
        }
        $getNoticeData = getData(Table::COMPANY_NOTICE_DATA, ['*'], [
            COMPANY_NOTICE_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_NOTICE_DATA::COMPANY_ID => $company_id
        ]);
        if (count($getNoticeData) > 0) {
            foreach ($getNoticeData as $k => $v) {
                $qids = $v[COMPANY_NOTICE_DATA::QUERY_IDS];
                $getQnos = getData(Table::QUERY_DATA, [
                    QUERY_DATA::QUERY_NO
                ], [
                    QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                ], [
                    QUERY_DATA::ID => explode(',', $qids)
                ]);
                $query_nos = $date_of_reply = $reply_status = EMPTY_VALUE;
                if (
                    ($v[COMPANY_NOTICE_DATA::DATE_OF_REPLY_NOTICE] != "") ||
                    ($v[COMPANY_NOTICE_DATA::DATE_OF_REPLY_NOTICE] != null)
                ) {
                    $date_of_reply = getFormattedDateTime($v[COMPANY_NOTICE_DATA::DATE_OF_REPLY_NOTICE]);
                }
                switch ($v[COMPANY_NOTICE_DATA::NOTICE_STATUS]) {
                    case 2:
                        $reply_status = '<span class="badge badge-light"><small>'.EMPTY_VALUE.'</small></span>';
                        $reply_status .= ($_SESSION[USER_TYPE] == EMPLOYEE) && (!$audit_closed) && (!$company_inactive) ? '&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddNoticeReply(' . $v[COMPANY_NOTICE_DATA::ID] . ')"><small><i class="fas fa-plus"></i>Add Rep.</small></span>' : '';
                        break;
                    case 1:
                        $reply_status = '<span class="badge badge-success"><small>Submitted</small></span>';
                        break;
                    case 3:
                        $reply_status = '<span class="badge badge-danger">Overdue</span>';
                        $reply_status .= ($_SESSION[USER_TYPE] == EMPLOYEE) && (!$audit_closed) && (!$company_inactive) ? '&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddNoticeReply(' . $v[COMPANY_NOTICE_DATA::ID] . ')"><small><i class="fas fa-plus"></i>Add Rep.</small></span>' : '';
                        break;
                }
                if (
                    // (getToday(false, 'Y') == getFormattedDateTime($v[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY], 'Y')) &&
                    // (getToday(false, 'm') == getFormattedDateTime($v[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY], 'm')) &&
                    // (getToday(false, 'd') > getFormattedDateTime($v[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY], 'd')) &&
                    (getToday(false) > $v[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY]) 
                    &&
                    (
                        ($v[COMPANY_NOTICE_DATA::NOTICE_STATUS] == 3) ||
                        ($v[COMPANY_NOTICE_DATA::NOTICE_STATUS] == 2)
                    )
                ) {
                    $reply_status = '<span class="badge badge-danger"><small>Overdue</small></span>';
                    $reply_status .= (!$audit_closed) && (!$company_inactive) ? '&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddNoticeReply(' . $v[COMPANY_NOTICE_DATA::ID] . ')"><small><i class="fas fa-plus"></i>Add Rep.</small></span>':'';
                }
                $query_ids = [];
                if (count($getQnos) > 0) {
                    foreach ($getQnos as $qk => $qv) {
                        $query_ids[] = $qv[QUERY_DATA::QUERY_NO];
                    }
                    $query_nos = implode(', ', $query_ids);
                }
                $trows .= '
                <tr>
                    <td>' . ($k + 1) . '</td>
                    <td>' . altRealEscape($v[COMPANY_NOTICE_DATA::NOTICE_NO]) . '</td>
                    <td>' . $query_nos . '</td>
                    <td>' . getFormattedDateTime($v[COMPANY_NOTICE_DATA::DATE_OF_NOTICE_ISSUE]) . '</td>
                    <td>' . getFormattedDateTime($v[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY]) . '</td>
                    <td>' . altRealEscape($v[COMPANY_NOTICE_DATA::DAYS_TO_REPLY_NOTICE]) . '</td>
                    <td>' . $date_of_reply . '</td>
                    <td>' . $reply_status . '</td>
                </tr>
                ';
            }
        } else {
            $trows = '
            <tr class="animated fadeInDown">
                <td colspan="8">
                    <div class="alert alert-danger" role="alert">
                        No Notice found !
                    </div>
                </td>
            </tr>
            ';
        }
        $response['error'] = false;
        $response['trows'] = $trows;
        $response['audit_closed'] = $audit_closed;
        $response['company_inactive'] = $company_inactive;
        sendRes();
        break;
    case 'SAVE_MEMO':
        $memo_no = (isset($ajax_form_data['mno'])) ? altRealEscape($ajax_form_data['mno']) : "";
        $memo_total_no_of_query = (isset($ajax_form_data['mtnoq'])) ? altRealEscape($ajax_form_data['mtnoq']) : "";
        $date_of_issue_memo = (isset($ajax_form_data['doi'])) ? altRealEscape($ajax_form_data['doi']) : "";
        $days_to_reply_memo = (isset($ajax_form_data['dystr'])) ? altRealEscape($ajax_form_data['dystr']) : "";
        $end_date_of_reply_memo = (isset($ajax_form_data['ldor'])) ? altRealEscape($ajax_form_data['ldor']) : "";
        $company_id = $ajax_form_data['cid'];

        $primary_aud_id = 0;
        $getPrimaryAud = getData(Table::COMPANY_ASSIGNED_DATA, [
            COMPANY_ASSIGNED_DATA::AUDITOR_ID
        ], [
            COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANY_ASSIGNED_DATA::COMPANY_IDS => $company_id,
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY => 1,
            COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS
        ]);
        $primary_aud_id = (count($getPrimaryAud) > 0) ? $getPrimaryAud[0][COMPANY_ASSIGNED_DATA::AUDITOR_ID] : 0;
        $save = setData(Table::AUDIT_MEMO_DATA, [
            AUDIT_MEMO_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_MEMO_DATA::COMPANY_ID => $company_id,
            AUDIT_MEMO_DATA::SECONDARY_AUDITOR_ID => $_SESSION[RID],
            AUDIT_MEMO_DATA::PRIMARY_AUDITOR_ID => $primary_aud_id,
            AUDIT_MEMO_DATA::MEMO_NO => $memo_no,
            AUDIT_MEMO_DATA::TOTAL_NO_OF_QUERY => $memo_total_no_of_query,
            AUDIT_MEMO_DATA::DATE_OF_ISSUE => $date_of_issue_memo,
            AUDIT_MEMO_DATA::DAYS_TO_REPLY => $days_to_reply_memo,
            AUDIT_MEMO_DATA::LAST_DATE_OF_REPLY => $end_date_of_reply_memo,
            AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS,
            AUDIT_MEMO_DATA::CREATED_AT => getToday(),
            AUDIT_MEMO_DATA::UPDATED_AT => getToday()
        ]);
        if (!$save['res']) {
            logError("Unabled to save Memo data, company id: " . $company_id . ", Auditor id: " . $_SESSION[RID] . ", Memo No.: " . $memo_no, $save['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Memo Saved Successfully !";
        sendRes();
        break;
    case 'GET_AUDITOR_AUDITS_TABLE':
        $company_row = $audit_btn = '';
        $assignedCompanyIds = [];
        $getAssignedCompanies = getData(Table::COMPANY_ASSIGNED_DATA, [
            COMPANY_ASSIGNED_DATA::COMPANY_IDS,
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
            COMPANY_ASSIGNED_DATA::AUDITOR_ID
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
        // rip($getAssignedCompanies);
        $getCompData = getData(Table::COMPANIES, ['*'], [
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANIES::STATUS => ACTIVE_STATUS
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
                        $getAuditData = getData(Table::AUDITS_DATA, [
                            AUDITS_DATA::ID,
                            AUDITS_DATA::AUDIT_START_DATE,
                            AUDITS_DATA::AUDIT_END_DATE,
                            AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE,
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
                                $closeRequested=$closeApproved=$closeRejected=false;
                                if (count($getApprovalData)>0) {
                                    $closeRequested=true;
                                    if ($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS]==1) {
                                    $closeApproved=true;
                                    }
                                    if ($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS]==2) {
                                      $closeRejected=true;
                                    }
                                }
                                $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
                                $audit_btn .= ($getAuditData[0][AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE] != null) ? '<br><span><strong class="text-info"><small>Expected Close Date: </small></strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE]) . '</small></span>' : '';
                                if ($closeRequested && !$closeApproved && !$closeRejected) {
                                    $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong>Close Requested on: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::REQUEST_DATE]).'<small></span></br><button class="btn btn-sm btn-success" type="button" onclick="approveCloseAudit(' . $getAuditData[0][AUDITS_DATA::ID] . ',\''.$getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::REASON].'\');"><i class="fas fa-stamp"></i>&nbsp;Approve</button>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                } else {
                                    if ($closeRequested && $closeApproved) {
                                        $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong>Close Request Approved: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_DATE]).'<small></span>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                    }
                                    if ($closeRequested && $closeRejected) {
                                        $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong class="text-danger">Close Request Rejected: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_DATE]).'<small></span>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                    }
                                }
                                // $audit_btn .= '<br><button class="btn btn-sm btn-danger" type="button">Close</button>';

                            } else {
                                // check & show closed status here
                                if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 2) {
                                  $audit_closed=true;
                                  $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
                                  $audit_btn .= '<br/><span class="cursor-pointer"><strong class="text-danger">Closed: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_END_DATE]) . '</small></span>';
                                } else {
                                  $audit_closed=false;
                                  $audit_btn = '<span class="text-info"><small style="font-weight: bold;">Not Started yet !</small></span>';
                                }
                                // $audit_btn = '<span class="text-info"><small style="font-weight: bold;">Not Started yet !</small></span>';
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
                            // $audit_btn = ($v[COMPANIES::ACTIVE]==1)?'<button class="btn btn-sm btn-success" type="button" onclick="StartAudit(' . $v[COMPANIES::ID] . ');">Start</button>':'<span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                            // $audit_btn = '';
                            $checkPrimarySecondary = getData(Table::COMPANY_ASSIGNED_DATA, [
                                COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
                            ], [
                                COMPANY_ASSIGNED_DATA::COMPANY_IDS => $v[COMPANIES::ID],
                                COMPANY_ASSIGNED_DATA::AUDITOR_ID => $_SESSION[RID],
                            ]);
                            // rip($checkPrimarySecondary);
                            // echo "end ".($k+1)."st loop <br>";
                            $getAuditData = getData(Table::AUDITS_DATA, [
                                AUDITS_DATA::ID,
                                AUDITS_DATA::AUDIT_START_DATE,
                                AUDITS_DATA::AUDIT_END_DATE,
                                AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE,
                                AUDITS_DATA::ACTIVE
                            ], [
                                AUDITS_DATA::STATUS => ACTIVE_STATUS,
                                AUDITS_DATA::COMPANY_ID => $v[COMPANIES::ID]
                            ]);
                            // $audit_btn .= $checkPrimarySecondary[0][COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY];
                            if ($checkPrimarySecondary[0][COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 2) {
                                $audit_btn = ($v[COMPANIES::ACTIVE]==1)?'<small><button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');"><i class="fas fa-tasks"></i>&nbsp;<small>Send Memo</small></button></small>':'<span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                $getMemoData = getData(Table::AUDIT_MEMO_DATA, [AUDIT_MEMO_DATA::MEMO_NO], [
                                    AUDIT_MEMO_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                                    AUDIT_MEMO_DATA::COMPANY_ID => $v[COMPANIES::ID],
                                    AUDIT_MEMO_DATA::SECONDARY_AUDITOR_ID => $_SESSION[RID],
                                    AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS
                                ]);
                                // $audit_btn .= $checkPrimarySecondary[0][COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY];
                                if (count($getMemoData) > 0) {
                                  $audit_btn = '
                                  <div class="audit_memo_btn">
                                    <small><span class="badge badge-white cursor-pointer '.TOOLTIP_CLASS.'" style="font-size: 20px !important;" title="Click to see Memo Details" onclick="viewMemoInfo(' . $v[COMPANIES::ID] . ')"><i class="fas fa-info-circle"></i></span></small>
                                  ';
                                    if (count($getAuditData) > 0) {
                                        if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
                                            if ($v[COMPANIES::ACTIVE]==1) {
                                                $audit_btn .= '
                                                &nbsp;
                                                <small><button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');"><i class="fas fa-tasks"></i>&nbsp;<small>Send Memo</small></button></small>
                                                ';
                                            } else {
                                                $audit_btn .= '
                                                &nbsp;
                                                <span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>
                                                ';
                                            }
                                        } else {
                                            $audit_btn .= '&nbsp;<span class="badge alert-danger"><small>Audit Closed!<small></span>';
                                        }
                                    } else {
                                        $audit_btn = '<span class="badge alert-info"><small>Audit has not started yet!<small></span>';
                                    }
                                  $audit_btn .= '
                                  </div>
                                  ';
                                } else {
                                  if (count($getAuditData) > 0) {
                                    if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 1) {
                                        // echo 'Hii';
                                    //   $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>':'<span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                    //   $audit_btn = '<button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>';
                                    //   echo $audit_btn;
                                    } else {
                                        // echo 'Hii';
                                      $audit_btn = '<span class="badge badge-info"><small>Audit Closed!<small></span>';
                                    }
                                  } else {
                                    $audit_btn = '<span class="badge alert-info"><small>Audit has not started yet!<small></span>';
                                  }
                                //   $audit_btn = '<button class="btn btn-sm btn-info" type="button" onclick="SendMemo(' . $v[COMPANIES::ID] . ');">Send Memo</button>';
                                }
                            } else {
                                $audit_btn = ($v[COMPANIES::ACTIVE]==1)?'<button class="btn btn-sm btn-success" type="button" onclick="enterExpectedCompleteDate(' . $v[COMPANIES::ID] . ');"><i class="fas fa-play"></i>&nbsp;Start</button>':'<span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                $getAuditData = getData(Table::AUDITS_DATA, [
                                    AUDITS_DATA::ID,
                                    AUDITS_DATA::AUDIT_START_DATE,
                                    AUDITS_DATA::AUDIT_END_DATE,
                                    AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE,
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
                                        $closeRequested=$closeApproved=$closeRejected=false;
                                        if (count($getApprovalData)>0) {
                                          $closeRequested=true;
                                          if ($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS]==1) {
                                            $closeApproved=true;
                                          }
                                          if ($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS]==2) {
                                            $closeRejected=true;
                                          }
                                        }
                                        $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
                                        $audit_btn .= ($getAuditData[0][AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE]!=null)?'<br><span><strong class="text-info"><small>Expected Close Date: </small></strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE]) . '</small></span>':'';
                                        if (!$closeRequested && !$closeApproved) {
                                            $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><button class="btn btn-sm btn-danger" type="button" onclick="closeAudit(' . $v[COMPANIES::ID] . ');"><i class="fas fa-stop-circle"></i>&nbsp;Close</button>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                        }
                                        if ($closeRequested && !$closeApproved && !$closeRejected) {
                                            $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong>Close Requested on: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::REQUEST_DATE]).'<small></span>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                        } else {
                                            if ($closeRequested && $closeApproved) {
                                                $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong>Close Request Approved: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_DATE]).'<small></span><br><button class="btn btn-sm btn-danger" type="button" onclick="closeAudit(' . $v[COMPANIES::ID] . ');"><i class="fas fa-stop-circle"></i>&nbsp;Close</button>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                            }
                                            if ($closeRequested && $closeRejected) {
                                                $audit_btn .= ($v[COMPANIES::ACTIVE]==1)?'<br><span class="badge badge-light"><small><strong class="text-danger">Close Request Rejected: </strong>'.getFormattedDateTime($getApprovalData[0][AUDIT_CLOSE_REQUEST_DATA::APPROVAL_DATE]).'<small></span><br><button class="btn btn-sm btn-danger" type="button" onclick="closeAudit(' . $v[COMPANIES::ID] . ');"><i class="fas fa-stop-circle"></i>&nbsp;Close</button>':'</br><span class="badge badge-light"><small><strong>Inactive From: </strong>'.getFormattedDateTime($v[COMPANIES::ACTIVE_INACTIVE_DATE]).'<small></span>';
                                            }
                                        }
                                    } else {
                                        // check & show close status here
                                        if ($getAuditData[0][AUDITS_DATA::ACTIVE] == 2) {
                                          $audit_btn = '<span><strong class="text-success">Started: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_START_DATE]) . '</small></span>';
                                          $audit_btn .= '<br/><span><strong class="text-danger">Closed: </strong><small>' . getFormattedDateTime($getAuditData[0][AUDITS_DATA::AUDIT_END_DATE]) . '</small></small><b style="cursor:pointer;" class="'.TOOLTIP_CLASS.'" title="Click to edit the audit close date" onclick="editAuditCloseDate('.$getAuditData[0][AUDITS_DATA::ID].',\''.$getAuditData[0][AUDITS_DATA::AUDIT_END_DATE].'\');">&nbsp;<i class="fas fa-edit text-primary"></i></b></span>';
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
        $response['error'] = false;
        $response['company_row'] = $company_row;
        $response['audit_btn'] = $audit_btn;
        sendRes();
        break;
    case 'SAVE_EDITED_AUDIT_CLOSE_DATE':
        $audit_id = isset($ajax_form_data['aid'])?$ajax_form_data['aid']:0;
        $date = isset($ajax_form_data['dt'])?altRealEscape($ajax_form_data['dt']):'';
        if (($audit_id == 0) || ($date == "")) {
            $response['message']=ERROR_1;
            sendRes();
        }
        $update = updateData(Table::AUDITS_DATA,[
            AUDITS_DATA::AUDIT_END_DATE=>$date,
            AUDITS_DATA::UPDATED_AT=>getToday()
        ],[
            AUDITS_DATA::ID=>$audit_id,
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if (!$update['res']) {
            logError("Unabled to update the audit close date, Audit ID: ".$audit_id.", User ID: ".$_SESSION[RID].", Edited close date: ".$date, $update['error']);
            $response["message"]=ERROR_1;
            sendRes();
        }
        $response['error']=false;
        $response['message']="Updated Successfully !";
        sendRes();
        break;
    case 'APPROVE_AUDIT_CLOSE':
        $audit_id = isset($ajax_form_data['audid'])?$ajax_form_data['audid']:0;
        $action = isset($ajax_form_data['act'])?$ajax_form_data['act']:0;
        if(($audit_id==0)||($action==0)){
            $response['message'] = ERROR_3;
            sendRes();
        }
        $update = updateData(Table::AUDIT_CLOSE_REQUEST_DATA,[
            AUDIT_CLOSE_REQUEST_DATA::APPROVAL_STATUS=>$action,
            AUDIT_CLOSE_REQUEST_DATA::APPROVED_BY=>$_SESSION[RID],
            AUDIT_CLOSE_REQUEST_DATA::APPROVAL_DATE=>getToday(),
            AUDIT_CLOSE_REQUEST_DATA::UPDATED_AT=>getToday()
        ],[
            AUDIT_CLOSE_REQUEST_DATA::AUDIT_ID=>$audit_id,
            AUDIT_CLOSE_REQUEST_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if (!$update['res']) {
            logError("Unabled to update Audit Approval, Audit ID: ".$audit_id, $update['error']);
            $response['message']=ERROR_1;
            sendRes();
        }
        $response['error']=false;
        $response['message']=($action==1)?"Approved!":"Rejected!";
        sendRes();
        break;
    case 'GET_AUDITOR_MEMO_INFO':
        $company_id = $ajax_form_data['cid'];
        $audType = $ajax_form_data['audType'];
        $trows = $thtml = "";
        if (($company_id == "") || ($company_id == 0)) {
            $response['message'] = ERROR_1;
            sendRes();
        }
        $wh=[
            AUDIT_MEMO_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_MEMO_DATA::COMPANY_ID => $company_id,
            AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS
        ];
        if($audType==1){
            $wh[AUDIT_MEMO_DATA::PRIMARY_AUDITOR_ID] = $_SESSION[RID];
        } else {
            $wh[AUDIT_MEMO_DATA::SECONDARY_AUDITOR_ID] = $_SESSION[RID];
        }
        $getMemoData = getData(Table::AUDIT_MEMO_DATA, [
            AUDIT_MEMO_DATA::ID,
            AUDIT_MEMO_DATA::DATE_OF_ISSUE,
            AUDIT_MEMO_DATA::DAYS_TO_REPLY,
            AUDIT_MEMO_DATA::LAST_DATE_OF_REPLY,
            AUDIT_MEMO_DATA::MEMO_NO,
            AUDIT_MEMO_DATA::TOTAL_NO_OF_QUERY
        ], $wh);
        if (count($getMemoData) > 0) {
            $sl = 1;
            foreach ($getMemoData as $k => $v) {
                $checkIfMemoUsed=getData(Table::QUERY_DATA,[QUERY_DATA::MEMO_ID],[QUERY_DATA::MEMO_ID=>$v[AUDIT_MEMO_DATA::ID]]);
                $used='';
                if(count($checkIfMemoUsed)>0){
                    $used='<span class="badge badge-info"><small>Used</small></span>';
                }
                $trows .= '<tr class="animated bounceInUp">
                    <td>' . $sl . '</td>
                    <td>' . altRealEscape($v[AUDIT_MEMO_DATA::MEMO_NO]) . '&nbsp;'.$used.'</td>
                    <td>' . altRealEscape($v[AUDIT_MEMO_DATA::TOTAL_NO_OF_QUERY]) . '</td>
                    <td>' . getFormattedDateTime($v[AUDIT_MEMO_DATA::DATE_OF_ISSUE]) . '</td>
                    <td>' . altRealEscape($v[AUDIT_MEMO_DATA::DAYS_TO_REPLY]) . '</td>
                    <td>' . getFormattedDateTime($v[AUDIT_MEMO_DATA::LAST_DATE_OF_REPLY]) . '</td>
                </tr>';
                $sl++;
            }
        } else {
            $trows = '<tr class="animated fadeInDown">
                <td colspan="7">
                    <div class="alert alert-danger" role="alert">
                        No Memos found !
                    </div>
                </td>
            </tr>';
        }
        $thtml = <<<HTML
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12">
    <div class="table-responsive">
    <table class="table table-sm table-striped table-hover text-center auditor_memo_table data-table" id="auditor_memo_table">
        <thead class="text-center table-warning">
        <tr style="text-transform: uppercase; font-size: 12px;">
            <th>sl.</th>
            <th>memo No.</th>
            <th>total no. of query</th>
            <th>date of issue</th>
            <th>days to reply</th>
            <th>Due Reply date</th>
        </tr>
        </thead>
        <tbody>
            $trows
        </tbody>
    </table>
    </div>
</div>
</div>
HTML;
        $response['error'] = false;
        $response['thtml'] = $thtml;
        sendRes();
        break;
    case 'GET_MEMO_SELECT_TO_QUERY':
        $company_id = $ajax_form_data['cid'];
        $select = "";
        $op = '<option value="0" disabled>--- Select Memo ? ----</option>';
        $memoCount = 0;
        $getMemoData = getData(Table::AUDIT_MEMO_DATA, [
            AUDIT_MEMO_DATA::ID,
            AUDIT_MEMO_DATA::DATE_OF_ISSUE,
            AUDIT_MEMO_DATA::DAYS_TO_REPLY,
            AUDIT_MEMO_DATA::LAST_DATE_OF_REPLY,
            AUDIT_MEMO_DATA::MEMO_NO,
            AUDIT_MEMO_DATA::TOTAL_NO_OF_QUERY
        ], [
            AUDIT_MEMO_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_MEMO_DATA::COMPANY_ID => $company_id,
            AUDIT_MEMO_DATA::PRIMARY_AUDITOR_ID => $_SESSION[RID],
            AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS
        ]);

        if (count($getMemoData) > 0) {
            foreach ($getMemoData as $k => $v) {
                $checkMemoUsed = getData(Table::QUERY_DATA, [
                    QUERY_DATA::MEMO_ID
                ], [
                    QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                    QUERY_DATA::MEMO_ID => $v[AUDIT_MEMO_DATA::ID]
                ]);
                if (count($checkMemoUsed) > 0) {
                    //do nothing
                } else {
                    $memoCount++;
                    $memoInfo=$v[AUDIT_MEMO_DATA::MEMO_NO].' [ Q.DOI: '.$v[AUDIT_MEMO_DATA::DATE_OF_ISSUE].',    Q.Due Dt.: '.$v[AUDIT_MEMO_DATA::LAST_DATE_OF_REPLY].',    No. Q.: '.$v[AUDIT_MEMO_DATA::TOTAL_NO_OF_QUERY].' ]';
                    $op .= '<option value="' . $v[AUDIT_MEMO_DATA::ID] . '">' . $memoInfo . '</option>';
                }
            }
        }
        $tc = TOOLTIP_CLASS;
        $select = <<<HTML
<div class="col-md-7">
    <select class="form-control multiple" id="query_section_memo_select" multiple="" name="query_section_memo_select[]">
        $op
    </select>
</div>
<div class="col-md-3 text-left" id="query_memo_view_section">
    <span class="badge badge-white cursor-pointer $tc" title="Click to see Memo Details" onclick="viewMemoInfo($company_id,1)"><i class="fas fa-2x fa-info-circle"></i></span>
</div>
HTML;
        $response['error'] = false;
        $response['select'] = $select;
        $response['count'] = $memoCount;
        sendRes();
        break;
    case 'AUDITOR_FILL_INFO_FROM_MEMO':
        $memo_id = $ajax_form_data['mid'];
        $info = [];
        $getMemoData = getData(Table::AUDIT_MEMO_DATA, [
            AUDIT_MEMO_DATA::DATE_OF_ISSUE,
            AUDIT_MEMO_DATA::DAYS_TO_REPLY,
            AUDIT_MEMO_DATA::LAST_DATE_OF_REPLY,
            AUDIT_MEMO_DATA::MEMO_NO,
            AUDIT_MEMO_DATA::TOTAL_NO_OF_QUERY
        ], [
            AUDIT_MEMO_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_MEMO_DATA::ID => $memo_id,
            AUDIT_MEMO_DATA::PRIMARY_AUDITOR_ID => $_SESSION[RID],
            AUDIT_MEMO_DATA::STATUS => ACTIVE_STATUS
        ]);
        if (count($getMemoData) > 0) {
            $d = $getMemoData[0];
            $info = [
                'toq' => $d[AUDIT_MEMO_DATA::TOTAL_NO_OF_QUERY],
                'doi' => $d[AUDIT_MEMO_DATA::DATE_OF_ISSUE],
                'dtr' => $d[AUDIT_MEMO_DATA::DAYS_TO_REPLY],
                'ldtr' => $d[AUDIT_MEMO_DATA::LAST_DATE_OF_REPLY],
            ];
        }
        $response['error'] = false;
        $response['info'] = $info;
        sendRes();
        break;
    case 'SAVE_NOTICE_REPLY':
        $notice_id = $ajax_form_data['nid'];
        $date_of_reply = $ajax_form_data['dor'];
        if (($notice_id == "") || ($notice_id == 0)) {
            $response['message'] = ERROR_1;
            sendRes();
        }
        $update = updateData(Table::COMPANY_NOTICE_DATA, [
            COMPANY_NOTICE_DATA::DATE_OF_REPLY_NOTICE => altRealEscape($date_of_reply),
            COMPANY_NOTICE_DATA::UPDATED_AT => getToday(),
            COMPANY_NOTICE_DATA::NOTICE_STATUS => 1
        ], [
            COMPANY_NOTICE_DATA::ID => $notice_id,
            COMPANY_NOTICE_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$update['res']) {
            logError("Unable to update notice data to capture notice reply, notice id: " . $notice_id, $update['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Reply captured successfully !";
        sendRes();
        break;
    case 'SAVE_POSITION_PAPER_REPLY':
        $position_id = $ajax_form_data['pid'];
        $date_of_reply = $ajax_form_data['dor'];
        if (($position_id == "") || ($position_id == 0)) {
            $response['message'] = ERROR_1;
            sendRes();
        }
        $update = updateData(Table::POSITION_PAPERS, [
            POSITION_PAPERS::DATE_OF_REPLY => altRealEscape($date_of_reply),
            POSITION_PAPERS::UPDATED_AT => getToday(),
            POSITION_PAPERS::OPEN_CLOSE_STATUS => 0
        ], [
            POSITION_PAPERS::ID => $position_id,
            POSITION_PAPERS::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$update['res']) {
            logError("Unable to update position data to capture position reply, position id: " . $position_id, $update['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $updatePdata = updateData(Table::POSITION_PAPER_DATA, [
            POSITION_PAPER_DATA::DATE_OF_REPLY => altRealEscape($date_of_reply),
            POSITION_PAPER_DATA::UPDATED_AT => getToday(),
            POSITION_PAPER_DATA::ACTIVE => 0
        ], [
            POSITION_PAPER_DATA::POSITION_PAPER_ID => $position_id,
            POSITION_PAPER_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$updatePdata['res']) {
            logError("Unable to update position paper data to capture position reply, position id: " . $position_id, $updatePdata['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Reply captured successfully !";
        sendRes();
        break;
    case 'SAVE_EXTENSION_DATE_AUDITOR':
        $query_id = (isset($ajax_form_data['qid'])) ? $ajax_form_data['qid'] : 0;
        $extension_start_date = (isset($ajax_form_data['stdate'])) ? altRealEscape($ajax_form_data['stdate']) : "";
        $extension_end_date = (isset($ajax_form_data['enddate'])) ? altRealEscape($ajax_form_data['enddate']) : "";
        $extension_days = (isset($ajax_form_data['extdays'])) ? altRealEscape($ajax_form_data['extdays']) : "";

        if (($query_id == 0) || ($extension_start_date == "") || ($extension_days == "") || ($extension_end_date == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $getExtData = getData(Table::QUERY_EXTENSION_DATES, [
            QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED,
            QUERY_EXTENSION_DATES::EXTENTION_END_DATE,
            QUERY_EXTENSION_DATES::ACTIVE
        ], [
            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_EXTENSION_DATES::ACTIVE => 1,
            QUERY_EXTENSION_DATES::QUERY_ID => $query_id
        ]);
        if (count($getExtData) > 0) {
            if (
                ($getExtData[0][QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED] == 1) &&
                ($getExtData[0][QUERY_EXTENSION_DATES::ACTIVE] == 1)
            ) {
                if (getToday(false) <= getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                    $response['message'] = "An extension already Active !";
                    sendRes();
                }
            }
            if (
                ($getExtData[0][QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED] == 0) &&
                ($getExtData[0][QUERY_EXTENSION_DATES::ACTIVE] == 1)
            ) {
                if (getToday(false) <= getFormattedDateTime($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                    $response['message'] = "An extension request already raised !";
                    sendRes();
                }
            }
            $update = updateData(Table::QUERY_EXTENSION_DATES, [
                QUERY_EXTENSION_DATES::ACTIVE => 0,
                QUERY_EXTENSION_DATES::UPDATED_AT => getToday(),
            ], [
                QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
                QUERY_EXTENSION_DATES::QUERY_ID => $query_id
            ]);
            if (!$update['res']) {
                logError("Unabled to inactive all the previous extionsions before requesting new extension, Query id: " . $query_id, $update['error']);
                $response['message'] = ERROR_1;
                sendRes();
            }
        }
        $save = setData(Table::QUERY_EXTENSION_DATES, [
            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_EXTENSION_DATES::QUERY_ID => $query_id,
            QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED => 0,
            QUERY_EXTENSION_DATES::EXTENSION_DAYS => $extension_days,
            QUERY_EXTENSION_DATES::EXTENTION_START_DATE => $extension_start_date,
            QUERY_EXTENSION_DATES::EXTENTION_END_DATE => $extension_end_date,
            QUERY_EXTENSION_DATES::ACTIVE => 1,
            QUERY_EXTENSION_DATES::CREATED_AT => getToday(),
            QUERY_EXTENSION_DATES::UPDATED_AT => getToday()
        ]);
        if (!$save['res']) {
            logError("Unabled to raise new extension request for query id: " . $query_id, $save['error']);
            $response['message'] = ERROR_3;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "New Extension request placed successfully !";
        sendRes();
        break;
    case 'GET_QUERY_EXT_REQUESTED_DAYS':
        $query_id = $ajax_form_data['qid'];
        $ext_id = $ajax_form_data['eid'];
        $getExtData=getData(Table::QUERY_EXTENSION_DATES,[
            QUERY_EXTENSION_DATES::EXTENSION_DAYS,
            QUERY_EXTENSION_DATES::EXTENTION_START_DATE,
            QUERY_EXTENSION_DATES::EXTENTION_END_DATE
        ],[
            QUERY_EXTENSION_DATES::ID=>$ext_id,
            QUERY_EXTENSION_DATES::QUERY_ID=>$query_id,
            QUERY_EXTENSION_DATES::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        $ext_days=$ext_end_date='';
        if(count($getExtData)>0){
            $ext_days=$getExtData[0][QUERY_EXTENSION_DATES::EXTENSION_DAYS];
            $ext_end_date=$getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE];
        }
        $response['error']=false;
        $response['ext_days']=$ext_days;
        $response['ext_end_date']=getFormattedDateTime($ext_end_date);
        sendRes();
        break;
    case 'GET_NEW_QUERY_EXT_LAST_DATE':
        $query_id = $ajax_form_data['qid'];
        $ext_id = $ajax_form_data['eid'];
        $days = $ajax_form_data['days'];
        $getReqExtData = getData(Table::QUERY_EXTENSION_DATES, [
            QUERY_EXTENSION_DATES::EXTENSION_DAYS,
            QUERY_EXTENSION_DATES::EXTENTION_START_DATE,
            QUERY_EXTENSION_DATES::EXTENTION_END_DATE,
            QUERY_EXTENSION_DATES::ACTIVE
        ], [
            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_EXTENSION_DATES::ACTIVE => 1,
            QUERY_EXTENSION_DATES::QUERY_ID => $query_id,
            QUERY_EXTENSION_DATES::ID => $ext_id
        ]);
        $ext_req_days = $getReqExtData[0][QUERY_EXTENSION_DATES::EXTENSION_DAYS];
        $ext_req_start_date = $getReqExtData[0][QUERY_EXTENSION_DATES::EXTENTION_START_DATE];
        $ext_req_end_date = $getReqExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE];
        $new_ext_date=addDaysToDate($ext_req_start_date,$days);
        $response['error']=false;
        $response['new_ext_date']=getFormattedDateTime($new_ext_date);
        sendRes();
        break;
    case 'APPROVE_QUERY_EXTENSION':
        $query_id = $ajax_form_data['qid'];
        $ext_id = $ajax_form_data['eid'];
        $query_ext_approved_days = $ajax_form_data['ed'];

        $getReqExtData = getData(Table::QUERY_EXTENSION_DATES, [
            QUERY_EXTENSION_DATES::EXTENSION_DAYS,
            QUERY_EXTENSION_DATES::EXTENTION_START_DATE,
            QUERY_EXTENSION_DATES::EXTENTION_END_DATE,
            QUERY_EXTENSION_DATES::ACTIVE
        ], [
            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_EXTENSION_DATES::ACTIVE => 1,
            QUERY_EXTENSION_DATES::QUERY_ID => $query_id,
            QUERY_EXTENSION_DATES::ID => $ext_id
        ]);
        $ext_req_days = $getReqExtData[0][QUERY_EXTENSION_DATES::EXTENSION_DAYS];
        $ext_req_start_date = $getReqExtData[0][QUERY_EXTENSION_DATES::EXTENTION_START_DATE];
        $ext_req_end_date = $getReqExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE];
        $req_ext_date=addDaysToDate($ext_req_start_date,$query_ext_approved_days);
        if ($ext_req_days!=$query_ext_approved_days) {
            if ($req_ext_date<getToday(false)) {
                $response['message']="Approval cannot be processed ! The approved extension date cannot be earlier than the current date";
                sendRes();
            }
        }

        $extUpdate = updateData(Table::QUERY_EXTENSION_DATES, [
            QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED => 1,
            QUERY_EXTENSION_DATES::EXTENSION_DAYS => $query_ext_approved_days,
            QUERY_EXTENSION_DATES::EXTENTION_END_DATE => $req_ext_date,
            QUERY_EXTENSION_DATES::UPDATED_AT => getToday()
        ], [
            QUERY_EXTENSION_DATES::QUERY_ID => $query_id,
            QUERY_EXTENSION_DATES::ID => $ext_id,
            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$extUpdate['res']) {
            logError("Unabled to Approve Query Extension Date, Query ID: " . $query_id . ", Extension ID: " . $ext_id, $extUpdate['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $getExtData = getData(Table::QUERY_EXTENSION_DATES, [
            QUERY_EXTENSION_DATES::EXTENSION_DAYS,
            QUERY_EXTENSION_DATES::EXTENTION_END_DATE,
            QUERY_EXTENSION_DATES::ACTIVE
        ], [
            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_EXTENSION_DATES::ACTIVE => 1,
            QUERY_EXTENSION_DATES::QUERY_ID => $query_id,
            QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED => 1,
            QUERY_EXTENSION_DATES::ID => $ext_id
        ]);
        $ext_days = $getExtData[0][QUERY_EXTENSION_DATES::EXTENSION_DAYS];
        $ext_end_date = $getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE];

        $qupdate = updateData(Table::QUERY_DATA, [
            QUERY_DATA::IF_EXTENSION_GRANTED => 1,
            QUERY_DATA::EXTENSION_DAYS => $ext_days,
            QUERY_DATA::EXTENTION_END_DATE_TO_REPLY => $ext_end_date
        ], [
            QUERY_DATA::ID => $query_id,
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$qupdate['res']) {
            logError("Unabled to Approve Query Table Extension Date & Days, Query ID: " . $query_id . ", Extension ID: " . $ext_id, $qupdate['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Approved Successfully";
        sendRes();
        break;
    case 'REJECT_QUERY_EXTENSION':
        $query_id = $ajax_form_data['qid'];
        $ext_id = $ajax_form_data['eid'];
        $extUpdate = updateData(Table::QUERY_EXTENSION_DATES, [
            QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED => 2,
            QUERY_EXTENSION_DATES::UPDATED_AT => getToday()
        ], [
            QUERY_EXTENSION_DATES::QUERY_ID => $query_id,
            QUERY_EXTENSION_DATES::ID => $ext_id,
            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$extUpdate['res']) {
            logError("Unabled to Reject Query Extension Date, Query ID: " . $query_id . ", Extension ID: " . $ext_id, $extUpdate['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error']=false;
        $response['message']="Rejected Successfully !";
        sendRes();
        break;
    case 'SAVE_POSITION_PAPER_DATA':
        $cid = $ajax_form_data['cid'];
        $qid = isset($ajax_form_data['qid']) ? $ajax_form_data['qid'] : [];
        $date_of_issue = altRealEscape($ajax_form_data['doi']);
        $date_of_submit = altRealEscape($ajax_form_data['psd']);
        $ref_no = altRealEscape($ajax_form_data['ref']);
        if (
            ($cid == 0) || ($cid == null) ||
            ($date_of_issue == "") || ($date_of_issue == null) || (count($qid)==0) ||
            ($date_of_submit == "") || ($date_of_submit == null) || ($ref_no=="")
        ) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $saveMainPositionData = setData(Table::POSITION_PAPERS,[
            POSITION_PAPERS::CLIENT_ID=>$_SESSION[CLIENT_ID],
            POSITION_PAPERS::COMPANY_ID => $cid,
            POSITION_PAPERS::DATE_OF_ISSUE => $date_of_issue,
            POSITION_PAPERS::INITIAL_SUBMISSION_DATE => $date_of_submit,
            POSITION_PAPERS::REFERENCE_NO=>$ref_no,
            POSITION_PAPERS::OPEN_CLOSE_STATUS=>1,
            POSITION_PAPERS::CREATED_AT=>getToday(),
            POSITION_PAPERS::UPDATED_AT=>getToday()
        ]);
        if(!$saveMainPositionData['res']){
            logError("Unabled to save main position paper data, Company: ".$cid.", Auditor: ".$_SESSION[RID], $saveMainPositionData['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $pos_id = $saveMainPositionData['id'];
        foreach ($qid as $qdk => $qdv) {
            $queryId = $qdv;
            $save = setData(Table::POSITION_PAPER_DATA, [
                POSITION_PAPER_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                POSITION_PAPER_DATA::COMPANY_ID => $cid,
                POSITION_PAPER_DATA::QUERY_ID => $queryId,
                POSITION_PAPER_DATA::POSITION_PAPER_ID => $pos_id,
                POSITION_PAPER_DATA::USER_ID => $_SESSION[RID],
                POSITION_PAPER_DATA::DATE_OF_ISSUE => $date_of_issue,
                POSITION_PAPER_DATA::INITIAL_SUBMISSION_DATE => $date_of_submit,
                POSITION_PAPER_DATA::ACTIVE => 1,
                POSITION_PAPER_DATA::CREATED_AT => getToday(),
                POSITION_PAPER_DATA::UPDATED_AT => getToday()
            ]);
            if (!$save['res']) {
                logError("Unabled to insert position paper data, company_id: " . $cid . ", date_of_issue: " . $date_of_issue, $save['error']);
                $response['message'] = ERROR_1;
                sendRes();
            }
        }
        $response['error'] = false;
        $response['message'] = "Inserted Successfully!";
        sendRes();
        break;
    case 'GET_POSITION_PAPER_TABLE':
        $company_id = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $table = "";
        $assIssueArr=[];
        $sl = 1;
        if (($company_id == 0) || ($company_id == null)) {
            $response['message'] = "Unabled to fetch data due to no company found!";
            sendRes();
        }
        $audit_closed=$company_inactive=false;
        $getAuditActiveStatus=getData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE],[
            AUDITS_DATA::COMPANY_ID=>$company_id,
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDITS_DATA::STATUS=>ACTIVE_STATUS
        ]);
        $getComActiveStat=getData(Table::COMPANIES,[COMPANIES::ACTIVE],[COMPANIES::ID=>$company_id]);
        if (count($getComActiveStat)>0) {
            if($getComActiveStat[0][COMPANIES::ACTIVE]==0){
                $company_inactive=true;
            } else {
                $company_inactive=false;
            }
        }
        if(count($getAuditActiveStatus)>0){
            if($getAuditActiveStatus[0][AUDITS_DATA::ACTIVE]==2){
                $audit_closed=true;
            }
        }
        $getPositionData = getData(Table::POSITION_PAPERS, ['*'], [
            POSITION_PAPERS::CLIENT_ID => $_SESSION[CLIENT_ID],
            POSITION_PAPERS::COMPANY_ID => $company_id
        ]);
        if (count($getPositionData) > 0) {
            foreach ($getPositionData as $k => $v) {
                $assessmentIssued=false;
                $asmtRef='';
                $getAssessIssData=getData(Table::AUDIT_ASSESSMENT_DATA,[
                    AUDIT_ASSESSMENT_DATA::ACTIVE,
                    AUDIT_ASSESSMENT_DATA::REF_NO
                ],[
                    AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID=>$v[POSITION_PAPERS::ID]
                ]);
                if (count($getAssessIssData)>0) {
                    // $assessmentIssued=true;
                    $asmtRef=$getAssessIssData[0][AUDIT_ASSESSMENT_DATA::REF_NO];
                }
                if($asmtRef!=""){
                    $assessmentIssued=true;
                } else {
                    $assessmentIssued=false;
                }
                $assIssueArr[]=$assessmentIssued;
                $date_of_reply = $reply_extension = $reply_status = '<span class="badge badge-light"><small>'.EMPTY_VALUE.'</small></span>';
                // $reply_extension = $reply_status = '';
                $getExtData = getData(Table::POSITION_PAPER_EXTENTION_DATES, [
                    POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED,
                    POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE,
                    POSITION_PAPER_EXTENTION_DATES::EXTENSION_DAYS,
                    POSITION_PAPER_EXTENTION_DATES::ACTIVE,
                    POSITION_PAPER_EXTENTION_DATES::ID
                ], [
                    POSITION_PAPER_EXTENTION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
                    POSITION_PAPER_EXTENTION_DATES::ACTIVE => 1,
                    POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID => $v[POSITION_PAPERS::ID]
                ]);
                $last_date_reply = ($v[POSITION_PAPERS::INITIAL_SUBMISSION_DATE] != "") ? $v[POSITION_PAPERS::INITIAL_SUBMISSION_DATE] : EMPTY_VALUE;
                if (count($getExtData) > 0) {
                    if (
                        ($getExtData[0][POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED] == 1) &&
                        ($getExtData[0][POSITION_PAPER_EXTENTION_DATES::ACTIVE] == 1)
                    ) {
                        if (getToday(false) <= getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                            $reply_extension = '<span class="badge badge-light"><small>' . $getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENSION_DAYS] . ' Days (<b>Due Date:</b> ' . getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                            $last_date_reply = $getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE];
                        } else {
                            $ldd = (getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE], 'd') + 1);
                            $ldr = getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE], 'Y-m-' . $ldd);
                            // $reply_extension = ((!$audit_closed) && (!$company_inactive)) ? ((!$assessmentIssued)?'<span class="badge badge-primary" style="cursor:pointer;" onclick="getExtAreaPositionPaper(' . $v[POSITION_PAPERS::ID] . ', , \'' . $getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE] . '\');"><small><i class="fas fa-plus"></i>Add Ext.</small></span>':'<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>'):'';
                            $reply_extension = ((!$audit_closed) && (!$company_inactive) && ($v[POSITION_PAPERS::DATE_OF_REPLY] != "")) ? ((!$assessmentIssued)?'<span class="badge badge-primary" style="cursor:pointer;" onclick="getExtAreaPositionPaper(' . $v[POSITION_PAPERS::ID] . ', , \'' . $getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE] . '\');"><small><i class="fas fa-plus"></i>Add Ext.</small></span>':$reply_extension):$reply_extension;
                            if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                                $reply_extension = '<span class="badge badge-white"><small><span class="text-danger">Ext. Exceeded !</span> (<b>Due Date:</b> ' . getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                                
                            }
                            if($assessmentIssued){
                                // $reply_extension.='<br>&nbsp;<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>';
                            }
                        }
                    }
                    if (
                        ($getExtData[0][POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED] == 0) &&
                        ($getExtData[0][POSITION_PAPER_EXTENTION_DATES::ACTIVE] == 1)
                    ) {
                        if (getToday(false) <= getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                            $reply_extension = '<span class="badge badge-white"><small> <span class="text-danger">Awating for Approval !</span> (<b>Req. Ext. Date: </b> ' . getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                            // $reply_extension .= ($assessmentIssued)?'<br>&nbsp;<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>':'';
                            if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                                // $reply_extension = (!$audit_closed) && (!$company_inactive) ? ((!$assessmentIssued)?'<span class="badge badge-primary" style="cursor:pointer;" onclick="AddPositionPaperExtApproval(' . $getExtData[0][POSITION_PAPER_EXTENTION_DATES::ID] . ', ' . $v[POSITION_PAPERS::ID] . ');"><small><i class="fas fa-plus"></i>&nbsp;Add Approval.</small></span>':'<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>'):'';
                                $reply_extension = (!$audit_closed) && (!$company_inactive) && ($v[POSITION_PAPERS::DATE_OF_REPLY] != "") ? ((!$assessmentIssued)?'<span class="badge badge-primary" style="cursor:pointer;" onclick="AddPositionPaperExtApproval(' . $getExtData[0][POSITION_PAPER_EXTENTION_DATES::ID] . ', ' . $v[POSITION_PAPERS::ID] . ');"><small><i class="fas fa-plus"></i>&nbsp;Add Approval.</small></span>':$reply_extension):$reply_extension;
                                $reply_extension .= '&nbsp;<small> <span class="text-dark">(<b>Req. Date: </b> ' . getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]) . ')</small>';
                            }
                        } else {
                            $ldd = (getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE], 'd') + 1);
                            $ldr = getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE], 'Y-m-' . $ldd);
                            $reply_extension = (!$audit_closed) && (!$company_inactive) && ($v[POSITION_PAPERS::DATE_OF_REPLY] != "") ? ((!$assessmentIssued)?'<span class="badge badge-primary" style="cursor:pointer;" onclick="AddQueryExt(' . $v[POSITION_PAPERS::ID] . ', \'' . $getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE] . '\');"><small><i class="fas fa-plus"></i>Add Ext.</small></span>':$reply_extension):$reply_extension;
                            if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                                $reply_extension = '<span class="badge badge-white"><small><span class="text-danger">Ext. Exceeded !</span> (<b>Due Date:</b> ' . getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]) . ')</small></span>';
                                // $reply_extension .= ($assessmentIssued)?'<br>&nbsp;<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>':'';
                            }
                        }
                    }
                } else {
                    $ldd = (getFormattedDateTime($v[POSITION_PAPERS::INITIAL_SUBMISSION_DATE], 'd') + 1);
                    $ldr = getFormattedDateTime($v[POSITION_PAPERS::INITIAL_SUBMISSION_DATE], 'Y-m-' . $ldd);
                    // $reply_extension = (!$audit_closed) && (!$company_inactive) ? ((!$assessmentIssued)?'<span class="badge badge-primary" style="cursor:pointer;" onclick="getExtAreaPositionPaper(' . $v[POSITION_PAPERS::ID] . ', \'' . $v[POSITION_PAPERS::INITIAL_SUBMISSION_DATE] . '\');"><small><i class="fas fa-plus"></i>Add Ext.</small></span>':'<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>'):'';
                    $reply_extension = (!$audit_closed) && (!$company_inactive) && ($v[POSITION_PAPERS::DATE_OF_REPLY] != "") ? ((!$assessmentIssued)?'<span class="badge badge-primary" style="cursor:pointer;" onclick="getExtAreaPositionPaper(' . $v[POSITION_PAPERS::ID] . ', \'' . $v[POSITION_PAPERS::INITIAL_SUBMISSION_DATE] . '\');"><small><i class="fas fa-plus"></i>Add Ext.</small></span>':$reply_extension):$reply_extension;
                    if ($_SESSION[USER_TYPE] != EMPLOYEE) {
                        $reply_extension = '<span class="badge badge-info"><small>No Ext. Request</small></span>';
                        
                    }
                    // $reply_extension .= ($assessmentIssued)?'<br>&nbsp;<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>':'';
                }
                //getting reply status
                if ($v[POSITION_PAPERS::DATE_OF_REPLY] != "") {
                    $reply_status = '<span class="badge badge-success"><small>Submitted</small></span>';
                } else {
                    $reply_status = (getToday(false) > $v[POSITION_PAPERS::INITIAL_SUBMISSION_DATE]) ? '<span class="badge badge-danger"><small>Overdue</small></span>' : '<span class="badge badge-light"><small>'.EMPTY_VALUE.'</small></span>';
                    if ($last_date_reply != EMPTY_VALUE) {
                        $reply_status = (getToday(false) > $last_date_reply) ? '<span class="badge badge-danger"><small>Overdue</small></span>' : '<span class="badge badge-light"><small>'.EMPTY_VALUE.'</small></span>';
                    }
                    $reply_status .= (($_SESSION[USER_TYPE] == EMPLOYEE) && (!$audit_closed) && (!$company_inactive)) ? ((!$assessmentIssued)?'&nbsp;<span class="badge badge-primary cursor-pointer" onclick="AddPositionPaperReply(' . $v[POSITION_PAPERS::ID] . ');"><small><i class="fas fa-plus"></i>Add Rep.</small></span>':'<br>&nbsp;<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>') : '';
                }
                //getting date of reply
                if ($v[POSITION_PAPERS::DATE_OF_REPLY] != "") {
                    $date_of_reply = getFormattedDateTime($v[POSITION_PAPERS::DATE_OF_REPLY]);
                }
                // if ($v[POSITION_PAPERS::DATE_OF_REPLY] != "") {
                //     $reply_extension = '<span class="badge badge-white"><small> <span class="text-success">Already Submitted !</span></small></span>';
                // }
                $days = getDateDiff($v[POSITION_PAPERS::DATE_OF_ISSUE], (($last_date_reply != EMPTY_VALUE) ? $last_date_reply : ""));
                // $response['issue_date'] = getFormattedDateTime($v[POSITION_PAPERS::DATE_OF_ISSUE],'Y-m-d');
                // $response['last_date_reply'] = getFormattedDateTime($last_date_reply,'m');
                $getQids = getData(Table::POSITION_PAPER_DATA,[POSITION_PAPER_DATA::QUERY_ID],[POSITION_PAPER_DATA::POSITION_PAPER_ID=>$v[POSITION_PAPERS::ID]]);
                $qnames = [];
                foreach ($getQids as $pqidk => $pqidv) {
                    $getQueryNo = getData(Table::QUERY_DATA, [QUERY_DATA::QUERY_NO], [QUERY_DATA::ID => $pqidv[POSITION_PAPER_DATA::QUERY_ID]]);
                    $qnames[]=$getQueryNo[0][QUERY_DATA::QUERY_NO];
                }
                $query_details = (count($qnames) > 0) ? implode(', ',$qnames) : EMPTY_VALUE;
                if($assessmentIssued){
                    $reply_status.='<br>&nbsp;<span class="badge alert-danger"><small>Assessment Issued #'.$asmtRef.'</small></span>';
                }
                $table .= '
                <tr>
                    <td>' . $sl . '</td>
                    <td>' . $v[POSITION_PAPERS::REFERENCE_NO] . '</td>
                    <td>' . $query_details . '</td>
                    <td>' . getFormattedDateTime($v[POSITION_PAPERS::DATE_OF_ISSUE]) . '</td>
                    <td>' . getFormattedDateTime($last_date_reply) . '</td>
                    <td>' . $days . '</td>
                    <td>' . $date_of_reply . '</td>
                    <td>' . $reply_extension . '</td>
                    <td>' . $reply_status . '</td>
                </tr>
                ';
                $sl++;
            }
        } else {
            $table = '
            <tr>
                <td colspan="9">
                    <div class="alert alert-danger" role="alert">
                        No Position Paper found !
                    </div>
                </td>
            </tr>
            ';
        }
        $response['error'] = false;
        $response['table'] = $table;
        $response['audit_closed'] = $audit_closed;
        $response['company_inactive'] = $company_inactive;
        $response['assIssueArr'] = $assIssueArr;
        sendRes();
        break;
    case 'SAVE_POSITION_PAPER_EXTENSION_DATE_AUDITOR':
        $position_id = (isset($ajax_form_data['pid'])) ? $ajax_form_data['pid'] : 0;
        $extension_start_date = (isset($ajax_form_data['stdate'])) ? altRealEscape($ajax_form_data['stdate']) : "";
        $extension_end_date = (isset($ajax_form_data['enddate'])) ? altRealEscape($ajax_form_data['enddate']) : "";
        $extension_days = (isset($ajax_form_data['extdays'])) ? altRealEscape($ajax_form_data['extdays']) : "";

        if (($position_id == 0) || ($extension_start_date == "") || ($extension_days == "") || ($extension_end_date == "")) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $getExtData = getData(Table::POSITION_PAPER_EXTENTION_DATES, [
            POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED,
            POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE,
            POSITION_PAPER_EXTENTION_DATES::ACTIVE
        ], [
            POSITION_PAPER_EXTENTION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
            POSITION_PAPER_EXTENTION_DATES::ACTIVE => 1,
            POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID => $position_id
        ]);
        if (count($getExtData) > 0) {
            if (
                ($getExtData[0][POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED] == 1) &&
                ($getExtData[0][POSITION_PAPER_EXTENTION_DATES::ACTIVE] == 1)
            ) {
                if (getToday(false) <= getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                    $response['message'] = "An extension already Active !";
                    sendRes();
                }
            }
            if (
                ($getExtData[0][POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED] == 0) &&
                ($getExtData[0][POSITION_PAPER_EXTENTION_DATES::ACTIVE] == 1)
            ) {
                if (getToday(false) <= getFormattedDateTime($getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE], 'Y-m-d')) {
                    $response['message'] = "An extension request already raised !";
                    sendRes();
                }
            }
            $update = updateData(Table::POSITION_PAPER_EXTENTION_DATES, [
                POSITION_PAPER_EXTENTION_DATES::ACTIVE => 0,
                POSITION_PAPER_EXTENTION_DATES::UPDATED_AT => getToday(),
            ], [
                POSITION_PAPER_EXTENTION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
                POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID => $position_id
            ]);
            if (!$update['res']) {
                logError("Unabled to inactive all the previous extionsions before requesting new extension [Position Paper], Position id: " . $position_id, $update['error']);
                $response['message'] = ERROR_1;
                sendRes();
            }
        }
        $save = setData(Table::POSITION_PAPER_EXTENTION_DATES, [
            POSITION_PAPER_EXTENTION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
            POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID => $position_id,
            POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED => 0,
            POSITION_PAPER_EXTENTION_DATES::EXTENSION_DAYS => $extension_days,
            POSITION_PAPER_EXTENTION_DATES::EXTENTION_START_DATE => $extension_start_date,
            POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE => $extension_end_date,
            POSITION_PAPER_EXTENTION_DATES::ACTIVE => 1,
            POSITION_PAPER_EXTENTION_DATES::CREATED_AT => getToday(),
            POSITION_PAPER_EXTENTION_DATES::UPDATED_AT => getToday()
        ]);
        if (!$save['res']) {
            logError("Unabled to raise new extension request for position paper id: " . $position_id, $save['error']);
            $response['message'] = ERROR_3;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "New Extension request placed successfully !";
        sendRes();
        break;
    case 'APPROVE_POSITION_EXTENSION':
        $position_id = $ajax_form_data['pid'];
        $ext_id = $ajax_form_data['peid'];

        $extUpdate = updateData(Table::POSITION_PAPER_EXTENTION_DATES, [
            POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED => 1,
            POSITION_PAPER_EXTENTION_DATES::UPDATED_AT => getToday()
        ], [
            POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID => $position_id,
            POSITION_PAPER_EXTENTION_DATES::ID => $ext_id,
            POSITION_PAPER_EXTENTION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$extUpdate['res']) {
            logError("Unabled to Approve Position Paper Extension Date, Position Paper ID: " . $position_id . ", Extension ID: " . $ext_id, $extUpdate['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $getExtData = getData(Table::POSITION_PAPER_EXTENTION_DATES, [
            POSITION_PAPER_EXTENTION_DATES::EXTENSION_DAYS,
            POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE,
            POSITION_PAPER_EXTENTION_DATES::ACTIVE
        ], [
            POSITION_PAPER_EXTENTION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
            POSITION_PAPER_EXTENTION_DATES::ACTIVE => 1,
            POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID => $position_id,
            POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED => 1,
            POSITION_PAPER_EXTENTION_DATES::ID => $ext_id
        ]);
        $ext_days = $getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENSION_DAYS];
        $ext_end_date = $getExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE];

        $qupdate = updateData(Table::POSITION_PAPERS, [
            POSITION_PAPERS::IF_EXTENSION_GRANTED => 1,
            POSITION_PAPERS::EXTENSION_DAYS => $ext_days,
            POSITION_PAPERS::EXTENTION_END_DATE_TO_REPLY => $ext_end_date
        ], [
            POSITION_PAPERS::ID => $position_id,
            POSITION_PAPERS::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$qupdate['res']) {
            logError("Unabled to Approve Position Paper Table Extension Date & Days, Position Paper ID: " . $position_id . ", Extension ID: " . $ext_id, $qupdate['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Approved Successfully";
        sendRes();
        break;
    case 'SAVE_ASSESSMENT_ISSUE':
        $company_id = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $query_id = isset($ajax_form_data['qid']) ? $ajax_form_data['qid'] : 0;
        $date_of_issue = isset($ajax_form_data['doi']) ? altRealEscape($ajax_form_data['doi']) : "";
        $ref_no = isset($ajax_form_data['ref']) ? altRealEscape($ajax_form_data['ref']) : "";
        $amount = isset($ajax_form_data['tca']) ? altRealEscape($ajax_form_data['tca']) : 0;
        $penalty_amount = isset($ajax_form_data['pca']) ? altRealEscape($ajax_form_data['pca']) : 0;
        $omitted_income_amount = isset($ajax_form_data['oi']) ? altRealEscape($ajax_form_data['oi']) : 0;
        if (
            ($company_id == 0) ||
            ($date_of_issue == "") ||
            ($ref_no == "") ||
            ($query_id == 0) ||
            ($amount == 0) ||
            ($omitted_income_amount == 0) ||
            ($penalty_amount == 0)
        ) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $position_paper_id = $query_id;
        $getAssessData = getData(Table::AUDIT_ASSESSMENT_DATA, [
            AUDIT_ASSESSMENT_DATA::ACTIVE,
            AUDIT_ASSESSMENT_DATA::DATE_OF_CLOSURE
        ], [
            AUDIT_ASSESSMENT_DATA::COMPANY_ID => $company_id,
            AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID => $position_paper_id,
            AUDIT_ASSESSMENT_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (count($getAssessData) > 0) {
            $response['message'] = "Assessment Report already registered !";
            sendRes();
        }
        $save = setData(Table::AUDIT_ASSESSMENT_DATA, [
            AUDIT_ASSESSMENT_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_ASSESSMENT_DATA::COMPANY_ID => $company_id,
            AUDIT_ASSESSMENT_DATA::USER_ID => $_SESSION[RID],
            AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID => $position_paper_id,
            AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT => $amount,
            AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT => $penalty_amount,
            AUDIT_ASSESSMENT_DATA::OMITTED_INCOME_AMOUNT => $omitted_income_amount,
            AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE => $date_of_issue,
            AUDIT_ASSESSMENT_DATA::REF_NO => $ref_no,
            AUDIT_ASSESSMENT_DATA::ACTIVE => 1,
            AUDIT_ASSESSMENT_DATA::CREATED_AT => getToday(),
            AUDIT_ASSESSMENT_DATA::UPDATED_AT => getToday()
        ]);
        if (!$save['res']) {
            logError("Unabled to insert new assessment row, ref no.: " . $ref_no . ", company id: " . $company_id, $save['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        // $assessId = $save['id'];
        // $getQueryIds = 
        // $assessSave = 
        $response['error'] = false;
        $response['message'] = "Assessment Issued successfully !";
        sendRes();
        break;
    case 'GET_ASSESSMENT_TABLE_DATA':
        $company_id = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $table_tr = '';
        if ($company_id == 0) {
            $response['message'] = "Company not found!";
            sendRes();
        }
        $audit_closed=$company_inactive=false;
        $getAuditActiveStatus=getData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE],[
            AUDITS_DATA::COMPANY_ID=>$company_id,
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDITS_DATA::STATUS=>ACTIVE_STATUS
        ]);
        $getComActiveStat=getData(Table::COMPANIES,[COMPANIES::ACTIVE],[COMPANIES::ID=>$company_id]);
        if (count($getComActiveStat)>0) {
            if($getComActiveStat[0][COMPANIES::ACTIVE]==0){
                $company_inactive=true;
            } else {
                $company_inactive=false;
            }
        }
        if(count($getAuditActiveStatus)>0){
            if($getAuditActiveStatus[0][AUDITS_DATA::ACTIVE]==2){
                $audit_closed=true;
            }
        }
        $getAssessData = getData(Table::AUDIT_ASSESSMENT_DATA, ['*'], [
            AUDIT_ASSESSMENT_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_ASSESSMENT_DATA::COMPANY_ID => $company_id
        ]);
        $queryNos=$queryUnderPos=[];
        $status = $checked = $closure = $checkDisabled = $query_no = $claimAmt = $penaltyAmt = $omitted_income_amount = $date_count = $posRefNo='';
        if (count($getAssessData) > 0) {
            $sl = 1;
            foreach ($getAssessData as $k => $v) {
                $getPdata=getData(Table::POSITION_PAPER_DATA,[POSITION_PAPER_DATA::QUERY_ID],[POSITION_PAPER_DATA::POSITION_PAPER_ID=>$v[AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID]]);
                $getPosData=getData(Table::POSITION_PAPERS,[
                    POSITION_PAPERS::REFERENCE_NO,
                    POSITION_PAPERS::DATE_OF_ISSUE,
                    POSITION_PAPERS::OPEN_CLOSE_STATUS
                ],[
                    POSITION_PAPERS::ID=>$v[AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID]
                ]);
                if (count($getPosData)>0) {
                    $posRefNo='<b>'.$getPosData[0][POSITION_PAPERS::REFERENCE_NO].'</b> <small>(Issued on: '.getFormattedDateTime($getPosData[0][POSITION_PAPERS::DATE_OF_ISSUE]).')</small>';
                    // $posRefNo.=($getPosData[0][POSITION_PAPERS::OPEN_CLOSE_STATUS]==1)?'&nbsp;<span class="badge alert-success">Open</span>':'&nbsp;<span class="badge alert-danger">Closed</span>';

                }
                if(count($getPdata)>0){
                    foreach ($getPdata as $pqidsk => $pqidsv) {
                        $queryUnderPos[]=$pqidsv[POSITION_PAPER_DATA::QUERY_ID];
                    }
                }
                $getQueryData = getData(Table::QUERY_DATA, [QUERY_DATA::QUERY_NO], [QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]],[QUERY_DATA::ID=>$queryUnderPos]);
                // $query_no = (count($getQueryData) > 0) ? altRealEscape($getQueryData[0][QUERY_DATA::QUERY_NO]) : EMPTY_VALUE;
                if (count($getQueryData)>0) {
                    foreach ($getQueryData as $qupk => $qupv) {
                        $queryNos[]=$qupv[QUERY_DATA::QUERY_NO];
                    }
                    $query_no=(count($queryNos)>0)?implode(", ",$queryNos):EMPTY_VALUE;
                }
                $claimAmt = ($v[AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT] != 0) ? moneyFormatIndia($v[AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT]) : DEFAULT_AMOUNT;
                $penaltyAmt = ($v[AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT] != 0) ? moneyFormatIndia($v[AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT]) : DEFAULT_AMOUNT;
                $omitted_income_amount = ($v[AUDIT_ASSESSMENT_DATA::OMITTED_INCOME_AMOUNT] != 0) ? moneyFormatIndia($v[AUDIT_ASSESSMENT_DATA::OMITTED_INCOME_AMOUNT]) : DEFAULT_AMOUNT;
                switch ($v[AUDIT_ASSESSMENT_DATA::ACTIVE]) {
                    case 0:
                        $status = '<span class="badge badge-success">Close</span>';
                        $checked = '';
                        $checkDisabled = 'disabled';
                        $closure = getFormattedDateTime($v[AUDIT_ASSESSMENT_DATA::DATE_OF_CLOSURE]);
                        break;
                    case 1:
                        $status = '<span class="badge badge-danger">Open</span>';
                        $checked = 'checked';
                        $closure = '<span class="badge badge-warning">Not yet closed</span>';
                        $checkDisabled = '';
                        break;
                    case 2:
                        $status = '<span class="badge badge-danger">Open</span>';
                        $checked = 'checked';
                        $closure = getFormattedDateTime($v[AUDIT_ASSESSMENT_DATA::DATE_OF_CLOSURE]);
                        $checkDisabled = '';
                        break;
                }
                switch ($_SESSION[USER_TYPE]) {
                    case EMPLOYEE:
                        $action = '
                        <!--<div class="custom-control custom-switch noselect" style="cursor:pointer;">
                            <input type="checkbox" ' . $checked . ' ' . $checkDisabled . ' class="custom-control-input" id="assessment_active_' . $v[AUDIT_ASSESSMENT_DATA::ID] . '" onclick="makeAssessmentActive(' . $v[AUDIT_ASSESSMENT_DATA::ID] . ');" style="cursor:pointer;" />
                            <label class="custom-control-label text-success" for="assessment_active_' . $v[AUDIT_ASSESSMENT_DATA::ID] . '"></label>
                        </div>-->
                        <select class="form-control-sm" '.((($company_inactive) || ($audit_closed))?"disabled":"").' id="assessment_active_' . $v[AUDIT_ASSESSMENT_DATA::ID] . '" onchange="changeAssessmentStatus(' . $v[AUDIT_ASSESSMENT_DATA::ID] . ');">
                            <option value="0" ' . (($v[AUDIT_ASSESSMENT_DATA::ACTIVE] == 0) ? "selected" : "") . ' disabled>---Select Status----</option>
                            <option value="1"' . (($v[AUDIT_ASSESSMENT_DATA::ACTIVE] == 1) ? "selected" : "") . '>Collection stage</option>
                            <option value="2"' . (($v[AUDIT_ASSESSMENT_DATA::ACTIVE] == 2) ? "selected" : "") . '>Objection stage</option>
                        </select>
                        ';
                        break;
                    case SADMIN:
                    case ADMIN:
                        $status_text = $status_color = '';
                        switch ($v[AUDIT_ASSESSMENT_DATA::ACTIVE]) {
                            case 0:
                                $status_text = EMPTY_VALUE;
                                $status_color = 'white';
                                break;
                            case 1:
                                $status_text = 'Collection stage';
                                $status_color = 'success';
                                break;
                            case 2:
                                $status_text = 'Objection stage';
                                $status_color = 'danger';
                                break;
                        }
                        $action = '
                        <span class="badge badge-' . $status_color . '">' . $status_text . '</span>
                        ';
                        break;
                }
                $date_count = (getDateDiff($v[AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE], getToday(false)) == "") ? "0 Days" : getDateDiff($v[AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE], getToday(false));
                $table_tr .= '
                <tr>
                    <td>' . $sl . '</td>
                    <td>' . $v[AUDIT_ASSESSMENT_DATA::REF_NO] . '</td>
                    <td>' . $posRefNo . '</td>
                    <td>' . $query_no . '</td>
                    <td>' . $claimAmt . '</td>
                    <td>' . $penaltyAmt . '</td>
                    <td>' . $omitted_income_amount . '</td>
                    <td>' . getFormattedDateTime($v[AUDIT_ASSESSMENT_DATA::DATE_OF_ISSUE]) . '</td>
                    <td>' . $date_count . '</td>
                    <td>' . $action . '</td>
                </tr>
                ';
                $sl++;
            }
        } else {
            $table_tr = '<tr>
                <td colspan="10">
                    <div class="alert alert-danger" role="alert">
                        No Assessment Report found !
                    </div>
                </td>
            </tr>';
        }
        $response['error'] = false;
        $response['message'] = "Fetched Successfully!";
        $response['table_tr'] = $table_tr;
        $response['date_count'] = $date_count;
        $response['audit_closed'] = $audit_closed;
        $response['company_inactive'] = $company_inactive;
        sendRes();
        break;
    case 'MAKE_ASSESSMENT_OPEN_CLOSE':
        $aid = $ajax_form_data['aid'];
        $active = $ajax_form_data['act'];
        if ($aid == 0 || $aid == "") {
            $response['message'] = "No Assessment ID found!";
            sendRes();
        }
        $update = updateData(Table::AUDIT_ASSESSMENT_DATA, [
            AUDIT_ASSESSMENT_DATA::ACTIVE => $active,
            AUDIT_ASSESSMENT_DATA::DATE_OF_CLOSURE => getToday(),
            AUDIT_ASSESSMENT_DATA::UPDATED_AT => getToday()
        ], [
            AUDIT_ASSESSMENT_DATA::ID => $aid,
            AUDIT_ASSESSMENT_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$update['res']) {
            logError("Unabled to update open-close status on Assessment data, id: " . $aid, $update['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Success!";
        sendRes();
        break;
    case 'MAKE_COMPANY_ACTIVE_INACTIVE':
        $cid = $ajax_form_data['cid'];
        $active = $ajax_form_data['act'];
        if ($cid == 0 || $cid == "") {
            $response['message'] = "No Company ID found!";
            sendRes();
        }
        $checkActivity = getData(Table::COMPANY_ASSIGNED_DATA, [COMPANY_ASSIGNED_DATA::COMPANY_IDS], [
            COMPANY_ASSIGNED_DATA::COMPANY_IDS => $cid,
            COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS,
            COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        // if ((count($checkActivity)>0) && ($active==0)) {
        //     $response['message']="Company cannot be inactive as it has already been assigned to auditors.";
        //     sendRes();
        // }
        $update = updateData(Table::COMPANIES, [
            COMPANIES::ACTIVE => $active,
            COMPANIES::ACTIVE_INACTIVE_DATE => getToday(false),
            COMPANIES::UPDATED_AT => getToday()
        ], [
            COMPANIES::ID => $cid,
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$update['res']) {
            logError("Unabled to update active-inactive status on Company data, id: " . $cid, $update['error']);
            $response['message'] = ERROR_1;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Success!";
        sendRes();
        break;
    case 'GET_TAX_AUDIT_TYPE_FOR_QUERY':
        $company_id = $ajax_form_data['cid'];
        $audt = $taxt = $audtOpt = $taxtOpt = $audtDisabled = $taxtDisabled = '';
        $count = false;
        $getAuditTaxTypeHistory = getData(Table::AUDIT_TAX_TYPE_HISTORY, [
            AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID,
            AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID
        ], [
            AUDIT_TAX_TYPE_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_TAX_TYPE_HISTORY::COMPANY_ID => $company_id,
            AUDIT_TAX_TYPE_HISTORY::ACTIVE => 1
        ]);
        if (count($getAuditTaxTypeHistory) > 0) {
            $count = true;
            foreach ($getAuditTaxTypeHistory as $k => $v) {
                if ($v[AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID] != "") {
                    $getAudtName = getData(Table::AUDIT_TYPES, [AUDIT_TYPES::AUDIT_TYPE], [
                        AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID],
                        AUDIT_TYPES::ID => $v[AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID],
                        AUDIT_TYPES::STATUS => ACTIVE_STATUS
                    ]);
                    $audtDisabled = 'disabled';
                    $audtName = (count($getAudtName) > 0) ? altRealEscape($getAudtName[0][AUDIT_TYPES::AUDIT_TYPE]) : EMPTY_VALUE;
                    $audtOpt = '<option selected value="' . $v[AUDIT_TAX_TYPE_HISTORY::AUDIT_TYPE_ID] . '">' . $audtName . '</option>';
                } else {
                    $getAuditTypes = getData(Table::AUDIT_TYPES, [
                        AUDIT_TYPES::ID,
                        AUDIT_TYPES::AUDIT_TYPE
                    ], [
                        AUDIT_TYPES::STATUS => ACTIVE_STATUS,
                        AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID]
                    ]);
                    if (count($getAuditTypes) > 0) {
                        foreach ($getAuditTypes as $atk => $atv) {
                            $audtDisabled = '';
                            $audtOpt .= '<option value="' . $atv[AUDIT_TYPES::ID] . '">' . $atv[AUDIT_TYPES::AUDIT_TYPE] . '</option>';
                        }
                    }
                }
                if ($v[AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID] != "") {
                    $taxtids = explode(',', $v[AUDIT_TAX_TYPE_HISTORY::TYPE_OF_TAX_ID]);
                    $getTaxtName = getData(Table::TYPE_OF_TAX, [TYPE_OF_TAX::TYPE_OF_TAX, TYPE_OF_TAX::ID], [
                        TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
                        TYPE_OF_TAX::STATUS => ACTIVE_STATUS
                    ], [
                        TYPE_OF_TAX::ID => $taxtids
                    ]);
                    if (count($taxtids) > 1) {
                        foreach ($getTaxtName as $tk => $tv) {
                            $taxtOpt .= '<option value="' . $tv[TYPE_OF_TAX::ID] . '">' . altRealEscape($tv[TYPE_OF_TAX::TYPE_OF_TAX]) . '</option>';
                        }
                    } else {
                        $taxtDisabled = 'disabled';

                        $taxtOpt = '<option selected value="' . $getTaxtName[0][AUDIT_TAX_TYPE_HISTORY::ID] . '">' . altRealEscape($getTaxtName[0][TYPE_OF_TAX::TYPE_OF_TAX]) . '</option>';
                    }
                } else {
                    $getTaxTypes = getData(Table::TYPE_OF_TAX, [
                        TYPE_OF_TAX::TYPE_OF_TAX,
                        TYPE_OF_TAX::ID
                    ], [
                        TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
                        TYPE_OF_TAX::STATUS => ACTIVE_STATUS
                    ]);
                    if (count($getTaxTypes) > 0) {
                        $taxtDisabled = '';
                        foreach ($getTaxTypes as $ttk => $ttv) {
                            $taxtOpt .= '<option value="' . $ttv[TYPE_OF_TAX::ID] . '">' . $ttv[TYPE_OF_TAX::TYPE_OF_TAX] . '</option>';
                        }
                    }
                }
            }

            $audt = '
            <label class="form_label" for="audit_type_id">Audit Type</label>' . getAsterics() . '
            <select id="audit_type_id" class="form-control" ' . $audtDisabled . '>
            ' . $audtOpt . '
            ';
            $taxt = '
            <label class="form_label" for="type_of_tax_id">Tax Type</label>' . getAsterics() . '
            <select id="type_of_tax_id" class="form-control" ' . $taxtDisabled . '>
            ' . $taxtOpt . '
            ';
            $audt .= '
            </select>
            ';
            $taxt .= '
            </select>
            ';
        } else {
            $getAuditTypes = getData(Table::AUDIT_TYPES, [
                AUDIT_TYPES::ID,
                AUDIT_TYPES::AUDIT_TYPE
            ], [
                AUDIT_TYPES::STATUS => ACTIVE_STATUS,
                AUDIT_TYPES::CLIENT_ID => $_SESSION[CLIENT_ID]
            ]);
            if (count($getAuditTypes) > 0) {
                foreach ($getAuditTypes as $atk => $atv) {
                    $audtDisabled = '';
                    $audtOpt .= '<option value="' . $atv[AUDIT_TYPES::ID] . '">' . $atv[AUDIT_TYPES::AUDIT_TYPE] . '</option>';
                }
            }
            $getTaxTypes = getData(Table::TYPE_OF_TAX, [
                TYPE_OF_TAX::TYPE_OF_TAX,
                TYPE_OF_TAX::ID
            ], [
                TYPE_OF_TAX::CLIENT_ID => $_SESSION[CLIENT_ID],
                TYPE_OF_TAX::STATUS => ACTIVE_STATUS
            ]);
            if (count($getTaxTypes) > 0) {
                $taxtDisabled = '';
                foreach ($getTaxTypes as $ttk => $ttv) {
                    $taxtOpt .= '<option value="' . $ttv[TYPE_OF_TAX::ID] . '">' . $ttv[TYPE_OF_TAX::TYPE_OF_TAX] . '</option>';
                }
            }
            $audt = '
            <label class="form_label" for="audit_type_id">Audit Type</label>' . getAsterics() . '
            <select id="audit_type_id" class="form-control" ' . $audtDisabled . '>
            ' . $audtOpt . '
            ';
            $taxt = '
            <label class="form_label" for="type_of_tax_id">Tax Type</label>' . getAsterics() . '
            <select id="type_of_tax_id" class="form-control" ' . $taxtDisabled . '>
            ' . $taxtOpt . '
            ';
            $audt .= '
            </select>
            ';
            $taxt .= '
            </select>
            ';
        }
        $response['error'] = false;
        $response['audt'] = $audt;
        $response['taxt'] = $taxt;
        $response['count'] = $count;
        sendRes();
        break;
    case 'GET_QUERIES_FOR_POSITION_PAPER':
        $company_id = $ajax_form_data['cid'];
        $queries = '
        <label class="form_label" for="position_section_query_select">Select Query</label>
        <select class="form-control multiple" name="position_section_query_select[]" multiple="" id="position_section_query_select" onchange="getQueriesForForceClose();">
        <option value="0" disabled>Select Query</option>
        ';
        $getQueries = getData(Table::QUERY_DATA, [
            QUERY_DATA::ID,
            QUERY_DATA::QUERY_NO,
            QUERY_DATA::LAST_DATE_OF_REPLY
        ], [
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_DATA::COMPANY_ID => $company_id
        ]);
        if (count($getQueries) > 0) {
            foreach ($getQueries as $k => $v) {
                $getQExtData=getData(Table::QUERY_EXTENSION_DATES,[
                    QUERY_EXTENSION_DATES::EXTENTION_END_DATE
                ],[
                    QUERY_EXTENSION_DATES::QUERY_ID=>$v[QUERY_DATA::ID],
                    QUERY_EXTENSION_DATES::ACTIVE=>1,
                    QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED=>1
                ]);
                if(count($getQExtData)>0){
                    if($getQExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]<getToday(false)){
                        $checkIssuedQs = getData(Table::POSITION_PAPER_DATA, [
                            POSITION_PAPER_DATA::ACTIVE
                        ], [
                            POSITION_PAPER_DATA::QUERY_ID => $v[QUERY_DATA::ID],
                            POSITION_PAPER_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                        ]);
                        if (count($checkIssuedQs) == 0) {
                            $queries .= '<option value="' . $v[QUERY_DATA::ID] . '">' . $v[QUERY_DATA::QUERY_NO] . '</option>';
                        }
                    }
                }else{
                    if($v[QUERY_DATA::LAST_DATE_OF_REPLY]<getToday(false)){
                        $checkIssuedQs = getData(Table::POSITION_PAPER_DATA, [
                            POSITION_PAPER_DATA::ACTIVE
                        ], [
                            POSITION_PAPER_DATA::QUERY_ID => $v[QUERY_DATA::ID],
                            POSITION_PAPER_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                        ]);
                        if (count($checkIssuedQs) == 0) {
                            $queries .= '<option value="' . $v[QUERY_DATA::ID] . '">' . $v[QUERY_DATA::QUERY_NO] . '</option>';
                        }
                    }  
                }
            }
        }
        $queries .= '
        </select>
        ';
        $response['error'] = false;
        $response['queries'] = $queries;
        sendRes();
        break;
    case 'GET_QUERIES_FOR_ASSESSMENT':
        $company_id = $ajax_form_data['cid'];
        $queries = '
        <label class="form_label" for="assessment_section_query_select">Select Position Paper</label>
        <select class="form-control" id="assessment_section_query_select" onchange="getQueriesForForceCloseAssessment();">
            <option value="0" selected disabled>Select Position Paper</option>
        ';
        $getQueries = getData(Table::QUERY_DATA, [
            QUERY_DATA::ID,
            QUERY_DATA::QUERY_NO
        ], [
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            QUERY_DATA::COMPANY_ID => $company_id
        ]);
        if (count($getQueries) > 0) {
            $prefNos=[];
            foreach ($getQueries as $k => $v) {
                $getPdata = getData(Table::POSITION_PAPERS,[
                    POSITION_PAPERS::ID,
                    POSITION_PAPERS::INITIAL_SUBMISSION_DATE
                ],[
                    POSITION_PAPERS::COMPANY_ID=>$company_id,
                    POSITION_PAPERS::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ]);
                if (count($getPdata)>0) {
                    $getPPExtData=getData(Table::POSITION_PAPER_EXTENTION_DATES,[
                        POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE
                    ],[
                        POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID=>$getPdata[0][POSITION_PAPERS::ID],
                        POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED=>1,
                        POSITION_PAPER_EXTENTION_DATES::ACTIVE=>1
                    ]);
                    if(count($getPPExtData)>0){
                        if($getPPExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]<getToday(false)){
                            $foundPositionIds=[];
                            foreach ($getPdata as $ispd => $ispv) {
                                $foundPositionIds[]=$ispv[POSITION_PAPERS::ID];
                            }
                            $checkIssuedQs = getData(Table::POSITION_PAPER_DATA, [
                                POSITION_PAPER_DATA::ACTIVE,
                                POSITION_PAPER_DATA::DATE_OF_ISSUE,
                                POSITION_PAPER_DATA::INITIAL_SUBMISSION_DATE,
                                POSITION_PAPER_DATA::POSITION_PAPER_ID,
                                POSITION_PAPER_DATA::QUERY_ID
                            ], [
                                POSITION_PAPER_DATA::QUERY_ID => $v[QUERY_DATA::ID],
                                POSITION_PAPER_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                            ],[
                                POSITION_PAPER_DATA::POSITION_PAPER_ID => $foundPositionIds
                            ]);
                            // if ((count($checkIssuedQs) > 0) && ($checkIssuedQs[0][POSITION_PAPER_DATA::ACTIVE] == 0)) {
                            $issuedQids=[];
                            if ((count($checkIssuedQs) > 0)) {
                                foreach ($checkIssuedQs as $cqidk => $cqidv) {
                                    $issuedQids[]=$cqidv[POSITION_PAPER_DATA::QUERY_ID];
                                }
                            }
                            if (in_array($v[QUERY_DATA::ID],$issuedQids)) {
                                // echo "QUERY_DATA: ".$v[QUERY_DATA::ID];
                                // echo "<br>";
                                // exit();
                                $getPosId=getData(Table::POSITION_PAPER_DATA,[POSITION_PAPER_DATA::POSITION_PAPER_ID],[POSITION_PAPER_DATA::QUERY_ID=>$v[QUERY_DATA::ID]]);
                                $getPref = getData(Table::POSITION_PAPERS,[POSITION_PAPERS::ID,POSITION_PAPERS::REFERENCE_NO,POSITION_PAPERS::DATE_OF_ISSUE],[POSITION_PAPERS::ID=>$getPosId[0][POSITION_PAPER_DATA::POSITION_PAPER_ID]]);
                                $getIssuedAssessData=getData(Table::AUDIT_ASSESSMENT_DATA,[AUDIT_ASSESSMENT_DATA::ACTIVE],[AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID=>$getPref[0][POSITION_PAPERS::ID]]);
                                if (count($getIssuedAssessData)==0) {
                                    $qname = $getPref[0][POSITION_PAPERS::REFERENCE_NO].' [ Issued: '.$getPref[0][POSITION_PAPERS::DATE_OF_ISSUE].' ]';
                                    $qname .= ($checkIssuedQs[0][POSITION_PAPER_DATA::ACTIVE] == 0)?'&nbsp;&nbsp;[Closed]':'&nbsp;&nbsp;[Open]';
                                    if (count($prefNos)>0) {
                                        if (!in_array($getPref[0][POSITION_PAPERS::REFERENCE_NO],$prefNos)) {
                                            $prefNos[] = $getPref[0][POSITION_PAPERS::REFERENCE_NO];
                                            $queries .= '<option value="' . $getPref[0][POSITION_PAPERS::ID] . '">' . $qname . '</option>';
                                        }
                                    } else {
                                        $prefNos[] = $getPref[0][POSITION_PAPERS::REFERENCE_NO];
                                        $queries .= '<option value="' . $getPref[0][POSITION_PAPERS::ID] . '">' . $qname . '</option>';
                                    }
                                }
                            }
                        }
                    }else{
                        if($getPdata[0][POSITION_PAPERS::INITIAL_SUBMISSION_DATE]<getToday(false)){
                            $foundPositionIds=[];
                            foreach ($getPdata as $ispd => $ispv) {
                                $foundPositionIds[]=$ispv[POSITION_PAPERS::ID];
                            }
                            $checkIssuedQs = getData(Table::POSITION_PAPER_DATA, [
                                POSITION_PAPER_DATA::ACTIVE,
                                POSITION_PAPER_DATA::DATE_OF_ISSUE,
                                POSITION_PAPER_DATA::INITIAL_SUBMISSION_DATE,
                                POSITION_PAPER_DATA::POSITION_PAPER_ID,
                                POSITION_PAPER_DATA::QUERY_ID
                            ], [
                                POSITION_PAPER_DATA::QUERY_ID => $v[QUERY_DATA::ID],
                                POSITION_PAPER_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                            ],[
                                POSITION_PAPER_DATA::POSITION_PAPER_ID => $foundPositionIds
                            ]);
                            // if ((count($checkIssuedQs) > 0) && ($checkIssuedQs[0][POSITION_PAPER_DATA::ACTIVE] == 0)) {
                            $issuedQids=[];
                            if ((count($checkIssuedQs) > 0)) {
                                foreach ($checkIssuedQs as $cqidk => $cqidv) {
                                    $issuedQids[]=$cqidv[POSITION_PAPER_DATA::QUERY_ID];
                                }
                            }
                            if (in_array($v[QUERY_DATA::ID],$issuedQids)) {
                                // echo "QUERY_DATA: ".$v[QUERY_DATA::ID];
                                // echo "<br>";
                                // exit();
                                $getPosId=getData(Table::POSITION_PAPER_DATA,[POSITION_PAPER_DATA::POSITION_PAPER_ID],[POSITION_PAPER_DATA::QUERY_ID=>$v[QUERY_DATA::ID]]);
                                $getPref = getData(Table::POSITION_PAPERS,[POSITION_PAPERS::ID,POSITION_PAPERS::REFERENCE_NO,POSITION_PAPERS::DATE_OF_ISSUE],[POSITION_PAPERS::ID=>$getPosId[0][POSITION_PAPER_DATA::POSITION_PAPER_ID]]);
                                $getIssuedAssessData=getData(Table::AUDIT_ASSESSMENT_DATA,[AUDIT_ASSESSMENT_DATA::ACTIVE],[AUDIT_ASSESSMENT_DATA::POSITION_PAPER_ID=>$getPref[0][POSITION_PAPERS::ID]]);
                                if (count($getIssuedAssessData)==0) {
                                    $qname = $getPref[0][POSITION_PAPERS::REFERENCE_NO].' [ Issued: '.$getPref[0][POSITION_PAPERS::DATE_OF_ISSUE].' ]';
                                    $qname .= ($checkIssuedQs[0][POSITION_PAPER_DATA::ACTIVE] == 0)?'&nbsp;&nbsp;[Closed]':'&nbsp;&nbsp;[Open]';
                                    if (count($prefNos)>0) {
                                        if (!in_array($getPref[0][POSITION_PAPERS::REFERENCE_NO],$prefNos)) {
                                            $prefNos[] = $getPref[0][POSITION_PAPERS::REFERENCE_NO];
                                            $queries .= '<option value="' . $getPref[0][POSITION_PAPERS::ID] . '">' . $qname . '</option>';
                                        }
                                    } else {
                                        $prefNos[] = $getPref[0][POSITION_PAPERS::REFERENCE_NO];
                                        $queries .= '<option value="' . $getPref[0][POSITION_PAPERS::ID] . '">' . $qname . '</option>';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $queries .= '
        </select>
        ';
        $response['error'] = false;
        $response['queries'] = $queries;
        $response['prefNos'] = $prefNos;
        sendRes();
        break;
    case 'CHECK_FORCE_CLOSE_QUERIES_FOR_POSITION':
        $qids = isset($ajax_form_data['qid']) ? $ajax_form_data['qid'] : [];
        // $query_id = $ajax_form_data['qid'];
        $qhtml = '';
        $haveDays = $count = false;
        if (count($qids)>0) {
            foreach ($qids as $qidk => $qidv) {
                $query_id = $qidv;
                $getQueryData = getData(Table::QUERY_DATA, ['*'], [
                    QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                    QUERY_DATA::ID => $query_id
                ]);
                $qdata = $getQueryData[0];
                if (
                    ($qdata[QUERY_DATA::QUERY_STATUS] != 2) &&
                    ($qdata[QUERY_DATA::QUERY_STATUS] != 4)
                ) {
                    $count = true;
                    if ($qdata[QUERY_DATA::LAST_DATE_OF_REPLY] >= getToday(false)) {
                        $haveDays = true;
                        $reply_date = getFormattedDateTime($qdata[QUERY_DATA::LAST_DATE_OF_REPLY]);
                        $checkExtDate = getData(Table::QUERY_EXTENSION_DATES, ['*'], [
                            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
                            QUERY_EXTENSION_DATES::QUERY_ID => $query_id,
                            QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED => 1,
                            QUERY_EXTENSION_DATES::ACTIVE => 1,
                        ]);
                        if (count($checkExtDate)) {
                            if ($checkExtDate[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE] >= getToday(false)) {
                                $reply_date = getFormattedDateTime($checkExtDate[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]);
                            }
                        }
                        $qhtml = '
                        <fieldset class="fldset">
                            <legend>Force Close Query</legend>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <p>Query No.: ' . $qdata[QUERY_DATA::QUERY_NO] . ' is <span class="badge badge-danger">Open</span> & still has days in hand to reply.</p>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge badge-white"><small><b>Due Date:</b> ' . $reply_date . '</small></span>
                                </div>
                            </div>
                        </fieldset>
                        ';
                        $response['count'] = $count;
                        $response['haveDays'] = $haveDays;
                        $response['qhtml'] = $qhtml;
                        sendRes();
                    } else {
                        $getExtDates = getData(Table::QUERY_EXTENSION_DATES, ['*'], [
                            QUERY_EXTENSION_DATES::CLIENT_ID => $_SESSION[CLIENT_ID],
                            QUERY_EXTENSION_DATES::QUERY_ID => $query_id,
                            QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED => 1,
                            QUERY_EXTENSION_DATES::ACTIVE => 1,
                        ]);
                        if (count($getExtDates)) {
                            if ($getExtDates[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE] >= getToday(false)) {
                                $haveDays = true;
                                $qhtml = '
                                <fieldset class="fldset">
                                    <legend>Force Close Query</legend>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <p>Query No.: <strong>' . $qdata[QUERY_DATA::QUERY_NO] . '</strong> is <span class="badge badge-danger">Open</span> & still has days in hand to reply.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="badge badge-white"><small><b>Due Date:</b> ' . getFormattedDateTime($getExtDates[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE]) . '</small></span>
                                        </div>
                                    </div>
                                </fieldset>
                                ';
                                $response['count'] = $count;
                                $response['haveDays'] = $haveDays;
                                $response['qhtml'] = $qhtml;
                                sendRes();
                            }
                        }
                        $haveDays = false;
                        $qhtml = '
                        <fieldset class="fldset">
                            <legend>Force Close Query</legend>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <p>Query No.: <b>' . $qdata[QUERY_DATA::QUERY_NO] . '</b> is <span class="badge badge-danger">Overdue</span> & still <span class="badge badge-success">Open</span>.</br> Please close the query before issuing position paper.</p>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-sm btn-danger" type="button" onclick="getForceCloseQuery(' . $query_id . ');"><i class="fas fa-ban"></i>&nbsp;Force Close Query</button>
                                </div>
                            </div>
                        </fieldset>
                        ';
                    }
                }
            }
        }
        
        $response['qhtml'] = $qhtml;
        $response['haveDays'] = $haveDays;
        $response['count'] = $count;
        $response['error'] = false;
        sendRes();
        break;
    case 'SAVE_QUERY_FORCE_CLOSE':
        $remarks = isset($ajax_form_data['rmks']) ? altRealEscape($ajax_form_data['rmks']) : "";
        $query_id = isset($ajax_form_data['qid']) ? $ajax_form_data['qid'] : 0;
        if ($query_id == 0) {
            $response['message'] = "No Query ID Found!";
            sendRes();
        }
        $cols = [
            QUERY_DATA::QUERY_STATUS => 4,
            QUERY_DATA::UPDATED_AT => getToday()
        ];
        if ($remarks != "") {
            $cols[QUERY_DATA::REMARKS] = $remarks;
        }
        $update = updateData(Table::QUERY_DATA, $cols, [
            QUERY_DATA::ID => $query_id,
            QUERY_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (!$update['res']) {
            logError("Unabled to force close query, query_id:" . $query_id, $update['error']);
            $response['message'] = ERROR_3;
            sendRes();
        }
        $response['error'] = false;
        $response['message'] = "Query has been closed successfully!";
        sendRes();
        break;
    case 'GET_ALL_REPLY_DATES_QUERY':
        $query_id = $ajax_form_data['qid'];
        $getAllReplyDt = getData(Table::QUERY_REPLY, [
            QUERY_REPLY::DATE_OF_REPLY,
            QUERY_REPLY::NO_OF_QUERY_SOLVED
        ], [
            QUERY_REPLY::QUERY_ID => $query_id,
            QUERY_REPLY::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        $html = '
        <fieldset class="fldset" style="padding: 15px !important;">
            <legend>Query Reply Dates</legend>
            <div class="row">
                <div class="col-md-6 text-center" style="border-right:solid 1px; border-bottom:solid 1px;">
                    <span style="font-weight:bold;" class="text-danger">Reply Date</span>
                </div>
                <div class="col-md-6 text-center" style="border-left:solid 1px; border-bottom:solid 1px;">
                    <span style="font-weight:bold;" class="text-danger">No. of query solved</span>
                </div>
            </div>
        ';
        foreach ($getAllReplyDt as $k => $v) {
            $html .= '
            <div class="row mt-2">
                <div class="col-md-6 text-center" style="border-right:solid 1px;">
                    <span class="text-dark">' . getFormattedDateTime($v[QUERY_REPLY::DATE_OF_REPLY]) . '</span>
                </div>
                <div class="col-md-6 text-center" style="border-left:solid 1px;">
                    <span class="text-dark">' . $v[QUERY_REPLY::NO_OF_QUERY_SOLVED] . '</span>
                </div>
            </div>
            ';
        }
        $html .= '
        </fieldset>';
        $response['error'] = false;
        $response['html'] = $html;
        sendRes();
        break;
    case 'SAVE_TAX_PAYMENT_DATA':
        $assess_id = isset($ajax_form_data['aid']) ? $ajax_form_data['aid'] : 0;
        $company_id = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $pay_date = isset($ajax_form_data['dt']) ? $ajax_form_data['dt'] : "";
        $amount = isset($ajax_form_data['amt']) ? $ajax_form_data['amt'] : 0;
        if (
            ($assess_id == 0) ||
            ($company_id == 0) ||
            ($amount == 0) ||
            ($pay_date == "")
        ) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $isUpdate = false;
        $pendingAmt = $penaltyPendingAmt = $paidAmt = $taxAmt = $penaltyAmt = 0;
        $getTaxAmt = getData(Table::AUDIT_ASSESSMENT_DATA, [AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT, AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT], [AUDIT_ASSESSMENT_DATA::ID => $assess_id]);
        $taxAmt = (count($getTaxAmt) > 0) ? $getTaxAmt[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT] : 0;
        $penaltyAmt = (count($getTaxAmt) > 0) ? $getTaxAmt[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT] : 0;
        $getPayData = getData(Table::TAX_COLLECTION_DATA, ['*'], [
            TAX_COLLECTION_DATA::COMPANY_ID => $company_id,
            TAX_COLLECTION_DATA::ASSESSMENT_ID => $assess_id,
            TAX_COLLECTION_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (count($getPayData) > 0) {
            $isUpdate = true;
            $tdata = $getPayData[0];
            $paidAmt = (($tdata[TAX_COLLECTION_DATA::PAID_AMOUNT]) + $amount);
            $pendingAmt = (($tdata[TAX_COLLECTION_DATA::TAX_AMOUNT]) - $paidAmt);
            $penaltyPendingAmt = $tdata[TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT];
            if (
                ($tdata[TAX_COLLECTION_DATA::PAID_AMOUNT] != 0) &&
                ($tdata[TAX_COLLECTION_DATA::PENDING_AMOUNT] < $amount)
            ) {
                $response['message'] = "Please enter a valid amount";
                sendRes();
            }
            if ($tdata[TAX_COLLECTION_DATA::TAX_AMOUNT] < $paidAmt) {
                $response['message'] = "Please enter a valid amount";
                sendRes();
            }
            $updateTdata = updateData(Table::TAX_COLLECTION_DATA, [
                TAX_COLLECTION_DATA::LAST_PAYMENT_DATE => $pay_date,
                TAX_COLLECTION_DATA::PAYMENT_STATUS => (($pendingAmt == 0) && ($penaltyPendingAmt == 0)) ? 2 : 3,
                TAX_COLLECTION_DATA::PAID_AMOUNT => $paidAmt,
                TAX_COLLECTION_DATA::PENDING_AMOUNT => $pendingAmt,
                TAX_COLLECTION_DATA::UPDATED_AT => getToday()
            ], [
                TAX_COLLECTION_DATA::ASSESSMENT_ID => $assess_id,
                TAX_COLLECTION_DATA::COMPANY_ID => $company_id,
                TAX_COLLECTION_DATA::ID => $tdata[TAX_COLLECTION_DATA::ID],
                TAX_COLLECTION_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
            ]);
            if (!$updateTdata['res']) {
                logError("Unabled to update tax collection data, assessment id: " . $assess_id . ", Company id: " . $company_id . ", Tax collection id: " . $tdata[TAX_COLLECTION_DATA::ID], $updateTdata['error']);
                $response['message'] = ERROR_3;
                sendRes();
            }
            $saveTPhistory = setData(Table::TAX_PAYMENT_HISTORY, [
                TAX_PAYMENT_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
                TAX_PAYMENT_HISTORY::ASSESSMENT_ID => $assess_id,
                TAX_PAYMENT_HISTORY::TAX_COLLECTION_ID => $tdata[TAX_COLLECTION_DATA::ID],
                TAX_PAYMENT_HISTORY::PAYMENT_AMOUNT => $amount,
                TAX_PAYMENT_HISTORY::PAYMENT_DATE => $pay_date,
                TAX_PAYMENT_HISTORY::PAYMENT_TYPE => 1,
                TAX_PAYMENT_HISTORY::STATUS => ACTIVE_STATUS,
                TAX_PAYMENT_HISTORY::CREATED_AT => getToday(),
                TAX_PAYMENT_HISTORY::UPDATED_AT => getToday()
            ]);
            if (!$saveTPhistory['res']) {
                logError("Unabled to save tax payment history. Tax collection id: " . $tdata[TAX_COLLECTION_DATA::ID], $saveTPhistory['error']);
                $response['message'] = ERROR_2;
                sendRes();
            }
            $response['error'] = false;
            $response['message'] = "Payment updated successfully!";
            sendRes();
        } else {
            $paidAmt = $amount;
            $pendingAmt = ($taxAmt - $paidAmt);
            if ($taxAmt < $amount) {
                $response['message'] = "Please enter a valid amount";
                sendRes();
            }
            $saveTdata = setData(Table::TAX_COLLECTION_DATA, [
                TAX_COLLECTION_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                TAX_COLLECTION_DATA::COMPANY_ID => $company_id,
                TAX_COLLECTION_DATA::ASSESSMENT_ID => $assess_id,
                TAX_COLLECTION_DATA::TAX_AMOUNT => $taxAmt,
                TAX_COLLECTION_DATA::PAID_AMOUNT => $paidAmt,
                TAX_COLLECTION_DATA::PENDING_AMOUNT => $pendingAmt,
                TAX_COLLECTION_DATA::LAST_PAYMENT_DATE => $pay_date,
                TAX_COLLECTION_DATA::PENALTY_AMOUNT => $penaltyAmt,
                TAX_COLLECTION_DATA::PAYMENT_STATUS => ($taxAmt == $paidAmt) ? 2 : 3,
                TAX_COLLECTION_DATA::CREATED_AT => getToday(),
                TAX_COLLECTION_DATA::UPDATED_AT => getToday()
            ]);
            if (!$saveTdata['res']) {
                logError("Unabled to save tax collection data. Company id: " . $company_id . ", Assessment id: " . $assess_id, $saveTdata['error']);
                $response['message'] = ERROR_2;
                sendRes();
            }
            $tdataid = $saveTdata['id'];
            $getPenaltyPendingAmt = getData(Table::TAX_COLLECTION_DATA, [
                TAX_COLLECTION_DATA::PENDING_AMOUNT,
                TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT
            ], [
                TAX_COLLECTION_DATA::ID => $tdataid
            ]);
            $tdatastatues = 3;
            if (
                ($getPenaltyPendingAmt[0][TAX_COLLECTION_DATA::PENDING_AMOUNT] == 0) &&
                ($getPenaltyPendingAmt[0][TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT] == 0)
            ) {
                $tdatastatues = 2;
            }
            $updateTstatus = updateData(Table::TAX_COLLECTION_DATA, [
                TAX_COLLECTION_DATA::PAYMENT_STATUS => $tdatastatues
            ], [
                TAX_COLLECTION_DATA::ID => $tdataid
            ]);
            $saveTPhistory = setData(Table::TAX_PAYMENT_HISTORY, [
                TAX_PAYMENT_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
                TAX_PAYMENT_HISTORY::ASSESSMENT_ID => $assess_id,
                TAX_PAYMENT_HISTORY::TAX_COLLECTION_ID => $tdataid,
                TAX_PAYMENT_HISTORY::PAYMENT_AMOUNT => $amount,
                TAX_PAYMENT_HISTORY::PAYMENT_DATE => $pay_date,
                TAX_PAYMENT_HISTORY::PAYMENT_TYPE => 1,
                TAX_PAYMENT_HISTORY::STATUS => ACTIVE_STATUS,
                TAX_PAYMENT_HISTORY::CREATED_AT => getToday(),
                TAX_PAYMENT_HISTORY::UPDATED_AT => getToday()
            ]);
            if (!$saveTPhistory['res']) {
                logError("Unabled to save tax payment history. Tax collection id: " . $tdataid, $saveTPhistory['error']);
                $response['message'] = ERROR_2;
                sendRes();
            }
            $response['error'] = false;
            $response['message'] = "Payment recorded successfully!";
            sendRes();
        }
        break;
    case 'SAVE_PENALTY_PAYMENT_DATA':
        $assess_id = isset($ajax_form_data['aid']) ? $ajax_form_data['aid'] : 0;
        $company_id = isset($ajax_form_data['cid']) ? $ajax_form_data['cid'] : 0;
        $pay_date = isset($ajax_form_data['dt']) ? $ajax_form_data['dt'] : "";
        $amount = isset($ajax_form_data['amt']) ? $ajax_form_data['amt'] : 0;
        if (
            ($assess_id == 0) ||
            ($company_id == 0) ||
            ($amount == 0) ||
            ($pay_date == "")
        ) {
            $response['message'] = EMPTY_FIELD_ALERT;
            sendRes();
        }
        $isUpdate = false;
        $pendingAmt = $penaltyPendingAmt = $paidAmt = $penaltyPaidAmt = $taxAmt = $PenaltyAmt = 0;
        $getTaxAmt = getData(Table::AUDIT_ASSESSMENT_DATA, [AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT, AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT], [AUDIT_ASSESSMENT_DATA::ID => $assess_id]);
        $taxAmt = (count($getTaxAmt) > 0) ? $getTaxAmt[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT] : 0;
        $PenaltyAmt = (count($getTaxAmt) > 0) ? $getTaxAmt[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT] : 0;
        $getPayData = getData(Table::TAX_COLLECTION_DATA, ['*'], [
            TAX_COLLECTION_DATA::COMPANY_ID => $company_id,
            TAX_COLLECTION_DATA::ASSESSMENT_ID => $assess_id,
            TAX_COLLECTION_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
        ]);
        if (count($getPayData) > 0) {
            $isUpdate = true;
            $tdata = $getPayData[0];
            $paidAmt = (($tdata[TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT]) + $amount);
            $pendingAmt = (($tdata[TAX_COLLECTION_DATA::PENALTY_AMOUNT]) - $paidAmt);
            $penaltyPendingAmt = $tdata[TAX_COLLECTION_DATA::PENDING_AMOUNT];
            if (
                ($tdata[TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT] != 0) &&
                ($tdata[TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT] < $amount)
            ) {
                $response['message'] = "Please enter a valid amount";
                sendRes();
            }
            if ($tdata[TAX_COLLECTION_DATA::PENALTY_AMOUNT] < $paidAmt) {
                $response['message'] = "Please enter a valid amount";
                sendRes();
            }
            $updateTdata = updateData(Table::TAX_COLLECTION_DATA, [
                TAX_COLLECTION_DATA::PENALTY_LAST_PAYMENT_DATE => $pay_date,
                TAX_COLLECTION_DATA::PAYMENT_STATUS => (($pendingAmt == 0) && ($penaltyPendingAmt == 0)) ? 2 : 3,
                TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT => $paidAmt,
                TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT => $pendingAmt,
                TAX_COLLECTION_DATA::UPDATED_AT => getToday()
            ], [
                TAX_COLLECTION_DATA::ASSESSMENT_ID => $assess_id,
                TAX_COLLECTION_DATA::COMPANY_ID => $company_id,
                TAX_COLLECTION_DATA::ID => $tdata[TAX_COLLECTION_DATA::ID],
                TAX_COLLECTION_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
            ]);
            if (!$updateTdata['res']) {
                logError("Unabled to update penalty collection data, assessment id: " . $assess_id . ", Company id: " . $company_id . ", Tax collection id: " . $tdata[TAX_COLLECTION_DATA::ID], $updateTdata['error']);
                $response['message'] = ERROR_3;
                sendRes();
            }
            $saveTPhistory = setData(Table::TAX_PAYMENT_HISTORY, [
                TAX_PAYMENT_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
                TAX_PAYMENT_HISTORY::ASSESSMENT_ID => $assess_id,
                TAX_PAYMENT_HISTORY::TAX_COLLECTION_ID => $tdata[TAX_COLLECTION_DATA::ID],
                TAX_PAYMENT_HISTORY::PAYMENT_AMOUNT => $amount,
                TAX_PAYMENT_HISTORY::PAYMENT_DATE => $pay_date,
                TAX_PAYMENT_HISTORY::PAYMENT_TYPE => 2,
                TAX_PAYMENT_HISTORY::STATUS => ACTIVE_STATUS,
                TAX_PAYMENT_HISTORY::CREATED_AT => getToday(),
                TAX_PAYMENT_HISTORY::UPDATED_AT => getToday()
            ]);
            if (!$saveTPhistory['res']) {
                logError("Unabled to save penalty payment history. Tax collection id: " . $tdata[TAX_COLLECTION_DATA::ID], $saveTPhistory['error']);
                $response['message'] = ERROR_2;
                sendRes();
            }
            $response['error'] = false;
            $response['message'] = "Penalty payment updated successfully!";
            sendRes();
        } else {
            $paidAmt = $amount;
            $pendingAmt = ($taxAmt - $paidAmt);
            if ($taxAmt < $amount) {
                $response['message'] = "Please enter a valid amount";
                sendRes();
            }
            $saveTdata = setData(Table::TAX_COLLECTION_DATA, [
                TAX_COLLECTION_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                TAX_COLLECTION_DATA::COMPANY_ID => $company_id,
                TAX_COLLECTION_DATA::ASSESSMENT_ID => $assess_id,
                TAX_COLLECTION_DATA::TAX_AMOUNT => $PenaltyAmt,
                TAX_COLLECTION_DATA::PENALTY_AMOUNT => $taxAmt,
                TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT => $paidAmt,
                TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT => $pendingAmt,
                TAX_COLLECTION_DATA::PENALTY_LAST_PAYMENT_DATE => $pay_date,
                TAX_COLLECTION_DATA::PAYMENT_STATUS => ($taxAmt == $paidAmt) ? 2 : 3,
                TAX_COLLECTION_DATA::CREATED_AT => getToday(),
                TAX_COLLECTION_DATA::UPDATED_AT => getToday()
            ]);
            if (!$saveTdata['res']) {
                logError("Unabled to save Penalty collection data. Company id: " . $company_id . ", Assessment id: " . $assess_id, $saveTdata['error']);
                $response['message'] = ERROR_2;
                sendRes();
            }
            $tdataid = $saveTdata['id'];
            $getPenaltyPendingAmt = getData(Table::TAX_COLLECTION_DATA, [
                TAX_COLLECTION_DATA::PENDING_AMOUNT,
                TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT
            ], [
                TAX_COLLECTION_DATA::ID => $tdataid
            ]);
            $tdatastatues = 3;
            if (
                ($getPenaltyPendingAmt[0][TAX_COLLECTION_DATA::PENDING_AMOUNT] == 0) &&
                ($getPenaltyPendingAmt[0][TAX_COLLECTION_DATA::PENALTY_PENDING_AMOUNT] == 0)
            ) {
                $tdatastatues = 2;
            }
            $updateTstatus = updateData(Table::TAX_COLLECTION_DATA, [
                TAX_COLLECTION_DATA::PAYMENT_STATUS => $tdatastatues
            ], [
                TAX_COLLECTION_DATA::ID => $tdataid
            ]);
            $saveTPhistory = setData(Table::TAX_PAYMENT_HISTORY, [
                TAX_PAYMENT_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
                TAX_PAYMENT_HISTORY::ASSESSMENT_ID => $assess_id,
                TAX_PAYMENT_HISTORY::TAX_COLLECTION_ID => $tdataid,
                TAX_PAYMENT_HISTORY::PAYMENT_AMOUNT => $amount,
                TAX_PAYMENT_HISTORY::PAYMENT_DATE => $pay_date,
                TAX_PAYMENT_HISTORY::PAYMENT_TYPE => 2,
                TAX_PAYMENT_HISTORY::STATUS => ACTIVE_STATUS,
                TAX_PAYMENT_HISTORY::CREATED_AT => getToday(),
                TAX_PAYMENT_HISTORY::UPDATED_AT => getToday()
            ]);
            if (!$saveTPhistory['res']) {
                logError("Unabled to save penalty payment history. Tax collection id: " . $tdataid, $saveTPhistory['error']);
                $response['message'] = ERROR_2;
                sendRes();
            }
            $response['error'] = false;
            $response['message'] = "Payment recorded successfully!";
            sendRes();
        }
        break;
    case 'GET_TAX_PAYMENT_HISTORY':
        $taxid = isset($ajax_form_data['tid']) ? $ajax_form_data['tid'] : 0;
        $type = isset($ajax_form_data['type']) ? $ajax_form_data['type'] : 0;
        if (
            ($taxid == 0) ||
            ($type == 0)
        ) {
            $response['message'] = ERROR_1;
            logError("INPUT MISSING!", "Tax id or payment type found missing at showing payment history");
            sendRes();
        }
        $getPayData = getData(Table::TAX_PAYMENT_HISTORY, ['*'], [
            TAX_PAYMENT_HISTORY::TAX_COLLECTION_ID => $taxid,
            TAX_PAYMENT_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
            TAX_PAYMENT_HISTORY::PAYMENT_TYPE => $type
        ]);
        $html = '
        <fieldset class="fldset" style="padding: 15px !important;">
            <legend>Payment History</legend>
            <div class="row">
                <div class="col-md-6 text-center" style="border-right:solid 1px; border-bottom:solid 1px;">
                    <span style="font-weight:bold;" class="text-danger">Payment Date</span>
                </div>
                <div class="col-md-6 text-center" style="border-left:solid 1px; border-bottom:solid 1px;">
                    <span style="font-weight:bold;" class="text-danger">Payment Amount</span>
                </div>
            </div>
        ';
        foreach ($getPayData as $k => $v) {
            $html .= '
            <div class="row mt-2">
                <div class="col-md-6 text-center" style="border-right:solid 1px;">
                    <span class="text-dark">' . getFormattedDateTime($v[TAX_PAYMENT_HISTORY::PAYMENT_DATE]) . '</span>
                </div>
                <div class="col-md-6 text-center" style="border-left:solid 1px;">
                    <span class="text-dark">' . moneyFormatIndia($v[TAX_PAYMENT_HISTORY::PAYMENT_AMOUNT]) . '</span>
                </div>
            </div>
            ';
        }
        $html .= '
        </fieldset>';
        $response['error'] = false;
        $response['html'] = $html;
        sendRes();
        break;
    case 'GET_AUDIT_ASSIGNMENT_VIEW_DATA':
        $com_table_rows = "";
        $showName = [];

        $getCompanyData = getData(Table::COMPANIES, ['*'], [
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANIES::STATUS => ACTIVE_STATUS
        ]);

        if (count($getCompanyData) > 0) {

            foreach ($getCompanyData as $cdk => $cdv) {
                $c_industry_type = $c_audit_type = $c_tax_type = "";
                $primary_auditor = $secondary_auditors = '<span class="badge badge-info">Yet to be assigned</span>';

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
                $getAssignmentData = getData(Table::COMPANY_ASSIGNED_DATA, [
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID,
                    COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
                    COMPANY_ASSIGNED_DATA::STATUS
                ], [
                    COMPANY_ASSIGNED_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS => $cdv[COMPANIES::ID],
                    COMPANY_ASSIGNED_DATA::STATUS => ACTIVE_STATUS
                ]);
                if (count($getAssignmentData) > 0) {
                    $secAudIds = $secAudNames = [];
                    foreach ($getAssignmentData as $aad => $aav) {
                        if ($aav[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 1) {
                            $getpAudName = getData(Table::USERS, [USERS::NAME], [USERS::ID => $aav[COMPANY_ASSIGNED_DATA::AUDITOR_ID], USERS::ACTIVE => 1]);
                            $primary_auditor = (count($getpAudName) > 0) ? altRealEscape($getpAudName[0][USERS::NAME]) : EMPTY_VALUE;
                        }
                        if ($aav[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY] == 2) {
                            $secAudIds[] = $aav[COMPANY_ASSIGNED_DATA::AUDITOR_ID];
                        }
                    }
                    $getSecAudName = getData(Table::USERS, [USERS::NAME], [
                        USERS::CLIENT_ID => $_SESSION[CLIENT_ID],
                        USERS::ACTIVE => 1,
                        USERS::STATUS => ACTIVE_STATUS
                    ], [USERS::ID => $secAudIds]);
                    if (count($getSecAudName) > 0) {
                        foreach ($getSecAudName as $sank => $sanv) {
                            $secAudNames[] = $sanv[USERS::NAME];
                        }
                        $secondary_auditors = (count($secAudNames) > 0) ? implode(", ", $secAudNames) : '<span class="badge badge-warning"><small>Not Found!<small></span>';
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
                    <div class="cursor-pointer text-success ' . TOOLTIP_CLASS . '" title="Click to Reassign" onclick="editAuditorAssignment(' . $cdv[COMPANIES::ID] . ');"><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
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
        $response['error'] = false;
        $response['table'] = $com_table_rows;
        sendRes();
        break;
    case 'GET_COMPANY_DASH_COM_STAND_DATA':
        $company_id = isset($ajax_form_data['cid'])?$ajax_form_data['cid']:0;
        $tax_id = isset($ajax_form_data['ttid'])?$ajax_form_data['ttid']:0;
        $thtml=$auditAgingvalue=$noticeStatLabel=$noticeStatValue=[];
        $noticeStatLabel=$noticeStatValue=$queryStatLabel=$queryStatValue=$positionStatLabel=$positionStatValue=$assessmentStatLabel=$assessmentStatValue=[];
        $agingValueInMonths=$taxClaimed=$penaltyClaimed=$taxAch=$penaltyAch=$taxPercent=$penaltyPercent=0;
        $auditAgingLabel=[
            '0-3 Months',
            '3-6 Months',
            '6-9 Months',
            '9 Months - 1 Year',
            '1 Year - 2 Years',
            '> 2 Years'
        ];
        $qids=[];
        if (($company_id==0)||($tax_id==0)) {
            $response['message']="Company or Tax type not found!";
            sendRes();
        }
        $queryIds=getData(Table::QUERY_DATA,[
            QUERY_DATA::ID,
            QUERY_DATA::NO_OF_QUERY_SOLVED,
            QUERY_DATA::NO_OF_QUERY_UNSOLVED,
            QUERY_DATA::TOTAL_NO_OF_QUERY
        ],[
            QUERY_DATA::COMPANY_ID=>$company_id,
            QUERY_DATA::TAX_TYPE_ID=>$tax_id,
            QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        //generating chart data [start]
        $getAudData=getData(Table::AUDITS_DATA,[
            AUDITS_DATA::AUDIT_START_DATE,
            AUDITS_DATA::AUDIT_END_DATE,
            AUDITS_DATA::ACTIVE,
            AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE
        ],[
            AUDITS_DATA::COMPANY_ID=>$company_id,
            AUDITS_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDITS_DATA::STATUS=>ACTIVE_STATUS
        ]);
        $getTaxCollecData=getData(Table::TAX_COLLECTION_DATA,[
            TAX_COLLECTION_DATA::TAX_AMOUNT,
            TAX_COLLECTION_DATA::PENALTY_AMOUNT,
            TAX_COLLECTION_DATA::PAID_AMOUNT,
            TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT
        ],[
            TAX_COLLECTION_DATA::COMPANY_ID=>$company_id,
            TAX_COLLECTION_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if (count($getTaxCollecData)>0) {
            $taxClaimed=moneyFormatIndia($getTaxCollecData[0][TAX_COLLECTION_DATA::TAX_AMOUNT]);
            $penaltyClaimed=moneyFormatIndia($getTaxCollecData[0][TAX_COLLECTION_DATA::PENALTY_AMOUNT]);
            $taxAch=moneyFormatIndia($getTaxCollecData[0][TAX_COLLECTION_DATA::PAID_AMOUNT]);
            $penaltyAch=moneyFormatIndia($getTaxCollecData[0][TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT]);

            $taxPercent=($taxAch!=0)?($taxAch/$taxClaimed)*100:0;
            $penaltyPercent=($penaltyAch!=0)?($penaltyAch/$penaltyClaimed)*100:0;
        }
        if (count($getAudData)>0) {
            $aStartDate=$getAudData[0][AUDITS_DATA::AUDIT_START_DATE];
            $aEndDate=($getAudData[0][AUDITS_DATA::AUDIT_END_DATE]!=null)?$getAudData[0][AUDITS_DATA::AUDIT_END_DATE]:getToday(false);
            // $auditAgingLabel=getDateDiff($aStartDate,$aEndDate);
            $agingValueInMonths=(getMonthsDifference($aStartDate,$aEndDate)==0)?1:getMonthsDifference($aStartDate,$aEndDate);
            // echo "agingValueInMonths: ".$agingValueInMonths;
            foreach ($auditAgingLabel as $aagk => $aagv) {
                switch ($agingValueInMonths) {
                    case (($agingValueInMonths<=3)&&($agingValueInMonths>0)&&($aagk==0)):
                        $agValue=$agingValueInMonths;
                        break;
                    case (($agingValueInMonths<=6)&&($agingValueInMonths>3)&&($aagk==1)):
                        $agValue=$agingValueInMonths;
                        break;
                    case (($agingValueInMonths<=9)&&($agingValueInMonths>6)&&($aagk==2)):
                        $agValue=$agingValueInMonths;
                        break;
                    case (($agingValueInMonths<=12)&&($agingValueInMonths>9)&&($aagk==3)):
                        $agValue=$agingValueInMonths;
                        break;
                    case (($agingValueInMonths<=24)&&($agingValueInMonths>12)&&($aagk==4)):
                        $agValue=$agingValueInMonths;
                        break;
                    case (($agingValueInMonths>=24)&&($agingValueInMonths>0)&&($aagk==5)):
                        $agValue=$agingValueInMonths;
                        break;
                    default:
                        $agValue=0;
                        break;
                }
                $auditAgingvalue[$aagk]=$agValue;
            }
        }
        //generating chart data [end]
        if (count($queryIds)>0) {
            $tax_target_claimed=$tax_target_ach=$total_queries=$qSolved=$qUnsolved=0;
            $notice_issued=$notice_pending=$position_paper_issued=$position_paper_pending=$assessment_issued=$assessment_pending=0;
            $total_queries=count($queryIds);
            foreach ($queryIds as $qidk => $qidv) {
                $qids[]=$qidv[QUERY_DATA::ID];
                if ($qidv[QUERY_DATA::NO_OF_QUERY_SOLVED]!=null) {
                    $qSolved += $qidv[QUERY_DATA::NO_OF_QUERY_SOLVED];
                }
                if ($qidv[QUERY_DATA::TOTAL_NO_OF_QUERY]!=null) {
                    $total_queries += $qidv[QUERY_DATA::TOTAL_NO_OF_QUERY];
                }
            }
            $qUnsolved=($total_queries-$qSolved);
            $getAssessData=getData(Table::AUDIT_ASSESSMENT_DATA,['*'],[
                AUDIT_ASSESSMENT_DATA::COMPANY_ID=>$company_id,
                AUDIT_ASSESSMENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
            ],[
                AUDIT_ASSESSMENT_DATA::QUERY_ID=>$qids
            ]);
            if (count($getAssessData)>0) {
                $assessment_issued=count($getAssessData);
                foreach($getAssessData as $asdk=>$asdv){
                    $tax_target_claimed += $asdv[AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT];
                    $getTaxCollData=getData(Table::TAX_COLLECTION_DATA,[TAX_COLLECTION_DATA::PAID_AMOUNT,TAX_COLLECTION_DATA::PAYMENT_STATUS],[
                        TAX_COLLECTION_DATA::ASSESSMENT_ID=>$asdv[AUDIT_ASSESSMENT_DATA::ID]
                    ]);
                    if(count($getTaxCollData)>0){
                        $tax_target_ach+=$getTaxCollData[0][TAX_COLLECTION_DATA::PAID_AMOUNT];
                        if ($getTaxCollData[0][TAX_COLLECTION_DATA::PAYMENT_STATUS]==1) {
                            $assessment_pending++;
                        }
                    }
                }
            }
            $getNoticeData=getData(Table::COMPANY_NOTICE_DATA,[
                COMPANY_NOTICE_DATA::NOTICE_NO,
                COMPANY_NOTICE_DATA::QUERY_IDS,
                COMPANY_NOTICE_DATA::NOTICE_STATUS
            ],[
                COMPANY_NOTICE_DATA::COMPANY_ID=>$company_id,
                COMPANY_NOTICE_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
            ]);
            if(count($getNoticeData)>0){
                foreach ($getNoticeData as $nqdk => $nqdv) {
                    $noticeQids=array_unique(explode(",",$nqdv[COMPANY_NOTICE_DATA::QUERY_IDS]));
                    foreach ($qids as $ctqidk => $ctqidv) {
                        if (in_array($ctqidv,$noticeQids)) {
                            $notice_issued++;
                            if ($nqdv[COMPANY_NOTICE_DATA::NOTICE_STATUS]!=1) {
                                $notice_pending++;
                            }
                        }
                    }
                }
            }
            $getPositionPaperData=getData(Table::POSITION_PAPERS,[
                POSITION_PAPERS::OPEN_CLOSE_STATUS
            ],[
                POSITION_PAPERS::COMPANY_ID=>$company_id,
                POSITION_PAPERS::CLIENT_ID=>$_SESSION[CLIENT_ID]
            ]);
            if (count($getPositionPaperData)>0) {
                $position_paper_issued=count($getPositionPaperData);
                foreach($getPositionPaperData as $ppdk=>$ppdv){
                    if ($ppdv[POSITION_PAPERS::OPEN_CLOSE_STATUS]==1) {
                        $position_paper_pending++;
                    }
                }
            }
            $noticeStatLabel=['Notice Issued','Notice Pending'];
            $noticeStatValue=[$notice_issued,$notice_pending];
            $queryStatLabel=['Query Solved','Query Unsolved'];
            $queryStatValue=[$qSolved,$qUnsolved];
            $positionStatLabel=['Position Paper Issued','Position Paper Pending'];
            $positionStatValue=[$position_paper_issued,$position_paper_pending];
            $assessmentStatLabel=['Assessment Issued','Assessment Pending'];
            $assessmentStatValue=[$assessment_issued,$assessment_pending];
            $thtml='<tr>
                <td>'.moneyFormatIndia($tax_target_claimed).'</td>
                <td>'.moneyFormatIndia($tax_target_ach).'</td>
                <td>'.$total_queries.'</td>
                <td>'.$qSolved.'</td>
                <td>'.$qUnsolved.'</td>
                <td>'.$notice_issued.'</td>
                <td>'.$notice_pending.'</td>
                <td>'.$position_paper_issued.'</td>
                <td>'.$position_paper_pending.'</td>
                <td>'.$assessment_issued.'</td>
                <td>'.$assessment_pending.'</td>
            </tr>';
        } else {
            $response['message']="No Queries found on this Tax type!";
            $thtml='<tr>
                <td colspan="11">
                    <div class="alert alert-danger" role="alert">
                        No Data found !
                    </div>
                </td>
            </tr>';
        }
        $response['thtml']=$thtml;
        $response['auditAgingLabel']=$auditAgingLabel;
        $response['auditAgingvalue']=$auditAgingvalue;
        $response['agingValueInMonths']=$agingValueInMonths;
        $response['penaltyPercent']=$penaltyPercent;
        $response['taxPercent']=$taxPercent;
        $response['taxPenaltyStatsLabel']=['Tax Claimed','Tax Achieved','Penalty Claimed','Penalty Achieved'];
        $response['taxPenaltyStatsValue']=[$taxClaimed,$taxAch,$penaltyClaimed,$penaltyAch];
        $response['noticeStatLabel']=$noticeStatLabel;
        $response['noticeStatValue']=$noticeStatValue;
        $response['queryStatLabel']=$queryStatLabel;
        $response['queryStatValue']=$queryStatValue;
        $response['positionStatLabel']=$positionStatLabel;
        $response['positionStatValue']=$positionStatValue;
        $response['assessmentStatLabel']=$assessmentStatLabel;
        $response['assessmentStatValue']=$assessmentStatValue;
        sendRes();
        break;
    case 'GET_AUDIT_DASH_IND_WISE_TAX_REPORT':
        $ind_id=isset($ajax_form_data['ind_id'])?$ajax_form_data['ind_id']:0;
        $comIds=$qIds=[];
        $taxClaimed=$taxRecieved=$penaltyClaimed=$penaltyRecieved=$taxRecPer=$penaltyRecPer=0;
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
        $getComIds=getData(Table::COMPANIES,[COMPANIES::ID],[
            COMPANIES::INDUSTRY_TYPE_ID=>$ind_id,
            COMPANIES::CLIENT_ID=>$_SESSION[CLIENT_ID],
            COMPANIES::STATUS=>ACTIVE_STATUS
        ],($_SESSION[USER_TYPE]==EMPLOYEE)?[COMPANIES::ID=>$assignedCompanyIds]:[]);
        if(count($getComIds)>0){
            foreach($getComIds as $cidk=>$cidv){
                $comIds[]=$cidv[COMPANIES::ID];
            }
        }
        $getQids=getData(Table::QUERY_DATA,[
            QUERY_DATA::ID
        ],[
            QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ],[
            QUERY_DATA::COMPANY_ID=>$comIds
        ]);
        if(count($getQids)>0){
            foreach($getQids as $qidk=>$qidv){
                $qIds[]=$qidv[QUERY_DATA::ID];
            }
        }
        $getAssessmentData=getData(Table::AUDIT_ASSESSMENT_DATA,[
            AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT,
            AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT,
            AUDIT_ASSESSMENT_DATA::ACTIVE,
            AUDIT_ASSESSMENT_DATA::ID
        ],[
            AUDIT_ASSESSMENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ],[
            AUDIT_ASSESSMENT_DATA::COMPANY_ID=>$comIds,
            AUDIT_ASSESSMENT_DATA::QUERY_ID=>$qIds
        ]);
        if(count($getAssessmentData)>0){
            foreach($getAssessmentData as $assdk => $assdv){
                $taxClaimed+=$assdv[AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT];
                $penaltyClaimed+=$assdv[AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT];
                $getTaxCollectionData = getData(Table::TAX_COLLECTION_DATA,[
                    TAX_COLLECTION_DATA::PAID_AMOUNT,
                    TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT
                ],[
                    TAX_COLLECTION_DATA::ASSESSMENT_ID=>$assdv[AUDIT_ASSESSMENT_DATA::ID],
                    TAX_COLLECTION_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ]);
                if(count($getTaxCollectionData)>0){
                    $taxRecieved += $getTaxCollectionData[0][TAX_COLLECTION_DATA::PAID_AMOUNT];
                    $penaltyRecieved += $getTaxCollectionData[0][TAX_COLLECTION_DATA::PENALTY_PAID_AMOUNT];
                }
            }
        }
        $taxRecPer=($taxClaimed!=0)?(($taxRecieved/$taxClaimed)*100):0;
        $penaltyRecPer=($penaltyClaimed!=0)?(($penaltyRecieved/$penaltyClaimed)*100):0;
        $response['error']=false;
        $response['taxClaimed']=moneyFormatIndia($taxClaimed);
        $response['penaltyClaimed']=moneyFormatIndia($penaltyClaimed);
        $response['taxRecieved']=moneyFormatIndia($taxRecieved);
        $response['penaltyRecieved']=moneyFormatIndia($penaltyRecieved);
        $response['taxRecPer']=floor($taxRecPer);
        $response['penaltyRecPer']=floor($penaltyRecPer);
        sendRes();
        break;
    case 'GET_AUDIT_QUERY_STATUS_ON_AUDITOR_DASH':
        $ind_id=isset($ajax_form_data['ind'])?$ajax_form_data['ind']:0;
        $aud_id=isset($ajax_form_data['aud'])?$ajax_form_data['aud']:0;
        if (($aud_id==0)||($ind_id==0)) {
            $response['message']=EMPTY_FIELD_ALERT;
            sendRes();
        }
        $comIds=$qIds=[];
        $auditCompleted=$auditPending=$primary=$secondary=$auditsCount=$totalQuery=$qResolved=$qPending=0;
        $notice_issued=$notice_pending=$noticeCount=0;
        $audit_thtml=$query_thtml=$auditor=$notice_thtml='';
        $getAudName=getData(Table::USERS,[USERS::NAME],[USERS::ID=>$aud_id]);
        $auditor=(count($getAudName)>0)?altRealEscape($getAudName[0][USERS::NAME]):$auditor;
        $getComIds=getData(Table::COMPANIES,[COMPANIES::ID],[
            COMPANIES::INDUSTRY_TYPE_ID=>$ind_id,
            COMPANIES::CLIENT_ID=>$_SESSION[CLIENT_ID],
            COMPANIES::STATUS=>ACTIVE_STATUS
        ]);
        if(count($getComIds)>0){
            foreach($getComIds as $cidk=>$cidv){
                $comIds[]=$cidv[COMPANIES::ID];
            }
        }
        $getNoticeData=getData(Table::COMPANY_NOTICE_DATA,[
            COMPANY_NOTICE_DATA::NOTICE_NO,
            COMPANY_NOTICE_DATA::QUERY_IDS,
            COMPANY_NOTICE_DATA::NOTICE_STATUS
        ],[
            COMPANY_NOTICE_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ],[
            COMPANY_NOTICE_DATA::COMPANY_ID=>$comIds
        ]);
        $getAuditorsAssigned=getData(Table::COMPANY_ASSIGNED_DATA,[
            COMPANY_ASSIGNED_DATA::AUDITOR_ID,
            COMPANY_ASSIGNED_DATA::COMPANY_IDS,
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
            COMPANY_ASSIGNED_DATA::STATUS,
            COMPANY_ASSIGNED_DATA::ID
        ],[
            COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            COMPANY_ASSIGNED_DATA::AUDITOR_ID=>$aud_id
        ],[
            COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$comIds
        ]);
        $getQueryData=getData(Table::QUERY_DATA,[
            QUERY_DATA::QUERY_REPLY_IS_SUBMITTED,
            QUERY_DATA::QUERY_STATUS,
            QUERY_DATA::TOTAL_NO_OF_QUERY,
            QUERY_DATA::NO_OF_QUERY_SOLVED
        ],[
            QUERY_DATA::USER_ID=>$aud_id,
            QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ],[
            QUERY_DATA::COMPANY_ID=>$comIds
        ]);
        if(count($getNoticeData)>0){
            $noticeCount=count($getNoticeData);
            foreach ($getNoticeData as $nqdk => $nqdv) {
                $noticeQids=array_unique(explode(",",$nqdv[COMPANY_NOTICE_DATA::QUERY_IDS]));
                // foreach ($qids as $ctqidk => $ctqidv) {
                    // if (in_array($ctqidv,$noticeQids)) {
                        $notice_issued++;
                        if ($nqdv[COMPANY_NOTICE_DATA::NOTICE_STATUS]!=1) {
                            $notice_pending++;
                        }
                    // }
                // }
            }
            $notice_thtml='
            <tr>
                <td>'.$auditor.'</td>
                <td class="text-left"><strong>Total: </strong>'.($noticeCount).'</br><strong>Issued: </strong>'.$notice_issued.'&nbsp;<strong>Pending: </strong>'.$notice_pending.'</td>
            </tr>
            ';
        } else {
            $notice_thtml='
            <tr>
                <td colspan="2">
                    <div class="alert alert-danger" role="alert">
                        No Data Found !
                    </div>
                </td>
            </tr>
            ';
        }
        if (count($getQueryData)>0) {
            foreach($getQueryData as $qdk => $qdv){
                $totalQuery += $qdv[QUERY_DATA::TOTAL_NO_OF_QUERY];
                if ($qdv[QUERY_DATA::NO_OF_QUERY_SOLVED]!=null) {
                    $qResolved+= $qdv[QUERY_DATA::NO_OF_QUERY_SOLVED];
                }
            }
            $qPending=($qResolved==0)?$totalQuery:($totalQuery-$qResolved);
            $query_thtml='
            <tr>
                <td>'.$auditor.'</td>
                <td>'.$totalQuery.'</td>
            </tr>
            ';
        } else {
            $query_thtml='
            <tr>
                <td colspan="4">
                    <div class="alert alert-danger" role="alert">
                        No Data Found !
                    </div>
                </td>
            </tr>
            ';
        }
        if(count($getAuditorsAssigned)>0){
            $auditsCount=count($getAuditorsAssigned);
            foreach ($getAuditorsAssigned as $aadk => $aadv) {
                if ($aadv[COMPANY_ASSIGNED_DATA::STATUS]==ACTIVE_STATUS) {
                    if ($aadv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==1) {
                        $primary++;
                    }
                    if ($aadv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==2) {
                        $secondary++;
                    }
                    $auditData=getData(Table::AUDITS_DATA,['*'],[
                        AUDITS_DATA::COMPANY_ID=>$aadv[COMPANY_ASSIGNED_DATA::COMPANY_IDS],
                        AUDITS_DATA::USER_ID=>$aadv[COMPANY_ASSIGNED_DATA::AUDITOR_ID]
                    ]);
                    // rip($auditData);
                    if (count($auditData)>0) {
                        if($auditData[0][AUDITS_DATA::ACTIVE]==1){
                            $auditPending++;
                        }
                        if($auditData[0][AUDITS_DATA::ACTIVE]==2){
                            $auditCompleted++;
                        }
                    }
                }
            }
            $audit_thtml='
            <tr>
                <td>'.$auditor.'</td>
                <td class="text-left"><strong>Total: </strong>'.($primary+$secondary).'</br><strong>Primary: </strong>'.$primary.'&nbsp;<strong>Secondary: </strong>'.$secondary.'</td>
            </tr>
            ';
        } else {
            $audit_thtml='
            <tr>
                <td colspan="4">
                    <div class="alert alert-danger" role="alert">
                        No Data Found !
                    </div>
                </td>
            </tr>
            ';
        }
        $response['audit_thtml']=$audit_thtml;
        $response['query_thtml']=$query_thtml;
        $response['notice_thtml']=$notice_thtml;
        $response['auditPending']=(($auditPending==0)?$auditsCount:$auditPending);
        $response['auditCompleted']=$auditCompleted;
        $response['qPending']=$qPending;
        $response['qResolved']=$qResolved;
        $response['notice_issued']=$notice_issued;
        $response['notice_pending']=$notice_pending;
        sendRes();
        break;
    case 'GET_AUDITOR_TIME_SPENT_DATA':
        $audit_id = isset($ajax_form_data['aid'])?$ajax_form_data['aid']:0;
        if($audit_id==0){
            $response['message']="Audit not found!";
            sendRes();
        }
        $secAudNames=[];
        $primary_auditor_name=$secondary_auditor_name=EMPTY_VALUE;
        $primary_auditor_hour=$secondary_auditor_hour=0;

        $getAuditTimeData=getData(Table::AUDIT_TIME_SPENT_DATA,[
            AUDIT_TIME_SPENT_DATA::AUDITOR_ID,
            AUDIT_TIME_SPENT_DATA::DATE,
            AUDIT_TIME_SPENT_DATA::TIME_IN_HRS
        ],[
            AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDIT_TIME_SPENT_DATA::COMPANY_ID=>$audit_id
        ]);
        if (count($getAuditTimeData)>0) {
            foreach ($getAuditTimeData as $atdk => $atdv) {
                // $getComId=getData(Table::AUDITS_DATA,[AUDITS_DATA::COMPANY_ID],[AUDITS_DATA::ID=>$audit_id]);
                $getPrimSecAuditor=getData(Table::COMPANY_ASSIGNED_DATA,[
                    COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
                ],[
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID=>$atdv[AUDIT_TIME_SPENT_DATA::AUDITOR_ID],
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$audit_id,
                    COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ]);
                // echo "Com ID: ".$getComId[0][AUDITS_DATA::COMPANY_ID];
                // rip($getPrimSecAuditor);
                if (count($getPrimSecAuditor)>0) {
                    $getAName=getData(Table::USERS,[USERS::NAME],[USERS::ID=>$atdv[AUDIT_TIME_SPENT_DATA::AUDITOR_ID]]);
                    if ($getPrimSecAuditor[0][COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==1) {
                        $primary_auditor_name=$getAName[0][USERS::NAME];
                        $primary_auditor_hour+=$atdv[AUDIT_TIME_SPENT_DATA::TIME_IN_HRS];
                    }
                    if ($getPrimSecAuditor[0][COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==2) {
                        if(count($secAudNames)>0){
                            if(!in_array($getAName[0][USERS::NAME],$secAudNames)){
                                $secAudNames[]=$getAName[0][USERS::NAME];
                            }
                        } else {
                            $secAudNames[]=$getAName[0][USERS::NAME];
                        }
                        $secondary_auditor_hour += $atdv[AUDIT_TIME_SPENT_DATA::TIME_IN_HRS];
                    }
                }
            }
        }
        if (count($secAudNames)>0) {
            $secondary_auditor_name = implode(', ',$secAudNames);
        }
        $response['error']=false;
        $response['primary_auditor_name']=$primary_auditor_name;
        $response['secondary_auditor_name']=$secondary_auditor_name;
        $response['primary_auditor_hour']=$primary_auditor_hour;
        $response['secondary_auditor_hour']=$secondary_auditor_hour;
        $response['Time data']=$getAuditTimeData;
        sendRes();
        break;
    case 'SAVE_AUDITOR_TIME_SPENT':
        $cid=isset($ajax_form_data['cid'])?$ajax_form_data['cid']:0;
        $aud_id=isset($ajax_form_data['aud_id'])?$ajax_form_data['aud_id']:0;
        $time=isset($ajax_form_data['th'])?$ajax_form_data['th']:"";
        if($cid==0 || $aud_id==0 || $time==""){
            $response['message']=ERROR_1;
            sendRes();
        }
        $getTimeReportData=getData(Table::AUDIT_TIME_SPENT_DATA,[
            AUDIT_TIME_SPENT_DATA::TIME_IN_HRS
        ],[
            AUDIT_TIME_SPENT_DATA::DATE=>getToday(false),
            AUDIT_TIME_SPENT_DATA::AUDIT_ID=>$aud_id,
            AUDIT_TIME_SPENT_DATA::COMPANY_ID=>$cid,
            AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$_SESSION[RID],
            AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if (count($getTimeReportData)>0) {
            $response['message']="Time already recorded for today!";
            sendRes();
        }
        $save=setData(Table::AUDIT_TIME_SPENT_DATA,[
            AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDIT_TIME_SPENT_DATA::COMPANY_ID=>$cid,
            AUDIT_TIME_SPENT_DATA::AUDIT_ID=>$aud_id,
            AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$_SESSION[RID],
            AUDIT_TIME_SPENT_DATA::DATE=>getToday(false),
            AUDIT_TIME_SPENT_DATA::TIME_IN_HRS=>$time,
            AUDIT_TIME_SPENT_DATA::CREATED_AT=>getToday(),
            AUDIT_TIME_SPENT_DATA::UPDATED_AT=>getToday()
        ]);
        if(!$save['res']){
            logError("Unabled to save Auditor Time Spent Data, Audit id: ".$aud_id.", Company id: ".$cid.", Auditor: ".$_SESSION[RID],$save['error']);
            $response['message']=ERROR_1;
            sendRes();
        }
        $response['error']=false;
        $response['message']="Recorded Successfully!";
        sendRes();
        break;
    case 'GET_CURRENT_AUDITOR_DETAILS':
        $aud_id = isset($ajax_form_data['caudid']) ? $ajax_form_data['caudid'] : 0;
        if($aud_id==0){
            $response['message'] = "Auditor not found !";
            sendRes();
        }
        $currentAuditorDetails=$primeAuditDetails=$secAuditDetails='';
        $getAuditor = getData(Table::USERS, [
            Users::NAME,
            Users::ID
        ], [
            Users::ID => $aud_id,
            Users::USER_TYPE => EMPLOYEE,
            Users::CLIENT_ID => $_SESSION[CLIENT_ID],
            Users::ACTIVE => 1,
            Users::STATUS => ACTIVE_STATUS
        ]);
        $getAuditAssignedData=getData(Table::COMPANY_ASSIGNED_DATA,[
            COMPANY_ASSIGNED_DATA::COMPANY_IDS,
            COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
        ],[
            COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS,
            COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            COMPANY_ASSIGNED_DATA::AUDITOR_ID=>$aud_id
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
        // $all_auditor_statics_data.='
        // <tr>
        //     <td>'.$asl.'</td>
        //     <td>'.$getAuditor[0][Users::NAME].$audCount.'</td>
        //     <td>'.$primCompany.'</td>
        //     <td>'.$secCompanies.'</td>
        // </tr>
        // ';
        $currentAuditorDetails = $getAuditor[0][Users::NAME].$audCount;
        $primeAuditDetails=$primCompany;
        $secAuditDetails=$secCompanies;
        $response['error'] = false;
        $response['currentAuditorDetails'] = $currentAuditorDetails;
        $response['primeAuditDetails'] = $primeAuditDetails;
        $response['secAuditDetails'] = $secAuditDetails;
        sendRes();
        break;
    case 'GET_CURRENT_SECONDARY_AUDITOR_DETAILS':
        $auditor_id = isset($ajax_form_data['caudid']) ? $ajax_form_data['caudid'] : [];
        if(count($auditor_id)==0){
            $response['message'] = "Auditor not found !";
            sendRes();
        }
        $currentAuditorDetails=$primeAuditDetails=$secAuditDetails=$data_row='';
        foreach ($auditor_id as $aidk => $aidv) {
            $aud_id = $aidv;
            $getAuditor = getData(Table::USERS, [
                Users::NAME,
                Users::ID
            ], [
                Users::ID => $aud_id,
                Users::USER_TYPE => EMPLOYEE,
                Users::CLIENT_ID => $_SESSION[CLIENT_ID],
                Users::ACTIVE => 1,
                Users::STATUS => ACTIVE_STATUS
            ]);
            $getAuditAssignedData=getData(Table::COMPANY_ASSIGNED_DATA,[
                COMPANY_ASSIGNED_DATA::COMPANY_IDS,
                COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
            ],[
                COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS,
                COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
                COMPANY_ASSIGNED_DATA::AUDITOR_ID=>$aud_id
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
            // $all_auditor_statics_data.='
            // <tr>
            //     <td>'.$asl.'</td>
            //     <td>'.$getAuditor[0][Users::NAME].$audCount.'</td>
            //     <td>'.$primCompany.'</td>
            //     <td>'.$secCompanies.'</td>
            // </tr>
            // ';
            $currentAuditorDetails = $getAuditor[0][Users::NAME].$audCount;
            $primeAuditDetails=$primCompany;
            $secAuditDetails=$secCompanies;
            $data_row.='
            <div class="row mt-3">
                <div class="col-md-4">
                    <span class="text-dark mt-2 currentSecondaryAudDataSpan">'.$currentAuditorDetails.'</span>
                </div>
                <div class="col-md-4">
                    <span class="text-dark mt-2 currentSecondaryAudDataSpan">'.$primeAuditDetails.'</span>
                </div>
                <div class="col-md-4">
                    <span class="text-dark mt-2 currentSecondaryAudDataSpan">'.$secAuditDetails.'</span>
                </div>
            </div>
            ';
        }
        $response['error'] = false;
        $response['data_row'] = $data_row;
        sendRes();
        break;
    case 'GET_AUDIT_ALL_SECTION_STATS':
        $hash = isset($ajax_form_data['hash'])?$ajax_form_data['hash']:"";
        if ($hash=="") {
            $response['message']="Unabled to fetch Stats !";
            sendRes();
        }
        $exactHashArr = explode('#',$hash);
        $exactHash = $exactHashArr[1];
        $fullHtml=$query_raised=$query_pending=$query_replied=$query_overdue='';
        $notice_issued=$notice_pending=$notice_replied=$notice_overdue='';
        $positionPaperIssued=$positionPaperPending=$positionPaperReplied=$positionPaperOverdue='';
        // $asmtIssued=$asmt
        //today variables
        $end_of_due_date=$end_of_extended_due_date=$overdue_companies='';
        $endDueNoticeDate=$overdueNotices='';
        $endPositionDueDate=$endPositionExtDueDate=$overduePositionPapers='';
        $getCompData = getData(Table::COMPANIES, [
            COMPANIES::COMPANY_NAME,
            COMPANIES::CREATED_AT,
            COMPANIES::ID
        ], [
            COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
            COMPANIES::STATUS => ACTIVE_STATUS
        ]);
        if (count($getCompData) > 0) {
            $query_raised_count=$query_solved=$query_pending_count=$query_replied_count=$query_overdue_count=0;
            $endOfDueDateComsArr=$qRunningOverdue=$endExtDueDate=[];

            $noticeIssuedCount=$noticePendingCount=$noticeRepliedCount=$noticeOverdueCount=0;
            $endDueNoticeDateArr=$overdueNoticesArr=[];

            $positionPaperIssuedCount=$positionPaperPendingCount=$positionPaperRepliedCount=$positionPaperOverdueCount=0;
            $endPositionDueDateArr=$endPositionExtDueDateArr=$overduePositionPapersArr=[];

            // $assessmentIssued
            foreach ($getCompData as $k => $v) {
                $audWh = [
                    AUDITS_DATA::STATUS => ACTIVE_STATUS,
                    AUDITS_DATA::COMPANY_ID => $v[COMPANIES::ID]
                ];
                if ($_SESSION[USER_TYPE]==EMPLOYEE) {
                    $audWh[AUDITS_DATA::USER_ID] = $_SESSION[RID];
                }
                $getAuditData = getData(Table::AUDITS_DATA, [
                    AUDITS_DATA::ID,
                    AUDITS_DATA::AUDIT_START_DATE,
                    AUDITS_DATA::AUDIT_END_DATE,
                    AUDITS_DATA::ACTIVE
                ], $audWh);
                switch ($exactHash) {
                    case 'query':
                        if (count($getAuditData) > 0) {
                            foreach ($getAuditData as $ak => $av) {
                                $getQueryData=getData(Table::QUERY_DATA,[
                                    QUERY_DATA::QUERY_REPLY_IS_SUBMITTED,
                                    QUERY_DATA::ID,
                                    QUERY_DATA::QUERY_STATUS,
                                    QUERY_DATA::TOTAL_NO_OF_QUERY,
                                    QUERY_DATA::NO_OF_QUERY_SOLVED,
                                    QUERY_DATA::NO_OF_QUERY_UNSOLVED,
                                    QUERY_DATA::LAST_DATE_OF_REPLY,
                                    QUERY_DATA::QUERY_NO
                                ],[
                                    QUERY_DATA::COMPANY_ID=>$v[COMPANIES::ID],
                                    QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                ]);
                                $query_raised_count=$query_pending_count=$query_solved=$query_overdue_count=0;
                                if (count($getQueryData)>0) {
                                    foreach ($getQueryData as $sqdk => $sqdv) {
                                        $getExtData = getData(Table::QUERY_EXTENSION_DATES,[
                                            QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED,
                                            QUERY_EXTENSION_DATES::EXTENTION_END_DATE
                                        ],[
                                            QUERY_EXTENSION_DATES::QUERY_ID=>$sqdv[QUERY_DATA::ID],
                                            QUERY_EXTENSION_DATES::ACTIVE=>1
                                        ]);
                                        $query_raised_count += $sqdv[QUERY_DATA::TOTAL_NO_OF_QUERY];
                                        $query_solved += $sqdv[QUERY_DATA::NO_OF_QUERY_SOLVED];
                                        if (($sqdv[QUERY_DATA::LAST_DATE_OF_REPLY] < getToday(false)) && (!in_array($sqdv[QUERY_DATA::QUERY_STATUS],[2,4]))) {
                                            $query_overdue_count += ($sqdv[QUERY_DATA::TOTAL_NO_OF_QUERY]-$sqdv[QUERY_DATA::NO_OF_QUERY_SOLVED]);
                                            // $qRunningOverdue[] = $v[COMPANIES::COMPANY_NAME];
                                            $oc_name = '<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$sqdv[QUERY_DATA::QUERY_NO].')</b> [<span class="text-danger">Since:</span> '.getFormattedDateTime($sqdv[QUERY_DATA::LAST_DATE_OF_REPLY]).']';
                                            if (count($qRunningOverdue)>0) {
                                                if (!in_array($oc_name,$qRunningOverdue)) {
                                                    $qRunningOverdue[] = $oc_name;
                                                }
                                            } else {
                                                $qRunningOverdue[] = $oc_name;
                                            }
                                        }
                                        if (($sqdv[QUERY_DATA::LAST_DATE_OF_REPLY] == getToday(false)) && (!in_array($sqdv[QUERY_DATA::QUERY_STATUS],[2,4]))) {
                                            // echo QUERY_DATA::LAST_DATE_OF_REPLY.': '.$sqdv[QUERY_DATA::LAST_DATE_OF_REPLY];
                                            $endDueDateQcomNames='<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$sqdv[QUERY_DATA::QUERY_NO].')</b>';
                                            if (count($endOfDueDateComsArr)>0) {
                                                if (!in_array($endDueDateQcomNames,$endOfDueDateComsArr)) {
                                                    $endOfDueDateComsArr[] = $endDueDateQcomNames;
                                                }
                                            } else {
                                                $endOfDueDateComsArr[] = $endDueDateQcomNames;
                                            }
                                            // rip($endOfDueDateComsArr);
                                        }
                                        if (count($getExtData)>0) {
                                            if (!in_array($sqdv[QUERY_DATA::QUERY_STATUS],[2,4])) {
                                                if (($getExtData[0][QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED]==1)&&($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE] == getToday(false))) {
                                                    // $endExtDueDate[]=$v[COMPANIES::COMPANY_NAME];
                                                    $endExtDueDateQcomNames='<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$sqdv[QUERY_DATA::QUERY_NO].')</b>';
                                                    if (count($endExtDueDate)>0) {
                                                        if (!in_array($endExtDueDateQcomNames,$endExtDueDate)) {
                                                            $endExtDueDate[] = $endExtDueDateQcomNames;
                                                        }
                                                    } else {
                                                        $endExtDueDate[] = $endExtDueDateQcomNames;
                                                    }
                                                }
                                            } 
                                        }
                                    }
                                    $query_pending_count = ($query_raised_count != 0) ? ($query_raised_count-$query_solved) : 0;
                                }
                                $query_raised .='
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$query_raised_count.'</span></div>
                                ';
                                $query_pending .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$query_pending_count.'</span></div>
                                ';
                                $query_replied .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$query_solved.'</span></div>
                                ';
                                $query_overdue .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$query_overdue_count.'</span></div>
                                ';
                            }
                        }
                        break;
                    case 'notice':
                        if (count($getAuditData) > 0) {
                            foreach ($getAuditData as $ak => $av) {
                                $getNoticeData=getData(Table::COMPANY_NOTICE_DATA,[
                                    COMPANY_NOTICE_DATA::ID,
                                    COMPANY_NOTICE_DATA::NOTICE_NO,
                                    COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY,
                                    COMPANY_NOTICE_DATA::NOTICE_STATUS
                                ],[
                                    COMPANY_NOTICE_DATA::COMPANY_ID => $v[COMPANIES::ID],
                                    COMPANY_NOTICE_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                                ]);
                                $noticeIssuedCount=$noticePendingCount=$noticeRepliedCount=$noticeOverdueCount=0;
                                if (count($getNoticeData)>0) {
                                    $noticeIssuedCount = count($getNoticeData);
                                    foreach ($getNoticeData as $ndk => $ndv) {
                                        if ($ndv[COMPANY_NOTICE_DATA::NOTICE_STATUS]==1) {
                                            $noticeRepliedCount ++;
                                        } else {
                                            if (($ndv[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY]<getToday(false)) && ($ndv[COMPANY_NOTICE_DATA::NOTICE_STATUS]!=1)) {
                                                $noticeOverdueCount++;
                                                $oc_name = '<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$ndv[COMPANY_NOTICE_DATA::NOTICE_NO].')</b> [<span class="text-danger">Since:</span> '.getFormattedDateTime($ndv[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY]).']';
                                                if (count($overdueNoticesArr)>0) {
                                                    if (!in_array($oc_name,$overdueNoticesArr)) {
                                                        $overdueNoticesArr[] = $oc_name;
                                                    }
                                                } else {
                                                    $overdueNoticesArr[] = $oc_name;
                                                }
                                            }
                                            if ($ndv[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY]==getToday(false)) {
                                                // $endDueNoticeDateArr[]=$v[COMPANIES::COMPANY_NAME];
                                                $endDueDateNoticeNames='<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$ndv[COMPANY_NOTICE_DATA::NOTICE_NO].'</b>)';
                                                if (count($endDueNoticeDateArr)>0) {
                                                    if (!in_array($endDueDateNoticeNames,$endDueNoticeDateArr)) {
                                                        $endDueNoticeDateArr[] = $endDueDateNoticeNames;
                                                    }
                                                } else {
                                                    $endDueNoticeDateArr[] = $endDueDateNoticeNames;
                                                }
                                            }
                                        }
                                    }
                                    $noticePendingCount = ($noticeRepliedCount>0)?($noticeIssuedCount-$noticeRepliedCount):$noticeIssuedCount;
                                }
                                $notice_issued .='
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$noticeIssuedCount.'</span></div>
                                ';
                                $notice_pending .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$noticePendingCount.'</span></div>
                                ';
                                $notice_replied .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$noticeRepliedCount.'</span></div>
                                ';
                                $notice_overdue .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$noticeOverdueCount.'</span></div>
                                ';
                            }
                        }
                        break;
                    case 'position-paper':
                        if (count($getAuditData) > 0) {
                            foreach ($getAuditData as $ak => $av) {
                                $getPositionPaperData=getData(Table::POSITION_PAPERS,[
                                    POSITION_PAPERS::INITIAL_SUBMISSION_DATE,
                                    POSITION_PAPERS::OPEN_CLOSE_STATUS,
                                    POSITION_PAPERS::ID,
                                    POSITION_PAPERS::REFERENCE_NO
                                ],[
                                    POSITION_PAPERS::COMPANY_ID=>$v[COMPANIES::ID],
                                    POSITION_PAPERS::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                ]);
                                // echo $v[COMPANIES::COMPANY_NAME].' (ID: '.$v[COMPANIES::ID].')<br>';
                                // rip($getPositionPaperData);
                                $positionPaperIssuedCount=$positionPaperPendingCount=$positionPaperRepliedCount=$positionPaperOverdueCount=0;
                                if (count($getPositionPaperData)>0) {
                                    $positionPaperIssuedCount = count($getPositionPaperData);
                                    foreach ($getPositionPaperData as $ppdk => $ppdv) {
                                        if ($ppdv[POSITION_PAPERS::OPEN_CLOSE_STATUS]==1) {
                                            $getPositionExtData=getData(Table::POSITION_PAPER_EXTENTION_DATES,[
                                                POSITION_PAPER_EXTENTION_DATES::ACTIVE,
                                                POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED,
                                                POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE
                                            ],[
                                                POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID=>$ppdv[POSITION_PAPERS::ID],
                                                POSITION_PAPER_EXTENTION_DATES::CLIENT_ID=>$_SESSION[CLIENT_ID],
                                                POSITION_PAPER_EXTENTION_DATES::ACTIVE=>1,
                                                POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED=>1
                                            ]);
                                            $positionExtFound=$positionExtOverDue=false;
                                            if (count($getPositionExtData)>0) {
                                                $positionExtFound=true;
                                                if ($getPositionExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]<getToday(false)) {
                                                    $positionExtOverDue=true;
                                                }
                                            }
                                            if ($ppdv[POSITION_PAPERS::INITIAL_SUBMISSION_DATE]<getToday(false)) {
                                                // $getQnameOverdue = getData(Table::QUERY_DATA,[QUERY_DATA::QUERY_NO],[QUERY_DATA::ID=>$ppdv[POSITION_PAPER_DATA::QUERY_ID]]);
                                                
                                                if ($positionExtFound) {
                                                    if ($positionExtOverDue) {
                                                        $positionPaperOverdueCount++;
                                                        $QnameWcomOverdue = '<b>#'.$ppdv[POSITION_PAPERS::REFERENCE_NO].'</b> from: '.$v[COMPANIES::COMPANY_NAME].' [<span class="text-danger">Since:</span> '.getFormattedDateTime($getPositionExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]).']';
                                                        // $overduePositionPapersArr[]=$QnameWcomOverdue;
                                                        if (count($overduePositionPapersArr)>0) {
                                                            if (!in_array($QnameWcomOverdue,$overduePositionPapersArr)) {
                                                                $overduePositionPapersArr[] = $QnameWcomOverdue;
                                                            }
                                                        } else {
                                                            $overduePositionPapersArr[] = $QnameWcomOverdue;
                                                        }
                                                    }
                                                } else {
                                                    $positionPaperOverdueCount++;
                                                    $QnameWcomOverdue = '<b>#'.$ppdv[POSITION_PAPERS::REFERENCE_NO].'</b> from: '.$v[COMPANIES::COMPANY_NAME].' [<span class="text-danger">Since:</span> '.getFormattedDateTime($ppdv[POSITION_PAPER_DATA::INITIAL_SUBMISSION_DATE]).']';
                                                    // $overduePositionPapersArr[]=$QnameWcomOverdue;
                                                    if (count($overduePositionPapersArr)>0) {
                                                        if (!in_array($QnameWcomOverdue,$overduePositionPapersArr)) {
                                                            $overduePositionPapersArr[] = $QnameWcomOverdue;
                                                        }
                                                    } else {
                                                        $overduePositionPapersArr[] = $QnameWcomOverdue;
                                                    }
                                                }
                                            }
                                            if ($ppdv[POSITION_PAPER_DATA::INITIAL_SUBMISSION_DATE]==getToday(false)) {
                                                // $getQname = getData(Table::QUERY_DATA,[QUERY_DATA::QUERY_NO],[QUERY_DATA::ID=>$ppdv[POSITION_PAPER_DATA::QUERY_ID]]);
                                                $QnameWcom = '<b>#'.$ppdv[POSITION_PAPERS::REFERENCE_NO].'</b> from: '.$v[COMPANIES::COMPANY_NAME];
                                                if ($positionExtFound) {
                                                    if ($getPositionExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]==getToday(false)) {
                                                        // $endPositionDueDateArr[]=$QnameWcom;
                                                        if (count($endPositionExtDueDateArr)>0) {
                                                            if (!in_array($QnameWcom,$endPositionExtDueDateArr)) {
                                                                $endPositionExtDueDateArr[] = $QnameWcom;
                                                            }
                                                        } else {
                                                            $endPositionExtDueDateArr[] = $QnameWcom;
                                                        }
                                                    }
                                                } else {
                                                    if (count($endPositionDueDateArr)>0) {
                                                        if (!in_array($QnameWcom,$endPositionDueDateArr)) {
                                                            $endPositionDueDateArr[] = $QnameWcom;
                                                        }
                                                    } else {
                                                        $endPositionDueDateArr[] = $QnameWcom;
                                                    }
                                                }
                                            }
                                        } else {
                                            if ($ppdv[POSITION_PAPERS::OPEN_CLOSE_STATUS]==0) {
                                                $positionPaperRepliedCount++;
                                            }
                                        }
                                    }
                                    $positionPaperPendingCount = ($positionPaperRepliedCount!=0)?($positionPaperIssuedCount-$positionPaperRepliedCount):$positionPaperIssuedCount;
                                }
                                $positionPaperIssued .='
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$positionPaperIssuedCount.'</span></div>
                                ';
                                $positionPaperPending .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$positionPaperPendingCount.'</span></div>
                                ';
                                $positionPaperReplied .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$positionPaperRepliedCount.'</span></div>
                                ';
                                $positionPaperOverdue .= '
                                <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$positionPaperOverdueCount.'</span></div>
                                ';
                            }
                        }
                        break;
                    case 'assessment':
                        if (count($getAuditData) > 0) {
                            foreach ($getAuditData as $ak => $av) {
                                $getAssessmentData = getData(Table::AUDIT_ASSESSMENT_DATA,['*'],[
                                    AUDIT_ASSESSMENT_DATA::COMPANY_ID =>$v[COMPANIES::ID],
                                    AUDIT_ASSESSMENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                                ]);
                            }
                        }
                        break;
                }
            }
            // rip($endOfDueDateComsArr);
            switch ($exactHash) {
                case 'query':
                    $end_of_due_date = (count($endOfDueDateComsArr)>0)?implode(', <br>', $endOfDueDateComsArr):EMPTY_VALUE;
                    $overdue_companies = (count($qRunningOverdue)>0)?implode(', <br>', $qRunningOverdue):EMPTY_VALUE;
                    $end_of_extended_due_date = (count($endExtDueDate)>0)?implode(', <br>', $endExtDueDate):EMPTY_VALUE;
                    break;
                case 'notice':
                    $endDueNoticeDate = (count($endDueNoticeDateArr)>0)?implode(', <br>', $endDueNoticeDateArr):EMPTY_VALUE;
                    $overdueNotices = (count($overdueNoticesArr)>0)?implode(', <br>', $overdueNoticesArr):EMPTY_VALUE;
                    break;
                case 'position-paper':
                    $endPositionDueDate = (count($endPositionDueDateArr)>0)?implode(', <br>', $endPositionDueDateArr):EMPTY_VALUE;
                    $endPositionExtDueDate = (count($endPositionExtDueDateArr)>0)?implode(', <br>', $endPositionExtDueDateArr):EMPTY_VALUE;
                    $overduePositionPapers = (count($overduePositionPapersArr)>0)?implode(', <br>', $overduePositionPapersArr):EMPTY_VALUE;
                    break;
                case 'assessment':
                    # code...
                    break;
            }
        }
        switch ($exactHash) {
            case 'query':
                $fullHtml='
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Query Raised</h6>
                                <hr />
                                <div class="row">
                                    '.$query_raised.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Query Pending</h6>
                                <hr />
                                <div class="row">
                                    '.$query_pending.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Query Replied</h6>
                                <hr />
                                <div class="row">
                                    '.$query_replied.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Query Overdue</h6>
                                <hr />
                                <div class="row">
                                    '.$query_overdue.'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <fieldset class="fldset">
                            <legend>TODAY&nbsp;['.getFormattedDateTime(getToday(false)).']</legend>
                                <div class="row">
                                    <div class="col-md-7 mt-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span class="text-danger font-weight-bold">End of due date for reply</span>
                                            </div>
                                            <div class="col-md-5">
                                                <span class="text-dark">'.$end_of_due_date.'</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-7 mt-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span class="text-danger font-weight-bold"> End of extended due date: </span>
                                            </div>
                                            <div class="col-md-5">
                                                <span class="text-dark pt-2">'.$end_of_extended_due_date.'</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-7 mt-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span class="text-danger font-weight-bold"> Query running overdue:</span>
                                            </div>
                                            <div class="col-md-5">
                                                <span class="text-dark pt-2">'.$overdue_companies.'</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                        </fieldset>
                    </div>
                </div>
                ';
                break;
            case 'notice':
                $fullHtml='
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Notice Issued</h6>
                                <hr />
                                <div class="row">
                                    '.$notice_issued.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Notice Pending</h6>
                                <hr />
                                <div class="row">
                                    '.$notice_pending.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Notice Replied</h6>
                                <hr />
                                <div class="row">
                                    '.$notice_replied.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Notice Overdue</h6>
                                <hr />
                                <div class="row">
                                    '.$notice_overdue.'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <fieldset class="fldset">
                            <legend>TODAY&nbsp;['.getFormattedDateTime(getToday(false)).']</legend>
                                <div class="row">
                                    <div class="col-md-7 mt-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span class="text-danger font-weight-bold">End of due date for reply: </span>
                                            </div>
                                            <div class="col-md-5">
                                                <span class="text-dark">'.$endDueNoticeDate.'</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-7 mt-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span class="text-danger font-weight-bold">Notice running overdue:</span>
                                            </div>
                                            <div class="col-md-5">
                                                <span class="text-dark pt-2">'.$overdueNotices.'</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                        </fieldset>
                    </div>
                </div>
                ';
                break;
            case 'position-paper':
                $fullHtml='
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Position Paper Issued</h6>
                                <hr />
                                <div class="row">
                                    '.$positionPaperIssued.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Position Paper Pending</h6>
                                <hr />
                                <div class="row">
                                    '.$positionPaperPending.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Position Paper Replied</h6>
                                <hr />
                                <div class="row">
                                    '.$positionPaperReplied.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-primary">Position Paper Overdue</h6>
                                <hr />
                                <div class="row">
                                    '.$positionPaperOverdue.'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <fieldset class="fldset">
                            <legend>TODAY&nbsp;['.getFormattedDateTime(getToday(false)).']</legend>
                                <div class="row">
                                    <div class="col-md-7 mt-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span class="text-danger font-weight-bold">End of due date for reply: </span>
                                            </div>
                                            <div class="col-md-5">
                                                <span class="text-dark">'.$endPositionDueDate.'</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-7 mt-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span class="text-danger font-weight-bold">Position Paper running overdue:</span>
                                            </div>
                                            <div class="col-md-5">
                                                <span class="text-dark pt-2">'.$endPositionExtDueDate.'</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-7 mt-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span class="text-danger font-weight-bold">Position Paper running overdue:</span>
                                            </div>
                                            <div class="col-md-5">
                                                <span class="text-dark pt-2">'.$overduePositionPapers.'</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                        </fieldset>
                    </div>
                </div>
                ';
                break;
            case 'assessment':
                # code...
                break;
        }
        
        $response['error']=false;
        $response['hash']=$exactHash;
        $response['fullHtml']=$fullHtml;
        sendRes();
        break;
    case 'GET_CASES_UNDER_AUDIT_TABLE_DATA':
        $auditor_id=isset($ajax_form_data['aud'])?$ajax_form_data['aud']:0;
        $month=isset($ajax_form_data['mon'])?$ajax_form_data['mon']:"";
        $year=isset($ajax_form_data['yr'])?$ajax_form_data['yr']:"";
        if ($auditor_id==0 || $month=="" || $year=="") {
            $response['message']=EMPTY_FIELD_ALERT;
            sendRes();
        }
        $month = ($month < 10) ? '0' . $month : $month;
        $fullHtml=$totalHtml='';
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
        if (count($getCompData) > 0) {
            $sl=1;
            $greater12Months=$greater6Months=$less6Months=$durrMonth=$durrExactMon=0;
            foreach ($getCompData as $k => $v) {
                $name = altRealEscape($v[COMPANIES::COMPANY_NAME]);
                $name .= ($v[COMPANIES::TAX_IDENTIFICATION_NUMBER]!=null)?'[ <b>TIN: </b>'.altRealEscape($v[COMPANIES::TAX_IDENTIFICATION_NUMBER]).' ]':'';
                $ccode = ($v[COMPANIES::CASE_CODE]!=null)?altRealEscape($v[COMPANIES::CASE_CODE]):EMPTY_VALUE;
                $date_allocated=$date_commence=$auditsDuration=EMPTY_VALUE;
                $auditTax=$auditPenalty=$auditHours=$auditStartYear=$auditEndYear=0;
                $auditDur=0;
                $getAuditAssignData=getData(Table::COMPANY_ASSIGNED_DATA,[
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID,
                    COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
                    "DATE(".COMPANY_ASSIGNED_DATA::CREATED_AT.") as auditAssignedDate",
                    "YEAR(".COMPANY_ASSIGNED_DATA::CREATED_AT.") as auditAssignedYear",
                    COMPANY_ASSIGNED_DATA::USER_ID
                ],[
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$v[COMPANIES::ID],
                    COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
                    COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS
                ]);
                $getAuditAssessData=getData(Table::AUDIT_ASSESSMENT_DATA,[
                    AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT,
                    AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT
                ],[
                    AUDIT_ASSESSMENT_DATA::COMPANY_ID=>$v[COMPANIES::ID],
                    AUDIT_ASSESSMENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ]);
                $getAuditHoursData=getData(Table::AUDIT_TIME_SPENT_DATA,[
                    "SUM(".AUDIT_TIME_SPENT_DATA::TIME_IN_HRS.") as totalHrs"
                ],[
                    AUDIT_TIME_SPENT_DATA::COMPANY_ID=>$v[COMPANIES::ID],
                    AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$auditor_id,
                    "YEAR(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$year,
                    "MONTH(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$month,
                    AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ]);
                if (count($getAuditAssignData)>0) {
                    foreach ($getAuditAssignData as $aadk => $aadv) {
                        if ($aadv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY==1]) {
                            $date_allocated = getFormattedDateTime($aadv["auditAssignedDate"]);
                            $getDateCommData=getData(Table::AUDITS_DATA,[
                                AUDITS_DATA::AUDIT_START_DATE,
                                AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE,
                                AUDITS_DATA::AUDIT_END_DATE,
                                "YEAR(".AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE.") as auditExpComYear",
                                "YEAR(".AUDITS_DATA::AUDIT_START_DATE.") as auditStartYear",
                                "YEAR(".AUDITS_DATA::AUDIT_END_DATE.") as auditEndYear"
                            ],[AUDITS_DATA::COMPANY_ID=>$v[COMPANIES::ID]]);
                            $date_commence=(count($getDateCommData)>0)?getFormattedDateTime($getDateCommData[0][AUDITS_DATA::AUDIT_START_DATE]):$date_commence;
                            $auditsDuration=(count($getDateCommData)>0)?$getDateCommData[0]["auditStartYear"]:$auditsDuration;
                            $auditStartYear=(count($getDateCommData)>0)?$getDateCommData[0][AUDITS_DATA::AUDIT_START_DATE]:$auditStartYear;
                            if ((count($getDateCommData)>0)&&($getDateCommData[0]["auditEndYear"]!=null)) {
                                $auditsDuration.=" - ".$getDateCommData[0]["auditEndYear"];
                                $auditEndYear=$getDateCommData[0][AUDITS_DATA::AUDIT_END_DATE];
                            } else {
                                if ((count($getDateCommData)>0)&&($getDateCommData[0]["auditExpComYear"]!=null)) {
                                    $auditsDuration.=" - ".$getDateCommData[0]["auditExpComYear"];
                                    $auditEndYear=$getDateCommData[0][AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE];
                                }
                            }
                            if ($auditStartYear!=0 && $auditEndYear!=0) {
                                // $auditDur=($auditEndYear-$auditStartYear);
                                $auditDur=getDateDiff($auditStartYear,$auditEndYear);
                                $durrMonth=getMonthsDifference($auditStartYear,$auditEndYear);
                                $durrExactMonArr[$v[COMPANIES::COMPANY_NAME]]=$durrMonth;
                                // if ($durrMonth!=0) {
                                //     if ($durrMonth>12) {
                                //         $greater12Months++;
                                //     }
                                //     if ($durrMonth<6) {
                                //         $less6Months++;
                                //     }
                                //     if (($durrMonth<12)&&($durrMonth>6)) {
                                //         $greater6Months++;
                                //     }
                                // }
                                // $auditDur=($auditDur<=0)?1:$auditDur;
                            }
                            // $auditsDuration=(count($getDateCommData)>0)?$getDateCommData[0]["auditStartYear"]." - ".$getDateCommData[0]["auditExpComYear"]:$auditsDuration;
                        }
                    }
                } 
                // else {
                //     $greater12Months=$greater6Months=$less6Months=0;
                // }
                if (count($getAuditAssessData)>0) {
                    $auditTax = ($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT]!=0)?moneyFormatIndia($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT]):$auditTax;
                    $auditPenalty = ($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT]!=0)?moneyFormatIndia($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT]):$auditPenalty;
                }
                $auditHours=0;
                if (count($getAuditHoursData)>0) {
                    $auditHours=$getAuditHoursData[0]["totalHrs"];
                }
                $fullHtml.='
                <tr>
                    <td>'.$sl.'</td>
                    <td>'.$name.'</td>
                    <td>'.$ccode.'</td>
                    <td>'.$date_allocated.'</td>
                    <td>'.$date_commence.'</td>
                    <td>'.$auditsDuration.' ('.$auditDur.')</td>
                    <td>'.$auditHours.'</td>
                    <td>'.$auditTax.'</td>
                    <td>'.$auditPenalty.'</td>
                </tr>
                ';
                $sl++;
            }
            foreach ($durrExactMonArr as $dmk => $dmv) {
                // if ($dmv!=0) {
                    if (($dmv>12)&&($dmv!=0)) {
                        $greater12Months++;
                    }
                    if (($dmv<6)||($dmv==0)) {
                        $less6Months++;
                    }
                    if (($dmv<12)&&($dmv>6)&&($dmv!=0)) {
                        $greater6Months++;
                    }
                // }
            }
            $totalHtml='
            <tbody>
            <tr>
                <th colspan="5" rowspan="2" class="text-right" style="vertical-align:center;"><b>Total:</b></th>
                <th>> 12 Months</th>
                <th>> 6 Months</th>
                <th>< 6 months</th>
                <th>Total</th>
            </tr>
            <tr>
                <td>'.$greater12Months.'</td>
                <td>'.$greater6Months.'</td>
                <td>'.$less6Months.'</td>
                <td>'.($greater12Months+$greater6Months+$less6Months).'</td>
            </tr>
            </tbody>
            ';
        } else {
            $fullHtml.='
            <tr class="animated fadeInDown">
                <td colspan="9">
                    <div class="alert alert-danger" role="alert">
                        No data found !
                    </div>
                </td>
            </tr>
            ';
        }
        $response['error']=false;
        $response['fullHtml']=$fullHtml;
        $response['totalHtml']=$totalHtml;
        $response['durrExactMonArr']=$durrExactMonArr;
        sendRes();
        break;
    case 'GET_AUDITOR_DAILY_REPORT_DATA':
        $auditor_id=isset($ajax_form_data['aud'])?$ajax_form_data['aud']:0;
        $month=isset($ajax_form_data['mon'])?$ajax_form_data['mon']:"";
        $year=isset($ajax_form_data['yr'])?$ajax_form_data['yr']:"";
        if ($auditor_id==0 || $month=="" || $year=="") {
            $response['message']=EMPTY_FIELD_ALERT;
            sendRes();
        }
        $tableHead='
        <thead class="text-center table-warning">
            <tr>
                <th>sl.</th>
                <th>Taxpayer</th>
                <th>date allocated</th>
                <th>Total Hours</th>';
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
        if ($year > $cur_year) {
            $response['error'] = true;
            $response['message'] = 'You Cannot Select Year Greater Than ' . $cur_year;
            sendRes();
        } elseif (($month > $cur_month)) {
            if ($year >= $cur_year) {
                $response['error'] = true;
                $response['message'] = 'You Cannot Select Month Greater Than ' . $bill_monthName . ' In The Year ' . $cur_year . '';
                sendRes();
            }
        }
        $month = ($month < 10) ? '0' . $month : $month;
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
                            $xx=$lh=$th=$oh='';
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
                                $xx.='
                                <td class="'.$bg_color.'">'.$dayHr.'</td>
                                ';
                                if($ccount==1){
                                    $lh.='
                                        <td class="'.$bg_color.'">'.$dayLeave.'</td>
                                    ';
                                    $th.='
                                        <td class="'.$bg_color.'">'.$dayTrain.'</td>
                                    ';
                                    $oh.='
                                        <td class="'.$bg_color.'">'.$dayOthDuty.'</td>
                                    ';
                                }
                            }
                            $fullHtml.='
                                <td class="alert-warning">'.$auditHours.'</td>
                                '.$xx.'
                            </tr>
                            ';
                            if($ccount==1){
                                $leaveHtml.='
                                    <td class="alert-warning">'.$leaveHours.'</td>
                                    '.$lh.'
                                </tr>
                                ';
                                $trainingHtml.='
                                    <td class="alert-warning">'.$trainingHours.'</td>
                                    '.$th.'
                                </tr>
                                ';
                                $otherDutyHtml.='
                                    <td class="alert-warning">'.$othDutyHours.'</td>
                                    '.$oh.'
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
                            $yy=$olh=$oth=$ooh='';
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
                                $yy.='
                                    <td class="'.$bg_color.'">'.$dayHr.'</td>
                                ';
                                if($ccount==1){
                                    $leaveHtml.='
                                        <td class="'.$bg_color.'">'.$dayLeave.'</td>
                                        '.$olh.'
                                    ';
                                    $trainingHtml.='
                                        <td class="'.$bg_color.'">'.$dayTrain.'</td>
                                        '.$oth.'
                                    ';
                                    $otherDutyHtml.='
                                        <td class="'.$bg_color.'">'.$dayOthDuty.'</td>
                                        '.$ooh.'
                                    ';
                                }
                            }
                            $otherAuditHtml.='
                                <td class="alert-warning">'.$auditHours.'</td>
                                '.$yy.'
                            </tr>
                            ';
                            if($ccount==1){
                                $leaveHtml.='
                                    <td class="alert-warning">'.$leaveHours.'</td>
                                    '.$olh.'
                                </tr>
                                ';
                                $trainingHtml.='
                                    <td class="alert-warning">'.$trainingHours.'</td>
                                    '.$oth.'
                                </tr>
                                ';
                                $otherDutyHtml.='
                                    <td class="alert-warning">'.$othDutyHours.'</td>
                                    '.$ooh.'
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
            $tableHead.='
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
                    <td colspan="3"><h6><b>Total Time :</b></h6></td>
                    <td>'.(($totOwnCase+$totOtherCase+$othDutyHours+$trainingHours)-($leaveHours)).'</td>
                    <td colspan="'.$colspan.'"> </td>
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
        $response['error']=false;
        $response['tableHead']=$tableHead;
        $response['fullHtml']=$fullHtml;
        $response['otherAuditHtml']=$otherAuditHtml;
        $response['hrsArr']=$hrsArr;
        $response['singleHrDataArr']=$singleHrDataArr;
        $response['leaveHtml']=$leaveHtml;
        sendRes();
        break;
    case 'GET_AUDITOR_SELF_DAILY_REPORT_DATA':
        $auditor_id=$_SESSION[RID];
        $month=isset($ajax_form_data['mon'])?$ajax_form_data['mon']:"";
        $year=isset($ajax_form_data['yr'])?$ajax_form_data['yr']:"";
        if ($auditor_id==0 || $month=="" || $year=="") {
            $response['message']=EMPTY_FIELD_ALERT;
            sendRes();
        }
        $tableHead='
        <thead class="text-center table-warning">
            <tr>
                <th>sl.</th>
                <th>Taxpayer</th>
                <th>date allocated</th>
                <th>total hours</th>';
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
        if ($year > $cur_year) {
            $response['error'] = true;
            $response['message'] = 'You Cannot Select Year Greater Than ' . $cur_year;
            sendRes();
        } elseif (($month > $cur_month)) {
            if ($year >= $cur_year) {
                $response['error'] = true;
                $response['message'] = 'You Cannot Select Month Greater Than ' . $bill_monthName . ' In The Year ' . $cur_year . '';
                sendRes();
            }
        }
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
                            $xx=$lh=$th=$oh='';
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
                                $xx.='
                                <td class="'.$bg_color.'">'.$dayHr.'</td>
                                ';
                                if($ccount==1){
                                    $lh.='
                                        <td class="'.$bg_color.'">'.$dayLeave.'</td>
                                    ';
                                    $th.='
                                        <td class="'.$bg_color.'">'.$dayTrain.'</td>
                                    ';
                                    $oh.='
                                        <td class="'.$bg_color.'">'.$dayOthDuty.'</td>
                                    ';
                                }
                            }
                            $fullHtml.='
                                <td class="alert-warning">'.$auditHours.'</td>
                                '.$xx.'
                            </tr>
                            ';
                            if($ccount==1){
                                $leaveHtml.='
                                    <td class="alert-warning">'.$leaveHours.'</td>
                                    '.$lh.'
                                </tr>
                                ';
                                $trainingHtml.='
                                    <td class="alert-warning">'.$trainingHours.'</td>
                                    '.$th.'
                                </tr>
                                ';
                                $otherDutyHtml.='
                                    <td class="alert-warning">'.$othDutyHours.'</td>
                                    '.$oh.'
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
                            $yy=$olh=$oth=$ooh='';
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
                                $yy.='
                                    <td class="'.$bg_color.'">'.$dayHr.'</td>
                                ';
                                if($ccount==1){
                                    $olh.='
                                        <td class="'.$bg_color.'">'.$dayLeave.'</td>
                                    ';
                                    $oth.='
                                        <td class="'.$bg_color.'">'.$dayTrain.'</td>
                                    ';
                                    $ooh.='
                                        <td class="'.$bg_color.'">'.$dayOthDuty.'</td>
                                    ';
                                }
                            }
                            $otherAuditHtml.='
                                <td class="alert-warning">'.$auditHours.'</td>
                                '.$yy.'
                            </tr>
                            ';
                            if($ccount==1){
                                $leaveHtml.='
                                    <td class="alert-warning">'.$leaveHours.'</td>
                                    '.$olh.'
                                </tr>
                                ';
                                $trainingHtml.='
                                    <td class="alert-warning">'.$trainingHours.'</td>
                                    '.$oth.'
                                </tr>
                                ';
                                $otherDutyHtml.='
                                    <td class="alert-warning">'.$othDutyHours.'</td>
                                    '.$ooh.'
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
                    <td colspan="3"><h6><b>Total Time :</b></h6></td>
                    <td>'.(($totOwnCase+$totOtherCase+$othDutyHours+$trainingHours)-($leaveHours)).'</td>
                    <td colspan="'.($colspan).'"> </td>
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
        $response['error']=false;
        $response['tableHead']=$tableHead;
        $response['fullHtml']=$fullHtml;
        sendRes();
        break;
    case 'GET_CASE_COMPLETED_TABLE_DATA':
        $auditor_id=isset($ajax_form_data['aud'])?$ajax_form_data['aud']:0;
        $month=isset($ajax_form_data['mon'])?$ajax_form_data['mon']:"";
        $year=isset($ajax_form_data['yr'])?$ajax_form_data['yr']:"";
        if ($auditor_id==0 || $month=="" || $year=="") {
            $response['message']=EMPTY_FIELD_ALERT;
            sendRes();
        }
        $month = ($month < 10) ? '0' . $month : $month;
        $fullHtml=$totalHtml=$summeryHours='';
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
        if (count($getCompData) > 0) {
            $sl=1;
            $greater12Months=$greater6Months=$less6Months=$durrMonth=$durrExactMon=0;
            $totalTimeHrs=$totalOmittedAmt=$totalTaxAmt=$totalPenaltyAmt=$totalTaxPenalty=[];
            foreach ($getCompData as $k => $v) {
                $name = altRealEscape($v[COMPANIES::COMPANY_NAME]);
                $name .= ($v[COMPANIES::TAX_IDENTIFICATION_NUMBER]!=null)?' <br><small>[ <b>TIN: </b>'.altRealEscape($v[COMPANIES::TAX_IDENTIFICATION_NUMBER]).' ]</small>':'';
                $ccode = ($v[COMPANIES::CASE_CODE]!=null)?altRealEscape($v[COMPANIES::CASE_CODE]):EMPTY_VALUE;
                $date_allocated=$date_commence=$date_completed=$auditsDuration=$typeOfAssmt=EMPTY_VALUE;
                $auditTax=$auditPenalty=$auditHours=$auditStartYear=$auditEndYear=$omittedIncome=0;
                $auditDur=0;
                $getAuditAssignData=getData(Table::COMPANY_ASSIGNED_DATA,[
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID,
                    COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY,
                    "DATE(".COMPANY_ASSIGNED_DATA::CREATED_AT.") as auditAssignedDate",
                    "YEAR(".COMPANY_ASSIGNED_DATA::CREATED_AT.") as auditAssignedYear",
                    COMPANY_ASSIGNED_DATA::USER_ID
                ],[
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$v[COMPANIES::ID],
                    COMPANY_ASSIGNED_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
                    COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS
                ]);
                $getAuditAssessData=getData(Table::AUDIT_ASSESSMENT_DATA,[
                    AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT,
                    AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT,
                    AUDIT_ASSESSMENT_DATA::OMITTED_INCOME_AMOUNT,
                    AUDIT_ASSESSMENT_DATA::ACTIVE
                ],[
                    AUDIT_ASSESSMENT_DATA::COMPANY_ID=>$v[COMPANIES::ID],
                    AUDIT_ASSESSMENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ]);
                $getAuditHoursData=getData(Table::AUDIT_TIME_SPENT_DATA,[
                    "SUM(".AUDIT_TIME_SPENT_DATA::TIME_IN_HRS.") as totalHrs"
                ],[
                    AUDIT_TIME_SPENT_DATA::COMPANY_ID=>$v[COMPANIES::ID],
                    AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$auditor_id,
                    "YEAR(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$year,
                    "MONTH(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$month,
                    AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                ]);
                if (count($getAuditAssignData)>0) {
                    foreach ($getAuditAssignData as $aadk => $aadv) {
                        if ($aadv[COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY==1]) {
                            $date_allocated = getFormattedDateTime($aadv["auditAssignedDate"]);
                            $getDateCommData=getData(Table::AUDITS_DATA,[
                                AUDITS_DATA::AUDIT_START_DATE,
                                AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE,
                                AUDITS_DATA::AUDIT_END_DATE,
                                "YEAR(".AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE.") as auditExpComYear",
                                "YEAR(".AUDITS_DATA::AUDIT_START_DATE.") as auditStartYear",
                                "YEAR(".AUDITS_DATA::AUDIT_END_DATE.") as auditEndYear"
                            ],[AUDITS_DATA::COMPANY_ID=>$v[COMPANIES::ID]]);
                            $date_commence=(count($getDateCommData)>0)?getFormattedDateTime($getDateCommData[0][AUDITS_DATA::AUDIT_START_DATE]):$date_commence;
                            $auditsDuration=(count($getDateCommData)>0)?$getDateCommData[0]["auditStartYear"]:$auditsDuration;
                            $auditStartYear=(count($getDateCommData)>0)?$getDateCommData[0][AUDITS_DATA::AUDIT_START_DATE]:$auditStartYear;
                            if ((count($getDateCommData)>0)&&($getDateCommData[0]["auditEndYear"]!=null)) {
                                if ($auditsDuration==$getDateCommData[0]["auditEndYear"]) {
                                    $auditsDuration.=" - ".($getDateCommData[0]["auditEndYear"]+1);
                                } else {
                                    $auditsDuration.=" - ".$getDateCommData[0]["auditEndYear"];
                                }
                                $auditEndYear=$getDateCommData[0][AUDITS_DATA::AUDIT_END_DATE];
                                $date_completed=getFormattedDateTime($getDateCommData[0][AUDITS_DATA::AUDIT_END_DATE]);
                            } else {
                                if ((count($getDateCommData)>0)&&($getDateCommData[0]["auditExpComYear"]!=null)) {
                                    if ($auditsDuration==$getDateCommData[0]["auditExpComYear"]) {
                                        $auditsDuration.=" - ".($getDateCommData[0]["auditExpComYear"]+1);
                                    } else {
                                        $auditsDuration.=" - ".$getDateCommData[0]["auditExpComYear"];
                                    }
                                    // $auditsDuration.=" - ".$getDateCommData[0]["auditExpComYear"];
                                    $auditEndYear=$getDateCommData[0][AUDITS_DATA::AUDIT_EXPECTED_COMPLETE_DATE];
                                }
                            }
                            if ($auditStartYear!=0 && $auditEndYear!=0) {
                                // $auditDur=($auditEndYear-$auditStartYear);
                                $auditDur=getDateDiff($auditStartYear,$auditEndYear);
                                $durrMonth=getMonthsDifference($auditStartYear,$auditEndYear);
                                $durrExactMonArr[$v[COMPANIES::COMPANY_NAME]]=$durrMonth;
                                // if ($durrMonth!=0) {
                                //     if ($durrMonth>12) {
                                //         $greater12Months++;
                                //     }
                                //     if ($durrMonth<6) {
                                //         $less6Months++;
                                //     }
                                //     if (($durrMonth<12)&&($durrMonth>6)) {
                                //         $greater6Months++;
                                //     }
                                // }
                                // $auditDur=($auditDur<=0)?1:$auditDur;
                            }
                            // $auditsDuration=(count($getDateCommData)>0)?$getDateCommData[0]["auditStartYear"]." - ".$getDateCommData[0]["auditExpComYear"]:$auditsDuration;
                        }
                    }
                } 
                // else {
                //     $greater12Months=$greater6Months=$less6Months=0;
                // }
                if (count($getAuditAssessData)>0) {
                    $auditTax = ($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT]!=0)?($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::CLAIMABLE_TAX_AMOUNT]):$auditTax;
                    $auditPenalty = ($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT]!=0)?($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::PENALTY_AMOUNT]):$auditPenalty;
                    $omittedIncome = ($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::OMITTED_INCOME_AMOUNT]!=0)?($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::OMITTED_INCOME_AMOUNT]):$omittedIncome;
                    
                    $totalOmittedAmt[]=$omittedIncome;
                    $totalTaxAmt[]=$auditTax;
                    $totalPenaltyAmt[]=$auditPenalty;
                    $totalTaxPenalty[]=($auditPenalty+$auditTax);
                    if ($getAuditAssessData[0][AUDIT_ASSESSMENT_DATA::ACTIVE]==1) {
                        $typeOfAssmt = 'Collection';
                    } else {
                        $typeOfAssmt = 'Objection';
                    }
                }
                if (count($getAuditHoursData)>0) {
                    $auditHours=$getAuditHoursData[0]["totalHrs"];
                    $totalTimeHrs[]=$auditHours;
                } else {
                    $auditHours=0;
                }
                $fullHtml.='
                <tr>
                    <td>'.$sl.'</td>
                    <td>'.$name.'</td>
                    <td>'.$ccode.'</td>
                    <td>'.$date_allocated.'</td>
                    <td>'.$date_commence.'</td>
                    <td>'.$date_completed.'</td>
                    <td>'.$auditHours.'</td>
                    <td>'.$omittedIncome.'</td>
                    <td>'.moneyFormatIndia($auditTax).'</td>
                    <td>'.moneyFormatIndia($auditPenalty).'</td>
                    <td>'.moneyFormatIndia($auditTax+$auditPenalty).'</td>
                    <td>'.$auditsDuration.' <br><small>('.$auditDur.')</small></td>
                    <td>'.$typeOfAssmt.'</td>
                </tr>
                ';
                $sl++;
            }
            foreach ($durrExactMonArr as $dmk => $dmv) {
                if (($dmv>12)&&($dmv!=0)) {
                    $greater12Months++;
                }
                if (($dmv<6)||($dmv==0)) {
                    $less6Months++;
                }
                if (($dmv<12)&&($dmv>6)&&($dmv!=0)) {
                    $greater6Months++;
                }
            }
            // $totalTimeHrs=$totalOmittedAmt=$totalTaxAmt=$totalPenaltyAmt=$totalTaxPenalty=[];
            $fullHtml.='
            <tr>
                <th colspan="6" class="text-right" style="vertical-align:center;"><b>Total For Month:</b></th>
                <td>'.array_sum($totalTimeHrs).'</td>
                <td>'.array_sum($totalOmittedAmt).'</td>
                <td>'.array_sum($totalTaxAmt).'</td>
                <td>'.array_sum($totalPenaltyAmt).'</td>
                <td>'.array_sum($totalTaxPenalty).'</td>
                <td colspan="2" class="text-left"></td>
            </tr>
            ';
            $totalHtml='
            <tbody>
            <tr>
                <th colspan="5" rowspan="2" class="text-right" style="vertical-align:center;"><b>Total:</b></th>
                <th>> 12 Months</th>
                <th>> 6 Months</th>
                <th>< 6 months</th>
                <th>Total</th>
            </tr>
            <tr>
                <td>'.$greater12Months.'</td>
                <td>'.$greater6Months.'</td>
                <td>'.$less6Months.'</td>
                <td>'.($greater12Months+$greater6Months+$less6Months).'</td>
            </tr>
            ';
        } else {
            $fullHtml.='
            <tr class="animated fadeInDown">
                <td colspan="9">
                    <div class="alert alert-danger" role="alert">
                        No data found !
                    </div>
                </td>
            </tr>
            ';
        }        
        $getSummeryAuditHoursData=getData(Table::AUDIT_TIME_SPENT_DATA,[
            "SUM(".AUDIT_TIME_SPENT_DATA::TIME_IN_HRS.") as totalHrs",
            AUDIT_TIME_SPENT_DATA::COMPANY_ID
        ],[
            AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$auditor_id,
            "YEAR(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$year,
            "MONTH(".AUDIT_TIME_SPENT_DATA::DATE.")"=>$month,
            AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if (count($getSummeryAuditHoursData)>0) {
            $timeOwnAudit=$timeOtherAudit=$totalTime=0;
            foreach ($getSummeryAuditHoursData as $ashk => $ashv) {
                $getPrimSecAud=getData(Table::COMPANY_ASSIGNED_DATA,[
                    COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY
                ],[
                    COMPANY_ASSIGNED_DATA::AUDITOR_ID=>$auditor_id,
                    COMPANY_ASSIGNED_DATA::STATUS=>ACTIVE_STATUS,
                    COMPANY_ASSIGNED_DATA::COMPANY_IDS=>$ashv[AUDIT_TIME_SPENT_DATA::COMPANY_ID],
                ]);
                if (count($getPrimSecAud)>0) {
                    if ($getPrimSecAud[0][COMPANY_ASSIGNED_DATA::PRIMARY_SECONDARY]==1) {
                        $timeOwnAudit+=$ashv["totalHrs"];
                    } else {
                        $timeOtherAudit+=$ashv["totalHrs"];
                    }
                    $totalTime=($timeOwnAudit+$timeOtherAudit);
                }
            }
            $summeryHours='
            <tbody>
                <tr>
                    <th colspan="5" class="text-center" style="vertical-align:center;"><b>SUMMARY OF HOURS FOR MONTH</b></th>
                </tr>
                <tr>
                    <th colspan="2"><b>TIME ON OWN AUDIT:</b></th>
                    <th colspan="2"><b>TIME ON OTHER AUD CASES:</b></th>
                    <th colspan="1"><b>TOTAL TIME:</b></th>
                </tr>
                <tr>
                    <td colspan="2">'.$timeOwnAudit.'</td>
                    <td colspan="2">'.$timeOtherAudit.'</td>
                    <td colspan="1">'.$totalTime.'</td>
                </tr>
            </tbody>
            ';
        }
        $totalHtml.='
            </tbody>
            ';
        $response['error']=false;
        $response['fullHtml']=$fullHtml;
        $response['totalHtml']=$totalHtml;
        $response['summeryHours']=$summeryHours;
        $response['durrExactMonArr']=$durrExactMonArr;
        sendRes();
        break;
    case 'GET_AUDITOR_BACK_DATE_ATT_DETAILS':
        $date = isset($ajax_form_data['dt'])?altRealEscape($ajax_form_data['dt']):'';
        $otherActArr=$attArr=$auditClosed=[];
        $otherAct=$attRec=false;
        if ($date=="") {
            $response['message']=ERROR_1;
            sendRes();
        }
        if ($date>getToday(false)) {
            $response["message"]='You Cannot Select Date After ' . getFormattedDateTime(getToday(false),LONG_DATE_FORMAT);
            sendRes();
        }
        $getAttData=getData(Table::AUDIT_TIME_SPENT_DATA,[],[
            AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$_SESSION[RID],
            AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if(count($getAttData)>0){
            foreach ($getAttData as $ak => $av) {
                $attData = $av;
                $getAuditStat=getData(Table::AUDITS_DATA,[AUDITS_DATA::ACTIVE],[AUDITS_DATA::COMPANY_ID=>$attData[AUDIT_TIME_SPENT_DATA::COMPANY_ID]]);
                // rip($getAuditStat);
                if(count($getAuditStat)>0){
                    $auditClosed[$attData[AUDIT_TIME_SPENT_DATA::COMPANY_ID]]=$getAuditStat[0][AUDITS_DATA::ACTIVE];
                }
                if($attData[AUDIT_TIME_SPENT_DATA::DATE]==$date){
                    if (
                        (($attData[AUDIT_TIME_SPENT_DATA::LEAVE_HRS]!=0) ||
                        ($attData[AUDIT_TIME_SPENT_DATA::TRAINING_HRS]!=0) ||
                        ($attData[AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS]!=0)) &&
                        ($attData[AUDIT_TIME_SPENT_DATA::TIME_IN_HRS]==0)
                    ) {
                        $v = [
                            AUDIT_TIME_SPENT_DATA::LEAVE_HRS => $attData[AUDIT_TIME_SPENT_DATA::LEAVE_HRS],
                            AUDIT_TIME_SPENT_DATA::TRAINING_HRS => $attData[AUDIT_TIME_SPENT_DATA::TRAINING_HRS],
                            AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS => $attData[AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS]
                        ];
                        $otherActArr[AUDIT_TIME_SPENT_DATA::LEAVE_HRS]=$attData[AUDIT_TIME_SPENT_DATA::LEAVE_HRS];
                        $otherActArr[AUDIT_TIME_SPENT_DATA::TRAINING_HRS]=$attData[AUDIT_TIME_SPENT_DATA::TRAINING_HRS];
                        $otherActArr[AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS]=$attData[AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS];
                    } else {
                        $attArr[$attData[AUDIT_TIME_SPENT_DATA::COMPANY_ID]] = $attData[AUDIT_TIME_SPENT_DATA::TIME_IN_HRS];
                    }
                }
            }
            if(count($otherActArr)>0){
                $otherAct=true;
            }
            if(count($attArr)>0){
                $attRec=true;
            }
        }
        $response['error']=false;
        $response['auditClosed']=$auditClosed;
        $response['otherAct']=$otherAct;
        $response['attRec']=$attRec;
        $response['otherActArr']=$otherActArr;
        $response['attArr']=$attArr;
        $response['today']=getToday(false);
        sendRes();
        break;
    case 'SAVE_AUDITOR_OTHER_ACTIVITY':
        $date = isset($ajax_form_data['dt'])?altRealEscape($ajax_form_data['dt']):'';
        $leaveInput = isset($ajax_form_data['li'])?altRealEscape($ajax_form_data['li']):'';
        $TrainingInput = isset($ajax_form_data['ti'])?altRealEscape($ajax_form_data['ti']):'';
        $OtherDutyInput = isset($ajax_form_data['odi'])?altRealEscape($ajax_form_data['odi']):'';

        if (
            ($date=='') ||
            (
                ($leaveInput=='')&&
                ($TrainingInput=='')&&
                ($OtherDutyInput=='')
            )
        ) {
            $response['message']=ERROR_1;
            sendRes();
        }
        $getAttData=getData(Table::AUDIT_TIME_SPENT_DATA,[],[
            AUDIT_TIME_SPENT_DATA::AUDITOR_ID=>$_SESSION[RID],
            AUDIT_TIME_SPENT_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID],
            AUDIT_TIME_SPENT_DATA::DATE=>$date,
        ]);
        if (count($getAttData)>0) {
            $response['message']="Attendance has already been recorded !";
            sendRes();
        }
        $cols=[
            AUDIT_TIME_SPENT_DATA::CLIENT_ID => $_SESSION[CLIENT_ID],
            AUDIT_TIME_SPENT_DATA::COMPANY_ID => 0,
            AUDIT_TIME_SPENT_DATA::AUDIT_ID => 0,
            AUDIT_TIME_SPENT_DATA::AUDITOR_ID => $_SESSION[RID],
            AUDIT_TIME_SPENT_DATA::DATE => $date,
            AUDIT_TIME_SPENT_DATA::TIME_IN_HRS => 0,
            AUDIT_TIME_SPENT_DATA::CREATED_AT => getToday(),
            AUDIT_TIME_SPENT_DATA::UPDATED_AT => getToday()
        ];
        if($leaveInput!=''){
            $cols[AUDIT_TIME_SPENT_DATA::LEAVE_HRS]=$leaveInput;
        }
        if($TrainingInput!=''){
            $cols[AUDIT_TIME_SPENT_DATA::TRAINING_HRS]=$TrainingInput;
        }
        if($OtherDutyInput!=''){
            $cols[AUDIT_TIME_SPENT_DATA::OTHER_DUTY_HRS]=$OtherDutyInput;
        }
        $save=setData(Table::AUDIT_TIME_SPENT_DATA,$cols);
        if(!$save['res']){
            logError("Unabled to save Auditor attendance data [Other Activities], Auditor ID: ".$_SESSION[RID].", Date: ".$date, $save['error']);
            $response['message']=ERROR_1;
            sendRes();
        }
        $response['error']=false;
        $response['message']='saved successfully';
        sendRes();
        break;
    case 'EMP_SELF_PROFILE_UPDATE':
        $name=isset($ajax_form_data['nm'])?altRealEscape($ajax_form_data['nm']):"";
        $mobile=isset($ajax_form_data['mb'])?altRealEscape($ajax_form_data['mb']):"";
        $email=isset($ajax_form_data['em'])?altRealEscape($ajax_form_data['em']):"";
        if ($name==""||$email=""||$mobile="") {
            $response["message"]=EMPTY_FIELD_ALERT;
            sendRes();
        }
        if (in_array($_SESSION[RID],PERMITTED_USER_IDS)) {
            $response["message"]="You do not have the permission on this account";
            sendRes();
        }
        $checkIfActive=getData(Table::USERS,[
            Users::ACTIVE,
            Users::STATUS,
            Users::NAME,
            Users::EMAIL,
            Users::MOBILE
        ],[
            Users::ID=>$_SESSION[RID],
            Users::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if (count($checkIfActive)>0) {
            if ($checkIfActive[0][Users::ACTIVE]!=1) {
                $response["message"]="Currently you are not active.";
                $response['login'] = 0;
                sendRes();
            }
            if ($checkIfActive[0][Users::STATUS]!=ACTIVE_STATUS) {
                $response["message"]="You do not have this permission. Please contact your administrator";
                $response['login'] = 0;
                sendRes();
            }
            if ($checkIfActive[0][Users::NAME]==$name) {
                $response["message"]="Name already exists";
                sendRes();
            }
            if ($checkIfActive[0][Users::EMAIL]==$email) {
                $response["message"]="Email already exists";
                sendRes();
            }
            if ($checkIfActive[0][Users::MOBILE]==$mobile) {
                $response["message"]="Mobile already exists";
                sendRes();
            }
        }
        $update=updateData(Table::USERS,[
            Users::NAME=>$name,
            Users::EMAIL=>$email,
            Users::MOBILE=>$mobile
        ],[
            Users::ID=>$_SESSION[RID],
            Users::CLIENT_ID=>$_SESSION[CLIENT_ID]
        ]);
        if(!$update['res']){
            logError("Unabled to update self profile of the user: ".$_SESSION[RID],$update['error']);
            $response["message"]="Failed to update profile";
            sendRes();
        }
        $response['error']=false;
        $response['message']="Profile updated successfully !";
        sendRes();
        break;
    default:
        $response['message'] = "Invalid Request";
        sendRes();
        break;
}
