<?php
$data = $data[0];
?>

<div class="card">
    <div class="card-body">

        <h5 class="text-muted fs-3 mt-0 mb-2" title="Number of Orders"><?php echo $widget->name; ?></h5>
        <div class="container">
            <div class="row text-center mt-2">
                <div class="col-md-3 rounded d-flex flex-column">
                    <div class="bg-primary rounded-top">
                        <div class="col-md-12">
                            <h2 class="font-weight-normal mt-3 text-white">
                                <span><?php echo number_format($data->total); ?></span>
                            </h2>
                            <p class="mb-0 mb-2 text-white"><i class="mdi mdi-clipboard-account text-white"></i>Asistencias totales registradas</p>
                        </div>
                    </div>
                    <div class="bg-success rounded-bottom">
                        <div class="col-md-12">
                            <h3 class="font-weight-normal mt-3 text-white">
                                <span><?php echo number_format($data->fully_closed); ?></span>
                            </h3>
                            <p class="mb-0 mb-2 text-white"><i class="mdi mdi-clipboard-check text-white"></i>Asistencias Cerradas</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-9 mt-4">
                    <div class="row">
                        <div class="col-md-4">
                            <h3 class="font-weight-normal mt-4">
                                <span><?php echo number_format($data->pending); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-clock text-secoundary"></i>Pendientes</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="font-weight-normal mt-4">
                                <span><?php echo number_format($data->approved); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-multiple-marked-circle text-success"></i>Aprobadas</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="font-weight-normal mt-4">
                                <span><?php echo number_format($data->rejected); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-close-circle text-danger"></i>Rechazadas</p>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
</div>