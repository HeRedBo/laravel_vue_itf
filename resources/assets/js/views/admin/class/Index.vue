<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                 <div class="box-header">
                    <div class="row">
                        <div class="col-md-2">
                          <span>
                            <button v-show="showCreateButton"  @click="handleCreate" type="button" class="btn btn-sm btn-success">添加班级</button>
                         </span>
                        </div>
                        <div class="col-md-10">
                           <div class="form-inline pull-right">
                           <!--  数据搜索框 -->
                           <div class="input-group input-group-sm" >
                                <el-input 
                                    placeholder="请输入班级名称" 
                                    v-model="params.name"
                                    size="small"
                                >
                                </el-input>
                           </div>
                           <!--  按钮分组 -->
                            <div class="btn-group btn-group-sm">
                                <button type="submit" class="btn btn-primary" @click="$refs.table.loadList()"><i class="fa fa-search"></i>
                                </button>
                                <a href="javascript:void(0)" class="btn btn-warning" @click="reset"><i class="fa fa-undo"></i></a>
                            </div>
                        </div>
                        </div>
                    </div>
                        
                       

                 </div>

                 <vTable ref="table"
                        stripped
                        hover
                        :searchType=2
                        :ajax_url="ajax_url"
                        :params="params"
                        :items="items"
                        :fields="fields"
                        :current-page="currentPage"
                        :per-page="perPage"
                        :del="del"
                >
                 <!-- 道馆名称 -->
                     <template slot="venue_name" slot-scope="item">
                        <span>{{item.item.venues.name}}</span>
                    </template>
                    
                     <template slot="remark" slot-scope="item">
                        <!-- <div class="remark_content" slot="content">
                            <p>
                                    {{scope.row.remark}}
                            </p>
                      </div>  -->
                        <div class="auto_hidden inline-block" data-toggle="tooltip" data-placement="top" :title="item.item.remark">
                            {{item.item.remark}}
                        </div>
                        <!-- <span class="auto_hidden" >     
                        </span> -->

                    </template>
                    

                     <!-- 操作 -->
                    <template slot="actions" slot-scope="item">
                        <div class="btn-group">
                           <!--  <a href="javascript:;" @click="view(item.item)" class="btn btn-success btn-xs">查看</a> -->
                            <!-- v-show="item.item.status == 0"  -->
                           <!--  <button class="btn bg-orange btn-xs" @click="handleUpdate(scope.row)">编辑</button> -->
                            <button class="btn bg-orange btn-xs" @click="handleUpdate(item.item)">编辑</button>
                            <!-- <router-link target="_blank"  :to="{path:'logger/'+ item.item.id}" class="btn bg-info btn-xs">操作日志</router-link>
                             -->
                            <!-- <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a> -->
                        </div>
                    </template>
                </vTable>
            </div>
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
                              <el-button type="primary" @click="handleClass" :loading="buttonLoading">确 定</el-button>
                            </div>
                          </el-dialog>
    </div>
</template>

<script>

$(function() {
    $('.box').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
});


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
            items: [],
            fields : {
                id: {label: 'ID', sortable: true},
                venue_name: {label: '道馆', need: 'venues'},
                name: {label: '课程名称'},
                remark: {label: '课程课程备注'},
                created_at:{label:'创建时间', sortable: true},
                updated_at:{label:'更新时间', sortable: true},
                username : {label:'最新操作人', need:'operator'},
                actions : {label: '操作'}
            },
            ajax_url: "/class",
            params: {},
            currentPage: 1,
            perPage: 15,
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
            venueOptions : [],
            del: {url:'/admin/user',title:'确定要删除用户吗?',successText:'删除后台用户成功!'},
            buttonLoading: false,
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
                let method = this.ClassForm.id ? 'put' : 'post';
                that.buttonLoading = true;
                this.$http({
                    method :method,
                    url : url,
                    data : that.ClassForm
                })
                .then(function(response) {
                    var {data} = response; 
                    that.dialogFormVisible = false;
                    that.buttonLoading = false;
                    that.$message({
                        showClose: true,
                        message: data.message,
                        type: 'success'
                    });
                    that.$refs.table.loadList();
                    // 跳转到列表页
                })
                .catch(function(error) {
                    that.buttonLoading = false;
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

                that.$refs.table.loadList();
              
                
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
    reset() 
    {
            this.params = {};
    },
    
    }
}
</script>

<style>
    .auto_hidden {
        overflow:hidden; 
        text-overflow:ellipsis;
        white-space: nowrap;
        width:160px;
    }
    .inline-block {
        display: inline-block;
    }
    .remark_content {
        max-width: 300px;
    }
    .el-tooltip__popper{
        max-width: 500px;
    }
</style>