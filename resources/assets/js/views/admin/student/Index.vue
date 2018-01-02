<template>
  <div class="row">
    <div class="col-md-12">
        <div class="box">
          <div class="box-header">
            <div class="row">
              <div class="col-md-2">
                <router-link :to="{path:'create'}" class="btn btn-sm btn-success">
                      学生新增
                </router-link> 
              </div>

              <div class="col-md-10">
                <div class="form-inline pull-right">
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
                    
                    <!-- 归属道馆 -->
                    <div class="input-group input-group-sm">
                        <el-select style="width:160px"  v-show="selectItemVisible" v-model="params.venue_id" placeholder="请选择道馆"  class="filter-item"  @change="venueChange" size="small">
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
                         <el-select style="width:160px" v-model="params.class_id" placeholder="班级"  class="filter-item"  size="small" >
                                <el-option
                                       v-for="item in classOptions"
                                       :key="item.value"
                                       :label="item.label"
                                       :value="item.value"
                                       >
                                </el-option>
                            </el-select>
                    </div>    
                        <!--  按钮分组 -->
                    <div class="btn-group btn-group-sm">
                        <button type="submit" class="btn btn-primary" @click="$refs.table.loadList()"><i class="fa fa-search"></i></button>
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
          > 

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
                      close-transition>
                      {{row.name}}
                </el-tag>
            </template>
             <!--  操作人 -->
            <template slot="operator_name" slot-scope="item">
                <span>{{item.item.operator.name}}</span>
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

    <div id="studnet_view_box" style="display: none">
        <!-- Widget: user widget style 1 -->
        <div class="box box-widget">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <table class="table  table-bordered" style="font-size: 14px">
                <tbody>
                <tr>
                    <th>姓名</th>
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
                    <th>家庭住址</th>
                    <td colspan="6">{{student_info.province}}{{student_info.city}}{{student_info.area}}{{ student_info.address}}</td>
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
                        <el-progress :text-inside="true" :stroke-width="15" :percentage="student_info.in_user_student_card ? student_info.in_user_student_card.percentage: 0"></el-progress>
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
import {stack_error,parseSearchParam} from 'config/helper';
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
            operator_name : {label:'操作人', need:'operator'},
            created_at:{label:'创建时间', sortable: true},
            updated_at:{label:'更新时间', sortable: true},
            actions : {label: '操作'}
        },
        ajax_url: "/student",
        params: {},
        currentPage: 1,
        perPage: 15,
        searchQuery : {},
        listLoading: true,
        selectItemVisible : false,
        sexOptions : [],
        venueOptions : [],
        classOptions : [],
        student_info : {},
      }
    },
    created() 
    {
      this.getSexOptions();
      this.getUserVenus();
    },
    methods: {
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
            this.getClasses(value)
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
    }
}
</script>
