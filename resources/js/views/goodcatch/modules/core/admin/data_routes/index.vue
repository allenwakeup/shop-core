<template>
    <div>
        <div class="admin_table_page_title">数据路径列表</div>
        <div class="unline underm"></div>

        <div class="admin_table_handle_btn">
            <a-button @click="$router.push('/Admin/goodcatch/m/core/data_routes/form')" type="primary" icon="plus">添加</a-button>
            <a-button class="admin_delete_btn" type="danger" icon="delete" @click="del">批量删除</a-button>
        </div>
        <div class="admin_table_list">
            <a-table :columns="columns" :data-source="list" :scroll="{ x: 2048, y: 400 }" :pagination="false" :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }" row-key="id">
                <span slot="connection_id" slot-scope="record">
                    {{ record.connection ? (record.connection.datasource ? (record.connection.name + '(' + record.connection.datasource.name + ')') : record.connection.name) : '--' }}
                </span>
                <span slot="action" slot-scope="rows">
                    <a-button icon="edit" @click="$router.push('/Admin/goodcatch/m/core/data_routes/form/'+rows.id)">编辑</a-button>
                </span>
            </a-table>
            <div class="admin_pagination" v-if="total>0">
                <a-pagination v-model="params.page" :page-size.sync="params.per_page" :total="total" @change="onChange" show-less-items />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: {},
    data() {
      return {
          params:{
              page:1,
              per_page:30,
          },
          total:0, //总页数
          selectedRowKeys:[], // 被选择的行
          columns:[
              {title:'#',dataIndex:'id',fixed:'left', width: 80},
              {title:'名称',dataIndex:'name', width: 200},
              {title:'简称',dataIndex:'short', width: 180},
              {title:'别名',dataIndex:'alias', width: 180},
              {title:'首表名称',dataIndex:'from', width: 120},
              {title:'首表表名',dataIndex:'table_from', width: 120},
              {title:'尾表名称',dataIndex:'to', width: 120},
              {title:'尾表表名',dataIndex:'table_to', width: 120},
              {title:'目标表',dataIndex:'output', width: 150},
              {title:'连接', scopedSlots:{ customRender: 'connection_id' }, width: 220},
              {title:'描述',dataIndex:'description', width: 280},
              {title:'创建时间',dataIndex:'created_at', width: 200},
              {title:'更新时间',dataIndex:'updated_at', width: 200},
              {title:'操作',fixed:'right',scopedSlots: { customRender: 'action' }},
          ],
          list:[],
      };
    },
    watch: {},
    computed: {},
    methods: {
        // 选择框被点击
        onSelectChange(selectedRowKeys) {
            this.selectedRowKeys = selectedRowKeys;
        },
        // 选择分页
        onChange(e){
            this.params.page = e;
        },
        // 删除
        del(){
            if(this.selectedRowKeys.length==0){
                return this.$message.error('未选择数据.');
            }
            this.$confirm({
                title: '你确定要删除选择的数据？',
                content: '确定删除后无法恢复.',
                okText: '是',
                okType: 'danger',
                cancelText: '取消',
                onOk:()=> {
                    let ids = this.selectedRowKeys.join(',');
                    this.$delete(this.$api.moduleCoreDataRoutes+'/'+ids).then(res=>{
                        if(res.code == 200){
                            this.onload();
                            this.$message.success('删除成功');
                        }else{
                            this.$message.error(res.msg)
                        }
                    });

                },
            });
        },

        onload(){
            this.$get(this.$api.moduleCoreDataRoutes,this.params).then(res=>{
                this.total = res.data.total;
                this.list = res.data.data;
            });
        },
    },
    created() {
        this.onload();
    },
    mounted() {}
};
</script>
<style lang="scss" scoped>

</style>
