<template>

    <div>
        <div class="page_title">{{ title }}</div>
        <div class="unline underm"></div>
        <div class="page_body">
            <a-spin v-show="loading"/>
            <a-row :gutter="8" v-show="!loading">
                <a-col :span="6">
                    <a-space direction="vertical">
                        <div>
                            <a-input-search
                                placeholder="搜索"
                                @search="onSearchLeft"
                            />
                        </div>
                        <div class="list_container">
                            <a-list
                                size="small"
                                class="list"
                                :loading="loading_left"
                                item-layout="horizontal"
                                :data-source="data_left">
                                <a-list-item slot="renderItem" slot-scope="item, index" @click="showAssignment(item.value)" :style="{color : (select_list_item === item.value ? '#c32617' : '')}">
                                    <a-list-item-meta>
                                        <span slot="title" :style="{color : (select_list_item === item.value ? '#c32617' : '')}">{{ item.title }}</span>
                                    </a-list-item-meta>
                                    <div>
                                        {{ item.rows }}
                                    </div>
                                </a-list-item>
                            </a-list>
                        </div>
                    </a-space>
                </a-col>
                <a-col :span="18">
                    <a-spin v-show="loading_right"/>
                    <a-transfer
                        :data-source="data_right_source"
                        show-search
                        showSelectAll
                        :list-style="{
                          width: '250px',
                          height: '300px',
                        }"
                        :operations="['to right', 'to left']"
                        :target-keys="data_right_target"
                        :render="item => `${item.title}-${item.description}`"
                        @change="handleChange">
                        <a-button
                            slot="footer"
                            slot-scope="props"
                            size="small"
                            style="float:right;margin: 5px"
                            @click="getDataRightSource">
                            reload
                        </a-button>
                        <span slot="notFoundContent">
                          没数据
                        </span>
                    </a-transfer>
                </a-col>
            </a-row>
        </div>
    </div>
</template>
<script>
    export default {
        name: "AAssignment",
        components: {},
        props: {
            api: {
                type: String,
                required: true
            },
            title: {
                type: String,
                default: ''
            },
            actions: {
                type: Object
            }
        },
        data() {
            return {
                search_left: '',
                loading: true,
                loading_left: true,
                loading_right: false,
                data_left: [],
                data_right_source: [],
                data_right_target: []
            };
        },
        computed: {},
        watch: {
            api(){
                this.onload();
            }
        },
        methods: {
            onload(){
                this.$get(this.api,{}).then(res=>{
                    this.loading = false;
                    this.loading_left = false;
                    this.data_left = res.data.data
                });
            },
            onSearchLeft(value){
                this.search_left = value;
            },
            showAssignment(value){
                this.loading_right = true;
            },
            handleChange(targetKeys, direction, moveKeys) {
                console.log(targetKeys, direction, moveKeys);

            },
            getDataRightSource(){

            }
        },
        created() {

        },
        mounted() {}
    };
</script>
<style lang="scss" scoped>
    .page_title {
        font-size: 14px;
        font-weight: bold;
    }
</style>
