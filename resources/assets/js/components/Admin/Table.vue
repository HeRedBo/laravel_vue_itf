          <!-- <el-table-column  v-for="(field,key) in fields" :key="key"
            :prop="key"
            :label= "field.label"
            :sortable="field.sortable?true:false"


        >

        </el-table-column> -->


  <template>
    <div>

    <el-table
        :data="tableData"
        border
        :stripe="stripped?true:false"
        style="width: 100%"
         >

         <el-table-column  v-for="(field,key) in fields" :key="key"
            :prop="key"
            :label= "field.label"
            :sortable="field.sortable?true:false"
        >
        <slot name="key" >
            <span></span>
        </slot>
          

        </el-table-column>

       
 
    </el-table>

  
        <Pagination></Pagination>
    </div>
</template>
<script>
import Pagination from './Pagination.vue';
export default {
    components: {Pagination},
    props : {
        fields: {
            type: Object,
                default: () => {
            }
        },
        stripped: {
            type: Boolean,
            default: false
        },
    },
    data () {
        return {
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