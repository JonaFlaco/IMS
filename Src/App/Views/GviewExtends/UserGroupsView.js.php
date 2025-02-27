
const ctypeId = 'user_groups';
const shapeWidth = 200;
const shapeHeight = 70;

var diagram;
document.getElementById("btnHideShowColumnsSettings").style.display = "none";

function template(config) {
    var template = '<section class="template">';
        template += '<div style="background-color:' + config.color + '; height: 4px;width: ' + shapeWidth + 'px;top: 0px;left: 0px;position: absolute;" class="dhx_item_header"></div>';
        template += '<div class="template_container">';
        template += '<h3>'+ config.name +'</h3>';
        if(config.organizer_name != null){
            template += '<p id="organizer_name"><i class="mdi mdi-tooltip-account"></i> '+ config.organizer_name +'</p>';
        }
        template += '</div>';
        template += '<div class="toggle_container"><img class="template_icon" src="/assets/app/js/dhtmlx/diagram/img/icons/toggle.svg" alt="toggle"></img></div>';
        template += '</section>';
    return template;
}

function refreshDiagram() {
    
    if(diagram != undefined){
        diagram.destructor();
    }
    var orgChartData = vm.records;
    
    diagram = new dhx.Diagram("diagram", { 
        type: "org",
        defaultShapeType: "template"
    });


    diagram.addShape("template", {
        template: template,
        defaults: {
            height: shapeHeight,
            width: shapeWidth
        }
    });
    

    diagram.data.parse(orgChartData);

    var item;
    var parentItem;

    diagram.events.on("ShapeClick", function (id) {
        item = diagram.data.getItem(id);
        parentItem = diagram.data.getItem(id);
    });

    
    diagram.events.on('shapedblclick', function () {
        showDetail(true);
    })

    var contextMenu = new dhx.ContextMenu(null, {
        css: "dhx_widget--bg_gray"
    });

    
    contextMenu.data.parse([
        {
            "type": "menuItem",
            "id": "quickEdit",
            "value": "Quick Edit",
            "icon": "mdi mdi-square-edit-outline"
        },
        {
            "type": "menuItem",
            "id": "edit",
            "value": "Edit",
            "icon": "mdi mdi-square-edit-outline"
        },
        {
            "type": "menuItem",
            "id": "add",
            "value": "Add Child",
            "icon": "dripicons-plus text-success"
        },
        {
            "type": "menuItem",
            "id": "delete",
            "value": "Delete",
            "icon": "dripicons-trash text-danger"
        }
    ]);

    contextMenu.events.on("click", function (id) {
        switch (id) {
            case "quickEdit":
                showDetail(true);
                break;
            case "edit":
                window.open('/' + ctypeId + '/edit/' + item.id, '_blank');
                break;
            case "add":
                showDetail();
                break;
            case "delete":
                window.open('/' + ctypeId + '/delete/' + item.id, '_blank');
                break;
        }
    });

    var toggleItem;

    
    function show(event) {
        event.preventDefault();
        contextMenu.showAt(event.target, "bottom");
    }
    
    function showMenu() {
        dhx.awaitRedraw().then(function () {
            if (toggleItem) {
                for (var index = 0; index < toggleItem.length; index++) {
                    toggleItem[index].removeEventListener("click", show);
                }
            }
            toggleItem = document.querySelectorAll(".toggle_container");
            for (var index = 0; index < toggleItem.length; index++) {
                toggleItem[index].addEventListener("click", show);
            }
        });
    }

    showMenu();

    diagram.events.on("afterexpand", function() {
        showMenu();           
    });

    document.getElementById("saveDetail").addEventListener("click", saveDetail);
    
    var btnAdd = document.getElementById('btnAdd');
    btnAdd.setAttribute('href', 'javascript: void(0);');
    btnAdd.removeAttribute('target');
    btnAdd.addEventListener("click",  function() { showDetail();});


        
    function showDetail(isEditMode = false) {
        
        if(isEditMode){
            document.getElementById("divJustification").style.display = "block";
            document.getElementById("justification").value = '';
        } else {
            item = null;
            document.getElementById("divJustification").style.display = "none";
            document.getElementById("justification").value = 'N/A';
        }
        
        document.getElementById("detailName").value = item?.name ?? "";
        document.getElementById("detailParent").value = (isEditMode ? item?.parent : parentItem?.id);
        document.getElementById("detailColor").value = item?.color;

        
        var myModal = new bootstrap.Modal(document.getElementById('detailModal'), {})
        myModal.show();
    }

    function saveDetail() {

        const element = document.querySelector('#detailForm');
        element.classList.add("was-validated");
        
        if (!document.getElementById('detailForm').checkValidity()) {
            $.toast({
                heading: 'Error',
                text: 'Please enter valid values',
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'error'
            });
            
            return;
        }


        let isAddMode = (item == null);

        let name = document.getElementById("detailName").value;
        let parent = document.getElementById("detailParent").value;
        let color = document.getElementById("detailColor").value;
        let justification = document.getElementById("justification").value;

        let data = {
            sett_ctype_id: ctypeId,                    
            id: item?.id,
            name: name?.replace(/'/g, "\'")
                        .replace(/"/g, "\""),
            color: color?.replace(/'/g, "\'")
                        .replace(/"/g, "\""),
            parent_group_id: parent,
        }
        
        let formData = new FormData();
        formData.append('justification', justification);
        formData.append('token', "");
        formData.append('data', JSON.stringify(data));
    
        axios({
            method: 'POST',
            url: '/' + ctypeId + '/' + (isAddMode ? 'add' : 'edit') + '/' + item?.id + '&response_format=json',
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data',
                'Csrf-Token': '1bde3068d132c698c0f812bdd20e1fe9', // TODO: Hardcoded CSRF found
            }
        }).then(function(response){
            
            try{
                if(response.data.status == 'success') {
                    
                    var modal = document.getElementById('detailModal')
                    modal = bootstrap.Modal.getInstance(modal)
                    modal.hide();
                    
                } else {
                    $.toast({
                        heading: 'Error',
                        text: response.data,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    
                } 
            } catch(e){
                $.toast({
                    heading: 'Error',
                    text: 'Something went wrong',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });

            }
            
        }).catch(function(error){
            
            if(error.response != undefined && error.response.data.status == 'failed') {
                $.toast({
                    heading: 'Error',
                    text: error.response.data.message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
                
                
            } else {

                $.toast({
                    heading: 'Error',
                    text: error.message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });

            }

        });

        
    }
}

