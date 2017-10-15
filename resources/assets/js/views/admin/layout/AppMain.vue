<template>
	<section class="content" style="min-height: 100%; border-left: 1px solid #d2d6de;
	background-color:rgb(228, 232, 239);
	z-index: 800;">
		<!-- 动态组件 -->
		<transition name="fade" mode="out-in">
		  <router-view :key="key"></router-view>
		</transition>
	</section>

	
</template>

<script>

export default {
	name : 'AppMain',
	computed : {
		key() {
			return this.$route.name !== 'undefined' ? this.$route.name + new Date() : this.$route + +new Date()
		}
	}
}
</script>