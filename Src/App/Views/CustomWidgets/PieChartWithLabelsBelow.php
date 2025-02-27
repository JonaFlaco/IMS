<?php
    
    if(isset($widget->colors)){
        $colors = _explode(",",$widget->colors);
    } else {
        $colors = _explode(",",WIDGET_COLORS);
    }

?>

<div class="card">
    <div class="card-body">
        
        <h4 class="header-title"><?php echo $widget->name; ?></h4>

        <div class="mb-1 mt-2 chartjs-chart" ><div class="chartjs-size-monitor" style="max-height: 100px; max-width: 100px;"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <div id="chart_<?php echo $widget->name; ?>"></div>
        </div>

        <div class="chart-widget-list">
            <?php 
                $i = 0;
                foreach($data as $itm){ 
                    $color = isset($colors) && isset($colors[$i]) ? $colors[$i] : "'#727cf5'";
                    $color = _str_replace("'","",$color);
            ?>
            <p>
                <i class="mdi mdi-square" style="color:<?php echo $color; ?> !important;"></i> <?php echo $itm->label; ?>
                <span class="float-end"><?php echo number_format($itm->value) . (isset($itm->{"value_perc"}) ? " - " . number_format($itm->value_perc,2) . "%" : ""); ?></span>
            </p>
            <?php 
                $i++;
                } 
            ?>
        </div>
    </div>
</div>