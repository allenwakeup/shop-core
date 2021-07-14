<template>
    <div>
        <div class="admin_table_page_title">
            <a-button @click="$router.back()" class="float_right" icon="arrow-left">返回</a-button>
            数据映射编辑
        </div>
        <div class="unline underm"></div>
        <div class="admin_form">
            <a-form-model ref="form" :model="form" :rules="rules">
                <a-row>
                    <a-col>
                        <a-form-model-item label="数据路径" :labelCol="{span:2}" :wrapperCol="{span:8}" prop="data_route_id">
                            <a-select
                                show-search
                                placeholder="选择数据路径"
                                :value="form.data_route_id"
                                :model="form.data_route_id"
                                :filter-option="false"
                                not-found-content="没有更多的数据路径"
                                @search="handleDataRouteSearch"
                                @change="handleDataRouteChange" >
                                <a-select-option v-for="item in data_routes" :key="item.id" :value="item.id">
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>
                    </a-col>
                </a-row>

                <a-row>
                    <a-col :span="12">
                        <a-form-model-item label="左表名称" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="left">
                            <a-input v-model="form.left" placeholder="请输入左表名称" />
                        </a-form-model-item>
                    </a-col>
                    <a-col :span="12">
                        <a-form-model-item label="左表" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="left_table">
                            <a-select
                                show-search
                                placeholder="选择左表"
                                :value="form.left_table"
                                :model="form.left_table"
                                :filter-option="false"
                                not-found-content="没有更多的数据库表"
                                @search="handleLeftTableSearch"
                                @change="handleLeftTableChange" >
                                <a-select-option v-for="item in left_tables" :key="item.id" :value="item.id">
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col>
                        <a-form-model-item label="左表模板" :labelCol="{span:2}" :wrapperCol="{span:16}" prop="left_tpl">
                            <a-textarea v-model="form.left_tpl" placeholder="用来显示的列，支持字段名+转换规则，
如：表列名有name、department，想要显示成「department」name前两个字符的拼接格式，
可以设置成 department::prepend:「|append:」+name::substr:0,2
" :auto-size="{ minRows: 5, maxRows: 8 }" />
                        </a-form-model-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col>
                        <a-form-model-item label="关联关系" :labelCol="{span:2}" :wrapperCol="{span:8}">
                            <a-select
                                default-value="morphToMany"
                                disabled >
                                <a-select-option v-for="item in relationships" :key="item.value" :value="item.value">
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col :span="12">
                        <a-form-model-item label="右表名称" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="right">
                            <a-input v-model="form.right" placeholder="请输入右表名称" />
                        </a-form-model-item>
                    </a-col>
                    <a-col :span="12">
                        <a-form-model-item label="右表" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="right_table">
                            <a-select
                                show-search
                                placeholder="选择右表"
                                :value="form.right_table"
                                :model="form.right_table"
                                :filter-option="false"
                                not-found-content="没有更多的数据库表"
                                @search="handleRightTableSearch"
                                @change="handleRightTableChange" >
                                <a-select-option v-for="item in right_tables" :key="item.id" :value="item.id">
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col>
                        <a-form-model-item label="右表模板" :labelCol="{span:2}" :wrapperCol="{span:16}" prop="right_tpl">
                            <a-textarea v-model="form.right_tpl" placeholder="用来显示的列，支持字段名+转换规则，
如：表列名有name、department，想要显示成「department」name前两个字符的拼接格式，
可以设置成 department::prepend:「|append:」+name::substr:0,2
" :auto-size="{ minRows: 5, maxRows: 8 }" />
                        </a-form-model-item>
                    </a-col>
                </a-row>

                <a-row>
                    <a-col :span="12">
                        <a-form-model-item label="多态前缀" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="name">
                            <a-input v-model="form.name" placeholder="请输入多态前缀">
                                <a-tooltip slot="suffix" title="在多态关系中，列名通常为 「前缀名」 + 「_type」， 「前缀名」 + 「_id」">
                                    <a-icon type="info-circle" />
                                </a-tooltip>
                            </a-input>
                        </a-form-model-item>
                    </a-col>
                    <a-col :span="12">
                        <a-form-model-item label="表名" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="table">
                            <a-input v-model="form.table" disabled/>
                        </a-form-model-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col :span="12">
                        <a-form-model-item label="Foreign Pivot Key" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="foreign_pivot_key">
                            <a-input v-model="form.foreign_pivot_key" />
                        </a-form-model-item>
                    </a-col>
                    <a-col :span="12">
                        <a-form-model-item label="Related Pivot Key" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="related_pivot_key">
                            <a-input v-model="form.related_pivot_key" />
                        </a-form-model-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col :span="12">
                        <a-form-model-item label="Parent Key" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="parent_key">
                            <a-input v-model="form.parent_key" placeholder="关联主表的主键名，如：id"/>
                        </a-form-model-item>
                    </a-col>
                    <a-col :span="12">
                        <a-form-model-item label="Related Key" :labelCol="{span:4}" :wrapperCol="{span:16}" prop="related_key">
                            <a-input v-model="form.related_key"  placeholder="关联副表的主键名，如：id"/>
                        </a-form-model-item>
                    </a-col>
                </a-row>

                <a-row>
                    <a-col>
                        <a-form-model-item label="描述" :labelCol="{span:2}" :wrapperCol="{span:16}" prop="description">
                            <a-textarea v-model="form.description" placeholder="这个数据映射是用来干啥的？什么都没写" :auto-size="{ minRows: 3, maxRows: 5 }" />
                        </a-form-model-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col :span="12">
                        <a-form-model-item label="状态" :labelCol="{span:4}" :wrapperCol="{span:16}">
                            <a-switch checked-children="启用" un-checked-children="禁用" :checked="form.status === 1" @change="onChangeStatus"/>
                        </a-form-model-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col>
                        <a-form-model-item :labelCol="{span:4}" :wrapper-col="{ span: 12, offset: 5 }">
                            <a-button type="primary" @click="handleSubmit">提交</a-button>
                        </a-form-model-item>
                    </a-col>
                </a-row>
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
                data_routes: [],
                left_tables: [],
                right_tables: [],
                relationships: [
                    {
                        value: 'morphToMany',
                        name: '多对多（多态）'
                    }
                ],
                form:{
                    data_route_id: '',
                    left: '',
                    left_table: '',
                    left_tpl: '',
                    right: '',
                    right_table: '',
                    right_tpl: '',
                    relationship: 'morphToMany',
                    name: 'left',
                    description: '',
                    table: 'core_data_mappings',
                    through: '',
                    first_key: '',
                    second_key: '',
                    foreign_key: '',
                    owner_key: '',
                    local_key: '',
                    second_local_key: '',
                    foreign_pivot_key: 'left_id',
                    related_pivot_key: 'right_id',
                    parent_key: '',
                    related_key: '',
                    relation: '',
                    status: 1
                },
                id:0,
                rules: {
                    data_route_id: [
                        {
                            required: true,
                            message: '请选择数据路径'
                        }
                    ],
                    left: [
                        {
                            required: true,
                            message: '请填写左表名称'
                        }
                    ],
                    left_table: [
                        {
                            required: true,
                            message: '请选择左表'
                        }
                    ],
                    left_tpl: [
                        {
                            required: true,
                            message: '请填写左表模板'
                        }
                    ],
                    right: [
                        {
                            required: true,
                            message: '请填写右表名称'
                        }
                    ],
                    right_table: [
                        {
                            required: true,
                            message: '请选择右表'
                        }
                    ],
                    right_tpl: [
                        {
                            required: true,
                            message: '请填写右表模板'
                        }
                    ],
                    name: [
                        {
                            required: true,
                            message: '请填写多态前缀'
                        }
                    ],
                    foreign_pivot_key: [
                        {
                            required: true,
                            message: '请填写Foreign Pivot Key'
                        }
                    ],
                    related_pivot_key: [
                        {
                            required: true,
                            message: '请填写Related Pivot Key'
                        }
                    ],
                    parent_key: [
                        {
                            required: true,
                            message: '请填写Parent Key'
                        }
                    ],
                    related_key: [
                        {
                            required: true,
                            message: '请填写Related Key'
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
                        let api = this.$apiHandle(this.$api.moduleCoreDataMaps,this.id);
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
            handleDataRouteSearch(value){
                this.getDataRouteSelector({keyword: value});
            },
            handleDataRouteChange(value){
                this.form.data_route_id = value;
            },

            handleLeftTableSearch(value){
                this.getLeftTableSelector({keyword: value});
            },
            handleLeftTableChange(value){
                this.form.left_table = value;
            },

            handleRightTableSearch(value){
                this.getRightTableSelector({keyword: value});
            },
            handleRightTableChange(value){
                this.form.right_table = value;
            },

            onChangeStatus(checked){
                this.form.status = checked ? 1 : 0;
            },
            get_form(){
                this.$get(this.$api.moduleCoreDataMaps+'/'+this.id).then(res=>{
                    this.form = res.data;
                });
            },
            getDataRouteSelector(params){
                this.$get(this.$api.moduleCoreDataRoutes, params).then(res=>{
                    if(res.data && res.data.data){
                        this.data_routes = res.data.data;
                    }
                });
            },
            getLeftTableSelector(params){
                this.$get(this.$api.moduleCoreDatabases, params).then(res=>{
                    if(res.data && res.data.data){
                        this.left_tables = res.data.data;
                    }
                });
            },
            getRightTableSelector(params){
                this.$get(this.$api.moduleCoreDatabases, params).then(res=>{
                    if(res.data && res.data.data){
                        this.right_tables = res.data.data;
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
                this.getDataRouteSelector();
                this.getLeftTableSelector();
                this.getRightTableSelector();
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
