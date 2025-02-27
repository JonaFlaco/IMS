<?php use App\Core\Application; ?>

<!-- Components -->
<?= Application::getInstance()->view->renderView('admin/Update/components/CtypesDetailModalComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Update/components/CtypesResultTableComponent', (array)$data) ?>

<template id="tpl-ctypes-component">
    <div>
        
        <!-- Content-Type Detail Modal -->
        <ctypes-detail-modal-component
            :item="currentCtype"
            :previous-ctype-list="previousCtypeList"
            @open-detail="openDetail"
            @close="closeDetail"
            @goto-previous-ctype="gotoPreviousCtype"
            >
        </ctypes-detail-modal-component>


        <div class="row">
            <div class="col-sm-4">
                <h4>Content-Types</h4>    
            </div>

            <div class="col-sm-8">
                <div class="text-sm-end">
                    <button @click="refresh()" class="btn btn-primary mb-2">
                        <i v-if="ctypesItemsStatus != 1" class="mdi mdi-refresh me-2"></i> 
                        <i v-if="ctypesItemsStatus == 1" class="mdi mdi-spin mdi-refresh me-2"></i> 
                        Refresh
                    </button> 
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label" for="support_tickets_code">Name:</label>
        
                <input class="form-control" type="text" 
                    v-model="ctypesFilterName"
                    >
                </input>

            </div>
            
            <div v-if="ctypesItemsStatus == 1">
                Loading...
            </div>
            <div v-if="ctypesItemsStatus == 2" class="col-sm-12">
                <ctypes-result-table-component :items="ctypesItems" :ctype-name-search="ctypesFilterName" @open-detail="openDetail"/>
                </table>
            </div> <!-- end table-responsive-->
            <div v-if="ctypesItemsStatus == 3">
                Error while loading data
            </div>

        </div>

    </div>
    

</template>


<script type="text/javascript">

    var CtypesComponent = {

        template: '#tpl-ctypes-component',
        data() {
            return {
                currentCtype: null,
                previousCtypeList: [],
                ctypesFilterName: '',
                ctypesItems: [],
                ctypesItemsStatus: 0,
                ctypesErrorInLoading: null,
            }
        },
        props: [],
        methods: {
            async refresh(){
                
                let self = this;
                self.ctypesItems = [];
                self.ctypesItemsStatus = 1;
                self.ctypesErrorInLoading = '';

                var response = await axios.get( '/InternalApi/systemupdate/?cmd=get_ctypes&response_format=json',   
                    ).catch(function(error){
                        self.ctypesItemsStatus = 3;
                        if(error.response.data.message != undefined){
                            self.ctypesErrorInLoading = error.response.data.message;
                        } else {
                            self.ctypesErrorInLoading = 'Something went wrong while loading data';
                        }
                    });

                    if(response.status == 200) {
                        self.ctypesItems = response.data.result;
                        self.ctypesItemsStatus = 2;
                    }
            },
            openDetail(name) {
                
                if(this.ctypesItems.find((e) => e.name == name) == null) {
                    alert(name + ' not found');
                    return;
                }

                
                var myModal = bootstrap.Modal.getInstance(document.getElementById('ctypeDetailModal'))
                if(myModal) {
                    myModal.hide();
                } 
                
                myModal = new bootstrap.Modal(document.getElementById('ctypeDetailModal'), {})
                myModal.show();

                this.dialog = true;
                
                if(this.currentCtype != null){
                    this.previousCtypeList.push(this.currentCtype?.name);
                }

                this.currentCtype = this.ctypesItems.find((e) => e.name == name);
            },  
            closeDetail() {
                
                myModal = bootstrap.Modal.getInstance(document.getElementById('ctypeDetailModal'))
                if(myModal) {
                    myModal.hide();
                }
                
                this.previousCtypeList = [];
            } ,
            gotoPreviousCtype: function() {
                
                if(this.previousCtypeList.length > 0) {
                
                    let name = this.previousCtypeList.pop();
                    
                    this.currentCtype = this.ctypesItems.find((e) => e.name == name);

                }
                
            },
            
        },
        computed: {
            
        },
    }

    Vue.component('ctypes-component', CtypesComponent)

</script>
