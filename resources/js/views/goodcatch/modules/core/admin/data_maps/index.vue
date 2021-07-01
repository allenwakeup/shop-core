<template>
    <div>
        <div class="admin_table_page_title">数据映射列表</div>
        <div class="unline underm"></div>

        <div class="admin_table_handle_btn">
            <a-button @click="$router.push('/Admin/goodcatch/m/core/schedules/form')" type="primary" icon="plus">添加</a-button>
            <a-button class="admin_delete_btn" type="danger" icon="delete" @click="del">批量删除</a-button>
        </div>
        <div class="admin_table_list">
            <a-table :columns="columns" :data-source="list" :scroll="{ x: 2048, y: 400 }" :pagination="false" :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }" row-key="id">

                <span slot="action" slot-scope="rows">
                    <a-button icon="edit" @click="$router.push('/Admin/goodcatch/m/core/schedules/form/'+rows.id)">编辑</a-button>
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
              {title:'名称',dataIndex:'name', width: 180},
              {title:'指令',dataIndex:'input', width: 280},
              {title:'执行周期',dataIndex:'cron', width: 120},
              {title:'执行前访问地址',dataIndex:'ping_before', width: 120},
              {title:'执行成功访问地址',dataIndex:'ping_success', width: 120},
              {title:'执行失败访问地址',dataIndex:'ping_failure', width: 180},
              {title:'详细配置',dataIndex:'payload', width: 120},
              {title:'描述',dataIndex:'description', width: 150},
              {title:'任务类型',dataIndex:'schedule_type', width: 120},
              {title:'是否覆盖相同任务',dataIndex:'overlapping', width: 120},
              {title:'单例模式',dataIndex:'one_server', width: 180},
              {title:'前/后台',dataIndex:'background', width: 120},
              {title:'维护模式下执行',dataIndex:'maintenance', width: 120},
              {title:'不重复执行',dataIndex:'once', width: 180},
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
                    this.$delete(this.$api.moduleCoreSchedules+'/'+ids).then(res=>{
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
            this.$get(this.$api.moduleCoreSchedules,this.params).then(res=>{
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
