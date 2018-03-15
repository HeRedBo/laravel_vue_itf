<template>
	<div class="menu-wrapper">
			<!-- <li class="header">栏目导航</li> -->
			<!--Optionally  -->
			<!-- <li><a href="/admin"><i class="fa fa-dashboard"></i><span>控制面板</span></a></li> -->
		
			<!-- <router-link :to="index">
					<el-menu-item index="index">
							<i class="el-icon-setting"></i>
							<span slot="title">控制面板</span>
					</el-menu-item>
			</router-link> -->
			
		<template v-for="item in routes">

			<!-- 处理无下拉框menu -->
			    <router-link v-if="item.is_show&&item.children.length==0" :to="item.url">
	        	<el-menu-item :index="item.url" class='submenu-title-noDropdown'>
								<i class="fa" v-if='item.icon' v-bind:class="item.icon"></i> 
								<span>{{item.display_name}}</span>
	        	</el-menu-item>
      		</router-link>


				<!-- 处理有下拉框的menu数据 -->
      		<el-submenu :index="item.name" v-if="item.is_show&&item.children.length>0">
		        <template slot="title">
		        	<i class="fa" v-bind:class="item.icon" ></i> <span>{{ item.display_name}}</span>
		        </template>
		        <template v-for="child in item.children">
		          <sidebar-item class='nest-menu' v-if='child.children&&child.children.length>0' :routes='[child]'> </sidebar-item>
		          <router-link v-else :to="child.url">
		            <el-menu-item :index="child.url">
		              <i class="fa" v-if='child.icon' :icon-class="child.icon" ></i> <span>{{ child.display_name}}</span>
		            </el-menu-item>
		          </router-link>
		        </template>
					</el-submenu>
					

		</template>
	</div>
</template>

<script>
export default {
  name: 'SidebarItem',
  props: {
    routes: {
			type: Object
    }
	},
	data() {
		return {
			index : "/admin/dashboard"
		}
	},
	created(){
      // console.log(this.routes)
    }
}
</script>
