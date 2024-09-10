<?php 
function printContent() {
    $to = ADMIN_USERNAME;
    $name = 'Jsaha';
    $subject = 'Test Mail';
    $body = 'Hi! this is a test mail';
    $alt_body = $body;
    // if (simple_mail($to, $name, $body)) {
    //     echo "Mail sent !";
    // } else {
    //     echo "Mail not sent !";
    // }
    // echo '<br>';




    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    // use PHPMailer\PHPMailer\PHPMailer;
    // use PHPMailer\PHPMailer\SMTP;
    // use PHPMailer\PHPMailer\Exception;
    
    // require_once BASE_DIR. 'vendor/phpmailer/phpmailer/src/Exception.php';
    // require_once BASE_DIR. 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
    // require_once BASE_DIR. 'vendor/phpmailer/phpmailer/src/SMTP.php';
    // // require_once BASE_DIR. 'vendor/autoload.php';

    // //Load Composer's autoloader
    // require 'vendor/autoload.php';

    
    

    // //Create an instance; passing `true` enables exceptions
    // $mail = new PHPMailer\PHPMailer\PHPMailer;
    // try {
    //     //Server settings
    //     // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    //     if (DEBUG_MAIL) {
    //         $mail->SMTPDebug = 2;
    //     }
    //     $mail->isSMTP();                                            //Send using SMTP
    //     $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    //     $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    //     $mail->Username   = ADMIN_USERNAME;                     //SMTP username
    //     $mail->Password   = ADMIN_PASSWORD;                               //SMTP password
    //     $mail->SMTPSecure = "ssl";                         //Enable implicit TLS encryption
    //     // $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;                                  //Enable implicit TLS encryption
    //     $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //     //Recipients
    //     $mail->setFrom(ADMIN_USERNAME, COMPANY_BUSINESS_NAME);
    //     $mail->addAddress($to, $name);                          //Add a recipient
    //     // $mail->addAddress('ellen@example.com');               //Name is optional
    //     // $mail->addReplyTo('info@example.com', 'Information');
    //     // $mail->addCC('cc@example.com');
    //     // $mail->addBCC('bcc@example.com');

    //     //Attachments
    //     // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //     // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //     //Content
    //     $mail->isHTML(true);                                  //Set email format to HTML
    //     $mail->Subject = 'Here is the subject';
    //     $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    //     $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    //     // $mail->send();
    //     if ($mail->send()) {            
    //         echo 'Message has been sent';
    //     } else {
    //         logError("Sending Mail: ", $subject . " :- " . $mail->ErrorInfo);
    //         echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    //     }
    // } catch (Exception $e) {
    //     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    // }
    // rip($_SESSION);
    // exit;
?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 text-center">
        <h4 class="text-center main_heading">My Profile</h4>
        <h6 class="text-center sub_heading">View / Modify Your Profile Details here</h6>
    </div>
</div>

<div class="card mt-5">
    <div class="card_body" style="padding: 25px;">
    <?php 
    getSpinner(true, "profile_loader");
    if (($_SESSION[USER_TYPE] == ADMIN) || ($_SESSION[USER_TYPE] == SADMIN) || ($_SESSION[USER_TYPE] == IT_ADMIN)) {
        $getData = getData(Table::USERS,[
            Users::NAME,
            Users::EMAIL,
            Users::MOBILE
        ], [
            Users::ID => $_SESSION[RID],
            Users::CLIENT_ID => $_SESSION[CLIENT_ID],
            USERS::STATUS => ACTIVE_STATUS,
            USERS::ACTIVE => 1
        ]);
        if (count($getData)>0) {
            $data = $getData[0];
            $name = (!empty($data[Users::NAME])) ? altRealEscape($data[Users::NAME]) : '';
            $email = (!empty($data[Users::EMAIL])) ? altRealEscape($data[Users::EMAIL]) : '';
            $mobile = (!empty($data[Users::MOBILE])) ? altRealEscape($data[Users::MOBILE]) : '';
        }
?>
    <fieldset class="fldset mt-3 mb-5">
        <legend>User Details</legend>
        <div class="row">
            <div class="col-6 text-left">
                User Type: <span class="text-primary" style="font-weight: bold;"><?=($_SESSION[USER_TYPE]==IT_ADMIN)?'IT ADMIN':USERS[$_SESSION[USER_TYPE]]?></span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3 col-lg-3 col-sm-12 text-right">
                <label class="form_label" for="admin_profile_name">Name: </label>
            </div>
            <div class="col-md-9 col-lg-9 col-sm-12">
                <input type="text" class="form-control" id="admin_profile_name" value="<?=$name?>"/>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3 col-lg-3 col-sm-12 text-right">
                <label class="form_label" for="admin_profile_email">Email: </label>
            </div>
            <div class="col-md-9 col-lg-9 col-sm-12">
                <input type="email" class="form-control" id="admin_profile_email" value="<?=$email?>"/>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3 col-lg-3 col-sm-12 text-right">
                <label class="form_label" for="admin_profile_mobile">Mobile: </label>
            </div>
            <div class="col-md-9 col-lg-9 col-sm-12">
                <input type="text" class="form-control" id="admin_profile_mobile" value="<?=$mobile?>"/>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 col-lg-12 col-sm-12 text-right mt-3">
                <button class="btn btn-sm btn-success" type="button" id="admin_profile_update"><i class="fas fa-history"></i>&nbsp;Update</button>
            </div>
        </div>
    </fieldset>
<?php
    }
    switch ($_SESSION[USER_TYPE]) {
        case EMPLOYEE:
        case MANAGER:
        case ADMIN:
            $name = $emp_id = $designation = $department = $date_of_joinning = $salary = "";
            $payroll = $webmail = $personal_contact = $personal_email = $blood_group = "";
            $father_name = $mother_name = $current_address = $permanent_address = "";
            $emergency_person_name = $emergency_person_number = $aadhaar_number = "";
            $pan_number = $salary_account_number = $salary_account_ifsc = $uan_number = "";
            $esic_number = $reporting_time = $reporting_manager = "";
            $getData = getData(Table::EMPLOYEE_DETAILS,['*'],[
                EMPLOYEE_DETAILS::ID => $_SESSION[EMPLOYEE_ID],
                EMPLOYEE_DETAILS::CLIENT_ID => $_SESSION[CLIENT_ID],
                EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS
            ]);
            if (count($getData)>0) {
                $data = $getData[0];
                $getDesig = getData(Table::DESIGNATIONS,[DESIGNATIONS::DESIGNATION_TITLE],[
                    DESIGNATIONS::ID => $data[EMPLOYEE_DETAILS::EMPLOYEE_DESIGNATION_ID],
                    DESIGNATIONS::CLIENT_ID => $_SESSION[CLIENT_ID],
                    DESIGNATIONS::STATUS => ACTIVE_STATUS
                ]);
                $getDepartment = getData(Table::DEPARTMENTS,[DEPARTMENTS::DEPARTMENT_NAME],[
                    DEPARTMENTS::ID => $data[EMPLOYEE_DETAILS::DEPARTMENT_ID],
                    DEPARTMENTS::CLIENT_ID => $_SESSION[CLIENT_ID],
                    DEPARTMENTS::STATUS => ACTIVE_STATUS
                ]);
                $getReportManager = getData(Table::EMPLOYEE_REPORTING_MANAGER,[
                    EMPLOYEE_REPORTING_MANAGER::REPORTING_MANAGER_USER_ID
                ], [
                    EMPLOYEE_REPORTING_MANAGER::EMPLOYEE_ID => $_SESSION[EMPLOYEE_ID],
                    EMPLOYEE_REPORTING_MANAGER::CLIENT_ID => $_SESSION[CLIENT_ID],
                    EMPLOYEE_REPORTING_MANAGER::STATUS => ACTIVE_STATUS
                ]);
                if (count($getReportManager)>0) {
                    $reporting_manager_user_id = $getReportManager[0][EMPLOYEE_REPORTING_MANAGER::REPORTING_MANAGER_USER_ID];
                    $getReportingManager = getData(Table::USERS, [
                        Users::NAME,
                        USERS::USER_TYPE
                    ], [
                        Users::ID => $reporting_manager_user_id,
                        Users::ACTIVE => 1,
                        Users::STATUS => ACTIVE_STATUS,
                        Users::CLIENT_ID => $_SESSION[CLIENT_ID]
                    ]);
                    if (count($getReportingManager)>0) {
                        $reporting_manager = (!empty($getReportingManager[0][Users::NAME])) ? ((!empty($getReportingManager[0][Users::USER_TYPE])) ? $getReportingManager[0][Users::NAME]." [". USERS[$getReportingManager[0][Users::USER_TYPE]]."]" : $getReportingManager[0][Users::NAME]) : EMPTY_VALUE;
                    }
                }
                $name = altRealEscape($data[EMPLOYEE_DETAILS::EMPLOYEE_NAME]);
                $emp_id = EMPLOYEE_ID_PREFIX.$data[EMPLOYEE_DETAILS::EMPLOYEE_ID];
                $designation = ((count($getDesig)>0) && (!empty($getDesig[0][DESIGNATIONS::DESIGNATION_TITLE]))) ? altRealEscape($getDesig[0][DESIGNATIONS::DESIGNATION_TITLE]) : EMPTY_VALUE;
                $department = ((count($getDepartment)>0) && (!empty($getDepartment[0][DEPARTMENTS::DEPARTMENT_NAME]))) ? altRealEscape($getDepartment[0][DEPARTMENTS::DEPARTMENT_NAME]) : EMPTY_VALUE;
                $date_of_joinning = (!empty($data[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING])) ? getFormattedDateTime($data[EMPLOYEE_DETAILS::EMPLOYEE_DATE_OF_JOINNING], LONG_DATE_FORMAT) : EMPTY_VALUE;
                $salary = (!empty($data[EMPLOYEE_DETAILS::SALARY_AMOUNT])) ? moneyFormatIndia($data[EMPLOYEE_DETAILS::SALARY_AMOUNT]) : DEFAULT_AMOUNT;
                $payroll = ((!empty($data[EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL])) || ($data[EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL] != 0)) ? PAYROLL_OPTIONS[$data[EMPLOYEE_DETAILS::EMPLOYEE_PAYROLL]] : EMPTY_VALUE;
                $webmail = (!empty($data[EMPLOYEE_DETAILS::WEBMAIL_ADDRESS])) ? altRealEscape($data[EMPLOYEE_DETAILS::WEBMAIL_ADDRESS]) : EMPTY_VALUE;
                $personal_contact = (!empty($data[EMPLOYEE_DETAILS::EMPLOYEE_MOBILE])) ? altRealEscape($data[EMPLOYEE_DETAILS::EMPLOYEE_MOBILE]) : EMPTY_VALUE;
                $personal_email = (!empty($data[EMPLOYEE_DETAILS::EMPLOYEE_EMAIL])) ? altRealEscape($data[EMPLOYEE_DETAILS::EMPLOYEE_EMAIL]) : EMPTY_VALUE;
                $blood_group = (!empty($data[EMPLOYEE_DETAILS::EMPLOYEE_BLOOD_GROUP])) ? altRealEscape($data[EMPLOYEE_DETAILS::EMPLOYEE_BLOOD_GROUP]) : EMPTY_VALUE;
                $father_name = (!empty($data[EMPLOYEE_DETAILS::EMPLOYEE_FATHER_NAME])) ? altRealEscape($data[EMPLOYEE_DETAILS::EMPLOYEE_FATHER_NAME]) : EMPTY_VALUE;
                $mother_name = (!empty($data[EMPLOYEE_DETAILS::EMPLOYEE_MOTHER_NAME])) ? altRealEscape($data[EMPLOYEE_DETAILS::EMPLOYEE_MOTHER_NAME]) : EMPTY_VALUE;
                $current_address = (!empty($data[EMPLOYEE_DETAILS::CURRENT_ADDRESS])) ? altRealEscape($data[EMPLOYEE_DETAILS::CURRENT_ADDRESS]) : EMPTY_VALUE;
                $permanent_address = (!empty($data[EMPLOYEE_DETAILS::PERMANENT_ADDRESS])) ? altRealEscape($data[EMPLOYEE_DETAILS::PERMANENT_ADDRESS]) : EMPTY_VALUE;
                $emergency_person_name = (!empty($data[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME])) ? altRealEscape($data[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME]) : EMPTY_VALUE;
                $emergency_person_number = (!empty($data[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_MOBILE_NUMBER])) ? altRealEscape($data[EMPLOYEE_DETAILS::EMERGENCY_CONTACT_PERSON_NAME]) : EMPTY_VALUE;
                $aadhaar_number = (!empty($data[EMPLOYEE_DETAILS::AADHAAR_NUMBER])) ? altRealEscape($data[EMPLOYEE_DETAILS::AADHAAR_NUMBER]) : EMPTY_VALUE;
                $pan_number = (!empty($data[EMPLOYEE_DETAILS::PAN_NUMBER])) ? altRealEscape($data[EMPLOYEE_DETAILS::PAN_NUMBER]) : EMPTY_VALUE;
                $salary_account_number = (!empty($data[EMPLOYEE_DETAILS::SALARY_ACCOUNT_NUMBER])) ? altRealEscape($data[EMPLOYEE_DETAILS::SALARY_ACCOUNT_NUMBER]) : EMPTY_VALUE;
                $salary_account_ifsc = (!empty($data[EMPLOYEE_DETAILS::SALARY_ACCOUNT_IFSC_CODE])) ? altRealEscape($data[EMPLOYEE_DETAILS::SALARY_ACCOUNT_IFSC_CODE]) : EMPTY_VALUE;
                $uan_number = (!empty($data[EMPLOYEE_DETAILS::UAN_NUMBER])) ? altRealEscape($data[EMPLOYEE_DETAILS::UAN_NUMBER]) : EMPTY_VALUE;
                $esic_number = (!empty($data[EMPLOYEE_DETAILS::ESIC_IP_NUMBER])) ? altRealEscape($data[EMPLOYEE_DETAILS::ESIC_IP_NUMBER]) : EMPTY_VALUE;
                $reporting_time = (!empty($data[EMPLOYEE_DETAILS::REPORTING_TIME])) ? $data[EMPLOYEE_DETAILS::REPORTING_TIME] : EMPTY_VALUE;
                $reporting_time .= ($reporting_time == '12') ? ' PM' : ' AM';
            }
            getSpinner(true, "audAdminSelfProfileLoader");
    ?>
        <div class="row">
            <div class="col-md-4 col-lg-4 col-sm-12">
                <label class="form_label" for="audAdmin_name">Name: </label><?=getAsterics();?>
                <input type="text" class="form-control" id="audAdmin_name" value="<?=$name?>">
            </div>
            <div class="col-md-4 col-lg-4 col-sm-12">
                <label class="form_label" for="audAdmin_desig">Designation: </label>
                <input type="text" disabled class="form-control <?=TOOLTIP_CLASS?>" id="audAdmin_desig" value="<?=$designation?>" title="<?=$designation?>" />
            </div>
            <div class="col-md-4 col-lg-4 col-sm-12">
                <label class="form_label" for="audAdmin_dept">Department: </label>
                <input type="text" disabled class="form-control <?=TOOLTIP_CLASS?>" id="audAdmin_dept" value="<?=$department?>" title="<?=$department?>" />
            </div>
        </div>
        <div class="row d-none">
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Date of Joinning: </label>
                <input type="text" disabled class="form-control" value="<?=$date_of_joinning?>">
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Salary: </label>
                <input type="text" disabled class="form-control" value="<?=$salary?>">
            </div>
        </div>
        <div class="row d-none">
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Payroll: </label>
                <input type="text" disabled class="form-control" value="<?=$payroll?>">
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Web Mail: </label>
                <input type="text" disabled class="form-control" value="<?=$webmail?>">
            </div>
        </div>
        <fieldset class="fldset mt-3">
            <legend>Personal Details</legend>
        <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12">
                <label class="form_label" for="audAdmin_mobile">Contact Number: </label>
                <input type="text" class="form-control" id="audAdmin_mobile" value="<?=$personal_contact?>">
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12">
                <label class="form_label" for="audAdmin_email">Email: </label>
                <input type="text" class="form-control" id="audAdmin_email" value="<?=$personal_email?>">
            </div>
            <div class="col-md-4 col-lg-4 col-sm-12 d-none">
                <label class="form_label">Blood Group: </label>
                <input type="text" disabled class="form-control" value="<?=$blood_group?>">
            </div>
        </div>
        <div class="row d-none">
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Father Name: </label>
                <input type="text" disabled class="form-control" value="<?=$father_name?>">
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Mother Name: </label>
                <input type="text" disabled class="form-control" value="<?=$mother_name?>">
            </div>
        </div>
        <div class="row d-none">
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Current Address: </label>
                <textarea disabled class="form-control"><?=$current_address?></textarea>
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Permanent Address: </label>
                <textarea type="text" disabled class="form-control"><?=$permanent_address?></textarea>
            </div>
        </div>
        <div class="row d-none">
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Emergency Contact Person: </label>
                <input type="text" disabled class="form-control" value="<?=$emergency_person_name?>">
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                <label class="form_label">Emergency Contact Person's Contact: </label>
                <input type="text" disabled class="form-control" value="<?=$emergency_person_number?>">
            </div>
        </div>
        </fieldset>
        <fieldset class="fldset mt-3">
            <legend>Identity Details</legend>
            <div class="row">
                <div class="col-md-12 col-lg-12 col-sm-12">
                    <label class="form_label" for="audAdmin_emp_id">Employee ID: </label>
                    <input type="text" disabled class="form-control" id="audAdmin_emp_id" value="<?=$emp_id?>">
                </div>
                <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                    <label class="form_label">Aadhaar Number: </label>
                    <input type="text" disabled class="form-control" value="<?=$aadhaar_number?>">
                </div>
                <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                    <label class="form_label">PAN Number: </label>
                    <input type="text" disabled class="form-control" value="<?=$pan_number?>">
                </div>
            </div>
        </fieldset>
        <fieldset class="fldset mt-3 d-none">
            <legend>Bank & PF</legend>
            <div class="row">
                <div class="col-md-6 col-lg-6 col-sm-12">
                    <label class="form_label">Salary Account Number: </label>
                    <input type="text" disabled class="form-control" value="<?=$salary_account_number?>">
                </div>
                <div class="col-md-6 col-lg-6 col-sm-12">
                    <label class="form_label">Salary Account IFSC: </label>
                    <input type="text" disabled class="form-control" value="<?=$salary_account_ifsc?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-6 col-sm-12">
                    <label class="form_label">UAN Number: </label>
                    <input type="text" disabled class="form-control" value="<?=$uan_number?>">
                </div>
                <div class="col-md-6 col-lg-6 col-sm-12">
                    <label class="form_label">ESIC IP Number: </label>
                    <input type="text" disabled class="form-control" value="<?=$esic_number?>">
                </div>
            </div>
        </fieldset>
        <fieldset class="fldset mt-3 d-none">
            <legend>Reporting</legend>
            <div class="row">
                <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                    <label class="form_label">Reporting Manager</label>
                    <input type="text" class="form-control" value="<?=$reporting_manager;?>" readonly />
                </div>
                <div class="col-md-6 col-lg-6 col-sm-12 d-none">
                    <label class="form_label">Reporting Time</label>
                    <input type="text" class="form-control" value="<?=$reporting_time;?>" readonly />
                </div>
            </div>
        </fieldset>
        <div class="row mt-2">
            <div class="col-md-12 col-lg-12 col-sm-12 text-right">
                <button type="button" class="btn btn-sm btn-primary" id="empProfileUpdate"><i class="fas fa-history"></i>&nbsp;Update</button>
            </div>
        </div>
    <?php
            break;
    }
    ?>
    </div>
</div>
<?php } ?>