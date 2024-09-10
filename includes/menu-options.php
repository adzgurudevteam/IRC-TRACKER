<?php 

$exclude_sidebar = ['login'];
//Admin Sidebar Menu Options
$admin_dashboard = ['home', '', 'audit-dashboard','company-dashboard','auditor-dashboard'];
$admin_account = ['profile','setting', 'company-details','auditors', 'add-auditors', 'designations', 'departments'];
$admin_dept = ['admin-companies', 'admin-add-companies', 'admin-modify-companies', 'admin-company-industry', 'admin-assign-companies', 'admin-audits', 'admin-tax-collection','admin-audit-type-management','admin-tax-type-management'];
$admin_reports = ['admin-daily-report','admin-cases-under-audit-report','admin-case-completed'];
$admin_workspace = ['admin-workspace'];
//Sadmin Sidebar Menu Options
$sadmin_dashboard = ['home', '', 'audit-dashboard','company-dashboard','auditor-dashboard'];
$sadmin_account = ['sadmin-profile','sadmin-setting', 'sadmin-company-details','sadmin-admin-management', 'sadmin-auditor-management', 'sadmin-add-auditor', 'sadmin-modify-auditor', 'sadmin-designations', 'sadmin-departments'];
$sadmin_dept = ['sadmin-companies', 'sadmin-add-companies', 'sadmin-modify-companies', 'sadmin-company-industry', 'sadmin-assign-companies', 'sadmin-audits', 'sadmin-tax-collection','sadmin-audit-type-management','sadmin-tax-type-management'];
$sadmin_reports = ['sadmin-daily-report','sadmin-cases-under-audit-report','sadmin-case-completed'];
$sadmin_workspace = ['sadmin-workspace'];
//Employees Sidebar Menu Options
$auditor_dashboard = ['home', '', 'dashboard', 'audit-dashboard','company-dashboard','auditor-dashboard'];
$auditor_account = ['auditor-profile','auditor-setting'];
$auditor_audits = ['auditor-audits','auditor-tax-collection','auditor-time-spent'];
$auditor_workspace = ['auditor-workspace'];
//IT_ADMIN Sidebar Menu Options
$itAdminUserProfiles=['it-profile','it-setting','it-admin-management','it-auditor-management','it-departments','it-designations', 'it-add-auditor'];

$admin_sidebar_menus = array_merge($admin_dashboard,$admin_account,$admin_dept,$admin_reports,$admin_workspace);
$auditor_sidebar_menus = array_merge($auditor_dashboard, $auditor_account, $auditor_audits, $auditor_workspace);
$sadmin_sidebar_menus = array_merge($sadmin_dashboard,$sadmin_account,$sadmin_dept,$sadmin_reports,$sadmin_workspace);
// rip($admin_sidebar_menus);
// exit();
?>