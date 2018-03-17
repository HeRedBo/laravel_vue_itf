<template>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">权限树</h3>
                </div>

                <div id="treeAcl"></div>
                <div class="box-footer">
                    <button type="button" @click="handleAcl" class="btn btn-success">设置</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
 require('jstree/dist/themes/default/style.min.css');
 import {stack_error} from 'config/helper';
 export default {
     data() {
         return {
             treeData: null,
             treeDom : '#treeAcl'
         }
     },

     watch : {

     },

     mounted () {
        this.loadList();
     },

     methods : {
         loadList : function () 
         {
                var url = '/role/getAcl', that = this, id = this.$route.params.id;
                this.$http({
                    method : 'GET',
                    url : url,
                    params : {
                        id : id
                    }
                })
                .then(function(response) {
                    var {data} = response; 
                    that.treeData = data.data.tree;
                    that.initTree();
                    // 跳转到列表页
                })
                .catch(function(error) {
                    stack_error(error);
                });

         },
         initTree : function () {
             var that = this, treeData = this.treeData;
             $(that.treeDom).jstree({
                    'core': {
                        'check_callback': true,
                        "themes": {
                            "theme": "default",
                        },
                        'data': treeData
                    },
                    ui: {
                        theme_name : "classic"
                    },
                    "checkbox": {
                        cascade : "", three_state : true, whole_node: true
                    },
                    'plugins': ['types', 'checkbox', 'ui']
                });

         },
         handleAcl : function() {
            var url = '/role/setAcl', that = this, id = this.$route.params.id,permission = $.jstree.reference(this.treeDom).get_selected();

            this.$http({
                    method : 'POST',
                    url : url,
                    params : {
                        id : id,
                        permission : permission
                    }
                })
                .then(function(response) {
                    var {data} = response; 
                    that.$message({
                        showClose: true,
                        message: data.message,
                        type: 'success'
                    });
                    // 跳转到列表页
                    that.$router.push({ path: '/admin/role/index' })
                    // 跳转到列表页
                })
                .catch(function(error) {
                    stack_error(error);
                });
            
         }
     }
 }
</script>