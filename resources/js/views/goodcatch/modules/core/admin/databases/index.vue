<template>
    <div>
        <div class="admin_table_page_title">数据库信息</div>
        <div class="unline underm"></div>


        <div class="admin_table_list">
            <a-row>
                <a-col :span="6">
                    <a-space direction="vertical">
                        <div>
                            <a-input-search
                                    placeholder="搜索"
                                    @search="onSearch"
                            />
                        </div>
                        <div class="list_container">
                            <a-list
                                    size="small"
                                    class="list"
                                    :loading="loading"
                                    item-layout="horizontal"
                                    :data-source="data"
                            >
                                <a-list-item slot="renderItem" slot-scope="item, index" @click="showColumns(item.value)" :style="{color : (select_list_item === item.value ? '#c32617' : '')}">
                                    <a-list-item-meta>
                                        <span slot="title" :style="{color : (select_list_item === item.value ? '#c32617' : '')}">{{ item.schema + '.' + item.name }}</span>
                                    </a-list-item-meta>
                                    <div>
                                        {{ item.rows }}
                                    </div>
                                </a-list-item>
                            </a-list>
                        </div>
                        <div>共计{{ total }}条记录</div>
                    </a-space>
                </a-col>
                <a-col :span="1"></a-col>
                <a-col :span="17">
                    <a-table :columns="columns" :scroll="{ y: 330 }" :loading="table_loading" :data-source="table_columns" :pagination="false" row-key="id">

                    </a-table>
                </a-col>
            </a-row>
        </div>
    </div>
</template>

<script>
    export default {
        components: {},
        props: {},
        data() {
            return {
                search: '',
                total:0, //总页数
                loading: true,
                select_list_item: null,
                list: [],
                table_columns: [],
                table_loading: false,
                columns:[
                    {
                        title: '',
                        children: [
                            {title:'名称',dataIndex:'Field'},
                            {title:'类型',dataIndex:'Type'},
                            {title:'默认值',dataIndex:'Default'},
                            {title:'描述',dataIndex:'Extra'},
                            {title:'键',dataIndex:'Key'},
                            {title:'是否空',dataIndex:'Null'},
                        ]
                    }

                ],
            };
        },
        watch: {},
        computed: {
            data(){
                return this.list.filter(item => item.name.indexOf(this.search) > -1)
            }
        },
        methods: {
            onload(){
                this.$get(this.$api.moduleCoreDatabases,{}).then(res=>{
                    this.loading = false;
                    this.total = res.data.total;
                    this.list = res.data.data;
                });
            },
            onSearch(value){
                this.search = value;
            },
            showColumns(table){
                this.columns [0].title = table;
                this.table_loading = true;
                this.table_columns = [];
                this.select_list_item = table;
                this.$get(this.$api.moduleCoreDatabases,{ table }).then(res=>{
                    this.table_columns = res.data.data;
                    this.table_loading = false;
                });
            }
        },
        created() {
            this.onload();
        },
        mounted() {}
    };
</script>
<style lang="scss" scoped>
    .list_container{
        border: 1px solid #e8e8e8;
        border-radius: 4px;
        overflow: auto;
        padding: 8px 24px;
        height: 400px;
    }
</style>
