<template>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
           <div class="row">
              <div class="col-md-2">
                <span>
                    <router-link :to="{path:'create'}" class="btn btn-sm btn-success">
                      添加
                    </router-link>
                </span>  
              </div>  
              <div class="col-md-10">
                <div class="form-inline pull-right">
                  <!--  数据搜索框 -->
                   <div class="input-group input-group-sm" >
                        <el-input 
                            placeholder="请输入道馆名称" 
                            v-model="params.name"
                            size="small"
                        >
                        </el-input>
                   </div>
                    <!--  按钮分组 -->
                    <div class="btn-group btn-group-sm">
                        <button type="submit" class="btn btn-primary" @click="$refs.table.loadList()"><i class="fa fa-search"></i>
                        </button>
                       <!--  <a href="javascript:void(0)" class="btn btn-warning" @click="reset"><i class="fa fa-undo"></i></a> -->
                    </div>
                </div>  
              </div>
           </div>
        </div>
         <vTable ref="table"
                        stripped
                        hover
                        :searchType=2
                        :ajax_url="ajax_url"
                        :params="params"
                        :items="items"
                        :fields="fields"
                        :current-page="currentPage"
                        :per-page="perPage"
                        :del="del"
                >
              <template slot="LOGO" slot-scope="item">
                       <el-popover
                        ref="popover"
                        placement="right"
                        width="170"
                        trigger="hover"
                     >
                     <div style="text-align: right; margin: 0">
                        <img :src="item.item.logo" width="150px" height="150px" class="user-avatar "/>
                     </div>
                    </el-popover>
                      <img :src="item.item.logo" width="40px" height="40px" class="user-avatar img-circle" v-popover:popover />
                </template>

                <!-- 操作 -->
                <template slot="actions" slot-scope="item">
                    <div class="btn-group">
                      <!--   <a href="javascript:;" @click="view(item.item)" class="btn btn-success btn-xs">查看</a> -->
                        <!-- v-show="item.item.status == 0"  -->
                      <router-link :to="{path:'update/'+  item.item.id}" class="btn bg-orange btn-xs">编辑</router-link>
                        <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a>
          <!--               <router-link target="_blank"  :to="{path:'logger/'+ item.item.id}" class="btn bg-info btn-xs">操作日志</router-link> -->
                        
                        <!-- <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a> -->
                    </div>
                </template>

                </vTable>
      </div>
    </div>
  </div>

</template>
<script type="text/javascript">

jQuery(document).ready(function($) {
  $('.box').popover({
    selector:'[data-toggle="popover"]'
  })
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
                name: {label: '道馆名称'},
                LOGO: {label: 'LOGO'},
                province: {label: '省'},
                city: {label: '市'},
                area: {label: '区'},
                address:{label:'详情地址'},
                created_at:{label:'创建时间', sortable: true},
                operator_name : {label:'操作人'},
                actions : {label: '操作'}
        },
        ajax_url: "/venue",
        params: {},
        currentPage: 1,
        perPage: 15,
        del: {url:'/venue',title:'确定要删除道馆吗?',successText:'道馆信息删除成功!'}
      }
    },
    created() {
      
    },
    methods: {

      formatter(row, column) {
          return row.address;
      },

      handleEdit(index, row) {
        console.log(index, row);
      },

      reset() 
      {
            this.params = {};
      },
    }
}
</script>