<?php 
use \App\Core\Application;

$fieldTypes = Application::getInstance()->coreModel->nodeModel("field_types")
    ->fields(["id", "name", "description", "icon"])
    ->OrderBy("sort")
    ->load();

$fieldTypeAppearances = Application::getInstance()->coreModel->nodeModel("field_type_appearances")
    ->fields(["id", "name", "description", "icon", "field_type_id"])
    ->OrderBy("m.sort")
    ->load();

?>

<style scoped>

    .bg-color-region {
        background-color: #053fb3 !important
    }
    .text-color-region {
        color: #795548 !important
    }
    .bg-color-region-lighten {
        background-color: #d0dfff !important;
    }


    .bg-color1 {
        background-color: #536de6 !important
    }
    .bg-color1-lighten {
        background-color: #d4dbfc !important;
    }

    .bg-color2 {
        background-color: #667eed !important
    }
</style>

<script type="text/x-template" id="tpl-fields-list-component">
    
    <div class="col-md-12">

        <!-- addFieldModal -->
        <div id="addFieldModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    
                    <div class="modal-body">

                        <div class="card m-0">
                            <div class="card-body">
                            
                                <h4 class="mb-3 header-title">Add a new Field</h4>

                                <!-- Left sidebar -->
                                <div class="page-aside-left">

                                    <!-- Links -->
                                    <div class="email-menu-list mt-1">
                                        <a 
                                            v-for="item in fieldTypes" 
                                            :key="item.id"
                                            href="javascript: void(0);" 
                                            @click="selectedFieldType = item"
                                            class="pt-1 pb-0"
                                            :class="{'text-danger font-weight-bold': selectedFieldType?.id == item.id}"
                                            >
                                            <span class="account-user-avatar">
                                                <img height="28" :src="'/assets/app/images/field_type_icons/' + item.icon">
                                            </span>
                                            {{ item.name }}
                                        </a>

                                    </div>

                                </div>
                                <!-- End Left sidebar -->

                                <!-- Right sidebar -->
                                <div class="page-aside-right pt-0">
                                    <!-- Top Bar -->
                                    <div v-if="selectedFieldType">
                                        <div class="row">
                                            <div class="col-sm-12">                            
                                                <h4 class="mb-3 header-title">
                                                    {{ selectedFieldType.name }}
                                                </h4>
                                                <p>{{ selectedFieldType.description }}</p>
                                            </div>
                                        </div>

                                        <div 
                                            v-if="selectedFieldType" 
                                            class="m-1 cursor-pointer" 
                                            v-for="obj in fieldTypeAppearances.filter((x) => x.field_type_id == selectedFieldType?.id)"
                                            :key="obj.id"
                                            @click="addFieldAction(obj.field_type_id, obj.id)"
                                            >
                                            <span class="account-user-avatar">
                                            <img height="28" :src="'/assets/app/images/field_type_icons/' + obj.icon">
                                            </span>
                                            {{ obj.name }}
                                            <p class="mt-1"> {{ obj.description}} </p>
                                            <hr>

                                        </div>
                                    </div>
                                    <div v-else>
                                        Please select an item
                                    </div>

                                </div> 
                                <!-- End of Right sidebar -->
                            
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-secondary" data-bs-dismiss="modal"> Close</button>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>

        <!-- dependency modal -->
        <div id="dependencyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    
                    <div class="modal-body">

                        <div class="card m-0">
                            <div class="card-body">
                            
                                <h4 class="mb-3 header-title">Dependency</h4>

                                <div class="mb-3">
                                    <!-- <textarea v-model="dependency" class="form-control" id="dependency-textarea" rows="7"></textarea> -->
                                    <dependencies-component
                                        title="Dependency" 
                                        name="dependencyMain"
                                        @update="updateDependency"
                                        ref="dependencyComponent"
                                        :value="dependency"
                                        >
                                    </dependencies-component>
                                </div>

                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-secondary" data-bs-dismiss="modal"> 
                                    <i class="mdi mdi-close"></i>
                                    Close
                                </button>
                                <button class="btn btn-primary" @click="saveDependency"> 
                                    <i class="mdi mdi-content-save"></i> 
                                    Save
                                </button>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>

        <div class="col-lg-12 btn-group mb-2">
            <button 
                type="button" 
                v-tooltip="'Clear all the filters'" 
                @click="filterHideIsSystemFields = false;filterHideIsHiddenFields = false;filterFieldNameContains='';filterFieldType=null" 
                class="btn btn-light">
                <i class="mdi mdi-24px mdi-window-close"></i>
            </button>
            <button 
                type="button" 
                v-tooltip="'Filter by keyword'" 
                class="btn btn-light col-lg-12">
                <!-- <i class="mdi mdi-24px mdi-form-textbox col-lg-2" :class="filterFieldNameContains.length > 0 ? 'text-primary' : ''" ></i> -->
                <input type="text" class="col-lg-12" v-model="filterFieldNameContains">
            </button>
            <button 
                type="button" 
                v-tooltip="'Filter by system field'" 
                @click="filterHideIsSystemFields = !filterHideIsSystemFields" 
                class="btn btn-light">
                <i v-if="filterHideIsSystemFields" class="text-primary mdi mdi-24px mdi-shield-off-outline"></i>
                <i v-else class="mdi mdi-24px mdi-shield-lock-outline"></i>
            </button>
            <button 
                type="button" 
                v-tooltip="'Filter by hidden fields'" 
                @click="filterHideIsHiddenFields = !filterHideIsHiddenFields" 
                class="btn btn-light">
                <i v-if="filterHideIsHiddenFields" class="text-primary mdi mdi-24px mdi-eye-off-outline"></i>
                <i v-else class="mdi mdi-24px mdi-eye-circle-outline"></i>
            </button>
            <div class="btn-group">
            <button 
                    class="btn dropdown-toggle btn-light" 
                    type="button" 
                    v-tooltip="'Filter by field type'" 
                    id="filterFieldTypedropdownMenuButton" 
                    data-bs-toggle="dropdown" 
                    aria-haspopup="true" 
                    aria-expanded="false">
                    <span v-if="filterFieldType" class="text-primary">
                        <img height="24" :src="'/assets/app/images/field_type_icons/' + filterFieldType.icon">
                        {{ filterFieldType.name }}
                    </span>
                    <span v-else>
                        Filter by Field
                    </span>
                </button>
                <div class="dropdown-menu">
                    <button type="button" class="dropdown-item" @click="filterFieldType = null;; $('#filterFieldTypedropdownMenuButton').dropdown('toggle')"> -- All -- </button>
                    <button v-for="field in fieldTypes" class="dropdown-item" type="button" @click="filterFieldType = field; $('#filterFieldTypedropdownMenuButton').dropdown('toggle')">
                        <img height="24" :src="'/assets/app/images/field_type_icons/' + field.icon">
                        {{ field.name }} 
                    </button>
                </div>
            </div>
        </div>



        <draggable 
            tag="ul" 
            v-model="tabs" 
            ghost-class="vuedraggable-ghost" 
            handle=".tab-handle" 
            class="nav nav-tabs nav-bordered mb-3"
            >
            <li 
                v-for="tab in tabs" 
                :key="tab.sort"
                @mouseover="$set(tab, 'isMouseOver', true)"
                @mouseleave="$set(tab, 'isMouseOver', false)"
                class="nav-item nav-item-tab"
                >
                
                <a :href="'#tab' + tab.sort" data-bs-toggle="tab" aria-expanded="false" class="nav-link dragover-to-enter tab-handle">
                    <i class="mdi mdi-pan me-1 cursor-grab"></i>
                    {{ tab.name}}
                    <a 
                        @click="openDependency(tab)"
                        href="javascript:void(0);" 
                        v-if="tab.dependencies" 
                        >
                        <i 
                            class="mdi mdi-24px mdi-code-array"
                            v-tooltip="'This tab has dependency condition'" 
                        ></i>
                        
                    </a>
                            
                    <button 
                        class="btn btn-link p-1 text-dark dropdown-toggle" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        v-tooltip="'More Actions'"
                        >
                    </button>    
                
                    <div
                        class="dropdown-menu">
                        <a href="javascript:void(0);" @click="openDependency(tab)" class="dropdown-item">
                            <i class="mdi mdi-code-array me-1"></i>
                            Tab Dependency Condition
                        </a>
                        <a href="javascript:void(0);" @click="rename(tab)" class="dropdown-item">
                            <i class="mdi mdi-form-textbox me-1"></i>
                            Rename Tab
                        </a>
                        <a href="javascript:void(0);" @click="deleteTab(tab)" class="dropdown-item">
                            <i class="mdi mdi-trash-can me-1"></i>
                            Delete Tab
                        </a>
                    </div>
                    
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript: void(0);" @click="addTab()"  class="nav-link">
                    <i class="mdi mdi-plus me-1"></i>
                    Add Tab 
                </a>
            </li>
        </draggable>

        <div class="tab-content">
            
            <div 
                v-for="tab in tabs" 
                :key="tab.sort"
                @mouseover="$set(tab, 'isMouseOver', true)"
                @mouseleave="$set(tab, 'isMouseOver', false)"
                class="tab-pane" 
                :id="'tab' + tab.sort"
                >
                
                <div class="row">

                    <div class="col-sm-4">                            
                        <h4> 
                            {{ tab.name }}
                        </h4>
                        
                    </div>
                    
                </div>
                
                <div 
                    v-if="tab.locations.length == 0"
                    class="p-1 mt-1 mb-1 text-white" 
                    >
                    <i class="mdi mdi-information-outline me-2"></i> Empty Tab
                </div>
                
                <div class="row ms-0 me-0">
                    <div 
                        v-for="location in tab.locations" 
                        :key="location.sort"
                        class="card-body m-0 p-0 px-1"
                        :class="'col-md-' + (location.name == 'left' || location.name == 'right' ? 6 : 12)"
                        @mouseover="$set(location, 'isMouseOver', true)"
                        @mouseleave="$set(location, 'isMouseOver', false)"
                        >
                        
                        <div v-if="location.isButtonToAddNew == 1" @click="addLocation(tab, location.name)" class="cursor-pointer">
                            
                            <div class="card text-white text-white bg-color-region border border-secondary mt-3 mb-3 p-0">

                                <div class="card-body p-0">
                                    
                                    <div class="p-1 mt-1 mb-1" >
                                        <i class="mdi mdi-plus-circle-outline"></i> 
                                        <i><span>Click here to add location (<strong>{{ location.name }}</strong>)</span></i>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div v-else
                            class="card m-0 mt-1 mb-3 bg-color-region-lighten border border-secondary"
                            :class="(location.name == 'left' ? 'mr-2' : '') + (location.name == 'right' ? 'ml-2' : '')"
                            >
                            <div class="card-body p-0">
                                <div class="card-widgets p-0 bg-color-region text-white">
                            
                                    <a 
                                        @click="openDependency(location)"
                                        href="javascript:void(0);" 
                                        >
                                        <i 
                                            v-if="location.dependencies" 
                                            class="mdi mdi-24px mdi-code-array"
                                            v-tooltip="'This region has dependency condition'" 
                                            ></i>
                                    </a>
                                    

                                    <a 
                                        href="javascript:void(0);" 
                                        class="dropdown-toggle arrow-none card-drop" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false"
                                        v-tooltip="'More Actions'"
                                        >
                                        <i class="mdi mdi-24px mdi-dots-vertical"></i>
                                    </a>
            
                                    <div
                                        class="dropdown-menu dropdown-menu-right">
                                            <a href="javascript:void(0);" @click="openDependency(location)" class="dropdown-item">
                                                <i class="mdi mdi-code-array me-1"></i>
                                                Group Dependency Condition
                                            </a>
<!--                                             
                                            <a href="javascript:void(0);" @click="deleteLocation(tab, location)" class="dropdown-item">
                                                <i class="mdi mdi-trash-can me-1"></i>
                                                Delete Region
                                            </a> -->
                                    </div>
                                </div>
                                <h5 class="capitalize p-1  m-0 bg-color-region">
                                    <a 
                                        data-bs-toggle="collapse" 
                                        class="text-white"
                                        :href="'#cardtab' + tab.sort + 'location' + location.sort" 
                                        role="button" 
                                        aria-expanded="false" 
                                        aria-controls="'#cardtab' + tab.sort + 'location' + location.sort"
                                        >
                                        Region: {{ location.name }} <span v-tooltip="'Number of groups inside this location'">({{location.groups.length}})</span>
                                    </a>
                                </h5>
                            
                                <div 
                                    :id="'cardtab' + tab.sort + 'location' + location.sort" 
                                    class="collapse show p-2">

                                    <div 
                                        v-if="!groupDragged && location.groups.length == 0"
                                        class="p-1 mt-1 mb-1 text-dark"
                                        >
                                        <i class="mdi mdi-information-outline me-2"></i> Empty Region
                                    </div>
                                    <draggable 
                                        tag="div" 
                                        :list="location.groups" 
                                        class="mb-1 mt-1 " 
                                        :class="groupDragged && location.groups.length == 0 ? 'p-2 border border-danger dashed-border' : ''"
                                        @start="groupDragged = true"
                                        @end="endDragGroup"
                                        :group="{ name: 'groups' }" 
                                        ghost-class="vuedraggable-ghost" 
                                        handle=".group-handle"
                                        >

                                    
                                        <div 
                                            v-for="group in location.groups"
                                            @mouseover="$set(group, 'isMouseOver', true)"
                                            @mouseleave="$set(group, 'isMouseOver', false)"
                                            class="card border border-secondary mt-3 mb-3 p-0"
                                            >

                                            <div class="card-body p-0">
                                                <div class="card-widgets p-0 bg-color2 text-white">
                                                
                                                    <a 
                                                        @click="openDependency(group)"
                                                        href="javascript:void(0);" 
                                                        >
                                                        <i 
                                                            v-if="group.dependencies" 
                                                            class="mdi mdi-24px mdi-code-array"
                                                            v-tooltip="'This group has dependency condition'" 
                                                            ></i>
                                                    </a>
                                                    
                                                    <a 
                                                        href="javascript:void(0);" 
                                                        class="dropdown-toggle arrow-none card-drop" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false"
                                                        v-tooltip="'More Actions'"
                                                        >
                                                        <i class="mdi mdi-24px mdi-dots-vertical cursor-pointer"></i>
                                                    </a>
                                                    
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a href="javascript:void(0);" @click="rename(group)" class="dropdown-item">
                                                            <i class="mdi mdi-form-textbox me-1"></i>
                                                            Rename Group
                                                        </a>
                                                        <a href="javascript:void(0);" @click="openDependency(group)" class="dropdown-item">
                                                            <i class="mdi mdi-code-array me-1"></i>
                                                            Group Dependency Condition
                                                        </a>
                                                        
                                                        <a href="javascript:void(0);" @click="deleteGroup(location, group)" class="dropdown-item">
                                                            <i class="dripicons-trash me-1"></i>
                                                            Delete Group
                                                        </a>
                                                    </div>

                                                </div>
                                                <div>
                                                    <h5 class="bg-color2 text-white m-0 p-1">
                                                        <i class="mdi mdi-pan group-handle cursor-grab"></i>
                                                        <a 
                                                            data-bs-toggle="collapse" 
                                                            class="text-white"
                                                            :href="'#cardtab' + tab.sort + 'location' + location.sort + 'group' +  group.sort" 
                                                            role="button" 
                                                            aria-expanded="false" 
                                                            aria-controls="'#cardtab' + tab.sort + 'location' + location.sort + 'group' +  group.sort"
                                                            >
                                                            Group: {{group.name}} <span v-tooltip="'Number of fields inside this group'">({{group.fields.length}})</span>
                                                    </a>
                                                    
                                                </h5>
                                                </div>
                                                

                                                <div 
                                                    :id="'cardtab' + tab.sort + 'location' + location.sort + 'group' +  group.sort" 
                                                    class="collapse show bg-white"
                                                    >
                                                    
                                                    <div 
                                                        v-if="!fieldDragged && group.fields.length == 0"
                                                        class="p-1 mt-1 mb-1" 
                                                        >
                                                        <i class="mdi mdi-information-outline me-2"></i> Empty Group
                                                    </div>

                                                    <draggable 
                                                        tag="div" 
                                                        :list="group.fields" 
                                                        :group="{ name: 'fields' }"
                                                        ghost-class="vuedraggable-ghost" 
                                                        handle=".field-handle"
                                                        class="row m-0"
                                                        :class="fieldDragged && group.fields.length == 0 ? 'p-2 border border-danger dashed-border' : ''"
                                                        @start="fieldDragged = true"
                                                        @end="fieldDragged = false"
                                                        >
                                                        <div
                                                            v-for="field in group.fields.filter((e) => 
                                                                (!filterHideIsSystemFields || !e.is_system_field) &&
                                                                (!filterHideIsHiddenFields || !e.is_hidden) &&
                                                                (filterFieldType == null || e.field_type_id == filterFieldType.id) &&
                                                                (filterFieldNameContains.length == 0 || e.name.toLowerCase().includes(filterFieldNameContains.toLowerCase().trim()) || e.title.toLowerCase().includes(filterFieldNameContains.toLowerCase().trim()))
                                                            )" 
                                                            :key="field.ind" 
                                                            class="p-0 m-0"
                                                            :class="'col-md-' + (12 / (field.size ?? 1))"
                                                            >
                                                            <div class="row m-1 border border-info solid-border">
                                                                
                                                                <div class="col-auto bg-light p-1">
                                                                    <i class="text-dark mdi mdi mdi-pan field-handle cursor-grab"></i>
                                                                </div>
                                                                <div class="col-auto px-1">
                                                                    <img height="28" class="mt-1" :src="'/assets/app/images/field_type_icons/' + field.icon">
                                                                </div>
                                                                <div class="col ps-0">
                                                                    <a href="javascript:void(0);" @click="edit(field.sett_index)" class="text-body">{{field.title}}</a>
                                                                    <p class="mb-0 text-muted"><small>{{ field.name }} ({{ field.type }})</small></p>
                                                                </div>

                                                                <div class="col-auto">
                                                                    <a v-tooltip="'Click to change field size'" href="javascript:void(0);" @click="updateSize(field)" class="text-body"> 
                                                                        <span v-if="field.size == 2">1/2</span>
                                                                        <span v-else-if="field.size == 3">1/3</span>
                                                                        <span v-else-if="field.size == 4">1/4</span>
                                                                        <span v-else>Full</span>
                                                                    </a>
                                                                    <p class="mb-0 text-muted"><small>Size</small></p>
                                                                </div>

                                                                <div v-if="field.size < 3" class="col-auto">
                                                                    <i v-if="field.name == 'id'" v-tooltip="'This field is primary column'" class="text-warning mdi mdi-24px mdi-key-variant"></i>    
                                                                    <i v-if="field.is_required" v-tooltip="'This field is required'" class="text-warning mdi mdi-24px mdi-star"></i>
                                                                    <i v-if="field.is_system_field" v-tooltip="'This field is system field'" class="text-primary mdi mdi-24px mdi-shield-lock-outline"></i>
                                                                    <i v-if="field.is_hidden" v-tooltip="'This field is hidden'" class="mdi mdi-24px mdi-eye-off-outline"></i>
                                                                    
                                                                    <i v-if="field.validation_pattern" v-tooltip="'This field has validation pattern'" class="mdi mdi-24px mdi-codepen"></i>
                                                                    <i v-if="field.validation_condition" v-tooltip="'This field has validation condition'" class="mdi mdi-shield-check-outline"></i>
                                                                    <i v-if="field.dependencies" v-tooltip="'This field has dependency condition'" class="mdi mdi-24px mdi-code-array"></i>
                                                                    <i v-if="field.required_condition" v-tooltip="'This field has required condition'" class="mdi mdi-24px mdi-code-array"></i>
                                                                    <i v-if="field.read_only_condition" v-tooltip="'This field has read only condition'" class="mdi mdi-24px mdi-code-array"></i>
                                                                </div>

                                                                <div class="col-auto">
                                                                    <a href="javascript: void(0);" v-tooltip="'Edit this field'" @click="edit(field.sett_index)"
                                                                        class="action-icon text-primary"><i class="mdi mdi-24px mdi-pencil"></i></a>

                                                                    <a href="javascript: void(0);" v-tooltip="'Delete this field'" @click="deleteField(group, field)"
                                                                        class="action-icon text-danger"><i class="mdi mdi-24px mdi-delete"></i></a>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        
                                                    </draggable>

                                                    <div class="row m-0 cursor-pointer" @click="addField(tab, location, group)">
                                                        <div
                                                            class="p-0 m-0 col-md-12">
                                                            <div class="p-1 bg-success text-white   ">
                                                                
                                                                <i class="mdi mdi-plus-circle-outline"></i> 
                                                                <i><span>Click here to add a new <strong>field</strong></span></i>

                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <!-- <div class="card text-white bg-color2 border border-secondary mt-3 mb-3 p-0 cursor-pointer" 
                                                        >

                                                        <div class="card-body p-0">
                                                            
                                                            <div class="p-1 mt-1 mb-1" >
                                                                
                                                            </div>

                                                        </div>

                                                    </div> -->

                                                </div>
                                            </div>

                                        </div>

                                    </draggable>


                                        <div class="card text-white bg-color2 border border-secondary mt-3 mb-3 p-0 cursor-pointer" 
                                            @click="addGroup(location)">

                                            <div class="card-body p-0">
                                                
                                                <div class="p-1 mt-1 mb-1" >
                                                    <i class="mdi mdi-plus-circle-outline"></i> 
                                                    <i><span>Click here to add a new <strong>group</strong></span></i>
                                                </div>

                                            </div>

                                        </div>



                                
                                </div>
                            </div>
                        
                        </div>

                    </div>
                </div>
                
            </div>

        </div>


        <div 
            v-if="(!tabs || tabs.length == 0)"
            class="alert alert-info" role="alert">
            <i class="dripicons-information me-2"></i> Empty, click <strong>Add Tab</strong> to add a new tab
        </div>
        
    </div>
    
        
</script>

<script>

    var selected = function(source, field = required("field"), value = null, operator = '=') { return true; } 
    function required(name) {   throw new Error('Parameter ' + name +' is missing'); }

    
    Vue.component('fields-list-component', {
        template: '#tpl-fields-list-component',
        props: {
            title: {},
            value: {},
        },
        data() {
            return {
                tabs: [],
                fields: [],
                fieldTypes: <?= json_encode($fieldTypes) ?>,
                fieldTypeAppearances: <?= json_encode($fieldTypeAppearances) ?>,
                selectedFieldType: null,
                selectedTab: null,
                selectedLocation: null,
                selectedGroup: null,

                groupDragged: false,
                fieldDragged: false,

                selectedDependencyItem: null,
                dependency: null,
                filterHideIsSystemFields: 1,
                filterHideIsHiddenFields: 0,
                filterFieldType: null,
                filterFieldNameContains: ''
            }
        },
        mounted() {
            
            $('#dependencyModal').on('shown.bs.modal', function () {
                $('#dependency-textarea').trigger('focus');
            });
            
            if(this.value.length == 0) {
                this.tabs =  [
                    {
                        name: 'General',
                        sort: 0,
                        locations: [
                            {
                                name: 'top',
                                sort: 0,
                                groups: [
                                    {
                                        name: 'General',
                                        sort: 0,
                                        fields: [],
                                    },
                                ],
                            },
                            {
                                name: 'left',
                                sort: 1,
                                isButtonToAddNew: true,
                                groups:[]
                            },
                            {
                                name: 'right',
                                sort: 2,
                                isButtonToAddNew: true,
                                groups:[]
                            },
                            {
                                name: 'bottom',
                                sort: 3,
                                isButtonToAddNew: true,
                                groups:[]
                            },
                        ]
                    },
                    
                ];
            }

            this.refresh(this.value);

            //Activate first tab
            setTimeout(function(){
                $('.nav-tabs a:first').tab('show');
            }, 500);
        },
        methods: {
            updateDependency: function (value) {
                this.dependency = value;
            },
            openDependency(item){
                this.selectedDependencyItem = item;
                
                this.dependency = item.dependencies;

                var myModal = new bootstrap.Modal(document.getElementById('dependencyModal'), {})
                myModal.show();

                

            },
            saveDependency() {

                if(this.$refs.dependencyComponent.beforeSave() == false) {
                    return;
                }

                this.$set(this.selectedDependencyItem, 'dependencies', this.dependency);
                
                logModal = bootstrap.Modal.getInstance(document.getElementById('dependencyModal'))
                logModal.hide();
            },
            updateSize(field) {

                if(field.size == null || field.size.length == 0)
                    field.size = 1;

                if(field.size == 4)
                    field.size = 1;
                else
                    field.size++;
                    
            },
            updateValue: function () {
                
                let i = 0;
                let newValue = [];
                
                this.tabs.forEach((tab) => {

                    tab.locations.filter((e) => !e.isButtonToAddNew).forEach((location) => {
                        location.groups.forEach((group) => {

                            group.fields.forEach((field) => {

                                field.tab_name = tab.name;
                                field.location = location.name;
                                field.group_name = group.name;
                                newValue.push(field);

                            });
                            
                        });

                    });

                });

                newValue.forEach((item) => {
                    item.sort = i++;
                })
                
                this.$emit('input', newValue);
            },
            refresh(value) {
                
                this.fields = value;
                
                this.fields.forEach((field) => {
                    let obj = this.fieldTypeAppearances.find((x) => x.field_type_id == field.field_type_id && x.id == field.appearance_id);
                    let fieldType = this.fieldTypes.find((x) => x.id == field.field_type_id);
                    
                    field.type = obj ? fieldType.name + '/' + obj.name : fieldType.name;
                    field.icon = obj ? obj.icon : fieldType.icon;

                    field.tab_name = field.tab_name ?? 'General';
                    field.location = field.location ?? 'top';
                    field.group_name = field.group_name ?? 'General';
                });

                let i = 0;
                let j = 0;

                let oldTabs = this.tabs;
                oldTabs.forEach((tab) => {
                    tab.locations.filter((e) => !e.isButtonToAddNew).forEach((location) => {
                        location.groups.forEach((group) => {
                            group.fields = [];
                        });
                    });
                });

                var newTabs = [];
                var oldFieldGroups = this.$parent.field_groups;
                
                this.fields.map((item) => item.tab_name).filter((itm, idx, arr) => arr.indexOf(itm) === idx).forEach((tab) => {
                    newTabs.push(
                        {
                            name: tab,
                            sort: i++,
                            dependencies: this.$parent.field_groups.find((g) => g.name == tab && g.type == 'tab')?.dependencies,
                            locations: [
                                {
                                    name: 'top',
                                    sort: 0,
                                    isButtonToAddNew: false,
                                    dependencies: this.$parent.field_groups.find((g) => g.name == tab + ' - top' && g.type == 'location')?.dependencies,
                                    groups: this.fields.filter((obj) => obj.tab_name == tab && obj.location == 'top').map((obj) => obj.group_name).filter((itm, idx, arr) => arr.indexOf(itm) === idx).map((group) => {
                                        return {
                                            name: group,
                                            sort: j++,
                                            dependencies: this.$parent.field_groups.find((g) => g.name == tab + ' - top - ' + group && g.type == 'group')?.dependencies,
                                            fields: this.fields.filter((obj) => obj.tab_name == tab && obj.location == 'top' && obj.group_name == group),
                                        }
                                    }),
                                },
                                {
                                    name: 'left',
                                    sort: 1,
                                    isButtonToAddNew: false,
                                    dependencies: this.$parent.field_groups.find((g) => g.name == tab + ' - left' && g.type == 'location')?.dependencies,
                                    groups: this.fields.filter((obj) => obj.tab_name == tab && obj.location == 'left').map((obj) => obj.group_name).filter((itm, idx, arr) => arr.indexOf(itm) === idx).map((group) => {
                                        return {
                                            name: group,
                                            sort: j++,
                                            dependencies: this.$parent.field_groups.find((g) => g.name == tab + ' - left - ' + group && g.type == 'group')?.dependencies,
                                            fields: this.fields.filter((obj) => obj.tab_name == tab && obj.location == 'left' && obj.group_name == group),
                                        }
                                    }),
                                },
                                {
                                    name: 'right',
                                    sort: 2,
                                    isButtonToAddNew: false,
                                    dependencies: this.$parent.field_groups.find((g) => g.name == tab + ' - right' && g.type == 'location')?.dependencies,
                                    groups: this.fields.filter((obj) => obj.tab_name == tab && obj.location == 'right').map((obj) => obj.group_name).filter((itm, idx, arr) => arr.indexOf(itm) === idx).map((group) => {
                                        return {
                                            name: group,
                                            sort: j++,
                                            dependencies: this.$parent.field_groups.find((g) => g.name == tab + ' - right - ' + group && g.type == 'group')?.dependencies,
                                            fields: this.fields.filter((obj) => obj.tab_name == tab && obj.location == 'right' && obj.group_name == group),
                                        }
                                    }),
                                },
                                {
                                    name: 'bottom',
                                    sort: 3,
                                    isButtonToAddNew: false,
                                    dependencies: this.$parent.field_groups.find((g) => g.name == tab + ' - bottom' && g.type == 'location')?.dependencies,
                                    groups: this.fields.filter((obj) => obj.tab_name == tab && obj.location == 'bottom').map((obj) => obj.group_name).filter((itm, idx, arr) => arr.indexOf(itm) === idx).map((group) => {
                                        return {
                                            name: group,
                                            sort: j++,
                                            dependencies: this.$parent.field_groups.find((g) => g.name == tab + ' - bottom - ' + group && g.type == 'group')?.dependencies,
                                            fields: this.fields.filter((obj) => obj.tab_name == tab && obj.location == 'bottom' && obj.group_name == group),
                                        }
                                    }),
                                },
                            ],
                        }
                    );

                });

                newTabs.sort(this.compare);

                newTabs.forEach((tab) => {
                    tab.locations.filter((e) => !e.isButtonToAddNew).forEach((location) => {
                        location.groups.sort(this.compare);
                    });
                })
                
                
                if(oldTabs && oldTabs.length > 0){
                    
                    this.tabs = oldTabs;

                    this.tabs.forEach((tab) => {

                        tab.locations.filter((e) => !e.isButtonToAddNew).forEach((location) => {
                            location.groups.forEach((group) => {

                                group.fields = this.fields.filter((field) => field.tab_name == tab.name && (field.location ?? 'top') == location.name && field.group_name == group.name)

                            });

                        })

                    });

                } else {

                    newTabs.forEach((tab) => {
                        let delLoc = [];
                        tab.locations.forEach((location) => {
                            if(location.groups.length == 0) {
                                delLoc.push(location.name);
                            }
                        });

                        delLoc.forEach((loc) => {
                            tab.locations = tab.locations.filter((x) => x.name != loc);
                        });

                    });

                    this.tabs = newTabs;
                }

                this.generateFieldGroups();
            },
            compare( a, b ) {
                if ( a.sort < b.sort ){
                    return -1;
                }
                if ( a.sort > b.sort ){
                    return 1;
                }
                return 0;
            },
            rename(item) {
                var value = window.prompt('Enter a new name: ');

                if(!value && value.length == 0) {
                    alert('Invalid input');
                    return
                }
                
                item.name = value;
                
            },
            addTab() {

                var name = window.prompt('Enter tab name');
                
                if(!name && name.length == 0) {
                    alert('Invalid input');
                    return;
                }

                if(this.tabs.find((tab) => tab.name.toLowerCase().trim() == name.toLowerCase().trim())){
                    alert('This tab already exist');
                    return;
                }
                
                this.tabs.push({
                    name: name.trim(),
                    sort: this.tabs.length + 1,
                    locations: [
                        {
                            name: 'top',
                            sort: 0,
                            groups: [
                                {
                                    name: 'General',
                                    sort: 0,
                                    fields: [],
                                }
                            ],
                        },
                        {
                            name: 'left',
                            sort: 1,
                            isButtonToAddNew: true,
                            groups:[]
                        },
                        {
                            name: 'right',
                            sort: 2,
                            isButtonToAddNew: true,
                            groups:[]
                        },
                        {
                            name: 'bottom',
                            sort: 3,
                            isButtonToAddNew: true,
                            groups:[]
                        },
                    ]
                });
                

            },
            addGroup(location) {
                
                var name = window.prompt('Enter group name');

                if(!name && name.length == 0) {
                    alert('Invalid input');
                    return;
                }

                if(location.groups.find((group) => group.name.toLowerCase().trim() == name.toLowerCase().trim())){
                    alert('This group already exist');
                    return;
                }

                location.groups.push({
                    name: name.trim(),
                    sort: location.groups.length + 1,
                    fields: [],
                });
                
            },
            addLocation(tab, locationName) {

                if(tab.locations.find((item) => !item.isButtonToAddNew && item.name == locationName)) {
                    alert('Location "' + locationName + '" already added');
                    return;
                }
                    
                tab.locations = tab.locations.filter((e) => e.name != locationName);

                let sort = 0;
                if(locationName == 'left') {
                    sort = 1;
                } else if (locationName == 'right') {
                    sort = 2;
                } else if (locationName == 'bottom') {
                    sort = 3;
                }

                tab.locations.push(
                    {
                        name: locationName,
                        sort: sort,
                        isButtonToAddNew: false,
                        groups:[
                            {
                                name: 'General',
                                fields: [],
                            }
                        ]
                    }
                );

                
                tab.locations.sort(this.compare);
                
            },
            deleteTab(tab) {
                if(!confirm('Are you sure you want to delete "' + tab.name + '"'))
                    return;
            
                this.tabs = this.tabs.filter((item) => item != tab);
            },
            deleteGroup(location, group) {
                if(!confirm('Are you sure you want to delete "' + group.name + '"'))
                    return;

                location.groups = location.groups.filter((item) => item.name != group.name);
                
            },
            deleteLocation(tab, location) {
                if(!confirm('Are you sure you want to delete "' + location.name + '"'))
                    return;
                
                location.groups = [];
                location.isButtonToAddNew = true;
            },
            addField(tab, location, group) {

                var myModal = new bootstrap.Modal(document.getElementById('addFieldModal'), {})
                myModal.show();
                
                this.selectedTab = tab;
                this.selectedLocation = location;
                this.selectedGroup = group;
                
            },
            deleteField(group, field) {
                group.fields = group.fields.filter((item) => item.name != field.name);
            },
            endDragGroup() {
                this.groupDragged = false;

                this.tabs.forEach((tab) => {

                    tab.locations.forEach((location) => {

                        var groups = location.groups.map((group) => group.name);
                        var processedGroups = [];

                        groups.forEach((group) => {
                            if(location.groups.filter((x) => x.name == group).length <= 1 || processedGroups.find((x) => x == group)){
                                //Ignore
                            } else {

                                //Processing
                                
                                oldGroup = location.groups.filter((item) => item.name == group)[0];

                                var fields = [];
                                
                                location.groups.filter((item) => item.name == group).map((item) => item.fields).forEach((t) => {
                                    fields = fields.concat(t);
                                });

                                location.groups = location.groups.filter((item) => item.name != group);

                                location.groups.push({
                                    name: oldGroup.name,
                                    sort: oldGroup.sort,
                                    fields: fields,
                                });

                                location.groups.sort(this.compare)

                                //Add to processed
                                processedGroups.push(group);
                            }

                        });

                    });

                });
            },
            addFieldAction(field_type_id, appearance_id) {

                this.updateValue();
                
                logModal = bootstrap.Modal.getInstance(document.getElementById('addFieldModal'))
                logModal.hide();

                this.$parent.addNewfields();

                this.$parent.current_fields.field_type_id = field_type_id;
                this.$parent.current_fields.appearance_id = appearance_id;

                this.$parent.current_fields.tab_name = this.selectedTab.name;
                this.$parent.current_fields.location = this.selectedLocation.name;
                this.$parent.current_fields.group_name = this.selectedGroup.name;
                
                this.$parent.reload_current_fields_appearance_id(field_type_id);
                this.$parent.handleChange_current_fields_field_type_id();
                
            },
            edit(sett_index) {
                this.$parent.editRecordfields(sett_index);
            },
            generateFieldGroups() {
                
                let items = [];
                let i = 0;
                
                this.tabs.forEach((tab) => {

                    items.push(
                        {
                            name: tab.name,
                            dependencies: tab.dependencies,
                            type: 'tab',
                            sort: i++,
                        }
                    );
                    
                    if(tab.locations.filter((e) => e.name == "top").length == 0) {
                        tab.locations.push(
                            {
                                name: 'top',
                                sort: 0,
                                isButtonToAddNew: true,
                                groups:[]
                            }
                        )
                    }

                    if(tab.locations.filter((e) => e.name == "left").length == 0) {
                        tab.locations.push(
                            {
                                name: 'left',
                                sort: 1,
                                isButtonToAddNew: true,
                                groups:[]
                            }
                        )
                    }

                    if(tab.locations.filter((e) => e.name == "right").length == 0) {
                        tab.locations.push(
                            {
                                name: 'right',
                                sort: 2,
                                isButtonToAddNew: true,
                                groups:[]
                            }
                        )
                    }

                    if(tab.locations.filter((e) => e.name == "bottom").length == 0) {
                        tab.locations.push(
                            {
                                name: 'bottom',
                                sort: 3,
                                isButtonToAddNew: true,
                                groups:[]
                            }
                        )
                    }

                    tab.locations.filter((e) => !e.isButtonToAddNew).forEach((location) => {
                        
                        items.push(
                            {
                                name: tab.name + ' - ' + location.name,
                                dependencies: location.dependencies,
                                type: 'location',
                                sort: i++,
                            }
                        );
                        
                        location.groups.forEach((group) => {

                            items.push(
                                {
                                    name: tab.name + " - " + location.name + " - " + group.name,
                                    dependencies: group.dependencies,
                                    type: 'group',
                                    sort: i++,
                                }
                            );
                        });

                    });

                });

                this.$set(this.$parent, 'field_groups', items)
                

            },
            beforeSave(fields) {
                this.updateValue();
                this.generateFieldGroups();

                if(this.$parent.fields.length == 0) {
                    $.toast({
                        heading: 'Error',
                        text: 'Fields can not be empty',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    return false;
                }

                let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) !== index)
                duplicates = findDuplicates(this.$parent.fields.map((item) => item.name));
                if(duplicates.length > 0) {
                
                    $.toast({
                        heading: 'Error',
                        text: 'Duplicated fields found' + ' (' + duplicates.join(" and ") + ')',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    
                    return false;

                }
                

            },
            beforeDelete($id = null) {
            }
        },
    });
    
</script>

<!-- End of Field Groups - Field-Collection -->