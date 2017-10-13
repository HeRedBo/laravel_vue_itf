<template>
    <div class="app-container calendar-list-container">
        <div class="filter-container">
            <el-input  style="width: 200px;" class="filter-item" placeholder="标题" ></el-input>
            <router-link :to="{path:'create'}" class="btn btn-success btn-md">
                <el-button class="filter-item" style="margin-left: 10px;"  type="primary" icon="edit">
                    添加  
                </el-button>
            </router-link> 
           
             
        </div> 

        <el-table
            :data="tableData"
            border
            stripe
            fit highlight-current-row
            style="width: 100%"
            :default-sort = "{prop: 'id', order:'descending'}"
            @sort-change="sortChange"
            v-loading="listLoading"
            element-loading-text="拼命加载中"
        >

        <el-table-column 
            align="center" 
            label="ID" 
            
            prop="id"
            sortable="custom"
          >
 
        </el-table-column>
    
        <el-table-column
            prop="name"
            label="道馆名"
            width="180">
        </el-table-column>
        
        <el-table-column
          label="logo"
          >
          <template scope="scope">


              <el-popover
                ref="popover"
                placement="right"
                width="170"
                trigger="hover"
               >
             
              <div style="text-align: right; margin: 0">
                <img :src="scope.row.logo" width="150px" height="150px" class="user-avatar"/>
                <!-- <el-button size="mini" type="text" >取消</el-button>
                <el-button type="primary" size="mini" >确定</el-button> -->
              </div>
            </el-popover>
            <img :src="scope.row.logo" width="40px" height="40px" class="user-avatar" v-popover:popover />
          </template>
      </el-table-column>

        <el-table-column
          prop="province"
          label="省"
          >
        </el-table-column>

        <el-table-column
          prop="city"
          label="市"
         >
        </el-table-column>

        <el-table-column
          prop="address"
          label="详细地址"
        >
        </el-table-column>

        <el-table-column
          prop="created_at"
          label="创建时间"
          sortable="custom"
         >
        </el-table-column>

        <el-table-column label="操作"
        width="150"
        >
            <template scope="scope">
                <router-link :to="{path:'update/'+  scope.row.id}">
                  <el-button
                  size="small"
                  @click="handleEdit(scope.$index, scope.row)">编辑
                </el-button>
  
                </router-link>
              
              <el-button
                size="small"
                type="danger"
                @click="handleDelete(scope.row.id)">删除
              </el-button>
            </template>
        </el-table-column>

      </el-table>

      <div v-show="!listLoading" class="pagination-container">
        <el-pagination
         @size-change="handleSizeChange"
         @current-change="handleCurrentChange"
         :current-page="currentPage"
         :page-sizes="pageSizes"
         :page-size="perPage"
         :layout="layouts"
         :total="totalRows">
       </el-pagination>
     </div>
    </div>
</template>
<script>
import {stack_error} from 'config/helper';
export default {
    data() {
      return {
        tableData: [],
        currentPage : 1,
        pageSizes : [15,20, 50, 100, 200],
        perPage : 1,
        layouts : 'total, sizes, prev, pager, next, jumper',
        totalRows : 0,
        params : {
          orderBy:'id',
          sortedBy: 'desc'
        },
        listLoading: true,

      }
    },
    created() {
      this.loadVenueList();
    },
    methods: {
        formatter(row, column) {
          return row.address;
        },

        loadVenueList() {
          let url = 'venue',that =this;
          this.listLoading = true
          this.$http({
            method :'GET',
            url : url,
            params : that.params
          })
          .then(function(response) {
            that.listLoading = false
            let {data} = response;
            let responseData = data.data;
            that.totalRows = responseData.total;
            that.perPage = responseData.per_page;
            that.tableData = data.data.data;
            //that.$router.back();
          })
          .catch(function(error) {
            that.listLoading = false;
            stack_error(error);
          });

        },

      handleSizeChange(val) {
        this.params.pageSize = val;
        this.loadVenueList();
      
      },

      handleCurrentChange(val) {
        this.params.page = val;
        this.loadVenueList();
      },

      sortChange(val) {
       
        this.params.orderBy = val.prop;

        if(val.order == 'ascending')
        {
            this.params.sortedBy = 'ASC';
        } 

        if(val.order == 'descending')
        {
            this.params.sortedBy = 'DESC';
        }

        this.loadVenueList();
      },

      handleEdit(index, row) {
        console.log(index, row);
      },
      handleDelete(id) {
        var that = this;
        swal({
              title: "确定要删除?",
              text: '确定要删除用户吗？',
              type: "warning",
              showCancelButton: true,
              cancelButtonText: "取消",
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "删除",
              closeOnConfirm: true
            }).then(function () {
              that.listLoading = false
              var url =  'venue' +'/'+id;
              console.log(url)
              that.$http({
                method :'DELETE',
                url : url,
              })
              .then(function(response) {
                that.listLoading = false
                let {data} = response;
                console.log(data);

                toastr.success(data.message);
              that.loadVenueList();
            })
            .catch(function(error) {
              that.listLoading = false;
              stack_error(error);
            });

                 
                   
                }, function (dismiss) {
                      // dismiss can be 'cancel', 'overlay',
                      // 'close', and 'timer'
                      if (dismiss === 'cancel') {
                        swal(
                          'Cancelled',
                          'Your imaginary file is safe :)',
                          'error'
                        )
                      }
                });
                        
       
      }

    }
}
</script>
<style rel="stylesheet/scss" lang="scss">
@import "resources/assets/styles/index";
</style>