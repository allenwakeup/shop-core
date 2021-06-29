<template>
    <div>
        <div class="admin_table_page_title">连接列表</div>
        <div class="unline underm"></div>

        <div class="admin_table_handle_btn">
            <a-button @click="$router.push('/Admin/goodcatch/m/core/connections/form')" type="primary" icon="plus">添加</a-button>
            <a-button class="admin_delete_btn" type="danger" icon="delete" @click="del">批量删除</a-button>
        </div>
        <div class="admin_table_list">
            <a-table :columns="columns" :data-source="list" :scroll="{ x: 4096, y: 400 }" :pagination="false" :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }" row-key="id">
                <span slot="datasource_id" slot-scope="record">
                    {{ record.datasource ? record.datasource.name : '--' }}
                </span>
                <span slot="action" slot-scope="rows">
                    <a-button icon="edit" @click="$router.push('/Admin/goodcatch/m/core/connections/form/'+rows.id)">编辑</a-button>
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
              {title:'数据源', scopedSlots:{ customRender: 'datasource_id' }, width: 180},
              {title:'名称',dataIndex:'name', width: 180},
              {title:'描述',dataIndex:'description', width: 280},
              {title:'连接类型',dataIndex:'conn_type', width: 120},
              {title:'TNS',dataIndex:'tns', width: 120},
              {title:'驱动',dataIndex:'driver', width: 120},
              {title:'主机名',dataIndex:'host', width: 180},
              {title:'端口号',dataIndex:'port', width: 120},
              {title:'数据库名',dataIndex:'database', width: 150},
              {title:'用户',dataIndex:'username', width: 120},
              {title:'密码',dataIndex:'password', width: 120},
              {title:'连接地址',dataIndex:'url', width: 180},
              {title:'服务名',dataIndex:'service_name', width: 120},
              {title:'通信地址',dataIndex:'unix_socket', width: 120},
              {title:'字符编码',dataIndex:'charset', width: 180},
              {title:'字符集',dataIndex:'collation', width: 200},
              {title:'表前缀名',dataIndex:'prefix', width: 120},
              {title:'资源前缀',dataIndex:'prefix_schema', width: 120},
              {title:'严格模式',dataIndex:'strict', width: 90},
              {title:'Engine',dataIndex:'engine', width: 120},
              {title:'资源名',dataIndex:'schema', width: 120},
              {title:'版本限制',dataIndex:'edition', width: 100},
              {title:'软件版本',dataIndex:'server_version', width: 90},
              {title:'加密模式',dataIndex:'sslmode', width: 90},
              {title:'附加设置',dataIndex:'options', width: 180},
              {title:'数据方向',dataIndex:'type', width: 120},
              {title:'分组',dataIndex:'group', width: 120},
              {title:'排序',dataIndex:'order', width: 100},
              {title:'状态',dataIndex:'status', width: 90},
              {title:'创建时间',dataIndex:'created_at', width: 200},
              {title:'更新时间',dataIndex:'updated_at', width: 200},
              {title:'操作',key:'id',fixed:'right',scopedSlots: { customRender: 'action' }},
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
                    this.$delete(this.$api.moduleCoreConnections+'/'+ids).then(res=>{
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
            this.$get(this.$api.moduleCoreConnections,this.params).then(res=>{
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
