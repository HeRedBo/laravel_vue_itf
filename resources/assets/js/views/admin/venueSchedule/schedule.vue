<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header"> 
					<div class="col-md-6">
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
                             <el-select style="width:160px" v-model="params.class_id" placeholder="班级"  class="filter-item"  size="small"
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
					</div>
                    
                    <div class="col-md-6">
                        <div class="form-inline pull-right">
                            <!--  数据搜索框 -->
                           <div class="input-group input-group-sm" >
                                <el-date-picker
                                    v-model="params.date"
                                    type="date"
                                    placeholder="选择日期"
                                    size="small"
                                    :picker-options="pickerOptions"
                                    @change="seachDataChange"
                                  >
                                </el-date-picker>
                           </div>

                            <!--  按钮分组 -->
                        <div class="btn-group btn-group-sm">
                            <button type="submit" class="btn btn-primary" @click="getSchedule"><i class="fa fa-search"></i>
                            </button>
                            <a href="javascript:void(0)" class="btn btn-warning" @click="reset"><i class="fa fa-undo"></i></a>
                        </div>
                        </div>
                       
                    </div>
                </div>

                <div class="box-body tablew-responsive no-padding">
                    <table class="table table-bordered dataTable table-striped table-hover">
                        <thead>
                            <tr>
                            	<!-- :class="['th_' + key + ' text-color-red']" -->
                            	<!-- field.head_name.indexOf('星期六') > -1 || 
								field.head_name.indexOf('星期日') > -1  -->
                                <th v-for="field,key in fields"
                                    :class="[((field.date && field.head_name) && 
										(field.head_name.indexOf('星期') == -1 
										)
									 )?'th_' + key + ' text-color-red'
									 :'th_' + key
									]"         
                                >
                                <template>
                                	 {{field.head_name}}
                                </template>
                                <template v-if="field.date">
                                	<br>
                                	{{field.date}}
                                </template>   
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr  v-for="r in course_count" >
                                <td v-for="j in total_column" @click="tdClick(j-data_start_column+1,r)"
                                    :class="['td_course_time_' + (j-data_start_column+1) + '_' + r ]"
                                >
                                    <template v-if="j==1">
                                       <!--  <el-time-picker
                                            is-range
                                            disabled
                                            :class="['course_time','course_time_' + r + '_' + j]"
                                            size="small"
                                            v-model="course_times[r]"
                                            @change="inputChange"
                                            @blur="courseTimeChange(r)"
                                            placeholder="选择时间范围"
                                            >
                                        </el-time-picker> -->
                                        {{course_times[r][0]}} -
                                        {{course_times[r][1]}}
                                    </template>
                                    <template v-else-if="j==2">
                                        {{r}}
                                    </template>
                                    <template v-else>
                                       <!-- {{j-data_start_column+1}} -->
                                       <!-- {{r}} -->
                                        {{
                                             venue_schedules[j-data_start_column+1] ? 
                                                venue_schedules[j-data_start_column+1][r] ?
                                                    venue_schedules[j-data_start_column+1][r].class_name
                                                    : ''
                                             : ''    
                                        }}

                                        <br>
                                        <span class='text-error'>
                                             {{ venue_schedules[j-data_start_column+1] ? venue_schedules[j-data_start_column+1][r] ? venue_schedules[j-data_start_column+1][r].remark
                                            ? venue_schedules[j-data_start_column+1][r].remark : '' :'' :'' }}
                                        </span>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                        

                    </table>
                </div>

                <div class="box-footer">
                    <!-- <button type="submit" @click="$router.back()" class="btn btn-default">返回</button>
                    <button type="submit" @click="$router.push({ path: '/admin/venueSchedule/index' })" class="btn btn-default">返回</button>
                    <button type="submit" @click="onSubmit" class="btn btn-info">
                         {{ venueCourseForm.id ? '更新' : '添加' }}
                    </button> -->

                </div>
            </div>
        </div>

        <!-- Form -->
            <el-dialog title="设置课程" :visible.sync="dialogFormVisible" class="course_time_form" >
              <div class="row">
                  <div class="col-md-10">
                      <el-form ref="ScheduleForm" 
                      :model="ScheduleForm"  :rules="CourseRules"
                      >
                      	<el-form-item label="日期" :label-width="formLabelWidth">
                        <!--   <el-input  v-model="ScheduleForm.date" auto-complete="off" 
                          size="small">
                          </el-input> -->
                          <span>{{ScheduleForm.schedule_date}}</span>

                        </el-form-item>
                        <el-form-item label="星期" :label-width="formLabelWidth">
                          <el-input  v-show="0" v-model="ScheduleForm.week" auto-complete="off" 
                          size="small">
                          </el-input>
                          <span>星期{{WeekMap[ScheduleForm.week]}} </span>
                        </el-form-item>

                        <el-form-item label="节次" :label-width="formLabelWidth">
                            <span>第{{ScheduleForm.section}}节课</span>
                            <el-input v-show="0" v-model="ScheduleForm.section" auto-complete="off" 
                            size="small">
                            </el-input>
                        </el-form-item>

                        <!-- 班级  prop="class_id"-->
                        <el-form-item label="班级"  :label-width="formLabelWidth" >
                            <el-select v-model="ScheduleForm.class_id" placeholder="请选择班级" size="small" clearable>
                              <el-option
                                v-for="item in classOptions"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                              </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item label="课程备注" :label-width="formLabelWidth">
                          <el-input type="textarea" :rows="2" v-model="ScheduleForm.remark" auto-complete="off" 
                          size="small">
                          </el-input>
                       </el-form-item>
              </el-form>
                  </div>
              </div>

              <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="handleCourseRow">确 定</el-button>
              </div>

            </el-dialog>

    </div>
</template>
<script>
import {stack_error,isEmpty,parseTime} from 'config/helper';
export default {
    name: 'Form',
    data() {
        var checkCourseCount = (rule, value, callback) => {
            var patrn = /^[0-9]*$/;
            if (patrn.exec(value) == null || value == "" || value > this.limit_data_row) 
            {
                callback(new Error('请输入正确的数字值'));
            } 
            else 
            {
                 callback();
            }
        };
        return {
            fields: {},
            params :{},
            data_start_column: 3,
            total_column : 9,
            limit_data_row : 9,
            course_count : 0,   // 课程总数
            data_row: 7,
            class_Options : [],
            dialogFormVisible: false,
            ScheduleForm: {
              class_id: '',
              remark: '',
              week: "2",
              section: ''
            },
            WeekMap : {
                "1": "一",
                "2": "二",
                "3": "三",
                "4": "四",
                "5": "五",
                "6": "六",
                "7": "日",
            },
            venueCourseFormRules : {
                venue_id : [
                    { required: true, message: '归属道馆不能为空'}
                ],
                schedule_name : [
                     { required: true, message: '课表名称不能为空'}
                ],
                date_between : [
                     { required: true, message: '课程有效期不能为空'}
                ],
                course_count : [
                    { required: true, message: '节次数不能为空'},
                    { validator: checkCourseCount, trigger: 'blur' }
                ]
            },
            CourseRules: { 
                // venue_id : [
                //     { required: true, message: '归属道馆不能为空'}
                // ],
                // class_id: [
                //     { required: true, type: 'number', message: '班级不能为空', trigger: 'blur' }
                // ]
            },
            formLabelWidth: '110px',
            venueOptions: [],
            classOptions: [],
            classMap : [],
            selectItemVisible : false,
            course_times : {},
            venue_schedules : {},
            venueCourseForm:{},
            schedule : {},
            pickerOptions: {
                shortcuts: [{
                    text: '今天',
                    onClick(picker) {
                      picker.$emit('pick', new Date());
                    }
                  }, {
                    text: '昨天',
                    onClick(picker) {
                      const date = new Date();
                      date.setTime(date.getTime() - 3600 * 1000 * 24);
                      picker.$emit('pick', date);
                    }
                  }, {
                    text: '一周前',
                    onClick(picker) {
                      const date = new Date();
                      date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
                      picker.$emit('pick', date);
                    }
                  },
                  {
                    text: '一周后',
                    onClick(picker) {
                      const date = new Date();
                      date.setTime(date.getTime() + 3600 * 1000 * 24 * 7);
                      picker.$emit('pick', date);
                    }
                  },
                 ]
            },

        }
    },

    created() 
    {
    	this.getUserVenus();
        this.getSchedule();  
    },
    methods:{
    	getSchedule() 
    	{
    		var that = this,params = this.params;
            var url = '/venueSchedules/schedules';
            this.$http({
                method :'GET',
                url : url,
                params : params
            })
            .then(function(response) 
            {
                let {data} = response;
                var  respondata = data.data
                that.fields   = respondata.fields;
                that.schedule = respondata.schedule;
                that.course_count    = that.schedule.course_count;
                that.course_times    = respondata.course_times;
                that.venue_schedules = respondata.venue_schedules;
            })
            .catch(function(error) 
            {
                console.log(error);
                stack_error(error);
            });
    	},

        seachDataChange(value)
        {
            this.params.date = value;
            this.getSchedule();
        },

        inputChange(value,index) {
            var dom_str  = '.course_time'
            $(dom_str).find("input").removeClass('is-error');
        },
        tdClick(row_num, col_num)
        {
            // 校验道馆的基本信息必填项
            var that = this;
             // 处理每个的数据
            if(((1<=row_num) && (row_num <=7)) && ((col_num >=1) && (col_num <= that.data_row)))
            {  
                if(isEmpty(that.venue_schedules[row_num]) || (!isEmpty(that.venue_schedules[row_num]) && isEmpty(that.venue_schedules[row_num][col_num])))
                {
                    that.ScheduleForm = {};
                    that.ScheduleForm.week = row_num;
                    that.ScheduleForm.section = col_num;
                    that.ScheduleForm.remark = '';
                    that.ScheduleForm.schedule_date = that.fields[row_num +1 ].ori_date
                    that.ScheduleForm.start_time = that.course_times[col_num][0];
                    that.ScheduleForm.end_time = that.course_times[col_num][1];
                    that.ScheduleForm.schedule_id = that.schedule.id;
                } 
                else 
                {
                    if(!isEmpty(that.venue_schedules[row_num][col_num]))
                    {
                        that.ScheduleForm = that.venue_schedules[row_num][col_num];
                        that.ScheduleForm.schedule_date = that.fields[row_num +1 ].ori_date;
                        that.ScheduleForm.schedule_id = that.schedule.id;
                    }
                }      
                that.dialogFormVisible = true;
            }
        },

        validataVenueCourse(cb)
        {
            this.$refs.venueCourseForm.validate(valid => {
                  var that = this;
                  if (valid) 
                  {
                    cb();
                    return true;
                  }  
                  else 
                  {
                    console.log('venueCourseForm error submit!!')
                    return false
                 }
            });
            return false;
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
                    var venue_id =  options[0].value;
                    that.getClasses(venue_id);
                } 
                else
                {
                    that.selectItemVisible = true;
                }    
            })
            .catch(function(error) {
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
                  var options =[], classMap =  [];
                  for (var i in data ) {
                    let label =  data[i].name;
                    options.push({value : data[i].id , label: label});
                    classMap[data[i].id] = label;
                  } 
                  that.classOptions = options;
                  that.classMap = classMap;
                })
                .catch(function(error) {
                  stack_error(error);
                }); 
        },

        venueChange(value) {
            this.getClasses(value);
        },

        /**
         * 处理某个指定特殊日期的课程
         */
        handleCourseRow()
        {
            this.$refs.ScheduleForm.validate(valid => {
            var that = this;
            if (valid) 
            {
            	// 数据操作入库
                var  week  = that.ScheduleForm.week;
                var section = that.ScheduleForm.section;
                var url = '/venueSchedules/saveScheduleExtend';
                this.$http({
                    method: 'POST',
                    url: url,
                    data: that.ScheduleForm
                })
                .then(function (response) {
                   
                    var { data } = response;
                    that.$message({
                        showClose: true,
                        message: data.message,
                        type: 'success'
                    });

                    let td_dom_class = 'td_course_time_' + that.ScheduleForm.week + '_' + that.ScheduleForm.section;
                    let class_id = that.ScheduleForm.class_id;
                    let class_name = that.classMap[class_id];
                    var html = class_name;
                    if(that.ScheduleForm.remark !='')
                    {
                        html += "<br><p class='text-error'>" + that.ScheduleForm.remark +  "</p>"
                    }
                     // 填充表格数据
                    $("." + td_dom_class).html(html)
                    that.dialogFormVisible = false;
                    
                })
                .catch(function (error) {
                    stack_error(error);
                });
            } 
            else 
            {
                console.log('error submit!!')
                return false
            }

            });     
        },

        // 保存 开始前需要做一数据校验 校验课表数据与课程时间数据
        onSubmit()
        {   
            var that = this;
            // 校验数据课程表时间是否有空 data_row course_times
            var flag = 0;
            var res = this.validateCourseTimes();
            if(!res)
            {
                this.$message.error('课程时间开始结束时间必填！');
                return ;
            }
            // 校验课程时间
            var check = this.validateVenueSchedule();
            if(!check)
            {
                this.$message.error('课程表数据不能为空！');
                return 
            }

            // 数据校验完成 提交数据到后台 
            // 时间数据进行数据格式转换 提交前 js 格林时间需要转换
            var formData = {};
            formData.course_times = that.tranformCourseTimes(that.course_times);
            formData.venue_schedules   = that.venue_schedules;
            formData.venue_course_form = that.tranformVenueCourseForm(that.venueCourseForm);
            
            // ajax 调用后台接口保存数据
            let url = '/venueSchedules' + (this.venueCourseForm.id ? '/' + this.venueCourseForm.id : '')
            let method = this.venueCourseForm.id ? 'put' : 'post';
            this.$http({
                method: method,
                url: url,
                data: formData
            })
            .then(function (response) {
                var { data } = response;
                that.$message({
                    showClose: true,
                    message: data.message,
                    type: 'success'
                });
                // 跳转到列表页
                // that.$message.info('ok');
                that.$router.push({ path: '/admin/venueSchedule/index' })
            })
            .catch(function (error) {
                    stack_error(error);
            });
            return;
        },

        validateCourseTimes()
        {
            var that = this;
            var check_flag = true;
            var data_row = this.venueCourseForm.course_count;
            for (let i=1;i<=data_row;i++)
            {
                if(isEmpty(that.course_times[i]))
                {
                    var dom_str = '.course_time_';
                    dom_str = dom_str + i + '_1';
                    $(dom_str).find("input").addClass('is-error');
                    check_flag = false;
                }
            }
            if(check_flag)
                return true;
            else 
                return false;
        },
        validateVenueSchedule()
        {
            if(isEmpty(this.venue_schedules))
                return false;
            else 
                return true;
        },

        tranformCourseTimes(course_times)
        {
            var datalimit = this.data_row;
            var result = [];
            for(let i=1;i<=this.data_row;i++) {
                if(!isEmpty(course_times[i])) {
                    var tempArr = course_times[i];
                    tempArr.forEach(function(value,index) {
                        if(isEmpty(result[i])) {
                            result[i] = [];
                        }
                        result[i][index] = parseTime(value);
　　　　             });
                }
            }
            return result;
        },

        tranformVenueCourseForm(venueCourseForm)
        {
            var date_between = venueCourseForm.date_between;
            var date_between_res = [];
            if(!isEmpty(date_between))
            {
                date_between.forEach(function(value,index) {
                    date_between_res[index] = parseTime(value);
　　　　         });
            }
            venueCourseForm.date_between = date_between_res;
            return venueCourseForm;
        },

        reset() 
        {
            this.params = {};
        },
    }
}
</script>

<style>
    .th_course_time {
        width: 10%;
    }
    .is-error{
        border-color: #ff4949;
    }
    .course_time_form > .el-select, .el-input {
        width: 90%;
    }
    .text-error {
        color: red;
    }

</style>