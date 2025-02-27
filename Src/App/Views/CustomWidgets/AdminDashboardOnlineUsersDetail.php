<div class="card">
    <div class="card-body">

    <div class="dropdown float-end">
        <a href="javascript: void(0);" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a onclick="vm.chart_export_data('<?= $widget->id ?>')" href="javascript: void(0);" class="dropdown-item">Export As Excel</a>
        </div>
    </div>

    <h4 class="header-title"><?php echo $widget->name; ?> (<?= sizeof($data) ?>)</h4>

<div data-simplebar style="max-height: 250px;">
<?php 

$i = 0;
foreach($data as $itm){
    
    if(!isset($itm->profile_picture_name) || !is_file(UPLOAD_DIR_FULL . "\\users\\" . $itm->profile_picture_name)){
        if($itm->gender_id == 2){
            $itm->profile_picture_name = DEFAULT_PROFILE_PICTURE_FEMALE;
        } else {
            $itm->profile_picture_name = DEFAULT_PROFILE_PICTURE_MALE;
        }
    }

    ?>


    <div class="d-flex align-items-start mt-1 mb-1">
        <img class="me-3 rounded-circle" src="/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=<?= $itm->profile_picture_name; ?>" width="40" alt="User Profile Image">
        <div class="w-100 overflow-hidden">
            <span class="float-end"><?= \App\Helpers\DateHelper::humanify( strtotime($itm->last_heartbeat)); ?></span>
            <a class="text-dark" target="_blank" href="/users/show/<?= $itm->id; ?>">
                <h5 class="mt-0 mb-1"><?= $itm->full_name ?></h5>
            </a>
            <span class="font-13"><?= $itm->position_name ?> </span>
        </div>
    </div>

<?php } ?>
</div>

</div>
</div>