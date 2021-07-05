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
                    <a-input v-model="form.name" placeholder="请输入名称" />
                </a-form-model-item>
                <a-form-model-item label="描述" prop="description">
                    <a-textarea v-model="form.description" placeholder="请输入描述信息。" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="排序" prop="order">
                    <a-input-number v-model="form.order" :min="0" @change="onChangeOrder" />
                </a-form-model-item>
                <a-form-model-item label="状态">
                    <a-switch checked-children="启用" un-checked-children="禁用" default-checked @change="onChangeStatus"/>
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
                form:{
                    name: '',
                    description: '',
                    order: 1,
                    status: 1
                },
                id:0,
                rules: {
                    name: [
                        {
                            required: true,
                            message: '请填写名称'
                        }
                    ]
                },
            };
        },
        watch: {},
        computed: {

        },
        methods: {
            handleSubmit(test){

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

            onChangeOrder(value){
                this.form.order = value;
            },
            onChangeStatus(checked){
                this.form.status = checked ? 1 : 0;
            },
            get_form(){
                this.$get(this.$api.moduleCoreDataRoutes+'/'+this.id).then(res=>{
                    this.form = res.data;
                });
            },
            // 获取列表
            onload(){
                // 判断你是否是编辑
                if(!this.$isEmpty(this.$route.params.id)){
                    this.id = this.$route.params.id;
                    this.get_form();
                }
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
