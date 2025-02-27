<?php

use App\Core\Controller;
use App\Core\Application;

$data['sett_blank'] = true;
$lang = Application::getInstance()->user->getLangId();
$langDir = Application::getInstance()->user->getLangDirection();
$rentInfo = Application::getInstance()->coreModel->nodeModel("rent_info")->load();

?>

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> -->
    <style>
        .card {
            transition: transform 0.2s;
            min-height: 300px;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-title {
            background-color: #053fb3;
            color: white;
            padding: 15px;
            border-radius: 0.25rem 0.25rem 0 0;
            width: 100%;
            height: 100px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        h5 {
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .alert {
            margin-bottom: 20px;
        }

        .header {
            background-color: #053fb3;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 36px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        ASISTENCIA HUMANITARIA DE OIM
        <center>
            <table>
                <tr>
                    <td>
                        <img width="500px" height="200px" src="\assets\ext\images\logos\Logo_OIM_Blanco_HD.png">
                    </td>

                </tr>
            </table>
        </center>
    </div>

    <div class="container mt-4">
        <br>

        <div class="mb-4">
            <form method="GET" action="">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="property_type">Tipo de vivienda</label>
                        <select id="property_type" name="property_type" class="form-control">
                            <option value="">Selecciona un tipo de vivienda</option>
                            <?php
                            $propertyTypes = [];
                            foreach ($rentInfo as $vivienda) {
                                if (!empty($vivienda->user_form_property)) {
                                    foreach ($vivienda->user_form_property as $casa) {
                                        $propertyTypes[] = $casa->property_type_display;
                                    }
                                }
                            }
                            $propertyTypes = array_unique($propertyTypes);
                            $selectedType = $_GET['property_type'] ?? '';
                            foreach ($propertyTypes as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($type === $selectedType) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="province_display">Provincia</label>
                        <select id="province_display" name="province_display" class="form-control">
                            <option value="">Selecciona una provincia</option>
                            <?php
                            $provinces = [];
                            foreach ($rentInfo as $vivienda) {
                                if (!empty($vivienda->user_form_property)) {
                                    foreach ($vivienda->user_form_property as $casa) {
                                        $provinces[] = $casa->province_display;
                                    }
                                }
                            }
                            $provinces = array_unique($provinces);
                            $selectedProvince = $_GET['province_display'] ?? '';
                            foreach ($provinces as $province): ?>
                                <option value="<?php echo htmlspecialchars($province); ?>" <?php echo ($province === $selectedProvince) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($province); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="city_display">Ciudad</label>
                        <select id="city_display" name="city_display" class="form-control">
                            <option value="">Selecciona una ciudad</option>
                            <?php
                            $cities = [];
                            foreach ($rentInfo as $vivienda) {
                                if (!empty($vivienda->user_form_property)) {
                                    foreach ($vivienda->user_form_property as $casa) {
                                        $cities[] = $casa->city_display;
                                    }
                                }
                            }
                            $cities = array_unique($cities);
                            $selectedCity = $_GET['city_display'] ?? '';
                            foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>" <?php echo ($city === $selectedCity) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="availability_rent_display">Disponibilidad</label>
                        <select id="availability_rent_display" name="availability_rent_display" class="form-control">
                            <option value="">Selecciona la disponibilidad</option>
                            <?php
                            $status = [];
                            foreach ($rentInfo as $vivienda) {
                                if (!empty($vivienda->user_form_property)) {
                                    foreach ($vivienda->user_form_property as $casa) {
                                        $status[] = $casa->availability_rent_display;
                                    }
                                }
                            }
                            $status = array_unique($status);
                            $selectedStatus = $_GET['availability_rent_display'] ?? '';
                            foreach ($status as $status): ?>
                                <option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($status === $selectedStatus) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($status); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="?<?php echo http_build_query(array_diff_key($_GET, ['property_type' => '', 'province_display' => '', 'city_display' => ''])); ?>" class="btn btn-danger">Eliminar filtros</a>
            </form>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <?php foreach ($rentInfo as $vivienda): ?>
                        <?php if (!empty($vivienda->user_form_property)): ?>
                            <?php foreach ($vivienda->user_form_property as $casa): ?>
                                <?php
                                $match = true;

                                if ($selectedType && $casa->property_type_display !== $selectedType) {
                                    $match = false;
                                }
                                if ($selectedProvince && $casa->province_display !== $selectedProvince) {
                                    $match = false;
                                }
                                if ($selectedCity && $casa->city_display !== $selectedCity) {
                                    $match = false;
                                }
                                if ($selectedStatus && $casa->availability_rent_display !== $selectedStatus) {
                                    $match = false;
                                }

                                if (!$match) {
                                    continue;
                                }
                                ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card shadow-sm">
                                        <div class="card-title">
                                            <h4><strong>Tipo de vivienda: </strong><?php echo htmlspecialchars($casa->property_type_display); ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <h5>Vivienda</h5>
                                                <li class="list-group-item"><strong>La vivienda está disponible?: </strong> <?php echo htmlspecialchars($casa->availability_rent_display); ?></li>
                                                <li class="list-group-item"><strong>Precio de renta: </strong> $<?php echo htmlspecialchars($casa->rental_price); ?></li>
                                            </ul>

                                            <ul class="list-group list-group-flush">
                                                <h5>Dormitorios</h5>
                                                <li class="list-group-item"><strong>¿Cuantas personas entran por habitación?: </strong> <?php echo htmlspecialchars($casa->room_occupancy_display); ?></li>
                                                <li class="list-group-item"><strong>¿El baño esta dentro de la vivienda? (privado): </strong> <?php echo htmlspecialchars($casa->private_bathroom_display); ?></li>
                                            </ul>

                                            <ul class="list-group list-group-flush">
                                                <h5>Ubicación</h5>
                                                <li class="list-group-item"><strong>Provincia: </strong> <?php echo htmlspecialchars($casa->province_display); ?></li>
                                                <li class="list-group-item"><strong>Ciudad: </strong> <?php echo htmlspecialchars($casa->city_display); ?></li>
                                                <li class="list-group-item"><strong>Barrio: </strong> <?php echo htmlspecialchars($casa->neighborhood); ?></li>
                                                <li class="list-group-item"><strong>Sector: </strong> <?php echo htmlspecialchars($casa->sector); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>
</body>