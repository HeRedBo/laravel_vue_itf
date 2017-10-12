<template>
    <div class="app-container calendar-list-container">
       
         <vTable
            :fields="fields"
         ></vTable>
    </div>
        
</template>
<style rel="stylesheet/scss" lang="scss">
    @import "resources/assets/styles/index";
    </style>
<script>
 export default {
   
    methods: {
    },
    data() {
      return {
        currentPage: 1,
        // pageSize: 20,
        fields: {
                date : {label : 'date', sortable: true},
                name : {label : 'name' , sortable:true},
                address: { label : 'address'}
        }
            

      };
    }
  }
</script>

