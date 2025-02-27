<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-dark">
                <h4 id="dark-header-modalLabel" class="modal-title">Revisar lista de duplicados</h4>
                <button type="button" id="sbtn_close" data-bs-dismiss="modal" aria-hidden="true" class="btn-close"></button>
            </div>
            <div class="modal-body">
                <!---->
                </br>
                <div id="tbl_dublication">


                </div>
            </div>
            <div><!----></div>
            <div class="modal-footer">
                <button type="button" id="btn_close" data-bs-dismiss="modal" class="btn btn-light">
                    Close</button> <button type="button" id="check-btn" class="btn btn-dark"><!---->
                    Empezar revision
                    <span id="btn_spiner" class="badge bg-info"></span>
                    <span id="span_counter" class="badge bg-info">0 / 5</span></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_profile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-dark">
                <h4 id="dark-header-modalLabel" class="modal-title">Download Beneficiaries Profile</h4>
                <button type="button" id="sbtn_close_profile" data-bs-dismiss="modal" class="btn-close btn-close-white"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold">Por favor seleccione un método de descarga</p>
                <p>Se generará un zip con un archivo por registro o un solo archivo por todos los registros seleccionados</p>
            </div>
            <div><!----></div>
            <div class="modal-footer">
                <button type="button" id="multiple-files" class="btn btn-outline-dark"><!---->
                    <i class="mdi mdi-file-multiple"></i> Archivos separados
                    <button type="button" id="one-file" class="btn btn-dark"><!---->
                        <i class="mdi mdi-file-document"></i> Un archivo


            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_cw_assignment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-dark">
                <h5 class="modal-title" id="exampleModalLabel">Asignar Case Worker</h5>
                <button type="button" id="sbtn_close_profile" data-bs-dismiss="modal" class="btn-close btn-close-white"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary" role="alert">
                    <p>Esta acción enviará un correo automático de notificación al Case worker seleccionado!</p>
                    <p>Los casos por asignar son:</p>
                <table class="table">
                    <tbody id="code_table_body"></tbody>
                </table>
                </div>
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Seleccionar case worker:</label>
                        <select class="form-control" id="case_worker_select" name="case_worker_select">
                            <!-- Aquí se llenará dinámicamente con los datos del controlador -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Agregar mensaje adicional:</label>
                        <textarea class="form-control" id="message"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_close" data-bs-dismiss="modal" class="btn btn-light">
                    Cerrar</button>
                <button type="button" id="btn_assignament" class="btn btn-primary">Asignar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cw_already_assigned" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-dark">
                <h5 class="modal-title" id="exampleModalLabel">Reasignar Case Worker</h5>
                <button type="button" id="sbtn_close_profile" data-bs-dismiss="modal" class="btn-close btn-close-white"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <p>Recuerda que puedes ver el registro historico de los case workers asignados a cada caso en la seccion "show log"</p>
                    <p>Los siguientes casos ya estan asignados </p>
                    <p>Seguro que quieres reasignar estos casos a: <strong class="rounded p-1 bg-primary text-white fw-bold" id="new_cw" name="new_cw"></strong> </p>
                    <table class="table">
                        <thead class="thead-dark text-white bg-dark">
                            <tr>
                                <th scope="col">Codigo de caso</th>
                                <th scope="col">Case worker previo</th>
                            </tr>
                        </thead>
                        <tbody id="case_worker_table_body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_close" data-bs-dismiss="modal" class="btn btn-light">
                    Cancelar</button>
                <button type="button" id="btn_re_assignament" class="btn btn-primary">Acepto y continúo</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_summary" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-dark">
                <h4 id="dark-header-modalLabel" class="modal-title">Descargar export de monitoreo y evaluación</h4>
                <button type="button" id="sbtn_close_cv" data-bs-dismiss="modal" class="btn-close btn-close-white"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold">Agregar filtros</p>
                <p>Se generará un archivo excel con los filtros ingresados</p>
            </div>
    
            <div><!----></div>
            <div class="modal-footer">
                <button type="button" id="generate-summary" class="btn btn-outline-dark"><!---->
                    <i class="mdi mdi-file-multiple"></i> Generar
                    
            </div>
        </div>
    </div>
</div>