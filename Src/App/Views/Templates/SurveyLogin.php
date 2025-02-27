<?php 

use \App\Core\Application; 

$data['sett_blank'] = true;
$nodeData = $data['nodeData'];

$isSystemAuthenticated = Application::getInstance()->user->isAuthenticated();

$languagesWhere = "";
foreach($nodeData->languages as $itm) {
    if(!empty($languagesWhere))
        $languagesWhere .= ",";
    $languagesWhere .= "'$itm->value'";
}


$languages = Application::getInstance()->coreModel->nodeModel("languages")
    ->where("m.id in ($languagesWhere)")
    ->fields(["id", "title", "name", "is_default"])
    ->OrderBy("m.sort")
    ->load();

$current_url = Application::getInstance()->request->getRequestUrl();
$ur = parse_url($current_url);
$u = [];
if(isset($ur['query'])){
    parse_str($ur['query'], $u);
    if(isset($u['lang'])) {
        unset($u['lang']);
    }
}
$new_url = $ur['path'] . "?" . http_build_query($u);
    
$flashContent = Application::getInstance()->session->flash();
?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div>
        <?php if($isSystemAuthenticated != true && sizeof($nodeData->languages) > 1): ?>
        <div class="container">

            <header class="d-flex py-1 flex-wrap justify-content-center border-bottom">
                <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                    <?php $i = 0; 
                    foreach($languages as $item) : 
                        if($item->name != Application::getInstance()->user->getLangId(true)): ?>
                            <?php if ($i++ > 0): ?> • &nbsp;  <?php endif; ?>
                            <a href="<?= $new_url ?>&lang=<?= $item->id ?>"><?= $item->name ?></a> &nbsp;
                        <?php endif;
                    endforeach; ?>
                </div>

                <ul class="nav nav-pills">
                    
                </ul>
            </header>
        </div>
        <?php endif; ?>
        
        <div class="mt-5">
            <div class="row justify-content-center">

                <div class="card col-md-6 d-flex justify-content-center">
                    <div class='card-body p-4'>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title"><i class="dripicons-lock"></i> <?= $data['title'] ?></h5>
                            </div>
                            <div class="col-md-6 float-end text-end">
                                
                            </div>
                        </div>
                        
                        <p><?= t("Necesitas un usuario y contraseña para poder aplicar a este formulario") ?>

                        <?= $flashContent ?>
                        
                        <div v-if="error" class="alert alert-danger">
                            <i class="dripicons-warning me-2"></i>
                            <span id="alert-error-message">{{ error }}</span>
                        </div>

                        <form action="/SurveyManagement/login/<?=$nodeData->id?>/?lang=<?= Application::getInstance()->user->getLangId() ?>" id="login_form" @submit="loading = true" method="post" autocomplete="off">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label"><?= t("Usuario") ?></label>
                                <input 
                                    class="form-control" 
                                    type="text" 
                                    tabindex="10"
                                    id="username" 
                                    name="username" 
                                    required
                                    value=""
                                    placeholder="<?= t("Ingresa tu usuario") ?>"
                                    >
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label"><?= t("Contraseña") ?></label>
                                <div class="input-group input-group-merge">
                                    <input 
                                        :type="showPassword ? 'text' : 'password'" 
                                        id="password" 
                                        name="password"
                                        tabindex="11" 
                                        autocomplete="on"
                                        class="form-control" 
                                        placeholder="<?= t("Ingresa tu contraseña") ?>"
                                        required
                                        value=""
                                        >
                                        <div class="input-group-text cursor-pointer" @click="showPassword = !showPassword" data-password="false">
                                            <i :class="showPassword ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline'"></i>
                                        </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button tabindex="12" id="btnLogin" :disabled="loading" class="btn btn-primary" type="submit"> 
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <?= t("Ingresar") ?>
                                </button>
                                
                            </div>
                        
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            loading: false, 
            error: '<?= $data['error'] ?? '' ?>',
            username: '<?= $data['username'] ?? '' ?>',
            showPassword: false,
        },
        mounted() {
            setTimeout(function () { 
                document.getElementById("username").focus();
            }, 200);
            
        },
        methods: {
            
        }
    })
</script>
