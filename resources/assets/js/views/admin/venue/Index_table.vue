<template>
    <div class="app-container calendar-list-container">
            <vTable ref="table"
            :fields="fields"
            stripped
            
        > 
    
       </vTable>

    </div>
        
</template>
<style rel="stylesheet/scss" lang="scss">
    @import "resources/assets/styles/index";
    </style>
<script>

export default {
    data() {
        return {
            fields: {
                date : {label : 'date', sortable: true},
                name : {label : 'name' , sortable:true},
                address: { label : 'address'}
            },
            

        }
    },

    
}
</script>

