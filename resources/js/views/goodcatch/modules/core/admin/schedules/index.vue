<template>
    <div>
        <div class="admin_table_page_title">计划与任务列表</div>
        <div class="unline underm"></div>

        <div class="admin_table_handle_btn">
            <a-button @click="$router.push('/Admin/goodcatch/m/core/schedules/form')" type="primary" icon="plus">添加</a-button>
            <a-button class="admin_delete_btn" type="danger" icon="delete" @click="del">批量删除</a-button>
        </div>
        <div class="admin_table_list">
            <a-table :columns="columns" :data-source="list" :scroll="{ x: 2048, y: 400 }" :pagination="false" :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }" row-key="id">

                <div slot="id" slot-scope="record" @click="handleIdClick(record)">{{ record.id }}</div>

                <div slot="name" slot-scope="record">
                    <a-icon type="play-circle" theme="twoTone" two-tone-color="#eb2f96" :spin="loading_start['_' + record.id]" @click="handleNameClick(record)" style="font-size: 24px;"/>
                    {{ record.name }}
                </div>

                <div slot="schedule_type" slot-scope="record">
                    {{ dictionary.schedule_type [record.schedule_type % dictionary.schedule_type.length] }}
                </div>
                <div slot="once" slot-scope="record">
                    {{ dictionary.once [record.once % dictionary.once.length] }}
                </div>
                <div slot="overlapping" slot-scope="record">
                    {{ dictionary.overlapping [record.overlapping % dictionary.overlapping.length] }}
                </div>
                <div slot="one_server" slot-scope="record">
                    {{ dictionary.one_server [record.one_server % dictionary.one_server.length] }}
                </div>
                <div slot="background" slot-scope="record">
                    {{ dictionary.background [record.background % dictionary.background.length] }}
                </div>
                <div slot="maintenance" slot-scope="record">
                    {{ dictionary.maintenance [record.maintenance % dictionary.maintenance.length] }}
                </div>

                <a-switch
                    slot="status"
                    :loading="loading_status['_' + record.id]"
                    @change="onStatusChange(record)"
                    slot-scope="record"
                    :checked-children="dictionary.status.enabled"
                    :un-checked-children="dictionary.status.disabled"
                    :default-checked="record.status === 1" />

                <span slot="action" slot-scope="rows">
                    <a-button icon="edit" @click="$router.push('/Admin/goodcatch/m/core/schedules/form/'+rows.id)">编辑</a-button>
                </span>
            </a-table>
            <div class="admin_pagination" v-if="total>0">
                <a-pagination v-model="params.page" :page-size.sync="params.per_page" :total="total" @change="onChange" show-less-items />
            </div>
        </div>
        <a-modal
            v-model="logs.show"
            title="任务详情">
            <template slot="footer">
                <a-button key="submit" type="primary" @click="() => this.logs.show = false">
                    知道了
                </a-button>
            </template>
            <a-table :columns="logs.columns" :data-source="logs.list" :pagination="false" row-key="id">
            </a-table>
            <div class="admin_pagination" v-if="logs.total>0">
                <a-pagination v-model="logs.params.page" :page-size.sync="logs.params.per_page" :total="logs.total" @change="onChangeLogs" show-less-items />
            </div>
        </a-modal>
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
              {title:'#',scopedSlots: { customRender: 'id' },fixed:'left', width: 80},
              {title:'名称',scopedSlots: { customRender: 'name' }, width: 180},
              {title:'描述',dataIndex:'description', width: 150},
              {title:'状态',scopedSlots: { customRender: 'status' }, width: 90},
              {title:'指令',dataIndex:'input', width: 280},
              {title:'执行周期',dataIndex:'cron', width: 120},
              {title:'任务类型',scopedSlots: { customRender: 'schedule_type' }, width: 120},
              {title:'分组',dataIndex:'group', width: 120},
              {title:'排序',dataIndex:'order', width: 100},
              {title:'单次任务',scopedSlots: { customRender: 'once' }, width: 180},
              {title:'重复',scopedSlots: { customRender: 'overlapping' }, width: 120},
              {title:'集群',dataIndex:'one_server', width: 180},
              {title:'后台执行',dataIndex:'background', width: 120},
              {title:'维护模式',dataIndex:'maintenance', width: 120},
              {title:'创建时间',dataIndex:'created_at', width: 200},
              {title:'更新时间',dataIndex:'updated_at', width: 200},
              {title:'操作',fixed:'right',scopedSlots: { customRender: 'action' }},
          ],
          list:[],
          dictionary: {
              status: {
                  enabled: '启用',
                  disabled: '禁用'
              },
              schedule_type: ['--', 'Command', 'Exec', 'Job'],
              once: ['可循环', '单次'],
              overlapping: ['可重复', '不可重复'],
              one_server: ['多服务器', '单服务器'],
              background: ['前台执行', '后台执行'],
              maintenance: ['不执行', '执行']
          },
          loading_status: {},
          loading_start: {},
          logs: {
              show: false,
              total: 0,
              list: [],
              params:{
                  page:1,
                  per_page:30
              },
              columns:[
                  {title:'状态',dataIndex:'status_text'},
                  {title:'内容',dataIndex:'content'},
                  {title:'时间',dataIndex:'created_at_text'}
              ],
          },
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
        onStatusChange(record){
            this.loading_status ['_' + record.id] = true;
            this.$put(this.$api.moduleCoreSchedules + '/' + record.id, Object.assign({}, record, {
                status: 1,
                logs: '',
                payload: record.payload ? JSON.stringify(record.payload) : ''
            })).then(res=>{
                this.loading_status ['_' + record.id] = false;
                if(res.code == 200){
                    this.$message.success(res.msg);
                }else{
                    return this.$message.error(res.msg);
                }
            }).catch(()=>this.loading_status ['_' + record.id] = false);
        },
        handleNameClick(record){
            this.loading_start['_' + record.id] = true;
            this.$put(this.$api.moduleCoreSchedules + '/' + record.id, Object.assign({}, record, {
                start: 1,
                logs: '',
                payload: record.payload ? JSON.stringify(record.payload) : ''
            })).then(res=>{
                this.loading_start['_' + record.id] = false;
                if(res.code == 200){
                    this.$message.success(res.msg);
                }else{
                    return this.$message.error(res.msg);
                }
            }).catch(()=>this.loading_start['_' + record.id] = false);
        },
        handleIdClick(record){
            this.logs.show = true;
            this.$get(this.$api.moduleCoreSchedules + '/' + record.id + '/logs',this.logs.params).then(res=>{
                this.logs.total = res.data.total;

                this.logs.list = res.data.data;
            });
        },
        onChangeLogs(e){
            this.logs.params.page = e;
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
