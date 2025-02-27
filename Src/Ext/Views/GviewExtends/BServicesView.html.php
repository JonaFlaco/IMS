<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-dark">
                <h4 id="dark-header-modalLabel" class="modal-title">Download Beneficiaries Profile</h4>
                <button type="button" id="sbtn_close" data-bs-dismiss="modal" aria-hidden="true" class="btn-close"></button>
            </div>
            <div class="modal-body">                
                <p>Por favor seleccione un método de descarga</p>
            </div>
            <div><!----></div>
            <div class="modal-footer">
                <button type="button" id="btn_close" data-bs-dismiss="modal" class="btn btn-light">
                    Close</button> <button type="button" id="multiple-files" class="btn btn-dark"><!---->
                    Archivos separados
                    <button type="button" id="one-file" class="btn btn-dark"><!---->
                    Un archivo

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_filters" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
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
                <button type="button" id="generate-file" class="btn btn-outline-dark"><!---->
                    <i class="mdi mdi-file-multiple"></i> Generar
                    
            </div>
        </div>
    </div>
</div>