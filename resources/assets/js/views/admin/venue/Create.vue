<template>

  <el-row style="margin-top: 20px;">
    <el-col :span="12" :offset="4">

      <el-form ref="venue"  :model="venue" :rules="VenueRules"  label-width="80px" class="el-form">
            <el-form-item label="道馆名称" prop="name">
              <el-input v-model="venue.name" ></el-input>
            </el-form-item>

          <el-form-item label="父级">
              <el-select v-model="venue.parent_id" placeholder="请选择">
                <el-option
                  v-for="item in venueOptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
                </el-option>
              </el-select>
          </el-form-item>

          <!-- 头像 -->
          <el-form-item label="头像">
            <div class="components-container">
              <pan-thumb :image='image'>
              </pan-thumb>
              <el-button type="primary" icon="upload" style="position: absolute;bottom: 15px;margin-left: 40px;" @click="imagecropperShow=true">修改logo
              </el-button>
	            <image-cropper :width="300" :height="300" url="/upload/upAvatar" @close='close' @crop-upload-fail='cropUploadFail' @crop-upload-success="cropSuccess" :key="imagecropperKey" v-show="imagecropperShow"></image-cropper>
	        </div>
          </el-form-item>

           <el-form-item label="区域">
             <v-distpicker @selected="onSelected"></v-distpicker>
          </el-form-item>
          	
          <el-form-item label="详细地址">
            <el-input type="textarea" v-model="venue.address"></el-input>
          </el-form-item>

          <el-form-item label="道馆备注">
            <el-input type="textarea" v-model="venue.remark"></el-input>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" @click="onSubmit">立即创建</el-button>
            <el-button>取消</el-button>
          </el-form-item>
        </el-form>
    </el-col>
    
  </el-row>
</template>

<script>

import PanThumb from 'components/Panthumb';
import ImageCropper from 'components/ImageCropper';
import {stack_error,str_repeat} from 'config/helper';
import VDistpicker from 'v-distpicker'

export default {
    components: { PanThumb ,ImageCropper, VDistpicker},
    name: 'venue',
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
        venue : {
           name: '',
           parent_id: '',
           logo : '',
           province_code : '',
           city_code : '',
           district_code:  '',
           address : '',
           remark : '',
        },
        
        venueOptions :  [],
        value: '',
        VenueRules: {
            name: [
              { required: true, message: '请输入活动名称', trigger: 'blur'},
              { min: 3, max: 50, message: '长度在 3 到 50 个字符', trigger: 'blur' },
              { validator: validateUsername, trigger: 'blur' }
        
            ],
        },

        imagecropperShow: false,
		    imagecropperKey: 0,
        image: 'https://wpimg.wallstcn.com/577965b9-bb9e-4e02-9f0c-095b41417191',
        
      }
    },
    created() {
      this.getVenues();
    },
    methods: {
      onSubmit() 
      {
         this.$refs.venue.validate(valid => {
            var that = this;
            console.log(valid);
            if (valid) {
               console.log(this.venue);
              console.log('submit!');
              this.$http({
                method :"post",
                url : '/venue',
                data : that.venue
              })
              .then(function(response) {
                console.log(JSON.stringify(response));
                var responseData = response.data;
                toastr.success(responseData.message);
						    //that.$router.back();
              })
              .catch(function(error) {
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
        this.venue.logo = resData.data.real_path;
      },

      close() {
        this.imagecropperShow = false
      },

      cropUploadFail(error,field, ki)
      {
        stack_error(error);
      },

      onSelected(data) {
        this.venue.province_code = data.province.code;
        this.venue.city_code = data.city.code;
        this.venue.district_code = data.area.code;
        console.log(data)
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
          this.$http({
             method :"GET",
             url : url,
             params : {
               name : name
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
      }
  
    }
  }
</script>


<style rel="stylesheet/scss" lang="scss" scoped>
@import "resources/assets/styles/mixin";
</style>