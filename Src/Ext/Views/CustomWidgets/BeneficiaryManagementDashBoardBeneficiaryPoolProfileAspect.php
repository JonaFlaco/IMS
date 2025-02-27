<?php
$data = $data[0]; // Asegúrate de que $data[0] contiene el resultado del query
?>

<div class="card">
    <div class="card-body">
        <div class="grey-bg container-fluid">
            <section id="minimal-statistics">
                <div class="row">
                    <div class="col-12 mt-3 mb-1">
                        <h4 class="text-uppercase"><?php echo $widget->name; ?></h4>
                        <p>Características promedio de un solicitante de asistencia.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-pencil primary font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <span>Género más común del beneficiario</span>
                                            <h3><span><?php echo htmlspecialchars($data->most_common_gender); ?></span></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-speech warning font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <span>Nacionalidad más común del beneficiario</span>
                                            <h3><span><?php echo htmlspecialchars($data->most_common_nationality_name); ?></span></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-graph success font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <span>Edad promedio del beneficiario</span>
                                            <h3><span><?php echo htmlspecialchars($data->average_age); ?></span></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-center">
                                            <i class="icon-pointer danger font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <span>Servicio más común solicitado por los beneficiarios</span>
                                            <h3><span><?php echo htmlspecialchars($data->most_common_service); ?></span></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>