<template>
  <div class="row">
    <div class="col-md-12">
        <div class="box">
          <div class="box-header">
            <div class="row">

              <div class="col-md-4">
                    <div class="form-group form-inline">

                        <button type="button" 
                          class="btn btn-default btn-sm checkbox-toggle"  
                          @click="selectAll" 
                          v-show="check_box_select"
                        >
                            <i class="fa fa-square-o"></i>
                        </button>

                        <button type="button" class="btn btn-default btn-sm checkbox-sign"  @click="sign" >
                            <i class="fa fa-square-o"></i>
                            签到
                        </button>
                        
                        <div class="input-group input-group-sm" v-show="params.sign">
                            <el-dropdown  @command="handleCommand">
                              <el-button  class="btn btn-default btn-sm" size="small">
                                  操作<i class="el-icon-caret-bottom el-icon--right"></i>
                              </el-button>
                              <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="batch_sign">批量签到</el-dropdown-item>
                              </el-dropdown-menu>
                          </el-dropdown>
                        </div>
                        
                      
                       <!--  <button type="button" class="btn btn-default btn-sm checkbox-toggle" @click="getSelctItem">
                            获取子组件数据
                        </button> -->
                         
                      
                    </div>
            
                  
              </div>

              <div class="col-md-8">

                <div class="form-inline pull-right">

                    <router-link :to="{path:'create'}" class="btn btn-sm btn-success">
                      学生新增
                    </router-link> 

                    <!-- 学生姓名 -->
                    <div class="input-group input-group-sm" >
                        <el-input  size="small" class="input-group-sm" placeholder="学生姓名" v-model="params.name" ></el-input>       
                    </div>
                    
                   
                    <!-- 学生性别 -->
                    <div class="input-group input-group-sm" > 
                         <el-select style="width:90px" size="small" v-model="params.sex" class="filter-item" placeholder="性别">
                            <el-option
                                  v-for="(value, key) in sexOptions"
                                  :key="key"
                                  :value="key"
                                  :label="value"
                                  >
                            </el-option>
                        </el-select>
                    </div>

                    
                     <template v-if="!params.sign">
                        <!-- 归属道馆 -->
                        <div class="input-group input-group-sm">
                            <el-select style="width:160px"  v-show="selectItemVisible" v-model="params.venue_id" placeholder="请选择道馆"  class="filter-item"  @change="venueChange" size="small"
                            clearable
                            >
                                <el-option
                                       v-for="item in venueOptions"
                                       :key="item.value"
                                       :label="item.label"
                                       :value="item.value"
                                       >
                                </el-option>
                            </el-select>
                        </div>
                        <!-- 班级 -->
                         <div class="input-group input-group-sm">
                             <el-select style="width:180px" v-model="params.class_id" placeholder="班级"  class="filter-item"  size="small"
                                clearable
                             >
                                    <el-option
                                           v-for="item in classOptions"
                                           :key="item.value"
                                           :label="item.label"
                                           :value="item.value"
                                           >
                                    </el-option>
                                </el-select>
                        </div> 

                    </template>


                    
                    
                        <!--  按钮分组 -->
                    <div class="btn-group btn-group-sm">
                        <button type="submit" class="btn btn-primary" @click="$refs.table.loadList()"><i class="fa fa-search"></i></button>
                        <a href="javascript:void(0)" class="btn btn-warning" @click="reset"><i class="fa fa-undo"></i></a>
                    </div>
                </div>
             </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-inline" v-show="params.sign">
                         <!-- 签到日期    -->
                        <div class="input-group input-group-sm">
                             <el-date-picker
                                v-model="params.sign_date"
                                type   ="date"
                                placeholder="请选择签到日期"
                                :picker-options="pickerOptions0"
                                @change="signDateChange"
                                size="small"
                                >
                        </el-date-picker>
                        </div>
                         <!-- 归属道馆 -->
                        <div class="input-group input-group-sm">
                            <el-select style="width:160px"  v-show="selectItemVisible" v-model="params.venue_id" placeholder="请选择道馆"  class="filter-item"  @change="venueChange" size="small"
                            clearable
                            >
                                <el-option
                                       v-for="item in venueOptions"
                                       :key="item.value"
                                       :label="item.label"
                                       :value="item.value"
                                       >
                                </el-option>
                            </el-select>
                         </div>
                        
                         <!-- 签到班级 -->
                         <div class="input-group input-group-sm">
                             <el-select style="width:180px" v-model="params.sign_class" 
                                    placeholder="签到班级"  
                                    class="filter-item"  
                                    @change="signClassChange"
                                    size="small"   
                                clearable
                             >
                                    <el-option
                                           v-for="item in signClassOptions"
                                           :key="item.value"
                                           :label="item.label"
                                           :value="item.value"
                                           >
                                    </el-option>
                                </el-select>
                        </div> 

                       
                       


                    </div>
                    

                    


                </div>
            </div>
          </div>

          <vTable ref="table"
            stripped
            hover
            :checkbox="check_box_select"
            :ajax_url="ajax_url"
            :params="params"
            :items="items"
            :fields="fields"
            :current-page="currentPage"
            :per-page="perPage"
            :selectAll="selectAllStatus"
          > 
            <!-- checkbox -->
             <template slot="check_box" slot-scope="item">
                 <input type="checkbox" :value="item.item.id" :disabled="!item.item.can_sign" />
                 <!-- <el-checkbox  :true-labe="item.item.id"></el-checkbox> -->

            </template>


            <!-- 学生姓名 -->
             <template slot="name" slot-scope="item">
                <span :class="'student_' + item.item.id">{{item.item.name}}</span>
            </template>
            <!-- 学生性别 -->
            <template slot="sex" slot-scope="item">
                <span>{{item.item.sex == 1 ? "男":"女"}}</span>
            </template>

            <!--  学生头像 -->
            <template slot="picture" slot-scope="item">
                 <el-popover
                    ref="popover"
                    placement="right"
                    width="170"
                    trigger="hover"
                  >
                  <div style="text-align: right; margin: 0">
                          <img :src="item.item.picture" width="400px" height="400px" class="user-avatar"/>
                  </div>
                  </el-popover>
                  <img :src="item.item.picture" width="40px" height="40px" class="user-avatar img-circle" v-popover:popover />
            </template>
        
            <!--  归属道馆 -->
            <template slot="venues_name" slot-scope="item">
                <span>{{item.item.venues.name}}</span>
            </template>
          
            <!--  班级 -->
            <template slot="class_name" slot-scope="item">
                 <el-tag v-for="row in item.item.classes"
                      :key="row.id"
                      type="primary"
                      close-transition
                  >
                      {{row.name}}
                </el-tag>
            </template>


             <!--  签到 -->
            <template slot="sign_data" slot-scope="item">

               <!--  <el-tag v-for="row in item.item.sign_data"
                      :key="row.id"
                      type="success"
                      close-transition
                  >
                      {{row.class_name}}
                </el-tag>
 -->

                <el-tooltip
                    v-for="row in item.item.sign_data"
                    :key="row.id"
                    effect="dark" :content="row.status_name" placement="right"
                    >

                    <el-tag 
                      :key="row.id"
                      :type="row.type_name"
                      close-transition
                     >
                      {{row.class_name}}
                    </el-tag>
                  
                </el-tooltip>
            </template>


            <!-- 操作 -->
            <template slot="actions" slot-scope="item">
                <div class="btn-group">
                    <a href="javascript:;" @click="view(item.item)" class="btn btn-success btn-xs">查看</a>
                    <router-link :to="{path:'update/'+  item.item.id}" class="btn bg-orange btn-xs">编辑</router-link>
                    <router-link target="_blank"  :to="{path:'studentCardList/'+ item.item.id}" class="btn bg-info btn-xs">学生卡券</router-link>
                    <!-- <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a> -->
                </div>
            </template>
          </vTable>
      </div>
    </div>

   <!--  form -->
    <!-- Form -->
            <el-dialog title="课程签到" :visible.sync="dialogFormVisible" class="course_time_form" >
              <div class="row">
                  <div class="col-md-10">
                      <el-form ref="ScheduleForm" 
                      :model="signForm" 
                      >

                      <el-form-item label="签到学生" :label-width="formLabelWidth">
                          <el-tag
                              :key="tag"
                              v-for="tag in studnet_names"
                              :closable="true"
                              type="primary"
                              :close-transition="true" 
                              @close="handleClose(tag)"
                          >
                          {{tag}}
                          </el-tag>

                                                   
                        </el-form-item>
                        <el-form-item label="道馆" :label-width="formLabelWidth">
                             <template v-for="option in venueOptions">
                                   <span v-show="option.value==signForm.venue_id">
                                    {{option.label}}
                                   </span>
                             </template>
                        </el-form-item>

                        
                       <!--  签到班级 -->
                        <el-form-item label="签到班级" :label-width="formLabelWidth">
                              <template v-for="option in signClassOptions">
                                 <span v-show="option.value==signForm.sign_class">
                                  {{option.label}}
                                 </span>
                              </template>
                        </el-form-item>
                        
                        <!-- 签到日期 -->
                        <el-form-item label="签到日期" :label-width="formLabelWidth">
                            <span>{{signForm.sign_date}}</span>
                        </el-form-item>

                         <el-form-item label="签到类型" :label-width="formLabelWidth">
                              <el-radio-group v-model="signForm.status">
                                  <el-radio :label="1">签到</el-radio>
                                  <el-radio :label="2">迟到</el-radio>
                                  <el-radio :label="3">请假</el-radio>
                                  <el-radio :label="4">旷课</el-radio>
                              </el-radio-group>
                         </el-form-item>


        
                       <el-form-item label="签到备注" :label-width="formLabelWidth">
                          <el-input type="textarea" :rows="2" v-model="signForm.remark" auto-complete="off" 
                            size="small">
                          </el-input>
                      </el-form-item>
              </el-form>
                  </div>
              </div>

              <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="studentSign" :loading="buttonLoading">确 定</el-button>
              </div>

            </el-dialog>





    <div id="studnet_view_box" style="display: none">
        <!-- Widget: user widget style 1 -->
        <div class="box box-widget">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <table class="table  table-bordered" style="font-size: 14px">
                <tbody>
                <tr>
                    <th>姓名</th>studentSign
                    <td> {{student_info.name}} </td>
                    <th>性别</th>
                    <td> {{student_info.sex_map?student_info.sex_map[student_info.sex]:''}} </td>
                    <th>年龄</th>
                    <td> {{student_info.age}} </td>
                    <td rowspan=3>
                        <img :src="student_info.picture" width="120px" height="120px" class="user-avatar" />
                    </td>
                </tr>
                <tr>

                    <th>籍贯</th>
                    <td>{{student_info.native_place}} </td>
                    <th>道馆</th>
                    <td>{{student_info.venue_name}}</td>
                    <th>班级</th>
                    <td>
                        <el-tag v-for="row in student_info.classes" :key="student_info.id" type="primary" close-transition>
                            {{row.name}}
                        </el-tag>
                    </td>
                </tr>
                <tr>
                    <th>身份证</th>
                    <td colspan="2"> {{student_info.id_card}}</td>
                    <th>学校</th>
                    <td colspan="2">{{student_info.school}}</td>
                </tr>
                
                <tr>
                    <th>报名时间</th>
                    <td colspan="2"> {{student_info.sign_up_at}}</td>
                    <th>家庭住址</th>
                    <td colspan="3">{{student_info.province}}{{student_info.city}}{{student_info.area}}{{ student_info.address}}</td>
                </tr> 
                <tr>
                    <th colspan="7" style="text-align:center;"> 个人卡券信息 </th>
                </tr>
                <tr>
                    <th>卡券编号</th>
                    <td colspan="2"> {{student_info.in_user_student_card ?  student_info.in_user_student_card.student_card_number : '' }} </td>
                    <th>卡券类型</th>
                    <td colspan="3">{{student_info.in_user_student_card ? student_info.in_user_student_card.type_name : '' }}</td>
                </tr>
                <tr v-show="student_info.in_user_student_card&&student_info.in_user_student_card.type==1">
                    <th>卡券购买时间</th>
                    <td colspan="2"> {{student_info.in_user_student_card ? student_info.in_user_student_card.created_at : '' }} </td>
                    <th>有效期开始时间</th>
                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.start_time : '' }} </td>
                    <th>有效期结束时间</th>
                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.end_time : '' }}</td>
                </tr>

                <tr v-show="student_info.in_user_student_card&&student_info.in_user_student_card.type==2">
                    <th>卡券购买时间</th>
                    <td colspan="2"> {{student_info.in_user_student_card ? student_info.in_user_student_card.created_at : '' }} </td>
                    <th>卡券总次数/th>
                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.total_class_number : 0 }} </td>
                    <th>卡券消费次数</th>
                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.residue_class_number : 0 }}</td>
                </tr>
                <tr>
                    <td colspan="7">
                        <el-progress :text-inside="true" :stroke-width="15" :percentage="student_info.in_user_student_card?student_info.in_user_student_card.percentage:0"></el-progress>
                    </td>
                </tr>

                <tr>
                    <th colspan="7"  style="text-align:center;">联系人信息</th>
                </tr>
                <tr>
                    <th>关系</th>
                    <th colspan="2">联系人姓名</th>
                    <th colspan="2">联系人手机号码</th>
                    <th colspan="2">联系人邮箱</th>
                </tr>
                <tr v-for="row in student_info.user_contacts"  :key="row.id">
                    <td>{{row.relation_name}}</td>
                    <td colspan="2">{{row.contact_name}}</td>
                    <td colspan="2">{{row.contact_phone}}</td>
                    <td colspan="2">{{row.contact_email}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- /.widget-user -->
    </div>
  </div>
</template>

<script>

import {stack_error,parseSearchParam,isEmpty,parseTime} from 'config/helper';
export default {
    data() {
      return {
        items: [],
        fields: {
            id: {label: 'ID', sortable: true},
            name: {label: '学生姓名'},
            sex: {label: '性别'},
            age : {label:'年龄'},
            picture:{label:'头像'},
            venues_name : {label:'道馆', need:'venues'},
            class_name : {label:'班级', need:'classes'},
            sign_data : {label:'签到状态', need:'sign_data', hide:true},
            // created_at:{label:'创建时间', sortable: true},
            updated_at:{label:'更新时间', sortable: true},
            actions : {label: '操作'}
        },
        pickerOptions0: {
          disabledDate(time) {
            return time.getTime() > Date.now();
          }
        },
        ajax_url: "/student",
        params: {
          sign_date : new Date,
          date : parseTime(new Date,'{y}-{m}-{d}')
        },
        currentPage: 1,
        perPage: 15,
        searchQuery : {},
        listLoading: true,
        selectItemVisible : false,
        sexOptions : [],
        venueOptions : [],
        classOptions : [],
        signClassOptions : [],
        student_info : {},
        selectAllStatus : false,
        selectItmes : [],
        signForm : {
          status : 1
        },
        dialogFormVisible : false,
        formLabelWidth: '110px',
        studnet_names : [],
        buttonLoading: false,
        check_box_select : false,
        
      }
    },
    watch()
    {
       
    },
    updated () {
        
    },
    

    created() 
    {
      this.getSexOptions();
      this.getUserVenus();
    },
    methods: {
        getSelctItem()
        {
            console.log(this.$refs.table.checkBoxItems());
        },
        debug()
        {
            
            this.fields.sign_data.hide = !this.fields.sign_data.hide;
        },

        signDateChange(value)
        {
            this.params.date = value;
            // 刷新课程下拉框
            this.getSignClass();
            // 刷新列表
            this.$refs.table.loadList();
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
                if(options.length == 1) {
                    var venue_id =  options[0].value;
                    that.params.venue_id =  venue_id;
                } else {
                    that.selectItemVisible = true;
                }
                      
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

            this.getClasses(value);
            if(this.params.sign)
            {
              this.getSignClass();
            }

            // 刷新页面
            this.$refs.table.loadList();
        },

        getSignClass () 
        {
         
            var url = '/student/signClassOptions', that = this;
            this.$http({
               method :"GET",
               url : url,
               params : that.params
            })
            .then(function(response) {
              var responseJson = response.data;
              that.signClassOptions = responseJson.data
            })
            .catch(function(error) {
              stack_error(error);
            }); 
        },

        signClassChange(value)
        {
            var arr = value.split('_');
            this.params.section  = arr[0];
            this.params.class_id = arr[1];
            this.$refs.table.loadList();
            
        },

        handleEdit(index, row) {
            console.log(index, row);
        },
        view(row) {
            
            var student_id = row.id;
            // 请求数据
            var url = '/student/getStudentInfo', that = this;
            this.$http({
                method: "GET",
                url: url,
                params: {
                    student_id: student_id
                }
            })
            .then(function (response) {
                var responseJson = response.data, data = responseJson.data
                    that.student_info = data;
                    var title = row.name + "的个人信息";
                    setTimeout(function () {
                    swal({
                        title: title,
                        width: '80%',
                        html: $('#studnet_view_box').html(),
                    });
                }, 100);
                })
            .catch(function (error) {
                stack_error(error);
            }); 
            
        },

        reset() 
        {
                this.params = {};
        },

        selectAll()
        {
          
            if (this.selectAllStatus) 
            {
                //Uncheck all checkboxes
               //$(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
                $(".checkbox-toggle  > .fa").removeClass("fa-check-square-o").addClass('fa-square-o');

            } 
            else 
            {
                //Check all checkboxes
                //$(".mailbox-messages input[type='checkbox']").iCheck("check");
                $(".checkbox-toggle > .fa").removeClass("fa-square-o").addClass('fa-check-square-o');
            }
            this.selectAllStatus =!this.selectAllStatus;  
            this.selectItmes    = this.$refs.table.checkBoxItems();
            console.log(this.selectItmes);
        },
        sign()
        {
            if (this.params.sign) 
            {
                //Uncheck all checkboxes
               //$(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
                $(".checkbox-sign  > .fa").removeClass("fa-check-square-o").addClass('fa-square-o');
                this.params.sign = 0;

            } 
            else 
            {
                //Check all checkboxes
                //$(".mailbox-messages input[type='checkbox']").iCheck("check");
                $(".checkbox-sign > .fa").removeClass("fa-square-o").addClass('fa-check-square-o');
                this.params.sign = 1;
            }
            
            this.fields.sign_data.hide = !this.fields.sign_data.hide;
            this.check_box_select = !this.check_box_select;
        },
        handleCommand(command)
        {
          if(command == 'batch_sign')
          {
              this.batch_sign();
          }
        },

        batch_sign()
        {

            this.selectItmes = this.$refs.table.checkBoxItems();
            if(isEmpty(this.selectItmes))
            {
                this.$message.error("请选择需要签到的学生!");
                return;
            }

            if(!this.params.venue_id)
            {
               this.$message.error("请选择需要签到道馆!");
               return;
            }

            if(!this.params.sign_class)
            {
              this.$message.error("请选择需要签到的班级");
               return;
            }

            // 获取学生姓名信息
            var student_names = [];
            var temp = {}
            this.selectItmes.forEach(function(value, index, array) {
               var name = $(".student_" + value).html();
               student_names.push(name);   
            });

            this.studnet_names          = student_names;
            this.signForm.venue_id      = this.params.venue_id;
            this.signForm.sign_date     = parseTime(this.params.sign_date,'{y}-{m}-{d}');
            this.signForm.section       = this.params.section;
            this.signForm.class_id       = this.params.class_id;
            this.signForm.sign_class    = this.params.sign_class;
            this.signForm.student_ids   = this.selectItmes;
            this.signForm.student_names = student_names;
            // this.signForm.status = '2';
            
           
            this.dialogFormVisible = true;
            //初始化签到数据 
            
            // 显示签到确认页面
            
        },

        handleClose(val)
        {
          
            if(this.signForm.student_ids.length == 1)
            {
               
                 this.$message.error("签到学生数不能在少了啊！");
                 return;
            }  

            var index = this.signForm.student_names.indexOf(val);
            this.signForm.student_names.splice(index, 1);
            this.signForm.student_ids.splice(index, 1);
        
            console.log(this.signForm.student_ids.length);
        },

        studentSign()
        {
            // 学生信息签到 
            console.log('asdas');
              var that = this;
              let url = '/student/sign'
              let method = 'post';
              that.buttonLoading = true;
              this.$http({
                  method :method,
                  url : url,
                  data : that.signForm
              })
              .then(function(response) {
                  var {data} = response; 
                  that.dialogFormVisible = false;
                  that.buttonLoading = false;
                  that.dialogFormVisible = false;
                  that.signForm = {};

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
        }
    }
}




</script>
