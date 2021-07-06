<template>

    <a-assignment :title="info.title" :api="info.api" :actions="actions">

    </a-assignment>
</template>

<script>

    import AAssignment from "@/components/goodcatch/modules/core/admin/aassignment";

    export default {
        components: { AAssignment },
        props: {},
        data() {
            return {
                id: 0,
                info: {}
            };
        },
        watch: {},
        computed: {

        },
        methods: {

            get_form(){
                this.$get(this.$api.moduleCoreDataMaps+'/'+this.id + '/assignment').then(res=>{
                    this.info = res.data;
                });
            },

            // 获取列表
            onload(){
                // 判断你是否是编辑
                if(!this.$isEmpty(this.$route.params.id)){
                    this.id = this.$route.params.id;
                    this.get_form();
                }
            }
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
