<template>
    <div class="app-container calendar-list-container">

    <el-row style="margin-top: 20px; margin-left:20px">
        <el-col :span="12" >

            <div class="grid-content bg-purple" style="border: 1px solid rgb(163, 161, 161);
            width: 95%;">
            <div class="box-header with-border">
					<h3 class="box-title">快速添加</h3>
			</div>  
        <el-form ref="permissionForm"  :model="permissionForm"   label-width="100px" class="el-form"
            style='width: 85%; margin-left:50px; margin-top:20px'
        >
            <el-form-item label="父级">
                <el-select v-model="permissionForm.parent_id" placeholder="请选择">
                  <el-option
                    v-for="item in parentOptions"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                  </el-option>
                </el-select>
            </el-form-item>
  
            <el-form-item label="权限名称" prop="name">
                  <el-input v-model="permissionForm.display_name"></el-input>
            </el-form-item>
  
            <el-form-item label="图标">
                  <el-input v-model="permissionForm.icon" ></el-input>
            </el-form-item>
  
            <el-form-item label="权限别名">
                  <el-input v-model="permissionForm.name" ></el-input>
            </el-form-item>
  
            <el-form-item label="排序">
                  <el-input v-model="permissionForm.order_num" ></el-input>
            </el-form-item>
  
            <el-form-item label="左侧是否显示">
                  <el-input v-model="permissionForm.is_show" ></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="onSubmit"> {{ permissionForm.id ? '更新' : '立即创建' }}</el-button>
            </el-form-item>
          </el-form>
  
            </div>
        </el-col>

        <el-col :span="12">
            <div class="grid-content bg-purple-light"  style="border: 1px solid rgb(189, 186, 186);
            width: 95%;">
                <p>he a sadf 222</p>    
            </div>
        </el-col>
  </el-row>
       
    </div>
</template>

<script>

export default {
    data() {
        return {
            permissionForm : {
                id : null,
                display_name : '',
                icon : '',
                name : '',
                parent_id : '',
                order_num : 0,
                is_show : 0
            },
            parentOptions : [],
            
        }
    },
    methods : {

        onSubmit() {

        }
    }
}

</script>