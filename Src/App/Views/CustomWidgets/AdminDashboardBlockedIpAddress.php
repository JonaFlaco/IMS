
<div data-simplebar style="max-height: 250px;">
<?php 

$i = 0;
foreach($data as $itm){
    
    // if(!isset($itm->profile_picture_name) || !is_file(UPLOAD_DIR_FULL . "\\users\\" . $itm->profile_picture_name)){
    //     if($itm->gender_id == 2){
    //         $itm->profile_picture_name = DEFAULT_PROFILE_PICTURE_FEMALE;
    //     } else {
    //         $itm->profile_picture_name = DEFAULT_PROFILE_PICTURE_MALE;
    //     }
    // }

    ?>

    
    <div class="d-flex align-items-start mt-1 mb-1">
        <?php if(isset($itm->country_code)): ?>
            <img class="me-3 rounded-circle" src="/assets/app/images/flags/<?= $itm->country_code; ?>.svg" width="40" alt="User Profile Image">
        <?php else: ?>
            <img class="me-3 rounded-circle" src="/assets/app/images/unknown.png" width="40" alt="User Profile Image">
        <?php endif; ?>
        <div class="w-100 overflow-hidden">
            <span class="float-end"><?= \App\Helpers\DateHelper::humanify( strtotime($itm->blocked_date)); ?></span>
            <a class="text-dark" target="_blank" href="/sec_ip_address/show/<?= $itm->id; ?>">
                <h5 class="mt-0 mb-1"><?= $itm->ip_address ?></h5>
            </a>
            <span class="font-13"><a target="_blank" href="http://maps.google.com/maps?q=<?= $itm->lat . "," . $itm->lng ?>"><img src="/assets/app/images/icons/gps_pin.png" height="16" width="16"></img></a> <?= $itm->city . ", " . $itm->country_name; ?> </span>
        </div>
    </div>

<?php } ?>
</div>
