<template>

  <el-row style="margin-top: 20px;">
    <el-col :span="12" :offset="4">

      <el-form ref="form" :model="form" label-width="80px" class="el-form">
            <el-form-item label="道馆名称">
              <el-input v-model="form.name"></el-input>
            </el-form-item>

          <el-form-item label="父级">
            <el-select v-model="form.parent_id" placeholder="请选择活动区域">
              <el-option label="区域一" value="shanghai"></el-option>
              <el-option label="区域二" value="beijing"></el-option>
            </el-select>
            
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
  export default {
    data() {
      return {
        form: {
          name: '',
          parent_id: '',
          date1: '',
          date2: '',
          delivery: false,
          type: [],
          resource: '',
          desc: ''
        }
      }
    },
    methods: {
      onSubmit() {
        console.log('submit!');
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
@import "resources/assets/styles/mixin";
</style>
