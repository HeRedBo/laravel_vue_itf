<template>
    <div class="app-container calendar-list-container">
       
         <vTable
            :fields="fields"
         >
         
           <template slot="actions" scope="scope">
              <el-button
              size="small"
              @click="handleClick(scope.row)">编辑
            </el-button>
             
          </template>
          
        </vTable>
    </div>
        
</template>
<style rel="stylesheet/scss" lang="scss">
    @import "resources/assets/styles/index";
    </style>
<script>
 export default {
   
    methods: {
      handleClick(data) {
        console.log('asdfasd')
        console.log(data);
      }
    },
    data() {
      return {
        currentPage: 1,
        // pageSize: 20,
        fields: {
              date : {label : 'date', sortable: true},
              name : {label : 'name' , sortable:true},
              address: { label : 'address'},
              actions : {label: '操作'},
        }
            

      };
    }
  }
</script>

