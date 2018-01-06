<template>
        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <el-form ref="studentForm"  :model="studentForm" :rules="studentRules"  label-width="130px" class="el-form"
                      style="width: 70%;
                      margin-left: 5%;
                      margin-top: 20px;"
                    >
                        <!-- 姓名 -->
                        <el-form-item label="学生姓名" prop="name">
                          <el-input v-model="studentForm.name" placeholder="请输入姓名"></el-input>
                        </el-form-item>
            
                        <!-- 性别 -->
                        <el-form-item label="性别" prop="sex">
                            <el-radio-group v-model="studentForm.sex">
                                <el-radio :label="0">女</el-radio>
                                <el-radio :label="1">男</el-radio>
                            </el-radio-group>
                        </el-form-item>

                        <!-- 头像 -->
                      <el-form-item label="头像">
                            <div class="components-container">
                              <pan-thumb :image="image">
                              </pan-thumb>
                              <el-button type="primary" icon="upload" style="position: absolute;bottom: 15px;margin-left: 40px;" @click="imagecropperShow=true">修改头像
                              </el-button>
                              <image-cropper :width="1000" :height="1000" url="/upload/upAvatar" @close='close' @crop-upload-fail='cropUploadFail' @crop-upload-success="cropSuccess" :key="imagecropperKey" v-show="imagecropperShow"></image-cropper>
                          </div>
                        </el-form-item>
                        
                        <!-- 籍贯 -->
                        <el-form-item label="籍贯">
                            <el-input v-model="studentForm.native_place" placeholder="请输入籍贯"></el-input>
                        </el-form-item>
                        
                        <!-- 出生日期 -->
                        <el-form-item label="出生日期" prop="birthday">
                            <el-date-picker
                                v-model="studentForm.birthday"
                                type="date"
                                format="yyyy-MM-dd"
                                placeholder="选择出生日期"
                                default-value="">
                             </el-date-picker>
                        </el-form-item>

                        <!-- 身份证 -->
                        <el-form-item label="身份证" >
                            <el-input v-model="studentForm.id_card" placeholder="请输入身份证"></el-input>
                        </el-form-item>

                        <!-- 区域 -->
                        <el-form-item label="区域">
                            <v-distpicker  @selected="onSelected"  :province="studentForm.province" :city="studentForm.city" :area="studentForm.area" ></v-distpicker>
                        </el-form-item>

                        <!-- 详细地址 -->
                        <el-form-item label="详细地址">
                            <el-input type="textarea" v-model="studentForm.address"></el-input>
                        </el-form-item>
                        
                        <!-- 学校 -->
                        <el-form-item label="学校" >
                                <el-input v-model="studentForm.school" placeholder="请输入就读学校"></el-input>
                        </el-form-item>

                        <!-- 道馆 -->
                        <el-form-item label="道馆" v-show="selectItemVisible" prop="venue_id" >
                                <el-select v-model="studentForm.venue_id" placeholder="请选择道馆" style="width:100%" @change="venueChange">
                                  <el-option
                                     v-for="item in venueOptions"
                                     :key="item.value"
                                     :label="item.label"
                                     :value="item.value"
                                      
                                     >
                                  </el-option>
                                </el-select>
                        </el-form-item>

                        <!-- 班级 -->
                        <el-form-item label="班级" prop="class_id">
                            <el-select v-model="studentForm.class_id" multiple placeholder="请选择班级" style="width:100%" >
                              <el-option
                                v-for="item in classOptions"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                              </el-option>
                            </el-select>
                        </el-form-item>
                        
                        <!-- 报名时间 -->
                        <el-form-item label="报名时间" prop="sign_up_at" v-show="studentForm.id==0">
                            <el-date-picker
                                v-model="studentForm.sign_up_at"
                                type="datetime"
                                placeholder="选择报名时间"
                                format="yyyy-MM-dd HH:mm:ss"
                                >
                            </el-date-picker>
                        </el-form-item>
                        
                        <!-- 是否自动创建会员卡号 -->
                        <el-form-item label="自动创建会员卡号" v-if="studentForm.id==0">
                            <el-radio-group v-model="studentForm.auto_create_number">
                                <el-radio :label="0">否</el-radio>
                                <el-radio :label="1">是</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        
                         <!-- 学校 -->
                        <el-form-item label="会员卡号" 
                                      prop="card_number"  
                                      v-show="showCardNumber()"
                        >
                              <el-input v-model="studentForm.card_number" placeholder="请输入会员卡号"></el-input>
                        </el-form-item>

                        <!-- 卡券种类 -->
                        <el-form-item label="购卡类型" v-if="!studentForm.id">
                                <el-select 
                                    v-model="studentForm.card" 
                                    @change="selectCard"  
                                    placeholder="请选择需要购买的卡" 
                                    style="width:100%" 
                                    filterable
                                >
                                  <el-option
                                    v-for="item in cardOptions"
                                      :key="item.id"
                                      :label="item.name"
                                      :value="item.id">
                                  </el-option>
                                </el-select>
                        </el-form-item> 

                        <el-table
            					    :data="studentForm.user_cards"
            					    style="min-width: 650px;margin-bottom: 20px;"
            						  align="cneter"
                          v-if="!studentForm.id"
                        >
                            <el-table-column
                                prop="card_id"
                                label="卡券ID"
                                align="cneter"
                                width="120">
                            </el-table-column>

                            <el-table-column
                            prop="name"
                            label="卡券名称"
                            align="cneter"
                            width="120">
                            </el-table-column>
                            <el-table-column
                            prop="buy_number"
                            label="购买数量"
                            align="cneter"
                            >
                            </el-table-column>
                            <el-table-column
                            prop="card_price"
                            align="cneter"
                            label="卡券价格">
                            </el-table-column>
                            <el-table-column
                                prop="total_price"
                                align="cneter"
                                label="总金额">
                            </el-table-column>

                            <el-table-column
                              align="cneter"
                              label="启用状态">
                                <!-- 状态 -->
                                <template slot-scope="scope">
                                  <a href="javascript:void(0)"   @click="changeCardStatus(scope.$index)" data-toggle="tooltip" :title="scope.row.status==1 ?'启用':'未启用'">
                                      <i :class="['fa','fa-circle',scope.row.status==1?'text-success':'text-danger']"></i>
                                  </a>
                                </template>
                                  
                            </el-table-column>

                            

                            <el-table-column 
                                label="操作" 
                                width="120">
                            <template slot-scope="scope">
                                <el-button
                                v-show="scope.row.is_new==1"
                                size="small"
                                type="danger"
                                @click="deleteUserCard(scope.$index)">删除</el-button>
                            </template>
                            </el-table-column>
					     </el-table>


                  
                    <el-form-item label="联系人" prop="phone">
                        <el-button @click="showNewContactsForm"  type="primary" >添加联系人</el-button>
                    </el-form-item>

                     <el-table
                          :data="studentForm.user_contacts"
                          style="min-width: 650px;margin-bottom: 20px;"
                          align="cneter"
                        >
              
                            <el-table-column
                              prop="contact_name"
                              label="联系人姓名"
                              align="cneter"
                              width="120">
                            </el-table-column>

                            <el-table-column
                              prop="relation_name"
                              label="与本人关系"
                              align="cneter">
                            </el-table-column>

                            <el-table-column
                              prop="contact_phone"
                              label="联系人手机号"
                              align="cneter"
                            >
                            </el-table-column>

                            <el-table-column
                              prop="contact_email"
                              align="cneter"
                              label="联系人邮箱">
                            </el-table-column>


                            <el-table-column 
                                label="操作" 
                                width="120">
                            <template slot-scope="scope">
                                <el-button
                                v-show="scope.row.id==0"
                                size="small"
                                type="danger"
                                @click="deleteUserContact(scope.$index)">删除</el-button>
                            </template>
                            </el-table-column>
                      </el-table>

                      <el-form-item>
                        <el-button type="primary" @click="onSubmit"> {{ studentForm.id ? '更新' : '立即创建' }}</el-button>
                        <router-link :to="{path:'/admin/student/index'}"> 
                       
                            <el-button>取消</el-button>
                    </router-link>
                        
                      </el-form-item>

                    </el-form>
                
                   <!--  创建联系人form -->
                   <el-dialog title="新建联系人" :visible.sync="dialogContactsFormVisible" >
                    
                            <el-form class="small-space" 
                                ref="Contacts" 
                                :model="Contacts"
                                :rules="ContactsRules"
                                label-position="right"
                                label-width="120px"
                                style='width: 400px; margin-left:50px;'
                            >
                                <el-form-item label="与本人关系" prop="relation_id">
                                  <el-select v-model="Contacts.relation_id" placeholder="请选择关系" style="width:100%">
                                    <el-option
                                       v-for="item in relationOptions"
                                       :key="item.id"
                                       :label="item.name"
                                       :value="item.id">
                                    </el-option>
                                  </el-select>
                                </el-form-item>

                                <el-form-item label="联系人姓名" prop="contact_name">
                                  <el-input v-model="Contacts.contact_name" auto-complete="off" ></el-input>
                                </el-form-item>


                                <el-form-item label="联系人手机号"  prop="contact_phone">
                                  <el-input v-model="Contacts.contact_phone" auto-complete="off" ></el-input>
                                </el-form-item>

                               <el-form-item label="联系人邮箱" prop="contact_email">
                                <el-input v-model="Contacts.contact_email" auto-complete="off" ></el-input>
                              </el-form-item>
                            </el-form>
                            <div slot="footer" class="dialog-footer">
                              <el-button @click="dialogContactsFormVisible = false">取 消</el-button>
                              <el-button type="primary" @click="hanaleAddContacts">确 定</el-button>
                            </div>
                          </el-dialog>
                </div>
      
            </div>
          </div>
        </div>
      </template>
      
      <script>
      
      import PanThumb from 'components/Panthumb';
      import ImageCropper from 'components/ImageCropper';
      import {stack_error,isMobile,parseTime} from 'config/helper';
      import VDistpicker from 'v-distpicker'
      export default 
      {
          components: { PanThumb ,ImageCropper,VDistpicker},
          name: 'Form',
          props: {
            studentForm: {
                type: Object,
                default() {
                    return {}
                }
            },
            select : {
                type : Object,
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

            validateBirthday = (rule, value, callback) => {
                console.log(typeof value);
                callback();
            },
            validateCardNumber = (rule, value, callback) =>  {
                if (this.studentForm.id == 0 && this.studentForm.auto_create_number ==0) 
                {
                    if(!value) {
                      return callback(new Error('学生会员卡不能为空'));
                    }

                    var reg = new RegExp( /^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i);
                    if (!reg.test(value)) {
                        return callback(new Error('卡号只能是字母与数字组合'));
                    }

                    if(value.length < 8) {
                        return callback(new Error('卡号不能小于8位'));
                    }
                }
                callback();
                
            };

            return {
              roleOptions :  [],
              venueOptions: [],
              classOptions: [],
              userVenueClass:[],
              cardOptions : [],
              relationOptions : [],
              selectItemVisible : false,
              dialogContactsFormVisible: false,
              studentRules: {
                name: [
                    { required: true, message: '请输入学生姓名', trigger: 'blur'},
                    { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' }
                ],
                sex: [
                    { required: true, type: 'number', message: '请选择学生性别', trigger: 'blur'},
                ],
                birthday: [
                    {  type: 'date',required: true , message: '请选择学生生日', trigger: 'change'}
                ],
                venue_id: [
                    { required: true, type: 'number', message: '请选择学生归属道馆', trigger: 'blur'}
                ],
                class_id: [
                    { required: true, type: 'array', message: '请选择学生归属班级', trigger: 'blur' }
                ],

                sign_up_at : [
                    {  type: 'date', required: true,message: '请选择学生报名时间', trigger: 'change' }
                ],
                card_number :[
                  { validator: validateCardNumber, trigger: 'blur' }
                ],
              },
              ContactsRules : {
                relation_id: [
                    { required: true, message: '请选择联系人关系', trigger: 'blur'}
                  ],
                  contact_name: [
                    { required: true, message: '请输入联系人姓名', trigger: 'blur'},
                    { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                  ],
                  contact_phone : [
                    { required: true, message: '请输入联系人手机号码', trigger: 'blur'},
                    { type: "string", required: true, pattern: /^1[3|4|5|8][0-9]\d{4,8}$/, message: '联系人手机号码格式有误', trigger: 'blur'}
                  ],
                  contact_email  : [
                     { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur,change' }
                  ]
              },
              imagecropperShow: false,
              imagecropperKey: 0,
              image: 'http://owu2vcxbh.bkt.clouddn.com/files/avatar/default.png', 
              userCards : [],
              Contacts : {},
              userContacts : [],
              cardUseStatus : 0
            }
          },

          watch : {
            studentForm() {
              // console.log(this.image);
              if(this.studentForm.id)
               {
                 this.image = this.studentForm.picture;               
               }
            }
          },

          created() {
            // this.getRole();
            this.getUserVenus();
            //this.getClasses();
            //this.getCards();
            this.getRelationOptions();
          },
         
         
          methods: {

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
                            var venue_id =  options[0].value;
                            that.studentForm.venue_id =  venue_id;
                            that.getClasses(venue_id);
                            that.getCards(venue_id);
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
            showCardNumber()
            {
              if(this.studentForm.id == 0 || this.studentForm.auto_create_number == 0)
                return false
              else if(this.studentForm.id > 0)
                return true;
              return true;
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

            getRelationOptions() {
                var url = '/student/relationOptions',that = this;
                this.$http({
                   method :"GET",
                   url : url,
                })
                .then(function(response) {
                  var responseJson = response.data,data = responseJson.data
                  var options      = [];
                  that.relationOptions = data;
                })
                .catch(function(error) {
                  stack_error(error);
                }); 
            },
            getCards(venue_id) {
                var url = '/card/cardOptions', that = this;
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
                //   for (var i in data ) {
                //     let label =  data[i].name;
                //     options.push({value : data[i].id , label: label});
                //   }	
                 // console.log(options);
                  that.cardOptions = data;
                })
                .catch(function(error) {
                  stack_error(error);
                }); 
            },
            selectCard(value) {
                var card = this.cardOptions[value];
                card.is_new = 1;
                card.status = 1;
                card.card_id = card.id;
                var that = this; 

                this.$prompt('请输入卡券数量', '卡券数量', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /^[1-9]\d*$/,
                    inputErrorMessage: '卡券购买数量必须是大于0数值'
                }).then(({ value }) =>  {
                    card.buy_number = value;
                    card.status = that.cardUseStatus?0:1;
                    if(!this.cardUseStatus) {
                      this.cardUseStatus = 1;
                    }
                    card.total_price = parseFloat(card.card_price * value).toFixed(2);
                    that.studentForm.user_cards.push(card);
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '取消输入'
                    });       
                });
            },

            onSubmit() 
            {
               var studentForm = this.studentForm;
               this.$refs.studentForm.validate(valid => {
                  var that = this;
                  if (valid) {
                    if(that.studentForm.id ==0 && that.studentForm.user_cards.length ==0) {
                      this.$notify.error({
                        title: '错误',
                        message: '卡券信息不能为空'
                      });
                      return;
                    }
                    if(this.studentForm.user_contacts.length ==0) {
                      this.$notify.error({
                        title: '错误',
                        message: '学生联系人信息不能为空'
                      });
                      return;
                    }
                    studentForm.birthday = parseTime(studentForm.birthday,'{y}-{m}-{d}');
                    studentForm.sign_up_at = parseTime(studentForm.sign_up_at);
                    let url = '/student' + (this.studentForm.id ? '/' + this.studentForm.id : '')
                    let method = this.studentForm.id ? 'put' : 'post';
                    this.$http({
                      method :method,
                      url : url,
                      data : studentForm
                    })
                    .then(function(response) {
                        var {data} = response; 
                        that.$message({
                          showClose: true,
                          message: data.message,
                          type: 'success'
                        });
                      // 跳转到列表页
                     that.$router.push({ path: '/admin/student/index' })
                    })
                    .catch(function(error) 
                    {
                      // 把日期数据格式再次装换
                      if(that.studentForm.birthday)
                      {
                          that.studentForm.birthday = new Date(studentForm.birthday); 
                      }

                      if(that.studentForm.sign_up_at)
                      {
                        that.studentForm.sign_up_at = new Date(studentForm.sign_up_at);
                      }

                       
                      stack_error(error);
                    });
                } else {
                  console.log('error submit!!')
                  return false
                }
      
              });
             
            },
            cropSuccess(resData) {
              this.imagecropperShow = false;
              this.imagecropperKey = this.imagecropperKey + 1;
              this.image = resData.data.url;
              this.studentForm.picture = resData.data.real_path;
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

           
            checkUserName(name,callback) 
            {   
                var url = 'user/checkUserName',that = this;
                let id = that.studentForm.id ? that.studentForm.id : 0;
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

            onSelected(data) {
                this.studentForm.province_code = data.province.code;
                this.studentForm.province      = data.province.value;
                this.studentForm.city_code     = data.city.code;
                this.studentForm.city          = data.city.value;
                this.studentForm.area_code     = data.area.code;
                this.studentForm.area          = data.area.value;
            },
            deleteUserCard(index) {
                this.studentForm.user_cards.splice(index, 1)
            },

            showNewContactsForm() 
            { 
              this.dialogContactsFormVisible = true;

            },

            hanaleAddContacts()
            {
              this.Contacts.relation_name = this.relationOptions[this.Contacts.relation_id].name;
              this.studentForm.user_contacts.push(this.Contacts);
              console.log(this.userContacts);
              this.Contacts = {};
              this.dialogContactsFormVisible = false;
            },
            deleteUserContact(index)
            {
              this.studentForm.user_contacts.splice(index, 1)
            },
            venueChange(value) {

              this.getClasses(value);
              this.getCards(value);
            },
            changeCardStatus(index) {
              var length = this.studentForm.user_cards.length;
              var that = this;
              if(length > 1) 
              {
                  for (var i = length-1; i >= 0; i--) {
                    console.log(this.studentForm.user_cards[i]);
                    this.studentForm.user_cards[i].status = 0;
                  }
                  that.studentForm.user_cards[index].status = 1;
              }
            }


          }
        }
      </script>
      
      
      <style rel="stylesheet/scss" lang="scss" scoped>
      @import "resources/assets/styles/mixin";
      </style>