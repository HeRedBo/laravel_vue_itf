<template>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
           <div class="row">
              <div class="col-md-2">
                <span>
                    <router-link :to="{path:'create'}" class="btn btn-sm btn-success">
                      添加课程表
                    </router-link>
                </span>  
              </div>  
              <div class="col-md-10">
                <div class="form-inline pull-right">
                  <!--  数据搜索框 -->
                  <!--  <div class="input-group input-group-sm" >
                        <el-input 
                            placeholder="请输入道馆名称" 
                            v-model="params.name"
                            size="small"
                        >
                        </el-input>
                   </div> -->


                    <!--  按钮分组 -->
                  <!--   <div class="btn-group btn-group-sm">
                        <button type="submit" class="btn btn-primary" @click="$refs.table.loadList()"><i class="fa fa-search"></i>
                        </button> -->
                       <!--  <a href="javascript:void(0)" class="btn btn-warning" @click="reset"><i class="fa fa-undo"></i></a> -->
                  <!--   </div> -->
                </div>  
              </div>
           </div>
        </div>
         <vTable ref="table"
                        stripped
                        hover
                        :searchType=1
                        :ajax_url="ajax_url"
                        :params="params"
                        :items="items"
                        :fields="fields"
                        :current-page="currentPage"
                        :per-page="perPage"
                        :del="del"
                >

                 <!--  归属道馆 -->
                <template slot="venue_name" slot-scope="item">
                    <span>{{item.item.venues.name}}</span>
                </template>

                <!-- 状态 -->
                <template slot="status" slot-scope="item">
                    <a href="javascript:void(0)" data-toggle="tooltip" :title="item.item.status==1 ? '启用':'未启用'">
                        <i @click="changeStatus(item.item)" :class="['fa','fa-circle',item.item.status==1?'text-success':'text-danger']"></i>
                    </a>
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

                      <a href="javascript:void(0)"  v-show="item.item.status==0" @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a>
          <!--               <router-link target="_blank"  :to="{path:'logger/'+ item.item.id}" class="btn bg-info btn-xs">操作日志</router-link> -->
                        
                        <!-- <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a> -->
                    </div>
                </template>

                </vTable>
      </div>
    </div>

      <div id="schedule_view_box" style="display: none">
            <!-- Widget: user widget style 1 -->
            <div class="box box-widget">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <table class="table  table-bordered" style="font-size: 14px">
                    <tbody>
                    <tr>
                        <th>课程表ID</th>
                        <td>{{schedule.id}}</td>
                        <th>课表名称</th>
                        <td>{{schedule.schedule_name}}</td>
                    </tr>

                    <tr>
                         <th>隶属道馆</th>
                         <td>{{schedule.venues.name}}</td>
                         <th>每日课程总数</th>
                        <td>{{schedule.course_count}}</td>
                    </tr>

                     <tr>
                        <th>课程有效期开始时间</th>
                        <td>{{schedule.start_time}}</td>
                        <th>课程有效期结束时间</th>
                        <td>{{schedule.end_time}}</td>
                    </tr>

                     <tr>
                        <th>创建时间</th>
                        <td>{{schedule.created_at}}</td>  
                        <th>最新更新时间</th>
                        <td>{{schedule.updated_at}}</td>
                    </tr>
                    <tr>
                        <th>启用状态</th>
                        <td>{{schedule.status==1? '启用':'未启用' }}</td>
                         <th>操作人</th>
                        <td>{{schedule.operator.name }}</td> 
                    </tr>

                   <!--  <tr>
                        <th></th>
                        <td></td>
                        <th></th>
                        <td></td>
                    </tr> -->
                    </tbody>
                </table>
            </div>
            <!-- /.widget-user -->
      </div>

  </div>




</template>

<script>

$(function() {
    $('.box').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
});

</script>
<script>
import {stack_error,parseSearchParam} from 'config/helper';
export default {
    data() {
      return {
        items: [],
        fields: {
                id: {label: 'ID', sortable: true},
                schedule_name: {label: '课表名称'},
                venue_name: {label: '道馆名称'},
                course_count: {label: '每日课程数'},
                start_time: {label: '课程开始时间'},
                end_time: {label: '课程结束时间'},
                status: {label: '状态'},
                created_at:{label:'创建时间', sortable: true},
                // updated_at:{label:'更新时间', sortable: true},
                operator_name : {label:'操作人', need:'operator'},
                actions : {label: '操作'}
        },
        ajax_url: "/venueSchedules",
        params: {},
        currentPage: 1,
        perPage: 15,
        schedule : {
          operator :{},
          venues : {}
        },
        del: {url:'/venueSchedules',title:"删除后数据无法恢复! 确定要删除课程表吗?",successText:'道馆信息删除成功!'}
        
      }
    },
    created() {
      
    },
    methods: 
    {
       changeStatus(row) 
        {

            var status = row.status;
            var id = row.id;
            if(status == 1)
              return false;
            var that = this;
            var changeStatus = status ? 0 : 1;
            swal({
            title: '修改课程表状态?',
            text: '你是否要继续执行该操作!?', // 
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '确认',
            cancelButtonText: '取消',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            }).then(function() 
            {

              var url =  '/venueSchedules/changeStatus';
              that.$http({
                method :'GET',
                url : url,
                params : {
                    id : id,
                    status : changeStatus
                }
              })
              .then(function(response) {
                let {data} = response;
                that.$message({
                    showClose: true,
                    message: data.message,
                    type: 'success'
                });
                // 从新刷新页面数据
                that.$refs.table.loadList();
            })
            .catch(function(error) {
                stack_error(error);
            });
            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                that.$message({
                    showClose: true,
                    message: '你已取消修改数据状态',
                });
                // if (dismiss === 'cancel') {
                //     swal(
                //     'Cancelled',
                //     'Your imaginary file is safe :)',
                //     'error'
                //     )
                // }
            })
           
            
         },
         getcardTypeOptions() {
            var url = '/card/cardTypeOptions', that = this;
                this.$http({
                       method :"GET",
                       url : url,
                })
                .then(function(response) {
                      var responseJson = response.data,data = responseJson.data
                      var options      = [];
                      that.typeOptions = data;
                    })
                .catch(function(error) {
                      stack_error(error);
                }); 
        },
         view(data) {
            console.log(data)
            this.schedule = data;
            setTimeout( function() {
                swal({
                    title : data.schedule_name,
                    width: '50%',
                    html : $('#schedule_view_box').html(),
                });
            },100);
        },

     

      reset() 
      {
            this.params = {};
      },
    }
}
</script>