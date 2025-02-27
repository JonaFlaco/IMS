<?php 
use App\Core\Application; 
$unreadNotificationCount = Application::getInstance()->coreModel->getUnreadNotificationsCount() ?? 0;
?>


<template id="tpl-notification-menu-component">
    
    <li class="dropdown notification-list">
        <a class="nav-link dropdown-toggle arrow-none" id="notifications-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
            <i class="dripicons-bell noti-icon"></i>
            <span hidden id="has_unread_notification" class="noti-icon-badge"></span>
            <span class="badge bg-primary">
                <span v-if="loading_unread_notifications_count != 1" >
                    <span >{{total_unread > 999 ? '999+' : total_unread}}</span>
                </span>
                <span v-if="loading_unread_notifications_count == 1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </span>
        </a>
        
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">

            <!-- item-->
            <div class="dropdown-item noti-title">
                <h5 class="m-0">
                    <span class="float-end">
                        <a href="javascript: void(0);" v-on:click = "markAllAsRead()" class="text-dark">
                            <small><?= t("Clear All"); ?></small>
                        </a>
                    </span><?= t("Notifications"); ?>
                </h5>
            </div>
            
            <div style="max-height: 230px;" data-simplebar="init">
                <div class="simplebar-wrapper" style="margin: 0px;">
                    <div class="simplebar-height-auto-observer-wrapper">
                        <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                            <div class="simplebar-content-wrapper" style="height: auto; overflow: hidden scroll;">
                                <div class="simplebar-content" id="notification_array" style="padding: 0px;">
                                    
                                    

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 386px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                    <div class="simplebar-scrollbar" style="height: 137px; display: block; transform: translate3d(0px, 0px, 0px);">
                    </div>
                </div>
            </div>

            <!-- All-->
            <a href="/notifications" class="dropdown-item text-center text-primary notify-item notify-all">
                <?= t("View All") ?>
            </a>

        </div>
    </li>

</template>


<script type="text/javascript">

    var notificationMenuComponent = {

        template: '#tpl-notification-menu-component',
        data() {
            return {
                loading_unread_notifications_count: 0,
                total_unread: <?= $unreadNotificationCount ?>,
            }
        },
        props: [],
        mounted(){
            
            let self = this;
            var myDropdown = document.getElementById('notifications-dropdown')
                myDropdown.addEventListener('show.bs.dropdown', function () {
                self.show_notifications();
            })
        },
        methods: {
            markAllAsRead(){
                
                let self = this;
                self.loading_unread_notifications_count = 1;
                axios({
                    method: 'post',
                    url: '/InternalApi/markAllNotificationAsRead?response_format=json',
                    data: null,
                    headers: {
                        'Content-Type': 'form-data',
                        'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("notifications") ?>',
                    }
                })
                .then(function(response){
                    if(response.data.status == 'success'){
                        self.total_unread = 0;
                        $.toast({heading: 'Success',text: response.data.message == undefined ? 'Task completed successfuly' : response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'success'});

                    } else if (response.data.status == 'failed'){
                        $.toast({heading: 'Error',text: response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    } else {
                        $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    }

                    self.loading_unread_notifications_count = 0;
                })
                .catch(function(error){
                    $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    self.loading_unread_notifications_count = 0;
                });
            },
            show_notifications(){
                this.get_notifications();
            },
            get_notifications() {

                document.getElementById('notification_array').innerHTML = '<h5 class="m-0 text-center"> <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...</h5>';
                
                axios({
                    method: 'get',
                    url: '/InternalApi/getnotifications?response_format=json',
                })
                .then(function(response){

                    if (response.data.status != undefined && response.data.status == 'failed'){
                        document.getElementById('notification_array').innerHTML = '<h5 class="m-0 text-center">' + response.data.message + '</h5>';
                    } else {
                        document.getElementById('notification_array').innerHTML = response.data;
                    }
                })
                .catch(function(error){
                    $.toast({heading: 'Error',text: 'An error occured while loading notifications',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    document.getElementById('notification_array').innerHTML = '<h5 class="m-0 text-center">An error occured while loading notifications</h5>';
                });
            },
        },
    }

    Vue.component('notification-menu-component', notificationMenuComponent)

</script>
