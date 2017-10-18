<template>
	<div class="menu-wrapper">
		<template v-for="item in routes">

			<!-- 处理无下拉框menu -->
			    <router-link v-if="item.is_show&&item.children.length==1" :to="item.children[0].url">
	        	<el-menu-item :index="item.children[0].path" class='submenu-title-noDropdown'>
	          		<!-- <icon-svg v-if='item.icon' :icon-class="item.icon"></icon-svg> -->
							  <i class="fa" v-if='item.icon' v-bind:class="item.icon"></i> 
	          		<span>{{item.children[0].display_name}}</span>
	        	</el-menu-item>
      		</router-link>

				<!-- 处理有下拉框的menu数据 -->
      		<el-submenu :index="item.display_name" v-if="item.is_show">
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
	created(){
       console.log('asda')
      // console.log(this.routes)
    }
}
</script>
