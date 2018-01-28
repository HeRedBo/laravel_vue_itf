<template>
<!--   <el-time-picker
    is-range
    v-model="value3"
    @change="inputChange"
    placeholder="选择时间范围">
  </el-time-picker> -->

<!--   <el-input-number size="large" v-model="value"></el-input-number>
  <el-input-number v-model="value"></el-input-number>
 -->  <el-input-number size="small" v-model="value"></el-input-number>
</template>



<script>
  export default {
    data() {
      return {
        value3: [new Date(2016, 9, 10, 8, 40), new Date(2016, 9, 10, 9, 40)],
        value : 123,
      };

    },
    methods:{
      inputChange(value) {
        console.log(value)
      }
    }
  }
</script>