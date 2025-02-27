<?php

use \App\Core\Application;

$appTitle = Application::getInstance()->settings->get('APP_TITLE');
$version = Application::getInstance()->globalVar->get("version", "N/A");

$loadChartLibraries = $data['sett_load_chart_libraries'] ?? false;
$loadRichTextEditor = $data['sett_load_rich_text_editor'] ?? false;
$loadDhtmlx = $data['sett_load_dhtmlx'] ?? false;
$blank = $data['sett_blank'] ?? false;

?>

<?php if (!$blank) : ?>

    </div> <!-- container -->
    </div> <!-- content -->
    </div>

    <!-- Footer Start -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    © Copyright <?= date("Y") . ' ' . $appTitle . ' - '. 'v' . $version; ?>
                </div>
                <div class="col-md-6">
                    <div class="text-md-end footer-links d-none d-md-block">
                        <a href="/"><?= t("Home") ?></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->

    </div>

<?php endif; ?>
<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->

<div aria-live="polite" aria-atomic="true">
    <div style="position: absolute; top: 80px; right: 20px;" id="notification_panel"></div>
</div>
<!-- END wrapper -->
<script src="/assets/app/js/main.js"></script>

<script src="/assets/theme/js/vendor.min.js"></script>
<script src="/assets/theme/js/app.min.js"></script>

<script src="/assets/app/js/v-tooltip.min.js"></script>


<script src="/assets/app/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>

<script src="/assets/app/js/jquery_date_picker_dropdown.js"></script>

<script src="/assets/app/js/Sortable.min.js"></script>
<script src="/assets/app/js/vuedraggable.umd.min.js"></script>



<?php if ($loadDhtmlx) : ?>
    <script src="/assets/app/js/dhtmlx/diagram/diagram.js?v=3.0.2"></script>
    <script src="/assets/app/js/dhtmlx/diagram/menu/menu.js"></script>
<?php endif; ?>

<?php if ($loadChartLibraries) : ?>
    <script src="/assets/app/js/highcharts/highcharts.js"></script>
    <script src="/assets/app/js/highcharts/modules/drilldown.js"></script>
    <script src="/assets/app/js/highcharts/modules/exporting.js"></script>
    <script src="/assets/app/js/highcharts/modules/export-data.js"></script>
    <script src="/assets/app/js/highcharts/modules/accessibility.js"></script>
<?php endif; ?>

<?php if ($loadDhtmlx) : ?>
    <script src="/assets/app/js/dhtmlx/gantt/dhtmlxgantt.js?v=7.0.11"></script>
    <script src="/assets/app/js/dhtmlx/scheduler/dhtmlxscheduler.js?v=5.3.10"></script>
    <script src="/assets/app/js/dhtmlx/diagram/diagram.min.js?v=3.0.2"></script>
<?php endif; ?>

<?php if ($loadRichTextEditor) : ?>
    <script src="/assets/app/js/highlight.min.js"></script>
    <script src="/assets/theme/js/vendor/quill.min.js"></script>
<?php endif; ?>

<?php if (Application::getInstance()->user->isAuthenticated() && 1 == 2) { ?>

    <!-- <script src="http://localhost/notify/socket.io.js"></script> -->
    <script>
        if (2 == 1) {
            var socket = io.connect('http://localhost:3000');

            socket.on('message', function(msg) {


                if (msg.to_user == '<?php echo \App\Core\Application::getInstance()->user->getId() ?>') {

                    document.getElementById("has_unread_notification").removeAttribute("hidden");
                    let x = document.getElementById("notification_array").innerHTML;
                    // document.getElementById("notification_array").innerHTML = "<a href=\"javascript:void(0);\" class=\"dropdown-item notify-item\"> <div class=\"notify-icon\"> <img src=\"" + msg.profile_picture + "\" class=\"img-fluid rounded-circle\" alt=\"\"> </div> <p class=\"notify-details\">" + msg.message + "<small class=\"text-muted\">1 min ago</small> </p> </a> " + x;
                    // $.toast({
                    //            heading: msg.data.user_full_name,
                    //            text: msg.data.message,
                    //            showHideTransition: 'slide',
                    //            position: 'top-right',
                    //            icon: 'info'
                    //        });        

                    $('.toast').toast("hide");

                    $('#notification_panel').html(`
                        <div role="alert" aria-live="assertive" aria-atomic="true" class="toast" data-autohide="true" data-delay="2000">
                            <div class="toast-header">
                            <img width="20" height="20" class="mr-2" class="pull-left" height="24" width="24" src="` + msg.data.profile_picture + `"
                            
                            <strong class="mr-auto">` + msg.data.user_full_name + ': ' + msg.data.title + `</strong>
                            <small>Just now</small>
                            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                
                            </button>
                            </div>
                            <div class="toast-body">
                            ` + msg.data.message + `
                            </div>
                        </div>
                        `);

                    $('.toast').toast("show");

                }

            });
        }
    </script>

<?php } ?>

</body>

</html>