<?php
// require_once 'phpmailer/src/Exception.php';
// require_once 'phpmailer/src/PHPMailer.php';
// require_once 'phpmailer/src/SMTP.php';
require_once BASE_DIR. 'vendor/phpmailer/phpmailer/src/Exception.php';
require_once BASE_DIR. 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once BASE_DIR. 'vendor/phpmailer/phpmailer/src/SMTP.php';
require_once BASE_DIR. 'vendor/autoload.php';

function checkSession($type = "")
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION[LOGGEDIN])) {
        session_destroy();
        // if ($type == ADMIN || $type == SELLER || $type == STAFF || $type == SADMIN) {
        //     header('location:' . ADMIN_URL . 'login/');
        //     exit;
        // } else {
        //     header('location:' . PAGE_URL . 'login/');
        //     exit;
        // }
        header('location:' . HOST_URL . 'login/');
        exit();
    }
    if ((isset($_SESSION[LOGGEDIN])) && (isset($_SESSION[EMPLOYEE_ID])) && ($_SESSION[EMPLOYEE_ID] != DEMO_UID) && ($_SESSION[USER_TYPE] != SADMIN)) {
        $getEmpData = getData(Table::EMPLOYEE_DETAILS, [
            EMPLOYEE_DETAILS::ACTIVE
        ], [
            EMPLOYEE_DETAILS::STATUS => ACTIVE_STATUS,
            EMPLOYEE_DETAILS::ID => $_SESSION[EMPLOYEE_ID],
            EMPLOYEE_DETAILS::ACTIVE => 1
        ]);
        // rip($getEmpData);
        if (count($getEmpData) <= 0) {
            $response = "Currently you are Inactive. Please contact your HR Department";
            // $_SESSION['success_msg'] = $response;
            setSessionMsg($response, 'error');
            // $_SESSION[ISSSEMSG] = true;
            // $_SESSION[SEMSG] = $response;
            // $_SESSION[SEMSG_COLOR] = 'error';
            header('location:' . HOST_URL . 'logout/');
            exit();
        }
    }
}
function is_mobile()
{
    $mobile = new Mobile_Detect;
    return ($mobile->isMobile() || $mobile->isTablet() || $mobile->isiOS() || $mobile->isAndroidOS());
}

function is_tablet()
{
    $tablet = new Mobile_Detect;
    return ($tablet->isTablet());
}

function is_ios_tablet()
{
    $tablet = new Mobile_Detect;
    return ($tablet->isiOS() && $tablet->isTablet() && $tablet->isiPad());
}
function isUserLoggedIn()
{
    // return (isset($_SESSION[LOGGEDIN]) && $_SESSION[USER_TYPE] == END_USER);
    return (isset($_SESSION[LOGGEDIN]));
}

function getSpinner($applyOverlay = true, $id = '')
{
    $html = '<div class="spinner-border" role="status" id="' . ($applyOverlay ? "" : $id) . '" style="' . ($applyOverlay ? "" : 'display:none;') . '">
  <span class="sr-only">Loading...</span>
</div>';
    if ($applyOverlay) {
        $html = '<div class="loader-overlay" id="' . $id . '" style="display: none;">' . $html . '</div>';
    }
    echo $html;
}

function getCustomSpinner ()
{
    echo '<div id="loading-bar-spinner" class="spinner" style="display: none;"><div class="spinner-icon"></div></div>';
}

function getAsterics() {
    $html = '<span class="form_label text-danger" style="padding: 5px;">*</span>';
    return $html;
}

function getInlineSpinner($id = '')
{
    $html = '<div class="spinner-border loader-sm" role="status" id="' . $id . '" style="display:none; width: 20px;
    height: 20px;
    margin-right: 5px;">
  <span class="sr-only">Loading...</span>
</div>';

    echo $html;
}
function getInlineSpinner2($id = '')
{
    $html = '<div class="spinner-border loader-sm" role="status" id="' . $id . '" style="display:none; width: 20px;
    height: 20px;
    margin-right: 5px;">
  <span class="sr-only">Loading...</span>
</div>';

    return $html;
}


function drawHeader($heading, $btn_array = [], $back_btn = false)
{
    $h = '
    <div class="row">
        <div class="card w-100">
            <div class="card-body">
            <div class="row">
            <div class="col-6 d-flex">';
    if ($back_btn) {
        $h .= '
            <a href="#" class="text-dark" onclick="javascript:window.history.back()" style="margin-right: 10px;"><i class="fas fa-chevron-left"></i></a>
            ';
    }

    $h .= '<h2>' . $heading . '</h2>
    </div>';
    if (count($btn_array) > 0) {
        $h .= '
        <div class="col-lg-6 text-lg-right d-flex align-items-center justify-content-end">
        ';
        foreach ($btn_array as $btn) {
            $link = $btn['link'];
            $text = $btn['text'];
            $icon = isset($btn['icon']) ? $btn['icon'] : '';
            $class = isset($btn['class']) ? $btn['class'] : 'btn btn-primary';
            $h .= '<a href="' . PAGE_URL . $link . '" class="' . $class . ' mr-1">' . $icon . ' ' . $text  . '</a>';
        }
        $h .= '</div>';
    }
    $h .= '</div>
        </div>
    </div>
    </div>';
    echo $h;
}
/**
 * 
 * @param string $to Recipients
 * @param string $name Name of the recipients
 * @param string $subject Subject of the mail
 * @param string $body Html body mail body
 * @param string $alt_body Plain text mail body
 * @param array $attachment
 * @param array $embeded_img Embded image array that needs to be embedded in html body
 * @return boolean True if sent or False.
 */
function send_mail($to, $name, $subject, $body, $alt_body = '', $attachment = array(), $embeded_img = array())
{
    if (IS_PHPMAILER) {
        $mail = new PHPMailer\PHPMailer\PHPMailer;

        /* If the mail is sent from another domain.
        * For only be used in Production mode. */
        if (SHOULD_USE_STAGE) {
            if (DEBUG_MAIL) {
                $mail->SMTPDebug = 3;
            }
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            // $mail->Host = gethostbyname('smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = ADMIN_USERNAME;
            $mail->Password = ADMIN_PASSWORD;
            $mail->SMTPSecure = "tls";
            // $mail->SMTPSecure = $mail::ENCRYPTION_SMTPS;
            $mail->Port = 587;
        } else {
            if (DEBUG_MAIL) {
                $mail->SMTPDebug = 3;
            }
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            // $mail->Host = gethostbyname('smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = ADMIN_USERNAME;
            $mail->Password = ADMIN_PASSWORD;
            $mail->SMTPSecure = "tls";
            // $mail->SMTPSecure = $mail::ENCRYPTION_SMTPS;
            $mail->Port = 587;
        }
        if (!empty($attachment)) {
            foreach ($attachment as $key => $image) {
                $mail->addAttachment($image, $key);
            }
        }
        if (!empty($embeded_img)) {
            foreach ($embeded_img as $key => $image) {
                $mail->addEmbeddedImage($image, $key);
            }
        }
        $mail->setFrom(ADMIN_USERNAME, COMPANY_BUSINESS_NAME);
        // $mail->From = ADMIN_USERNAME;
        // $mail->FromName = COMPANY_BUSINESS_NAME;
        $mail->addAddress($to, $name);
        $mail->isHTML(HTML_MAIL);

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $alt_body;
        if ($mail->send()) {
            return TRUE;
        } else {
            logError("Sending Mail: ", $subject . " :- " . $mail->ErrorInfo);
            return FALSE;
        }
    } else {
        // Send mail with mail method
        $headers = "From: " . COMPANY_BUSINESS_NAME . ' ' . strip_tags(ADMIN_USERNAME) . "\r\n";
        // $headers = '';
        $headers .= "Reply-To: noreply@account.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        return mail($to, $subject, $body, $headers);
    }
}

function simple_mail ($to, $from_name, $message)
{
    // $to = 'jsaha.pl@gmail.com'; // Add your email address inbetween the '' replacing yourname@yourdomain.com - This is where the form will send a message to.
    $email_subject = "Urgent Message From $from_name, Paperlink CRM";
    $email_body = "You have received an Urgent message from Paperlink CRM.\n\n"."Here are the details:\n\nSender Name: $from_name\n\nMessage:\n$message";
    $headers = "From: Paperlink CRM\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
    $headers .= "noreply@paperlinksoftwares.com";
    return mail($to,$email_subject,$email_body,$headers);
}

function makeThumb($src, $dest, $desired_width)
{

    $type = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    /* read the source image */
    $source_image = "";
    switch ($type) {
        case 'jpg':
        case 'jpeg':
            $source_image = imagecreatefromjpeg($src);
            break;
        case 'png':
            $source_image = imagecreatefrompng($src);
            break;
        case 'gif':
            $source_image = imagecreatefromgif($src);
            break;
        case 'webp':
            $source_image = imagecreatefromwebp($src);
    }
    $width = imagesx($source_image);
    $height = imagesy($source_image);

    /* find the "desired height" of this thumbnail, relative to the desired width  */
    $desired_height = floor($height * ($desired_width / $width));

    /* create a new, "virtual" image */
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

    /* copy source image at a resized size */
    if ($type == 'png') {
        imagealphablending($virtual_image, false);
        imagesavealpha($virtual_image, true);
        $transparent = imagecolorallocatealpha($virtual_image, 255, 255, 255, 127);
        imagefilledrectangle($source_image, 0, 0, $desired_width, $desired_height, $transparent);
    }

    if ($type == 'gif') {
        setTransparency($virtual_image, $source_image);
    }
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    $return = false;
    /* create the physical thumbnail image to its destination */
    if ($type == 'png') {
        $return = imagepng($virtual_image, $dest, 9);
    } else if ($type == 'gif') {
        $return = imagegif($virtual_image, $dest, 100);
    } else if ($type == 'webp') {
        $return = imagewebp($virtual_image, $dest, 100);
    } else {
        $return = imagejpeg($virtual_image, $dest, 100);
    }
    return $return;
}
function setTransparency($new_image, $image_source)
{
    $transparencyIndex = imagecolortransparent($image_source);
    $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
    if ($transparencyIndex >= 0) {
        $transparencyColor = imagecolorsforindex($image_source, $transparencyIndex);
    }
    $transparencyIndex = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
    imagefill($new_image, 0, 0, $transparencyIndex);
    imagecolortransparent($new_image, $transparencyIndex);
}
function removeSpace($str)
{
    $str = str_replace(" ", "_", $str);
    $str = mb_ereg_replace('/_+/', '_', $str);
    return mb_ereg_replace('/[^A-Za-z0-9\.\_]/', '', $str);
}
function craftSlug($str)
{
    // Replace space with -
    $str = str_replace(" ", "-", $str);
    // If there are more than one dash one after another then replace it with one dash
    $str = mb_ereg_replace('/-+/', '-', $str);
    // Replace special character
    return mb_ereg_replace('/[^A-Za-z0-9\.\_]/', '', $str);
}

function logError($title, $message = '', $log_status = "E")
{
    $file = fopen(LOG_FILE, "a");
    $date = date('d/m/Y H:i');
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    $err = error_get_last();
    $ermsg = "";
    if ($err != null && gettype($err) == 'array' && isset($err['message'])) {
        $ermsg = $err['message'];
    }
    $string = $log_status . ": " . $date . " :- [file:" . $caller['file'] . ', line:' . $caller['line'] . ', Err_Info[' . $ermsg . '] || ' . $title . " [--] " . $message . "\n";
    fwrite($file, $string);
    fclose($file);
}

function currencyInWord($num)
{
    $ones = array(
        0 => "ZERO",
        1 => "ONE",
        2 => "TWO",
        3 => "THREE",
        4 => "FOUR",
        5 => "FIVE",
        6 => "SIX",
        7 => "SEVEN",
        8 => "EIGHT",
        9 => "NINE",
        10 => "TEN",
        11 => "ELEVEN",
        12 => "TWELVE",
        13 => "THIRTEEN",
        14 => "FOURTEEN",
        15 => "FIFTEEN",
        16 => "SIXTEEN",
        17 => "SEVENTEEN",
        18 => "EIGHTEEN",
        19 => "NINETEEN",
        "014" => "FOURTEEN"
    );
    $tens = array(
        0 => "ZERO",
        1 => "TEN",
        2 => "TWENTY",
        3 => "THIRTY",
        4 => "FORTY",
        5 => "FIFTY",
        6 => "SIXTY",
        7 => "SEVENTY",
        8 => "EIGHTY",
        9 => "NINETY"
    );
    $hundreds = array(
        "HUNDRED",
        "THOUSAND",
        "LAKH",
        "CRORE",
        "ARAB",
        "KHARAB"
    ); /*limit t quadrillion */
    $num = number_format($num, 2, ".", ",");
    $num_arr = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",", $wholenum));
    krsort($whole_arr, 1);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {

        while (substr($i, 0, 1) == "0")
            $i = substr($i, 1, 5);
        if ($i < 20) {
            /* echo "getting:".$i; */
            if (isset($ones[$i])) {
                $rettxt .= $ones[$i];
            }
        } elseif ($i < 100) {
            if (substr($i, 0, 1) != "0")  $rettxt .= $tens[substr($i, 0, 1)];
            if (substr($i, 1, 1) != "0") $rettxt .= " " . $ones[substr($i, 1, 1)];
        } else {
            if (substr($i, 0, 1) != "0") $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
            if (substr($i, 1, 1) != "0") $rettxt .= " " . $tens[substr($i, 1, 1)];
            if (substr($i, 2, 1) != "0") $rettxt .= " " . $ones[substr($i, 2, 1)];
        }
        if ($key > 0) {
            $rettxt .= " " . $hundreds[$key] . " ";
        }
    }
    if ($decnum > 0) {
        $rettxt .= " and ";
        if ($decnum < 20) {
            $rettxt .= $ones[$decnum];
            $rettxt .= " PAISE";
        } elseif ($decnum < 100) {
            $rettxt .= $tens[substr($decnum, 0, 1)];
            $rettxt .= " " . $ones[substr($decnum, 1, 1)];
            $rettxt .= " PAISE";
        }
    }
    return $rettxt;
}
function altRealEscape($unescaped)
{
    $replacements = array(
        "\x00" => '\x00',
        "\n" => '\n',
        "\r" => '\r',
        "\\" => '\\\\',
        "'" => "\'",
        '"' => '\"',
        "\x1a" => '\x1a'
    );
    return strtr($unescaped, $replacements);
}
function cleanText($text) {
    // Check if the text contains backslashes
    if (strpos($text, '\\') !== false) {
        // If backslashes are found, remove them
        $cleaned_text = stripslashes($text);
    } else {
        // If no backslashes are found, use the original text
        $cleaned_text = $text;
    }
    return $cleaned_text;
}
function get_api_response($url, $method, $data_array)
{
    // echo $url;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    if (strtoupper($method) === 'POST') {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_array);
        curl_setopt($curl, CURLOPT_URL, $url);
    } else {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_URL, $url . "?" . http_build_query($data_array));
    }
    $response =  curl_exec($curl);
    curl_close($curl);
    return $response;
}
// function send_sms($receiver, $message, $template)
// {
//     $client_id = $_SESSION[CLIENT_ID];
//     $sms_details = getData(Table::CLIENT, [
//         Client::SMS_ENABLED,
//         Client::SMS_ENDPOINT,
//         Client::SMS_GATEWAY_TYPE,
//         Client::SMS_GATEWAY,
//         Client::SMS_BALANCE,
//         Client::SMS_SENT,
//         Client::SMS_SID,
//         Client::SMS_SID_ENABLE
//     ], [
//         Client::CLIENT_ID => $client_id
//     ]);
//     if (count($sms_details) == 0) {
//         return ['error' => true, 'message' => 'No client data.'];
//     }
//     $sms_gateway_type = $sms_details[0][Client::SMS_GATEWAY_TYPE];
//     $endpoint = $sms_details[0][Client::SMS_ENDPOINT];
//     $smsdata = getData(Table::SMS_GATEWAY_DETAILS, [], [SMS_Gateway_Details::CLIENT_ID => $client_id]);
//     $smstemplate = getData(
//         Table::SMS_TEMPLATE_ID,
//         [SMS_Template_ID::TEMPLATE_KEY, SMS_Template_ID::TEMPLATE_ID],
//         [SMS_Template_ID::CLIENT_ID => $client_id, SMS_Template_ID::TEMPLATE_NAME => $template]
//     );
//     if (count($smstemplate) == 0) {
//         return ['error' => true, 'message' => 'No template provided.'];
//     }
//     $dataparams = [];
//     foreach ($smsdata as $s) {
//         $dataparams[$s[SMS_Gateway_Details::DATA_KEY]] = $s[SMS_Gateway_Details::VALUE];
//     }
//     $dataparams[$dataparams[MESSAGE_KEY]] = $message;
//     $dataparams[$dataparams[RECEIVER_KEY]] = $receiver;
//     $dataparams[$smstemplate[0][SMS_Template_ID::TEMPLATE_KEY]] = $smstemplate[0][SMS_Template_ID::TEMPLATE_ID];
//     unset($dataparams[MESSAGE_KEY]);
//     unset($dataparams[RECEIVER_KEY]);
//     // rip($dataparams);
//     // echo $endpoint;
//     // exit;
//     return get_api_response(urldecode($endpoint), 'POST', json_encode($dataparams));
// }

function getAPIINFO()
{
    if (strpos(PHP_OS, 'WIN') === 0) {
        $MAC = exec('getmac');
        $MAC = strtok($MAC, ' ');
        $MAC = strtolower(str_replace("-", ":", $MAC));
        return $MAC;
    }
    $str = shell_exec("ifconfig -a | grep -Po 'ether \K.*$'");
    $mac = substr($str, 0, 17);
    $mac = strtolower(str_replace("-", ":", $mac));
    // $ip = $_SERVER['SERVER_ADDR'];
    // $ip = file_get_contents("http://ipecho.net/plain");
    // $r = ['m'=>$mac, 'ip'=>trim($ip)];
    return $mac;
}


function setData($table, $data)
{
    global $db_cfg;
    if ($db_cfg['api']) {
        $data_arr = array('action' => 'set_' . $table, 'api_key' => $db_cfg['api_key'], 'mac' => getAPIINFO(), 'data' => json_encode($data), 'dkey' => $db_cfg['app_name']);
        $api_response = get_api_response(API_URL, 'POST', $data_arr);
        // echo $api_response;
        $res =  json_decode($api_response, true);
        if ($res == null) {
            return ['error' => true, 'message' => 'No Response from API', 'api_url' => $db_cfg['api_url']];
        }
        if ($res['error']) {
            return array();
        }
        if ($res['dberror']) {
            ## Database error occurred
            if (DEBUG_APP) {
                return ['res' => false, 'error' => $res['error_info'], 'sql' => $res['sql']];
            }
            return [];
        }
        return $res['response'];
    } else {
        global $db;
        $res = $db->insert($table, $data, $data);
        return $res;
    }
}

function setMultipleData($table, $data)
{
    global $db_cfg;
    if ($db_cfg['api']) {
        $data_arr = array('action' => 'setmultiple_' . $table, 'api_key' => $db_cfg['api_key'], 'mac' => getAPIINFO(), 'data' => json_encode($data), 'dkey' => $db_cfg['app_name']);
        $api_response = get_api_response(API_URL, 'POST', $data_arr);
        // echo $api_response;
        $res =  json_decode($api_response, true);
        if ($res == null) {
            return ['error' => true, 'message' => 'No Response from API', 'api_url' => $db_cfg['api_url']];
        }
        if ($res['error']) {
            return array();
        }

        if ($res['dberror']) {
            ## Database error occurred
            if (DEBUG_APP) {
                return ['res' => false, 'error' => $res['error_info'], 'sql' => $res['sql']];
            }
            return [];
        }
        return $res['response'];
    } else {
        global $db;
        $res = $db->multipleInsert($table, $data, $data);
        return $res;
    }
}



function getData(
    $table,
    $columns = array(),
    $whereClause = array(),
    $inClause = array(),
    $betweenAndClause = array(),
    $orderBy = array(),
    $orderMode = "ASC",
    $limit = array()
) {
    global $db_cfg;
    if ($db_cfg['api']) {
        $data = $whereClause;
        $data['column'] = $columns;
        $data['orderby'] = $orderBy;
        $data['order_mode'] = $orderMode;
        $data['inclause'] = $inClause;
        $data['limit'] = $limit;
        $data['between_and_clause'] = $betweenAndClause;
        $data_arr = array('action' => 'get_' . $table, 'api_key' => $db_cfg['api_key'], 'mac' => getAPIINFO(), 'data' => json_encode($data), 'dkey' => $db_cfg['app_name']);
        $api_response = get_api_response(API_URL, 'POST', $data_arr);
        // echo $api_response;
        // exit;
        $res =  json_decode($api_response, true);
        if ($res == null) {
            return ['error' => true, 'message' => 'No Response from API', 'api_url' => $db_cfg['api_url']];
        }
        if ($res['error']) {
            return array();
        }
        if ($res['dberror']) {
            ## Database error occurred
            if (DEBUG_APP) {
                return ['res' => false, 'error' => $res['error_info'], 'sql' => $res['sql']];
            }
            return [];
        }
        return $res['response'];
    } else {
        global $db;
        $res = $db->select($table, $columns, $whereClause, $inClause, $betweenAndClause, $orderBy, $orderMode, $limit);
    }

    return $res;
}

function getCustomData($query, $returnJSON = false, $zeroCheck = false)
{
    global $db_cfg;
    if ($db_cfg['api']) {
        $data = [$query];
        $data_arr = array('action' => 'getcustomdata', 'api_key' => $db_cfg['api_key'], 'mac' => getAPIINFO(), 'data' => json_encode($data), 'dkey' => $db_cfg['app_name']);
        $res =  json_decode(get_api_response(API_URL, 'POST', $data_arr), true);
        if ($res == null) {
            return ['error' => true, 'message' => 'No Response from API', 'api_url' => $db_cfg['api_url']];
        }
        if ($res['error']) {
            return array();
        }
        if ($res['dberror']) {
            ## Database error occurred
            if (DEBUG_APP) {
                return ['res' => false, 'error' => $res['error_info'], 'sql' => $res['sql']];
            }
            return [];
        }
        return $res['response'];
    } else {
        global $db;
        $res = $db->getFormattedDataFromQuery($query, $returnJSON, $zeroCheck);
        if ($res === false) {
            $e = $db->errInfo();
            logError("Error in sql query: ", $e['message']);
        }
        return $res;
    }
}

function updateData($table, $data, $where, $inclause = array())
{
    global $db_cfg;
    if ($db_cfg['api']) {
        $data['where'] = $where;
        $data['inclause'] = $inclause;
        $data_arr = array('action' => 'update_' . $table, 'api_key' => $db_cfg['api_key'], 'mac' => getAPIINFO(), 'data' => json_encode($data), 'dkey' => $db_cfg['app_name']);
        $res =  json_decode(get_api_response(API_URL, 'POST', $data_arr), true);
        if ($res == null) {
            return ['error' => true, 'message' => 'No Response from API', 'api_url' => $db_cfg['api_url']];
        }
        if ($res['error']) {
            return array();
        }
        if ($res['dberror']) {
            ## Database error occurred
            if (DEBUG_APP) {
                return ['res' => false, 'error' => $res['error_info'], 'sql' => $res['sql']];
            }
            return [];
        }
        return $res['response'];
    } else {
        global $db;
        $res = $db->update($table, $data, $where, $inclause);
        return $res;
    }
}

function deleteData($table, $where, $inclause = array())
{
    global $db_cfg;
    if ($db_cfg['api']) {
        $where['inclause'] = $inclause;
        $data_arr = array('action' => 'delete_' . $table, 'api_key' => $db_cfg['api_key'], 'mac' => getAPIINFO(), 'data' => json_encode($where), 'dkey' => $db_cfg['app_name']);
        $res =  json_decode(get_api_response(API_URL, 'POST', $data_arr), true);
        if ($res == null) {
            return ['error' => true, 'message' => 'No Response from API', 'api_url' => $db_cfg['api_url']];
        }
        if ($res['error']) {
            return array();
        }
        if ($res['dberror']) {
            ## Database error occurred
            if (DEBUG_APP) {
                return ['res' => false, 'error' => $res['error_info'], 'sql' => $res['sql']];
            }
            return [];
        }
        return $res['response'];
    } else {
        global $db;
        $res = $db->delete($table, $where, $inclause);
        return $res;
    }
}

function hakai()
{
    global $db;
    if (!API) {
        $db->destroy();
        $db = null;
    }
}

function rip($a)
{
    echo "<pre>";
    print_r($a);
    echo "</pre>";
}
function codeRip($a)
{
    echo '<pre><code>';
    print_r($a);
    echo '</code></pre>';
}

function setSessionError($msg)
{
    $_SESSION[SERR] = true;
    $_SESSION[SERR_MSG] = $msg;
}

function decodeProductDescription($string)
{
    $output = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($string));
    return html_entity_decode($output,  ENT_QUOTES, 'UTF-8');
}

function appendCountryCode($number, $code = "+91")
{
    return preg_replace('/^(?:\+?27|\+?91|0)?/', $code, $number);
}
function removeCountryCode($number)
{
    return preg_replace('/^(?:\+?27|\+?91|0)?/', '', $number);
}

function navigate($tohandlepage, $query_string = [], $jsnaviage = false)
{
    $lk = '' . PAGE_URL . $tohandlepage . "/";
    if (!empty($query_string)) {
        $qs = '';
        foreach ($query_string as $k => $q) {
            $qs .= $k . "=" . urlencode($q) . "&";
        }
        $qs = rtrim($qs, "&");
        $lk .= '?' . $qs;
    }
    if (!$jsnaviage) {
        header('location:' . $lk);
        exit;
    } else {
        $sr = <<<HTML
<script>window.location="{$lk}";</script>
HTML;
        echo $sr;
        exit;
    }
}

/***
 * Get the User IP
 * @return string the User IP
 */
// function getUserIpAddr()
// {
//     if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//         //ip from share internet
//         $ip = $_SERVER['HTTP_CLIENT_IP'];
//     } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//         //ip pass from proxy
//         $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//     } else {
//         $ip = $_SERVER['REMOTE_ADDR'];
//     }
//     return $ip;
// }

function getUserIpAddr()
{
    // Check for the presence of the HTTP_X_FORWARDED_FOR header
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // The header might contain multiple IP addresses separated by commas
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        // Get the first IP address in the list
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // IP address from shared internet or proxy
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        // Direct IP address
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Validate IP address (optional)
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    } else {
        return 'UNKNOWN';
    }
}

// Functions Made By Jyotirmoy 

function getInfoText($returnUserAgent = true)
{
    $userAgent = $returnUserAgent ? '|| ' .  $_SERVER['HTTP_USER_AGENT'] : '';
    $infotext = getUserIpAddr() . ' ' . $userAgent . ' || ' . date('Y-m-d H:i:s');
    return $infotext;
}

/**
 * This Function can Format the Amount into Indian Currency Format
 */
function numFormatIndia($num)
{

    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3); // extracts the last three digits
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}
// INDIAN CURRENCY FORMAT FUNCTION [Made By Jyotirmoy Saha]
function moneyFormatIndia($amount)
{

    $amount = round($amount, 2);

    $amountArray =  explode('.', $amount);
    if (count($amountArray) == 1) {
        $int = $amountArray[0];
        $des = 00;
    } else {
        $int = $amountArray[0];
        $des = $amountArray[1];
    }
    if (strlen($des) == 1) {
        $des = $des . "0";
    }
    if ($int >= 0) {
        $int = numFormatIndia($int);
        $themoney = $int . "." . $des;
    } else {
        $int = abs($int);
        $int = numFormatIndia($int);
        $themoney = "-" . $int . "." . $des;
    }
    return $themoney;
}
// INDIAN CURRENCY TO WORDS FUNCTION [Made By Jyotirmoy Saha]
function getIndianCurrency(float $number, $includeRupee = true)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    $txt = ($Rupees) ? ($Rupees ? (($includeRupee) ? 'Rupees ' : '') . $Rupees : '') . $paise . ' ONLY' : '';
    return ($txt);
}
//function for getting the current date and time [made by Jyotirmoy Saha]
function getToday($includeTime = true, $dformat = 'Y-m-d', $tformat = 'H:i:s')
{
    $dt = date($dformat);
    if ($includeTime) {
        $dt = date($dformat . ' ' . $tformat);
    }
    return $dt;
}

function aka_ucwords($str, $exceptions = [], $separator = " ")
{
    $out = "";
    foreach (explode($separator, $str) as $word) {
        $out .= (!in_array($word, $exceptions)) ? ucfirst(strtolower($word)) . $separator : $word . $separator;
    }
    return rtrim($out);
}

function getFormattedDateTime($date = '', $format = 'd/m/Y')
{
    $date = ($date == '') ? date('Y-m-d') : $date;
    $d = date_format(date_create($date), $format);
    return $d;
}

function customLoader($id)
{
    $l = '
        <div class="overlay" id="' . $id . '" style="display:none;">
            <div id="text"><div class="lds-dual-ring" style="margin-right: 20px;"></div>Please Wait...</div>
        </div>
    ';
    return $l;
}

function getErrorCard($pid)
{
    $x = '
        <div class="animated fadeInRight alert alert-danger container" role="alert" id="error-card" style="display: none;">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>  
                <span style="font-weight: bold; font-size:14px;">Important Message</span>
            <p id="' . $pid . '">Error message is Showing here</p>
        </div>
    ';
    echo $x;
}

function setSessionMsg($msg, $color = "success")
{
    $_SESSION[ISSSEMSG] = true;
    $_SESSION[SEMSG] = $msg;
    $_SESSION[SEMSG_COLOR] = $color;
}

function test_input($data)
{
    $data = trim($data);
    $data = stripcslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//date function made by Jyotirmoy Saha
function getDt($dt, $tm = false, $format = 12, $df = "d/m/Y")
{
    $date = date_format(date_create($dt), $df);
    if ($tm) {
        $date = ($format == 12) ?  date_format(date_create($dt), $df . ' h:i a') : date_format(date_create($dt), $df . ' H:i a');
    }
    return $date;
}

//  function getDesiredDate ($format="Y-m-d", $date= date("Y-m-d")) {
//     //  if ($format ==) {
//     //      # code...
//     //  }
//  }

function getWorkingHrs ($t1, $t2)
{
    $time1 = new DateTime((count(explode(":", $t1)) == 2) ? $t1.':00' : $t1);
    $time2 = new DateTime((count(explode(":", $t2)) == 2) ? $t2.':00' : $t2);
    $interval = $time1->diff($time2);

    return $interval->format('%H:%I');
}
function makeUrltoLink($string) {
    // The Regular Expression filter
    $reg_pattern = "/(((http|https|ftp|ftps)\:\/\/)|(www\.))[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/";

    // make the urls to hyperlinks
    if ($string != "") {
        return preg_replace($reg_pattern, '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>', $string);
    } else {
        return $string;
    }
}
function getDateDiff($start_date, $end_date) {
    // echo "start_date: ".$start_date.", end_date: ".$end_date;
    if (($start_date=="") || ($end_date=="")) {
        return "Cannot Calculate";
    }
    $date1 = $start_date;
    $date2 = $end_date;
    
    $diff = abs(strtotime($date2) - strtotime($date1));
    
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    
    // printf("%d years, %d months, %d days\n", $years, $months, $days);
    $res = "";
    if ($years > 0) {
        $res .= $years." Years, ";
    }
    if ($months > 0) {
        $res .= $months." Months, ";
    }
    if ($days > 0) {
        $res .= $days." Days";
    }
    return $res;
}
// function getMonthsDifference($startDate, $endDate) {
//     $start = new DateTime($startDate);
//     $end = new DateTime($endDate);
    
//     // Get the difference
//     $interval = $start->diff($end);
    
//     // Calculate the total number of months
//     $months = $interval->y * 12 + $interval->m;
    
//     // If the end day is earlier in the month than the start day, subtract one month
//     if ($end->format('d') < $start->format('d')) {
//         $months--;
//     }
    
//     return $months;
// }
function getMonthsDifference($start_date, $end_date) {
    if (($start_date=="") || ($end_date=="") || ($start_date>$end_date)) {
        return "Cannot Calculate";
    }
    $date1 = $start_date;
    $date2 = $end_date;
    
    $diff = abs(strtotime($date2) - strtotime($date1));
    
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    
    $nmonths = floor(($diff) / (30*60*60*24));
    $ndays = floor(($diff)/ (60*60*24));
    
    // printf("%d years, %d months, %d days\n", $years, $months, $days);
    // $res = "";
    // if ($years > 0) {
    //     $res .= $years." Years, ";
    // }
    // if ($months > 0) {
    //     $res .= $months." Months, ";
    // }
    // if ($days > 0) {
    //     $res .= $days." Days";
    // }
    return $nmonths;
}
/**
 * Adds days from entered date
 *
 * @param string $date The date you want to add days from.
 * @param int/string $daysToAdd The number of days you want to add from $date.
 * @return date Returns the increased date value
 */
function addDaysToDate($date, $daysToAdd) {
    if ($daysToAdd!="") {
        $da=((int)$daysToAdd-1);
    }
    // Create a DateTime object from the input date string
    $dateTime = new DateTime($date);

    // Add the specified number of days
    $dateTime->modify("+$da days");

    // Return the new date in Y-m-d format
    return $dateTime->format('Y-m-d');
}
function getArrayValuesRecursively(array $array)
{
    $values = [];
    foreach ($array as $value) {
        if (is_array($value)) {
            $values = array_merge($values,
                getArrayValuesRecursively($value));
        } else {
            $values[] = $value;
        }
    }
    return $values;
}
function maintenanceModeOn()
{
    include ('main/pages/pause.php');
    hakai();
    exit();
}
/**
 * Calculates the year-on-year tax collection growth percentage.
 *
 * @param float $currentYear The tax collection for the current year.
 * @param float $lastYear The tax collection for the last year.
 * @return float The percentage growth from last year to this year.
 */
function calculateYearOnYearGrowth($currentYear, $lastYear) {
    // Avoid division by zero if last year's tax collection is zero
    if ($lastYear == 0) {
        if ($currentYear == 0) {
            // Both current year and last year are zero, no growth
            return 0;
        }
        // Last year's value is zero but this year's value is not zero, infinite growth
        return 100; // This can be adjusted based on how you want to represent infinite growth
    }
    
    // Calculate the growth percentage
    $growth = (($currentYear - $lastYear) / $lastYear) * 100;
    
    return $growth;
}