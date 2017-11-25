<template>
    <div class="row">
        
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-header">
                        
                    </div>

                </div>

                <vTable ref="table"
                        stripped
                        hover
                        :ajax_url="ajax_url"
                        :params="params"
                        :items="items"
                        :fields="fields"
                        :current-page="currentPage"
                        :per-page="perPage"
                        :del="del"
                >
                    <template slot="actions" slot-scope="item">
                        <div class="btn-group">
                            <router-link :to="{ path: 'update/'+item.item.id}" class="btn bg-orange btn-xs">编辑</router-link>
                            <a href="#"  @click.prevent="$refs.table.onDel(item.item.id)"  class="btn btn-danger btn-xs">删除</a>
                        </div>
                    </template>

                </vTable>
            </div>
        </div>



    </div>
</template>

<script>
    export default {
       data () {
            return {
                items: [],
                fields: {
                    id: {label: 'ID', sortable: true},
                    operator_name: {label: '操作人'},
                    created_at:{label:'操作时间', sortable: true},
                    operation: {label: '操作'},
                    field: {label: '字段'},
                    oldValue: {label: '旧值'},
                    newValue: {label: '新值'}
                },
                ajax_url: "/card/cardLogger",
                params: {keyword: ''},
                currentPage: 1,
                perPage: 15,
                del: {url:'/admin/user',title:'确定要删除用户吗?',successText:'删除后台用户成功!'},
            }
        },
    }
</script>

