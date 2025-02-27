<?php 

use App\Core\Application;

$data = array("title" => "Settings");

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">


<style>
  .el-header {
    background-color: #B3C0D1;
    color: #333;
    line-height: 60px;
  }
  
  .el-aside {
    color: #333;
  }
</style>


<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Settings</h4>
        </div>
    </div>
</div>     

<div id="app">
        

    <el-container style="border: 1px solid #eee">
    <el-aside width="200px" style="background-color: rgb(238, 241, 246)">
        <el-menu :default-openeds="['1', '3']">
        <el-input
            placeholder="Please input"
            v-model="keyword"
            clearable>
            </el-input>
        <el-submenu index="1">
                <template slot="title"><i class="el-icon-message"></i>Main Group</template>
                <el-menu-item-group>
                <el-menu-item v-for="group in filteredGroupData" index="1-1">{{group.name}}</el-menu-item>
            </el-submenu>
        </el-submenu>
        <!-- <el-submenu index="2">
            <template slot="title"><i class="el-icon-menu"></i>Navigator Two</template>
            <el-menu-item-group>
            <template slot="title">Group 1</template>
            <el-menu-item index="2-1">Option 1</el-menu-item>
            <el-menu-item index="2-2">Option 2</el-menu-item>
            </el-menu-item-group>
            <el-menu-item-group title="Group 2">
            <el-menu-item index="2-3">Option 3</el-menu-item>
            </el-menu-item-group>
            <el-submenu index="2-4">
            <template slot="title">Option 4</template>
            <el-menu-item index="2-4-1">Option 4-1</el-menu-item>
            </el-submenu>
        </el-submenu>
        <el-submenu index="3">
            <template slot="title"><i class="el-icon-setting"></i>Navigator Three</template>
            <el-menu-item-group>
            <template slot="title">Group 1</template>
            <el-menu-item index="3-1">Option 1</el-menu-item>
            <el-menu-item index="3-2">Option 2</el-menu-item>
            </el-menu-item-group>
            <el-menu-item-group title="Group 2">
            <el-menu-item index="3-3">Option 3</el-menu-item>
            </el-menu-item-group>
            <el-submenu index="3-4">
            <template slot="title">Option 4</template>
            <el-menu-item index="3-4-1">Option 4-1</el-menu-item>
            </el-submenu>
        </el-submenu> -->
        </el-menu>
    </el-aside>
    
    <el-container>
<!--         
        <el-main>
        <el-table :data="tableData">
            <el-table-column prop="id" label="Id" width="140">
            </el-table-column>
            <el-table-column prop="name" label="Name" width="120">
            </el-table-column>
            <el-table-column prop="group" label="Group">
            </el-table-column>
        </el-table>
        </el-main> -->
    </el-container>
    </el-container>
    

</div>


<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>

<script src="https://unpkg.com/element-ui/lib/index.js"></script>


<script>
    var vm = new Vue({
        el: '#app',
        data: {
            keyword: '',
            groupData: [
                {
                    'name': 'Group 1',
                },
                {
                    'name': 'Group 2',
                },
                {
                    'name': 'Group 3',
                },
                {
                    'name': 'Group 4',
                },
                {
                    'name': 'Group 5',
                },
                {
                    'name': 'Group 6',
                },
                {
                    'name': 'Group 7',
                },

            ],
            tableData: [
                {
                    id: 1,
                    name: 'sett_1',
                    value: 10,
                    group: 'General'
                },
                {
                    id: 2,
                    name: 'sett_2',
                    value: 12,
                    group: 'General'
                },
                {
                    id: 3,
                    name: 'sett_3',
                    value: 10,
                    group: 'PRISM'
                },
                {
                    id: 4,
                    name: 'sett_4',
                    value: 10,
                    group: 'Iraq Portal'
                },
                {
                    id: 5,
                    name: 'sett_5',
                    value: 434,
                    group: 'Iraq Portal'
                },
            ]
        },
        computed: {
            filteredGroupData: function(){

                if(this.keyword.length == 0)
                    return this.groupData;

                return this.groupData.filter((itm) => itm.name.toLowerCase().trim().includes(this.keyword.toLowerCase().trim()));
            }
        }
    });

</script>