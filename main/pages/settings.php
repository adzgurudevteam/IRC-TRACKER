<?php
function printContent()
{
    $lastUpdatedTime='NEVER';
    $getLastUpdatedTime=getData(Table::USERS,[Users::PASSWORD_UPDATED_AT],[Users::ID=>$_SESSION[RID],Users::CLIENT_ID=>$_SESSION[CLIENT_ID]]);
    if (count($getLastUpdatedTime)>0) {
        $lastUpdatedTime=($getLastUpdatedTime[0][Users::PASSWORD_UPDATED_AT]!=null)?getFormattedDateTime($getLastUpdatedTime[0][Users::PASSWORD_UPDATED_AT],LONG_DATE_TIME_FORMAT):$lastUpdatedTime;
    }
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 text-center">
        <h4 class="text-center main_heading">Settings</h4>
        <h6 class="text-center sub_heading">Manage your account settings here</h6>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 text-right">
        <span style="font-weight: bold;">Last Updated On : <span class="text-primary"><?=$lastUpdatedTime;?></span></span>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <?php getSpinner(true,'SettingLoader'); ?>
        <h4 class="mt-3">Account Settings</h4>
        <small id="passwordHelpInline" class="text-muted">
            <i class="text-danger">*</i>&nbsp; Passwords Must be 8-20 characters long.
        </small>
        <div class="row mt-3">
            <div class="col-lg-2 col-md-2 col-sm-12">
                <!-- <label for="user_old_pass" class="noselect" style="width: max-content;">Old Password</label> -->
                <label for="user_old_pass" class="noselect pass_label">Old Password</label>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text togglePassword" onclick="togglePassword($(this))">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control border-tr-radius-0 border-br-radius-0" id="user_old_pass" autocomplete="new-password" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3 mb-3">
            <div class="col-lg-2 col-md-2 col-sm-12">
                <label for="user_pass" class="noselect pass_label">New Password</label>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text togglePassword" onclick="togglePassword($(this))">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control border-tr-radius-0 border-br-radius-0" id="user_pass" autocomplete="new-password" />
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-12">
                <label for="user_cpass" class="noselect pass_label">Confirm New Password</label>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text togglePassword" onclick="togglePassword($(this))">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control border-tr-radius-0 border-br-radius-0" id="user_cpass" autocomplete="new-password" />
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-12 text-right">
                <button class="btn btn-sm btn-primary" id="ChangeUserPassword"><i class="fas fa-history"></i>&nbsp;<small>Change Password</small></button>
            </div>
        </div>
    </div>
</div>

<?php } ?>