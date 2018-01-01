<template>
    <div class="box-body tablew-responsive no-padding">

        <table :class="['table table-bordered dataTable',stripped?'table-striped':'',hover?'table-hover':'']"
                v-loading="listLoading"
                element-loading-text="拼命加载中"
        >
            <thead>
                <tr>
                    <th v-if="checkbox"></th>
                    <th @click="headClick(field,key)"
                        :class="[field.sortable?'sorting':null,sort===key?'sorting_'+(sortDesc?'desc':'asc'):'']"
                        v-for="field,key in fields"
                    >
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

            <tr  v-else v-for="item in _items" :key="items_key" :class="[item.state?'table-'+item.state:null]">
                <td v-if="checkbox"><input type="checkbox"/></td>
                <td v-for="(field,key) in fields">
                <slot :name="key" :value="field.need?item[field.need][key]:item[key]" :item="item">{{field.need?item[field.need][key]:item[key]}}</slot>
                </td>
            </tr>


                <!-- <tr v-for="item in _items" :key="items_key" :class="[item.state?'table-'+item.state:null]">
                    <td v-if="checkbox"><input type="checkbox" /></td>
                    <td v-for="(field,key) in fields">
                        <slot :name="key" :value="field.need?item[field.need][key]:item[key]" :item="item" >
                            {{field.need?item[field.need][key]:item[key]}}
                        </slot>

                    </td>
                </tr> -->
            </tbody>
        </table>
        <div v-show="!listLoading" class="col-sm-7 pagination-container" v-if="totalRows>initPage" style="margin-bottom:15px;">
          <!-- 分页组件  -->
            <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="currentPage"
                :page-sizes="pageSizes"
                :page-size="pageSize"
                :layout="layouts"
                :total="totalRows"
            >
            </el-pagination>
        </div>
    </div>
</template>

<script>

require('admin-lte/plugins/datatables/dataTables.bootstrap.css');
require('icheck/skins/minimal/_all.css');
import {stack_error,parseSearchParam,isEmpty} from 'config/helper';

export default {

    props : {
        sortable: {
            type: Boolean,
            default: false
        },
        checkbox: {
            type: Boolean,
            default: false
        },
        fields: {
            type: Object,
                default: () => {
            }
        },
        stripped: {
            type: Boolean,
            default: false
        },
        hover: {
            type: Boolean,
            default: false
        },
        items_key: {
            type: String,
            default: null
        },
        stripped: {
            type: Boolean,
            default: false
        },
        ajax_url : {
            type: String,
            default: null
        },
        perPage: {
            type: Number,
            default: null
        },
        params: {
            type: Object,
            default: () => {}
        },
        del: {
            type: Object,
            default: () => {}
        },
        searchType : {
            type: Number,
            default: 1
        },
        empty_text : {
            type: String,
            default : '暂无数据'
        }

    },
    data () {
        return {
            items:[],
            sort: null,
            sortDesc: true,
            currentPage: 1,
            pageSizes: [ 15, 20, 50, 100, 200],
            pageSize: 15,
            initPage : 15,
            layouts: 'total, sizes, prev, pager, next, jumper',
            totalRows: 0,
            listLoading: true,
        }
    },
    created() {
      
        if(this.perPage)
        {
          this.pageSize = this.perPage;
          this.initPage = this.perPage;
          if(this.pageSizes.indexOf(this.perPage) !== false ) {
            this.pageSizes.unshift(this.perPage);
          }
        }
        this.loadList();


    },

    computed:{

        _items() {
            if(!this.items)
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
    methods :{
        loadList : function() {
            var that = this;
            var url = this.ajax_url;
            var orderBy = this.sort;
            var sortedBy = this.sortDesc?'desc':'asc';
            var params = { pageSize :this.pageSize, page:this.currentPage, orderBy:orderBy,sortedBy:sortedBy}
            console.log(params);
            if(this.searchType == 1)
            {
                if(typeof this.params !== 'undefined') {
                    params = Object.assign(params, this.params);
                }
            }
            else if(this.searchType == 2)
            {
                params.searchJoin = 'and';
                var search_query = parseSearchParam(this.params);
                params.search = search_query;

            }

            this.listLoading = true;
            this.$http({
                method :'GET',
                url : url,
                params : params
           })
           .then(function(response) {
                that.listLoading = false
                let {data} = response;
                let responseData = data.data;
                that.totalRows = responseData.total;
                that.pageSize  =  parseInt(responseData.per_page);
                that.items     = responseData.data;
            })
            .catch(function(error)
            {
                that.listLoading = false
                console.log(error);
                stack_error(error);
            });

        },

         onDel: function (id) {
            var that = this;
            swal({
                title: "确定要删除?",
                text: that.del.title,
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "取消",
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "删除",
                closeOnConfirm: true
            }).then(function () {
                that.listLoading = true
                var url = that.del.url+'/'+id;
                console.log(url)
                that.$http({
                    method :'DELETE',
                    url : url,
                    params : {}
                })
              .then(function(response) {
                that.listLoading = false;
                let {data} = response;
                var message = that.del.successText ? that.del.successText : data.message;
                toastr.success(message);
                that.loadList();
            })
            .catch(function(error) {
              that.listLoading = false;
              stack_error(error);
            });
            });
        },

        headClick(field,key) {
            if (!field.sortable) {
                return;
            }
            if (key === this.sort) {
                this.sortDesc = !this.sortDesc;
            }
            this.sort = key;
            this.loadList();
        },

        formatter(row, column) {
            return row.address;
        },

        filterTag(value, row) {
            return row.tag === value;
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
    }

}
</script>
<style>
    .td-empty {
        text-align: center;
    }
</style>
