<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                        <span>
                            <button v-show="showCreateButton"  @click="handleCreate" type="button" class="btn btn-sm btn-success">添加卡券</button>
                        </span>
                       
                        <div class="form-inline pull-right">
                           
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
                    
                    <!-- 道馆名称 -->
                     <template slot="venue_name" slot-scope="item">
                        <span>{{item.item.name}}</span>
                    </template>

                    <!-- 状态 -->
                    <template slot="status" slot-scope="item">
                        <i :class="['fa','fa-circle',item.item.status==1?'text-success':'text-danger']"></i>
                    </template>
                    
                    <!-- 操作 -->
                    <template slot="actions" slot-scope="item">
                        <div class="btn-group">
                            <router-link :to="{ path: 'update/'+item.item.id}" class="btn bg-orange btn-xs">编辑</router-link>
                            <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a>
                        </div>
                    </template>

                </vTable>
            </div>
        </div>

        <el-dialog :title="dialogTitle" :visible.sync="dialogFormVisible" style="z-index:120;" >
                    
                            <el-form class="small-space" 
                                ref="CardForm" 
                                :model="CardForm"
                                :rules="RoleRules"
                                label-position="right"
                                label-width="80px"
                                style='width: 400px; margin-left:50px;'
                            >

                            <el-form-item label="卡类型" prop="type">
                                <el-radio-group v-model="CardForm.type">
                                    <el-radio :label="1" >期卡</el-radio>
                                    <el-radio :label="2" >次卡</el-radio>
                                </el-radio-group>
                            </el-form-item>

                              <el-form-item label="卡券名称" prop="name">
                                <el-input v-model="CardForm.name" auto-complete="off" ></el-input>
                              </el-form-item>
                              
                              <el-form-item label="道馆" v-show="selectItemVisible" prop="venue_id" >
                                <el-select v-model="CardForm.venue_id" placeholder="请选择道馆" style="width:100%" >
                                  <el-option
                                     v-for="item in venueOptions"
                                     :key="item.value"
                                     :label="item.label"
                                     :value="item.value">
                                  </el-option>
                                </el-select>
                             </el-form-item>

                             <el-form-item  v-if="CardForm.type == 1" label="计算单位" prop="unit" inline>
                                    <el-select v-model="CardForm.unit" placeholder="单位" >
                                      <el-option
                                         v-for="item in unitOptions"
                                         :key="item.value"
                                         :label="item.label"
                                         :value="item.value">
                                      </el-option>
                                    </el-select>
                            </el-form-item>

                            <el-form-item :label="CardForm.type == 1 ? '数量':'次数'" prop="number">
                                <el-input-number v-model="CardForm.number" ></el-input-number>
                            </el-form-item>

                            <el-form-item label="价格" prop="card_price">
                                    <el-input-number v-model="CardForm.card_price" ></el-input-number>
                                    
                                  </el-form-item>
                            <el-form-item label="启用状态">
                                    <el-switch on-value="1" off-value="0" on-text="" off-text="" v-model="CardForm.status"></el-switch>
                            </el-form-item>

                              <el-form-item label="卡券说明">
                                <el-input type="textarea" :autosize="{ minRows: 3, maxRows: 6}" v-model="CardForm.explain"></el-input>
                              </el-form-item>
                            </el-form>
                            <div slot="footer" class="dialog-footer">
                              <el-button @click="dialogFormVisible = false">取 消</el-button>
                              <el-button type="primary" @click="handleCard">确 定</el-button>
                            </div>
                          </el-dialog>

    </div>
</template>
<style type="text/css">
    .el-date-editor--datetimerange.el-input  {
        width: 320px !important;
    }
</style>
<script>
    import {stack_error,parseSearchParam} from 'config/helper';
    export default {
       data () {
            const validateCardName = (rule, value, callback) => {
                this.validateCardName(value, function(status) {
                   if(status == 1)
                   {
                        callback(new Error('卡券名称已存在'))
                   } else {
                        callback()
                   }
                });
            }
            return {
                items: [],
                fields: {
                    id: {label: 'ID', sortable: true},
                    type_str: {label: '卡类型'},
                    name: {label: '卡券名称'},
                    venue_name: {label: '道馆', need: 'venues'},
                    card_price:{label:'卡券价格'},
                    status:{label:'状态'},
                    created_at:{label:'创建时间', sortable: true},
                    username : {label:'创建时间', need:'operator'},
                    actions : {label: '操作'}
                },
                ajax_url: "/card",
                params: { name: ''},
                currentPage: 1,
                perPage: 15,
                del: {url:'/admin/user',title:'确定要删除用户吗?',successText:'删除后台用户成功!'},
                showCreateButton : false,
                dialogFormVisible : false,
                selectItemVisible : false,
                dialogTitle : '创建卡券',
                CardForm : {
                    name : '',
                    venue_id : '',
                    number: 0,
                    unit : '',
                    card_price : '',
                    explain : '',
                    status : '',
                },

                RoleRules: {
                    name: [
                        { required: true, message: '请输入班级名称', trigger: 'blur'},
                        { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
                        { validator: validateCardName, trigger: 'blur' }
                    ],
                    type: [
                            { required: true, message: '卡券类型必选' }
                    ],
                    number:[
                        { required: true, message: '数量不能为空'},
                        { type: 'number', message: '数量必须为数字值'}
                    ],
                    unit : [
                        { required: true, message: '计算单位不能为空'}
                    ],
                    venue_id : [
                        { required: true, message: '归属道馆不能为空'}
                    ],
                    card_price : [
                        { required: true, message: '卡券价格不能为空'}
                    ]
                },
                venueOptions : [],
                unitOptions : [
                    {label : '天', value : 'day'},
                    {label : '月', value : 'mouth'},
                    {label : '年', value : 'year'},
                ]
            }
        },
        created() {
          this.getUserVenus();
        },
        methods: {  

        handleCreate () {
            var venue_id = this.CardForm.venue_id;
            this.CardForm = {
                type: 1
            };
            if(venue_id)
            {
                this.CardForm.venue_id = venue_id;
            }
            this.dialogFormVisible = true;
            
        },

        handleCard() {
            this.$refs.CardForm.validate(valid => {
            var that = this;
            if (valid) 
            {
                if(this.CardForm.status ==1) 
                {
                    swal({
                        title: "确定要" + this.dialogTitle + "?",
                        text: '卡券一经启用无法之后无法修改 你是否要继续执行该操作？',
                        type: "warning",
                        showCancelButton: true,
                        cancelButtonText: "取消",
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "确认",
                        closeOnConfirm: true
                    }).then(function () {
                        that.saveCard();
                    
                    },function(dismiss) {
                        if (dismiss === 'cancel') {
                            that.CardForm.status = 0;
                        }
                    }
                )
                }
                else 
                {
                    that.saveCard();
                }
                
            } 
            else 
            {
                    console.log('error submit!!')
                    return false
            }

            });
        },
        saveCard() {
                
                let url = '/card' + (this.CardForm.id ? '/' + this.CardForm.id : ''), that = this;
                let method = this.CardForm.id ? 'put' : 'post'
                this.$http({
                    method :method,
                    url : url,
                    data : that.CardForm
                })
                .then(function(response) {
                    var {data} = response; 
                    that.dialogFormVisible = false;
                    that.$message({
                        showClose: true,
                        message: data.message,
                        type: 'success'
                    });
                    // 跳转到列表页
                    that.$refs.table.loadList();
                })
                .catch(function(error) {
                    stack_error(error);
                });

        },


        getUserVenus() 
        {
            var that = this;
            var url = '/user/userVenues';
            this.$http({
                method :'GET',
                url : url
           })
           .then(function(response) 
           {

                let {data} = response;
                var  respondata = data.data
                var options = [];
                for (var i in respondata ) {
                   
                    let label =  respondata[i].name;
                    options.push({value : respondata[i].id , label: label});
                }
                that.venueOptions = options;
                if(options.length == 1)
                {
                    that.CardForm.venue_id =  options[0].value;
                } 
                else
                {
                    that.selectItemVisible = true;
                }
                that.showCreateButton = true;
             })
            .catch(function(error) {
                console.log(error);
                stack_error(error);
            });
        },

        validateCardName (name,callback) 
        {
            var url = 'card/checkCardName',that = this;
            let id = that.CardForm.id ? that.CardForm.id : 0;
            this.$http({
                method :"GET",
                url : url,
                params : {
                    name : name,
                    id   : id,
                }
            })
            .then(function(response) {
                var responseJson = response.data;
                var data = responseJson.data;
                callback(data.status);
            })
            .catch(function(error) {
                console.log('check name error')
                stack_error(error);
            }); 
        },

            reset : function() 
            {
                this.params = {};
            }
        },  
       
    }
</script>

