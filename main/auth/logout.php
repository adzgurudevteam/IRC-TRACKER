<?php
if (isset($_SESSION[LAST_SESSION_DATA_ROW_ID])) {
    $updateNewData = updateData(Table::USER_LAST_SESSION_DATA,[
        USER_LAST_SESSION_DATA::LOGOUT_DATE => getToday(false),
        USER_LAST_SESSION_DATA::LOGOUT_TIME => date('H:i:s'),
    ],[
        USER_LAST_SESSION_DATA::ID => $_SESSION[LAST_SESSION_DATA_ROW_ID],
        USER_LAST_SESSION_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
    ]);
    if(!$updateNewData['res']){
        logError("Unabled to update last login data.",$updateNewData['error']);
    }
}

unset($_SESSION[CLIENT_ID]);
unset($_SESSION[LOGGEDIN]);
unset($_SESSION[USER_ID]);
unset($_SESSION[RID]);
unset($_SESSION[USERNAME]);
unset($_SESSION[USER_TYPE]);
unset($_SESSION[EMPLOYEE_ID]);

unset($_SESSION[LAST_LOGIN_DATE]);
unset($_SESSION[LAST_LOGIN_TIME]);
unset($_SESSION[LAST_LOGOUT_DATE]);
unset($_SESSION[LAST_LOGOUT_TIME]);
unset($_SESSION[LAST_SESSION_IP]);
unset($_SESSION[LAST_SESSION_DATA_ROW_ID]);



header('location:'.HOST_URL);
?>