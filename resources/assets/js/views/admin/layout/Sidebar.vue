<template>
	<el-menu mode="vertical" theme="dark" unique-opened :default-active="$route.path" :collapse="isCollapse">
		<sidebar-item :routes='permission_routers'></sidebar-item>
	</el-menu>	
</template>

<script>
import { mapGetters } from 'vuex';
import SidebarItem from './SidebarItem';
import { stack_error } from 'config/helper';
export default {
    components : {
        SidebarItem
    },
    data() {
        return {
            permission_routers : {},
        }
    },
    methods : {
       getMenus() {
            var url = '/menu',that =this;
            this.$http({
                    method :"GET",
                    url : url,
            })
            .then(function(response) {
                let {data} = response;
                console.log(data.data);
                that.permission_routers = data.data;     
            }).catch(function(error) {
                that.loading = false;
                stack_error(error);
            });
       }
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

    created(){
       this.getMenus();
    }
}
</script>