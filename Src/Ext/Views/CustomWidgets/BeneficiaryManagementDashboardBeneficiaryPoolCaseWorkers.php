<div id="chart_parent_<?php echo $widget->id; ?>">
    <div class="card">
        <div class="card-body">
            <h5 class="text-muted fs-3 mt-0 mb-2" title="Number of Orders"><?php echo $widget->name; ?></h5>
            <div class="d-flex mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre de gestor">

                <div class="dropdown">
                    <button id="dropdownButton" class="btn btn-primary dropdown-toggle" type="button">
                        Seleccionar Sub-Oficina
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <label><input type="checkbox" value="C-AMOR"> C-AMOR</label>
                        <label><input type="checkbox" value="Huaquillas"> Huaquillas</label>
                        <label><input type="checkbox" value="Tulcán"> Tulcán</label>
                        <label><input type="checkbox" value="Lago Agrio"> Lago Agrio</label>
                        <label><input type="checkbox" value="Guayaquil"> Guayaquil</label>
                        <label><input type="checkbox" value="Manta"> Manta</label>
                    </div>
                </div>

            </div>
            <div class="table-container">
                <table id="gestorTable" class="table table-hover table-centered mb-0">
                    <thead class="bg-primary text-white header-custom">
                        <tr>
                            <th>Nombre Gestor</th>
                            <th>
                                Rechazados
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Aprobados
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Verificado
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Asignado
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Pendiente
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Total Casos
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Total Evaluaciones
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Servicios Cerrados
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Servicios Aprobados
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Servicios Rechazados
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Servicios Pendientes
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>
                            <th>
                                Total Servicios
                                <i class="mdi mdi-sort-ascending sort-icon ascending"></i>
                                <i class="mdi mdi-sort-descending sort-icon descending" style="display: none;"></i>
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $gestor): ?>
                            <tr data-province="<?php echo htmlspecialchars($gestor->provincia_principal ?? ''); ?>">
                                <td><?php echo htmlspecialchars($gestor->gestor); ?></td>
                                <td><?php echo $gestor->rechazados; ?></td>
                                <td><?php echo $gestor->aprobados; ?></td>
                                <td><?php echo $gestor->verificado; ?></td>
                                <td><?php echo $gestor->asignado; ?></td>
                                <td><?php echo $gestor->pendiente; ?></td>
                                <td><?php echo $gestor->total_casos; ?></td>
                                <td><?php echo $gestor->total_evaluaciones; ?></td>
                                <td><?php echo $gestor->total_servicios_cerrados; ?></td>
                                <td><?php echo $gestor->total_servicios_aprobados; ?></td>
                                <td><?php echo $gestor->total_servicios_rechazados; ?></td>
                                <td><?php echo $gestor->total_servicios_pendientes; ?></td>
                                <td><?php echo $gestor->total_servicios; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // ordenar asc o desc
    const headers = document.querySelectorAll('#gestorTable thead th');
    headers.forEach((header, index) => {
        if (index > 0) { // Excluir "Nombre Gestor" de la ordenación
            header.onclick = () => sortTable(index, header);
        }
    });

    function sortTable(columnIndex, header) {
        const table = document.getElementById('gestorTable');
        const rows = Array.from(table.rows).slice(1);
        const isAscending = table.dataset.sortOrder === 'asc';

        rows.sort((a, b) => {
            const cellA = a.cells[columnIndex].innerText;
            const cellB = b.cells[columnIndex].innerText;

            const valueA = parseFloat(cellA) || cellA;
            const valueB = parseFloat(cellB) || cellB;

            if (valueA < valueB) {
                return isAscending ? -1 : 1;
            }
            if (valueA > valueB) {
                return isAscending ? 1 : -1;
            }
            return 0;
        });

        const tbody = table.querySelector('tbody');
        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));

        table.dataset.sortOrder = isAscending ? 'desc' : 'asc';

        // Actualizar los íconos
        const ascendingIcon = header.querySelector('.ascending');
        const descendingIcon = header.querySelector('.descending');
        if (isAscending) {
            ascendingIcon.style.display = 'none';
            descendingIcon.style.display = 'inline';
        } else {
            ascendingIcon.style.display = 'inline';
            descendingIcon.style.display = 'none';
        }
    }

    //busqueda en la tabla
    const table = document.getElementById('gestorTable');
    const searchInput = document.getElementById('searchInput');

    table.dataset.sortOrder = 'asc';

    searchInput.addEventListener('input', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const gestorName = row.cells[0].innerText.toLowerCase();
            row.style.display = gestorName.includes(filter) ? '' : 'none';
        });
    });


    const dropdownButton = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const checkboxes = dropdownMenu.querySelectorAll('input[type="checkbox"]');
    const tableRows = table.querySelectorAll('tbody tr');

    // Abrir/cerrar el dropdown
    dropdownButton.addEventListener('click', () => {
        dropdownMenu.parentElement.classList.toggle('open');
    });

    // Filtrar al seleccionar checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectedProvinces = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value.toLowerCase());

            tableRows.forEach(row => {
                const rowProvince = row.dataset.province.toLowerCase();
                row.style.display = selectedProvinces.length === 0 || selectedProvinces.includes(rowProvince) ? '' : 'none';
            });
        });
    });

    // Cerrar el dropdown al hacer clic fuera de él
    document.addEventListener('click', (event) => {
        if (!dropdownMenu.parentElement.contains(event.target)) {
            dropdownMenu.parentElement.classList.remove('open');
        }
    });
</script>

<style>
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: white;
        border: 1px solid #ddd;
        padding: 10px;
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .dropdown-menu label {
        display: block;
        margin-bottom: 5px;
    }

    .dropdown-menu input[type="checkbox"] {
        margin-right: 5px;
    }

    .dropdown.open .dropdown-menu {
        display: block;
    }
</style>
</div>