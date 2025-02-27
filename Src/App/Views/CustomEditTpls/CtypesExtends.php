<?php use \App\Core\Application;

?>



<script>
    
    $(document).on('dragover', '.dragover-to-enter[data-bs-toggle="tab"]', function () {
        $(this).tab('show');
    });

    var mix = {
        methods: {
        }
    }

</script>

<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FieldsListComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/DependenciesComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FieldsDependenciesComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FieldsRequiredConditionComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FieldsReadOnlyConditionComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FieldsValidationConditionComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/HeaderActionsComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FooterActionsComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FieldsDataSourceHelpersComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/NoOfRecordsComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/orphanColumnsComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FileCleanupComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/FieldsValidationComponent", []) ?>
