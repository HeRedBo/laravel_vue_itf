<template>
  <div class="block">
    <span class="demonstration">默认</span>
    <el-date-picker
      v-model="value6"
      type="daterange"
      @change="valueChange"
      placeholder="选择日期范围">
    </el-date-picker>
  </div>

  <!-- <div class="block">
    <span class="demonstration">带快捷选项</span>
    <el-date-picker
      v-model="value7"
      type="daterange"
      align="right"
      placeholder="选择日期范围"
      :picker-options="pickerOptions2">
    </el-date-picker>
  </div>
 -->
  
</template>

<script>
  export default {
    data() {
      return {
        pickerOptions2: {
          shortcuts: [{
            text: '最近一周',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: '最近一个月',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: '最近三个月',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit('pick', [start, end]);
            }
          }]
        },
        value6: '',
        value7: ''
      };
    },


    methods : {
      valueChange(value)
      {
        console.log(value);
      }

    }
  };
</script>
¶