<template>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
          <div class="box-body">
              <el-form ref="venueForm"  :model="venueForm" :rules="VenueRules"  label-width="120px" class="el-form"
                style="width: 50%;
                margin-left: 10%;
                margin-top: 20px;"
              >
                  <el-form-item label="道馆名称" prop="name">
                    <el-input v-model="venueForm.name" ></el-input>
                  </el-form-item>
                  
                  <el-form-item label="父级">
                      <el-select v-model="venueForm.parent_id" placeholder="请选择">
                        <el-option
                          v-for="item in venueOptions"
                          :key="item.value"
                          :label="item.label"
                          :value="item.value">
                        </el-option>
                      </el-select>
                  </el-form-item>

                  <el-form-item label="会员卡前缀">
                    <el-input v-model="venueForm.card_prefix" ></el-input>
                  </el-form-item>
      
                <!-- 头像 -->
                <el-form-item label="头像">
                  <div class="components-container">
                    <pan-thumb :image="venueForm.id ? venueForm.logo : image ">
                    </pan-thumb>
                    <el-button type="primary" icon="upload" style="position: absolute;bottom: 15px;margin-left: 40px;" @click="imagecropperShow=true">修改logo
                    </el-button>
                    <image-cropper :width="900" :height="900" url="/upload/upAvatar" @close='close' @crop-upload-fail='cropUploadFail' @crop-upload-success="cropSuccess" :key="imagecropperKey" v-show="imagecropperShow"></image-cropper>
                </div>
                </el-form-item>
      
                 <el-form-item label="区域">
                   <v-distpicker  @selected="onSelected"  :province="select.province" :city="select.city" :area="select.area" ></v-distpicker>
                </el-form-item>
                  
                <el-form-item label="详细地址">
                  <el-input type="textarea" v-model="venueForm.address"></el-input>
                </el-form-item>
      
                <el-form-item label="道馆备注">
                  <el-input type="textarea" v-model="venueForm.remark"></el-input>
                </el-form-item>
      
                <el-form-item>
                  <el-button type="primary" @click="onSubmit" :loading="buttonLoading"> {{ venueForm.id ? '更新' : '立即创建' }}</el-button>
                  <router-link :to="{path:'/admin/venue/index'}"> 
                      <el-button>取消</el-button>
                  </router-link>
                </el-form-item>
              </el-form>
          </div>

      </div>
    </div>
  </div>
</template>

<script>

import PanThumb from 'components/Panthumb';
import ImageCropper from 'components/ImageCropper';
import {stack_error,str_repeat} from 'config/helper';
import VDistpicker from 'v-distpicker'

export default 
{
    components: { PanThumb ,ImageCropper, VDistpicker},
    name: 'venue',


    props: {
      venueForm: {
          type: Object,
          default() {
              return {}
          }
      },
      select : {
        type : Object,
        default() {
              return {}
        }
      }
    },

    data() 
    {
      const validateUsername = (rule, value, callback) => {
      
         this.checkVenueName(value, function(status) {
           if(status == 1)
           {
             callback(new Error('道馆名称已存在'))
           } else {
             callback()
           }
         });
      }

      return {
       

        venueOptions :  [],
        //value: '',
        VenueRules: {
            name: [
              { required: true, message: '请输入道馆名称', trigger: 'blur'},
              { min: 3, max: 50, message: '长度在 3 到 50 个字符', trigger: 'blur' },
              { validator: validateUsername, trigger: 'blur' }
            ],
        },
        imagecropperShow: false,
		    imagecropperKey: 0,
        image: 'http://owu2vcxbh.bkt.clouddn.com/files/avatar/default.png',
        buttonLoading: false,
        
      }
    },
    created() {
      this.getVenues();
    },
    methods: {
      onSubmit() 
      {
         this.$refs.venueForm.validate(valid => {
            var that = this;
            if (valid) {
              
              let url = '/venue' + (this.venueForm.id ? '/' + this.venueForm.id : '')
              let method = this.venueForm.id ? 'put' : 'post';
              this.buttonLoading = true;
              this.$http({
                method :method,
                url : url,
                data : that.venueForm
              })
              .then(function(response) {
                  that.buttonLoading = false;
                  var {data} = response; 
                  that.$message({
                    showClose: true,
                    message: data.message,
                    type: 'success'
                  });
                // 跳转到列表页
                that.$router.push({ path: '/admin/venue/index' })
              })
              .catch(function(error) {
                that.buttonLoading = false;
                stack_error(error);
              });
          } else {
            console.log('error submit!!')
            return false
          }

        });
       
      },

      cropSuccess(resData) {
        this.imagecropperShow = false;
        this.imagecropperKey = this.imagecropperKey + 1;
        this.image = resData.data.url;
        this.venueForm.logo = resData.data.real_path;
        this.venueForm.logo_thumb = resData.data.thumb[0].real_path;
      },

      close() {
        this.imagecropperShow = false
      },

      cropUploadFail(error,field, ki)
      {
        stack_error(error);
      },

      onSelected(data) {
        this.venueForm.province_code = data.province.code;
        this.venueForm.province      = data.province.value;
        this.venueForm.city_code     = data.city.code;
        this.venueForm.city          = data.city.value;
        this.venueForm.area_code     = data.area.code;
        this.venueForm.area          = data.area.value;
      },

      getVenues() {
          var url = '/venue/getVenues', that = this;
          this.$http({
             method :"GET",
             url : url,
          })
          .then(function(response) {
            var responseJson = response.data,data = responseJson.data
            var options = [];
            for (var i in data ) {
             
              let label = str_repeat('--',data[i].level) + data[i].name;
              options.push({value : data[i].id , label: label});
            }	
            that.venueOptions = options;
          })
          .catch(function(error) {
            stack_error(error);
          }); 
      },

      checkVenueName(name,callback) {
          
          var url = 'venue/checkName',that = this;
          let id = that.venueForm.id ? that.venueForm.id : 0;
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
            callback(data.status)
          })
          .catch(function(error) {
            console.log('check name error')
            stack_error(error);
          }); 
      },

     
  
    }
  }
</script>


<style rel="stylesheet/scss" lang="scss" scoped>
@import "resources/assets/styles/mixin";
</style>