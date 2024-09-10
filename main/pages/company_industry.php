<?php 
function printContent() {
    $getIndustry = getData(Table::COMPANY_INDUSTRY_TYPE, [
        COMPANY_INDUSTRY_TYPE::ID,
        COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
    ], [
        COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
        COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS
    ]);
    $table = "";
    if (count($getIndustry)>0) {
        foreach ($getIndustry as $k => $v) {
            $actions = '
            <div class="" style="display:flex; justify-content: space-evenly;">
                <div class="text-success" onclick="updateIndustry('. $v[COMPANY_INDUSTRY_TYPE::ID] .',\''.$v[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE].'\');"><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
                <div class="text-danger cursor-pointer" onclick="initiateDelete('. $v[COMPANY_INDUSTRY_TYPE::ID] .', \'company_industry\')"><i style="font-size:15px; cursor: pointer" class="fas fa-trash-alt"></i></div>
            </div>
            ';
            $table .= '
            <tr class="company_industry_row" id="company_industry_'.$v[COMPANY_INDUSTRY_TYPE::ID].'">
                <td>'.($k+1).'</td>
                <td class="industry_name_td">'.$v[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE].'</td>
                <td>'.$actions.'</td>
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
?>

<h4 class="text-center main_heading">Industry</h4>
<h6 class="text-center sub_heading">Create or Modify Industry Name Here</h6>

<div class="card mt-5">
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 col-lg-5 col-sm-12 company_industry_add_section">
                <?=getSpinner(true, 'com_industry_loader')?>
                <fieldset class="fldset mt-3">
                  <legend>Industry Details</legend>
                  <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                      <div class="form-outline">
                        <label class="form_label" for="com_industry_name">Industry Name</label><?=getAsterics();?>
                        <input type="text" id="com_industry_name" class="form-control" />
                      </div>
                    </div>
                    <input type="hidden" id="com_industry_update_id" value="0" style="visibility: hidden; display:none;" />
                    <div class="col-md-12 col-lg-12 col-sm-12 text-right pt-4 action_btn">
                      <button class="btn btn-sm btn-primary" type="button" id="save_com_industry_btn"><i class="fas fa-plus"></i>&nbsp;Add</button>
                      <button class="btn btn-sm btn-success" type="button" id="update_com_industry_btn" style="display: none;"><i class="far fa-edit"></i>&nbsp;Update</button>
                      <button class="btn btn-sm btn-secondary" type="button" id="cancel_com_industry_btn" style="display: none;" onclick="location.reload();"><i class="fas fa-window-close"></i>&nbsp;Cancel</button>
                    </div>
                  </div>
                </fieldset>
            </div>
            <div class="col-md-7 col-lg-7 col-sm-12 company_industry_view_section">
                <?=getSpinner(true, 'company_industry_table_loader')?>
                <div class="table-responsive mt-4">
                    <table class="table table-sm table-striped table-hover table-bordered text-center com_industry_list_table company_industry_table data-table">
                        <thead class="text-center table-warning">
                            <tr style="text-transform: uppercase; font-size: 12px;">
                                <th>Sl.</th>
                                <th>Industry</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=$table?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>