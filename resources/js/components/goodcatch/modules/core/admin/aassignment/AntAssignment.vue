<template>

    <div>
        <div class="page_title">{{ title }}</div>
        <div class="unline underm"></div>
        <div class="page_body">
            <a-spin v-show="loading"/>
            <a-row :gutter="8" v-show="!loading" :style="{height: (height + 'px')}">
                <a-col :span="7">
                    <a-space direction="vertical">
                        <div>
                            <a-input-search
                                placeholder="搜索"
                                @search="onSearchLeft"
                            />
                        </div>
                        <div class="list_container" :style="{height: (height - 40) + 'px'}" :class="{'inactive' : loading_right}">
                            <a-list
                                size="small"
                                class="list"
                                :loading="loading_left"
                                item-layout="horizontal"
                                :data-source="data_left">
                                <a-list-item slot="renderItem" slot-scope="item, index" @click="showAssignment(item.value)" class="list-item" :class="{'active' : (select_list_item === item.value), 'inactive' : loading_right}">
                                    <a-list-item-meta>
                                        <span slot="title">{{ item.title }}</span>
                                    </a-list-item-meta>
                                    <a-icon type="check" class="list-item-icon" v-show="select_list_item === item.value"/>
                                </a-list-item>
                            </a-list>
                        </div>
                    </a-space>
                </a-col>
                <a-col :span="1"></a-col>
                <a-col :span="16">
                    <a-spin v-show="loading_right"/>
                    <a-transfer
                        :titles="['未分配', '已分配']"
                        :operations="['分配', '取消']"
                        show-search
                        showSelectAll
                        :list-style="{
                            width: width,
                            height: height + 'px',

                        }"
                        :data-source="data_right_source"
                        :target-keys="data_right_target"
                        :selected-keys="data_right_selected"
                        :render="item => item.title"
                        @change="handleChange"
                        @selectChange="handleSelectChange">
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
            },
            parameterReplacement: {
                type: String,
                default: ''
            },
            leftParameter: {
                type: String,
                default: 'id'
            },
            rightParameter: {
                type: String,
                default: 'id'
            },
            width: {
                type: String,
                default: '40%'
            },
            height: {
                type: Number,
                default: 420
            }
        },
        data() {
            return {
                select_list_item: undefined,
                search_left: '',
                loading: true,
                loading_left: true,
                loading_right: false,
                data_left: [],
                data_right_source: [],
                data_right_target: [],
                data_right_selected: []
            };
        },
        computed: {
            _api(){
                return this.api;
            }
        },
        watch: {
            _api(val){
                this.onload();
            }
        },
        methods: {
            onload(){
                if(!this.isEmpty(this.api)){
                    this.$get(this.api,{}).then(res=>{
                        this.loading = false;
                        this.loading_left = false;
                        this.data_left = res.data.data
                    });
                }
                this.loading = true;
                this.loading_left = true;
            },
            onSearchLeft(value){
                this.search_left = value;
            },
            showAssignment(value){
                if(this.select_list_item !== value){
                    this.select_list_item = value;
                    if(this.isEmpty(this.actions.source)){
                        return this.$message.error('API source 未定义');
                    } else {
                        this.loading_right = true;
                        this.data_right_source = [];
                        this.data_right_target = [];
                        this.$get(this.isEmpty(this.parameterReplacement)
                            ? (this.actions.source + '/' + value)
                            : this.actions.source.replace(this.parameterReplacement, value)).then(source=>{
                            this.data_right_source = source.data;
                            if(this.isEmpty(this.actions.target)){
                                return this.$message.error('API target 未定义');
                            } else {
                                this.$get(this.isEmpty(this.parameterReplacement)
                                    ? (this.actions.target + '/' + value)
                                    : this.actions.target.replace(this.parameterReplacement, value)).then(target=>{
                                    this.loading_right = false;
                                    this.data_right_target = target.data;
                                });
                            }
                        });
                    }
                }
            },
            handleChange(targetKeys, direction, moveKeys) {

                if(!this.isEmpty(this.actions [direction])){
                    let url = this.isEmpty(this.parameterReplacement)
                        ? (this.actions [direction] + '/' + this.select_list_item)
                        : this.actions [direction].replace(this.parameterReplacement, this.select_list_item);
                    const params = {};
                    const data_right_target_cp = this.data_right_target.concat([]);
                    params [this[direction + 'Parameter']] = moveKeys;
                    switch(direction){
                        case 'right':
                            this.data_right_target = data_right_target_cp.concat(moveKeys);
                            this.$post(url, params).then(res=> {
                                if (res.code == 200 || res.code == 201) {
                                    this.data_right_selected = moveKeys;
                                    this.$message.success(res.msg);
                                } else {
                                    this.data_right_target = data_right_target_cp;
                                    return this.$message.error(res.msg);
                                }
                            }).catch(()=>{
                                this.data_right_target = data_right_target_cp;
                            });
                            break;
                        case 'left':
                            const targets = moveKeys.reduce((target_idx, item) => {
                                target_idx ['_' + item] = true;
                                return target_idx;
                            }, {});
                            this.data_right_target = data_right_target_cp.reduce((target_idx, item)=>{
                                if(!targets ['_' + item])
                                {
                                    target_idx.push(item);
                                }
                                return target_idx;
                            }, []);
                            this.$delete(url, params).then(res=> {
                                if (res.code == 200 || res.code == 204) {
                                    this.data_right_selected = moveKeys;
                                    this.$message.success(res.msg);
                                } else {
                                    this.data_right_target = data_right_target_cp;
                                    return this.$message.error(res.msg);
                                }
                            }).catch(()=>{
                                this.data_right_target = data_right_target_cp;
                            });
                            break;
                    }
                }
            },
            handleSelectChange(sourceSelectedKeys, targetSelectedKeys){
                this.data_right_selected = [...sourceSelectedKeys, ...targetSelectedKeys];
            },
            isEmpty(value){
                return typeof value === 'undefined'
                    || null === value
                    || '' === value
                    || value.length === 0
            }
        },
        created() {},
        mounted() {}
    };
</script>
<style lang="scss" scoped>
    .page_title {
        font-size: 14px;
        font-weight: bold;
    }

    .active {

    }

    .ant-list-item-meta-title.inactive {
        color: #9c839f;
    }

    .list_container{
        border: 1px solid #e8e8e8;
        border-radius: 4px;
        overflow: auto;
        padding: 0 5px 60px 5px;
    }

    .list_container.inactive {
        background-color: #f9f9f9;
    }

    .list-item {
        padding: 5px 7px;
        margin-bottom: 0px;

        .list-item-icon {
            color: #d9ecff;
            font-size: 20px;
        }
    }

    .list-item.active {
        background-color: #547dc0;
        border-radius: 5px;
        box-shadow: 0 2px 12px 0 #cfcfcf;
        .ant-list-item-meta-title {
            color: #d9ecff!important;
        }
    }

    .list-item.inactive {
        .ant-list-item-meta-title {
            color: #98939f;
        }
    }

</style>
