<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-inline pull-right">
                             <router-link :to="{path:'create'}" class="btn btn-sm btn-success">
                              用户新增
                            </router-link> 


                            <!-- 角色 -->
                             <div class="input-group input-group-sm">
                                 <el-select style="width:160px" v-model="params.role_id" placeholder="角色"  class="filter-item"  size="small"
                                    clearable multiple autosize
                                 >
                                        <el-option
                                               v-for="item in roleOptions"
                                               :key="item.value"
                                               :label="item.label"
                                               :value="item.value"
                                               >
                                        </el-option>
                                    </el-select>
                            </div>


                            <!-- 道馆 -->
                             <div class="input-group input-group-sm">
                                 <el-select style="width:160px" v-model="params.venue_id" placeholder="归属道馆"  class="filter-item"  size="small"
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

                                 <!--  数据搜索框 -->
                               <div class="input-group input-group-sm">
                                    <el-input 
                                        placeholder="昵称、邮箱、手机号" 
                                        v-model="params.query_name"
                                        size="small"
                                    >
                                    </el-input>
                                </div>

                                 <!--  按钮分组 -->
                                <div class="btn-group btn-group-sm">
                                    <button type="submit" class="btn btn-primary" @click="$refs.table.loadList()"><i class="fa fa-search"></i>
                                    </button>
                                    <a href="javascript:void(0)" class="btn btn-warning" @click="reset"><i class="fa fa-undo"></i></a>
                                </div>


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
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :del="del"
                > 

                <!-- 用户名 -->
                <template slot="username" slot-scope="item">
                     <el-popover
                        ref="popover"
                        placement="right"
                        width="170"
                        trigger="hover"
                        >
                    
                        <div style="text-align: right; margin: 0">
                        <img :src="item.item.picture" width="150px" height="150px" class="user-avatar"/>
                        </div>
                    </el-popover>
                    <img :src="item.item.picture" width="20px" height="20px" class="img-circle"  v-popover:popover />
                    {{item.item.username}}
                </template>
                 <!--  归属道馆 -->
                <template slot="venues_name" slot-scope="item">
                    <div style="width:120px">
                        <el-tag v-for="row in item.item.venues"
                        :key="row.id"
                        type="primary"
                        close-transition
                     >
                        {{row.name}}
                    </el-tag>
                    </div>
                </template>

                <template slot="roles_name" slot-scope="item">
                    <div style="width:120px">
                        <el-tag v-for="row in item.item.roles"
                        :key="row.id"
                        type="primary"
                        close-transition
                     >
                        {{row.name}}
                    </el-tag>

                    </div>
                    

                </template>

                <!-- 操作 -->
                <template slot="actions" slot-scope="item">
                    <div class="btn-group">
                        <a href="#" @click.prevent="view(item.item)" class="btn btn-success btn-xs">查看</a>
                        <router-link target="_blank" :to="{path:'update/'+ item.item.id}" class="btn  bg-orange btn-xs">编辑</router-link>
                        <a @click="handleDelete(item.item.id)" class="btn btn-danger btn-xs">删除</a>


                    </div>
                </template>
                </vTable>
            </div>
        </div>

        <div id="user_view_box" style="display: none">    
                <div class="box box-widget widget-user-2">
                    <div class="widget-user-header bg-yellow">
                        <div class="widget-user-image">
                            <img class="img-circle" :src="user.picture" alt="User Avatar"/>
                        </div>
                        <h3 class="widget-user-username">{{user.username}}</h3>
                        <h5 class="widget-user-desc">{{user.name}}</h5>
                    </div>
    
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                            <li><a href="#">手机号<span class="pull-right badge bg-blue">{{user.phone}}</span></a></li>
                            <li><a href="#">角色
                                <span v-for="row in user.roles" :key="row.id" class="pull-right badge bg-aqua">{{row.name}}</span>
                            </a></li>
                             <li>
                                <a href="#">隶属道馆
                                 <span v-for="row in user.venues" :key="row.id" class="pull-right badge bg-green">{{row.name}}</span>
                            </a></li>
                            <li><a href="#">邮箱<span class="pull-right badge bg-aqua">{{user.email}}</span></a></li>

                           <!--  <li><a href="#">Projects <span class="pull-right badge bg-blue">31</span></a></li>
                <li><a href="#">Tasks <span class="pull-right badge bg-aqua">5</span></a></li>
                <li><a href="#">Completed Projects <span class="pull-right badge bg-green">12</span></a></li>
                <li><a href="#">Followers <span class="pull-right badge bg-red">842</span></a></li> -->
                        </ul>
                    </div>
                </div>
            </div>


    </div>
</template>

<script>
import {stack_error} from 'config/helper';
export default {
    data() {
        const validateRolename = (rule, value, callback) => {
            this.checkRoleName(value, function(status) {
                if(status == 1)
                {
                    callback(new Error('道馆名称已存在'))
                } else {
                    callback()
                }
         });
      }

     return { 
            items: [],
            fields: {
                id: {label: 'ID', sortable: true},
                username: {label: '登陆名'},
                name: {label: '昵称'},
                venues_name : {label:'归属道馆', need:'venues'},
                roles_name : {label:'角色', need:'roles'},
                email: {label: '邮箱'},
                phone: {label: '手机号码'},
                created_at:{label:'创建时间', sortable: true},
                // updated_at:{label:'更新时间', sortable: true},
                actions : {label: '操作'}
            },
            ajax_url: "/user",
            params : {},
            currentPage: 1,
            perPage: 15,
            del: {url:'/user',title:'用户删除后不可恢复，确定要删除角色吗?'},
            listLoading: true,
            buttonLoading: false,
            user:{},
            roleOptions: [],
            venueOptions: [],
        }
    },
    created() {
       this.getRole();
       this.getVenues();
    },
    methods : {
      reset() 
      {
        this.params = {};
      },

      view(row) {
        this.user = row;
        setTimeout(function() {
                swal({
                    title:'',
                    html : $("#user_view_box").html(),
                    customClass : 'user_view_box'
                });
        }, 100);
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


    }
}

</script>

<style rel="stylesheet/scss" lang="scss">
 @import "resources/assets/styles/index";
</style>
