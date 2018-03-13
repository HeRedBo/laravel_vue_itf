<template>
    <div class="row">

        <div class="col-md-12">
            <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">学生基本信息</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
               <!--  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
              </div>
            </div>
            <div class="box-body">
                 <table :class="['table table-bordered']" style="width:70%;">
                            <tbody>
                                <tr>
                                    <th>姓名</th>
                                    <td> {{student_info.name}} </td>
                                    <th>性别</th>
                                    <td> {{student_info.sex_map?student_info.sex_map[student_info.sex]:''}} </td>
                                    <th>年龄</th>
                                    <td> {{student_info.age}} </td>
                                </tr>
                                <tr>
                                    <th>卡券编号</th>
                                    <td> {{student_info.in_user_student_card ?  student_info.in_user_student_card.student_card_number : '' }} </td>
                                    <th>卡券类型</th>
                                    <td>{{student_info.in_user_student_card ? student_info.in_user_student_card.type_name : '' }}</td>
                                    <th>卡券购买时间</th>
                                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.created_at : '' }} </td>
                                </tr>
                                <tr v-show="student_info.in_user_student_card&&student_info.in_user_student_card.type==1">
                                
                                    <th>有效期开始时间</th>
                                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.start_time : '' }} </td>
                                    <th>有效期结束时间</th>
                                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.end_time : '' }}</td>
                                    <th></th>
                                    <td></td>
                                </tr>

                                <tr v-show="student_info.in_user_student_card&&student_info.in_user_student_card.type==2">
                                  
                                    <th>卡券总次数</th>
                                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.total_class_number : 0 }} </td>
                                    <th>卡券消费次数</th>
                                    <td> {{student_info.in_user_student_card ? student_info.in_user_student_card.residue_class_number : 0 }}</td>
                                    <th></th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <el-progress :text-inside="true" :stroke-width="15" :percentage="student_info.in_user_student_card ? student_info.in_user_student_card.percentage: 0"></el-progress>
                                    </td>
                                </tr>
                            </tbody>
                        </table> 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        
         <!-- /.col (LEFT) -->
        <div class="col-md-12">
          <!-- LINE CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">{{$route.name}}</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <!--<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
              </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <button  @click="handleCreate" type="button" class="btn btn-sm btn-success">        添加卡券
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="form-inline pull-right">
                             <router-link target="_blank"  :to="{path:'/admin/student/StudentCardLogger/'+ params.student_id}" class="btn bg-info">操作日志</router-link>
                        </div>
                    </div>
                </div>
                <!-- 学生卡券列表表 -->
                <vTable ref="table" 
                       stripped hover 
                        :searchType=1 
                        :ajax_url="ajax_url" 
                        :params="params" 
                        :items="items" 
                        :fields="fields" 
                        :current-page="currentPage"
                        :per-page="perPage"
                        >

            
                     <template slot="status" slot-scope="item">
                        <a  href="javascript:void(0)" 
                            data-toggle="tooltip" 
                            :title="item.item.status_name"
                            @click="changeStudentCardStatus(item.item)"
                            >
                            <i  :class="['fa','fa-circle',item.item.status==1?'text-success':item.item.status==0?'text-info':'text-danger']"></i>
                        </a>
                    </template>

                    <template slot="total_class_number" slot-scope="item">
                        <span>{{item.item.type==1?'--':item.item.total_class_number}}</span>
                    </template>


                    <template slot="residue_class_number" slot-scope="item">
                        <span>{{item.item.type==1?'--':item.item.residue_class_number}}</span>
                    </template>

                    <template slot="start_time" slot-scope="item">
                        <span v-if="item.item.status==2">
                            <del>
                                {{item.item.type==2?'--':item.item.start_time}}
                            </del>
                        </span>
                        <span v-else>
                             {{item.item.type==2?'--':item.item.start_time}}
                        </span>
                    </template>

                    <template slot="end_time" slot-scope="item">
                         <span v-if="item.item.status==2">
                            <del>
                                {{item.item.type==2?'--':item.item.end_time}}
                            </del>
                        </span>
                        <span v-else>
                             {{item.item.type==2?'--':item.item.end_time}}
                        </span>
                       <!--  <span>{{item.item.type==2?'--':item.item.end_time}}</span> -->
                    </template>


                    <!-- 操作 -->
                    <template slot="actions" slot-scope="item">
                        <div class="btn-group">
                            <a href="javascript:;" @click="view(item.item)" class="btn btn-success btn-xs">查看</a>
                            <!-- <router-link :to="{path:'update/'+  item.item.id}" class="btn bg-orange btn-xs">编辑</router-link> -->
                            <!--   <router-link target="_blank"  :to="{path:'logger/'+ item.item.id}" class="btn bg-info btn-xs">操作日志</router-link> -->
                            <!-- <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a> -->
                        </div>
                    </template>
                </vTable>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
      </div>
        <el-dialog :title="dialogTitle" :visible.sync="dialogFormVisible" style="z-index:120;" size="small" >
            <el-form
                class="small-space"
                ref="StudentCard"
                :model="StudentCard"
                label-position="right"
                label-width="80px"
                style='width:65%;margin-left:5px;'
            >
                <!-- 卡券种类 -->
                <el-form-item label="购卡类型">
                        <el-select v-model="StudentCard.card_id" @change="selectCard"  placeholder="请选择需要购买的卡" style="width:100%" >
                          <el-option
                            v-for="item in cardOptions"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            >
                          </el-option>
                        </el-select>
                </el-form-item>


                <el-table
                    :data="StudentCard.user_cards"
                    style="min-width: 650px;margin-bottom: 10px;"
                    align="cneter"
                >
                    <el-table-column
                        prop="card_id"
                        label="卡券ID"
                        align="cneter"
                        >
                    </el-table-column>

                    <el-table-column
                        prop="name"
                        label="卡券名称"
                        align="cneter"
                    >

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
                        label="卡券价格"
                    >
                    </el-table-column>

                    <el-table-column
                        prop="total_price"
                        align="cneter"
                        label="总金额"
                    >
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
                    >
                    <template slot-scope="scope">
                        <a
                         v-show="scope.row.is_new==1"
                         href="#"  @click="deleteUserCard(scope.$index)"
                         class="btn btn-danger btn-xs">删除
                        </a>
                    </template>
                    </el-table-column>

                </el-table>

            </el-form>

            <div slot="footer" class="dialog-footer" style="text-align:center">
              <el-button @click="dialogFormVisible = false">取 消</el-button>
              <el-button type="primary" @click="handleCard" :loading="buttonLoading">确 定</el-button>
            </div>
        </el-dialog>

        <!-- 修改状态显示层 dialog-->
        <el-dialog :title="dialogTitle" :visible.sync="dialogStatusFormVisible" style="z-index:120;" >       
            <el-form class="small-space" 
                ref="changeStatusForm" 
                :model="changeStatusForm"
                :rules="statusRules"
                label-position="right"
                label-width="120px"
                style='width: 400px; margin-left:50px;'
            >
                <el-form-item label="学生卡券ID">
                    <h4>{{changeStatusForm.id}}</h4>
                </el-form-item>

                <el-form-item label="修改状态"> 
                     <el-radio-group v-model="changeStatusForm.status">
                        <el-radio :label="1" v-show="changeStatusForm.old_status<1">启用</el-radio>
                        <el-radio :label="2" v-show="changeStatusForm.old_status<=2" >停用</el-radio>
                    </el-radio-group>

                </el-form-item>
                <!-- 期卡的生效时间 -->
                <el-form-item label="期卡生效时间" v-show="changeStatusForm.status==1&&changeStatusForm.type==1" prop="start_time">
                        <el-date-picker
                                v-model="changeStatusForm.start_time"
                                type="datetime"
                                placeholder="选择开始时间"
                                format="yyyy-MM-dd HH:mm:ss"
                                >
                        </el-date-picker>
                </el-form-item>
                <el-form-item label="操作备注"  prop="remark">
                    <el-input type="textarea" :autosize="{ minRows: 3, maxRows: 6}" v-model="changeStatusForm.remark"></el-input>
                </el-form-item>
                 <span class="text-red">
                    <strong>注意：</strong>
                 </span> 
                <span class="text-light-blue">卡券状态一经修改无法再操作！请谨慎操作！</span>
            </el-form>

            <div slot="footer" class="dialog-footer">
              <el-button @click="dialogStatusFormVisible = false">取 消</el-button>
              <el-button type="primary" @click="handleChangeStatus" :loading="buttonLoading">确 定</el-button>
            </div>
        </el-dialog>


        <div id="card_view_box" style="display: none">
            <!-- Widget: user widget style 1 -->
            <div class="box box-widget">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <table class="table  table-bordered" style="font-size: 14px">
                    <tbody>
                    <tr>
                        <th>ID</th>
                        <td>{{card.id}}</td>
                        <th>卡券类型</th>
                        <td>{{card.type_name}}</td>
                    </tr>

                    <tr>
                        <th>卡券名称</th>
                        <td>{{card.card_name}}</td>
                        <th>卡券价格</th>
                        <td>{{card.price}}</td>
                    </tr>

                    <tr>
                        <th>购买数量</th>
                        <td>{{card.number}}</td>
                        <th>总价</th>
                        <td>{{card.card_price}}</td>
                    </tr>
                    <tr>
                        <th>总课程数</th>
                        <td>
                            <span>{{card.type==1?'--':card.total_class_number}}</span>
                        </td>
                        <th>剩余课程数</th>
                        <td>{{card.type==1?'--':card.residue_class_number}}</td>
                    </tr>


                     <tr>
                        <th>卡券有效期开始时间</th>
                        <td>
                            <span v-if="card.status==2">
                                <del>
                                    {{card.type==2?'--':card.start_time}}
                                </del>
                            </span>
                            <span v-else>
                                 {{card.type==2?'--':card.start_time}}
                            </span>
                          
                        </td>
                        <th>卡券有效期结束时间</th>
                        <td>
                             <span v-if="card.status==2">
                                <del>
                                    {{card.type==2?'--':card.end_time}}
                                </del>
                            </span>
                            <span v-else>
                                 {{card.type==2?'--':card.end_time}}
                            </span>
                        </td>
                    </tr>
                    
                    <tr>
                        
                        <th>卡券状态</th>
                        <td>{{card.status_name}}</td>
                        <th>操作备注</th>
                        <td>{{card.remark}}</td>
                    </tr>
                
                    <tr>
                        <th>创建时间</th>
                        <td>{{card.created_at}}</td>

                        <th>最新更新时间</th>
                        <td>{{card.updated_at}}</td>
                    </tr>

                    <tr>
                       
                        <th>操作人</th>
                        <td>{{card.operator_name}}</td>
                        <th></th>
                        <td></td>
                    </tr>
                    
                    </tbody>
                </table>

            </div>
            <!-- /.widget-user -->

        </div>

    </div>
</template>

<script>
$(function () {
    $('.box').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
});

    import { stack_error, parseSearchParam,isEmpty,parseTime} from 'config/helper';
    export default {
        data() {
            const validateStartTime = (rule, value, callback) => 
            {
                if (this.changeStatusForm.status == 1 && this.changeStatusForm.type == 1) 
                {
                    if(!value)
                        return callback(new Error('卡券生效时间不能为空！'));
                }
                callback(); 
            }
            const validateRemark =  (rule, value, callback) => 
            {
                if (this.changeStatusForm.status == 2) 
                {
                    if(isEmpty(this.changeStatusForm.remark))
                        return callback(new Error('修改备注信息不能为空'));
                }
                callback(); 
            };

            return {
                items: [],
                fields: {
                    id: { label: 'ID', sortable: true },
                    type_name: { label: '卡券类别' },
                    card_name: { label: '卡券名称' },
                    price: { label: '卡券价格' },
                    number: { label: '购买数量' },
                    card_price: { label: '总价'},
                    total_class_number: { label: '总课程数' },
                    residue_class_number: { label: '剩余课程数' },
                    status: { label: '状态' ,sortable: true},
                    start_time: { label: '有效期开始时间' },
                    end_time: { label: '有效期结束时间'},
                    // created_at: { label: '创建时间', sortable: true },
                    // updated_at: { label: '更新时间', sortable: true },
                    actions: { label: '操作' }
                },
                ajax_url: "/student/studentCardList",
                params: {},
                currentPage: 1,
                perPage: 15,
                searchQuery: {},
                listLoading: true,
                selectItemVisible: false,
                dialogFormVisible:false,
                dialogStatusFormVisible:false,
                cardOptions: [],
                StudentCard : {
                    user_cards: []
                },
                dialogTitle : '新增卡券',
                buttonLoading: false,
                cardUseStatus:0,
                card : {},
                student_info : {},
                changeStatusForm : {
                    id : 0,
                    status : '',
                    renark: 0,
                    start_time : new Date(),
                },
                statusRules : {
                    start_time : [
                        { validator: validateStartTime, trigger: 'blur' },
                    ],
                     remark : [{
                        validator: validateRemark, trigger: 'blur'}
                   ]
                }
            }
        },
        created() {
           this.initData();
           this.getUserCardOptions();
           this.getStudnetInfo();

        },
        methods: {
            initData()
            {
                var that = this,id = this.$route.params.id;
                this.params.student_id=id;
            },
            getUserCardOptions()
            {
                var url = '/card/studentCardOptions', that = this;
                this.$http({
                   method :"GET",
                   url : url,
                   params : {
                     student_id : that.params.student_id
                   }
                })
                .then(function(response) {
                  var responseJson = response.data,data = responseJson.data
                  var options = [];
                  that.cardOptions = data;
                })
                .catch(function(error) {
                  stack_error(error);
                });
            },
            selectCard(value){
                var card = this.cardOptions[value];
                card.card_id = card.id;
                card.is_new = 1;
                card.status = 1;
                card.id = 0;
                var that = this;
                this.$prompt('请输入卡券数量', '卡券数量', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /^[1-9]\d*$/,
                    inputErrorMessage: '卡券购买数量必须是大于0数值'
                }).then(({ value }) => {
                    card.buy_number = value;
                    card.status = 0;// that.cardUseStatus?0:1;
                    // if(!this.cardUseStatus) {
                    //   this.cardUseStatus = 1;
                    // }
                    card.total_price = parseFloat(card.card_price * value).toFixed(2);
                    that.StudentCard.user_cards.push(card);
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '取消输入'
                    });
                });
            },

            deleteUserCard(index) {
                this.StudentCard.user_cards.splice(index, 1)
            },

            changeCardStatus(index) {
              var length = this.StudentCard.user_cards.length;
              var that = this;
              if(length > 1)
              {
                  for (var i = length-1; i >= 0; i--) {
                    this.StudentCard.user_cards[i].status = 0;
                  }
                  that.StudentCard.user_cards[index].status = 1;
              }
            },

            handleCreate()
            {
                this.dialogFormVisible = true;
            },

            handleCard(){
                var that = this,student_id = this.$route.params.id;
                this.StudentCard.student_id = student_id;
                // 教研学生卡券信息是否有提交
                if(that.StudentCard.user_cards.length ==0) {
                    this.$notify.error({
                      title: '错误',
                      message: '卡券信息不能为空',
                      duration:3000
                    });
                    return;
                }
                let url = '/student/saveStudentCard'
                let method = 'post';

                // 请求接口保存用户卡券信息
                this.$http({
                      method :method,
                      url : url,
                      data : that.StudentCard
                    })
                .then(function(response) {
                    var {data} = response;
                    that.$message({
                          showClose: true,
                          message: data.message,
                          type: 'success'
                    });
                    // 跳转到列表页
                    that.dialogFormVisible = false;
                    // 重置数据
                    that.StudentCard.user_cards = [];
                    // 刷新列表数据
                    that.$refs.table.loadList();
                    
                })
                .catch(function(error) {
                      stack_error(error);
                });
            },
            handleEdit(index, row) {
                console.log(index, row);
            },
            reset() {
                this.params = {};
            },

            view(card_info) {
                this.card = card_info;
                 setTimeout( function() {
                    swal({
                        title : card_info.card_name,
                        width: '50%',
                        html : $('#card_view_box').html(),
                    });
                },100);
            },
            getStudnetInfo() 
            {
                  
                var student_id = this.$route.params.id;
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
                        console.log(data);
                    })
                    .catch(function (error) {
                        stack_error(error);
                    }); 
            },
            changeStudentCardStatus(row)
            {
                console.log(row);
                if(row.status >=2)
                    return;

                this.changeStatusForm.id = row.id;
                this.changeStatusForm.old_status = row.status;
                this.changeStatusForm.type       = row.type;
                this.changeStatusForm.status = row.status +1;
                this.changeStatusForm.start_time = new Date();
                // 显示操作框
                this.dialogStatusFormVisible = true;

            },
            statusTimeChange(value)
            {
                console.log(value);
                this.changeStatusForm.start_time =value;
            },
            handleChangeStatus()
            {
                this.$refs.changeStatusForm.validate(valid => {
                var that = this;
                if (valid) 
                {
                    // 时间处理
                    that.changeStatusForm.start_time = parseTime(that.changeStatusForm.start_time);
                    // 数据校验通过 
                     that.buttonLoading = true;
                    var url = '/student/changeStudentCardStatus', that = this;
                    this.$http({
                       method :"POST",
                       url : url,
                       data : that.changeStatusForm
                    })
                .then(function(response) 
                {
                     // 提示
                     var {data} = response;
                     that.$message({
                          showClose: true,
                          message: data.message,
                          type: 'success'
                    });
                    that.dialogStatusFormVisible = false;
                    that.changeStatusForm = {};
                     // 刷新列表数据
                    that.$refs.table.loadList();
                    that.buttonLoading = false;
                })
                .catch(function(error) 
                {
                  that.buttonLoading = false;
                  stack_error(error);
                });
                } 
                else 
                {
                    console.log('error submit!!')
                    return false
                }

            });
            }
        }
    }
</script>
