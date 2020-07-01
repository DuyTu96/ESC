<template>
    <CCard>
        <CCardHeader>
            <slot name="header">
                <CIcon name="cil-grid" /> {{ caption }}
            </slot>
        </CCardHeader>
        <CCardBody>
            <CDataTable
                :items="items"
                :fields="fields"
                column-filter
                table-filter
                items-per-page-select
                :items-per-page="5"
                hover
                sorter
                pagination
            >
                <template #work_type="{item}">
                    <td>
                        <CBadge :color="getBadge(item.work_type)">
                            {{ item.work_type }}
                        </CBadge>
                    </td>
                </template>
                <template #show_details="{item, index}">
                    <td class="py-2">
                        <CButton
                            color="primary"
                            variant="outline"
                            square
                            size="sm"
                            @click="toggleDetails(item, index)"
                        >
                            {{ Boolean(item._toggled) ? 'Hide' : 'Show' }}
                        </CButton>
                    </td>
                </template>
                <template #details="{item}">
                    <CCollapse
                        :show="Boolean(item._toggled)"
                    >
                        <CCardBody>
                            <CMedia :aside-image-props="{ height: 102 }">
                                <h4>
                                    {{ item.username }}
                                </h4>
                                <p class="text-muted">
                                    User since: {{ item.start_date }}
                                </p>
                                <CButton
                                    size="sm"
                                    color="info"
                                    class=""
                                >
                                    User Settings
                                </CButton>
                                <CButton
                                    size="sm"
                                    color="danger"
                                    class="ml-1"
                                >
                                    Delete
                                </CButton>
                            </CMedia>
                        </CCardBody>
                    </CCollapse>
                </template>
            </CDataTable>
        </CCardBody>
    </CCard>
</template>

<script>
export default {
    name: 'Table',
    props: {
        items: Array,
        fields: {
            type: Array,
            default() {
                return [
                    { key: 'employee_ID', _style:'width:10%' },
                    { key: 'name' , style:'width:30%' },
                    { key: 'position', _style:'width:20%;' },
                    { key: 'department', _style:'width:20%;' },
                    { key: 'start_date', _style:'width:10%;' },
                    { key: 'work_type', _style:'width:10%;' },
                    { key: 'show_details' , label:'', _style:'width:1%', noSorting: true, noFilter: true },
                ];
            }
        },
        caption: {
            type: String,
            default: 'Table'
        },
        hover: Boolean,
        striped: Boolean,
        bordered: Boolean,
        small: Boolean,
        fixed: Boolean,
        dark: Boolean
    },
    methods: {
        getBadge(work_type) {
            return work_type === 'Active' ? 'success'
                : work_type === 'Inactive' ? 'secondary'
                    : work_type === 'Pending' ? 'warning'
                        : work_type === 'Banned' ? 'danger' : 'primary';
        },
        toggleDetails(index) {
            this.$router.push({ name: 'Employees information', params: { id: 1 }});
        }
    }
};
</script>

<style>
    .page-item.active .page-link {
        background-color: #39f;
        border-color: #39f;
    }

    .page-link {
        color: #39f;
    }
</style>
