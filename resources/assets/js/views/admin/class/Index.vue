<template>
    <div class="box">
            <div class="app-container calendar-list-container">
                    
                        <div class="filter-container">
                                <el-input  style="width: 200px;" class="filter-item" placeholder="班级名称" v-model="searchQuery.name" ></el-input>
                                <el-button class="filter-item" type="primary" icon="search" @click="loadList">搜索</el-button>
                            <el-button v-show="showCreateButton" class="filter-item" style="margin-left: 10px;"  @click="handleCreate" type="primary" icon="edit">
                                添加  
                            </el-button>
                            
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
                            label="道馆"
                        >
                        <template  slot-scope="scope"> 
                            <span> {{scope.row.venues.name}} </span>
                        </template>
                        </el-table-column>
                    
                        <el-table-column
                            prop="name"
                            label="班级名称"
                            width="180">
                        </el-table-column>

                        <el-table-column
                            label="班级备注"
                        >
                            <template  slot-scope="scope">
                                    <el-tooltip  placement="right" >
                                            <div class="remark_content" slot="content">
                                                    <p>
                                                            {{scope.row.remark}}
                                                    </p>
                                                </div>
                                        <span class="auto_hidden">{{scope.row.remark}}</span>
                                    </el-tooltip>
                            </template>
                        </el-table-column>

                        
                    
                        <el-table-column
                            prop="created_at"
                            label="创建时间"
                            sortable="custom"
                        >
                        </el-table-column>


                        <el-table-column
                        prop="updated_at"
                        label="最新更新时间"
                    
                        >
                        </el-table-column>

                        <el-table-column
                        
                          label="操作人"
                      >
                      <template  slot-scope="scope"> 
                          <span> {{scope.row.operator.name}} </span>
                      </template>
                      </el-table-column>
                    
                        <el-table-column label="操作" width="250">
                        <template slot-scope="scope">
                            <div class="btn-group">
                                <button class="btn bg-orange btn-xs" @click="handleUpdate(scope.row)">编辑</button>
                                <a @click="handleDelete(scope.row.id)" class="btn btn-danger btn-xs">删除</a>
                            </div>
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
                    
                        <el-dialog :title="dialogTitle" :visible.sync="dialogFormVisible" >
                    
                            <el-form class="small-space" 
                                ref="ClassForm" 
                                :model="ClassForm"
                                :rules="RoleRules"
                                label-position="right"
                                label-width="80px"
                                style='width: 400px; margin-left:50px;'
                            >
                              <el-form-item label="课程名称" prop="name">
                                <el-input v-model="ClassForm.name" auto-complete="off" ></el-input>
                              </el-form-item>
                              
                              <el-form-item label="道馆" v-show="selectItemVisible" >
                                <el-select v-model="ClassForm.venue_id" placeholder="请选择道馆" style="width:100%" >
                                  <el-option
                                     v-for="item in venueOptions"
                                     :key="item.value"
                                     :label="item.label"
                                     :value="item.value">
                                  </el-option>
                                </el-select>
                             </el-form-item>

                              <el-form-item label="课程备注">

                                <el-input type="textarea" :autosize="{ minRows: 3, maxRows: 6}" v-model="ClassForm.remark"></el-input>
                              </el-form-item>
                    
                            </el-form>
                            <div slot="footer" class="dialog-footer">
                              <el-button @click="dialogFormVisible = false">取 消</el-button>
                              <el-button type="primary" @click="handleClass">确 定</el-button>
                            </div>
                          </el-dialog>
                          
                      </div>
    </div>


</template>

<script>
import {stack_error,parseSearchParam} from 'config/helper';
export default {
    data() {
        const validateClassName = (rule, value, callback) => {
         this.validateClassName(value, function(status) {
           if(status == 1)
           {
                callback(new Error('道馆名称已存在'))
           } else {
                callback()
           }
         });
      }

        return {
            tableData: [],
            currentPage : 1,
            pageSizes : [15,20, 50, 100, 200],
            perPage : 15,
            layouts : 'total, sizes, prev, pager, next, jumper',
            totalRows : 0,
            params : {
                orderBy:'id',
                sortedBy: 'desc'    
            },
            searchQuery : {},
            listLoading: true,
            dialogFormVisible : false,
            selectItemVisible : false,
            showCreateButton : false,
            dialogTitle : '创建班级',
            ClassForm : {
                name : '',
                venue_id : '',
                remark : '',
            },

            RoleRules: {
                name: [
                    { required: true, message: '请输入班级名称', trigger: 'blur'},
                    { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                    { validator: validateClassName, trigger: 'blur' }
                ],
            },
            venueOptions : []
        }
    },
    created() {
        this.getUserVenus();
       
    },
    methods : {
        handleCreate () {
            var venue_id = this.ClassForm.venue_id;
            this.ClassForm = {};
            if(venue_id)
            {
                this.ClassForm.venue_id = venue_id;
            }
            
            this.dialogFormVisible = true;
            
        },

        getUserVenus() 
        {
            var that = this;
            var url = '/user/userVenues';
            this.$http({
                method :'GET',
                url : url
           })
           .then(function(response) 
           {

                let {data} = response;
                var  respondata = data.data
                var options = [];
                for (var i in respondata ) {
                   
                    let label =  respondata[i].name;
                    options.push({value : respondata[i].id , label: label});
                }
                that.venueOptions = options;
                if(options.length == 1)
                {
                    that.ClassForm.venue_id =  options[0].value;
                } 
                else
                {
                    that.selectItemVisible = true;
                }
                that.showCreateButton = true;
             })
            .catch(function(error) {
                console.log(error);
                stack_error(error);
            });
        },


        handleClass() {
            this.$refs.ClassForm.validate(valid => {
            var that = this;
            if (valid) 
            {
                let url = '/class' + (this.ClassForm.id ? '/' + this.ClassForm.id : '')
                let method = this.ClassForm.id ? 'put' : 'post'
                this.$http({
                    method :method,
                    url : url,
                    data : that.ClassForm
                })
                .then(function(response) {
                    var {data} = response; 
                    that.dialogFormVisible = false;
                    that.$message({
                        showClose: true,
                        message: data.message,
                        type: 'success'
                    });
                    that.loadList();
                    // 跳转到列表页
                })
                .catch(function(error) {
                    stack_error(error);
                });

                } else {
                    console.log('error submit!!')
                    return false
                }

            });
        },
        validateClassName (name,callback) 
        {
            var url = 'class/checkClassName',that = this;
            let id = that.ClassForm.id ? that.ClassForm.id : 0;
            this.$http({
                method :"GET",
                url : url,
                params : {
                    name : name,
                    id   : id,
                }
            })
            .then(function(response) {
                var responseJson = response.data;
                var data = responseJson.data;
                callback(data.status);
            })
            .catch(function(error) {
                console.log('check name error')
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

      loadList() {
        let url = 'class',that =this;
        var search_query = parseSearchParam(that.searchQuery);
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
            console.log(data.data.data);
            that.tableData = data.data.data;
            
          })
          .catch(function(error) {
            that.listLoading = false;
            stack_error(error);
          });

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

      handleDelete(id) {
        var that = this;
        swal({
              title: "确定要删除?",
              text: '角色删除后不可恢复，确定要删除角色吗？',
              type: "warning",
              showCancelButton: true,
              cancelButtonText: "取消",
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "删除",
              closeOnConfirm: true
            }).then(function () {
              that.listLoading = false
              var url =  'role' +'/'+id;
              that.$http({
                method :'DELETE',
                url : url,
              })
              .then(function(response) {
                that.listLoading = false
                let {data} = response;
                that.$message({
                    showClose: true,
                    message: data.message,
                    type: 'success'
                });
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
                        
       
      },

      handleUpdate(row) {
      this.ClassForm = Object.assign({}, row)
      this.dialogTitle = '更新班级';
      this.dialogFormVisible = true

    },


    }
}

</script>

<style rel="stylesheet/scss" lang="scss">
 @import "resources/assets/styles/index";
</style>

<style>
    .auto_hidden {
        overflow:hidden; 
        text-overflow:ellipsis;
        white-space: nowrap;
        width:160px;
    }
    .remark_content {
        max-width: 300px;
    }
</style>