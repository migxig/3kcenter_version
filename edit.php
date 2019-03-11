<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>版本内容_编辑</title>
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
            <el-breadcrumb-item>编辑</el-breadcrumb-item>
        </el-breadcrumb>
    </div>

    <el-card class="box-card">
        <el-form ref="refName" :model="form" :rules="rules" label-width="100px">
            <el-form-item label="版本号" style="display: inline-block" prop="version">
                <el-input v-model="form.version" disabled></el-input>
            </el-form-item>
            <el-form-item label="用户名" style="display: inline-block" prop="user">
                <el-input v-model="form.user" disabled></el-input>
            </el-form-item>
            <el-form-item label="系统" style="display: inline-block" prop="sys">
                <el-input v-model="form.sys" disabled></el-input>
            </el-form-item>
            <el-form-item label="模块" style="display: inline-block" prop="model">
                <el-input v-model="form.model" disabled></el-input>
            </el-form-item>
            <el-form-item label="功能" style="display: inline-block" prop="func">
                <el-input v-model="form.func" disabled></el-input>
            </el-form-item>
            <el-form-item label="更新内容" style="width: 81.3%" prop="content">
                <el-input type="textarea" v-model="form.content" rows="4" placeholder="请输入简概描述"></el-input>
            </el-form-item>
            <el-form-item label="数据表" style="width: 81.3%" prop="sql">
                <el-input type="textarea" v-model="form.sql" rows="3" placeholder="若无变动请留空"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="warning" @click="submit">提交</el-button>
                <el-button type="info" @click="returnBack">返回</el-button>
            </el-form-item>
        </el-form>
    </el-card>
</div>
<script>
    var vm = new Vue({
        el: '#app',
        data: {
            form: {
                id: '',
                version: '',
                user:'',
                sys: '',
                model: '',
                func: '',
                content: '',
                sql: '',
            },

            rules:{
                version: [
                    {required: true, message: '版本号不能为空'},
                ],
                user: [
                    {required: true, message: '用户名不能为空'},
                ],
            },
        },
        methods: {
            returnBack: function() {
                location.href = 'index.php';
            },
            submit: function () {
                vm.$refs['refName'].validate(function (valid) {
                    if (valid) {
                        $.ajax({
                            type: 'POST',
                            url:"route.php",
                            data:{
                                ct: 'VersionLogic',
                                ac: 'editVersion',
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
        },

        created: function () {
            //获取链接参数
            var url = document.location.toString();
            var arrUrl = url.split("?");
            var idArr = arrUrl[1].split("=");
            var id = idArr[1];

            $.ajax({
                type: 'POST',
                url:"route.php",
                data:{
                    ct: 'VersionLogic',
                    ac: 'getRowById',
                    params: {id: id},
                },
                success: function(result){
                    vm.form = JSON.parse(result);
                }
            });
        },
    });
</script>
</body>
</html>
