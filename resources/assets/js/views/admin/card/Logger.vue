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
                                <button type="submit" class="btn btn-primary" @click="loadList"><i class="fa fa-search"></i>
                                </button>

                                <a href="javascript:void(0)" class="btn btn-warning" @click="reset"><i class="fa fa-undo"></i></a>
                            </div>

                        </div>
                </div>
                    <div class="box-body tablew-responsive no-padding">
                        <table class="table table-bordered dataTable table-striped table-hover" v-loading="listLoading"
                            element-loading-text="拼命加载中">
                            <thead>
                                <tr>
                                    <th @click="headClick(field,key)" :class="[field.sortable?'sorting':null,sort===key?'sorting_'+(sortDesc?'desc':'asc'):'']"
                                        v-for="field,key in fields">
                                        {{field.label}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="_items.length==0">
                                    <td :colspan="[checkbox?1+Object.keys(fields).length:Object.keys(fields).length]" class="td-empty">
                                        <slot name="empty">{{empty_text}}</slot>
                                    </td>
                                </tr>

                                <template v-else v-for="(item,index) in _items">
                                    <tr v-for="(row,index) in item.log" :class="[item.state?'table-'+item.state:null]">
                                        <td v-if="index==0" :rowspan="index==0&&item['field_count']?item['field_count']:1" >{{item['id']}}</td>
                                        <td v-if="index==0" :rowspan="index==0&&item['field_count']?item['field_count']:1">{{item['operator_name']}}</td>
                                        <td v-if="index==0" :rowspan="index==0&&item['field_count']?item['field_count']:1">{{item['created_at']}}</td>
                                        <td v-if="index==0" :rowspan="index==0&&item['field_count']?item['field_count']:1">{{item['operation']}}</td>
                                        <td>{{row.field}}</td>
                                        <td><span v-if="!row.oldValue">--</span><del v-else>{{row.oldValue}}</del></td>
                                        <td>{{row.newValue}}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div v-show="!listLoading" class="col-sm-7 pagination-container" v-if="totalRows>initPage" style="margin-bottom:15px;">
                            <!-- 分页组件  -->
                            <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="currentPage" :page-sizes="pageSizes"
                                :page-size="pageSize" :layout="layouts" :total="totalRows">
                            </el-pagination>
                        </div>
                    </div>


               
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
                filter_fields : ['field','oldValue','newValue'],
                ajax_url: "/card/cardLogger",
                checkbox:false,
                params: {operator_name: ''},
                perPage: 15,
                listLoading: true,
                sort: null,
                sortDesc: true,
                currentPage: 1,
                pageSizes: [15, 20, 50, 100, 200],
                pageSize: 15,
                initPage: 15,
                layouts: 'total, sizes, prev, pager, next, jumper',
                totalRows: 0,
                empty_text:'暂无数据',
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
          this.loadList();
        },
        computed: {

                    _items() {
                        if (!this.items)
                            return []
                        let items = this.items;
                        const fix = v => {
                            if (v instanceof Object) {
                                return Object.keys(v).map(k => fix(v[k])).join(' ');
                            }
                            return String(v);
                        };
                        return items;
                    }
                },
        methods: {  
            convertSearchTime: function(){ 
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

            loadList: function () {
                var that = this;
                var url = this.ajax_url;
                var orderBy = this.sort;
                var sortedBy = this.sortDesc ? 'desc' : 'asc';
                var params = { pageSize: this.pageSize, page: this.currentPage, orderBy: orderBy, sortedBy: sortedBy }
                if (typeof this.params !== 'undefined') {
                    params = Object.assign(params, this.params);
                }
                this.listLoading = true;
                this.$http({
                    method: 'GET',
                    url: url,
                    params: params
                })
                    .then(function (response) {
                        that.listLoading = false
                        let { data } = response;
                        let responseData = data.data;
                        that.totalRows = responseData.total;
                        that.pageSize = parseInt(responseData.per_page);
                        that.items = responseData.data;
                    })
                    .catch(function (error) {
                        that.listLoading = false
                        console.log(error);
                        stack_error(error);
                    });

            },

            headClick(field, key) {
                if (!field.sortable) {
                    return;
                }
                if (key === this.sort) {
                    this.sortDesc = !this.sortDesc;
                }
                this.sort = key;
                this.loadList();
            },
            reset : function() 
            {
                this.params = {};
            },
             handleSizeChange(val) {
                this.params.pageSize = val;
                this.loadList();
            },
            handleCurrentChange(val) {
                this.params.page = val;
                this.loadList();
                console.log(`当前页: ${val}`);
            },
            inArray(search, array) {
                for (var i in array) {
                    if (array[i] == search) {
                        return true;
                    }
                }
                return false;
            }
        },  
        watch: {  
            'params.search_time_between': 'convertSearchTime',  
        }  
    }
</script>

<style>
    .td-empty {
        text-align: center;
    }
</style>

