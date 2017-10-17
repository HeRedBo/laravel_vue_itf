<template>
	<div class="tabs-view-container">
		<router-link class="tabs-view" v-for="tag in Array.from(visitedViews)" :to="tag.path" :key="tag.path">
			<el-tag :closable="true" :type="isActive(tag.path) ? 'primary' : '' " @close="closeViewTags(tag,$event)"> 
          {{tag.name}}
      </el-tag>
		</router-link>	
	</div>
</template>
<script>

export default {
  computed : {
     visitedViews () {
        return this.$store.state.app.visitedViews.slice(-6);
     }
  },
  methods : {
    closeViewTags (view, $event) {
       this.$store.dispatch('delVisitedViews', view).then((views) => {
         if(this.isActive(view.path)) {
           const latestView = views.slice(-1)[0];
           if(latestView) {
             this.$route.push(latestView.path);
           } else {
             this.$route.push('/');
           }
         }
        
       })
       $event.preventDefault();
    },
    generateRoute() {

      if (this.$route.matched[this.$route.matched.length - 1].name) {
        return this.$route.matched[this.$route.matched.length - 1]
      }
      this.$route.matched[0].path = '/'
      return this.$route.matched[0]
    },
    isActive(path) {
      return path === this.$route.path;
    },
    addViewTabs() {
      this.$store.dispatch('addVisitedViews', this.generateRoute())
    }
  },
  watch : {
    $route() {
      console.log('route')
      this.addViewTabs();
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .tabs-view-container {
    display: inline-block;
    vertical-align: top;
    margin-left: 10px;
    .tabs-view {
      margin-left: 10px;
    }
  }
</style>
