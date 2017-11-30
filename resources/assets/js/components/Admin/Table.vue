<template>
    <div class="box-body tablew-responsive no-padding">
        
        <table :class="['table table-bordered dataTable',stripped?'table-striped':'',hover?'table-hover':'']">
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
                <tr v-for="item in _items" :key="items_key" :class="[item.state?'table-'+item.state:null]">
                    <td v-if="checkbox"><input type="checkbox" /></td>
                    <td v-for="(field,key) in fields">
                        <slot :name="key" :value="field.need?item[field.need][key]:item[key]" :item="item" >
                            {{field.need?item[field.need][key]:item[key]}}
                        </slot>
                        
                    </td>
                </tr>
            </tbody>            
        </table>
        <div class="col-sm-7 pagination-container" v-if="totalRows>initPage" style="margin-bottom:15px;">
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
import {stack_error} from 'config/helper';
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
        }

    },
    data () {
        return {
            items:[],
            sort: null,
            sortDesc: true,
            currentPage: 1,
            pageSizes: [15, 20, 50, 100, 200],
            pageSize: 15,
            initPage : 15,
            layouts: 'total, sizes, prev, pager, next, jumper',
            totalRows: 0,
            params: {},
        }
    },
    created() {
        
        this.loadList(); 
        this.pageSize = this.perPage;
        this.initPage = this.perPage;
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
            if(typeof this.params !== 'undefined') {
                params = Object.assign(params, this.params);
            }

            this.$http({
                method :'GET',
                url : url,
                params : params
           })
           .then(function(response) {
                let {data} = response;
                let responseData = data.data;
                that.totalRows = responseData.total;
                that.pageSize  =  parseInt(responseData.per_page);
                that.items     = responseData.data;
            })
            .catch(function(error) 
            {
                console.log(error);
                stack_error(error);
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