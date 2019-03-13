<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>版本内容_列表</title>
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <style>
        .box{
            padding: 10px 0 10px 10px;
        }
        .sys{
            margin: 0;
            margin-top: 16px;
        }
        .func{
            margin: 3px 0 3px 0;
        }
        .el-dialog--center .el-dialog__body{
            padding-top: 0;
        }
        .title{
            color: #E6A23C;
        }
        .el-table th{
            background-color: #f5f7fa;
        }
        .link{
            color: #66b1ff;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div id="app">
    <div class="box">
        <el-breadcrumb separator-class="el-icon-arrow-right">
            <el-breadcrumb-item :to="{ path: '/' }">列表页</el-breadcrumb-item>
        </el-breadcrumb>
    </div>

    <el-card class="box-card" style="margin-bottom: 20px">
        <el-form ref="refName" :model="form" label-width="60px">
            <el-form-item label="版本号" style="display: inline-block" prop="version">
                <el-input v-model="form.version"></el-input>
            </el-form-item>
            <el-form-item label="用户名" style="display: inline-block" prop="user">
                <el-input v-model="form.user"></el-input>
            </el-form-item>
            <el-form-item style="display: inline-block" label-width="10px">
                <el-button type="warning" @click="loadData">搜索</el-button>
                <el-button type="warning" @click="induce">归纳</el-button>
                <el-button type="primary" @click="handelAdd">新增</el-button>
            </el-form-item>
        </el-form>
    </el-card>

    <el-card class="box-card">
        <el-table :data="tableData" border>
            <el-table-column prop="id" label="序号" width="70px"></el-table-column>
            <el-table-column prop="version" label="版本号" width="70px"></el-table-column>
            <el-table-column prop="sys" label="系统" width="150px"></el-table-column>
            <el-table-column prop="model" label="模块" width="150px"></el-table-column>
            <el-table-column prop="func" label="功能" width="150px"></el-table-column>
            <el-table-column prop="content" label="更新内容"></el-table-column>
            <el-table-column prop="sql" label="数据表"></el-table-column>
            <el-table-column prop="remark" label="备注"></el-table-column>
            <el-table-column prop="user" label="用户" width="90px"></el-table-column>
            <el-table-column prop="time" label="添加时间" width="200px"></el-table-column>
            <el-table-column prop="" label="操作" width="100px">
                <template slot-scope="scope">
                    <el-button type="text" @click="handelEdit(scope.row.id)">编辑</el-button>
                    <el-button type="text" @click="handelDel(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <el-dialog
                :title="'版本号：'+form.version"
                :visible.sync="centerDialogVisible"
                width="50%"
                center>
            <h3 class="title">III 功能变更：</h3>
            <div class="data" v-for="(item,index) in induceData">
                <h3 class="sys" v-text="'#'+index"></h3>
                <div class="func" v-for="(sItem,sIndex) in item" v-html="(sIndex+1)+'、'+sItem"></div>
            </div>
            <h3 class="title" style="margin-top: 60px">III 数据表变更：</h3>
            <div v-for="(item,index) in sqlData" v-html="(index+1)+'、'+item"></div>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="centerDialogVisible = false">关闭</el-button>
            </span>
        </el-dialog>
    </el-card>
</div>
<script>
    var vm = new Vue({
        el: '#app',
        data: {
            form: {
                version: '',
                user:'',
            },
            tableData: [],
            induceData: [],
            sqlData: [],
            centerDialogVisible: false,
        },
        methods: {
            induce: function() {
                vm.induceData = [];
                vm.sqlData = [];

                $.ajax({
                    type: 'POST',
                    url:"route.php",
                    data:{
                        ct: 'VersionLogic',
                        ac: 'induceVersion',
                        params: vm.form,
                    },
                    success: function(result){
                        info = JSON.parse(result);
                        if (info.code === 0) {
                            vm.centerDialogVisible = true;
                            vm.induceData = info.data;
                            vm.sqlData = info.sql;
                        } else {
                            vm.$message({
                                message: info.msg,
                                type: 'warning',
                            });
                        }
                    }
                });
            },
            handelAdd: function() {
                location.href = 'add.php';
            },
            handelEdit: function(id) {
                location.href = 'edit.php?id='+id;
            },
            handelDel: function(id) {
                console.log(id)

                vm.$confirm('确认删除吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    $.ajax({
                        type: 'POST',
                        url:"route.php",
                        data:{
                            ct: 'VersionLogic',
                            ac: 'delete',
                            params: {id: id},
                        },
                        success: function(result){
                            info = JSON.parse(result);
                            if(info.code === 0) {
                                vm.$message({
                                    message: info.msg,
                                    type: 'success',
                                });
                            } else {
                                vm.$message({
                                    message: info.msg,
                                    type: 'warning',
                                });
                            }

                            vm.loadData();
                        }
                    });
                });
            },
            loadData: function () {
                $.ajax({
                    type: 'POST',
                    url:"route.php",
                    data:{
                        ct: 'VersionLogic',
                        ac: 'listVersion',
                        params: this.form,
                    },
                    success: function(result){
                        vm.tableData = JSON.parse(result);
                    }
                });
            },
        },

        created: function () {
            this.loadData();
        },
    });
</script>
</body>
</html>
