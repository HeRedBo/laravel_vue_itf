<template>
        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <el-form ref="userForm"  :model="userForm" :rules="userRules"  label-width="80px" class="el-form"
                      style="width: 41%;
                      margin-left: 10%;
                      margin-top: 20px;"
                    >
                        <el-form-item label="账号" prop="username">
                          <el-input v-model="userForm.username" placeholder="请输入账号"></el-input>
                        </el-form-item>
                        

                        <el-form-item label="密码" prop="password">
                                <el-input  type="password" v-model="userForm.password" placeholder="密码"></el-input>
                        </el-form-item>

                        <el-form-item label="姓名" prop="name">
                                <el-input v-model="userForm.name" placeholder="姓名"></el-input>
                        </el-form-item>

                        <el-form-item label="手机号码" prop="phone">
                                <el-input type="number" v-model="userForm.phone" placeholder="手机号码"></el-input>
                        </el-form-item>
                        
                        <el-form-item label="邮箱" prop="email">
                            <el-input v-model="userForm.email" placeholder="邮箱"></el-input>
                        </el-form-item>

                      <el-form-item label="角色">
                          <el-select v-model="userForm.roles" multiple placeholder="请选择用户角色" style="width:100%" >
                            <el-option
                              v-for="item in roleOptions"
                              :key="item.value"
                              :label="item.label"
                              :value="item.value">
                            </el-option>
                          </el-select>
                      </el-form-item> 

                      <el-form-item label="道馆">
                            <el-select v-model="userForm.venues" multiple placeholder="请选择道馆" style="width:100%" >
                              <el-option
                                v-for="item in venueOptions"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                              </el-option>
                            </el-select>
                        </el-form-item>
              
            
                      <!-- 头像 -->
                      <el-form-item label="头像">
                        <div class="components-container">
                          <pan-thumb :image="image">
                          </pan-thumb>
                          <el-button type="primary" icon="upload" style="position: absolute;bottom: 15px;margin-left: 40px;" @click="imagecropperShow=true">修改头像
                          </el-button>
                          <image-cropper :width="300" :height="300" url="/upload/upAvatar" @close='close' @crop-upload-fail='cropUploadFail' @crop-upload-success="cropSuccess" :key="imagecropperKey" v-show="imagecropperShow"></image-cropper>
                      </div>
                      </el-form-item>

                      <el-form-item>
                        <el-button type="primary" @click="onSubmit"> {{ userForm.id ? '更新' : '立即创建' }}</el-button>
                        <router-link :to="{path:'/admin/user/index'}"> 
                       
                            <el-button>取消</el-button>
                    </router-link>
                        
                      </el-form-item>

                    </el-form>
      
                </div>
      
            </div>
          </div>
        </div>
      </template>
      
      <script>
      
      import PanThumb from 'components/PanThumb';
      import ImageCropper from 'components/ImageCropper';
      import {stack_error,isMobile} from 'config/helper';
      export default 
      {
          components: { PanThumb ,ImageCropper},
          name: 'Form',
          props: {
            userForm: {
                type: Object,
                default() {
                    return {}
                }
            }
          },
      
          data() 
          {
            const validateUsername = (rule, value, callback) => {
               this.checkUserName(value, function(status) {
                 if(status == 1)
                 {
                    callback(new Error('账号已存在'))
                 } else {
                    callback()
                 }
               });
            },
            validatePhone = (rule, value, callback) => {
                if(isMobile(value)){
                    callback();
                } else {
                    callback(new Error('手机格式输入有误'));
                }
            },
            validatePass = (rule, value, callback) => {
                if(!this.userForm.id) {
                    if(value == ''){
                        callback(new Error('请输入密码'));
                    } else if(value.length < 2 || value.length > 50) {
                        callback(new Error('长度在 6 到 50 个字符'));
                    }
                }
                callback();
            };
            return {
              roleOptions :  [],
              venueOptions: [],
              userRules: {
                username: [
                    { required: true, message: '请输入账号', trigger: 'blur'},
                    { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                    { validator: validateUsername, trigger: 'blur' }
                  ],
                   name: [
                        { required: true, message: '请输入姓名', trigger: 'blur'},
                        { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                    ],
                    phone: [
                        { required: true, message: '请输入手机号', trigger: 'blur'},
                        { min: 11, max: 11, message:'手机号码格式有误', trigger: 'blur' },
                        { validator: validatePhone, trigger: 'blur' }
                    ],
                    password: [
                        { validator: validatePass, trigger: 'blur' }
                    ],
              },
              imagecropperShow: false,
              imagecropperKey: 0,
              image: 'http://owu2vcxbh.bkt.clouddn.com/files/avatar/default.png', 
            }
          },

          watch : {
            userForm() {
              // console.log(this.image);
              if(this.userForm.id)
               {
                 this.image = this.userForm.picture;               }
            }
          },

          created() {
            this.getRole();
            this.getVenues();
          },
         
         
          methods: {
            onSubmit() 
            {
               
               this.$refs.userForm.validate(valid => {
                  var that = this;
                  if (valid) {
                    let url = '/user' + (this.userForm.id ? '/' + this.userForm.id : '')
                    let method = this.userForm.id ? 'put' : 'post';
                    this.$http({
                      method :method,
                      url : url,
                      data : that.userForm
                    })
                    .then(function(response) {
                        
                        var {data} = response; 
                        that.$message({
                          showClose: true,
                          message: data.message,
                          type: 'success'
                        });
                      // 跳转到列表页
                      that.$router.push({ path: '/admin/user/index' })
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

            changePasswordRule() {
                if(!this.userForm.id) {
                    this.userRules.password =  [
                      { required: true, message: '请输入密码', trigger: 'blur'},
                      { min: 6, max: 50, message: '长度在 6 到 50 个字符', trigger: 'blur' },
                    ]
                } else { 
                    delete  this.userRules.password; 
                }
            },
      
            cropSuccess(resData) {
              this.imagecropperShow = false;
              this.imagecropperKey = this.imagecropperKey + 1;
              this.image = resData.data.url;
              this.userForm.picture = resData.data.real_path;
            },
      
            close() {
              this.imagecropperShow = false
            },
      
            cropUploadFail(error,field, ki)
            {
              stack_error(error);
            },
      
            getRole() {
                var url = '/user/role', that = this;
                this.$http({
                   method :"GET",
                   url : url,
                })
                .then(function(response) {
                  var responseJson = response.data,data = responseJson.data
                  var options = [];
                  for (var i in data ) {
                    let label =  data[i].name;
                    options.push({value : data[i].id , label: label});
                  }	

                  that.roleOptions = options;
                })
                .catch(function(error) {
                  stack_error(error);
                }); 
            },

            getVenues() {
                var url = '/user/venues', that = this;
                this.$http({
                   method :"GET",
                   url : url,
                })
                .then(function(response) {
                  var responseJson = response.data,data = responseJson.data
                  var options = [];
                  for (var i in data ) {
                    let label =  data[i].name;
                    options.push({value : data[i].id , label: label});
                  }	
                  that.venueOptions = options;
                })
                .catch(function(error) {
                  stack_error(error);
                }); 
            },
            checkUserName(name,callback) 
            {   
                var url = 'user/checkUserName',that = this;
                let id = that.userForm.id ? that.userForm.id : 0;
                this.$http({
                   method :"GET",
                   url : url,
                   params : {
                     username : name,
                     id   : id,
                   }
                })
                .then(function(response) {
                  var responseJson = response.data;
                  var data = responseJson.data;
                  callback(data.status)
                })
                .catch(function(error) {
                  console.log('check name error')
                  stack_error(error);
                }); 
            },
          }
        }
      </script>
      
      
      <style rel="stylesheet/scss" lang="scss" scoped>
      @import "resources/assets/styles/mixin";
      </style>