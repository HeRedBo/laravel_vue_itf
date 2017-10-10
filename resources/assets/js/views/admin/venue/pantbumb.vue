<template>
	<div class="components-container">
		<pan-thumb :image='image'>
		</pan-thumb>
		 <el-button type="primary" icon="upload" style="position: absolute;bottom: 15px;margin-left: 40px;" @click="imagecropperShow=true">修改头像
    </el-button>
	  <image-cropper :width="300" :height="300" url="/upload/upAvatar" @close='close' @crop-upload-fail='cropUploadFail' @crop-upload-success="cropSuccess" :key="imagecropperKey" v-show="imagecropperShow"></image-cropper>
	</div>
</template>

<script>

import PanThumb from 'components/Panthumb';
import ImageCropper from 'components/ImageCropper';
import { stack_error } from 'config/helper';

export default {
  components: { PanThumb ,ImageCropper},
  data() {
	  return {
		imagecropperShow: false,
		imagecropperKey: 0,
		image: 'https://wpimg.wallstcn.com/577965b9-bb9e-4e02-9f0c-095b41417191'
	  }
  },
  methods: {
    cropSuccess(resData) {
      this.imagecropperShow = false
      this.imagecropperKey = this.imagecropperKey + 1
      this.image = resData.data.url
    },
    close() {
      this.imagecropperShow = false
    },
    cropUploadFail(error,field, ki)
    {
      stack_error(error);
    },
    
 }
}
</script>

<style scoped>
  .avatar{
    width: 200px;
    height: 200px;
    border-radius: 50%;
  }
</style>