<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-03-08
 * Time: 10:47
 */


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>添加版本内容</title>
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>

    <style>
        .el-input{
            width: 200px;
        }
    </style>
</head>
<body>
<div id="app">
    <el-card class="box-card">
        <el-form ref="form" :model="form" label-width="100px">
            <el-form-item label="版本号" >
                <el-input v-model="form.version"></el-input>
            </el-form-item>
        </el-form>

        <el-form v-for="(groups,key) in form.groups" :key="key" ref="form" :model="groups" label-width="100px"
                 style="border: 1px dashed #ccc;padding-top: 20px;width: 60%;position: relative;margin-top: 20px;">
            <i v-if="key == 0" class="el-icon-circle-plus-outline" style="font-size: 30px;position: absolute;right: 8%;top: 45%;cursor: pointer;" @click="addParams"></i>
            <i v-else class="el-icon-remove-outline" style="font-size: 30px;position: absolute;right: 8%;top: 45%;cursor: pointer;" @click="subParams(key)"></i>

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
                <el-input type="textarea" v-model="groups.content" rows="5"></el-input>
            </el-form-item>
            <el-form-item label="数据表" style="width: 82%" prop="sql">
                <el-input type="textarea" v-model="groups.sql" rows="5"></el-input>
            </el-form-item>
        </el-form>

        <el-form style="padding-top: 20px">
            <el-form-item>
                <el-button type="primary" @click="">提交</el-button>
                <el-button>取消</el-button>
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
                groups: [
                    {sys: '', model: '', func: '', content: '', sql: '', modelArr: [], funcArr: [],},
                ],
            },
            sysArr: [],
        },
        methods: {
            getModelMenu: function (key) {
                vm.form.groups[key].model = '';
                vm.form.groups[key].modelArr = [];
                vm.form.groups[key].func = '';
                vm.form.groups[key].funcArr = [];

                $.ajax({
                    url:"http://www.3kcenter_version.com/ajaxRoute.php",
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
                    url:"http://www.3kcenter_version.com/ajaxRoute.php",
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
                var o = {sys: '', model: '', func: '', content: '', sql: '', modelArr: [], funcArr: [],};
                vm.form.groups.push(o);
            },
            subParams: function (key) {
                vm.form.groups.splice(key, 1);
            },
        },

        created: function () {
            $.ajax({
                url:"http://www.3kcenter_version.com/ajaxRoute.php",
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
