<?php
if (!in_array($action, $exclude_sidebar)) : 
?>
    <nav id="sidebar">
    <?php if((is_mobile()) || (is_tablet())): ?>
        <div id="dismiss">
            <i class="fas fa-arrow-left"></i>
        </div>
    <?php endif; ?>
        <div class="sidebar-header text-center">
            <img src="<?=COMPANY_LOGO_PATH;?>" class="img-fluid" style="width: 90%;"/>
            <h3 style="font-size: 1rem !important; font-weight: 600  !important; margin-top: 10px;"><?=COMPANY_BUSINESS_HEADING?></h3>
            <h4 style="font-size: 1rem !important; font-weight: 400  !important; margin-top: 10px;"><?=COMPANY_BUSINESS_SUB_HEADING?></h4>
        </div>

        <ul class="list-unstyled components">
            <p class="text-center font-weight-bold">
                <?=($_SESSION[USER_TYPE]==IT_ADMIN)?'IT ADMIN':USERS[$_SESSION[USER_TYPE]];?> Portal
            </p>
        <?php 
        // Admin Sidebar Menu Start
        if(in_array($action, $admin_sidebar_menus)): ?>
            <?php 
            if ((in_array($action, $admin_dashboard))) : 
            ?>
                <li class="<?php if (($action == 'audit-dashboard') || ($action == 'home') || ($action == '')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>audit-dashboard/">Audit Dashboard</a>
                </li>
                <li class="<?php if (($action == 'company-dashboard')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>company-dashboard/">Tax Payer Dashboard</a>
                </li>
                <li class="<?php if (($action == 'auditor-dashboard')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>auditor-dashboard/">Auditor Dashboard</a>
                </li>
            <?php endif; ?>
            <?php if (in_array($action, $admin_account)) : ?>
                <!-- home and profile start -->
                <li class="<?php if ($action == 'profile') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>profile/">My Profile</a>
                </li>
                <li class="<?php if ($action == 'setting') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>setting/">Settings</a>
                </li>
                <li class="<?php if (($action == 'auditors')||($action == 'add-auditors')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>auditors/">Auditors</a>
                </li>
                <li class="<?php if ($action == 'departments') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>departments/">Departments</a>
                </li>
                <li class="<?php if ($action == 'designations') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>designations/">Designations</a>
                </li>
            <?php endif; ?>
            <?php if(in_array($action, $admin_dept)): ?>
                <li class="<?php if ($action == 'admin-audits') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-audits/">Audits</a>
                </li>
                <li class="<?php if ($action == 'admin-assign-companies') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-assign-companies/">Assign Audits</a>
                </li>
                <li class="<?php if ($action == 'admin-tax-collection') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-tax-collection/">Tax Collection</a>
                </li>
                <li class="<?php if (($action == 'admin-companies')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-companies/">Create / Modify Tax Payers</a>
                </li>
                <li class="<?php if ($action == 'admin-company-industry') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-company-industry/">Create / Modify Industries</a>
                </li>
                <li class="<?php if ($action == 'admin-audit-type-management') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-audit-type-management/">Create / Modify Audit Types</a>
                </li>
                <li class="<?php if ($action == 'admin-tax-type-management') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-tax-type-management/">Create / Modify Tax Types</a>
                </li>
            <?php endif; ?>
            <?php if(in_array($action, $admin_reports)): ?>
                <li class="<?php if ($action == 'admin-daily-report') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-daily-report/">Daily Report</a>
                </li>
                <li class="<?php if ($action == 'admin-cases-under-audit-report') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-cases-under-audit-report/">Cases Under Audit</a>
                </li>
                <li class="<?php if ($action == 'admin-case-completed') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-case-completed/">Case Completed</a>
                </li>
            <?php endif; ?>
            <?php if (in_array($action, $admin_workspace)) : ?>
                <li class="<?php if (($action == 'admin-workspace')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>admin-workspace/">Workspace</a>
                </li>
            <?php endif; ?>
        <?php 
        endif;
        // Admin Sidebar Menu End

        // Super Admin Sidebar Menu Start
        if(in_array($action, $sadmin_sidebar_menus)): ?>
            <?php if (in_array($action, $sadmin_account)) : ?>
                <!-- home and profile start -->
                <li class="<?php if (($action == 'sadmin-profile')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?=HOST_URL?>sadmin-profile/">My Profile</a>
                </li>
                <li class="<?php if ($action == 'sadmin-setting') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-setting/">Settings</a>
                </li>
                <li class="<?php if (($action == 'sadmin-admin-management')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?=HOST_URL?>sadmin-admin-management/">Admins</a>
                </li>
                <li class="<?php if (($action == 'sadmin-auditor-management')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?=HOST_URL?>sadmin-auditor-management/">Auditors</a>
                </li>
                <li class="<?php if (($action == 'sadmin-departments')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?=HOST_URL?>sadmin-departments/">Departments</a>
                </li>
                <li class="<?php if (($action == 'sadmin-designations')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?=HOST_URL?>sadmin-designations/">Designations</a>
                </li>
            <?php endif; ?>
            <?php if(in_array($action, $sadmin_dept)): ?>
                <li class="<?php if (($action == 'sadmin-audits')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-audits/">Audits</a>
                </li>
                <li class="<?php if ($action == 'sadmin-assign-companies') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-assign-companies/">Assign Audits</a>
                </li>
                <li class="<?php if (($action == 'sadmin-tax-collection')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-tax-collection/">Tax Collection</a>
                </li>
                <li class="<?php if (($action == 'sadmin-companies')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-companies/">Create / Modify Tax Payers</a>
                </li>
                <li class="<?php if ($action == 'sadmin-company-industry') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-company-industry/">Create / Modify Industries</a>
                </li>
                <li class="<?php if ($action == 'sadmin-audit-type-management') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-audit-type-management/">Create / Modify Audit Type</a>
                </li>
                <li class="<?php if ($action == 'sadmin-tax-type-management') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-tax-type-management/">Create / Modify Tax Type</a>
                </li>
            <?php endif; ?>
            <?php if(in_array($action, $sadmin_reports)): ?>
                <li class="<?php if ($action == 'sadmin-daily-report') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-daily-report/">Daily Report</a>
                </li>
                <li class="<?php if ($action == 'sadmin-cases-under-audit-report') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-cases-under-audit-report/">Cases Under Audit</a>
                </li>
                <li class="<?php if ($action == 'sadmin-case-completed') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-case-completed/">Case Completed</a>
                </li>
            <?php endif; ?>
            <?php if (in_array($action, $sadmin_workspace)) : ?>
                <li class="<?php if (($action == 'sadmin-workspace')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>sadmin-workspace/">Workspace</a>
                </li>
            <?php endif; ?>
        <?php 
        // Super Admin Sidebar Menu End
        endif;
        // Employee Sidebar Menu Start
        if(in_array($action, $auditor_sidebar_menus)):
        ?>
            <?php if (in_array($action, $auditor_account)) : ?>
                <li class="<?php if ($action == 'auditor-profile') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>auditor-profile/">My Profile</a>
                </li>
                <li class="<?php if ($action == 'auditor-setting') : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>auditor-setting/">Settings</a>
                </li>
            <?php endif; ?>
            <?php if (in_array($action, $auditor_audits)) : ?>
                <li class="<?php if (($action == 'auditor-audits')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>auditor-audits/">Audits</a>
                </li>
                <li class="<?php if (($action == 'auditor-tax-collection')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>auditor-tax-collection/">Tax Collection</a>
                </li>
                <li class="<?php if (($action == 'auditor-time-spent')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>auditor-time-spent/">Audits Time Spent</a>
                </li>
            <?php endif; ?>
            <?php if (in_array($action, $auditor_workspace)) : ?>
                <li class="<?php if (($action == 'auditor-workspace')) : ?>active<?php endif; ?>">
                    <a class="fload" href="<?= HOST_URL ?>auditor-workspace/">My Workspace</a>
                </li>
            <?php endif; ?>
        <?php
        endif;
        // Employee Sidebar Menu End
        // It Admin Sidebar Menu Start
        if(in_array($action, $itAdminUserProfiles)):
        ?>
            
            <li class="<?php if (($action == 'it-profile')) : ?>active<?php endif; ?>">
                <a class="fload" href="<?=HOST_URL?>it-profile/">My Profile</a>
            </li>
            <li class="<?php if ($action == 'it-setting') : ?>active<?php endif; ?>">
                <a class="fload" href="<?= HOST_URL ?>it-setting/">Settings</a>
            </li>
            <li class="<?php if (($action == 'it-admin-management')) : ?>active<?php endif; ?>">
                <a class="fload" href="<?=HOST_URL?>it-admin-management/">Admins</a>
            </li>
            <li class="<?php if (($action == 'it-auditor-management')) : ?>active<?php endif; ?>">
                <a class="fload" href="<?=HOST_URL?>it-auditor-management/">Auditors</a>
            </li>
            <li class="<?php if (($action == 'it-departments')) : ?>active<?php endif; ?>">
                <a class="fload" href="<?=HOST_URL?>it-departments/">Departments</a>
            </li>
            <li class="<?php if (($action == 'it-designations')) : ?>active<?php endif; ?>">
                <a class="fload" href="<?=HOST_URL?>it-designations/">Designations</a>
            </li>
        <?php
        endif;
        // It Admin Sidebar Menu end
        ?>
        </ul>

        <ul class="list-unstyled CTAs d-none">
            <li>
                <a href="<?=CDN_URL;?>docs/idea.pdf" target="_blank" class="download">Download Tutorial &NonBreakingSpace;<i class="far fa-file-pdf"></i></a>
            </li>
            <li>
                <a href="tel:+91 9123456789" class="article">Make a call &NonBreakingSpace;<i class="fas fa-phone-alt"></i></a>
            </li>
        </ul>
        <div class="row mb-5">
            <div class="col-md-12" style="padding-left: 20px; padding-right: 20px;">
                <!-- <div class="card"> -->
                    <!-- <div class="card-body"> -->
                    <fieldset class="fldset" style="padding: 15px;">
                            <legend style="background:none !important;">Server Info</legend>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-warning currAudDetHeading">Current Date</p>
                            </div>
                            <div class="col-md-12 mt-2">
                                <p class="text-light"><?=getFormattedDateTime(getToday(false),LONG_DATE_FORMAT)?><br />[&nbsp;<span id="live-time"></span>&nbsp;]</p>
                            </div>
                        </div>
                        <div class="row currentAudDetailsRow mt-2">
                            <div class="col-md-12">
                                <p class="text-warning currAudDetHeading">Last Login Date</p>
                            </div>
                            <div class="col-md-12 mt-2">
                                <span class="text-light">
                                    <?php echo ((isset($_SESSION[LAST_LOGIN_DATE]) && ($_SESSION[LAST_LOGIN_DATE]!='')) ? getFormattedDateTime($_SESSION[LAST_LOGIN_DATE],LONG_DATE_FORMAT) : EMPTY_VALUE) ?>
                                    <?php echo ((isset($_SESSION[LAST_LOGIN_TIME]) && ($_SESSION[LAST_LOGIN_TIME]!='')) ? '</br>'.getFormattedDateTime($_SESSION[LAST_LOGIN_TIME],LONG_TIME_FORMAT) : EMPTY_VALUE) ?>
                                </span>
                            </div>
                        </div>
                        <div class="row currentAudDetailsRow mt-2">
                            <div class="col-md-12">
                                <p class="text-warning currAudDetHeading">Last Logout Date</p>
                            </div>
                            <div class="col-md-12 mt-2">
                                <span class="text-light">
                                    <?php echo ((isset($_SESSION[LAST_LOGOUT_DATE]) && ($_SESSION[LAST_LOGOUT_DATE]!='')) ? getFormattedDateTime($_SESSION[LAST_LOGOUT_DATE],LONG_DATE_FORMAT) : EMPTY_VALUE) ?>
                                    <?php echo ((isset($_SESSION[LAST_LOGOUT_TIME]) && ($_SESSION[LAST_LOGOUT_TIME]!='')) ? '</br>'.getFormattedDateTime($_SESSION[LAST_LOGOUT_TIME],LONG_TIME_FORMAT) : EMPTY_VALUE) ?>
                                </span>
                            </div>
                        </div>
                        <div class="row currentAudDetailsRow mt-2">
                            <div class="col-md-12">
                                <p class="text-warning currAudDetHeading">Last Login IP Address</p>
                            </div>
                            <div class="col-md-12 mt-2">
                                <span class="text-light"><?php echo ((isset($_SESSION[LAST_SESSION_IP]) && ($_SESSION[LAST_SESSION_IP]!='')) ? $_SESSION[LAST_SESSION_IP] : EMPTY_VALUE) ?></span>
                            </div>
                        </div>
                    </fieldset>
                    <!-- </div>
                </div> -->
            </div>
        </div>
    </nav>
<?php
endif;
?>