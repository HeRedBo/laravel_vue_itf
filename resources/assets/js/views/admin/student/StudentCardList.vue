<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                  <div class="row" style="margin-bottom:10px;">
                    <div class="col-md-12">
                      <h3 class="box-title">{{$route.name}}</h3>
                    </div>
                  </div>
                  <!-- <div class="box-tools">
                      <div class="input-group input-group-sm" style="width:200px">
                          <input type="text" name="keyword" v-model="params.keyword" class="form-control pull-right" placeholder="请输入操作内容">

                          <div class="input-group-btn">
                              <button type="submit" class="btn btn-default" @click="$refs.table.loadlist()"><i class="fa fa-search"></i></button>
                          </div>
                      </div>
                  </div> -->
                    <div class="row">
                       <div class="col-md-2">
                             <button  @click="handleCreate" type="button" class="btn btn-sm btn-success">添加卡券
                            </button>
                       </div>
                    </div>
                </div>

                <vTable ref="table" stripped hover :searchType=1 :ajax_url="ajax_url" :params="params" :items="items" :fields="fields" :current-page="currentPage"
                    :per-page="perPage">

                    <!-- 状态 -->
                    <template slot="status" slot-scope="item">
                        <a href="javascript:void(0)" data-toggle="tooltip" :title="item.item.status==1 ? '启用':'未启用'">
                            <i :class="['fa','fa-circle',item.item.status==1?'text-success':'text-danger']"></i>
                        </a>
                    </template>

                    <template slot="total_class_number" slot-scope="item">
                        <span>{{item.item.type==1?'--':item.item.total_class_number}}</span>
                    </template>


                    <template slot="residue_class_number" slot-scope="item">
                        <span>{{item.item.type==1?'--':item.item.residue_class_number}}</span>
                    </template>

                    <template slot="start_time" slot-scope="item">
                        <span>{{item.item.type==2?'--':item.item.start_time}}</span>
                    </template>

                    <template slot="end_time" slot-scope="item">
                        <span>{{item.item.type==2?'--':item.item.end_time}}</span>
                    </template>


                    <!-- 操作 -->
                    <template slot="actions" slot-scope="item">
                        <div class="btn-group">
                            <a href="javascript:;" @click="view(item.item)" class="btn btn-success btn-xs">查看</a>
                            <!-- <router-link :to="{path:'update/'+  item.item.id}" class="btn bg-orange btn-xs">编辑</router-link> -->
                            <!--   <router-link target="_blank"  :to="{path:'logger/'+ item.item.id}" class="btn bg-info btn-xs">操作日志</router-link> -->
                            <!-- <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a> -->
                        </div>
                    </template>
                </vTable>
            </div>
        </div>


        <el-dialog :title="dialogTitle" :visible.sync="dialogFormVisible" style="z-index:120;" size="small" >
            <el-form
                class="small-space"
                ref="StudentCard"
                :model="StudentCard"
                label-position="right"
                label-width="80px"
                style='width:65%;margin-left:5px;'
            >
                <!-- 道馆 -->
                <el-form-item label="道馆" v-show="selectItemVisible" prop="venue_id" >
                        <el-select v-model="StudentCard.venue_id" placeholder="请选择道馆" style="width:100%" @change="venueChange">
                          <el-option
                             v-for="item in venueOptions"
                             :key="item.value"
                             :label="item.label"
                             :value="item.value"

                             >
                          </el-option>
                        </el-select>
                </el-form-item>
                <!-- 卡券种类 -->
                <el-form-item label="购卡类型">
                        <el-select v-model="StudentCard.card_id" @change="selectCard"  placeholder="请选择需要购买的卡" style="width:100%" >
                          <el-option
                            v-for="item in cardOptions"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                          </el-option>
                        </el-select>
                </el-form-item>


                <el-table
                    :data="StudentCard.user_cards"
                    style="min-width: 650px;margin-bottom: 10px;"
                    align="cneter"
                >
                    <el-table-column
                        prop="card_id"
                        label="卡券ID"
                        align="cneter"
                        >
                    </el-table-column>

                    <el-table-column
                        prop="name"
                        label="卡券名称"
                        align="cneter"
                    >

                    </el-table-column>

                    <el-table-column
                        prop="buy_number"
                        label="购买数量"
                        align="cneter"
                    >
                    </el-table-column>

                    <el-table-column
                        prop="card_price"
                        align="cneter"
                        label="卡券价格"
                    >
                    </el-table-column>

                    <el-table-column
                        prop="total_price"
                        align="cneter"
                        label="总金额"
                    >
                    </el-table-column>

                    <el-table-column
                      align="cneter"
                      label="启用状态">
                        <!-- 状态 -->
                        <template slot-scope="scope">
                          <a href="javascript:void(0)"   @click="changeCardStatus(scope.$index)" data-toggle="tooltip" :title="scope.row.status==1 ?'启用':'未启用'">
                              <i :class="['fa','fa-circle',scope.row.status==1?'text-success':'text-danger']"></i>
                          </a>
                        </template>
                    </el-table-column>



                    <el-table-column
                        label="操作"
                    >
                    <template slot-scope="scope">
                        <a
                         v-show="scope.row.id==0"
                         href="#"  @click="deleteUserCard(scope.$index)"
                         class="btn btn-danger btn-xs">删除
                        </a>
                    </template>
                    </el-table-column>

                </el-table>

            </el-form>

            <div slot="footer" class="dialog-footer" style="text-align:center">
              <el-button @click="dialogFormVisible = false">取 消</el-button>
              <el-button type="primary" @click="handleCard" :loading="buttonLoading">确 定</el-button>
            </div>
        </el-dialog>


        <div id="card_view_box" style="display: none">
            <!-- Widget: user widget style 1 -->
            <div class="box box-widget">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <table class="table  table-bordered" style="font-size: 14px">
                    <tbody>
                    <tr>
                        <th>ID</th>
                        <td>{{card.id}}</td>
                        <th>卡券类型</th>
                        <td>{{card.type_name}}</td>
                    </tr>

                    <tr>
                        <th>卡券名称</th>
                        <td>{{card.card_name}}</td>
                        <th>卡券价格</th>
                        <td>{{card.price}}</td>
                    </tr>

                    <tr>
                        <th>购买数量</th>
                        <td>{{card.number}}</td>
                        <th>总价</th>
                        <td>{{card.card_price}}</td>
                    </tr>
                    <tr>
                        <th>总课程数</th>
                        <td>
                            <span>{{card.type==1?'--':card.total_class_number}}</span>
                        </td>
                        <th>剩余课程数</th>
                        <td>{{card.type==1?'--':card.residue_class_number}}</td>
                    </tr>


                     <tr>
                        <th>卡券有效期开始时间</th>
                        <td><span>{{card.type==2?'--':card.start_time}}</span></td>
                        <th>卡券有效期结束时间</th>
                        <td><span>{{card.type==2?'--':card.end_time}}</span></td>
                    </tr>


                    <tr>
                        <th>创建时间</th>
                        <td>{{card.created_at}}</td>

                        <th>最新更新时间</th>
                        <td>{{card.updated_at}}</td>
                    </tr>
                    <tr>
                        <th>操作人</th>
                        <td>{{card.operator_name}}</td>
                        <th></th>
                        <td></td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <!-- /.widget-user -->

        </div>

    </div>
</template>

<script>
$(function () {
    $('.box').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
});

    import { stack_error, parseSearchParam } from 'config/helper';
    export default {
        data() {
            return {
                items: [],
                fields: {
                    id: { label: 'ID', sortable: true },
                    type_name: { label: '卡券类别' },
                    card_name: { label: '卡券名称' },
                    price: { label: '卡券价格' },
                    number: { label: '购买数量' },
                    card_price: { label: '总价'},
                    total_class_number: { label: '总课程数' },
                    residue_class_number: { label: '剩余课程数' },
                    status: { label: '状态' ,sortable: true},
                    start_time: { label: '有效期开始时间' },
                    end_time: { label: '有效期结束时间'},
                    // created_at: { label: '创建时间', sortable: true },
                    // updated_at: { label: '更新时间', sortable: true },
                    actions: { label: '操作' }
                },
                ajax_url: "/student/studentCardList",
                params: {},
                currentPage: 1,
                perPage: 15,
                searchQuery: {},
                listLoading: true,
                selectItemVisible: false,
                dialogFormVisible:false,
                sexOptions: [],
                venueOptions: [],
                cardOptions: [],
                StudentCard : {
                    user_cards: []
                },
                dialogTitle : '新增卡券',
                buttonLoading: false,
                cardUseStatus:1,
                card : {}
            }
        },
        created() {
           this.initData();
           this.getUserVenus();

        },
        methods: {

            initData()
            {
                var that = this,id = this.$route.params.id;
                this.params.student_id=id;
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
                        var venue_id =  options[0].value;
                        that.StudentCard.venue_id =  venue_id;

                        that.getCardOptions(venue_id);
                    }
                    else
                    {
                        that.selectItemVisible = true;
                    }
                        // that.showCreateButton = true;
                })
                .catch(function(error) {
                    console.log(error);
                    stack_error(error);
                });
            },

            venueChange(value) {
              this.getCardOptions(value)
            },

            getCardOptions(venue_id) {
                var url = '/card/cardOptions', that = this;
                this.$http({
                   method :"GET",
                   url : url,
                   params : {
                    venue_id : venue_id
                   }
                })
                .then(function(response) {
                  var responseJson = response.data,data = responseJson.data
                  var options = [];
                  that.cardOptions = data;
                })
                .catch(function(error) {
                  stack_error(error);
                });
            },

            selectCard(value){
                var card = this.cardOptions[value];
                card.card_id = card.id;
                card.id = 0;// 新增数据 id 置为 0
                card.status = 1;
                var that = this;
                this.$prompt('请输入卡券数量', '卡券数量', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /^[1-9]\d*$/,
                    inputErrorMessage: '卡券购买数量必须是大于0数值'
                }).then(({ value }) => {
                    card.buy_number = value;
                    card.status = that.cardUseStatus?0:1;
                    if(!this.cardUseStatus) {
                      this.cardUseStatus = 1;
                    }
                    card.total_price = parseFloat(card.card_price * value).toFixed(2);
                    that.StudentCard.user_cards.push(card);
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '取消输入'
                    });
                });
            },

            deleteUserCard(index) {
                this.StudentCard.user_cards.splice(index, 1)
            },

            changeCardStatus(index) {
              var length = this.StudentCard.user_cards.length;
              var that = this;
              if(length > 1)
              {
                  for (var i = length-1; i >= 0; i--) {
                    this.StudentCard.user_cards[i].status = 0;
                  }
                  that.StudentCard.user_cards[index].status = 1;
              }
            },

            handleCreate()
            {
                this.dialogFormVisible = true;
            },

            handleCard(){
                var that = this,student_id = this.$route.params.id;
                this.StudentCard.student_id = student_id;
                // 教研学生卡券信息是否有提交
                if(that.StudentCard.user_cards.length ==0) {
                    this.$notify.error({
                      title: '错误',
                      message: '卡券信息不能为空',
                      duration:3000
                    });
                    return;
                }
                let url = '/student/saveStudentCard'
                let method = 'post';
                // 请求接口保存用户卡券信息
                this.$http({
                      method :method,
                      url : url,
                      data : that.StudentCard
                    })
                .then(function(response) {
                    var {data} = response;
                    that.$message({
                          showClose: true,
                          message: data.message,
                          type: 'success'
                    });
                    // 跳转到列表页
                    that.dialogFormVisible = false;
                    // 重置数据
                    that.StudentCard.user_cards = [];
                    // 刷新列表数据
                    that.$refs.table.loadList();
                })
                .catch(function(error) {
                      stack_error(error);
                });
            },
            handleEdit(index, row) {
                console.log(index, row);
            },
            reset() {
                this.params = {};
            },

            view(card_info) {
                this.card = card_info;
                 setTimeout( function() {
                    swal({
                        title : card_info.card_name,
                        width: '50%',
                        html : $('#card_view_box').html(),
                    });
                },100);
        },
        }
    }
</script>
