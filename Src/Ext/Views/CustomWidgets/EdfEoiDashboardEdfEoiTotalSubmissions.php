<?php
$data = $data[0];
?>

<div class="card">
    <div class="card-body">

        <h5 class="text-muted font-weight-normal mt-0 mb-2" title="Number of Orders"><?php echo $widget->name; ?></h5>
 
        <div class="row text-center mt-2">
            <div class="col-md-3">
                <h2 class="font-weight-normal mt-3">
                    <span><?php echo number_format($data->total); ?></span>
                </h2>
                <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-primary"></i> Total expresiones de interes</p>
            </div>
            <div class="col-md-3">
                <h3 class="font-weight-normal mt-3">
                    <span><?php echo number_format($data->pending); ?></span>
                </h3>
                <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-warning"></i> Pendientes</p>
            </div>
            <div class="col-md-3">
                <h3 class="font-weight-normal mt-3">
                    <span><?php echo number_format($data->approved); ?></span>
                </h3>
                <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-success"></i> Aprobados</p>
            </div>
            <div class="col-md-3">
                <h3 class="font-weight-normal mt-3">
                    <span><?php echo number_format($data->rejected); ?></span>
                </h3>
                <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Rechazados</p>
            </div>

        </div>
    </div>
</div>
</div>