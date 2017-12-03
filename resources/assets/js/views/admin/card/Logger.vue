<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                        <div class="form-inline pull-right">
                           <!--  搜索时间的选择框 -->
                            <div class="form-group" >
                                <el-date-picker
                                      v-model="params.search_time_between"
                                      type="datetimerange"
                                      :picker-options="pickerOptions2"
                                      placeholder="选择时间范围"
                                      align="right"
                                      size="small"
                                >
                                </el-date-picker>
                            </div>
                           <!--  数据搜索框 -->
                           <div class="input-group input-group-sm" >
                                <el-input 
                                    placeholder="请输入操作人姓名" 
                                    v-model="params.operator_name"
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
                <vTable ref="table"
                        stripped
                        hover
                        :ajax_url="ajax_url"
                        :params="params"
                        :items="items"
                        :fields="fields"
                        :current-page="currentPage"
                        :per-page="perPage"
                        :del="del"
                >
                    <template slot="actions" slot-scope="item">
                        <div class="btn-group">
                            <router-link :to="{ path: 'update/'+item.item.id}" class="btn bg-orange btn-xs">编辑</router-link>
                            <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a>
                        </div>
                    </template>

                </vTable>
            </div>
        </div>



    </div>
</template>
<style type="text/css">
    .el-date-editor--datetimerange.el-input  {
        width: 320px !important;
    }
</style>
<script>
    import {parseTime} from 'config/helper';
    export default {
       data () {
            return {
                items: [],
                fields: {
                    id: {label: 'ID', sortable: true},
                    operator_name: {label: '操作人'},
                    created_at:{label:'操作时间', sortable: true},
                    operation: {label: '操作'},
                    field: {label: '字段'},
                    oldValue: {label: '旧值'},
                    newValue: {label: '新值'}
                },
                ajax_url: "/card/cardLogger",
                params: {operator_name: ''},
                currentPage: 1,
                perPage: 15,
                del: {url:'/admin/user',title:'确定要删除用户吗?',successText:'删除后台用户成功!'},
                pickerOptions2: {
                shortcuts: [{
                    text: '最近一周',
                    onClick(picker) {
                      const end = new Date();
                      const start = new Date();
                      start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                      picker.$emit('pick', [start, end]);
                    }
                    }, {
                    text: '最近一个月',
                    onClick(picker) {
                      const end = new Date();
                      const start = new Date();
                      start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                      picker.$emit('pick', [start, end]);
                    }
                  }, {
                    text: '最近三个月',
                    onClick(picker) {
                      const end = new Date();
                      const start = new Date();
                      start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                      picker.$emit('pick', [start, end]);
                    }
                }]
              },
            }
        },
        created() {
          this.params.card_id = this.$route.params.id
        },
        methods: {  
            convertSearchTime: function(){ 
              console.log(this.params.search_time_between); 
              if(this.params.search_time_between)
               {
                    var search_time = [];
                    this.params.search_time_between.forEach(function(value,index,array) {
                        if(value)
                            search_time.push(parseTime(value))
                    });
                    this.params.search_time = search_time;
               }
            },
            reset : function() 
            {
                this.params = {};
            }
        },  
        watch: {  
            'params.search_time_between': 'convertSearchTime',  
        }  
    }
</script>

