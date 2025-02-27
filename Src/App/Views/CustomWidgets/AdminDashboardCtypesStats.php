<?php 
    $data = $data[0]; 
?>

    <div class="card">
        <div class="card-body">

            <h5 class="text-muted font-weight-normal mt-0 mb-2" title="Number of Orders"><?php echo $widget->name;?></h5>

            <div class="row text-center mt-2">
                <div class="col-md-3">
                    <i class="mdi mdi-table widget-icon rounded-circle bg-light-lighten text-muted"></i>
                    <h3 class="font-weight-normal mt-3">
                        <span><?php echo number_format($data->total); ?></span>
                    </h3>
                    <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-info"></i> Total</p>
                </div>
                <div class="col-md-3">
                    <i class="mdi mdi-table widget-icon rounded-circle bg-light-lighten text-muted"></i>
                    <h3 class="font-weight-normal mt-3">
                        <span><?php echo number_format($data->ctypes_count); ?></span>
                    </h3>
                    <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-info"></i> Content-Types</p>
                </div>
                <div class="col-md-3">
                    <i class="mdi mdi-table widget-icon rounded-circle bg-light-lighten text-muted"></i>
                    <h3 class="font-weight-normal mt-3">
                        <span><?php echo number_format($data->fc_count); ?></span>
                    </h3>
                    <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-info"></i> Field-Collections</p>
                </div>
                <div class="col-md-3">
                    <i class="mdi mdi-table widget-icon rounded-circle bg-light-lighten text-muted"></i>
                    <h3 class="font-weight-normal mt-3">
                        <span><?php echo number_format($data->lookup_table_count); ?></span>
                    </h3>
                    <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-info"></i> Lookup-Tables</p>
                </div>
            </div>
        </div>
    </div>
</div>