<template>
	<el-menu mode="vertical" theme="dark" unique-opened :default-active="$route.path" :collapse="isCollapse">
		<sidebar-item :routes='permission_routers'></sidebar-item>
	</el-menu>	
</template>

<script>
import { mapGetters } from 'vuex';
import SidebarItem from './SidebarItem';
export default {
    components : {
        SidebarItem
    },
    data() {
        return {
            permission_routers : [],
        }
    },
    methods : {
       
    },
    computed: {
        ...mapGetters([
            'sidebar'
        ]),
        isCollapse() {
            console.log(!this.sidebar.opened);
            return !this.sidebar.opened
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