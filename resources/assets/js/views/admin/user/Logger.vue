<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-inline pull-right">
                                
                                <!-- 归属道馆 -->
                                <div class="input-group input-group-sm">
                                    <el-select style="width:160px"  v-show="selectItemVisible" v-model="params.venue_id" placeholder="请选择道馆"  class="filter-item"  @change="venueChange" size="small"
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

                                <div class="input-group input-group-sm" >
                                    <el-input 
                                        placeholder="用户名" 
                                        v-model="params.user_name"
                                        size="small"
                                    >
                                    </el-input>
                                </div>

                                <!--  数据搜索框 -->
                               <div class="input-group input-group-sm" >
                                    <el-input 
                                        placeholder="操作内容" 
                                        v-model="params.intro"
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
                
                </vTable>
            </div>
        </div>
    </div>
</template>

<script>
import {stack_error} from 'config/helper';
export default {
    data() {
     return { 
            items: [],
            fields: {
                id: {label: 'ID', sortable: true},
                venue_name : {label: '道馆'},
                name: {label: '用户名',need:'users'},
                url: {label: 'url'},
                intro: {label: '内容'},
                created_at:{label:'操作时间', sortable: true}
            },
            ajax_url: "/user/logger",
            params : {},
            currentPage: 1,
            perPage: 15,
            del: {},
            listLoading: true,
            buttonLoading: false,
            selectItemVisible : false,
            venueOptions: []
        }
    },
    created() 
    {
      this.getUserVenus();
    },
    methods : {
      reset() 
      {
        this.params = {};
      },
      getUserVenus() {
        var that = this;
        var url = '/user/userVenues';
        this.$http({
            method :'GET',
            url : url
        })
        .then(function(response) 
        {
            let {data} = response;
            var respondata = data.data
            var options = [];
            for (var i in respondata ) {
                let label =  respondata[i].name;
                options.push({value : respondata[i].id , label: label});
            }
            that.venueOptions = options;
            if(options.length == 1) {
                var venue_id =  options[0].value;
                that.params.venue_id =  venue_id;
            } else {
                that.selectItemVisible = true;
            }
                  
          })
          .catch(function(error) {
              stack_error(error);
          });
        },
        venueChange(value)
        {
            this.params.params = value;
            this.$refs.table.loadList();
            
        }
    }
}

</script>

<style rel="stylesheet/scss" lang="scss">
 @import "resources/assets/styles/index";
</style>
