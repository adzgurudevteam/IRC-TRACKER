<?php
/**
 * Author: Jyotirmoy Saha
 * Maintained By: Jyotirmoy Saha
 */
define('DEBUG_APP', true);
define('MAINTENANCE_MODE', false);
if ((isset($_GET['error']) && $_GET['error'] == 1) || DEBUG_APP) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}
define('TIME_ZONE', 'Pacific/Port_Moresby');
date_default_timezone_set(TIME_ZONE);
## Domain & URL setup
$http = 'http://';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    $http = 'https://';
}

$domain = $_SERVER['HTTP_HOST'];
$xip =  gethostbyname($domain);
## Flag for checking weather the project is on the local server or live server
define('IS_LOCAL', true);
define('IS_PHPMAILER', true);

## Flag for AJAX work to tell the server and the browser 
## In which format does the response is coming
define('IS_CONTENT_TYPE_JSON', false);

define('MAIN_DOMAIN', $http . $domain);
$hurl = MAIN_DOMAIN . '/';

$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = explode("/", trim(parse_url($requestUri)['path'], "/"));
## This action will define which php or any other file to open.
$folder = IS_LOCAL || $xip === $domain ? $parsedUrl[0] : '';
if (IS_LOCAL || $xip === $domain) {
    ## Here Chage the app_name with your project name.
    $hurl .= "$folder/";
}
$action = "";
$sidebar = "";
$last = count($parsedUrl) > 0 ? $parsedUrl[count($parsedUrl) - 1] : '';
$sidebar = count($parsedUrl) > 1 ? $parsedUrl[count($parsedUrl) - 2] : '';
if (IS_LOCAL && $last != $folder) {
    $action = $parsedUrl[1];
} else {
    $action = $parsedUrl[0];
}
if ($action == $folder) {
    // Change the default page of the admin panel
    $action = 'home';
}
/**
 * Root address
 */
define('BASE_DIR', __DIR__ . '/');
/**
 * Main host url of the website
 */
define('HOST_URL', $hurl);
define('AUTH_URL', HOST_URL.'main/auth/');
define('PAGE_URL', HOST_URL.'main/pages/');
## Log for in case any error occurs
define('LOG_FILE',  dirname(__FILE__) . "/log.log");
$db_json = file_get_contents(BASE_DIR . "appinfo.json");
$db_cfg = json_decode($db_json, true);
define('API', $db_cfg['api']);
if (API) {
    // Define api url
    define('API_URL', $db_cfg['api_url']);
}

/**
 * Content delivery network url
 */
define('CDN_URL', HOST_URL . 'assets/');
/**
 * Url for file that handles the ajax request
 */
define('AJAX_URL', HOST_URL . "cx/");
define('AJAX_REQUEST', 'ajax_action');

define('ASSETS_VERSION', (string)'0.00524');

define('FORM_HANDLER_URL', HOST_URL . "frmsbt/");
define('FORM_UI_PATH', BASE_DIR . "forms/ui/");
define('DEFAULT_NO_IMAGE', 'no-image.png');

define('ALLOWED_MAX_FILESIZE', 104857600);

define('ALLOWED_FILE_TYPE', ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'webp', 'WEBP', 'gif', 'GIF', 'mpg', 'MPG', 'mpeg', 'MPEG', 'mp4', 'MP4', 'MPEG4', 'MPEG4', 'mkv', 'MKV', 'mov', 'MOV']);
define('ALLOWED_VIDEO_FILE', ['mpg', 'mp4', 'mpeg', 'mpeg4', 'mkv', 'mov']);
define('ALLOWED_IMAGE_TYPE', ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG']);
define('ALL_IMAGE_TYPE', ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'webp', 'WEBP', 'gif', 'GIF']);
## Pages to perform some library js call
define('SWITCHERY_INCLUDES', []);
define('TOOLTIP_INCLUDES', []);

#EEror messages
define('ERROR_1', 'Opps! Soemthing went wrong. Please try again');
define('ERROR_2', 'Opps! Something happened. We are looking into this.');
define('ERROR_3', 'Opps! Something bad happened. Please reload and try again.');
define('ERROR_4', 'Opps! Soemthing went wrong. Please reload and try again');
define('EMPTY_FIELD_ALERT', 'Please Recheck all the fields and fill them properly');

define('EMPLOYEE_ID_PREFIX', 'IRC');

## Session Keys
// Change or add the session as per your choice

//admin session_keys
define('CLIENT_ID', 'irc_client_id'); // admin client id
define('USER_ID', 'irc_user_id'); // admin user id
define('RID', 'irc_rid'); // admin primary row id
define('USERNAME', 'irc_username');
define('LOGGEDIN', 'irc_loggedin');
define('USER_TYPE', 'irc_user_type');
define('EMPLOYEE_ID', 'irc_employee_id');

define('SADMIN_UID', 1001);
define('DEMO_UID', 0);

define('PERMITTED_USER_IDS', [SADMIN_UID,DEMO_UID]);


define('LAST_LOGIN_DATE', 'irc_last_login_date');
define('LAST_LOGIN_TIME', 'irc_last_login_time');
define('LAST_LOGOUT_DATE', 'irc_last_logout_date');
define('LAST_LOGOUT_TIME', 'irc_last_logout_time');
define('LAST_SESSION_IP', 'irc_last_session_ip');
define('LAST_SESSION_DATA_ROW_ID', 'irc_last_session_data_row_id');

//user session keys

define('ECLIENT_ID', 'irc_eclient_id');
define('EUSER_ID', 'irc_euser_id');
define('ERID', 'irc_erid');
define('EUSERNAME', 'irc_eusername');
define('ELOGGEDIN', 'irc_eloggedin');
define('EUSER_TYPE', 'irc_euser_type');

// Token Keys

define('TOKEN', 'token__');
define('TOKEN_ID', '__token_id');
define('REGIS_BY', 'regis_by');
define('EMAIL_VARIFIED', 'email_v');
define('MOBILE_VARIFIED', 'mobile_v');

define('FORM_DATA', 'form_data');
define('DEFAULT_USERNAME', 'User');
define('WEBSITE_NAME', 'website_name');
define("LOGO", 'logo');
define('SMS_ENABLED', 'sms_enabled');
define('SCOPE', 'scope_type');

define('TOKEN_VALIDATE', 1);
define('TOKEN_INVALID', 0);

## Cookie keys

## Temporary constant
define('SHOULD_ROOT_HEADER', false);
define('MIN_OTP_BOUND', 100000);
define('MAX_OTP_BOUND', 999999);

define('ACTIVE_STATUS', 'A');
define('DEACTIVE_STATUS', 'D');

define('TOOLTIP_CLASS', 'tooltip_ele');
define('EnterEventListnerClass', 'enter_event_class');

//definning constant for the purposes of sending an email

//constants for email verification remarks

//constant for Reciever of the Emails

//Constants for mail sent status
define('MAIL_SENT', 1);
define('MAIL_NOT_SENT', 2);

/**
 * constant for the purposes of sent sms
 */

define('HEADER_BOTTOM_EXCLUDE_PAGES', ['login', 'signup']);
/**
 * constants for session message management
 */
define('SERR', 'serr');
define('SERR_MSG', 'serr_msg');
define('ISSSEMSG', 'isMsg');
define('SEMSG', 'msgText');
define('SEMSG_COLOR', 'msgColors');

## Constants for SMS templates id
// Will increase as development proceeds

## Message key and receiver key for SMS api
// define('MESSAGE_KEY', 'message_key');
// define('RECEIVER_KEY', 'receiver_key');
// ## SMS gateway type

//constants for sms templates

//Constants for sms sent status

//constants to define user types
define('SADMIN', 7); //Super Admin
define('ADMIN', 6);  // Admin [HR]
define('MANAGER', 5);  // Manager [Team Manager (Reporting Manager)]
define('EMPLOYEE', 4); // Employee
define('IT_ADMIN', 3); // IT_ADMIN
define('USERS', [SADMIN => 'SUPER ADMIN', ADMIN => 'ADMIN', EMPLOYEE => 'AUDITOR']);
//Constants default empty value
define('EMPTY_VALUE', ' - ');

//Constants for Employee Inactive Reasons
define('RESIGNED', 2);
define('ABSCONDED', 3);
define('SERVING_NOTICE', 4);
define('OTHER_REASON', 5);
define('EMPLOYEE_INACTIVE_REASONS', [
    RESIGNED => 'RESIGNED',
    ABSCONDED => 'ABSCONDED',
    SERVING_NOTICE => 'SERVING NOTICE',
    OTHER_REASON => 'OTHER'
]);
define('COMPANY_PAYROLL', 1);
define('CONTRACT_PAYROLL', 2);
define('PAYROLL_OPTIONS', [
    COMPANY_PAYROLL => "COMPANY PAYROLL",
    CONTRACT_PAYROLL => "CONTRACT PAYROLL"
]);
define('EMPLOYEE_DEFAULT_ID', 800100);
define('DEFAULT_REPORT_TIME', 10);
define('EMPLOYEE_REPORTING_TIMES', ['8','9','10','11','12']);
define('ALL_MONTHS', '<option value="1">Janaury</option>
<option value="2">February</option>
<option value="3">March</option>
<option value="4">April</option>
<option value="5">May</option>
<option value="6">June</option>
<option value="7">July</option>
<option value="8">August</option>
<option value="9">September</option>
<option value="10">October</option>
<option value="11">November</option>
<option value="12">December</option>');
define('ALL_MONTHS_NAME', [
    1 => 'Janaury',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
]);
//Constants defining Software Update date
define('SOFTWARE_UPDATE_DATE', date('Y-m-d'));

// constants for Date Time Formats
define('LONG_DATE_TIME_FORMAT', 'l jS \of F Y   h:i a');
define('LONG_DATE_FORMAT', 'l jS \of F Y');
define('LONG_TIME_FORMAT', 'h:i a');
define('DEFAULT_AMOUNT', (int)0);
define('DEFAULT_GST_PERCENTAGE', (int)'18');
define('GRACE_TIME', (int)30);
define('RIGHT_WORK_HOUR', (int)9);
define('CUSTOM_SPINNER_ID', 'loading-bar-spinner');

## Add Header
if (SHOULD_ROOT_HEADER) {
    require 'layouts/header.php';
}
## Include DB Work file
require BASE_DIR . 'db/Connection.php';
require BASE_DIR . 'db/DB_Work.php';
$db = null;
if (!API) {
    $db = DB_Work::getDBInstance();
}
require BASE_DIR . 'library/mobile_detect/Mobile_Detect.php';
require BASE_DIR . 'library/function.php';
require BASE_DIR . 'includes/interfaces/TableStructure.php';

## Hashing the doamin to get the client id
## This will only be used in front end
if (!IS_LOCAL && (strpos($domain, "www") < 0 || strpos($domain, "www") === false)) {
    if ($xip !== $domain) {
        $domain = 'www.' . $domain;
    }
}
$domain_hash = '';
if (IS_LOCAL || $xip == $domain) {
    $domain_hash = hash('sha256', $folder);
} else {
    $domain_hash = hash('sha256', $domain);
}
define('DOMAIN_HASH', $domain_hash);
define('SHOULD_USE_STAGE', true);
session_start();
$email = $password = $company_website = $company_address = "";
$company_name = $db_cfg['company_name'];
$company_business_name = $db_cfg['business_name'];
$company_business_heading = $db_cfg['business_heading'];
$company_business_sub_heading = $db_cfg['business_sub_heading'];
$company_logo = $db_cfg['company_default_logo'];
$company_title_logo = $db_cfg['company_title_logo'];
if (isUserLoggedIn()) {
    $sc = getData(Table::CLIENT, [
        Client::WEBSITE_NAME,
        Client::COMPANY_LOGO,
        Client::COMPANY_EMAIL,
        Client::COMPANY_EMAIL_PASSWORD,
        Client::NAME,
        Client::COMPANY_NAME
    ], [
        Client::CLIENT_ID => $_SESSION[CLIENT_ID]
    ]);
    $email = SHOULD_USE_STAGE ? $db_cfg['default_email'] : $sc[0][Client::COMPANY_EMAIL];
    $password = SHOULD_USE_STAGE ? $db_cfg["default_email_pwd"] : $sc[0][Client::COMPANY_EMAIL_PASSWORD];
    $company_name = $sc[0][Client::NAME];
    $company_logo = $sc[0][Client::COMPANY_LOGO];
    $company_business_name = $sc[0][Client::COMPANY_NAME];
    $company_website = $sc[0][Client::WEBSITE_NAME];
    $_SESSION[WEBSITE_NAME] = $company_website;
    $_SESSION[LOGO] = $company_logo;
}
define('ADMIN_USERNAME', $email);
define('ADMIN_PASSWORD', $password);
// For now for testing purpose this is hard coded leter should be chaged with client email and password
define('HTML_MAIL', true);
define('DEBUG_MAIL', true);

//constants for company Information
define('COMPANY_NAME', $company_name);
define('COMPANY_BUSINESS_NAME', $company_business_name);
define('COMPANY_BUSINESS_HEADING', $company_business_heading);
define('COMPANY_BUSINESS_SUB_HEADING', $company_business_sub_heading);
define('COMPANY_LOGO_PATH', CDN_URL.'img/'.$company_logo);
define('COMPANY_TITLE_LOGO_PATH', CDN_URL.'img/'.$company_title_logo);
define('COMPANY_WEBSITE', $company_website);
define('WEBSITE_TITLE', COMPANY_BUSINESS_NAME);
?>
