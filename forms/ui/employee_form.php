<div class="row">
    <div class="col-md-6 col-lg-6 col-sm-12">
        <div class="form-outline">
            <label class="form_label" for="emp_department">Select Department</label>
            <select id="emp_department" class="form-control">
                <?= $departments; ?>
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-sm-12">
        <div class="form-outline">
            <label class="form_label" for="emp_desig_id">Select Designation</label>
            <select id="emp_desig_id" class="form-control">
                <?= $designations; ?>
            </select>
        </div>
    </div>
</div>
<div class="row mt-2 d-none">
    <div class="col-md-4 col-lg-4 col-sm-12 d-none">
        <div class="form-outline">
            <label class="form_label" for="emp_date_of_join">Date of Joinning</label>
            <input type="date" id="emp_date_of_join" class="form-control" value="<?= getToday(false); ?>" />
        </div>
    </div>
    <div class="col-md-4 col-lg-4 col-sm-12 d-none">
        <div class="form-outline">
            <label class="form_label" for="emp_salary">Salary</label>
            <input type="text" class="form-control text-right" id="emp_salary" value="0.00" />
        </div>
    </div>
    <div class="col-md-4 col-lg-4 col-sm-12 d-none">
        <div class="form-outline">
            <label class="form_label" for="emp_webmail">Web Mail</label>
            <input type="email" class="form-control" id="emp_webmail" value="" placeholder="name@paperlinksoftwares.com" />
        </div>
    </div>
</div>
<div class="row mt-2 d-none">
    <div class="col-md-6 col-lg-6 col-sm-12 d-none">
        <div class="form-outline">
            <label class="form_label" for="emp_payroll">Select Payroll</label>
            <select id="emp_payroll" class="form-control">
                <option value="<?= COMPANY_PAYROLL ?>">Company Payroll</option>
                <option value="<?= CONTRACT_PAYROLL ?>">Contract</option>
            </select>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-sm-12 d-none">
        <div class="form-outline">
            <label class="form_label" for="emp_exp">Experience</label>
            <input type="text" class="form-control" id="emp_exp" placeholder="XX Years XX Months" />
        </div>
    </div>
</div>
<fieldset class="fldset mt-3">
    <legend>Personal Details</legend>
    <div class="row mt-2">
        <div class="col-md-4 col-lg-4 col-sm-12">
            <div class="form-outline">
                <label class="form_label" for="emp_name">Employee Name</label><?= getAsterics(); ?>
                <input type="text" id="emp_name" class="form-control" />
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-sm-12">
            <div class="form-outline">
                <label class="form_label" for="emp_mobile">Contact Number</label><?= getAsterics(); ?>
                <input type="text" id="emp_mobile" class="form-control" />
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-sm-12">
            <div class="form-outline">
                <label class="form_label" for="emp_email">Email</label><?= getAsterics(); ?>
                <input type="email" id="emp_email" class="form-control" />
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="emp_date_of_birth">Date of Birth</label>
                <input type="date" id="emp_date_of_birth" class="form-control" />
            </div>
        </div>
    </div>
    <div class="row mt-2 d-none">
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="emp_mother_name">Mother Name</label>
                <input type="text" id="emp_mother_name" class="form-control" />
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="emp_father_name">Father Name</label>
                <input type="text" id="emp_father_name" class="form-control" />
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-4 col-lg-4 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="blood_group">Blood Group</label>
                <input type="text" id="blood_group" class="form-control" />
            </div>
        </div>
    </div>
    <div class="row mt-2 d-none">
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="emergeny_contact_name">Emergency Contact Person Name</label>
                <input type="text" id="emergeny_contact_name" class="form-control" />
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="emergeny_contact_mobile">Emergency Contact Person Mobile</label>
                <input type="text" id="emergeny_contact_mobile" class="form-control" />
            </div>
        </div>
    </div>
    <div class="row mt-2 d-none">
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="current_address">Current Address</label>
                <input type="text" id="current_address" class="form-control" />
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="permanent_address">Permanent Address</label>
                <input type="text" id="permanent_address" class="form-control" />
            </div>
        </div>
    </div>
    <div class="row mt-2 d-none">
    <div class="col-md-6 col-lg-6 col-sm-12 d-none">
        <!-- <input type="checkbox" id="address_samem_check" class="form-control-sm"/>
        <label class="form_label" for="address_samem_check"></label> -->
        <div class="form-group form-check noselect">
            <input type="checkbox" class="form-check-input cursor-pointer" id="address_samem_check">
            <label class="form-check-label cursor-pointer" for="address_samem_check"><small>Is Permanent Address Same as Current Address</small></label>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-sm-12 d-none"></div>
    </div>
</fieldset>
<fieldset class="fldset mt-3">
    <legend>Identity Details</legend>
    <div class="row mt-2">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="form-outline">
                <label class="form_label" for="emp_id"><?=COMPANY_NAME?> Employee ID</label><?= getAsterics(); ?>
                <!-- <input type="text" id="emp_id" class="form-control" /> -->
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?= EMPLOYEE_ID_PREFIX ?></span>
                </div>
                <input type="text" class="form-control" placeholder="00123..." value="" aria-label="Username" aria-describedby="basic-addon1" id="emp_id">
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="emp_aadhaar">Aadhaar Number</label>
                <input type="text" id="emp_aadhaar" class="form-control" />
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12 d-none">
            <div class="form-outline">
                <label class="form_label" for="emp_pan">PAN Number</label>
                <input type="text" id="emp_pan" class="form-control" />
            </div>
        </div>
    </div>
</fieldset>
<fieldset class="fldset mt-3 d-none">
    <legend>Bank & EPF</legend>
    <div class="row mt-2">
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="form-outline">
                <label class="form_label" for="emp_salary_ac_number">Salary Account Number</label>
                <input type="text" id="emp_salary_ac_number" class="form-control" />
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="form-outline">
                <label class="form_label" for="emp_salary_ac_ifsc">Salary Account IFSC</label>
                <input type="text" id="emp_salary_ac_ifsc" class="form-control" />
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="form-outline">
                <label class="form_label" for="emp_uan">UAN Number</label>
                <input type="text" id="emp_uan" class="form-control" />
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="form-outline">
                <label class="form_label" for="emp_esic_ip_number">ESIC IP Number</label>
                <input type="text" id="emp_esic_ip_number" class="form-control" />
            </div>
        </div>
    </div>
</fieldset>
<fieldset class="fldset mt-3">
    <legend>User Details</legend>
    <div class="row d-none">
        <div class="col-md-6 col-lg-6 col-sm-12">
            <label class="form_label" for="employee_report_manager">Select Reporting Manager</label><?=getAsterics();?>
            <select class="form-control" id="employee_report_manager">
                <?=$reporting_manager;?>
            </select>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12">
            <label class="form_label" for="employee_report_time">Assign Reporting Time</label><?=getAsterics();?>
            <select class="form-control" id="employee_report_time">
                <?=$reporting_times;?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-6 col-sm-12">
            <label class="form_label">User Type</label><?=getAsterics();?>
            <select class="form-control" id="employee_user_type" <?php if($_SESSION[USER_TYPE]==ADMIN): ?> disabled <?php endif; ?>>
                <option value="0" disabled>--- Select User Type ---</option>
                <?=$user_types;?>
            </select>
        </div>
        <div class="col-md-6 col-lg-6 col-sm-12 empFormPasswordSec">
            <label class="form_label" for="user_password">Set Password</label><?=getAsterics();?>
            <input type="text" class="form-control" id="user_password" placeholder="Type New Password..." />
            <small class="text-muted">
                <b>*</b>&nbsp;<i class="text-danger">Password is Case Sensitive.</i>
            </small>
        </div>
    </div>
</fieldset>
<div class="row mt-2 d-none">
    <div class="col-12">
        <label class="form_label" for="emp_remarks">Remarks</label>
        <input type="text" class="form-control" id="emp_remarks" />
    </div>
</div>