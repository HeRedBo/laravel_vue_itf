<template>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">快速添加</h3>
                    </div>
    
                    <div class="box-body">
                            <el-form ref="permissionForm"  
                                :model="permissionForm"   
                                label-width="100px" 
                                class="el-form"
                                :rules="permissionRules"
                                style='width: 85%; margin-left:50px; margin-top:20px'
                        >
                                <el-form-item label="父级">
                                    <el-select filterable  v-model="permissionForm.parent_id" placeholder="请选择">
                                      <el-option
                                        v-for="item in parentOptions"
                                            :key="item.value"
                                            :label="item.label"
                                            :value="item.value"
                                        >
                                      </el-option>
                                    </el-select>
                                </el-form-item>
                                
                            
                                <el-form-item label="权限名称" prop="display_name">
                                      <el-input v-model="permissionForm.display_name"></el-input>
                                </el-form-item>
                      
                                <el-form-item label="图标">
                                      <el-input v-model="permissionForm.icon" ></el-input>
                                </el-form-item>
                      
                                <el-form-item label="权限别名" prop="name">
                                      <el-input v-model="permissionForm.name" ></el-input>
                                </el-form-item>
                      
                                <el-form-item label="排序">
                                      <el-input v-model="permissionForm.order_num" ></el-input>
                                </el-form-item>
                      
                                <el-form-item label="左侧是否显示">
                                      <el-checkbox v-model="permissionForm.is_show"></el-checkbox>
                                </el-form-item>
                                <el-form-item>
                                  <el-button type="primary" @click="onSubmit" > {{ permissionForm.id ? '更新' : '立即创建' }}</el-button>
                                </el-form-item>
                              </el-form>
                      
                    </div>
                </div>
            </div>
    
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">权限树</h3>
                    </div>
    
                    <div id="treeAcl"></div>
                </div>
            </div>
        </div>
    </template>
    
<script>

require('jstree/dist/themes/default/style.min.css');
import {stack_error} from 'config/helper';


export default {
       
        data() {
            return {
                permissionForm : {
                    id : null,
                    display_name : '',
                    icon : '',
                    name : '',
                    parent_id : 0,
                    order_num : 0,
                    is_show : true
                },
                parentOptions : [],  
                treeData:null,
                treeDom : "#treeAcl",
                permissionRules: {
                    display_name: [
                        { required: true, message: '请输入权限名称', trigger: 'blur'},
                        { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                    ],
                    name: [
                        { required: true, message: '请输入权限别名', trigger: 'blur'},
                        { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                    ],
                    method: [
                        { required: true, message: '请选择请求方式', trigger: 'blur'},
                       
                    ],
                }
            }
        },
            watch : {
                treeData() {
                    $.jstree.reference(this.treeDom).settings.core.data = this.treeData;
                    $.jstree.reference(this.treeDom).refresh();
                }
            },
            mounted() {
                this.loadList();
            },
            methods : {
                onSubmit() {
                    this.$refs.permissionForm.validate(valid => {
                        var that = this;
                        if (valid) {
                            let url = '/permission' + (this.permissionForm.id ? '/' + this.permissionForm.id : '')
                            let method = this.permissionForm.id ? 'put' : 'post'
                            this.$http({
                                method :method,
                                url : url,
                                data : that.permissionForm
                            })
                            .then(function(response) {
                                var {data} = response; 
                                that.$message({
                                    showClose: true,
                                    message: data.message,
                                    type: 'success'
                                });
                                that.loadList();
                                that.permissionForm  = {};
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
                loadList: function() {
                    var url = '/permission', that = this;
                    this.$http({
                        method :'GET',
                        url : url,
                    })
                    .then(function(response) {
                        let {data} = response;
                        let responseData = data.data;
                        that.parentOptions = responseData.select;
                        that.treeData = responseData.tree;
					    that.initTree();                
                    })
                    .catch(function(error) {
                        stack_error(error);
                    });

                },
                initTree : function() {
                    var that = this , treeData = this.treeData;
                    $(this.treeDom).jstree({
                        'core': {
                            'check_callback': true,
                            'themes' : {
                                "theme" : 'default',
                            },
                            'data':treeData
                        },
                        "contextmenu" : {
                            "items" : function(node) {
                                return {
                                    "create" : {
                                        "separator_before":false,
                                        "separator_after": false,
                                        "label" : "添加子权限",
                                        "action": function(obj) {
                                            that.addUI(node.id, node.next)
                                        }
                                    },
                                    "edit" : {
                                        "separator_before" : false,
                                        "separator_after": false,
                                        "label" : "编辑",
                                        "action" : function(obj) {
                                            that.edtiUI(node.id)
                                        }
                                    },
                                    "delete": {
                                        "separator_before" : false,
                                        "separator_after": false,
                                        "label" : "删除",
                                        "action" : function(obj) {
                                            that.del(node.id)
                                        }
                                    }
                                }
                            }
                        },
                        "plugins": ["themes","types","contextmenu"]
                    });
                },
                addUI: function(id, next) {
                    var that = this;
                    //var $refs = this.$refs;
                    that.permissionForm = {
                    };
                    this.permissionForm.parent_id = id;
                },
                edtiUI : function(id) {
                    var url = '/permission/edit', that = this, $refs = this.$refs;
                    this.$http({
                        method :'GET',
                        url : url,
                        params : {
                            id : id
                        }
                    })
                    .then(function(response) {
                        let {data} = response;
                        let responseData = data.data;
                        that.permissionForm = responseData;
                    })
                    .catch(function(error) {
                        stack_error(error);
                    });
                   
                },
                del : function(id) {
                    var url = '/permission/' + id, that = this;
                    this.$http({
                        method :'DELETE',
                        url : url,
                    })
                    .then(function(response) {
                        let {data} = response;
                        that.$message({
                            showClose: true,
                            message: data.message,
                             type: 'success'
                        });
                        that.loadList();
                    })
                    .catch(function(error) {
                        stack_error(error);
                    });
                }
            }
        }
    </script>