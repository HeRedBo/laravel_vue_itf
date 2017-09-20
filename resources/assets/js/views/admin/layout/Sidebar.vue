<template>
	<el-menu mode="vertical" theme="dark" unique-opened :default-active="$route.path" :collapse="isCollapse">
		<sidebar-item :routes='permission_routers'></sidebar-item>
	</el-menu>	
</template>

<script>
import SidebarItem from './SidebarItem'
export default {
    components : {
        SidebarItem
    },
    data() {
        return {
            permission_routers : []
        }
    },
    methods : {

    },

    computed: {
    
        isCollapse() {
            return false
        }
    },

    created() {
        // var url = "/admin/menu", that = this;
        // this.$http.get(url).then(function(response) {
        //     that.permission_routers = response.data
        // })
    }
}
</script>