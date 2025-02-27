<?php
    $data = $data[0];
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card widget-flat bg-danger text-white">
            <div class="card-body">
                <div class="float-end">
                    <i class="text-white mdi mdi-block-helper widget-icon"></i>
                </div>
                <h5 class="text-white font-weight-normal mt-0" title="Blocked IPs"><?php echo $widget->name;?></h5>
                <a class="text-white" href="javascript: void(0);" onclick="vm.chart_<?php echo $widget->name; ?>_get_detail('<?php echo $widget->name; ?>',<?php echo $widget->id; ?>)"><h3 class="mt-3 mb-3"><?php echo $data->value;?></h3></a>
                
            </div> <!-- end card-body-->
        </div>
    </div>

</div>
