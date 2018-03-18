<template>
	<el-menu class="navbar" mode="horizontal">
		<hamburger class="hamburger-container" :toggleClick="toggleSideBar"  :isActive="sidebar.opened"></hamburger>
		<levelbar></levelbar>
		<tabs-view></tabs-view>
	

		<!-- <el-dropdown class="avatar-container" trigger="click">
			<el-dropdown-menu class="user-dropdown" slot="dropdown">
				<div class="avatar-wrapper">
					<img class="user-avatar" :src="avatar+'?imageView2/1/w/80/h/80'">
					<i class="el-icon-caret-bottom"></i>
				</div>

				<router-link class='inlineBlock' to="/admin">
					<el-dropdown-item>
						首页
					</el-dropdown-item>
				</router-link>

				<el-dropdown-item divided><span @click="logout" style="display:block;">退出登录</span></el-dropdown-item>
			</el-dropdown-menu>
		</el-dropdown> -->

		<div class="right-menu">
			<screenfull class='screenfull right-menu-item'></screenfull>
				<div class="right-menu-item">
				<span>{{user.name}}</span>
			</div>

			<el-dropdown class="avatar-container right-menu-item" trigger="click">
				<div class="avatar-wrapper">
					<img class="user-avatar" :src="user.picture">
					<i class="el-icon-caret-bottom"></i>
				</div>
				<el-dropdown-menu slot="dropdown">
					<el-dropdown-item divided>
						<router-link :to="{path:'/admin/user/info'}" class="">
							修改资料
						</router-link>
					</el-dropdown-item>

					<el-dropdown-item divided>
						<span @click="logout" style="display:block;">退出</span>
					</el-dropdown-item>
				</el-dropdown-menu>
			</el-dropdown>
		</div>		
	</el-menu>
</template>

<script>
import { mapGetters } from 'vuex'
import Hamburger from 'components/Hamburger';
import Levelbar from './Levelbar';
import TabsView from './TabsView';
import Screenfull from 'components/Screenfull'
import { stack_error,isEmpty} from 'config/helper';

export default {
	data() {
		return {
			user : window.User
		}
	},
	components : {
		Hamburger,
		Levelbar,
		Screenfull,
		TabsView
	},
	computed : {
		...mapGetters([
			'sidebar'
		])
	},
	created() {
		this.initUser();
	},
	methods : {
		toggleSideBar() {
			this.$store.dispatch('ToggleSideBar')
		},
		
		logout() {
			var that = this;
			this.$http({
              method :"GET",
              url : 'logout',
              data : this.loginForm
            })
            .then(function(response) {
                location.reload()// 为了重新实例化vue-router对象 避免bug
                //that.$router.push({ path: '/admin/login' })      
           })
           .catch(function(error) {
            
             stack_error(error);
           });
		},

		initUser()
		{
			var that = this;
			if(isEmpty(that.user))
			{
				this.$http({
	              	method :"GET",
	              	url : '/user/info',
            	})
            	.then(function(response) {
                	 var {data} = response; 
                	 that.user = data.data;
           		})
           		.catch(function(error) {
             		stack_error(error);
           		});

			}
			return true;
		}
	}
}
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
	.navbar {
		height: 50px;
		line-height: 50px;
		border-radius: 0px !important;
		.hamburger-container {
			line-height: 58px;
			height: 50px;
			float: left;
			padding: 0 10px;
		}
		.breadcrumb-container {
			float: left;
		}
		.errLog-container {
			display: inline-block;
			vertical-align: top;
		}
		.right-menu {
			float: right;
			height: 100%;
			&:focus {
				outline: none;
			}
			.right-menu-item {
				display: inline-block;
				margin: 0 8px;
			}
			.screenfull {
				height: 20px;
			}
			.international {
				vertical-align: top;
			}
			.theme-switch {
				vertical-align: 15px;
			}
			.avatar-container {
				height: 50px;
				margin-right: 30px;
				.avatar-wrapper {
					cursor: pointer;
					/* margin-top: 5px; */
					position: relative;
					.user-avatar {
						width: 40px;
						height: 40px;
						border-radius: 10px;
					}
					.el-icon-caret-bottom {
						position: absolute;
						right: -20px;
						top: 25px;
						font-size: 12px;
					}
				}
			}
		}
	}
</style>