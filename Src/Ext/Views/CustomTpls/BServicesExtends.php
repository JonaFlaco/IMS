<template id="tpl-members-assisted-list-component">
    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Miembros asistidos</h5>
            <p class="card-text"></p>
        </div>
        <ul class="list-group list-group-flush">
            <li v-for="member in this.$parent.nodeData.members_assisted" class="list-group-item" v-if="member.beneficiaries_id_display || member.family_member_display">
                <span v-if="member.beneficiaries_id_display">{{ member.beneficiaries_id_display }}</span>
                <span v-if="member.family_member_display">{{ member.family_member_display }}</span>
            </li>
        </ul>
    </div>
</template>

<script>
    Vue.component('members-assisted-list-component', {
        template: '#tpl-members-assisted-list-component',
        data() {
            return {
             
            };
        },
        mounted() {
        },
        methods: {
        }
    });
</script>