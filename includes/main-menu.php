<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container-fluid">

    <button type="button" id="sidebarCollapse" class="btn btn-warning">
        <i class="fas fa-align-left"></i>
        <span></span>
    </button>
    <?php if((is_mobile()) || (is_tablet())): ?>
        <span style="font-weight: bold; font-size: small;">&nbsp; <?php echo 'WELCOME ' . strtoupper(strtolower($_SESSION[USERNAME])); ?></span>
    <?php endif; ?>
    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-align-justify"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <?php if((is_mobile()) || (is_tablet())): ?><?php else: ?>    
        <span style="font-weight: bold;">&nbsp; <?php echo 'WELCOME ' . strtoupper(strtolower($_SESSION[USERNAME])); ?></span>
    <?php endif; ?>
        <ul class="nav navbar-nav ml-auto">

            <?php
            switch ($_SESSION[USER_TYPE]) {
                case EMPLOYEE:
            ?>
                    <li class="nav-item <?php if (in_array($action,$auditor_dashboard)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'audit-dashboard/'; ?>">Dashboard</a>
                    </li>
                    <li class="nav-item <?php if(in_array($action,$auditor_account)): ?>active<?php endif; ?>">
                        <a class="fload nav-link" href="<?= HOST_URL . 'auditor-profile/'; ?>">User Profile</a>
                    </li>
                    <li class="nav-item <?php if(in_array($action,$auditor_audits)): ?>active<?php endif; ?>">
                        <a class="fload nav-link" href="<?= HOST_URL . 'auditor-audits'; ?>">Audits</a>
                    </li>
                    <li class="nav-item <?php if(in_array($action,$auditor_workspace)): ?>active<?php endif; ?>">
                        <a class="fload nav-link" href="<?= HOST_URL . 'auditor-workspace'; ?>">Workspace</a>
                    </li>
                <?php
                    break;
                case ADMIN:
                ?>
                    <li class="nav-item <?php if (in_array($action,$admin_dashboard)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'audit-dashboard/'; ?>">Dashboard</a>
                    </li>
                    <li class="nav-item <?php if (in_array($action,$admin_account)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'profile/'; ?>">User Profiles</a>
                    </li>
                    <li class="nav-item <?php if (in_array($action, $admin_dept)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'admin-audits/'; ?>">Audits</a>
                    </li>
                    <li class="nav-item <?php if (in_array($action, $admin_workspace)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'admin-workspace/'; ?>">Workspace</a>
                    </li>
                    <li class="nav-item <?php if (in_array($action, $admin_reports)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'admin-daily-report/'; ?>">Reports</a>
                    </li>
                <?php
                    break;
                case SADMIN:
                ?>
                    <li class="nav-item <?php if (in_array($action,$sadmin_dashboard)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'audit-dashboard/'; ?>">Dashboard</a>
                    </li>
                    <li class="nav-item <?php if (in_array($action,$sadmin_account)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'sadmin-profile/'; ?>">User Profiles</a>
                    </li>
                    <li class="nav-item <?php if (in_array($action, $sadmin_dept)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'sadmin-audits/'; ?>">Audits</a>
                    </li>
                    <li class="nav-item <?php if (in_array($action, $sadmin_workspace)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'sadmin-workspace/'; ?>">Workspace</a>
                    </li>
                    <li class="nav-item <?php if (in_array($action, $sadmin_reports)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'sadmin-daily-report/'; ?>">Reports</a>
                    </li>
                <?php
                    break;
                case IT_ADMIN:
                    ?>
                    <li class="nav-item <?php if (in_array($action,$itAdminUserProfiles)) : ?>active<?php endif; ?>">
                        <a class="nav-link" href="<?= HOST_URL . 'it-profile/'; ?>">User Profiles</a>
                    </li>
                <?php
                    break;
            }
            ?>
            <li class="nav-item">
                <a class="fload nav-link text-danger" href="<?=HOST_URL . 'logout/'; ?>">Log Out</a>
            </li>
        </ul>
    </div>
</div>
</nav>