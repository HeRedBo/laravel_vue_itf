<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
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
                            <tr  v-for="r in data_row" >
                                <td v-for="j in total_column" @click="tdClick(j-data_start_column+1,r)">
                                    <template v-if="j==1">
                                        <el-time-picker
                                            is-range
                                            :class="['course_time','course_time_' + r]"
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
                                       {{j-data_start_column+1}}
                                       {{r}}
                                    </template>
                                    
                                </td>
                            </tr>
                        </tbody>
                        

                    </table>
                </div>
            </div>
        </div>


        <!-- Form -->
            <el-dialog title="设置课程" :visible.sync="dialogFormVisible" class="course_time_form" >
              <div class="row">
                  <div class="col-md-10">
                      <el-form :model="ScheduleForm">

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

                         <!-- 道馆 -->
                        <el-form-item label="道馆" v-show="selectItemVisible" :label-width="formLabelWidth" >
                                <el-select v-model="ScheduleForm.venue_id" placeholder="请选择道馆" size="small" @change="venueChange">
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
                        <el-form-item label="班级"  :label-width="formLabelWidth">
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
                <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
              </div>

            </el-dialog>

    </div>
</template>
<script>
import {stack_error,isEmpty} from 'config/helper';
export default {
    data() {
        return {
            fields: {
                    course_time: {label: '时间'},
                    section:{label:'节次'},
                    weak_1:{label:'周一'},
                    weak_2:{label:'周二'},
                    weak_3:{label:'周三'},
                    weak_4:{label:'周四'},
                    weak_5:{label:'周五'},
                    weak_6:{label:'周六'},
                    weak_7:{label:'周日'}
            },
            data_start_column: 3,
            total_column : 9,
            data_row : 6,
            class_Options : [],
            venueCourseForm:{
                classes:[]
            },

            course_times: [],

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
            selectItemVisible : false,
        }
       
    },

    created() {
        this.getUserVenus();
    },

    methods:{
        inputChange(value,index) {
            //console.log(this.course_times)
            console.log(value)
            var dom_str  = '.course_time'
            $(dom_str).find("input").removeClass('is-error');
        },

        courseTimeChange(r_key)
        {
            var row_course_times = this.course_times[r_key];
            var total_course_time = this.course_times;
            var dom_str  = '.course_time_' + r_key;
            $(dom_str).find("input").removeClass('is-error');
            if(!isEmpty(row_course_times))
            {
                var row_course_start_time = Date.parse(row_course_times[0]);
                var row_course_end_time   = Date.parse(row_course_times[1]);

                console.log(total_course_time)
                for (let j in total_course_time) {
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
                        console.log(flag_1)
                        console.log(flag_2)
                        console.log(flag_3)
                        
                        if(!flag_1 || !flag_2) 
                        {
                            $(dom_str).find("input").addClass('is-error');
                            this.$message.error('课程时间存在冲突，请检查1');
                            return false;
                        }
                        if(!flag_3)
                        {
                            $(dom_str).find("input").addClass('is-error');
                            this.$message.error('课程时间存在冲突，请检查1');
                            return false;
                        }
                        
                    }
                }

            }

        },
        clickDebug(r_key)
        {
            console.log('click');
            console.log(r_key);
        },

        tdClick(row_num, col_num)
        {

            console.log(row_num + ':' + col_num);
            if(((1<=row_num) && (row_num <=7)) && ((col_num >=1) && (col_num <= this.data_row)))
            {
                console.log("hello world");
                this.ScheduleForm.week = row_num;
                this.ScheduleForm.section = col_num;
                this.dialogFormVisible = true;
            }
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
</style>