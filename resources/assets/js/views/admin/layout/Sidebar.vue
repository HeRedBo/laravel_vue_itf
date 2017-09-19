<template>
	<el-menu mode="vertical" theme="dark" unique-opened :default-active="$route.path" : collapse = "isCollapse">
		
	</el-menu>	
</template>

<script type="text/javascript">
export default {
    data() {
        return {
            permission_routers : {}
        }
    },
    methods : {
    },
    created() {
        var url = "/admin/menu", that = this;
        this.$http.get(url).then(function(response) {
            that.permission_routers = response.data;
        })
    }
}
</script>