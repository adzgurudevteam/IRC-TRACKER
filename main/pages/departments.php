<?php 
function printContent() {
    $dept_tr = "";
    $getDepartmentData = getData(Table::DEPARTMENTS, [
        DEPARTMENTS::DEPARTMENT_NAME,
        DEPARTMENTS::ID
    ], [
        DEPARTMENTS::CLIENT_ID => $_SESSION[CLIENT_ID],
        DEPARTMENTS::STATUS => ACTIVE_STATUS
    ]);
    if (count($getDepartmentData)>0) {
        foreach ($getDepartmentData as $key => $v) {
            $slno = ($key + 1);
            $dname = ((!empty($v[DEPARTMENTS::DEPARTMENT_NAME])) || ($v[DEPARTMENTS::DEPARTMENT_NAME] != "")) ? altRealEscape(ucwords($v[DEPARTMENTS::DEPARTMENT_NAME])) : EMPTY_VALUE;
            $action = '
            <div class="" style="display:flex; justify-content: space-evenly;">
                <div class="text-success" onclick="updateDepartment('. $v[DEPARTMENTS::ID] .',\''.$v[DEPARTMENTS::DEPARTMENT_NAME].'\');"><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
                <div class="text-danger cursor-pointer" onclick="initiateDelete('. $v[DEPARTMENTS::ID] .', \'department\')" style="padding: 5px; display:flex; justify-content: center;">
                    <i class="fas fa-trash-alt cursor-pointer"></i>
                </div>
            </div>
            ';

            $dept_tr .= '<tr class="department_row" id="department_'.$v[DEPARTMENTS::ID].'">
            <td>'.$slno.'</td>
            <td>'.$dname.'</td>
            <td>'.$action.'</td>
        </tr>';
        }
    } else {
        $dept_tr = '<tr class="animated fadeInDown">
        <td colspan="3">
            <div class="alert alert-danger" role="alert">
                No Departments found !
            </div>
        </td>
        </tr>';
    }
?>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 text-center">
        <h2 class="text-center">Departments</h2>
    </div>
</div>

<div class="card mt-5">
    <?=getSpinner(true, "department_loader");?>
    <div class="card_body" style="padding: 15px;">
        <div class="row">
            <div class="col-md-4 col-lg-4 col-sm-12">
            <h5 class="text-left font-weight-bold">Add / Update Departments</h5>
                <?=getSpinner(true, 'deptLoader')?>
            <fieldset class="fldset mt-5">
                <legend>Create Departments</legend>
                <div class="row pt-2">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <input type="text" class="form-control" name="Department Name" id="department_name" onkeypress="enterEventListner($(this), $('#department_submit'));" placeholder="Write Department Name here..."/>
                    </div>
                    <input type="hidden" id="department_update_id" value="0" style="visibility: hidden; display:none;" />
                    <div class="col-md-12 col-lg-12 col-sm-12 text-right pt-4 action_btn">
                        <button class="btn btn-sm btn-primary" type="button" id="department_submit"><i class="fas fa-plus"></i>&nbsp;Add</button>
                        <button class="btn btn-sm btn-success" type="button" id="update_department_btn" style="display: none;"><i class="far fa-edit"></i>&nbsp;Update</button>
                        <button class="btn btn-sm btn-secondary" type="button" id="cancel_department_btn" style="display: none;" onclick="location.reload();"><i class="fas fa-window-close"></i>&nbsp;Cancel</button>
                    </div>
                </div>
            </fieldset>
            </div>
            <div class="col-md-8 col-lg-8 col-sm-12">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover text-center department_table data-table" style="font-size:14px;">
                        <thead class="text-center table-warning">
                            <tr style="text-transform: uppercase;">
                                <th>SL.</th>
                                <th>Department Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?=$dept_tr;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>