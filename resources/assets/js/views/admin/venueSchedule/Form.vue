<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <el-form 
                        :model="venueCourseForm"
                        ref="venueCourseForm"
                        label-width="160px" 
                        class="el-form"
                        :rules="venueCourseFormRules">
                           <!-- 道馆 -->
                            <el-form-item label="归属道馆" 
                                v-show="selectItemVisible" 
                                prop="venue_id" 
                            >
                                <el-select 
                                    v-model="venueCourseForm.venue_id" 
                                    placeholder="请选择道馆" 
                                    style="width:200px"   
                                    @change="venueChange"
                                    size="small"
                                >
                                      <el-option
                                         v-for="item in venueOptions"
                                         :key="item.value"
                                         :label="item.label"
                                         :value="item.value"
                                         >
                                      </el-option>
                                    </el-select>
                            </el-form-item>
                            <el-form-item label="课表名称" prop="schedule_name" >
                                <el-input 
                                    v-model="venueCourseForm.schedule_name" 
                                    placeholder="课表名称"
                                    style="width:300px"  
                                    size="small"

                                 > 
                                 </el-input>
                            </el-form-item>

                            <!-- 节次数 -->
                            <el-form-item label="节次数" prop="course_count">
                                </el-input-number>
                                  <el-input
                                    size="small"
                                    style="width:300px"  
                                    placeholder="请输入节次数"
                                    v-model="venueCourseForm.course_count"
                                    @change="courseCountChange"
                                    >
                                </el-input>
                            </el-form-item>

                             <!-- 课程有效期 -->
                            <el-form-item label="课程有效期" prop="date_between">
                               <el-date-picker
                                  size="small"
                                  v-model="venueCourseForm.date_between"
                                  type="daterange"
                                  placeholder="选择日期范围">
                                </el-date-picker>
                            </el-form-item>

                            <!-- 状态 -->
                            <el-form-item label="启用状态">
                                <el-radio-group v-model="venueCourseForm.status">
                                    <el-radio :label="0">否</el-radio>
                                    <el-radio :label="1">是</el-radio>
                                </el-radio-group>
                            </el-form-item>    
                    </el-form>                    
                </div>

                <div class="box-body tablew-responsive no-padding">
                    <table class="table table-bordered dataTable table-striped table-hover">
                        <thead>
                            <tr>
                                <th v-for="field,key in fields" 
                                     :class="['th_' + key ]"
                                >
                                    {{field.label}}
                                </th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr  v-for="r in venueCourseForm.course_count" >
                                <td v-for="j in total_column" @click="tdClick(j-data_start_column+1,r)"
                                    :class="['td_course_time_' + (j-data_start_column+1) + '_' + r ]"
                                >
                                    <template v-if="j==1">
                                        <el-time-picker
                                            is-range
                                            :class="['course_time','course_time_' + r + '_' + j]"
                                            size="small"
                                            v-model="course_times[r]"
                                            @change="inputChange"
                                            @blur="courseTimeChange(r)"
                                            placeholder="选择时间范围">
                                        </el-time-picker>

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
                    <button type="submit" @click="$router.back()" class="btn btn-default">返回</button>
                    <!-- <button type="submit" @click="$router.push({ path: '/admin/venueSchedule/index' })" class="btn btn-default">返回</button> -->
                    <button type="submit" @click="onSubmit" class="btn btn-info">
                         {{ venueCourseForm.id ? '更新' : '添加' }}
                    </button>

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

                        <!-- 班级 -->
                        <el-form-item label="班级"  :label-width="formLabelWidth" prop="class_id">
                            <el-select v-model="ScheduleForm.class_id" placeholder="请选择班级" size="small">
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
    props: {
        venueCourseForm: {
            type: Object,
            default() {
                return {}
            }
        },
        course_times : {
            type: Object,
            default() {
                return {}
            }
        },

        venue_schedules : {
            type: Object,
            default() {
                return {}
            }
        }
    },


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
            fields: {
                course_time: {label: '时间'},
                section:{label:'节次'},
                week_1:{label:'周一'},
                week_2:{label:'周二'},
                week_3:{label:'周三'},
                week_4:{label:'周四'},
                week_5:{label:'周五'},
                week_6:{label:'周六'},
                week_7:{label:'周日'}
            },
            data_start_column: 3,
            total_column : 9,
          
            limit_data_row : 9,

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
                class_id: [
                    { required: true, type: 'number', message: '班级不能为空', trigger: 'blur' }
                ]
            },
            formLabelWidth: '110px',
            venueOptions: [],
            classOptions: [],
            classMap : [],
            selectItemVisible : false,
        } 
    },
    created() {
        this.getUserVenus();
    },

    methods:{

        initData(){
        
        },

        inputChange(value,index) {
            //console.log(this.course_times)
            var dom_str  = '.course_time'
            $(dom_str).find("input").removeClass('is-error');
        },

        courseTimeChange(r_key)
        {
            var row_course_times = this.course_times[r_key];
            var total_course_time = this.course_times;
            var dom_str  = '.course_time_' + r_key + '_1';
            $(dom_str).find("input").removeClass('is-error');
            if(!isEmpty(row_course_times))
            {
                var row_course_start_time = Date.parse(row_course_times[0]);
                var row_course_end_time   = Date.parse(row_course_times[1]);

                for (let j in total_course_time) 
                {
                    console.log(j);
                    var t_course_time = total_course_time[j];
                    console.log(t_course_time);

                    if(j == r_key) 
                        continue;
                    if(!isEmpty(t_course_time))
                    {

                        // 立一个 flag
                        var flag_1 = true,flag_2 = true,flag_3= true;
                        var t_start_time_timestamp  = Date.parse(t_course_time[0]);
                        var t_end_time_timestamp    = Date.parse(t_course_time[1]);
                        //  比较数据大小
                        if(
                            (t_start_time_timestamp <= row_course_start_time ) 
                            && (row_course_start_time <= t_end_time_timestamp))
                        {
                            flag_1 = false;
                        }
                        if((t_start_time_timestamp <= row_course_end_time)  &&
                            (row_course_start_time <= t_end_time_timestamp)
                            )
                        {
                            flag_2 = false;
                        }

                        // 包围 时间段的判断
                        if( (row_course_start_time <= t_start_time_timestamp) &&
                            (row_course_end_time >= t_end_time_timestamp)
                        )
                        {
                            flag_3 = false;
                        }
                        
                        
                        if(!flag_1 || !flag_2) 
                        {
                            $(dom_str).find("input").addClass('is-error');
                            this.$message.error('课程时间存在冲突，请检查!');
                            return false;
                        }
                        if(!flag_3)
                        {
                            $(dom_str).find("input").addClass('is-error');
                            this.$message.error('课程时间存在冲突，请检查!');
                            return false;
                        }
                        

                    }
                }

            }

        },
        clickDebug(r_key)
        {
            console.log(r_key);
        },

        tdClick(row_num, col_num)
        {

            // 校验道馆的基本信息必填项
            var that = this;
            var chat_flag_1 = this.validataVenueCourse(function() 
            {
                // 处理每个的数据
                if(((1<=row_num) && (row_num <=7)) && ((col_num >=1) && (col_num <= that.data_row)))
                {  
                    if(isEmpty(that.venue_schedules[row_num]) || (!isEmpty(that.venue_schedules[row_num]) && isEmpty(that.venue_schedules[row_num][col_num])))
                    {
                        that.ScheduleForm = {};
                        that.ScheduleForm.week = row_num;
                        that.ScheduleForm.section = col_num;
                        that.ScheduleForm.remark = '';
                    } 
                    else 
                    {
                        if(!isEmpty(that.venue_schedules[row_num][col_num]))
                        {
                            that.ScheduleForm = that.venue_schedules[row_num][col_num];
                        } 
                       
                    }                
                    that.dialogFormVisible = true;
                }
            });
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
                            //that.studentForm.venue_id =  venue_id;
                            that.getClasses(venue_id);
                        } 
                        else
                        {
                            that.selectItemVisible = true;
                        }
                        // that.showCreateButton = true;
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

        handleCourseRow()
        {
            this.$refs.ScheduleForm.validate(valid => {
            var that = this;
            if (valid) {
                var  week = that.ScheduleForm.week;
                var section = that.ScheduleForm.section;
                if(isEmpty(that.venue_schedules[week]))
                    that.venue_schedules[week] = [];
                    
                that.venue_schedules[week][section] = that.ScheduleForm;
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
                return true;
            } 
            else 
            {
                console.log('error submit!!')
                return false
            }

            });     
        },
        courseCountChange(value)
        {
            var patrn = /^[0-9]*$/;
            if (patrn.exec(value) == null || value == "" || value > this.limit_data_row) 
            {
                return false;
            }
            this.data_row = +value;
            this.venueCourseForm.course_count =  +value;
            return true;
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
            console.log(check);
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
                //that.$message.info('ok');
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
            console.log(this.venue_schedules)

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
        }
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