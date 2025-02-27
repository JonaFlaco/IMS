<?php 
    $data = $data[0]; 
?>

<div class="row">
    <div class="col-lg-3">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class=" mdi mdi-account-key widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Number of Orders">Active Users - Last 24 Hours</h5>
                <h3 class="mt-3 mb-3"><?php echo number_format($data->value_1_1); ?></h3>
                <p class="mb-0 text-muted">
                    <span class="text-<?php echo ($data->value_1_sign == "-" ? "danger" : "success"); ?> me-2"><i class="mdi mdi-arrow-<?php echo ($data->value_1_diff < 0 ? "down" : "up"); ?>-bold"></i> <?php echo $data->value_1_sign . number_format($data->value_1_diff) . " (" . $data->value_1_sign . number_format($data->value_1_diff_perc,2); ?>%)</span>
                    <span class="text-nowrap">Since the day before</span>
                </p>
            </div> <!-- end card-body-->
        </div>
    </div>
    <div class="col-lg-3">
    <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-account-key widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Number of Orders">Active Users - Last 7 Days</h5>
                <h3 class="mt-3 mb-3"><?php echo number_format($data->value_7_1); ?></h3>
                <p class="mb-0 text-muted">
                <span class="text-<?php echo ($data->value_7_sign == "-" ? "danger" : "success"); ?> me-2"><i class="mdi mdi-arrow-<?php echo ($data->value_1_diff < 0 ? "down" : "up"); ?>-bold"></i> <?php echo $data->value_7_sign . number_format($data->value_7_diff) . " (" . $data->value_7_sign . number_format($data->value_7_diff_perc,2); ?>%)</span>
                    <span class="text-nowrap">Since the week before</span>
                </p>
            </div> <!-- end card-body-->
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-account-key widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Number of Orders">Active Users - Last 1 Month</h5>
                <h3 class="mt-3 mb-3"><?php echo number_format($data->value_30_1); ?></h3>
                <p class="mb-0 text-muted">
                <span class="text-<?php echo ($data->value_30_sign == "-" ? "danger" : "success"); ?> me-2"><i class="mdi mdi-arrow-<?php echo ($data->value_1_diff < 0 ? "down" : "up"); ?>-bold"></i> <?php echo $data->value_30_sign . number_format($data->value_30_diff) . " (" . $data->value_30_sign . number_format($data->value_30_diff_perc,2); ?>%)</span>
                    <span class="text-nowrap">Since the month before</span>
                </p>
            </div> <!-- end card-body-->
        </div>
    </div>
    <div class="col-lg-3">
    <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-account-key widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Number of Orders">Active Users - Last 3 Month</h5>
                <h3 class="mt-3 mb-3"><?php echo number_format($data->value_90_1); ?></h3>
                <p class="mb-0 text-muted">
                <span class="text-<?php echo ($data->value_90_sign == "-" ? "danger" : "success"); ?> me-2"><i class="mdi mdi-arrow-<?php echo ($data->value_90_diff < 0 ? "down" : "up"); ?>-bold"></i> <?php echo $data->value_90_sign . number_format($data->value_90_diff) . " (" . $data->value_90_sign . number_format($data->value_90_diff_perc,2); ?>%)</span>
                    <span class="text-nowrap">Since 3 month before</span>
                </p>
            </div> <!-- end card-body-->
        </div>
    </div>
