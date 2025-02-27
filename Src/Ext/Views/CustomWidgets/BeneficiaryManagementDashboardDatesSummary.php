<?php
$data = $data[0];
?>
<div id="chart_parent_<?php echo $widget->id; ?>">

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-xl-4">
                <div class="customcard bg-c-blue order-customcard">
                    <div class="customcard-block">
                        <h4 class="m-b-20">Solicitudes de este Mes</h4>
                        <h2 class="text-right"><i class="fa fa-cart-plus f-left"></i><span><?php echo number_format($data->mes); ?></span></h2>
                        <hr class="hr hr-blurry" />
                        <p>El mes pasado<span class="f-right"><?php echo number_format($data->mes_pasado); ?></span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-4">
                <div class="customcard bg-c-green order-customcard">
                    <div class="customcard-block">
                        <h4 class="m-b-20">Solicitudes de este Semana</h4>
                        <h2 class="text-right"><i class="fa fa-rocket f-left"></i><span><?php echo number_format($data->semana); ?></span></h2>
                        <hr class="hr hr-blurry" />
                        <p>Semana Pasada<span class="f-right"><?php echo number_format($data->semana_pasada); ?></span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-4">
                <div class="customcard bg-c-yellow order-customcard">
                    <div class="customcard-block">
                        <h4 class="m-b-20">Solicitudes de Hoy</h4>
                        <h2 class="text-right"><i class="fa fa-refresh f-left"></i><span><?php echo number_format($data->hoy); ?></span></h2>
                        <hr class="hr hr-blurry" />
                        <p>Solicitudes de Ayer<span class="f-right"><?php echo number_format($data->ayer); ?></span></p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <div class="weather-card one">
                    <div class="top">
                        <div class="wrapper">
                            <div class="mynav">
                                <a href="javascript:;"><span class="lnr lnr-chevron-left"></span></a>
                                <a href="javascript:;"><span class="lnr lnr-cog"></span></a>
                            </div>
                            <h1 class="heading">Centro Amor</h1>
                            <h3 class="location">Porcentaje de solicitudes este mes</h3>
                            <p class="temp">
                                <span class="temp-value">
                                    <?php
                                    if ($data->mes > 0) {
                                        $percentage = ($data->camor_mes / $data->mes) * 100;
                                        echo number_format($percentage, 2) . '%';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="wrapper">
                            <p class="mt-2">Este mes<span class="f-right"><?php echo number_format($data->camor_mes); ?></span></p>
                            <p class="mt-2">El mes pasado<span class="f-right"><?php echo number_format($data->camor_mes_pasado); ?></span></p>
                            <p class="mt-2">Esta semana<span class="f-right"><?php echo number_format($data->camor_mes_pasado); ?></span></p>
                            <p class="mt-2">La semana pasada<span class="f-right"><?php echo number_format($data->camor_semana_pasada); ?></span></p>
                            <p class="mt-2">Hoy<span class="f-right"><?php echo number_format($data->camor_hoy); ?></span></p>
                            <p class="mt-2">Ayer<span class="f-right"><?php echo number_format($data->camor_ayer); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="weather-card rain">
                    <div class="top">
                        <div class="wrapper">
                            <div class="mynav">
                                <a href="javascript:;"><span class="lnr lnr-chevron-left"></span></a>
                                <a href="javascript:;"><span class="lnr lnr-cog"></span></a>
                            </div>
                            <h1 class="heading">Huaquillas</h1>
                            <h3 class="location">Porcentaje de solicitudes este mes</h3>
                            <p class="temp">
                                <span class="temp-value">
                                    <?php
                                    if ($data->mes > 0) {
                                        $percentage = ($data->huaquillas_mes / $data->mes) * 100;
                                        echo number_format($percentage, 2) . '%';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="wrapper">
                            <p class="mt-2">Este mes<span class="f-right"><?php echo number_format($data->huaquillas_mes); ?></span></p>
                            <p class="mt-2">El mes pasado<span class="f-right"><?php echo number_format($data->huaquillas_mes_pasado); ?></span></p>
                            <p class="mt-2">Esta semana<span class="f-right"><?php echo number_format($data->huaquillas_mes_pasado); ?></span></p>
                            <p class="mt-2">La semana pasada<span class="f-right"><?php echo number_format($data->huaquillas_semana_pasada); ?></span></p>
                            <p class="mt-2">Hoy<span class="f-right"><?php echo number_format($data->huaquillas_hoy); ?></span></p>
                            <p class="mt-2">Ayer<span class="f-right"><?php echo number_format($data->huaquillas_ayer); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="weather-card rain">
                    <div class="top">
                        <div class="wrapper">
                            <div class="mynav">
                                <a href="javascript:;"><span class="lnr lnr-chevron-left"></span></a>
                                <a href="javascript:;"><span class="lnr lnr-cog"></span></a>
                            </div>
                            <h1 class="heading">Tulcán</h1>
                            <h3 class="location">Porcentaje de solicitudes este mes</h3>
                            <p class="temp">
                                <span class="temp-value">
                                    <?php
                                    if ($data->mes > 0) {
                                        $percentage = ($data->tulcan_mes / $data->mes) * 100;
                                        echo number_format($percentage, 2) . '%';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="wrapper">
                            <p class="mt-2">Este mes<span class="f-right"><?php echo number_format($data->tulcan_mes); ?></span></p>
                            <p class="mt-2">El mes pasado<span class="f-right"><?php echo number_format($data->tulcan_mes_pasado); ?></span></p>
                            <p class="mt-2">Esta semana<span class="f-right"><?php echo number_format($data->tulcan_mes_pasado); ?></span></p>
                            <p class="mt-2">La semana pasada<span class="f-right"><?php echo number_format($data->huaquillas_semana_pasada); ?></span></p>
                            <p class="mt-2">Hoy<span class="f-right"><?php echo number_format($data->tulcan_hoy); ?></span></p>
                            <p class="mt-2">Ayer<span class="f-right"><?php echo number_format($data->tulcan_ayer); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="weather-card rain">
                    <div class="top">
                        <div class="wrapper">
                            <div class="mynav">
                                <a href="javascript:;"><span class="lnr lnr-chevron-left"></span></a>
                                <a href="javascript:;"><span class="lnr lnr-cog"></span></a>
                            </div>
                            <h1 class="heading">Lago Agrio</h1>
                            <h3 class="location">Porcentaje de solicitudes este mes</h3>
                            <p class="temp">
                            <span class="temp-value">
                                    <?php
                                    if ($data->mes > 0) {
                                        $percentage = ($data->lago_mes / $data->mes) * 100;
                                        echo number_format($percentage, 2) . '%';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="wrapper">
                            <p class="mt-2">Este mes<span class="f-right"><?php echo number_format($data->lago_mes); ?></span></p>
                            <p class="mt-2">El mes pasado<span class="f-right"><?php echo number_format($data->lago_mes_pasado); ?></span></p>
                            <p class="mt-2">Esta semana<span class="f-right"><?php echo number_format($data->lago_mes_pasado); ?></span></p>
                            <p class="mt-2">La semana pasada<span class="f-right"><?php echo number_format($data->lago_semana_pasada); ?></span></p>
                            <p class="mt-2">Hoy<span class="f-right"><?php echo number_format($data->lago_hoy); ?></span></p>
                            <p class="mt-2">Ayer<span class="f-right"><?php echo number_format($data->lago_ayer); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="weather-card rain">
                    <div class="top">
                        <div class="wrapper">
                            <div class="mynav">
                                <a href="javascript:;"><span class="lnr lnr-chevron-left"></span></a>
                                <a href="javascript:;"><span class="lnr lnr-cog"></span></a>
                            </div>
                            <h1 class="heading">Manta</h1>
                            <h3 class="location">Porcentaje de solicitudes este mes</h3>
                            <p class="temp">
                            <span class="temp-value">
                                    <?php
                                    if ($data->mes > 0) {
                                        $percentage = ($data->manta_mes / $data->mes) * 100;
                                        echo number_format($percentage, 2) . '%';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="wrapper">
                            <p class="mt-2">Este mes<span class="f-right"><?php echo number_format($data->manta_mes); ?></span></p>
                            <p class="mt-2">El mes pasado<span class="f-right"><?php echo number_format($data->manta_mes_pasado); ?></span></p>
                            <p class="mt-2">Esta semana<span class="f-right"><?php echo number_format($data->manta_mes_pasado); ?></span></p>
                            <p class="mt-2">La semana pasada<span class="f-right"><?php echo number_format($data->manta_semana_pasada); ?></span></p>
                            <p class="mt-2">Hoy<span class="f-right"><?php echo number_format($data->manta_hoy); ?></span></p>
                            <p class="mt-2">Ayer<span class="f-right"><?php echo number_format($data->manta_ayer); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="weather-card rain">
                    <div class="top bg-info.bg-gradient">
                        <div class="wrapper">
                            <div class="mynav">
                                <a href="javascript:;"><span class="lnr lnr-chevron-left"></span></a>
                                <a href="javascript:;"><span class="lnr lnr-cog"></span></a>
                            </div>
                            <h1 class="heading">Guayaquil</h1>
                            <h3 class="location">Porcentaje de solicitudes este mes</h3>
                            <p class="temp">
                            <span class="temp-value">
                                    <?php
                                    if ($data->mes > 0) {
                                        $percentage = ($data->guayaquil_mes / $data->mes) * 100;
                                        echo number_format($percentage, 2) . '%';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="wrapper">
                            <p class="mt-2">Este mes<span class="f-right"><?php echo number_format($data->guayaquil_mes); ?></span></p>
                            <p class="mt-2">El mes pasado<span class="f-right"><?php echo number_format($data->guayaquil_mes_pasado); ?></span></p>
                            <p class="mt-2">Esta semana<span class="f-right"><?php echo number_format($data->guayaquil_mes_pasado); ?></span></p>
                            <p class="mt-2">La semana pasada<span class="f-right"><?php echo number_format($data->guayaquil_semana_pasada); ?></span></p>
                            <p class="mt-2">Hoy<span class="f-right"><?php echo number_format($data->guayaquil_hoy); ?></span></p>
                            <p class="mt-2">Ayer<span class="f-right"><?php echo number_format($data->guayaquil_ayer); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<style>
    .order-customcard {
        color: #fff;
    }

    .bg-c-blue {
        background: linear-gradient(45deg, #4099ff, #73b4ff);
    }

    .bg-c-green {
        background: linear-gradient(45deg, #2ed8b6, #59e0c5);
    }

    .bg-c-yellow {
        background: linear-gradient(45deg, #FFB64D, #ffcb80);
    }

    .bg-c-pink {
        background: linear-gradient(45deg, #FF5370, #ff869a);
    }


    .customcard {
        border-radius: 5px;
        -webkit-box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
        box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
        border: none;
        margin-bottom: 20px;
        -webkit-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }

    .customcard .customcard-block {
        padding: 12px;
    }

    .order-customcard i {
        font-size: 26px;
    }

    .f-left {
        float: left;
    }

    .f-right {
        float: right;
    }
    .weather-card {
        margin: 5px auto;
        height: 470px;
        width: 250px;
        background: #fff;
        box-shadow: 0 1px 38px rgba(0, 0, 0, 0.15), 0 5px 12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    .weather-card .top {
        position: relative;
        height: 230px;
        width: 100%;
        overflow: hidden;
        background: url("/assets/ext/images/logos/CAMOR-logo.png") no-repeat;
        background-size: cover;
        background-position: center center;
        text-align: center;
    }

    .weather-card .top .wrapper {
        padding: 10px;
        position: relative;
        z-index: 1;
    }

    .weather-card .top .wrapper .mynav {
        height: 20px;
    }

    .weather-card .top .wrapper .mynav .lnr {
        color: #fff;
        font-size: 20px;
    }

    .weather-card .top .wrapper .mynav .lnr-chevron-left {
        display: inline-block;
        float: left;
    }

    .weather-card .top .wrapper .mynav .lnr-cog {
        display: inline-block;
        float: right;
    }

    .weather-card .top .wrapper .heading {
        margin-top: 1px;
        font-size: 35px;
        font-weight: 400;
        color: #fff;
    }

    .weather-card .top .wrapper .location {
        margin-top: 1px;
        font-size: 21px;
        font-weight: 400;
        color: #fff;
    }

    .weather-card .top .wrapper .temp {
        margin-top: 1px;
    }

    .weather-card .top .wrapper .temp a {
        text-decoration: none;
        color: #fff;
    }

    .weather-card .top .wrapper .temp a .temp-type {
        font-size: 85px;
    }

    .weather-card .top .wrapper .temp .temp-value {
        display: inline-block;
        font-size: 60px;
        font-weight: 600;
        color: #fff;
    }

    .weather-card .top .wrapper .temp .deg {
        display: inline-block;
        font-size: 35px;
        font-weight: 600;
        color: #fff;
        vertical-align: top;
        margin-top: 10px;
    }

    .weather-card .top:after {
        content: "";
        height: 100%;
        width: 100%;
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .weather-card .bottom {
        padding: 0 30px;
        background: #fff;
    }

    .weather-card .bottom .wrapper .forecast {
        overflow: hidden;
        margin: 0;
        font-size: 0;
        padding: 0;
        padding-top: 20px;
        max-height: 155px;
    }

    .weather-card .bottom .wrapper .forecast a {
        text-decoration: none;
        color: #000;
    }

    .weather-card .bottom .wrapper .forecast .go-up {
        text-align: center;
        display: block;
        font-size: 25px;
        margin-bottom: 10px;
    }

    .weather-card .bottom .wrapper .forecast li {
        display: block;
        font-size: 25px;
        font-weight: 400;
        color: rgba(0, 0, 0, 0.25);
        line-height: 1em;
        margin-bottom: 30px;
    }

    .weather-card .bottom .wrapper .forecast li .date {
        display: inline-block;
    }

    .weather-card .bottom .wrapper .forecast li .condition {
        display: inline-block;
        vertical-align: middle;
        float: right;
        font-size: 25px;
    }

    .weather-card .bottom .wrapper .forecast li .condition .temp {
        display: inline-block;
        vertical-align: top;
        font-family: 'Montserrat', sans-serif;
        font-size: 20px;
        font-weight: 400;
        padding-top: 2px;
    }

    .weather-card .bottom .wrapper .forecast li .condition .temp .deg {
        display: inline-block;
        font-size: 10px;
        font-weight: 600;
        margin-left: 3px;
        vertical-align: top;
    }

    .weather-card .bottom .wrapper .forecast li .condition .temp .temp-type {
        font-size: 20px;
    }

    .weather-card .bottom .wrapper .forecast li.active {
        color: rgba(0, 0, 0, 0.8);
    }

    .weather-card.rain .top {
        background: url("/assets/ext/images/logos/oim_logo.png") no-repeat;
        background-size: 250px 110px;
        background-position: center center;
    }
</style>