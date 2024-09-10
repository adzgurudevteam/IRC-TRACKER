<?php
function printContent()
{
  $auditTypeOptions = '<option value="0" disabled selected>--- Select Audit Type ---</option>';
  $taxTypeOptions = '<option value="0" disabled selected>--- Select Tax Type ---</option>';
  $com_table_rows = "";
  $showName = [];
  $getCompanyData = getData(Table::COMPANIES, ['*'], [
    COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
    COMPANIES::STATUS => ACTIVE_STATUS
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
  $getIndustry = getData(Table::COMPANY_INDUSTRY_TYPE, [
    COMPANY_INDUSTRY_TYPE::ID,
    COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
  ], [
    COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
    COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS
  ]);
  // rip($getCompanyData);
  // echo $getCompanyData['sql'];
  // exit();
  if (count($getCompanyData) > 0) {

    foreach ($getCompanyData as $ck => $cv) {
      $c_industry_type = $c_audit_type = $c_tax_type = "";

      $showName[] = $cv[COMPANIES::COMPANY_NAME];
      if ($cv[COMPANIES::INDUSTRY_TYPE_ID] != 0) {
        $getIndustryName = getData(Table::COMPANY_INDUSTRY_TYPE, [
          COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE
        ], [
          COMPANY_INDUSTRY_TYPE::CLIENT_ID => $_SESSION[CLIENT_ID],
          COMPANY_INDUSTRY_TYPE::STATUS => ACTIVE_STATUS,
          COMPANY_INDUSTRY_TYPE::ID => $cv[COMPANIES::INDUSTRY_TYPE_ID]
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
        AUDIT_TAX_TYPE_HISTORY::COMPANY_ID => $cv[COMPANIES::ID],
        AUDIT_TAX_TYPE_HISTORY::CLIENT_ID => $_SESSION[CLIENT_ID],
      ]);
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
        <div class="text-success" onclick=\'updateCompany(' . $cv[COMPANY_INDUSTRY_TYPE::ID] . ','.json_encode($cv).');\'><i style="font-size:15px; cursor: pointer;" class="far fa-edit"></i></div>
        <div class="text-danger cursor-pointer" onclick="initiateDelete(' . $cv[COMPANY_INDUSTRY_TYPE::ID] . ', \'company\')"><i style="font-size:15px; cursor: pointer" class="fas fa-trash-alt"></i></div>
    </div>
    ';
      $checked = ($cv[COMPANIES::ACTIVE] == 1) ? 'checked' : '';
      $activeInactive = '
    <div class="custom-control custom-switch noselect" style="cursor:pointer;">
        <input type="checkbox" ' . $checked . ' class="custom-control-input" id="company_active_' . $cv[COMPANIES::ID] . '" onclick="makeCompanyActive(' . $cv[COMPANIES::ID] . ');" style="cursor:pointer;" />
        <label class="custom-control-label text-success" for="company_active_' . $cv[COMPANIES::ID] . '"></label>
    </div>
    ';
      $com_table_rows .= '
    <tr>
      <td>' . ($ck + 1) . '</td>
      <td>' . altRealEscape($cv[COMPANIES::COMPANY_NAME]) . '</td>
      <td>' . $c_industry_type . '</td>
      <td>' . altRealEscape($cv[COMPANIES::TAX_IDENTIFICATION_NUMBER]) . '</td>
      <td>' . altRealEscape($cv[COMPANIES::COMPANY_CODE]) . '</td>
      <td>' . altRealEscape($cv[COMPANIES::CASE_CODE]) . '</td>
      <td>' . $c_audit_type . '</td>
      <td>' . $c_tax_type . '</td>
      <td>' . $activeInactive . '</td>
      <td>' . $actions . '</td>
    </tr>';
    }
    // rip ($showName);
  } else {
    $com_table_rows = '
  <tr>
      <td colspan="10">
          <div class="alert alert-danger" role="alert">
              No Companies found !
          </div>
      </td>
  </tr>';
  }
  $com_ind_opt = '<option value="0" selected disabled>---- Select Industry ----</option>';
  if (count($getIndustry) > 0) {
    foreach ($getIndustry as $k => $v) {
      $com_ind_opt .=
        '<option value="' . $v[COMPANY_INDUSTRY_TYPE::ID] . '">' . altRealEscape($v[COMPANY_INDUSTRY_TYPE::INDUSTRY_TYPE]) . '</option>';
    }
  }
  if (count($getAuditTypes) > 0) {
    foreach ($getAuditTypes as $atk => $atv) {
      $auditTypeOptions .= '<option value="' . $atv[AUDIT_TYPES::ID] . '">' . $atv[AUDIT_TYPES::AUDIT_TYPE] . '</option>';
    }
  }
  if (count($getTaxTypes) > 0) {
    foreach ($getTaxTypes as $ttk => $ttv) {
      $taxTypeOptions .= '<option value="' . $ttv[TYPE_OF_TAX::ID] . '">' . $ttv[TYPE_OF_TAX::TYPE_OF_TAX] . '</option>';
    }
  }
?>
  <div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 text-center">
      <h4 class="text-center main_heading">Company</h4>
      <h6 class="text-center sub_heading">Create or Modify Company Details here</h6>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-12 text-right" id="list_nav_btn">
      <button class="btn btn-sm btn-primary" name="add" onclick="changeNavigateBtn('company');"><i class="fas fa-plus"></i>&nbsp;Add</button>
    </div>
  </div>

  <div class="card mt-5">
    <div class="card-body">
    <?= getSpinner(true, 'com_add_loader'); ?>
      <div class="row" id="add_company_row" style="display: none;">
        <div class="col-md-12 company_add_section">
          <fieldset class="fldset mt-3">
            <legend>Company Details</legend>
            <div class="row">
              <div class="col-md-4 col-lg-4 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="com_name">Company Name</label><?= getAsterics(); ?>
                  <input type="text" id="com_name" class="form-control" />
                </div>
              </div>
              <div class="col-md-4 col-lg-4 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="com_industry_type_select">Industry Type</label>
                  <select id="com_industry_type_select" class="form-control"><?= $com_ind_opt; ?></select>
                </div>
              </div>
              <div class="col-md-4 col-lg-4 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="com_tin_number">TIN Number</label><?= getAsterics(); ?>
                  <input type="text" id="com_tin_number" class="form-control" />
                </div>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="company_code">Company Code</label>
                  <input type="text" id="company_code" class="form-control" />
                </div>
              </div>
              <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="com_case_code">Case Code</label>
                  <input type="text" id="com_case_code" class="form-control" />
                </div>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="audit_type_id">Audit Type</label><?= getAsterics(); ?>
                  <select id="audit_type_id" class="form-control" onchange="setTaxtype();">
                    <?= $auditTypeOptions ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="form-outline">
                  <label class="form_label" for="type_of_tax_id">Tax Type</label><?= getAsterics(); ?>
                  <div class="tax_type_select_div">
                    <select id="type_of_tax_id" class="form-control">
                      <?= $taxTypeOptions ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <input type="hidden" id="company_update_id" value="0" style="visibility: hidden; display:none;" />
            <div class="row mt-2">
              <div class="col-md-12 col-lg-12 col-sm-12 text-right pt-4 action_btn">
                <button class="btn btn-sm btn-success" type="button" id="save_company_btn"><i class="fas fa-plus"></i>&nbsp;Add</button>
                <button class="btn btn-sm btn-success" type="button" id="update_company_btn" style="display: none;"><i class="far fa-edit"></i>&nbsp;Update</button>
                <button class="btn btn-sm btn-secondary" type="button" id="cancel_company_btn" style="display: none;" onclick="location.reload();"><i class="fas fa-window-close"></i>&nbsp;Cancel</button>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
      <div class="row" id="view_company_row">
        <div class="table-responsive mt-4">
          <table class="table table-sm table-striped table-hover table-bordered text-center company_list_table data-table">
            <thead class="text-center table-warning">
              <tr style="font-size: 12px;">
                <th>Sl.</th>
                <th>Company Name</th>
                <th>Industry</th>
                <th>Company TIN</th>
                <th>Company code</th>
                <th>case code</th>
                <th>Audit Type</th>
                <th>Tax Type</th>
                <th>Active</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?= $com_table_rows; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

<?php } ?>