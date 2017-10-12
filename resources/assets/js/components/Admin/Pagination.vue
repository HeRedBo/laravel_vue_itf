<template>
  <div class="pagination-container">
       <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="currentPage1"
        :page-sizes="pageSizes"
        :page-size="pageSize"
        :layout="layouts"
        :total="totalRows">
      </el-pagination>
    </div>
</template>
<script>

export default {

    data() {
        return {
            currentPage1: 5,
        }
    },

    props : {
        listLoading : {
            type : Number,
            default : 1
        },
        currentPage : {
            type : Number,
            default : 1
        },
        pageSize : {
            type : Number,
            default : 20
        },
        pageSizes : {
            type : Array,
            default: function () {
                return [20, 50, 100, 200]
            } 
        },
        totalRows : {
            type : Number,
            default : 200
        },
        layouts : {
            type : String,
            default : "total, sizes, prev, pager, next, jumper"
        }
    },

    methods : {
         handleSizeChange(val) {
        console.log(`每页 ${val} 条`);
      },
      handleCurrentChange(val) {
        console.log(`当前页: ${val}`);
      }
    }
}
</script>
