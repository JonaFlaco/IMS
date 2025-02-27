<?php 
    $data = $data[0]; 
?>

    <div class="card">
        <div class="card-body">

            <h5 class="text-muted font-weight-normal mt-0 mb-2" title="Number of Orders"><?php echo $widget->name;?></h5>

            <div class="row text-center mt-2">
                <div class="col-md-3">
                    <i class="mdi mdi-account-outline widget-icon rounded-circle bg-light-lighten text-muted"></i>
                    <h3 class="font-weight-normal mt-3">
                        <span><?php echo number_format($data->count_users); ?></span>
                    </h3>
                    <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-primary"></i> Total Users</p>
                </div>
                <div class="col-md-3">
                    <i class="mdi mdi-account-star widget-icon rounded-circle bg-light-lighten text-muted"></i>
                    <h3 class="font-weight-normal mt-3">
                        <span><?php echo number_format($data->heartbeat_1m); ?></span>
                    </h3>
                    <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-success"></i> Active Users</p>
                </div>
                <div class="col-md-3">
                    <i class="mdi mdi-account-off-outline widget-icon rounded-circle bg-light-lighten text-muted"></i>
                    <h3 class="font-weight-normal mt-3">
                        <span><?php echo number_format($data->is_not_active); ?></span>
                    </h3>
                    <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Disabled Users</p>
                </div>
                <div class="col-md-3">
                    <i class="mdi mdi-account-minus-outline widget-icon rounded-circle bg-light-lighten text-muted"></i>
                    <h3 class="font-weight-normal mt-3">
                        <span><?php echo number_format($data->no_activity); ?></span>
                    </h3>
                    <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-warning"></i> No Activity</p>
                </div>
            </div>
        </div>
    </div>
</div>