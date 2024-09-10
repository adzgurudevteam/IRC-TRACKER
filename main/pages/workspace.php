<?php 
function printContent()
{
    $fullHtml=$query_raised=$query_pending=$query_replied=$query_overdue='';
    $notice_issued=$notice_pending=$notice_replied=$notice_overdue='';
    $positionPaperIssued=$positionPaperPending=$positionPaperReplied=$positionPaperOverdue='';
    //today variables
    $end_of_due_date=$end_of_extended_due_date=$overdue_companies='';
    $endDueNoticeDate=$overdueNotices='';
    $endPositionDueDate=$endPositionExtDueDate=$overduePositionPapers='';
    $getCompData = getData(Table::COMPANIES, [
        COMPANIES::COMPANY_NAME,
        COMPANIES::CREATED_AT,
        COMPANIES::ID
    ], [
        COMPANIES::CLIENT_ID => $_SESSION[CLIENT_ID],
        COMPANIES::STATUS => ACTIVE_STATUS
    ]);
    if (count($getCompData) > 0) {
        $query_raised_count=$query_solved=$query_pending_count=$query_replied_count=$query_overdue_count=0;
        $endOfDueDateComsArr=$qRunningOverdue=$endExtDueDate=[];

        $noticeIssuedCount=$noticePendingCount=$noticeRepliedCount=$noticeOverdueCount=0;
        $endDueNoticeDateArr=$overdueNoticesArr=[];

        $positionPaperIssuedCount=$positionPaperPendingCount=$positionPaperRepliedCount=$positionPaperOverdueCount=0;
        $endPositionDueDateArr=$endPositionExtDueDateArr=$overduePositionPapersArr=[];
        foreach ($getCompData as $k => $v) {
            $audWh = [
                AUDITS_DATA::STATUS => ACTIVE_STATUS,
                AUDITS_DATA::COMPANY_ID => $v[COMPANIES::ID]
            ];
            if ($_SESSION[USER_TYPE]==EMPLOYEE) {
                $audWh[AUDITS_DATA::USER_ID] = $_SESSION[RID];
            }
            $getAuditData = getData(Table::AUDITS_DATA, [
                AUDITS_DATA::ID,
                AUDITS_DATA::AUDIT_START_DATE,
                AUDITS_DATA::AUDIT_END_DATE,
                AUDITS_DATA::ACTIVE
            ], $audWh);
            //Query
            if (count($getAuditData) > 0) {
                foreach ($getAuditData as $ak => $av) {
                    $getQueryData=getData(Table::QUERY_DATA,[
                        QUERY_DATA::QUERY_REPLY_IS_SUBMITTED,
                        QUERY_DATA::ID,
                        QUERY_DATA::QUERY_NO,
                        QUERY_DATA::QUERY_STATUS,
                        QUERY_DATA::TOTAL_NO_OF_QUERY,
                        QUERY_DATA::NO_OF_QUERY_SOLVED,
                        QUERY_DATA::NO_OF_QUERY_UNSOLVED,
                        QUERY_DATA::LAST_DATE_OF_REPLY
                    ],[
                        QUERY_DATA::COMPANY_ID=>$v[COMPANIES::ID],
                        QUERY_DATA::CLIENT_ID=>$_SESSION[CLIENT_ID]
                    ]);
                    $query_raised_count=$query_pending_count=$query_solved=$query_overdue_count=0;
                    if (count($getQueryData)>0) {
                        foreach ($getQueryData as $sqdk => $sqdv) {
                            $getExtData = getData(Table::QUERY_EXTENSION_DATES,[
                                QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED,
                                QUERY_EXTENSION_DATES::EXTENTION_END_DATE
                            ],[
                                QUERY_EXTENSION_DATES::QUERY_ID=>$sqdv[QUERY_DATA::ID],
                                QUERY_EXTENSION_DATES::ACTIVE=>1
                            ]);
                            $query_raised_count += $sqdv[QUERY_DATA::TOTAL_NO_OF_QUERY];
                            $query_solved += $sqdv[QUERY_DATA::NO_OF_QUERY_SOLVED];
                            if (($sqdv[QUERY_DATA::LAST_DATE_OF_REPLY] < getToday(false)) && (!in_array($sqdv[QUERY_DATA::QUERY_STATUS],[2,4]))) {
                                $query_overdue_count += ($sqdv[QUERY_DATA::TOTAL_NO_OF_QUERY]-$sqdv[QUERY_DATA::NO_OF_QUERY_SOLVED]);
                                // $qRunningOverdue[] = $v[COMPANIES::COMPANY_NAME];
                                $qop = '';
                                switch ($_SESSION[USER_TYPE]) {
                                    case EMPLOYEE:
                                        $qop = 'auditor-audits/';
                                        break;
                                    case SADMIN:
                                        $qop = 'sadmin-audits/';
                                        break;
                                    case ADMIN:
                                        $qop = 'admin-audits/';
                                        break;
                                }
                                $oc_name = '<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$sqdv[QUERY_DATA::QUERY_NO].')</b> [<span class="text-danger">Since:</span> '.getFormattedDateTime($sqdv[QUERY_DATA::LAST_DATE_OF_REPLY]).']';
                                $endOverDueComNames = '<a class="workspaceLink" href="'.HOST_URL.$qop.'?c='.$v[COMPANIES::ID].'#query">'.$oc_name.'</a>';
                                if (count($qRunningOverdue)>0) {
                                    if (!in_array($endOverDueComNames,$qRunningOverdue)) {
                                        $qRunningOverdue[] = $endOverDueComNames;
                                    }
                                } else {
                                    $qRunningOverdue[] = $endOverDueComNames;
                                }
                            }
                            if (($sqdv[QUERY_DATA::LAST_DATE_OF_REPLY] == getToday(false)) && (!in_array($sqdv[QUERY_DATA::QUERY_STATUS],[2,4]))) {
                                // echo QUERY_DATA::LAST_DATE_OF_REPLY.': '.$sqdv[QUERY_DATA::LAST_DATE_OF_REPLY];
                                $qp = '';
                                switch ($_SESSION[USER_TYPE]) {
                                    case EMPLOYEE:
                                        $qp = 'auditor-audits/';
                                        break;
                                    case SADMIN:
                                        $qp = 'sadmin-audits/';
                                        break;
                                    case ADMIN:
                                        $qp = 'admin-audits/';
                                        break;
                                }
                                $endDueDateQcomNames='<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$sqdv[QUERY_DATA::QUERY_NO].')</b>';
                                $endDueComNames = '<a class="workspaceLink" href="'.HOST_URL.$qp.'?c='.$v[COMPANIES::ID].'#query">'.$endDueDateQcomNames.'</a>';
                                if (count($endOfDueDateComsArr)>0) {
                                    if (!in_array($endDueComNames,$endOfDueDateComsArr)) {
                                        $endOfDueDateComsArr[] = $endDueComNames;
                                    }
                                } else {
                                    $endOfDueDateComsArr[] = $endDueComNames;
                                }
                                // rip($endOfDueDateComsArr);
                            }
                            if (count($getExtData)>0) {
                                if (!in_array($sqdv[QUERY_DATA::QUERY_STATUS],[2,4])) {
                                    if (($getExtData[0][QUERY_EXTENSION_DATES::IF_EXTENSION_GRANTED]==1)&&($getExtData[0][QUERY_EXTENSION_DATES::EXTENTION_END_DATE] == getToday(false))) {
                                        // $endExtDueDate[]=$v[COMPANIES::COMPANY_NAME];
                                        $qep = '';
                                        switch ($_SESSION[USER_TYPE]) {
                                            case EMPLOYEE:
                                                $qep = 'auditor-audits/';
                                                break;
                                            case SADMIN:
                                                $qep = 'sadmin-audits/';
                                                break;
                                            case ADMIN:
                                                $qep = 'admin-audits/';
                                                break;
                                        }
                                        $endExtDueDateQcomNames='<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$sqdv[QUERY_DATA::QUERY_NO].')</b>';
                                        $endExtDueComNames = '<a class="workspaceLink" href="'.HOST_URL.$qep.'?c='.$v[COMPANIES::ID].'#query">'.$endExtDueDateQcomNames.'</a>';
                                        if (count($endExtDueDate)>0) {
                                            if (!in_array($endExtDueComNames,$endExtDueDate)) {
                                                $endExtDueDate[] = $endExtDueComNames;
                                            }
                                        } else {
                                            $endExtDueDate[] = $endExtDueComNames;
                                        }
                                    }
                                } 
                            }
                        }
                        $query_pending_count = ($query_raised_count != 0) ? ($query_raised_count-$query_solved) : 0;
                    }
                    $query_raised .='
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$query_raised_count.'</span></div>
                    ';
                    $query_pending .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$query_pending_count.'</span></div>
                    ';
                    $query_replied .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$query_solved.'</span></div>
                    ';
                    $query_overdue .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$query_overdue_count.'</span></div>
                    ';
                }
            }
            //Notice
            if (count($getAuditData) > 0) {
                foreach ($getAuditData as $ak => $av) {
                    $getNoticeData=getData(Table::COMPANY_NOTICE_DATA,[
                        COMPANY_NOTICE_DATA::ID,
                        COMPANY_NOTICE_DATA::NOTICE_NO,
                        COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY,
                        COMPANY_NOTICE_DATA::NOTICE_STATUS
                    ],[
                        COMPANY_NOTICE_DATA::COMPANY_ID => $v[COMPANIES::ID],
                        COMPANY_NOTICE_DATA::CLIENT_ID => $_SESSION[CLIENT_ID]
                    ]);
                    $noticeIssuedCount=$noticePendingCount=$noticeRepliedCount=$noticeOverdueCount=0;
                    if (count($getNoticeData)>0) {
                        $noticeIssuedCount = count($getNoticeData);
                        foreach ($getNoticeData as $ndk => $ndv) {
                            if ($ndv[COMPANY_NOTICE_DATA::NOTICE_STATUS]==1) {
                                $noticeRepliedCount ++;
                            } else {
                                if (($ndv[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY]<getToday(false)) && ($ndv[COMPANY_NOTICE_DATA::NOTICE_STATUS]!=1)) {
                                    $noticeOverdueCount++;
                                    $oc_name = '<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$ndv[COMPANY_NOTICE_DATA::NOTICE_NO].')</b> [<span class="text-danger">Since:</span> '.getFormattedDateTime($ndv[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY]).']';
                                    $nop = '';
                                    switch ($_SESSION[USER_TYPE]) {
                                        case EMPLOYEE:
                                            $nop = 'auditor-audits/';
                                            break;
                                        case SADMIN:
                                            $nop = 'sadmin-audits/';
                                            break;
                                        case ADMIN:
                                            $nop = 'admin-audits/';
                                            break;
                                    }
                                    $overdueNoticeNames = '<a class="workspaceLink" href="'.HOST_URL.$nop.'?c='.$v[COMPANIES::ID].'#notice">'.$oc_name.'</a>';
                                    if (count($overdueNoticesArr)>0) {
                                        if (!in_array($overdueNoticeNames,$overdueNoticesArr)) {
                                            $overdueNoticesArr[] = $overdueNoticeNames;
                                        }
                                    } else {
                                        $overdueNoticesArr[] = $overdueNoticeNames;
                                    }
                                }
                                if ($ndv[COMPANY_NOTICE_DATA::LAST_DATE_OF_REPLY]==getToday(false)) {
                                    // $endDueNoticeDateArr[]=$v[COMPANIES::COMPANY_NAME];
                                    $np = '';
                                    switch ($_SESSION[USER_TYPE]) {
                                        case EMPLOYEE:
                                            $np = 'auditor-audits/';
                                            break;
                                        case SADMIN:
                                            $np = 'sadmin-audits/';
                                            break;
                                        case ADMIN:
                                            $np = 'admin-audits/';
                                            break;
                                    }
                                    $endDueDateNoticeNames='<b>'.$v[COMPANIES::COMPANY_NAME].' (#'.$ndv[COMPANY_NOTICE_DATA::NOTICE_NO].'</b>)';
                                    $endDueNoticeNames = '<a class="workspaceLink" href="'.HOST_URL.$np.'?c='.$v[COMPANIES::ID].'#notice">'.$endDueDateNoticeNames.'</a>';
                                    if (count($endDueNoticeDateArr)>0) {
                                        if (!in_array($endDueNoticeNames,$endDueNoticeDateArr)) {
                                            $endDueNoticeDateArr[] = $endDueNoticeNames;
                                        }
                                    } else {
                                        $endDueNoticeDateArr[] = $endDueNoticeNames;
                                    }
                                }
                            }
                        }
                        $noticePendingCount = ($noticeRepliedCount>0)?($noticeIssuedCount-$noticeRepliedCount):$noticeIssuedCount;
                    }
                    $notice_issued .='
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$noticeIssuedCount.'</span></div>
                    ';
                    $notice_pending .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$noticePendingCount.'</span></div>
                    ';
                    $notice_replied .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$noticeRepliedCount.'</span></div>
                    ';
                    $notice_overdue .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$noticeOverdueCount.'</span></div>
                    ';
                }
            }
            //Position Paper
            if (count($getAuditData) > 0) {
                foreach ($getAuditData as $ak => $av) {
                    $getPositionPaperData=getData(Table::POSITION_PAPERS,[
                        POSITION_PAPERS::INITIAL_SUBMISSION_DATE,
                        POSITION_PAPERS::OPEN_CLOSE_STATUS,
                        POSITION_PAPERS::ID,
                        POSITION_PAPERS::REFERENCE_NO
                    ],[
                        POSITION_PAPERS::COMPANY_ID=>$v[COMPANIES::ID],
                        POSITION_PAPERS::CLIENT_ID=>$_SESSION[CLIENT_ID]
                    ]);
                    $positionPaperIssuedCount=$positionPaperPendingCount=$positionPaperRepliedCount=$positionPaperOverdueCount=0;
                    if (count($getPositionPaperData)>0) {
                        $positionPaperIssuedCount = count($getPositionPaperData);
                        foreach ($getPositionPaperData as $ppdk => $ppdv) {
                            if ($ppdv[POSITION_PAPERS::OPEN_CLOSE_STATUS]==1) {
                                // $positionPaperRepliedCount++;
                                $getPositionExtData=getData(Table::POSITION_PAPER_EXTENTION_DATES,[
                                    POSITION_PAPER_EXTENTION_DATES::ACTIVE,
                                    POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED,
                                    POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE
                                ],[
                                    POSITION_PAPER_EXTENTION_DATES::POSITION_PAPER_ID=>$ppdv[POSITION_PAPERS::ID],
                                    POSITION_PAPER_EXTENTION_DATES::CLIENT_ID=>$_SESSION[CLIENT_ID],
                                    POSITION_PAPER_EXTENTION_DATES::ACTIVE=>1,
                                    POSITION_PAPER_EXTENTION_DATES::IF_EXTENSION_GRANTED=>1
                                ]);
                                $positionExtFound=$positionExtOverDue=false;
                                if (count($getPositionExtData)>0) {
                                    $positionExtFound=true;
                                    if ($getPositionExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]<getToday(false)) {
                                        $positionExtOverDue=true;
                                    }
                                }
                                if ($ppdv[POSITION_PAPERS::INITIAL_SUBMISSION_DATE]<getToday(false)) {
                                    // $getQnameOverdue = getData(Table::QUERY_DATA,[QUERY_DATA::QUERY_NO],[QUERY_DATA::ID=>$ppdv[POSITION_PAPER_DATA::QUERY_ID]]);
                                    $positionPaperOverdueCount++;
                                    $ppp = '';
                                    switch ($_SESSION[USER_TYPE]) {
                                        case EMPLOYEE:
                                            $ppp = 'auditor-audits/';
                                            break;
                                        case SADMIN:
                                            $ppp = 'sadmin-audits/';
                                            break;
                                        case ADMIN:
                                            $ppp = 'admin-audits/';
                                            break;
                                    }
                                    $QnameWcomOverdue = '<b>#'.$ppdv[POSITION_PAPERS::REFERENCE_NO].'</b> from: '.$v[COMPANIES::COMPANY_NAME].' [<span class="text-danger">Since:</span> '.getFormattedDateTime($ppdv[POSITION_PAPER_DATA::INITIAL_SUBMISSION_DATE]).']';
                                    $QnameWcomOverdueNames = '<a class="workspaceLink" href="'.HOST_URL.$ppp.'?c='.$v[COMPANIES::ID].'#position-paper">'.$QnameWcomOverdue.'</a>';
                                    if ($positionExtFound) {
                                        if ($positionExtOverDue) {
                                            $positionPaperOverdueCount++;
                                            // $overduePositionPapersArr[]=$QnameWcomOverdue;
                                            if (count($overduePositionPapersArr)>0) {
                                                if (!in_array($QnameWcomOverdueNames,$overduePositionPapersArr)) {
                                                    $overduePositionPapersArr[] = $QnameWcomOverdueNames;
                                                }
                                            } else {
                                                $overduePositionPapersArr[] = $QnameWcomOverdueNames;
                                            }
                                        }
                                    } else {
                                        // $overduePositionPapersArr[]=$QnameWcomOverdue;
                                        if (count($overduePositionPapersArr)>0) {
                                            if (!in_array($QnameWcomOverdueNames,$overduePositionPapersArr)) {
                                                $overduePositionPapersArr[] = $QnameWcomOverdueNames;
                                            }
                                        } else {
                                            $overduePositionPapersArr[] = $QnameWcomOverdueNames;
                                        }
                                    }
                                }
                                if ($ppdv[POSITION_PAPER_DATA::INITIAL_SUBMISSION_DATE]==getToday(false)) {
                                    // $getQname = getData(Table::QUERY_DATA,[QUERY_DATA::QUERY_NO],[QUERY_DATA::ID=>$ppdv[POSITION_PAPER_DATA::QUERY_ID]]);
                                    $ppop = '';
                                    switch ($_SESSION[USER_TYPE]) {
                                        case EMPLOYEE:
                                            $ppop = 'auditor-audits/';
                                            break;
                                        case SADMIN:
                                            $ppop = 'sadmin-audits/';
                                            break;
                                        case ADMIN:
                                            $ppop = 'admin-audits/';
                                            break;
                                    }
                                    $QnameWcom = '<b>#'.$ppdv[POSITION_PAPERS::REFERENCE_NO].'</b> from: '.$v[COMPANIES::COMPANY_NAME];
                                    $QnameWcomNames = '<a class="workspaceLink" href="'.HOST_URL.$ppop.'?c='.$v[COMPANIES::ID].'#position-paper">'.$QnameWcom.'</a>';
                                    if ($positionExtFound) {
                                        if ($getPositionExtData[0][POSITION_PAPER_EXTENTION_DATES::EXTENTION_END_DATE]==getToday(false)) {
                                            // $endPositionDueDateArr[]=$QnameWcom;
                                            if (count($endPositionExtDueDateArr)>0) {
                                                if (!in_array($QnameWcomNames,$endPositionExtDueDateArr)) {
                                                    $endPositionExtDueDateArr[] = $QnameWcomNames;
                                                }
                                            } else {
                                                $endPositionExtDueDateArr[] = $QnameWcomNames;
                                            }
                                        }
                                    } else {
                                        if (count($endPositionDueDateArr)>0) {
                                            if (!in_array($QnameWcomNames,$endPositionDueDateArr)) {
                                                $endPositionDueDateArr[] = $QnameWcomNames;
                                            }
                                        } else {
                                            $endPositionDueDateArr[] = $QnameWcomNames;
                                        }
                                    }
                                }
                            } else {
                                if ($ppdv[POSITION_PAPERS::OPEN_CLOSE_STATUS]==0) {
                                    $positionPaperRepliedCount++;
                                }
                            }
                        }
                        $positionPaperPendingCount = ($positionPaperRepliedCount!=0)?($positionPaperIssuedCount-$positionPaperRepliedCount):$positionPaperIssuedCount;
                    }
                    $positionPaperIssued .='
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$positionPaperIssuedCount.'</span></div>
                    ';
                    $positionPaperPending .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$positionPaperPendingCount.'</span></div>
                    ';
                    $positionPaperReplied .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$positionPaperRepliedCount.'</span></div>
                    ';
                    $positionPaperOverdue .= '
                    <div class="col-md-12"><span class="text-dark">'.$v[COMPANIES::COMPANY_NAME].'</span><span class="text-secondary"> - '.$positionPaperOverdueCount.'</span></div>
                    ';
                }
            }
        }
        $end_of_due_date = (count($endOfDueDateComsArr)>0)?implode(', <br>', $endOfDueDateComsArr):EMPTY_VALUE;
        $overdue_companies = (count($qRunningOverdue)>0)?implode(', <br>', $qRunningOverdue):EMPTY_VALUE;
        $end_of_extended_due_date = (count($endExtDueDate)>0)?implode(', <br>', $endExtDueDate):EMPTY_VALUE;
        $endDueNoticeDate = (count($endDueNoticeDateArr)>0)?implode(', <br>', $endDueNoticeDateArr):EMPTY_VALUE;
        $overdueNotices = (count($overdueNoticesArr)>0)?implode(', <br>', $overdueNoticesArr):EMPTY_VALUE;
        $endPositionDueDate = (count($endPositionDueDateArr)>0)?implode(', <br>', $endPositionDueDateArr):EMPTY_VALUE;
        $endPositionExtDueDate = (count($endPositionExtDueDateArr)>0)?implode(', <br>', $endPositionExtDueDateArr):EMPTY_VALUE;
        $overduePositionPapers = (count($overduePositionPapersArr)>0)?implode(', <br>', $overduePositionPapersArr):EMPTY_VALUE;
    }
            $fullHtml.='
        <fieldset class="fldset mt-3">
            <legend>Query Data</legend>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Query Raised</h6>
                            <hr />
                            <div class="row">
                                '.$query_raised.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Query Pending</h6>
                            <hr />
                            <div class="row">
                                '.$query_pending.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Query Replied</h6>
                            <hr />
                            <div class="row">
                                '.$query_replied.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Query Overdue</h6>
                            <hr />
                            <div class="row">
                                '.$query_overdue.'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <fieldset class="fldset">
                        <legend>TODAY&nbsp;['.getFormattedDateTime(getToday(false)).']</legend>
                            <div class="row">
                                <div class="col-md-7 mt-3">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="text-danger font-weight-bold">End of due date for reply</span>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="text-dark">'.$end_of_due_date.'</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-7 mt-3">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="text-danger font-weight-bold"> End of extended due date: </span>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="text-dark pt-2">'.$end_of_extended_due_date.'</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-7 mt-3">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="text-danger font-weight-bold"> Query running overdue:</span>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="text-dark pt-2">'.$overdue_companies.'</span>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                    </fieldset>
                </div>
            </div>
        </fieldset>
            ';
            $fullHtml.='
        <fieldset class="fldset mt-3">
            <legend>Notice Data</legend>
            <div class="row mt-2">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Notice Issued</h6>
                            <hr />
                            <div class="row">
                                '.$notice_issued.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Notice Pending</h6>
                            <hr />
                            <div class="row">
                                '.$notice_pending.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Notice Replied</h6>
                            <hr />
                            <div class="row">
                                '.$notice_replied.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Notice Overdue</h6>
                            <hr />
                            <div class="row">
                                '.$notice_overdue.'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <fieldset class="fldset">
                        <legend>TODAY&nbsp;['.getFormattedDateTime(getToday(false)).']</legend>
                            <div class="row">
                                <div class="col-md-7 mt-3">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="text-danger font-weight-bold">End of due date for reply: </span>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="text-dark">'.$endDueNoticeDate.'</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-7 mt-3">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="text-danger font-weight-bold">Notice running overdue:</span>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="text-dark pt-2">'.$overdueNotices.'</span>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                    </fieldset>
                </div>
            </div>            
        </fieldset>
            ';
            $fullHtml.='
        <fieldset class="fldset mt-3">
            <legend>Position Paper Data</legend>
            <div class="row mt-2">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Position Paper Issued</h6>
                            <hr />
                            <div class="row">
                                '.$positionPaperIssued.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Position Paper Pending</h6>
                            <hr />
                            <div class="row">
                                '.$positionPaperPending.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Position Paper Replied</h6>
                            <hr />
                            <div class="row">
                                '.$positionPaperReplied.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary">Position Paper Overdue</h6>
                            <hr />
                            <div class="row">
                                '.$positionPaperOverdue.'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <fieldset class="fldset">
                        <legend>TODAY&nbsp;['.getFormattedDateTime(getToday(false)).']</legend>
                            <div class="row">
                                <div class="col-md-7 mt-3">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="text-danger font-weight-bold">End of due date for reply: </span>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="text-dark">'.$endPositionDueDate.'</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-7 mt-3">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="text-danger font-weight-bold">Position Paper running overdue:</span>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="text-dark pt-2">'.$endPositionExtDueDate.'</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-7 mt-3">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="text-danger font-weight-bold">Position Paper running overdue:</span>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="text-dark pt-2">'.$overduePositionPapers.'</span>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                    </fieldset>
                </div>
            </div>
            
        </fieldset>
            ';
?>

<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 text-center">
        <h4 class="text-center main_heading">My Workspace</h4>
        <h6 class="text-center sub_heading">Manage all pending tasks of Today [ <span class="text-danger font-weight-bold"><?=getFormattedDateTime(getToday(false),LONG_DATE_FORMAT);?></span> ]</h6>
    </div>
</div>


<div class="card mt-3">
    <div class="card-body">
      <?=getSpinner(true,'auditAllSectionStatLoader');?>
      <div class="row">
        <div class="col-md-12">
          <h6 class="text-left sub_heading font-weight-bold">Audit Overview&nbsp;&nbsp;<i class="fas fa-chart-bar text-primary"></i>&nbsp;<i class="fas fa-chart-line text-success"></i></h6>
        </div>
        <div class="col-md-12 mt-4" id="auditStatCard_data">
            <?=$fullHtml;?>
        </div>
      </div>
    </div>
  </div>

<?php
}
?>