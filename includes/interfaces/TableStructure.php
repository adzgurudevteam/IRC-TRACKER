<?php
interface Table
{
    const CLIENT = 'client',
        USERS = 'users',
        DESIGNATIONS = 'designations',
        EMPLOYEE_DETAILS = 'employee_details',
        DEPARTMENTS = 'departments',
        EMPLOYEE_REPORTING_MANAGER = 'employee_reporting_manager',
        COMPANIES = 'companies',
        AUDITS_DATA = 'audits_data',
        QUERY_DATA = 'query_data',
        AUDIT_TYPES = 'audit_types',
        TYPE_OF_TAX = 'type_of_tax',
        COMPANY_INDUSTRY_TYPE = 'company_industry_type',
        AUDIT_TAX_TYPE_HISTORY = 'audit_tax_type_history',
        COMPANY_ASSIGNED_DATA = 'company_assigned_data',
        QUERY_REPLY = 'query_reply',
        COMPANY_NOTICE_DATA = 'company_notice_data',
        AUDIT_MEMO_DATA = 'audit_memo_data',
        QUERY_EXTENSION_DATES = 'query_extension_dates',
        POSITION_PAPER_DATA = 'position_paper_data',
        POSITION_PAPER_EXTENTION_DATES = 'position_paper_extention_dates',
        AUDIT_ASSESSMENT_DATA = 'audit_assessment_data',
        TAX_COLLECTION_DATA = 'tax_collection_data',
        TAX_PAYMENT_HISTORY = 'tax_payment_history',
        AUDIT_TIME_SPENT_DATA = 'audit_time_spent_data',
        AUDIT_CLOSE_REQUEST_DATA = 'audit_close_request_data',
        USER_LAST_SESSION_DATA = 'user_last_session_data',
        POSITION_PAPERS = 'position_papers',
        ASSESSMENT_QUERY_IDS = 'assessment_query_ids',
        QUERY_MEMO_IDS = 'query_memo_ids';
}
interface Users
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        EMPLOYEE_ID = 'employee_id',
        USER_TYPE = 'user_type',
        NAME = 'name',
        EMAIL = 'email',
        MOBILE = 'mobile',
        PASSWORD = 'password',
        PASS_HASH = 'pass_hash',
        REF_ID = 'ref_id',
        ACTIVE = 'active',
        STATUS = 'status',
        CREATION_DATE = 'creation_date',
        UPDATED_AT = 'updated_at',
        PASSWORD_UPDATED_AT = 'password_updated_at',
        INFOTEXT = 'infotext';
}
interface Client
{
    // Client details
    const CLIENT_ID = 'client_id',
        USER_ID = 'user_id',
        NAME = 'name',
        MOBILE = 'mobile',
        EMAIL = 'email',
        ADDRESS = 'address',
        CITY = 'city',
        DISTRICT = 'district',
        PIN_CODE = 'pin_code',
        STATE = 'state',

        WEBSITE_NAME = 'website_name',
    // Company details
        COMPANY_NAME = 'company_name',
        COMPANY_LOGO = 'company_logo',
        COMPANY_EMAIL = 'company_email',
        COMPANY_EMAIL_PASSWORD = 'company_email_password',
        COMPANY_MOBILE = 'company_mobile',
        COMPANY_PHONE = 'company_phone',
        COMPANY_ADDRESS = 'company_address',
        COMPANY_CITY = 'company_city',
        COMPANY_DISTRICT = 'company_district',
        COMPANY_STATE = 'company_state',
        COMPANY_PINCODE = 'company_pincode',
        GSTIN_NO = 'gstin_no',
        TAN = 'tan',
        PAN = 'pan',

    // Client joined and payment
        JOINED_DATE = 'joined_date',
        VALIDITY_PERIOD = 'validity_period',
        EXPIRY_DATE = 'expiry_date',
        TOTAL_CHARGE = 'total_charge',
        RENTAL_TYPE  = 'rental_type',
        RENTAL_CHARGE = 'rental_charge',
        PAID_AMOUNT = 'paid_amount',
        DUE_AMOUNT = 'due_amount',
        PAYMENT_STATUS = 'payment_status',

    // API Enable flag
        SMS_ENABLED = 'sms_enabled',
        GOOGLE_API_ENABLE = 'google_api_enable',
        WHATSAPP_INTEGRATION_ENABLE = 'whatsapp_integration_enable',
        LIVE_LOCATION_TRACKING_ENABLE = 'live_location_tracking_enable',
        MULTI_LANGUAGE_SUPPORT_ENABLE = 'multi_language_support_enable',
    // SMS
        SMS_PACKAGE_CODE  = 'sms_package_code',
        REGISTRATION_CHARGE = 'registration_charge',
        SMS_RECHARGE_DATE = 'sms_recharge_date',
        SMS_VALIDITY_PERIOD = 'sms_validity_period',
        SMS_GATEWAY_TYPE = 'sms_gateway_type',
        SMS_GATEWAY       = 'sms_gateway',
        SMS_ENDPOINT = 'sms_endpoint',
        SMS_SID           = 'sms_sid',
        SEND_AUTO_SMS     = 'send_auto_sms',
        TOTAL_SMS         = 'total_sms',
        SMS_SENT          = 'sms_sent',
        SMS_BALANCE       = 'sms_balance',
        SMS_SID_ENABLE    = 'sms_sid_enable',

    // Master limitg
        MAX_PRODUCT = 'max_product',
        MAX_MANAGER = 'max_user',
        MAX_USER = 'max_manager',
        MAX_CATEGORY = 'max_category',
        MAX_BANNER_CONTENT = 'max_banner_content',
        MAX_SPECIAL_MENU = 'max_special_menu',
        PRODUCT_ADDED = 'product_added',
        USER_ADDED = 'user_added',
        MANAGER_ADDED = 'manager_added',
        CATEGORY_ADDED = 'category_added',

        FEATURE_PLAN = 'feature_plan',
    // The current service of this app that the client using
        PROJECT_SERVICE_TYPE = 'project_service_type',
        APPLICATION_SERVER = 'application_server',
        MAC_ID = 'mac_id',
        IP = 'ip',
        SITE_URL = 'site_url',
    ## Docs
        TRADE_LICENSE = 'trade_license',
        GSTIN_CERTIFICATE = 'gstin_certificate',
        PAN_CARD = 'pan_card',
        COMPANY_DIRECTOR_LIST = 'company_director_list',
        COMPANY_MASTER_DATA = 'company_master_data',
        COMPANY_TYPE = 'company_type',
        CIN_DOCUMENT = 'cin_document',
        MOA_AOA = 'moa_aoa',
        PARTNERSHIP_DEED = 'partnership_deed',
        COMPANY_PHOTOGRAPH_1 = 'company_photograph_1',
        COMPANY_PHOTOGRAPH_2 = 'company_photograph_2',
        COMPANY_PHOTOGRAPH_3 = 'company_photograph_3',
        CORPORATE_MAIL_ID = 'corporate_mail_id',
    ## Account details
        ACCOUNT_NUMBER = 'account_number',
        ACCOUNT_HOLDER_NAME = 'account_holder_name',
        BANK_NAME = 'bank_name',
        IFSC_CODE = 'ifsc_code',
        BRANCH_ADDRESS = 'branch_address',
        CANCELLED_CHEQUE = 'cancelled_cheque',
    // Flag for client account
        ACTIVE            = 'active',
        STATUS            = 'status',
        CREATION_DATE     = 'creation_date';
}
interface DESIGNATIONS
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        DESIGNATION_TITLE = 'designation_title',
        RESPONSIBILITIES = 'responsibilities',
        EXPERIENCE_REQUIRED = 'experience_required',
        ADDED_BY = 'added_by',
        ACTIVE = 'active',
        STATUS = 'status',
        CREATION_DATE = 'creation_date',
        LAST_UPDATE_DATE = 'last_update_date';
}
interface EMPLOYEE_DETAILS
{
    const ID = 'id',
    CLIENT_ID = 'client_id',
    EMPLOYEE_NAME = 'employee_name',
    EMPLOYEE_MOBILE = 'employee_mobile',
    EMPLOYEE_EMAIL = 'employee_email',
    EMPLOYEE_DATE_OF_BIRTH = 'employee_date_of_birth',
    EMPLOYEE_FATHER_NAME = 'employee_father_name',
    EMPLOYEE_MOTHER_NAME = 'employee_mother_name',
    EMPLOYEE_BLOOD_GROUP = 'employee_blood_group',
    EMPLOYEE_DESIGNATION_ID = 'employee_designation_id',
    EMPLOYEE_DATE_OF_JOINNING = 'employee_date_of_joinning',
    EMPLOYEE_IS_EXPERIENCED = 'employee_is_experienced',
    EMPLOYEE_EXPERIENCE_DURATION = 'employee_experience_duration',
    EMPLOYEE_PAYROLL = 'employee_payroll',
    REMARKS = 'remarks',
    REMARK_BY = 'remark_by',
    EMPLOYEE_ADDED_BY = 'employee_added_by',
    ACTIVE = 'active',
    INACTIVE_REASON = 'inactive_reason',
    STATUS = 'status',
    CREATION_DATE = 'creation_date',
    LAST_UPDATE_DATE = 'last_update_date',
    
    EMPLOYEE_ID = 'employee_id',
    DEPARTMENT_ID = 'department_id',
    SALARY_AMOUNT = 'salary_amount',
    WEBMAIL_ADDRESS = 'webmail_address',
    CURRENT_ADDRESS = 'current_address',
    PERMANENT_ADDRESS = 'permanent_address',
    EMERGENCY_CONTACT_PERSON_NAME = 'emergency_contact_person_name',
    EMERGENCY_CONTACT_PERSON_MOBILE_NUMBER = 'emergency_contact_person_mobile_number',
    AADHAAR_NUMBER = 'aadhaar_number',
    PAN_NUMBER = 'pan_number',
    SALARY_ACCOUNT_NUMBER = 'salary_account_number',
    SALARY_ACCOUNT_IFSC_CODE = 'salary_account_ifsc_code',
    UAN_NUMBER = 'uan_number',
    ESIC_IP_NUMBER = 'esic_ip_number',
    LAST_WORKING_DAY = 'last_working_day',
    REPORTING_TIME = 'reporting_time';
}
interface DEPARTMENTS
{
    const ID = 'id',
    CLIENT_ID = 'client_id',
    DEPARTMENT_NAME = 'department_name',
    ADDED_BY = 'added_by',
    STATUS = 'status',
    CREATION_DATE = 'creation_date';
}
interface EMPLOYEE_REPORTING_MANAGER
{
    const ID = 'id',
    CLIENT_ID = 'client_id',
    EMPLOYEE_ID = 'employee_id',
    REPORTING_MANAGER_USER_ID = 'reporting_manager_user_id',
    ASSIGNED_BY_USER_ID = 'assigned_by_user_id',
    ASSIGN_DATE = 'assign_date',
    DISMISS_DATE = 'dismiss_date',
    STATUS = 'status',
    CREATION_DATE = 'creation_date';
}
interface COMPANIES
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_NAME = 'company_name',
        INDUSTRY_TYPE_ID = 'industry_type_id',
        COMPANY_CODE = 'company_code',
        STREET_NUMBER = 'street_number',
        STREET_NAME = 'street_name',
        CITY_OR_SUBURB = 'city_or_suburb',
        STATE = 'state',
        POSTCODE = 'postcode',
        COUNTRY = 'country',
        PHONE = 'phone',
        FAX = 'fax',
        TAX_IDENTIFICATION_NUMBER = 'tax_identification_number',
        BUSINESS_REGISTRATION_NUMBER = 'business_registration_number',
        CASE_CODE = 'case_code',
        ACTIVE = 'active',
        ACTIVE_INACTIVE_DATE = 'active_inactive_date',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface AUDITS_DATA
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        USER_ID = 'user_id',
        AUDIT_EXPECTED_COMPLETE_DATE = 'audit_expected_complete_date',
        AUDIT_START_DATE = 'audit_start_date',
        AUDIT_END_DATE = 'audit_end_date',
        ACTIVE = 'active',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface QUERY_DATA
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        AUDIT_ID = 'audit_id',
        USER_ID = 'user_id',
        MEMO_ID = 'memo_id',
        TAX_TYPE_ID = 'tax_type_id',
        AUDIT_TYPE_ID = 'audit_type_id',
        QUERY_NO = 'query_no',
        TOTAL_NO_OF_QUERY = 'total_no_of_query',
        DATE_OF_ISSUE = 'date_of_issue',
        DAYS_TO_REPLY = 'days_to_reply',
        LAST_DATE_OF_REPLY = 'last_date_of_reply',
        IF_EXTENSION_GRANTED = 'if_extension_granted',
        EXTENSION_DAYS = 'extension_days',
        EXTENTION_END_DATE_TO_REPLY = 'extention_end_date_to_reply',
        DATE_OF_REPLY = 'date_of_reply',
        QUERY_REPLY_IS_SUBMITTED = 'query_reply_is_submitted',
        NO_OF_QUERY_SOLVED = 'no_of_query_solved',
        NO_OF_QUERY_UNSOLVED = 'no_of_query_unsolved',
        QUERY_STATUS = 'query_status',
        REMARKS = 'remarks',
        NOTICE_NO = 'notice_no',
        NOTICE_SECTION = 'notice_section',
        DATE_OF_NOTICE_ISSUE = 'date_of_notice_issue',
        DAYS_TO_REPLY_NOTICE = 'days_to_reply_notice',
        DATE_OF_REPLY_NOTICE = 'date_of_reply_notice',
        NOTICE_STATUS = 'notice_status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface AUDIT_TYPES
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        AUDIT_TYPE = 'audit_type',
        USER_ID = 'user_id',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface TYPE_OF_TAX
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        TYPE_OF_TAX = 'type_of_tax',
        USER_ID = 'user_id',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface COMPANY_INDUSTRY_TYPE
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        INDUSTRY_TYPE = 'industry_type',
        USER_ID = 'user_id',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface AUDIT_TAX_TYPE_HISTORY
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        AUDIT_TYPE_ID = 'audit_type_id',
        TYPE_OF_TAX_ID = 'type_of_tax_id',
        COMPANY_ID = 'company_id',
        START_DATE = 'start_date',
        END_DATE = 'end_date',
        ACTIVE = 'active',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface COMPANY_ASSIGNED_DATA
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        USER_ID = 'user_id',
        AUDITOR_ID = 'auditor_id',
        COMPANY_IDS = 'company_ids',
        PRIMARY_SECONDARY = 'primary_secondary',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface QUERY_REPLY
{
    const ID = 'id',
        CLIENT_ID = 'client_id',
        QUERY_ID = 'query_id',
        DATE_OF_REPLY = 'date_of_reply',
        NO_OF_QUERY_SOLVED = 'no_of_query_solved',
        STATUS = 'status',
        CREATED_AT = 'created_at';
}
interface COMPANY_NOTICE_DATA
{
    const 
        ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        QUERY_IDS = 'query_ids',
        NOTICE_NO = 'notice_no',
        NOTICE_SECTION = 'notice_section',
        DATE_OF_NOTICE_ISSUE = 'date_of_notice_issue',
        DAYS_TO_REPLY_NOTICE = 'days_to_reply_notice',
        LAST_DATE_OF_REPLY = 'last_date_of_reply',
        DATE_OF_REPLY_NOTICE = 'date_of_reply_notice',
        NOTICE_STATUS = 'notice_status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface AUDIT_MEMO_DATA
{
    const 
        ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        SECONDARY_AUDITOR_ID = 'secondary_auditor_id',
        PRIMARY_AUDITOR_ID = 'primary_auditor_id',
        MEMO_NO = 'memo_no',
        TOTAL_NO_OF_QUERY = 'total_no_of_query',
        DATE_OF_ISSUE = 'date_of_issue',
        DAYS_TO_REPLY = 'days_to_reply',
        LAST_DATE_OF_REPLY = 'last_date_of_reply',
        STATUS = 'status',
        USED_UNUSED_STATUS = 'used_unused_status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}

interface QUERY_EXTENSION_DATES
{
    const 
        ID = 'id',
        CLIENT_ID = 'client_id',
        QUERY_ID = 'query_id',
        IF_EXTENSION_GRANTED = 'if_extension_granted',
        EXTENSION_DAYS = 'extension_days',
        EXTENTION_START_DATE = 'extention_start_date',
        EXTENTION_END_DATE = 'extention_end_date',
        ACTIVE = 'active',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface POSITION_PAPER_DATA
{
    const
        ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        QUERY_ID = 'query_id',
        POSITION_PAPER_ID = 'position_paper_id',
        USER_ID = 'user_id',
        DATE_OF_ISSUE = 'date_of_issue',
        INITIAL_SUBMISSION_DATE = 'initial_submission_date',
        EXTENDED_SUBMISSION_DATE = 'extended_submission_date',
        IF_EXTENSION_GRANTED = 'if_extension_granted',
        EXTENSION_DAYS = 'extension_days',
        EXTENTION_END_DATE_TO_REPLY = 'extention_end_date_to_reply',
        DATE_OF_REPLY = 'date_of_reply',
        ACTIVE = 'active',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface POSITION_PAPER_EXTENTION_DATES
{
    const
        ID = 'id',
        CLIENT_ID = 'client_id',
        POSITION_PAPER_ID = 'position_paper_id',
        IF_EXTENSION_GRANTED = 'if_extension_granted',
        EXTENSION_DAYS = 'extension_days',
        EXTENTION_START_DATE = 'extention_start_date',
        EXTENTION_END_DATE = 'extention_end_date',
        ACTIVE = 'active',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface AUDIT_ASSESSMENT_DATA
{
    const 
        ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        USER_ID = 'user_id',
        QUERY_ID = 'query_id',
        POSITION_PAPER_ID = 'position_paper_id',
        CLAIMABLE_TAX_AMOUNT = 'claimable_tax_amount',
        PENALTY_AMOUNT = 'penalty_amount',
        OMITTED_INCOME_AMOUNT = 'omitted_income_amount',
        DATE_OF_ISSUE = 'date_of_issue',
        DATE_OF_CLOSURE = 'date_of_closure',
        REF_NO = 'ref_no',
        ACTIVE = 'active',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface TAX_COLLECTION_DATA
{
    const 
        ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        ASSESSMENT_ID = 'assessment_id',
        TAX_AMOUNT = 'tax_amount',
        PAID_AMOUNT = 'paid_amount',
        PENDING_AMOUNT = 'pending_amount',
        LAST_PAYMENT_DATE = 'last_payment_date',
        PENALTY_AMOUNT = 'penalty_amount',
        PENALTY_PAID_AMOUNT = 'penalty_paid_amount',
        PENALTY_PENDING_AMOUNT = 'penalty_pending_amount',
        PENALTY_LAST_PAYMENT_DATE = 'penalty_last_payment_date',
        PAYMENT_STATUS = 'payment_status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface TAX_PAYMENT_HISTORY
{
    const 
        ID = 'id',
        CLIENT_ID = 'client_id',
        ASSESSMENT_ID = 'assessment_id',
        TAX_COLLECTION_ID = 'tax_collection_id',
        PAYMENT_AMOUNT = 'payment_amount',
        PAYMENT_DATE = 'payment_date',
        PAYMENT_TYPE = 'payment_type',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface AUDIT_TIME_SPENT_DATA
{
    const
        ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        AUDIT_ID = 'audit_id',
        AUDITOR_ID = 'auditor_id',
        DATE = 'date',
        TIME_IN_HRS = 'time_in_hrs',
        LEAVE_HRS = 'leave_hrs',
        TRAINING_HRS = 'training_hrs',
        OTHER_DUTY_HRS = 'other_duty_hrs',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface USER_LAST_SESSION_DATA
{
    const
        ID = 'id',
        CLIENT_ID = 'client_id',
        USER_ID = 'user_id',
        LOGIN_DATE = 'login_date',
        LOGIN_TIME = 'login_time',
        LOGOUT_DATE = 'logout_date',
        LOGOUT_TIME = 'logout_time',
        IP_ADDRESS = 'ip_address',
        INFOTEXT = 'infotext';
}
interface AUDIT_CLOSE_REQUEST_DATA
{
    const
        ID = 'id',
        CLIENT_ID = 'client_id',
        AUDIT_ID = 'audit_id',
        AUDITOR_ID = 'auditor_id',
        APPROVED_BY = 'approved_by',
        APPROVAL_STATUS = 'approval_status',
        REQUEST_DATE = 'request_date',
        APPROVAL_DATE = 'approval_date',
        REASON = 'reason',
        ACTIVE = 'active',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface POSITION_PAPERS
{
    const
        ID = 'id',
        CLIENT_ID = 'client_id',
        REFERENCE_NO = 'reference_no',
        COMPANY_ID = 'company_id',
        DATE_OF_ISSUE = 'date_of_issue',
        INITIAL_SUBMISSION_DATE = 'initial_submission_date',
        DATE_OF_REPLY = 'date_of_reply',
        IF_EXTENSION_GRANTED = 'if_extension_granted',
        EXTENSION_DAYS = 'extension_days',
        EXTENTION_END_DATE_TO_REPLY = 'extention_end_date_to_reply',
        OPEN_CLOSE_STATUS = 'open_close_status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}
interface ASSESSMENT_QUERY_IDS
{
    const
        ID = 'id',
        CLIENT_ID = 'client_id',
        ASSESSMENT_ID = 'assessment_id',
        QUERY_ID = 'query_id',
        POSITION_PAPER_ID = 'position_paper_id',
        ACTIVE = 'active',
        CREATED_AT = 'created_at';
}
interface QUERY_MEMO_IDS
{
    const   
        ID = 'id',
        CLIENT_ID = 'client_id',
        COMPANY_ID = 'company_id',
        QUERY_ID = 'query_id',
        MEMO_ID = 'memo_id',
        USED_UNUSED_STATUS = 'used_unused_status',
        STATUS = 'status',
        CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';
}