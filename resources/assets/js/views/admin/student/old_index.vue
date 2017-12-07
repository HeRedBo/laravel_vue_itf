
<template>
    <div class="box">
            <div class="app-container calendar-list-container">
                    <div class="filter-container">
                        <el-input  style="width:140px;" class="filter-item" placeholder="姓名" v-model="searchQuery.name" ></el-input>
                        <el-select style="width:90px"  v-model="searchQuery.sex" class="filter-item" placeholder="性别">
                            <el-option
                                  v-for="(value, key) in sexOptions"
                                  :key="key"
                                  :value="key"
                                  :label="value"
                                  >
                            </el-option>
                        </el-select>

                        <el-select style="width:160px"  v-show="selectItemVisible" v-model="searchQuery.venue_id" placeholder="请选择道馆"  class="filter-item"  @change="venueChange">
                            <el-option
                                   v-for="item in venueOptions"
                                   :key="item.value"
                                   :label="item.label"
                                   :value="item.value"
  
                                   >
                            </el-option>
                        </el-select>

                        <el-select style="width:160px" v-model="searchQuery.class_id" placeholder="请选择课程"  class="filter-item"  >
                                <el-option
                                       v-for="item in classOptions"
                                       :key="item.value"
                                       :label="item.label"
                                       :value="item.value"
      
                                       >
                                </el-option>
                            </el-select>
                        
                        <el-button class="filter-item" type="primary" icon="search" @click="loadList">搜索</el-button>
                        <router-link :to="{path:'create'}">
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
                        width="80"
                      >
             
                    </el-table-column>
                
                    <el-table-column
                        prop="name"
                        label="姓名"
                        width="120">
                    </el-table-column>
            
                    <el-table-column
                        prop="age"
                        label="年龄"
                        width="70">
                    </el-table-column>
            
                    <el-table-column
                        label="性别"
                        width="70"
                    >
                        <template slot-scope="scope">
                            <span>{{scope.row.sex == 1 ? "男":"女"}}</span>
                        </template>
                    </el-table-column>
                    
                    <el-table-column
                      label="picture"
                      >
                      <template slot-scope="scope">
                          <el-popover
                            ref="popover"
                            placement="right"
                            width="170"
                            trigger="hover"
                           >
                          <div style="text-align: right; margin: 0">
                            <img :src="scope.row.picture" width="150px" height="150px" class="user-avatar"/>
                          </div>
                        </el-popover>
                        <img :src="scope.row.picture" width="40px" height="40px" class="user-avatar" v-popover:popover />
                      </template>
                    </el-table-column>
            
                    <el-table-column
                        label="道馆"
                    >
                        <template slot-scope="scope">
                        <span>{{scope.row.venues.name}}</span>
                        </template>
                    </el-table-column>
            
                    <el-table-column
                        label="班级"
                        width="200px"
                    >
                        <template slot-scope="scope">
                            <el-tag v-for="item in scope.row.classes"
                                :key="item.id"
                                type="primary"
                                close-transition
                                >{{item.name}}
                            </el-tag>
                        </template>
                    </el-table-column>
            
                    <el-table-column
                        label="操作人"
                    >
                        <template slot-scope="scope">
                        <span>{{scope.row.operator.name}}</span>
                        </template>
                    </el-table-column>
            
                    <el-table-column
                        prop="created_at"
                        label="创建时间"
                        sortable="custom"
                        width="170"
                    >
                    </el-table-column>
                    <el-table-column label="操作" width="150">
                        <template slot-scope="scope">

                                <div class="btn-group">
                                        <!-- <router-link :to="{path:'setacl/'+ scope.row.id}" class="btn bg-purple btn-xs">设置权限</router-link> -->
                                       <!--  <a href="#" @click.prevent="view(scope.row.id)" class="btn btn-success btn-xs">查看</a> -->
                                        <router-link :to="{path:'update/'+  scope.row.id}" class="btn bg-orange btn-xs">编辑</router-link>

                                        <!-- <a @click="handleDelete(scope.row.id)" class="btn btn-danger btn-xs">删除</a> -->

                                        <!-- <a @click="handleDelete(scope.row.id)" class="btn btn-danger btn-xs">删除</a> -->
                                </div>
                        </template>
                    </el-table-column>
            
                  </el-table>
            
                  <div v-show="!listLoading"  v-if="totalRows>initPage" class="pagination-container">
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
    </div>
</template>
<script>
import {stack_error,parseSearchParam} from 'config/helper';
export default {
    data() {
      return {
        tableData: [],
        currentPage : 1,
        pageSizes : [15,20, 50, 100, 200],
        perPage : 15,
        initPage : 15,
        layouts : 'total, sizes, prev, pager, next, jumper',
        totalRows : 0,
        params : {
          orderBy:'id',
          sortedBy: 'desc'
        },
        searchQuery : {},
        listLoading: true,
        selectItemVisible : false,
        sexOptions : [],
        venueOptions : [],
        classOptions : []
      }
    },
    created() {
      this.loadList();
      this.getSexOptions();
      this.getUserVenus();
    },
    methods: {
        formatter(row, column) {
          return row.address;
        },
        getSexOptions() {
                var url = '/student/sexOptions',that = this;
                this.$http({
                   method :"GET",
                   url : url,
                })
                .then(function(response) {
                  var responseJson = response.data,data = responseJson.data
                  var options      = [];
                  that.sexOptions = data;
                })
                .catch(function(error) {
                  stack_error(error);
                }); 
        },

        getUserVenus() {
            var that = this;
            var url = '/user/userVenues';

            this.$http({
                    method :'GET',
                    url : url
            })
            .then(function(response) 
            {
                let {data} = response;
                var respondata = data.data
                var options = [];
                for (var i in respondata ) {
                
                    let label =  respondata[i].name;
                    options.push({value : respondata[i].id , label: label});
                }
                that.venueOptions = options;
                if(options.length == 1)
                {
                    var venue_id =  options[0].value;
                    that.searchQuery.venue_id =  venue_id;
                } 
                else
                {
                    that.selectItemVisible = true;
                }
                        // that.showCreateButton = true;
                })
                .catch(function(error) {
                    console.log(error);
                    stack_error(error);
                });
        },

        getClasses(venue_id) {
                var url = '/class/classOptions', that = this;
                this.$http({
                   method :"GET",
                   url : url,
                   params : {
                    venue_id : venue_id
                   }
                })
                .then(function(response) {
                  var responseJson = response.data,data = responseJson.data
                  var options = [];
                  for (var i in data ) {
                    let label =  data[i].name;
                    options.push({value : data[i].id , label: label});
                  } 
                  that.classOptions = options;
                })
                .catch(function(error) {
                  stack_error(error);
                }); 
            },


        venueChange(value) {
            this.getClasses(value)
        },
        loadList() {
          let url = 'student',that =this;
          var searchQuery = this.searchQuery;
          if(searchQuery.sex == -1)
            delete searchQuery.sex;
          var search_query = parseSearchParam(searchQuery);
          that.params.search = search_query;
          this.listLoading = true;
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
        this.loadList();
      },

      handleCurrentChange(val) {
        this.params.page = val;
        this.loadList();
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
        this.loadList();
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
              that.loadList();
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