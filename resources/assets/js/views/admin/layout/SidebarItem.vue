<template>
	<div class="menu-wrapper">
			<el-submenu index="1">
					<template slot="title">
						<i class="el-icon-message"></i>
						<span slot="title">导航一</span>
					</template>
					<el-menu-item-group>
						<span slot="title">分组一</span>
						<el-menu-item index="1-1">选项1</el-menu-item>
						<el-menu-item index="1-2">选项2</el-menu-item>
					</el-menu-item-group>
					<el-menu-item-group title="分组2">
						<el-menu-item index="1-3">选项3</el-menu-item>
					</el-menu-item-group>
					<el-submenu index="1-4">
						<span slot="title">选项4</span>
						<el-menu-item index="1-4-1">选项1</el-menu-item>
					</el-submenu>
				</el-submenu>

				
		<template v-for="item in routes">

			

					
			<!-- 处理无下拉框menu -->
			 <!-- <router-link v-if="item.is_show&&item.noDropdown&&item.children.length>0" :to="item.children[0].url">
	        	<el-menu-item :index="item.children[0].path" class='submenu-title-noDropdown'>
	          		<icon-svg v-if='item.icon' :icon-class="item.icon"></icon-svg>
					<i class="fa" v-if='item.icon' v-bind:class="item.icon"></i> 
	          		<span>{{item.children[0].display_name}}</span>
	        	</el-menu-item>
      		</router-link> -->

			<!-- 处理有下拉框的menu数据 -->
      		<!-- <el-submenu :index="item.display_name" v-if="!item.noDropdown&&item.is_show">
		        <template slot="title">
		        	<i class="fa" v-bind:class="item.icon" ></i> <span>{{ item.display_name}}</span>
		        </template>
		        <template v-for="child in item.children">
		          <sidebar-item class='nest-menu' v-if='child.children&&child.children.length>0' :routes='[child]'> </sidebar-item>
		          <router-link v-else :to="child.url">
		            <el-menu-item :index="child.url">
		              <i class="fa" v-if='child.icon' :icon-class="child.icon" ></i> <span>{{ item.display_name}}</span>
		            </el-menu-item>
		          </router-link>
		        </template>
      		</el-submenu> -->
		</template>
	</div>
</template>

<script>
export default {
  name: 'SidebarItem',
  props: {
    routes: {
      type: Array
    }
  }
}
</script>
