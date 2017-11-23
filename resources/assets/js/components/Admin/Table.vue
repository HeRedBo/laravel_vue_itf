<template>
    <div class="box-body tablew-responsive no-padding">
        
        <table :class="['table dataTable',stripped?'table-striped':'',hover?'table-hober':'']">
            <thead>
                <tr>
                    <th v-if="checkbox"></th>
                    <th v-for="field,key in fields"  
                        @click="headClick(field,key)"
                        :class="[field.sortable?'sorting':null,sort===key?'sorting_'+(sortDesc?'desc':'asc'):'']"

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
        <div class="col-sm-7">

          <!-- 分页组件  -->
        </div>
    </div>
</template>
<script>

// require('admin-lte/plugins/datatables/datatables.bootstrap.css');
require('icheck/skins/minimal/_all.css');
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
    },
    data () {
        return {
            items:[],
            total:0,
            currentPage:1,
            sort: null,
            sortDesc: true,

                tableData: [{
                    date: '2016-05-02',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1518 弄',
                    tag: '家'
                  }, {
                    date: '2016-05-04',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1517 弄',
                    tag: '公司'
                  }, {
                    date: '2016-05-01',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1519 弄',
                    tag: '家'
                  }, {
                    date: '2016-05-03',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1516 弄',
                    tag: '公司'
                  }],
            sort: null,
            listLoading : true,
            currentPage4: 1
        }
    },
    created() {
        this.listLoading = false
        console.log('23234534')
        //console.log(this.fields)
    
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
        formatter(row, column) {
        return row.address;
      },
      filterTag(value, row) {
        return row.tag === value;
      },

      handleSizeChange(val) {
        console.log(`每页 ${val} 条`);
      },
      handleCurrentChange(val) {
        console.log(`当前页: ${val}`);
      },
      handleClick(row,key) {
        console.log(row);
        console.log('asdfasd')
        console.log(key);
      }
    }
    
}
</script>