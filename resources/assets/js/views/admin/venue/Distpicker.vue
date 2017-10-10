<template>
  <div class="components-container">
      <v-distpicker @selected="onSelected"></v-distpicker>
  </div>
</template>

<script>
import VDistpicker from 'v-distpicker'
export default {
    components: { VDistpicker },
    data() {
      return {
          placeholders: {
              province: '------- 省 --------',
              city: '--- 市 ---',
              area: '--- 区 ---',
          }
      }
    },
    methods: {
      onSelected(data) {
        alert(data.province.value + ' | ' + data.city.value + ' | ' + data.area.value)
        console.log(data)
      }
    }
}
</script>
