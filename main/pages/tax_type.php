<?php 
function printContent()
{
    $table='';
    $getTaxTypeData=getData(Table::TYPE_OF_TAX,['*'],[TYPE_OF_TAX::CLIENT_ID=>$_SESSION[CLIENT_ID],TYPE_OF_TAX::STATUS=>ACTIVE_STATUS]);
    if (count($getTaxTypeData)>0) {
        $sl=1;
        foreach ($getTaxTypeData as $k => $v) {
            $actions = '
            <div class="" style="display:flex; justify-content: space-evenly;">
                <div class="text-success" onclick="updateTaxType('. $v[TYPE_OF_TAX::ID] .',\''.$v[TYPE_OF_TAX::TYPE_OF_TAX].'\');"><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
                <div class="text-danger cursor-pointer" onclick="initiateDelete('. $v[TYPE_OF_TAX::ID] .', \'tax_type\')"><i style="font-size:15px; cursor: pointer" class="fas fa-trash-alt"></i></div>
            </div>
            ';
            $table.='
            <tr class="tax_type_row" id="tax_type_'.$v[TYPE_OF_TAX::ID].'">
                <td>'.$sl.'</td>
                <td>'.altRealEscape($v[TYPE_OF_TAX::TYPE_OF_TAX]).'</td>
                <td>'.$actions.'</td>
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
?>

<h4 class="text-center main_heading">Tax Type</h4>
<h6 class="text-center sub_heading">Create or Modify Tax Type here</h6>
<div class="card mt-5">
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 col-lg-5 col-sm-12 tax_type_add_section">
                <?=getSpinner(true, 'taxTypeLoader')?>
                <fieldset class="fldset mt-4">
                  <legend>Tax Type Details</legend>
                  <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                      <div class="form-outline">
                        <label class="form_label" for="tax_type_name">Tax Type Name</label><?=getAsterics();?>
                        <input type="text" id="tax_type_name" class="form-control" />
                      </div>
                    </div>
                    <input type="hidden" id="tax_type_update_id" value="0" style="visibility: hidden; display:none;" />
                    <div class="col-md-12 col-lg-12 col-sm-12 text-right pt-4 action_btn">
                      <button class="btn btn-sm btn-primary" type="button" id="save_tax_type_btn"><i class="fas fa-plus"></i>&nbsp;Add</button>
                      <button class="btn btn-sm btn-success" type="button" id="update_tax_type_btn" style="display: none;"><i class="far fa-edit"></i>&nbsp;Update</button>
                      <button class="btn btn-sm btn-secondary" type="button" id="cancel_tax_type_btn" style="display: none;" onclick="location.reload();"><i class="fas fa-window-close"></i>&nbsp;Cancel</button>
                    </div>
                  </div>
                </fieldset>
            </div>
            <div class="col-md-7 col-lg-7 col-sm-12 tax_type_view_section">
                <?=getSpinner(true, 'tax_type_table_loader')?>
                <div class="table-responsive mt-4">
                    <table class="table table-sm table-striped table-hover table-bordered text-center data-table tax_type_table">
                        <thead class="text-center table-warning">
                            <tr style="text-transform: uppercase; font-size: 12px;">
                                <th>Sl.</th>
                                <th>Tax Type</th>
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
<?php
}
?>