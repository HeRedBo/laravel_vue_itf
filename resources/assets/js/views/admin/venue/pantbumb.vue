<template>
	<div class="components-container">
		<pan-thumb :image='image'>
			你的权限:
			<span class="pan-info-roles" > 查看个人头像</span>
		</pan-thumb>
		 <el-button type="primary" icon="upload" style="position: absolute;bottom: 15px;margin-left: 40px;" @click="imagecropperShow=true">修改头像
    </el-button>

	<div class="col-md-2 col-md-offset-1">
         <avatar :src="image"></avatar>
    </div>

	<image-cropper :width="300" :height="300" url="https://httpbin.org/post" @close='close' @crop-upload-success="cropSuccess" :key="imagecropperKey" v-show="imagecropperShow"></image-cropper>

	</div>

	
	
</template>

<script>

import PanThumb from 'components/Panthumb';
import ImageCropper from 'components/ImageCropper';
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
      this.image = resData.files.avatar
    },
    close() {
      this.imagecropperShow = false
	}
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