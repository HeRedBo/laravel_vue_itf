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
                            stripe
                            fit highlight-current-row
                            style="width: 100%"
                            :default-sort = "{prop: 'id', order:'descending'}"
                            @sort-change="sortChange"
                            v-loading="listLoading"
                            element-loading-text="拼命加载中"
                        >

                        <el-table-column type="expand">
                            <template slot-scope="props">
                              <el-form label-position="left" inline class="demo-table-expand">
                               
                                <el-form-item label="卡券ID">
                                    <span>{{ props.row.id }}</span>
                                  </el-form-item>
                                <el-form-item label="卡券名称">
                                  <span>{{ props.row.name }}</span>
                                </el-form-item>

                                <el-form-item label="计算数量">
                                  <span>{{ props.row.number}}</span>
                                </el-form-item>

                                <el-form-item label="计算单位">
                                  <span>{{ props.row.unit_str}}</span>
                                </el-form-item>
                                <el-form-item label="道馆">
                                    <span>{{ props.row.venues.name }}</span>
                                  </el-form-item>

                                  
                                <el-form-item label="卡券价格">
                                  <span>{{ props.row.card_price}}</span>
                                </el-form-item>

                                <el-form-item label="启用状态">
                                    <el-tooltip class="item" effect="dark" :content="props.row.status==1 ? '启用':'未启用'" placement="top">
                                        <i :class="['fa','fa-circle',props.row.status==1?'text-success':'text-danger']"></i>
                                    </el-tooltip>
                                </el-form-item>

                                <el-form-item label="创建时间">
                                    <span>{{ props.row.created_at }}</span>
                                </el-form-item>

                                <el-form-item label="最近更新时间">
                                    <span>{{ props.row.updated_at }}</span>
                                </el-form-item>

                                <el-form-item label="操作人">
                                    <span>{{ props.row.operator.name }}</span>
                                </el-form-item>
                              </el-form>
                            </template>
                          </el-table-column>

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
                            label="卡券名称"
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
                            prop="number"
                            label="计算数量"
                         >
                        </el-table-column>


                        <el-table-column
                            label="计算单位"
                        >
                        <template  slot-scope="scope"> 
                            <span> {{scope.row.unit_str}} </span>
                        </template>
                        </el-table-column>

                       
                        <el-table-column
                        prop="card_price"
                        label="卡券价格"
                        >
                        </el-table-column>
                       
                        <el-table-column
                            label="启用状态"
                        >

                            <template  slot-scope="scope"> 
                                <el-tooltip class="item" effect="dark" :content="scope.row.status==1 ? '启用':'未启用'" placement="top">
                                    <i @click="changeStatus(scope.row,scope.$index)" :class="['fa','fa-circle',scope.row.status==1?'text-success':'text-danger']"></i>
                                </el-tooltip>
                            </template>
                        </el-table-column>

                       
                        <!-- <el-table-column
                            label="卡券说明"
                        >
                            <template  slot-scope="scope">
                                    <el-tooltip  placement="right" >
                                            <div class="remark_content" slot="content">
                                                    <p>
                                                            {{scope.row.explain}}
                                                    </p>
                                                </div>
                                        <span class="auto_hidden">{{scope.row.explain}}</span>
                                    </el-tooltip>
                            </template>
                        </el-table-column> -->

                        
                    
                        <el-table-column
                            prop="created_at"
                            label="创建时间"
                            sortable="custom"
                        >
                        </el-table-column>


                        <!-- <el-table-column
                        prop="updated_at"
                        label="更新时间"
                        sortable="custom"
                    
                        >
                        </el-table-column> -->

                        <el-table-column
                        
                          label="操作人"
                      >
                      <template  slot-scope="scope"> 
                          <span> {{scope.row.operator.name}} </span>
                      </template>
                      </el-table-column>
                    
                        <el-table-column label="操作">
                        <template slot-scope="scope">
                            <div class="btn-group">
                                <button class="btn bg-orange btn-xs" @click="handleUpdate(scope.row)">编辑</button>
                                <!-- <a @click="handleDelete(scope.row.id)" class="btn btn-danger btn-xs">删除</a> -->
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
                    
                        <el-dialog :title="dialogTitle" :visible.sync="dialogFormVisible" style="z-index:120;" >
                    
                            <el-form class="small-space" 
                                ref="CardForm" 
                                :model="CardForm"
                                :rules="RoleRules"
                                label-position="right"
                                label-width="80px"
                                style='width: 400px; margin-left:50px;'
                            >
                              <el-form-item label="卡券名称" prop="name">
                                <el-input v-model="CardForm.name" auto-complete="off" ></el-input>
                              </el-form-item>
                              
                              <el-form-item label="道馆" v-show="selectItemVisible" prop="venue_id" >
                                <el-select v-model="CardForm.venue_id" placeholder="请选择道馆" style="width:100%" >
                                  <el-option
                                     v-for="item in venueOptions"
                                     :key="item.value"
                                     :label="item.label"
                                     :value="item.value">
                                  </el-option>
                                </el-select>
                             </el-form-item>

                             <el-form-item label="计算单位" prop="unit" inline>
                                    <el-select v-model="CardForm.unit" placeholder="单位" >
                                      <el-option
                                         v-for="item in unitOptions"
                                         :key="item.value"
                                         :label="item.label"
                                         :value="item.value">
                                      </el-option>
                                    </el-select>
                                 </el-form-item>
                             <el-form-item label="数量" prop="number">
                                <el-input-number v-model="CardForm.number" ></el-input-number>
                              
                            </el-form-item>

                            <el-form-item label="价格" prop="card_price">
                                    <el-input-number v-model="CardForm.card_price" ></el-input-number>
                                    
                                  </el-form-item>
                            <el-form-item label="启用状态">
                                    <el-switch on-value="1" off-value="0" on-text="" off-text="" v-model="CardForm.status"></el-switch>
                            </el-form-item>

                              <el-form-item label="卡券说明">
                                <el-input type="textarea" :autosize="{ minRows: 3, maxRows: 6}" v-model="CardForm.explain"></el-input>
                              </el-form-item>
                            </el-form>
                            <div slot="footer" class="dialog-footer">
                              <el-button @click="dialogFormVisible = false">取 消</el-button>
                              <el-button type="primary" @click="handleCard">确 定</el-button>
                            </div>
                          </el-dialog>
                          
                      </div>
    </div>


</template>

<script>
import {stack_error,parseSearchParam} from 'config/helper';
export default {
    data() {
        const validateCardName = (rule, value, callback) => {
          this.validateCardName(value, function(status) {
           if(status == 1)
           {
                callback(new Error('卡券名称已存在'))
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
            dialogTitle : '创建卡券',
            CardForm : {
                name : '',
                venue_id : '',
                number: 0,
                unit : '',
                card_price : '',
                explain : '',
                status : '',
                
            },

            RoleRules: {
                name: [
                    { required: true, message: '请输入班级名称', trigger: 'blur'},
                    { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                    { validator: validateCardName, trigger: 'blur' }
                ],
                number:[
                    { required: true, message: '数量不能为空'},
                    { type: 'number', message: '数量必须为数字值'}
                ],
                unit : [
                    { required: true, message: '计算单位不能为空'}
                ],
                venue_id : [
                    { required: true, message: '归属道馆不能为空'}
                ],
                card_price : [
                    { required: true, message: '卡券价格不能为空'}
                ]
            },
            venueOptions : [],
            unitOptions : [
                {label : '天', value : 'day'},
                {label : '月', value : 'mouth'},
                {label : '年', value : 'year'},
            ]
        }
    },
    created() {
        this.getUserVenus();
       
    },
    methods : {
        handleCreate () {
            var venue_id = this.CardForm.venue_id;
            this.CardForm = {};
            if(venue_id)
            {
                this.CardForm.venue_id = venue_id;
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
                    that.CardForm.venue_id =  options[0].value;
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


        handleCard() {
            this.$refs.CardForm.validate(valid => {
            var that = this;
            if (valid) 
            {
                if(this.CardForm.status ==1) 
                {
                    swal({
                        title: "确定要" + this.dialogTitle + "?",
                        text: '卡券一经启用无法之后无法修改 你是否要继续执行该操作？',
                        type: "warning",
                        showCancelButton: true,
                        cancelButtonText: "取消",
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "确认",
                        closeOnConfirm: true
                    }).then(function () {
                        that.saveCard();
                    
                    },function(dismiss) {
                        if (dismiss === 'cancel') {
                            that.CardForm.status = 0;
                        }
                    }
                )
                   
                    
                    
                }
                else 
                {
                    that.saveCard();
                }
                
            } 
            else 
            {
                    console.log('error submit!!')
                    return false
            }

            });
        },
        saveCard() {
            
                let url = '/card' + (this.CardForm.id ? '/' + this.CardForm.id : ''), that = this;
                let method = this.CardForm.id ? 'put' : 'post'
                this.$http({
                    method :method,
                    url : url,
                    data : that.CardForm
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

        },
        validateCardName (name,callback) 
        {
            var url = 'card/checkCardName',that = this;
            let id = that.CardForm.id ? that.CardForm.id : 0;
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
        let url = 'card',that =this;
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

        if(val.order ==  'descending')
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
        this.CardForm = Object.assign({}, row)
        this.dialogTitle = '更新班级';
        this.dialogFormVisible = true

     },
     changeStatus(row,index) {
       
        var status = row.status;
        var id = row.id;
        if(status == 0) {
            var that = this;
            swal({
            title: '是否开启卡券?',
            text: '卡券一经启用无法之后无法修改 你是否要继续执行该操作!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '确认',
            cancelButtonText: '取消'
            }).then(function() {

              var url =  '/card/changeStatus',changeStatus = 1;
              that.$http({
                method :'POST',
                url : url,
                data : {
                    id : id,
                    status : changeStatus
                }
              })
              .then(function(response) {
                let {data} = response;
                that.tableData[index].status = changeStatus;
                
                that.$message({
                    showClose: true,
                    message: data.message,
                    type: 'success'
                });
            })
            .catch(function(error) {
                stack_error(error);
            });
            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                that.$message({
                    showClose: true,
                    message: '你已取消修改数据状态',
                });
                // if (dismiss === 'cancel') {
                //     swal(
                //     'Cancelled',
                //     'Your imaginary file is safe :)',
                //     'error'
                //     )
                // }
            })
        }
        
     }


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
    .v-modal {
        z-index: 200 !important;
    }
    .el-dialog__wrapper{
        z-index: 250 !important;
    }


.demo-table-expand {
    font-size: 0;
  }
  .demo-table-expand label {
    width: 90px;
    color: #99a9bf;
  }
  .demo-table-expand .el-form-item {
    margin-right: 0;
    margin-bottom: 0;
    width: 50%;
  }
</style>