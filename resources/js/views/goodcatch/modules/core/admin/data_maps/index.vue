<template>
    <div>
        <div class="admin_table_page_title">数据映射列表</div>
        <div class="unline underm"></div>
        <div class="admin_table_handle_btn">
            <a-button @click="$router.push('/Admin/goodcatch/m/core/data_maps/form')" type="primary" icon="plus">添加</a-button>
            <a-button class="admin_delete_btn" type="danger" icon="delete" @click="del">批量删除</a-button>
        </div>
        <div class="admin_table_list">
            <a-table :columns="columns" :data-source="list" :scroll="{ x: 2048, y: 400 }" :pagination="false" :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }" row-key="id">

                <span slot="data_route_id" slot-scope="record">
                    {{ record.dataRoute ? record.dataRoute.name : record.data_route_id }}
                </span>
                <span slot="left" slot-scope="record">
                    {{ record.left + record.left_table }}
                </span>
                <span slot="right" slot-scope="record">
                    {{ record.right + record.right_table }}
                </span>
                <span slot="action" slot-scope="rows, record">
                    <a-button icon="edit" @click="$router.push('/Admin/goodcatch/m/core/data_maps/form/'+rows.id)">编辑</a-button>
                    <a-button icon="interaction" @click="handleAssignment(record)">分配</a-button>
                </span>
            </a-table>
            <div class="admin_pagination" v-if="total>0">
                <a-pagination v-model="params.page" :page-size.sync="params.per_page" :total="total" @change="onChange" show-less-items />
            </div>
        </div>
        <a-modal
                v-model="openAssignmentModal"
                title="数据映射"
                :body-style="{ overflow: 'scroll', height: '500px' }"
                :dialog-style="{ top: '20px' }"
                width="80%"
                height="600px">
            <template slot="footer">
                <span></span>
            </template>
            <a-assignment :assignment-id="selectedAssignment.id"></a-assignment>
        </a-modal>
    </div>
</template>

<script>

    import AAssignment from "./assignment";

    export default {
        components: { AAssignment },
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
                    {title:'数据路径', scopedSlots:{ customRender: 'data_route_id' }, width: 220},
                    {title:'左表', scopedSlots:{ customRender: 'left' }, width: 220},
                    {title:'左表模板',dataIndex:'left_tpl', width: 180},
                    {title:'右表', scopedSlots:{ customRender: 'right' }, width: 220},
                    {title:'右表模板',dataIndex:'right_tpl', width: 180},
                    {title:'关联关系',dataIndex:'relationshipText', width: 180},
                    {title:'Foreign Pivot Key',dataIndex:'foreign_pivot_key', width: 200},
                    {title:'Related Pivot Key',dataIndex:'related_pivot_key', width: 200},
                    {title:'Parent Key',dataIndex:'parent_key', width: 200},
                    {title:'Related Key',dataIndex:'related_key', width: 200},
                    {title:'多态前缀',dataIndex:'name', width: 180},
                    {title:'描述',dataIndex:'description', width: 150},
                    {title:'存储位置',dataIndex:'table', width: 180},
                    {title:'状态',dataIndex:'status', width: 90},
                    {title:'创建时间',dataIndex:'created_at', width: 200},
                    {title:'更新时间',dataIndex:'updated_at', width: 200},
                    {title:'操作',fixed:'right',scopedSlots: { customRender: 'action' }},
                ],
                list:[],
                selectedAssignment: {},
                openAssignmentModal: false
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
                if(this.selectedRowKeys.length===0){
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
                        this.$delete(this.$api.moduleCoreDataMaps+'/'+ids).then(res=>{
                            if(res.code === 200){
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
                this.$get(this.$api.moduleCoreDataMaps,this.params).then(res=>{
                    this.total = res.data.total;
                    this.list = res.data.data;
                });
            },
            handleAssignment(record) {
                this.selectedAssignment = record;
                this.openAssignmentModal = true;
            }
        },
        created() {
            this.onload();
        },
        mounted() {}
    };
</script>
<style lang="scss" scoped>

</style>
