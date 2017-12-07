<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="input-group input-group-sm" >
                                <a href="#" @click="handleCreate" class="btn btn-sm btn-success">角色添加</a>
                            </div>
                        </div>

                        <div class="col-md-10">
                            <div class="form-inline pull-right">
                                 <!--  数据搜索框 -->
                               <div class="input-group input-group-sm" >
                                    <el-input 
                                        placeholder="角色名称" 
                                        v-model="params.name"
                                        size="small"
                                    >
                                    </el-input>
                                </div>

                                 <!--  按钮分组 -->
                                <div class="btn-group btn-group-sm">
                                    <button type="submit" class="btn btn-primary" @click="$refs.table.loadList()"><i class="fa fa-search"></i>
                                    </button>
                               <!--  <a href="javascript:void(0)" class="btn btn-warning" @click="reset"><i class="fa fa-undo"></i></a> -->
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
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :del="del"
                > 
                <!-- 操作 -->
                <template slot="actions" slot-scope="item">
                    <div class="btn-group">
                        <router-link :to="{path:'setacl/'+ item.item.id}" class="btn bg-purple btn-xs">设置权限</router-link>
                        <button class="btn bg-orange btn-xs" @click="handleUpdate(item.item)">编辑</button>
                        <a href="javascript:void(0)"  @click="$refs.table.onDel(item.item.id)" class="btn btn-danger btn-xs">删除</a>
                    </div>
                </template>
                </vTable>
            </div>
        </div>

        <el-dialog :title="dialogTitle" :visible.sync="dialogFormVisible" >
            <el-form class="small-space" 
                ref="RoleForm" 
                :model="RoleForm"
                :rules="RoleRules"
                label-position="right"
                label-width="80px"
                style='width: 400px; margin-left:50px;'
            >

                <el-form-item label="角色名称" prop="name">
                    <el-input v-model="RoleForm.name" auto-complete="off" ></el-input>
                </el-form-item>
                    
                <el-form-item label="角色描述">
                    <el-input type="textarea" :autosize="{ minRows: 3, maxRows: 6}" v-model="RoleForm.description"></el-input>
                </el-form-item>
                    
            </el-form>
            <div slot="footer" class="dialog-footer">
              <el-button @click="dialogFormVisible = false">取 消</el-button>
              <el-button type="primary" @click="handleRole" :loading="buttonLoading">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import {stack_error} from 'config/helper';
export default {
    data() {
        const validateRolename = (rule, value, callback) => {
            this.checkRoleName(value, function(status) {
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
            fields: {
                id: {label: 'ID', sortable: true},
                name: {label: '角色名称'},
                description: {label: '角色描述'},
                created_at:{label:'创建时间', sortable: true},
                updated_at:{label:'更新时间', sortable: true},
                actions : {label: '操作'}
            },
            ajax_url: "/role",
            params : {},
            currentPage: 1,
            perPage: 15,
            del: {url:'/role',title:'角色删除后不可恢复，确定要删除角色吗?'},
            listLoading: true,
            dialogFormVisible : false,
            buttonLoading: false,
            dialogTitle : '创建角色',
            RoleForm : {
                name : '',
                description : ''
            },

            RoleRules: {
                name: [
                    { required: true, message: '请输入角色名称', trigger: 'blur'},
                    { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                    { validator: validateRolename, trigger: 'blur' }
                ],
            },
        }
    },
    methods : {
        handleCreate () {
            this.dialogFormVisible = true;
        },

        handleRole() {
            this.$refs.RoleForm.validate(valid => {
            var that = this;
            if (valid) {
                let url = '/role' + (this.RoleForm.id ? '/' + this.RoleForm.id : '')
                let method = this.RoleForm.id ? 'put' : 'post';
                that.buttonLoading = true;
                this.$http({
                    method :method,
                    url : url,
                    data : that.RoleForm
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
        checkRoleName (name,callback) {
            var url = 'role/checkName',that = this;
            let id = that.RoleForm.id ? that.RoleForm.id : 0;
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

      handleUpdate(row) {
        this.RoleForm = Object.assign({}, row)
        this.dialogTitle = '编辑角色'
        this.dialogFormVisible = true
      },

      reset() 
      {
        this.params = {};
      },


    }
}

</script>

<style rel="stylesheet/scss" lang="scss">
 @import "resources/assets/styles/index";
</style>
