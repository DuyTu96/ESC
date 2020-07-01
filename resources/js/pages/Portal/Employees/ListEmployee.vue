<template>
    <div>
        <CRow>
            <CCol sm="12">
                <EmployeeTable
                    :items="getShuffledUsersData()"
                    hover
                    striped
                    bordered
                    small
                    fixed
                    caption="List"
                />
            </CCol>
        </CRow>
    </div>
</template>

<script>
import EmployeeTable from '~/components/Portal/Table/EmployeeTable.vue';
import usersData from './UsersData';

export default {
    name: 'Tables',
    components: { EmployeeTable },
    data() {
        return {
            formCollapsed: false
        };
    },
    methods: {
        shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                let j = Math.floor(Math.random() * (i + 1));
                let temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
            return array;
        },

        getShuffledUsersData() {
            return this.shuffleArray(usersData.slice(0));
        }
    }
};
</script>
