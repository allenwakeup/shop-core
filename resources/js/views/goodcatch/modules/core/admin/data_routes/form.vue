<template>
    <div>
        <div class="admin_table_page_title">
            <a-button @click="$router.back()" class="float_right" icon="arrow-left">返回</a-button>
            数据路径编辑
        </div>
        <div class="unline underm"></div>
        <div class="admin_form">
            <a-form-model ref="form" :model="form" :rules="rules" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
                <a-form-model-item label="名称" prop="name">
                    <a-input v-model="form.name" placeholder="请输入路径名称">
                        <a-tooltip slot="suffix" title="数据路径的全称，尽可能保持路径的完整名称，通常夸一个模型进行路径定义。例如用户与客户的路径，可以是用户直接到客户，同时也可以是用户先到部门，部门再到客户。">
                            <a-icon type="info-circle" />
                        </a-tooltip>
                    </a-input>
                </a-form-model-item>
                <a-form-model-item label="简称" prop="short">
                    <a-input v-model="form.short" placeholder="请输入路径简称">
                        <a-tooltip slot="suffix" title="数据路径的简称，用于简单显示">
                            <a-icon type="info-circle" />
                        </a-tooltip>
                    </a-input>
                </a-form-model-item>
                <a-form-model-item label="别名" prop="alias">
                    <a-input v-model="form.alias" placeholder="请输入路径别名">
                        <a-tooltip slot="suffix" title="数据路径的别名，通常是通俗一点的名称，例如别名会用作菜单的分组名称。">
                            <a-icon type="info-circle" />
                        </a-tooltip>
                    </a-input>
                </a-form-model-item>
                <a-form-model-item label="首表名称" prop="from">
                    <a-input v-model="form.from" placeholder="请输入首表名称">
                        <a-tooltip slot="suffix" title="首部表的名称表示路径的起始名称。">
                            <a-icon type="info-circle" />
                        </a-tooltip>
                    </a-input>
                </a-form-model-item>
                <a-form-model-item label="首表表名" prop="table_from">
                    <a-select
                        show-search
                        placeholder="选择首部表"
                        :value="form.table_from"
                        :model="form.table_from"
                        :filter-option="false"
                        not-found-content="没有更多的表"
                        @search="handleTableFromSearch"
                        @change="handleTableFromChange" >
                        <a-select-option v-for="item in from_tables" :key="item.id" :value="item.id">
                            {{ item.name }}
                        </a-select-option>
                    </a-select>
                </a-form-model-item>
                <a-form-model-item label="尾表名称" prop="to">
                    <a-input v-model="form.to" placeholder="请输入尾表名称">
                        <a-tooltip slot="suffix" title="尾部表的名称表示路径的结束名称。">
                            <a-icon type="info-circle" />
                        </a-tooltip>
                    </a-input>
                </a-form-model-item>
                <a-form-model-item label="尾表表名" prop="table_to">
                    <a-select
                        show-search
                        placeholder="选择尾部表"
                        :value="form.table_to"
                        :model="form.table_to"
                        :filter-option="false"
                        not-found-content="没有更多的表"
                        @search="handleTableToSearch"
                        @change="handleTableToChange" >
                        <a-select-option v-for="item in to_tables" :key="item.id" :value="item.id">
                            {{ item.name }}
                        </a-select-option>
                    </a-select>
                </a-form-model-item>
                <a-form-model-item label="目标表名" prop="output">
                    <a-input v-model="form.output" placeholder="请输入数据库完整表名。注：表名一旦设置，均不可修改，请谨慎处理。">
                        <a-tooltip slot="suffix" title="目标表的表名将自动加入表前缀「sync_」。表名一旦设置，均不可修改，请谨慎处理。">
                            <a-icon type="info-circle" />
                        </a-tooltip>
                    </a-input>
                </a-form-model-item>
                <a-form-model-item label="使用数据库连接" prop="connection_id">

                    <a-select
                        show-search
                        placeholder="选择连接"
                        :value="form.connection_id"
                        :model="form.connection_id"
                        :filter-option="false"
                        not-found-content="没有更多的连接"
                        @search="handleConnectionSearch"
                        @change="handleConnectionChange" >
                        <a-select-option v-for="item in connections" :key="item.id" :value="item.id">
                            {{ item.name }}
                        </a-select-option>
                    </a-select>

                </a-form-model-item>
                <a-form-model-item label="描述" prop="description">
                    <a-textarea v-model="form.description" placeholder="请输入描述信息。" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item :wrapper-col="{ span: 12, offset: 5 }">
                    <a-button type="primary" @click="handleSubmit">提交</a-button>
                </a-form-model-item>
            </a-form-model>

        </div>
    </div>
</template>

<script>
    export default {
        components: {},
        props: {},
        data() {
            return {
                connections: [],
                from_tables: [],
                to_tables: [],
                form:{
                    name: '',
                    short: '',
                    alias: '',
                    from: '',
                    table_from: '',
                    to: '',
                    table_to: '',
                    output: '',
                    connection_id: '',
                    description: '',
                },
                id:0,
                rules: {
                    name: [
                        {
                            required: true,
                            message: '请填写名称'
                        }
                    ],
                    from: [
                        {
                            required: true,
                            message: '请填写首表的名称'
                        }
                    ],
                    table_from: [
                        {
                            required: true,
                            message: '请填写首表的数据库表名'
                        }
                    ],
                    to: [
                        {
                            required: true,
                            message: '请填写尾表的名称'
                        }
                    ],
                    table_to: [
                        {
                            required: true,
                            message: '请填写尾表的数据库表名'
                        }
                    ],
                    output: [
                        {
                            required: true,
                            message: '请填写目标表的数据库表名'
                        }
                    ],
                    connection_id: [
                        {
                            trigger: 'change',
                            required: true,
                            message: '请选择一个连接'
                        }
                    ]
                },
            };
        },
        watch: {},
        computed: {

        },
        methods: {
            handleSubmit(){

                this.$refs.form.validate(valid => {
                    if (valid) {
                        let api = this.$apiHandle(this.$api.moduleCoreDataRoutes,this.id);
                        if(api.status){
                            this.$put(api.url,this.form).then(res=>{
                                if(res.code == 200){
                                    this.$message.success(res.msg)
                                    return this.$router.back();
                                }else{
                                    return this.$message.error(res.msg)
                                }
                            })
                        }else{
                            this.$post(api.url,this.form).then(res=>{
                                if(res.code == 200 || res.code == 201){
                                    this.$message.success(res.msg)
                                    return this.$router.back();
                                }else{
                                    return this.$message.error(res.msg);
                                }
                            })
                        }
                    } else {
                        this.$message.error('请正确填写表单！');
                        return false;
                    }
                });
            },

            handleConnectionSearch(value) {
                this.getConnectionSelector({keyword: value});
            },
            handleConnectionChange(value) {
                this.form.connection_id = value;
            },
            handleTableFromSearch(value) {
                this.getTableFromSelector({keyword: value});
            },
            handleTableFromChange(value) {
                this.form.table_from = value;
            },
            handleTableToSearch(value) {
                this.getTableToSelector({keyword: value});
            },
            handleTableToChange(value) {
                this.form.table_to = value;
            },
            get_form(){
                this.$get(this.$api.moduleCoreDataRoutes+'/'+this.id).then(res=>{
                    this.form = res.data;
                });
            },
            getConnectionSelector(params){
                this.$get(this.$api.moduleCoreConnections, params).then(res=>{
                    if(res.data && res.data.data){
                        this.connections = res.data.data;
                    }
                });
            },
            getTableFromSelector(params){
                this.$get(this.$api.moduleCoreDatabases, params).then(res=>{
                    if(res.data && res.data.data){
                        this.from_tables = res.data.data;
                    }
                });
            },
            getTableToSelector(params){
                this.$get(this.$api.moduleCoreDatabases, params).then(res=>{
                    if(res.data && res.data.data){
                        this.to_tables = res.data.data;
                    }
                });
            },
            // 获取列表
            onload(){
                // 判断你是否是编辑
                if(!this.$isEmpty(this.$route.params.id)){
                    this.id = this.$route.params.id;
                    this.get_form();
                }
                this.getConnectionSelector();
                this.getTableFromSelector();
                this.getTableToSelector();
            },
        },
        created() {
            this.onload();
        },
        mounted() {}
    };
</script>
<style lang="scss" scoped>

    .payload-modal{
        width: 100%;
    }
</style>
