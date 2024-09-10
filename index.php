<?php 
include 'config.php';
if (MAINTENANCE_MODE) {
    maintenanceModeOn();
}
switch ($action) {
    case '':
    case 'home':
    case 'dashboard':
        if (isUserLoggedin()) {
            switch ($_SESSION[USER_TYPE]) {
                case SADMIN:
                    include('main/pages/sa-audit-dashboard.php');
                    break;
                case ADMIN:
                    include('main/pages/audit-dashboard.php');
                    break;
                case EMPLOYEE:
                    // include('main/pages/dashboard.php');
                    include('main/pages/audit-dashboard.php');
                    break;
                case IT_ADMIN:
                    // include('main/pages/audit-dashboard.php');
                    header('Location: '.HOST_URL.'it-profile');
                    exit();
                    break;
                
                default:
                    include('main/pages/dashboard.php');
                    break;
            }
        } else {
            // include ('main/pages/index.php');
            header('Location: '.HOST_URL.'login');
            exit();
        }
        break;
    case 'audit-dashboard':
        checkSession();
        switch ($_SESSION[USER_TYPE]) {
            case SADMIN:
                include('main/pages/sa-audit-dashboard.php');
                break;
            
            default:
                include('main/pages/audit-dashboard.php');
                break;
        }
        break;
        break;
    case 'company-dashboard':
        checkSession();
        include('main/pages/company-dashboard.php');
        break;
    case 'auditor-dashboard':
        checkSession();
        include('main/pages/auditor-dashboard.php');
        break;
    case 'login':
        if (isUserLoggedin()) {
            header('location:' . HOST_URL);
        } else {
            include ('main/auth/login.php');
        }
        break;
    case 'cx':
        include('ajax/ajax_handler.php');
        exit();
        break;
    case 'logout':
        include('main/auth/logout.php');
        exit();
        break;
    case 'setting':
    case 'auditor-setting':
    case 'sadmin-setting':
    case 'it-setting':
        checkSession();
        include('main/pages/settings.php');
        break;
    case 'auditors':
    case 'sadmin-auditor-management':
    case 'sadmin-admin-management': //show only admins here
    case 'it-auditor-management':
    case 'it-admin-management': //show only admins here
        checkSession();
    include('main/pages/employees.php');
        break;
    case 'add-auditors':
    case 'sadmin-add-auditor':
    case 'it-add-auditor':
        checkSession();
    include('main/pages/add_employee.php');
        break;
    case 'designations':
    case 'sadmin-designations':
    case 'it-designations':
        checkSession();
    include('main/pages/designations.php');
        break;
    case 'departments':
    case 'sadmin-departments':
    case 'it-departments':
        checkSession();
    include('main/pages/departments.php');
        break;
    case 'profile':
    case 'auditor-profile':
    case 'sadmin-profile':
    case 'it-profile':
    // case 'manager-profile':
        checkSession();
        include('main/pages/profile.php');
        break;
    case 'admin-companies':
    case 'sadmin-companies':
        checkSession();
        include ('main/pages/companies.php');
        break;
    case 'admin-add-companies':
    case 'sadmin-add-companies':
        checkSession();
        include ('main/pages/add_companies.php');
        break;
    case 'admin-company-industry':
    case 'sadmin-company-industry':
        checkSession();
        include ('main/pages/company_industry.php');
        break;
    case 'admin-assign-companies':
    case 'sadmin-assign-companies':
        checkSession();
        include ('main/pages/assign_companies.php');
        break;
    case 'admin-audits':
    case 'sadmin-audits':
    case 'auditor-audits':
        checkSession();
        include ('main/pages/manage_audits.php');
        break;
    case 'admin-tax-collection':
    case 'sadmin-tax-collection':
    case 'auditor-tax-collection':
        checkSession();
        include ('main/pages/tax_collection.php');
        break;
    case 'admin-time-spent-report':
    case 'sadmin-time-spent-report':
    case 'auditor-time-spent':
        checkSession();
        include ('main/pages/time_spent_report.php');
        break;
    case 'sadmin-tax-type-management':
    case 'admin-tax-type-management':
        checkSession();
        include ('main/pages/tax_type.php');
        break;
    case 'sadmin-audit-type-management':
    case 'admin-audit-type-management':
        checkSession();
        include ('main/pages/audit_type.php');
        break;
    case 'auditor-workspace':
    case 'admin-workspace':
    case 'sadmin-workspace':
        checkSession();
        include ('main/pages/workspace.php');
        break;
    case 'sadmin-daily-report':
    case 'admin-daily-report':
        checkSession();
        include ('main/pages/report-daily-report.php');
        break;
    case 'sadmin-cases-under-audit-report':
    case 'admin-cases-under-audit-report':
        checkSession();
        include ('main/pages/report-cases-under-audit-report.php');
        break;
    case 'sadmin-case-completed':
    case 'admin-case-completed':
        checkSession();
        include ('main/pages/report-case-completed.php');
        break;
    case 'ip-all':
        codeRip($_SERVER);
        exit();
        break;
    case 'ip':
        echo '<pre><code>';
        echo getInfoText(true);
        echo '</code></pre>';
        exit();
        break;
    case '404':
    default:
        include ('main/pages/not_found.php');
        printContent();
        hakai();
        exit();
        break;
}
$exclude_header = ['404','admin-area', 'login', 'pre_login'];
$exclude_footers = ['404','admin-area', 'login', 'pre_login'];
if (!in_array($action, $exclude_header)) {
    if (isUserLoggedIn()) {
        include('layouts/admin_header.php');
    } else {
        include('layouts/header.php');
    }
}
printContent();
if (!in_array($action, $exclude_footers)) {
    if (isUserLoggedIn()) {
        include('layouts/admin_footer.php');
    } else {
        include('layouts/footer.php');
    }
}
hakai();
?>