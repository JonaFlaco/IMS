<?php

use App\Core\Application;

$data = (object)$data; 
$nodeData = $data->nodeData;

$requests = Application::getInstance()->coreModel->get_ip_address_requests($nodeData->ip_address);

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
    
            <?php if($nodeData->is_blocked == true){ ?>
            <div class="col-md-12">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">                        
                        <i class="mdi mdi-block-helper"></i> This IP is blocked by <a class="text-white" target="_blank" href="/users/show/<?php echo $nodeData->blocked_user_id; ?>"><?php echo $nodeData->blocked_user_id_display; ?></a> on <?php echo \App\Helpers\DateHelper::humanify( strtotime($nodeData->blocked_date));?>
                    </div>
                </div>
                <!-- Personal-Information -->

            </div> <!-- end col-->
            <?php } ?>

            <div class="col-sm-12">
                <!-- Profile -->
                <div class="card bg-primary">
                    <div class="card-body profile-user-box">

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="media">
                                    <span class="float-start m-2 me-4">
                                <?php if (isset($nodeData->country_code)){ ?><img src="/assets/app/images/flags/<?php echo e($nodeData->country_code);?>.svg" style="height: 100px;" alt="" class="img-thumbnail"> <?php } ?>
                                    </span>
                                    <div class="media-body">

                                        <h4 class="mt-1 mb-1 text-white"><?php echo e($nodeData->ip_address); ?></h4>
                                        <p class="font-13 text-white-50"><?php echo e($nodeData->country_name); ?></p>

                                        <ul class="mb-0 list-inline text-light">
                                            <li class="list-inline-item me-3">
                                                <h5 class="mb-1"><?php echo e($nodeData->city);?></h5>
                                                <p class="mb-0 font-13 text-white-50"> City</p>
                                            </li>
                                                <li class="list-inline-item">
                                                    <h5 class="mb-1"><?php echo e($nodeData->zip);?></h5>
                                                    <p class="mb-0 font-13 text-white-50">Zip Code</p>
                                                </li>
                                        </ul>
                                    </div> <!-- end media-body-->
                                </div>
                            </div> <!-- end col-->

                            <div class="col-sm-4">
                                <div class="text-center mt-sm-0 mt-3 text-sm-end">
                                <a target="_blank" href="http://maps.google.com/maps?q=<?php echo e($nodeData->lat) . "," . e($nodeData->lng); ?>"><img src="/assets/app/images/icons/gps_pin.png" height="24" width="24"></img></a>
                                </div>
                            </div> 

                        </div> <!-- end row -->
                        

                    </div> <!-- end card-body/ profile-user-box-->
                </div><!--end profile/ card -->
            </div> <!-- end col-->


            <div class="col-md-12">

                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Requests</h4>
                        
                        <?php if(isset($requests) && $requests != array()){ ?>
                            <table class='table'><thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Browser</th>
                                <th>OS</th>
                                <th>Is Mobile</th>
                                <th>URL</th>
                                <th>Params</th>
                            </tr></thead><tbody>

                        <?php foreach($requests as $itm){ ?>
                            <tr>
                            <td><?php echo (\App\Helpers\DateHelper::humanify( strtotime($itm->created_date))); ?></td>
                            <td><?php echo e($itm->user_name); ?></td>
                            <td><img src="<?php echo (get_web_browser_logo($itm->browser)); ?>" width="24" hight="24" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo e($itm->browser); ?>"></img></td>
                            <td><img src="<?php echo (get_operating_system_logo($itm->os_name)); ?>" width="24" hight="24" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo e($itm->os_name); ?>"></img></td>
                            <td><?php echo e($itm->is_mobile); ?></td>
                            <td><?php echo e($itm->url); ?></td>
                            <td><?php echo e($itm->params); ?></td>
                            </tr>
                        <?php } ?>
                        </tr></thead><tbody>

                        </tbody></table>

                        <?php } ?>

                    </div>
                </div>


                </div>


        </div>
        <!-- end row -->

        

    </div>    

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>