<template id="tpl-related-forms-component">
    <div>
        <span v-if="data.loading">Loading ...</span>
        <span v-else-if="data.errorMessage" class="text-danger"> Error while loading Related : {{ data.errorMessage }} </span>
        <div v-else>
            <div class="p-1">
                <table width="80%" class="table table-striped">
                    <thead class='text-info'>
                        <th v-show="ctypeId !='edf_eoi'" style="text-align:center;">EDF Expression of Interest</th>
                        <th v-show="ctypeId !='edf_eoi_verification'" style="text-align:center;">EOI Verification</th>
                        <th v-show="ctypeId !='edf_eoi_full_application'" style="text-align:center;">Application</th>
                    </thead>
                    <tr>
                        <template v-for="(stageData, stageName) in stagesData">
                            <td v-show="ctypeId !== stageName" align="center">
                                <div v-if="data[stageData] !== null && data[stageData].length === 0" class="col-md-12 p-0 m-0">
                                    No Data
                                </div>
                                <div v-for="id in data[stageData]">
                                    <a target="_blank" :href="stagesLink(stageName, id.id)">
                                        <i class="mdi mdi-folder" style="cursor:pointer;font-size:2em;" title="View"></i>
                                        {{ id.code }}
                                    </a>
                                </div>
                            </td>
                        </template>
                    </tr>

                </table>

            </div>
        </div>

    </div>
</template>

<script>
    Vue.component('related-forms-component', {
        template: '#tpl-related-forms-component',
        data() {
            return {
                ctypeId: '',
                data: {
                    eoiData: null,
                    eoiVer: null,
                    edfApp: null,
                    errorMessage: '',
                    loading: false,
                },
                stagesData: {
                    edf_eoi: 'eoiData',
                    edf_eoi_verification: 'eoiVer',
                    edf_eoi_full_application: 'edfApp',
                }
            }
        },
        mounted() {
            this.getRelatedForms();
        },
        methods: {
            async getRelatedForms() {
                this.ctypeId = this.$parent.ctypeId
                const self = this;

                const id = this.ctypeId === 'edf_eoi' ? this.$parent.nodeData.id : (this.ctypeId === 'edf_eoi_verification' ? this.$parent.nodeData.business_id : this.$parent.nodeData.eoi_id);

                if (id == null || id == undefined) {
                    alert('Id not found');
                    return;
                }
                this.data.loading = true;

                try {
                    const response = await axios.get(`/InternalApi/EdfRelatedForms/${id}?response_format=json`);

                    if (response.status === 200 && response.data && response.data.status === 'success') {
                        this.updateFormData(response.data);
                    }
                } catch (error) {
                    let message = error;

                    if (error.response != undefined && error.response.data.status === 'failed') {
                        message = error.response.data.message;
                    }

                    self.data.errorMessage = message;
                    self.data.loading = false;
                }
            },

            updateFormData(data) {
                this.data.eoiData = data.edfEoiData;
                this.data.eoiVer = data.edfEoiVerData;
                this.data.edfApp = data.edfAppData;
                this.data.loading = false;
            },
            stagesLink(type, id) {
                switch (type) {
                    case 'edf_eoi':
                        return '/edf_eoi/show/' + id;
                    case 'edf_eoi_verification':
                        return '/edf_eoi_verification/show/' + id;
                    case 'edf_eoi_full_application':
                        return '/edf_eoi_full_application/show/' + id;
                    default:
                        return '/';
                }
            },
        }

    });
</script>