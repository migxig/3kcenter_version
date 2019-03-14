<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>版本内容_新增</title>
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <style>
        .el-input{
            width: 200px;
        }
        .box{
            padding: 10px 0 10px 10px;
        }
    </style>
</head>
<body>
<div id="app">
    <div class="box">
        <el-breadcrumb separator-class="el-icon-arrow-right">
            <el-breadcrumb-item>列表页</el-breadcrumb-item>
            <el-breadcrumb-item>新增</el-breadcrumb-item>
        </el-breadcrumb>
    </div>

    <el-card class="box-card" style="padding-bottom: 20px">
        <el-form ref="refName" :model="form" :rules="rules" label-width="70px" style="position: fixed; top: 15%;left: 5%">
            <el-form-item label="版本号" prop="version">
                <el-input v-model="form.version"></el-input>
            </el-form-item>
            <el-form-item label="用户名" prop="user">
                <el-input v-model="form.user"></el-input>
            </el-form-item>

            <el-form-item label-width="10px">
                <el-button type="warning" @click="submit">提交</el-button>
                <el-button type="info" @click="returnBack">返回</el-button>
            </el-form-item>
        </el-form>

        <el-form v-for="(groups,key) in form.groups" :rules="rulesGroup" :key="key" ref="refGroup" :model="groups" label-width="100px"
                 style="border: 1px dashed #ccc;padding: 20px 0 20px 0;width: 60%;position: relative;margin-top: 20px;float: right;margin: right: 10%">
            <i v-if="key == 0" class="el-icon-circle-plus-outline" style="font-size: 20px;position: absolute;right: 8%;top: 45%;cursor: pointer;" @click="addParams"></i>
            <i v-else class="el-icon-remove-outline" style="font-size: 20px;position: absolute;right: 8%;top: 45%;cursor: pointer;" @click="subParams(key)"></i>

            <el-form-item label="系统" style="display: inline-block" prop="sys">
                <el-select v-model="groups.sys" filterable clearable placeholder="请选择系统" @change="getModelMenu(key)">
                    <el-option v-for="item in sysArr" :label="item.name" :value="item.id" :key="item.id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="模块" style="display: inline-block" prop="model">
                <el-select v-model="groups.model" filterable clearable placeholder="请选择模块" @change="getFuncMenu(key)">
                    <el-option v-for="item in groups.modelArr" :label="item.name" :value="item.id" :key="item.id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="功能" style="display: inline-block" prop="func">
                <el-select v-model="groups.func" filterable clearable placeholder="请选择功能">
                    <el-option v-for="item in groups.funcArr" :label="item.name" :value="item.id" :key="item.id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="更新内容" style="width: 82%" prop="content">
                <el-input type="textarea" v-model="groups.content" rows="3" placeholder="例子：新增和编辑页面新加'游戏马甲名'字段并更改唯一性；搜索条件和列表展示新增游戏马甲名"></el-input>
            </el-form-item>
            <el-form-item label="数据表" style="width: 82%" prop="sql">
                <el-input type="textarea" v-model="groups.sql" rows="3" placeholder="例子1：在`admin_center`新加`version`，`user`表； 例子2：在`admin_center`.`version`表新加`remark`字段"></el-input>
            </el-form-item>
            <el-form-item label="备注" style="width: 82%" prop="remark">
                <el-input type="textarea" v-model="groups.remark" rows="3" placeholder=""></el-input>
            </el-form-item>
        </el-form>
    </el-card>
</div>
<script>
    var vm = new Vue({
        el: '#app',
        data: {
            form: {
                version: '',
                user:'',
                groups: [
                    {sys: '', model: '', func: '', content: '', sql: '', remark: '', modelArr: [], funcArr: [],},
                ],
            },
            sysArr: [],

            rules:{
                version: [
                    {required: true, message: '版本号不能为空'},
                ],
                user: [
                    {required: true, message: '用户名不能为空'},
                ],
            },

            rulesGroup:{
                sys: [
                    {required: true, message: '系统不能为空'},
                ],
                model: [
                    {required: true, message: '模块不能为空'},
                ],
                func: [
                    {required: true, message: '功能不能为空'},
                ],
                content: [
                    {required: true, message: '更新内容不能为空'},
                ],
            },
        },
        methods: {
            returnBack: function() {
                location.href = 'index.php';
            },
            submit: function () {
                vm.$refs['refName'].validate(function (valid) {
                    pass = true;
                    var refGroup = vm.$refs['refGroup'];
                    for (var i=0;i<refGroup.length;i++) {
                        vm.$refs['refGroup'][i].validate(function (valid2) {
                            if(!valid2) {
                                pass = false;
                            }
                        })
                    }

                    if (valid && pass) {
                        $.ajax({
                            type: 'POST',
                            url:"route.php",
                            data:{
                                ct: 'VersionLogic',
                                ac: 'addVersion',
                                params: vm.form,
                            },
                            success: function(result){
                                data = JSON.parse(result);
                                if(data.code === 0) {
                                    vm.$message({
                                        message: data.msg,
                                        type: 'success',
                                    });
                                    setTimeout(function () {
                                        location.href = 'index.php';
                                    }, 1000);
                                } else {
                                    vm.$message({
                                        message: data.msg,
                                        type: 'warning',
                                    });
                                }
                            }
                        });
                    }
                })
            },

            getModelMenu: function (key) {
                vm.form.groups[key].model = '';
                vm.form.groups[key].modelArr = [];
                vm.form.groups[key].func = '';
                vm.form.groups[key].funcArr = [];

                $.ajax({
                    type: 'POST',
                    url:"route.php",
                    data:{
                        ct: 'Menu',
                        ac: 'getModelMenu',
                        params: {sys: vm.form.groups[key].sys},
                    },
                    success: function(result){
                        vm.form.groups[key].modelArr = JSON.parse(result);
                    }
                });
            },
            getFuncMenu: function (key) {
                vm.form.groups[key].func = '';
                vm.form.groups[key].funcArr = [];

                $.ajax({
                    type: 'POST',
                    url:"route.php",
                    data:{
                        ct: 'Menu',
                        ac: 'getFuncMenu',
                        params: {sys: vm.form.groups[key].sys, model: vm.form.groups[key].model},
                    },
                    success: function(result){
                        vm.form.groups[key].funcArr = JSON.parse(result);
                    }
                });
            },
            addParams: function () {
                if (vm.form.groups.length >= 10) {
                    vm.$message({
                        message: '分组最多10个',
                        type: 'warning',
                    });

                    return false;
                }

                var o = {sys: '', model: '', func: '', content: '', sql: '', remark: '', modelArr: [], funcArr: [],};
                vm.form.groups.push(o);
            },
            subParams: function (key) {
                vm.form.groups.splice(key, 1);
            },
        },

        created: function () {
            $.ajax({
                type: 'POST',
                url:"route.php",
                data:{
                    ct: 'Menu',
                    ac: 'getSysMenu',
                    params: {},
                },
                success: function(result){
                    vm.sysArr = JSON.parse(result);
                }
            });
        },
    });
</script>
</body>
</html>
