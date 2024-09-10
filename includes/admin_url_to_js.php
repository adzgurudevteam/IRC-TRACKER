<?php 
global $action;
?>
<script>
 const PAGE_ACTION          = '<?=$action; ?>',
  DEF_AJAX_URL              = '<?=AJAX_URL; ?>',
  AJAX_REQUEST              = '<?=AJAX_REQUEST; ?>',
  DEBUG_APP                 = <?=DEBUG_APP ? 'true' : 'false'; ?>,
  IS_CONTENT_TYPE_JSON      = <?=IS_CONTENT_TYPE_JSON ? 'true' : 'false'; ?>,
  HOST_URL                  = '<?=HOST_URL; ?>',
  IS_MOBILE                 = <?php if(is_mobile()): ?> true <?php else: ?> false <?php endif; ?>,
  IS_TABLET                 = <?php if(is_tablet()): ?> true <?php else: ?> false <?php endif; ?>,
  IS_IOS_TABLET             = <?php if(is_ios_tablet()): ?> true <?php else: ?> false <?php endif; ?>,
  DEFAULT_GST_PERCENTAGE    = parseInt(<?=DEFAULT_GST_PERCENTAGE;?>),
  DEFAULT_AMOUNT            = parseInt(<?=DEFAULT_AMOUNT;?>),
  EMPTY_VALUE               = '<?=EMPTY_VALUE;?>',
  RESIGNED                  = '<?=RESIGNED;?>',
  ABSCONDED                 = '<?=ABSCONDED;?>',
  SERVING_NOTICE            = '<?=SERVING_NOTICE;?>',
  OTHER_REASON              = '<?=OTHER_REASON;?>',
  EMPLOYEE_INACTIVE_REASONS = <?=json_encode(EMPLOYEE_INACTIVE_REASONS);?>,
  USERS                     = <?=json_encode(USERS);?>,
  SADMIN                    = <?=SADMIN?>,
  MANAGER                   = <?=MANAGER?>,
  ADMIN                     = <?=ADMIN?>,
  EMPLOYEE                  = <?=EMPLOYEE?>,
  IT_ADMIN                  = <?=IT_ADMIN?>,
  USER_ROW_ID               = <?=(isset($_SESSION[RID])) ? $_SESSION[RID] : 0;?>,
  CURRENT_USER_TYPE         = <?=(isset($_SESSION[USER_TYPE])) ? $_SESSION[USER_TYPE] : 0;?>,
  ISUSERLOGGEDIN            = <?php if(isUserLoggedIn()): ?>1<?php else: ?>0<?php endif; ?>,
  CUSTOM_SPINNER_ID         = "<?=CUSTOM_SPINNER_ID?>",
  TIME_ZONE    		        = "<?=TIME_ZONE?>",
  TOOLTIP_CLASS             = "<?=TOOLTIP_CLASS?>";

  var CUR_DATE              = '<?=getToday(false); ?>',
   CUR_DATE_TIME            = '<?=getToday(true); ?>',
   CURRENT_CHAT_USER_ID     = 0;
</script>


<!-- jQuery CDN -->
<script src="<?=CDN_URL; ?>js/jquery-3.6.0.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<!-- jQuery Custom Scroller -->
<script src="<?=CDN_URL; ?>js/jquery.mCustomScrollbar.concat.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<!-- Popper.JS -->
<script src="<?=CDN_URL; ?>js/popper.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<!-- Bootstrap JS -->
<script src="<?=CDN_URL; ?>js/bootstrap.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<!-- Font Awesome JS -->
<script src="<?=CDN_URL; ?>fa/js/all.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<!-- toastr js -->
<script src="<?=CDN_URL; ?>js/toastr.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<script src="<?=CDN_URL; ?>js/sweetalert.min.js?v=<?=ASSETS_VERSION;?>"></script>
<!-- common custom js -->
<script src="<?=CDN_URL ?>js/common.js?v=<?=ASSETS_VERSION; ?>"></script>
<script src="<?=CDN_URL ?>js/jquery_value_validation.js?v=<?=ASSETS_VERSION; ?>"></script>
<script src="<?=CDN_URL ?>js/dataTables.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<?php if(($action == 'employee-leaves') || ($action == 'manager-leaves') || ($action == 'admin-apply-leave') || ($action == 'mod-domestic-clients')): ?>
    <script src="<?=CDN_URL ?>js/summernote.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<?php endif; ?>
<script src="<?=CDN_URL;?>lb/js/lightbox.min.js"></script>
<!-- lightgallery plugins -->
<!-- <script src="<?=CDN_URL;?>js/plugins/lg-thumbnail.umd.min.js"></script>
<script src="<?=CDN_URL;?>js/plugins/lg-zoom.umd.min.js"></script> -->
<script src="<?=CDN_URL; ?>js/select2.min.js?v=<?=ASSETS_VERSION; ?>"></script>
<!-- main custom js -->
<script src="<?=CDN_URL; ?>js/main.js?v=<?=ASSETS_VERSION; ?>"></script>
<script src="<?=CDN_URL; ?>js/crud.js?v=<?=ASSETS_VERSION; ?>"></script>