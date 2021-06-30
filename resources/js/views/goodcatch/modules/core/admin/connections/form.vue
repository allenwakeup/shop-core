<template>
    <div>
        <div class="admin_table_page_title">
            <a-button @click="$router.back()" class="float_right" icon="arrow-left">返回</a-button>
            编辑连接
        </div>
        <div class="unline underm"></div>
        <div class="admin_form">
            <a-form-model ref="form" :model="form" :rules="rules" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
                <a-form-model-item label="数据源" prop="datasource_id" v-show="showFormItem['datasource_id']">
                    <a-row :gutter="8">
                        <a-col :span="12">
                            <a-select
                                    show-search
                                    placeholder="选择数据源"
                                    :value="form.datasource_id"
                                    :model="form.datasource_id"
                                    :filter-option="false"
                                    not-found-content="没有更多的数据源"
                                    style="width: 200px"
                                    @search="handleDatasourceSearch"
                                    @change="handleDatasourceChange" >
                                <a-select-option v-for="item in datasources" :key="item.id" :value="item.id">
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-col>
                        <a-col :span="12">
                            <a-switch checked-children="更多选项" un-checked-children="更少选项" @change="onChangeMoreOptions" v-show="datasources.length > 0"/>
                        </a-col>
                    </a-row>
                </a-form-model-item>
                <a-form-model-item label="名称" prop="name" v-show="showFormItem['name']">
                    <a-input v-model="form.name"></a-input>
                </a-form-model-item>
                <a-form-model-item label="连接类型" prop="conn_type" v-show="showFormItem['conn_type']">
                    <a-input v-model="form.conn_type"></a-input>
                </a-form-model-item>
                <a-form-model-item label="TNS" prop="tns" v-show="showFormItem['tns']">
                    <a-textarea v-model="form.tns" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="驱动" prop="driver" v-show="showFormItem['driver']">
                    <a-input v-model="form.driver"></a-input>
                </a-form-model-item>
                <a-form-model-item label="主机名" prop="host" v-show="showFormItem['host']">
                    <a-input v-model="form.host"></a-input>
                </a-form-model-item>
                <a-form-model-item label="端口号" prop="port" v-show="showFormItem['port']">
                    <a-input-number v-model="form.port" :min="1024" @change="onChangePort" />
                </a-form-model-item>
                <a-form-model-item label="数据库名" prop="database" v-show="showFormItem['database']">
                    <a-input v-model="form.database"></a-input>
                </a-form-model-item>
                <a-form-model-item label="用户名" prop="username" v-show="showFormItem['username']">
                    <a-input v-model="form.username"></a-input>
                </a-form-model-item>
                <a-form-model-item label="密码" prop="password" v-show="showFormItem['password']">
                    <a-input v-model="form.password" type="password"></a-input>
                </a-form-model-item>
                <a-form-model-item label="URL" prop="url" v-show="showFormItem['url']">
                    <a-input v-model="form.url"></a-input>
                </a-form-model-item>
                <a-form-model-item label="服务名" prop="service_name" v-show="showFormItem['service_name']">
                    <a-input v-model="form.service_name"></a-input>
                </a-form-model-item>
                <a-form-model-item label="Socket路径" prop="unix_socket" v-show="showFormItem['unix_socket']">
                    <a-input v-model="form.unix_socket"></a-input>
                </a-form-model-item>
                <a-form-model-item label="字符编码" prop="charset" v-show="showFormItem['charset']">
                    <a-input v-model="form.charset"></a-input>
                </a-form-model-item>
                <a-form-model-item label="字符集" prop="collation" v-show="showFormItem['collation']">
                    <a-input v-model="form.collation"></a-input>
                </a-form-model-item>
                <a-form-model-item label="表前缀名" prop="prefix" v-show="showFormItem['prefix']">
                    <a-input v-model="form.prefix"></a-input>
                </a-form-model-item>
                <a-form-model-item label="资源前缀" prop="prefix_schema" v-show="showFormItem['prefix_schema']">
                    <a-input v-model="form.prefix_schema"></a-input>
                </a-form-model-item>
                <a-form-model-item label="资源名称" prop="schema" v-show="showFormItem['schema']">
                    <a-input v-model="form.schema"></a-input>
                </a-form-model-item>
                <a-form-model-item label="严格模式" prop="strict" v-show="showFormItem['strict']">
                    <a-switch checked-children="开启" un-checked-children="关闭" default-checked @change="onChangeStrictOptions"/>
                </a-form-model-item>
                <a-form-model-item label="Engine" prop="engine" v-show="showFormItem['engine']">
                    <a-input v-model="form.engine"></a-input>
                </a-form-model-item>
                <a-form-model-item label="版本限制" prop="edition" v-show="showFormItem['edition']">
                    <a-input v-model="form.edition"></a-input>
                </a-form-model-item>
                <a-form-model-item label="软件版本" prop="server_version" v-show="showFormItem['server_version']">
                    <a-input v-model="form.server_version"></a-input>
                </a-form-model-item>
                <a-form-model-item label="加密模式" prop="servesslmoder_version" v-show="showFormItem['sslmode']">
                    <a-input v-model="form.sslmode"></a-input>
                </a-form-model-item>
                <a-form-model-item label="其他选项" prop="options" v-show="showFormItem['options']">
                    <a-textarea v-model="form.options" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="分类" prop="type" v-show="showFormItem['type']">
                    <a-select v-model="form.type" default-value="SRC" style="width: 120px" @change="handleTypeChange">
                        <a-select-option value="SRC">
                            来源
                        </a-select-option>
                        <a-select-option value="DST">
                            目标
                        </a-select-option>
                    </a-select>
                </a-form-model-item>
                <a-form-model-item label="分组" prop="group" v-show="showFormItem['group']">
                    <a-input v-model="form.group"></a-input>
                </a-form-model-item>
                <a-form-model-item label="排序" prop="order" v-show="showFormItem['order']">
                    <a-input-number v-model="form.order" :min="0" @change="onChangeOrder" />
                </a-form-model-item>
                <a-form-model-item label="状态" prop="status" v-show="showFormItem['status']">
                    <a-switch checked-children="启用" un-checked-children="禁用" default-checked @change="onChangeStatus"/>
                </a-form-model-item>
                <a-form-model-item label="描述" prop="description" v-show="showFormItem['description']">
                    <a-textarea v-model="form.description" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item :wrapper-col="{ span: 12, offset: 5 }">
                    <a-row :gutter="8">
                        <a-col :span="6">
                            <a-button type="primary" @click="handleSubmit">提交</a-button>
                        </a-col>
                        <a-col :span="18">
                            <a-button v-if="!test" type="dashed" @click="handleSubmit('test')" :loading="loading_test">测试连接</a-button>
                            <a-button v-if="test" type="dashed" icon="check">连接成功</a-button>
                        </a-col>
                    </a-row>
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
                datasources: [],
                moreOption: false,
                datasource_requires: {},
                datasource_rules: {},
                form:{
                    datasource_id: '',
                    name: '',
                    description: '',
                    conn_type: '',
                    tns: '',
                    driver: '',
                    host: '',
                    port: 1433,
                    database: '',
                    username: '',
                    password: '',
                    url: '',
                    service_name: '',
                    unix_socket: '',
                    charset: '',
                    collation: '',
                    prefix: '',
                    prefix_schema: '',
                    strict: 0,
                    engine: '',
                    schema: '',
                    edition: '',
                    server_version: '',
                    sslmode: '',
                    options: '{}',
                    type: 'SRC',
                    group: '',
                    order: 1,
                    status: 1
                },
                id:0,
                loading_test: false,
                test: false
            };
        },
        watch: {},
        computed: {
            rules(){
                const default_rules = {

                    datasource_id: [
                        {
                            trigger: 'change',
                            required: true,
                            message: '请选择一个数据源'
                        }
                    ],
                    name: [
                        {
                            required: true,
                            message: '请填写名称'
                        }
                    ],
                    type: [
                        {
                            required: true,
                            message: '请选择一个连接类型'
                        }
                    ],
                    driver: [
                        {
                            required: true,
                            message: '请填写驱动'
                        }
                    ],
                    database: [
                        {
                            required: true,
                            message: '请填写数据库'
                        }
                    ],
                    group: [
                        {
                            required: true,
                            message: '请填写分组'
                        }
                    ]
                };
                const _rules = Object.assign({}, default_rules);
                for(const key in this.datasource_rules){
                    if(this.datasource_rules.hasOwnProperty(key)){
                        if(_rules.hasOwnProperty(key)){
                            _rules[key] = _rules[key].concat(this.datasource_rules[key]);
                        }else {
                            _rules[key] = this.datasource_rules[key];
                        }

                    }
                }
                return _rules;
            },
            showFormItem(){
                const items = {};
                for(const key in this.rules) {
                    if (this.rules.hasOwnProperty(key)) {
                        items[key] = true;
                    }
                }
                for(const key in this.datasource_requires) {
                    if (this.datasource_requires.hasOwnProperty(key)) {
                        if(this.datasource_requires[key] === true){
                            items[key] = true;
                        } else if (this.datasource_requires[key] === false && this.moreOption) {
                            items[key] = true;
                        }
                    }
                }
                items['options'] = this.moreOption;
                items['order'] = true;
                items['status'] = true;
                items['description'] = this.moreOption;
                return items;
            }
        },
        methods: {
            handleSubmit(test){

                this.$refs.form.validate(valid => {
                    if (valid) {
                        if(test){
                            this.loading_test = true;
                            this.$post(this.$api.moduleCoreTestConnection,this.form).then(res=>{
                                if(res.code == 200 || res.code == 201){
                                    this.loading_test = false;
                                    this.test = true;
                                    return this.$message.success(res.msg);
                                }else{
                                    this.loading_test = false;
                                    this.test = false;
                                    return this.$message.error(res.msg);
                                }
                            });

                        } else {
                            let api = this.$apiHandle(this.$api.moduleCoreConnections,this.id);
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
                        }

                    } else {
                        this.$message.error('请正确填写表单！');
                        return false;
                    }
                });


            },

            onChangeMoreOptions(checked){
                this.moreOption = checked;
            },

            onChangeStrictOptions(checked){
                this.form.strict = checked ? 1 : 0;
            },

            onChangeOrder(value){
                this.form.order = value;
            },
            onChangePort(value){
                this.form.port = value;
            },
            onChangeStatus(checked){
                this.form.status = checked ? 1 : 0;
            },
            get_form(){
                this.$get(this.$api.moduleCoreConnections+'/'+this.id).then(res=>{
                    this.form = res.data;
                });
            },
            getDatasourceSelector(params){
                this.$get(this.$api.moduleCoreDatasources, params).then(res=>{
                    if(res.data && res.data.data){
                        this.datasources = res.data.data;
                        let datasource = null;
                        if(this.$isEmpty(this.form.datasource_id)){
                            datasource = this.datasources [0];
                            this.form.datasource_id = datasource.id;
                        }else{
                            datasource = this.datasources.filter(item=>item.id === this.form.datasource_id) [0];
                        }
                        this.setDynamicFormItems(datasource);
                    }

                });
            },
            handleTypeChange(value) {
                this.form.type = value;
            },
            handleDatasourceChange(value) {
                this.form.datasource_id = value;
                const datasource = this.datasources.filter(item=>item.id === value) [0];
                this.setDynamicFormItems(datasource);
            },
            handleDatasourceSearch(value) {
                this.getDatasourceSelector({keyword: value, type: 'selector'});
            },
            setDynamicFormItems(datasource){
                if(datasource){
                    this.form.driver = datasource.code;
                    this.datasource_requires = {};
                    let rules = {};
                    datasource.requires.split(',').forEach (field => {
                        const [key, value] = field.split (':');
                        this.datasource_requires [key] = true;
                        this.form [key] = value;
                        rules[key] = {
                            required: true,
                            message: '请填写内容'
                        }
                    });
                    datasource.options.split(',').forEach (field => {
                        const [key, value] = field.split (':');
                        this.datasource_requires [key] = false;
                        this.form [key] = value;
                    });
                    this.datasource_rules = rules;

                    this.test = false;
                }
            },

            // 获取列表
            onload(){
                // 判断你是否是编辑
                if(!this.$isEmpty(this.$route.params.id)){
                    this.id = this.$route.params.id;
                    this.get_form();
                }
                this.getDatasourceSelector({});
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
