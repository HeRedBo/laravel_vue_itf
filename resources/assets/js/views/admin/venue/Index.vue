<template>
    <div class="app-container calendar-list-container">
        <div class="filter-container">
            <el-input  style="width: 200px;" class="filter-item" placeholder="标题" ></el-input>
            <el-button class="filter-item" style="margin-left: 10px;"  type="primary" icon="edit">添加</el-button>  
        </div> 

        <el-table
            :data="tableData"
            border
            style="width: 100%"
            :default-sort = "{prop: 'date', order: 'descending'}"
        >
        <el-table-column
            prop="date"
            label="日期"
            sortable
            width="180">
        </el-table-column>
        <el-table-column
          prop="name"
          label="姓名"
          sortable
          width="180">
        </el-table-column>
        <el-table-column
          prop="address"
          label="地址"
          :formatter="formatter">
        </el-table-column>
      </el-table>
    </div>
</template>
<script>
    export default {
    data() {
      return {
        tableData: [{
          date: '2016-05-02',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-04',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1517 弄'
        }, {
          date: '2016-05-01',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1519 弄'
        }, {
          date: '2016-05-03',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1516 弄'
        }]
      }
    },
    methods: {
      formatter(row, column) {
        return row.address;
      }
    }
  }
</script>
<style rel="stylesheet/scss" lang="scss">
@import "resources/assets/styles/index";
</style>