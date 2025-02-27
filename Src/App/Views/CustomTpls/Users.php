<?php

    use App\Core\Application;

    

    $data = (object)$data; 
    $nodeData = $data->nodeData;

    $coreModel = App\Core\Application::getInstance()->coreModel;

    $dailyLog = Application::getInstance()->userModel->get_user_daily_log($nodeData->id);

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

    <div id="cont">
        
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Profile</h4>
                </div>
            </div>
        </div>     
        <!-- end page title --> 


        <div class="row">
            <div class="col-sm-12">
                <!-- Profile -->
                <div class="card bg-primary">
                    <div class="card-body profile-user-box">

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="media">
                                    <span class="float-start m-2 me-4">
                                        <?php if(isset($nodeData->profile_picture_name) && _strlen($nodeData->profile_picture_name) > 0){ ?>
                                            <img src="/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=<?= $nodeData->profile_picture_name; ?>" style="height: 100px;" alt="" class="rounded-circle img-thumbnail">
                                        <?php } else { ?>
                                            <?php if($nodeData->gender_id == 2) { ?>
                                                <img src="<?php echo DEFAULT_PROFILE_PICTURE_FEMALE_FULL; ?>" style="height: 100px;" alt="" class="rounded-circle img-thumbnail">
                                            <?php } else { ?>
                                                <img src="<?php echo DEFAULT_PROFILE_PICTURE_MALE_FULL; ?>" style="height: 100px;" alt="" class="rounded-circle img-thumbnail">
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                    <div class="media-body">

                                        <h4 class="mt-1 mb-1 text-white"><?php echo e(isset($nodeData->full_name) && _strlen($nodeData->full_name) > 0 ? $nodeData->full_name : $nodeData->name); ?></h4>
                                        
                                        <?php if(isset($nodeData->position_id_display) && _strlen($nodeData->position_id_display) > 0) { ?>
                                            <p class="font-13 text-white-50"><?php echo e($nodeData->position_id_display); ?></p>
                                        <?php } ?>

                                        <ul class="mb-0 list-inline text-light">
                                            <?php if(isset($nodeData->email) && _strlen($nodeData->email) > 0) { ?>
                                                <li class="list-inline-item me-3">
                                                    <h5 class="mb-1"><?php echo e($nodeData->email);?></h5>
                                                    <p class="mb-0 font-13 text-white-50"> Email Address</p>
                                                </li>
                                            <?php } ?>

                                            <?php if(isset($nodeData->phone) && _strlen($nodeData->phone) > 0) { ?>
                                                <li class="list-inline-item me-3">
                                                    <h5 class="mb-1"><?php echo e($nodeData->phone);?></h5>
                                                    <p class="mb-0 font-13 text-white-50">Phone Number</p>
                                                </li>
                                            <?php } ?>

                                            <li class="list-inline-item me-3">
                                                <h5 class="mb-1 badge bg-<?= $nodeData->is_active ? "success" : "danger" ?>"><?= ($nodeData->is_active ? "Active" : "Disabled") ?></h5>
                                                <p class="mb-0 font-13 text-white-50">Account Status</p>
                                            </li>

                                        </ul>
                                    </div> <!-- end media-body-->
                                </div>
                            </div> <!-- end col-->

                            <?php if((Application::getInstance()->user->isAdmin()) || Application::getInstance()->user->getId() == $nodeData->id){ ?>
                                <div class="col-sm-4">
                                    <div class="text-center mt-sm-0 mt-3 text-sm-end">
                                        <a type="button" href="/users/edit/<?php echo e($nodeData->id); ?>" class="btn btn-light">
                                            <i class="mdi mdi-account-edit me-1"></i> Edit Profile
                                        </a>
                                    </div>
                                </div> <!-- end col-->
                            <?php } ?>

                        </div> <!-- end row -->
                        

                    </div> <!-- end card-body/ profile-user-box-->
                </div><!--end profile/ card -->
            </div> <!-- end col-->
        </div>
        <!-- end row -->


        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Contact Information</h4>
                        
                        <div class="text-start">
                            <p><strong>Full Name :</strong> <span class="ml-2"><?php echo e($nodeData->full_name);?></span></p>
                            <p><strong>Username :</strong> <span class="ml-2"><?php echo e($nodeData->name);?></span></p>

                            <p><strong>Registration Date :</strong> <span class="ml-2"><?php echo e($nodeData->created_date);?></span></p>

                            <p><strong>Email :</strong> <span class="ml-2"><?php echo e($nodeData->email);?></span></p>
                            <p><strong>Phone :</strong><span class="ml-2"><?php echo e($nodeData->phone);?></span></p>
                            
                        </div>
                    </div>
                </div>
                <!-- Personal-Information -->

            </div> <!-- end col-->

            
            <div class="col-md-8">

                <!-- Chart-->
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Details</h4>
                        
                        <p><strong>Status:</strong> <span class=" ms-2 badge badge-<?php echo ($nodeData->is_active == 1 ? "success" : "danger"); ?>"><?php echo ($nodeData->is_active == 1 ? "Active" : "Inactive"); ?></span></p>
              
                        <p><strong>Last Activity:</strong> <?php echo (!empty($nodeData->last_heartbeat) ? \App\Helpers\DateHelper::humanify( strtotime($nodeData->last_heartbeat)) : "N/A"); ?></span></p>

                        <p><strong>Gender:</strong> <span class="ml-2"><?php echo e($nodeData->gender_id_display);?></span></p>

                        <p><strong>Unit:</strong> <span class="ml-2"><?php echo e($nodeData->units_display);?></span></p>

                        <p><strong>Governorate:</strong> <span class="ml-2"><?php echo e($nodeData->governorates_display);?></span></p>

                        <p><strong>Roles:</strong> <span class="ml-2"><?php echo e($nodeData->roles_display);?></span></p>

                    </div>
                </div>

            </div>

            <?php if(Application::getInstance()->user->isAdmin()){ ?>
            

            <div class="col-md-12">

                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Login Log</h4>
                        

                        <div>

                        <?php 
                            $year = null;
                            $i = 1;
                            $x = 0;
                            foreach($dailyLog as $itm){ 
                                $date = $itm->date;
                                
                                if($year != date('Y', strtotime($date))){
                                    
                                    if($x++ > 0){
                                        if($i > 1){
                                            for($t = 0; $t <= 7-$i; $t++){
                                                echo "<div class=\"col-12 p-0\"><i class=\"m-0 text-white mdi mdi-square ms-1\"></i></div>";
                                            }
                                        }
                                        $i = 1;
                                        echo "</div></div>";
                                        $x = 0;
                                    }
                                    $year = date('Y', strtotime($date));
                                    echo "<h2>" . $year . "</h2>";
                                    echo "<div class=\"row\">";
                                    

                                    echo "<div class=\"row col-1 p-0 m-0 align-top\" style=\"max-width: 1.85% !important; margin-right 0px; margine-left: 0px;\">";
                                    echo "<div class=\"col-12 p-0\">" . substr(strtoupper(date('D', strtotime($date))),0,1) . "</div>";
                                    echo "<div class=\"col-12 p-0\">" . substr(strtoupper(date('D', strtotime($date . " - 1 day"))),0,1) . "</div>";
                                    echo "<div class=\"col-12 p-0\">" . substr(strtoupper(date('D', strtotime($date . " - 2 day"))),0,1) . "</div>";
                                    echo "<div class=\"col-12 p-0\">" . substr(strtoupper(date('D', strtotime($date . " - 3 day"))),0,1) . "</div>";
                                    echo "<div class=\"col-12 p-0\">" . substr(strtoupper(date('D', strtotime($date . " - 4 day"))),0,1) . "</div>";
                                    echo "<div class=\"col-12 p-0\">" . substr(strtoupper(date('D', strtotime($date . " - 5 day"))),0,1) . "</div>";
                                    echo "<div class=\"col-12 p-0\">" . substr(strtoupper(date('D', strtotime($date . " - 6 day"))),0,1) . "</div>";
                                    echo "</div>";
                                }

                                

                                if($i == 1){
                                    
                                    echo "<div class=\"row col-1 p-0 m-0 align-top\" style=\"max-width: 1.85% !important; margin-right 0px; margine-left: 0px;\">";
                                }

                                if($itm->value > 0){
                                    echo "<div class=\"col-12 p-0\"><i class=\"m-0 text-success mdi mdi-square ms-1\" data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . e($itm->date) . " (" . date('D', strtotime($date)) . ")\"></i></div>";
                                } else {
                                    echo "<div class=\"col-12 p-0\"><i class=\"m-0 text-light mdi mdi-square ms-1\" data-toggle=\"tooltip\" data-placement=\"top\" data-original-title=\"" . e($itm->date) . " (" . date('D', strtotime($date)) . ")\"></i></div>";
                                }



                                if($i >= 7){
                                    $i = 1;
                                    $x++;
                                    echo "</div>";
                                } else {
                                    
                                    $i++;
                                }

                                

                            }

                            while($i <= 7) {
                                echo "<div class=\"col-12 p-0\"><i class=\"m-0 text-light mdi mdi-square ms-1\" data-toggle=\"tooltip\" data-placement=\"top\" ></i></div>";
                                $i++;
                            }
                            if($i >= 7){
                                $i = 1;
                                $x++;
                                echo "</div>";
                            } else {
                                
                                $i++;
                            }

                            if($x++ > 0){
                                if($i > 1){
                                    for($t = 0; $t <= 7-$i; $t++){
                                        echo "<div class=\"col-12 p-0\"><i class=\"m-0 text-white mdi mdi-square ms-1\"></i></div>";
                                    }
                                }
                                $i = 1;
                                echo "</div></div>";
                                $x = 0;
                            }
                            
                        ?>

                        <div class="row m-2 float-end">
                            <span><i class="ml-2 text-info mdi mdi-information"></i> Indicators: </span>
                            <span><i class="ml-2 text-light mdi mdi-square"></i> No Login</span>
                            <span class="ml-2"><i class="text-success mdi mdi-square"></i> Has Login</span>
                        </div>
                        </div>

                    </div>
                </div>

            </div>
            <?php } ?>

        </div>
        <!-- end row -->
        
        </div> <!-- container -->    

    </div>    
    
<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>

