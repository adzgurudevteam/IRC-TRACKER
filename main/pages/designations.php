<?php 
function printContent() {

$getdname = getData(Table::DESIGNATIONS, [
    DESIGNATIONS::ID,
    DESIGNATIONS::DESIGNATION_TITLE,
    DESIGNATIONS::RESPONSIBILITIES,
    DESIGNATIONS::EXPERIENCE_REQUIRED,
    DESIGNATIONS::ACTIVE,
    DESIGNATIONS::LAST_UPDATE_DATE
],[
    DESIGNATIONS::CLIENT_ID => $_SESSION[CLIENT_ID],
    DESIGNATIONS::STATUS => ACTIVE_STATUS
]);
$tr = '';
if (count($getdname) > 0) {
    foreach ($getdname as $key => $des_value) {
     $sl = ($key + 1);
     $dname = altRealEscape($des_value[DESIGNATIONS::DESIGNATION_TITLE]);
     $dres = (!empty($des_value[DESIGNATIONS::RESPONSIBILITIES])) ? altRealEscape($des_value[DESIGNATIONS::RESPONSIBILITIES]) : "N/A";
     $dexp = (!empty($des_value[DESIGNATIONS::EXPERIENCE_REQUIRED])) ? altRealEscape($des_value[DESIGNATIONS::EXPERIENCE_REQUIRED]) : "N/A";
     $dact = (($des_value[DESIGNATIONS::ACTIVE]) == 1) ? "checked" : "";
     $action = '
     <div class="row">
        <div class="col-6 text-success" style="padding: 5px; cursor:pointer;" onclick="updateDesignation('. $des_value[DESIGNATIONS::ID] .',\''.$des_value[DESIGNATIONS::DESIGNATION_TITLE].'\');"><i class="far fa-edit"></i></div>
        <div class="col-6 text-danger cursor-pointer" onclick="initiateDelete('. $des_value[DESIGNATIONS::ID] .', \'designation\')" style="padding: 5px;"><i class="fas fa-trash-alt"></i></div>
     </div>
     ';

     $tr .= '<tr class="designation_row" id="designation_'.$des_value[DESIGNATIONS::ID].'">
     <td>'.$sl.'</td>
     <td>'.$dname.'</td>
     <td>'.$action.'</td>
 </tr>';
    }
} else {
    $tr = '<tr class="animated fadeInDown">
    <td colspan="3">
        <div class="alert alert-danger" role="alert">
            No Designation found !
        </div>
    </td>
    </tr>';
}
?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 text-center">
        <h2 class="text-center">Designations</h2>
    </div>
    <!-- <div class="col-sm-2 col-md-2 col-lg-2 text-right" id="list_nav_btn">
        <button class="btn btn-primary" name="list" onclick="changeNavigateBtn('designation');">View List</button>
    </div> -->
</div>

<div class="card mt-5">
    <?=getSpinner(true, "desig_loader");?>
    <div class="card-body" style="padding: 15px;">
        <div class="row" id="add_designation_row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <h5 class="text-center font-weight-bold">Add / Update Designations</h5>
                <fieldset class="fldset mt-5">
                    <legend>Create Designation</legend>
                <div class="row mt-4">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="form-outline">
                            <label class="form_label" for="desig_name">Designation Title</label><?=getAsterics();?>
                            <input type="text" id="desig_name" class="form-control" placeholder="Designation Name..." onkeypress="enterEventListner($(this), $('#designation_submit'));"/>
                        </div>
                    </div>
                    <input type="hidden" id="designation_update_id" value="0" style="visibility: hidden; display:none;" />
                    <div class="col-md-12 col-lg-12 col-sm-12 text-right pt-4 action_btn">
                        <button type="button" class="btn btn-success sbmt mt-4" data-action="designation" id="designation_submit">
                            <i class="fas fa-plus"></i>&nbsp;Add
                        </button>
                        <button class="btn btn-sm btn-success" type="button" id="update_designation_btn" style="display: none;"><i class="far fa-edit"></i>&nbsp;Update</button>
                        <button class="btn btn-sm btn-secondary" type="button" id="cancel_designation_btn" style="display: none;" onclick="location.reload();"><i class="fas fa-window-close"></i>&nbsp;Cancel</button>
                    </div>
                    <!-- <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-outline">
                            <label class="form_label" for="desig_responcibilities">Responsibilities</label>
                            <input type="text" class="form-control" id="desig_responcibilities" onkeypress="enterEventListner($(this), $('#designation_submit'));">
                        </div>
                    </div> -->
                    <div class="col-lg-12 col-md-12 col-sm-12 d-none">
                        <div class="form-outline">
                            <label class="form_label" for="desig_exp">Experience Required</label>
                            <input type="text" id="desig_exp" class="form-control" placeholder="Format: XX Years XX Months" onkeypress="enterEventListner($(this), $('#designation_submit'));"/>
                        </div>
                    </div>
                </div>
                <div class="row mt-3 d-none">
                    <div class="col-lg-6 col-md-6 col-sm-12 noselect">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" checked class="custom-control-input" id="designation_active">
                            <label class="custom-control-label text-success" for="designation_active">Active</label>
                        </div>
                    </div>
                </div>
                </fieldset>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-12">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover text-center designation_table data-table" style="font-size:14px;">
                        <thead class="text-center table-warning">
                            <tr style="text-transform: uppercase;">
                                <th>SL.</th>
                                <th>Designation Title</th>
                                <!-- <th>Designation Responsibility</th>
                                <th>Required Experience Duration</th> -->
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=$tr;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row" id="view_designation_row" style="display: none;">
        </div>
    </div>
</div>
<?php } ?>