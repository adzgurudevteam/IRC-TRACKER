</div>
    </div>
<!-- Delete modal [start]-->
<div class="modal animated shake" id="delete_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Delete Item</h5>
                <input type="hidden" id="delete_id" style="appearance: hidden; display:none;" disabled />
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
 <!-- Delete modal [end] -->
 <!--Common Modal [start]-->
<div class="modal fade" id="commonModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="commonModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="commonModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?=getSpinner(true, 'commonModalLoader')?>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--Common Modal [end]-->
<?php include BASE_DIR.'includes/admin_url_to_js.php'; ?>
<script type="text/javascript">
    const enterEventListner = (onEle, triggerEle) => {
        onEle.on('keypress', function(e) {
            if (e.which == 13) {
                triggerEle.click();
            }
        });
    }
    // enterEventListner ($('.currency_input'), $('#currency_action_btn_div').find('button'));
    $(document).ready(function () {
        <?php if(($action == 'employee-leaves') || ($action == 'manager-leaves') || ($action == 'admin-apply-leave') || ($action == 'mod-domestic-clients')): ?>
            $('.summernote').summernote({
                tabsize: 5,
                height: <?php if($action == 'mod-domestic-clients'): ?>200<?php else: ?>300<?php endif; ?>
            });
        <?php endif; ?>
        <?php if((isset($_SESSION[ISSSEMSG])) && ($_SESSION[SEMSG])): ?>
            // clog("Session message Active");
            toastAlert("<?=$_SESSION[SEMSG]?>", "<?=$_SESSION[SEMSG_COLOR]?>");
        <?php 
            unset($_SESSION[ISSSEMSG]);
            unset($_SESSION[SEMSG]);
            unset($_SESSION[SEMSG_COLOR]);
            endif;
        ?>
        $('#example').DataTable();
        $('.data-table').DataTable();
        $('.multiple').select2();
        //document ready functions
        $("#sidebar").mCustomScrollbar({
            theme: "minimal"
        });
        <?php if(is_mobile() || is_tablet()): ?>
            $('#dismiss, .overlay').on('click', function () {
                $('#sidebar').removeClass('active');
                $('.overlay').removeClass('active');
            });

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').addClass('active');
                $('.overlay').addClass('active');
                $('.collapse.in').toggleClass('in');
                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            });
        <?php else: ?>
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar, #content').toggleClass('active');
                $('.collapse.in').toggleClass('in');
                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            });
        <?php endif; ?>
        $('.<?php echo TOOLTIP_CLASS; ?>').tooltip();
        $("#fpage_loader").hide();
        $('.fload').on('click', ()=>{
          $("#fpage_loader").show();
        });
        // clog("Console message Active");
    });
    lightbox.option({
      'resizeDuration': 200,
      'wrapAround': true
    });
</script>
</body>

</html>