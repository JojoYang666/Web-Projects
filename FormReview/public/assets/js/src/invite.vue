<template>
    <div id="app">
        <form id="form" @submit.prevent="submit" class="form-horizontal" role="form">
            <div class="form-group">
                <label class="col-sm-2 control-label">表单</label>
                <div class="col-sm-10">
                    <v-select label="name" v-model="form" :options="forms"></v-select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">权限</label>
                <div class="col-sm-10">
                    <v-select multiple v-model="authority" :options="authorities"></v-select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">审核阶段</label>
                <div class="col-sm-10">
                    <v-select multiple v-model="stage" :options="stages"></v-select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">筛选</label>
                <div class="col-sm-10">
                    <div class="bootstrap-switch-square">
                        <input type="checkbox" v-model="add_condition" id="condition"/>
                    </div>
                </div>
            </div>
            <template v-if="add_condition">
                <div class="form-group conditions" v-for="(condition,index) in conditions">
                    <label class="col-sm-2 control-label">条件{{index+1}}</label>
                    <div class="col-sm-4">
                        <v-select v-model="condition.field" :options="fields"></v-select>
                    </div>
                    <div class="col-sm-5">
                        <component :is="condition.input" :condition="condition"></component>
                    </div>
                    <div class="col-sm-1">
                        <span v-if="index>0" class="glyphicon glyphicon-minus" aria-hidden="true" @click="addCondition(-1,index)"></span>
                        <span class="glyphicon glyphicon-plus" aria-hidden="true" @click="addCondition(1,index)"></span>
                    </div>
                </div>
            </template>
            <div class="form-group">
                <label class="col-sm-2 control-label">用户</label>
                <div class="col-sm-10">
                    <v-select multiple
                              v-model="user"
                              :debounce="250"
                              :on-search="getUsers"
                              :options="users"
                              placeholder="搜索用户"
                    >
                    </v-select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-10">
                    <textarea v-model="remark" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">发送邀请</button>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
    import vSelect from "vue-select"
    import axios from 'axios'
    import tagsInput from 'vue-tagsinput'

    export default {
        components: {
            vSelect,
            tagsInput,
            'select-input': {
                components: {
                    vSelect
                },
                props: ['condition'],
                template: '<v-select multiple v-model="condition.value" :options="condition.field.values"></v-select>'
            },
            'text-input': {
                components: {
                    tagsInput
                },
                props: ['condition'],
                template: '<tags-input placeholder="Tab键确认关键词" @tags-change="handleChange" :tags="condition.value"></tags-input>',
                methods: {
                    handleChange: function handleChange(index, text) {
                        if (text) {
                            this.condition.value.splice(index, 0, text);
                        } else {
                            this.condition.value.splice(index, 1);
                        }
                    }
                }
            }
        },

        data() {
            return {
                forms: [],
                form: null,
                authorities: [],
                authority: [],
                stage: [],
                add_condition: false,
                conditions: [],
                users: [],
                user: [],
                remark: ''
            }
        },
        created: function () {
            this.init();
        },
        methods: {
            init: function () {
                axios.get('/web/admin/getOwnForms').then(response => {
                    let fid = getQueryString('formId');
                    fid = fid != null ? parseInt(fid) : null;
                    this.forms = response.data;
                    this.forms.forEach(form => {
                        if (fid != null && fid == form.id) {
                            this.form = form;
                        }
                        form.value = form.id;
                        form.label = form.name;
                    });
                }).catch(function (error) {
                    console.log(error);
                });
                axios.get('/web/admin/getAuthorities').then(response => {
                    let auths = response.data;
                    for (let auth in auths) {
                        let a = {};
                        a.value = auth;
                        a.label = auths[auth];
                        this.authorities.push(a);
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            },
            addCondition: function (way,index) {
                if(way>0) {
                    this.conditions.push({
                        field: null,
                        value: [],
                        input: 'text-input'
                    });
                }else {
                    this.conditions.splice(index,1)
                }
            },
            getUsers: function (search, loading) {
                loading(true);
                axios.get('/web/admin/getExceptUsers', {
                    params: {
                        q: search
                    }
                }).then(resp => {
                    this.users = [];
                    let users = resp.data;
                    users.forEach((user, i) => {
                        let a = {};
                        a.value = user.id;
                        a.label = user.name + '|' + user.phone + '|' + user.email;
                        this.users.push(a);
                    });
                    loading(false)
                })
            },
            submit: function () {
                let conditions = [];
                this.conditions.forEach(c => {
                    let v = [];
                    c.value.forEach(val => {
                        if (typeof val == 'object' && typeof val.value != 'undefined') {
                            v.push(val.value);
                        } else {
                            v.push(val)
                        }
                    });
                    conditions.push({
                        label: c.field.label,
                        key: c.field.name,
                        value: v.join(',')
                    });
                });
                let authorities = [];
                this.authority.forEach(a => {
                    authorities.push(a.value);
                });
                let stages = [];
                this.stage.forEach(s => {
                    stages.push(s.value);
                });
                let users = [];
                this.user.forEach(u => {
                    users.push(u.value);
                });
                axios.post('/web/admin', {
                    tableId: this.form.id,
                    add_condition: this.add_condition,
                    conditions: conditions,
                    authorities: authorities,
                    stages: stages,
                    users: users,
                    remark: this.remark
                }).then(function (response) {
                    if (response.status == 200) {
                        toastr.success('成功发送邀请');
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        computed: {
            stages: function () {
                let s = [];
                if (this.form) {
                    let reviewTimes = this.form.reviewTimes;
                    for (let i = 0; i < reviewTimes; i++) {
                        s.push({
                            value: i,
                            label: reviewTime2zh(i)
                        });
                    }
                }
                return s;
            },
            fields: function () {
                if (this.form) {
                    return JSON.parse(this.form.fields);
                }
                return [];
            }
        },
        watch: {
            add_condition: function (val) {
                if (val) {
                    this.conditions = [{
                        field: null,
                        value: [],
                        input: 'text-input'
                    }];
                }
            },
            conditions: {
                handler: function (val) {
                    val.forEach((item, i) => {
                        if (typeof item != 'undefined' && item.field != null && ['checkbox-group', 'radio-group', 'select'].indexOf(item.field.type) != -1) {
                            item.input = 'select-input';
                        } else {
                            item.input = 'text-input';
                        }
                    });
                },
                deep: true
            }
        }
    };
</script>