<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header"> 
                    <div class="col-md-6">
                        <div class="form-inline">
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
                            <button type="submit" class="btn btn-primary" @click="getSignCalendar"><i class="fa fa-search"></i>
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
                                <th 
                                    v-for="field,key in fields"
                                    @click="headClick(field,key)"
                                 >
                                  {{field.label}}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr  v-for="r in course_count" >
                                <td v-for="j in total_column"
                                    :class="['td_course_time_' + j + '_' + r ]"
                                >
                                    
                                    <template>

                                        <p>
                                          <!-- 这一行显示数字与农历数字 -->
                                          <strong>
                                             {{
                                             venue_schedules[j] ? 
                                                venue_schedules[j][r] ?
                                                    venue_schedules[j][r].date_num
                                                    : ''
                                             : ''    
                                           }}
                                          </strong>
                                          <small>
                                              {{
                                             venue_schedules[j] ? 
                                                venue_schedules[j][r] ?
                                                    venue_schedules[j][r].lunar_name
                                                    : ''
                                             : ''    
                                             }}
                                          </small>
                                        </p>

                                        <template v-if="venue_schedules[j]&&venue_schedules[j][r]
                                                ">
                                               
                                          <el-tooltip
                                              v-for="row in venue_schedules[j][r].schedule_data"
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
                                         
                                    </template>
                                 <!--    <template v-else> -->
                                       <!-- {{j-data_start_column+1}} -->
                                       <!-- {{r}} -->
                                       

                                       <!--  <br>
                                        <span class='text-error'> -->
                                           <!--   {{ venue_schedules[j-data_start_column+1] ? venue_schedules[j-data_start_column+1][r] ? venue_schedules[j-data_start_column+1][r].remark
                                            ? venue_schedules[j-data_start_column+1][r].remark : '' :'' :'' }} -->
                                        <!-- </span>
                                    </template> -->
                                </td>
                            </tr>
                        </tbody>
                        

                    </table>
                </div>

                <div class="box-footer">
                

                </div>
            </div>
        </div>
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
            fields: {
              week_1: {label: '星期一'},
              week_2: {label: '星期二'},
              week_3: {label: '星期三'},
              week_4: {label: '星期四'},
              week_5: {label: '星期五'},
              week_6: {label: '星期六'},
              week_7: {label: '星期日'},
            },
            params :{
            
            },
            data_start_column: 3,
            total_column : 7,
            limit_data_row : 9,
            course_count : 5,   // 课程总数
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
            formLabelWidth: '110px',
            venueOptions: [],
            classOptions: [],
            classMap : [],
            selectItemVisible : false,
            course_times : {},
            venue_schedules : {},
            venueCourseForm:{},
            schedule : {

            },
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
       
        this.initParmas();
        this.getSignCalendar();  
    },
    methods:{

       initParmas()
       {
           this.params.venue_id = +this.$route.params.venue_id;
           this.params.student_id = +this.$route.params.student_id;
           //this.getSignCalendar();
       },
      
        getSignCalendar() 
        {
            var that = this, params = this.params;
            var url = '/student/getSignCalendar';
            this.$http({
                method :'GET',
                url : url,
                params : params
            })
            .then(function(response) 
            {
                let {data} = response;
                var  respondata = data.data
                console.log(respondata);
                // that.fields   = respondata.fields;
                that.schedule = respondata.schedule;
                that.course_count    = that.schedule.course_count;
              
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
            
        },

        inputChange(value,index) {
            var dom_str  = '.course_time'
            $(dom_str).find("input").removeClass('is-error');
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

                // 从新赋值
                //that.params.venue_id = +that.$route.params.venue_id ;
                
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

    .el-tag {
      margin-bottom: 5px;
      margin-left: 4px;
    }


</style>