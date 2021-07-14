<template>
    <div>
        <div class="admin_table_page_title">
            <a-button @click="$router.back()" class="float_right" icon="arrow-left">返回</a-button>
            数据源编辑
        </div>
        <div class="unline underm"></div>
        <div class="admin_form">
            <a-form-model :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">

                <a-form-model-item label="代码">
                    <a-input v-model="info.code"></a-input>
                </a-form-model-item>
                <a-form-model-item label="名称">
                    <a-input v-model="info.name"></a-input>
                </a-form-model-item>
                <a-form-model-item label="描述">
                    <a-textarea v-model="info.description" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="必填项">
                    <a-textarea v-model="info.requires" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="选填项">
                    <a-textarea v-model="info.options" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="排序">
                    <a-input-number v-model="info.order" :min="0" @change="onChangeOrder" />
                </a-form-model-item>
                <a-form-model-item label="状态">
                    <a-switch checked-children="启用" un-checked-children="禁用" :checked="info.status === 1" @change="onChangeStatus"/>
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
          cascader_area: [],
          info:{
              code: '',
              name: '',
              description: '',
              requires: '',
              options: '',
              order: 1,
              status: 1
          },
          id:0,
      };
    },
    watch: {},
    computed: {},
    methods: {
        handleSubmit(){

            // 验证代码处
            if(this.$isEmpty(this.info.code)){
                return this.$message.error('代码不能为空');
            }
            if(this.$isEmpty(this.info.code)){
                return this.$message.error('代码不能为空');
            }

            if(this.$isEmpty(this.info.name)){
                return this.$message.error('名称不能为空');
            }

            let api = this.$apiHandle(this.$api.moduleCoreDatasources,this.id);
            if(api.status){
                this.$put(api.url,this.info).then(res=>{
                    if(res.code == 200){
                        this.$message.success(res.msg)
                        return this.$router.back();
                    }else{
                        return this.$message.error(res.msg)
                    }
                })
            }else{
                this.$post(api.url,this.info).then(res=>{
                    if(res.code == 200 || res.code == 201){
                        this.$message.success(res.msg)
                        return this.$router.back();
                    }else{
                        return this.$message.error(res.msg)
                    }
                })
            }


        },
        onChangeOrder(value){
            this.info.order = value;
        },
        onChangeStatus(checked){
            this.info.status = checked ? 1 : 0;
        },
        get_info(){
            this.$get(this.$api.moduleCoreDatasources+'/'+this.id).then(res=>{
                this.info = res.data;
            })
        },

        // 获取列表
        onload(){
            // 判断你是否是编辑
            if(!this.$isEmpty(this.$route.params.id)){
                this.id = this.$route.params.id;
                this.get_info();
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

</style>
