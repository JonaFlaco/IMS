<?php
$data = $data[0];
?>

<div class="card">
    <div class="card-body">

        <h5 class="text-muted fs-3 mt-0 mb-2" title="Number of Orders"><?php echo $widget->name; ?></h5>
        <div class="container">
            <div class="row text-center mt-2">
                <div class="col-md-2 bg-primary rounded">
                    <div class="col-md-12 mt-5">
                        <h2 class="font-weight-normal mt-3 text-white">
                            <span><?php echo number_format($data->total); ?></span>
                        </h2>
                        <p class="mb-0 mb-2 text-white"><i class="mdi mdi-clipboard-account text-white"></i> Total de solicitudes de asistencia</p>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-3">
                            <h3 class="font-weight-normal mt-4">
                                <span><?php echo number_format($data->pending); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-clock text-secoundary"></i> Pendientes</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="font-weight-normal mt-4">
                                <span><?php echo number_format($data->assigned); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-account-check text-info"></i> Gestor asignado</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="font-weight-normal mt-4">
                                <span><?php echo number_format($data->verified); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-blank-circle text-warning"></i> Verificado</p>
                        </div>

                    </div>

                    <div class="row d-flex justify-content-center">
                        <div class="col-md-3">
                            <h3 class="font-weight-normal mt-3">
                                <span><?php echo number_format($data->approved); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-checkbox-marked-circle text-success"></i> Aprobado</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="font-weight-normal mt-3">
                                <span><?php echo number_format($data->rejected); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-close-circle text-danger"></i> Rechazado</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="font-weight-normal mt-3">
                                <span><?php echo number_format($data->cerrado); ?></span>
                            </h3>
                            <p class="text-muted mb-0 mb-2"><i class="mdi mdi-clipboard-check text-danger"></i> Asistencias cerradas</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
</div>