/* *
    后面的function是把post过去的数据从json格式编程表单数据
    Note List Controller
*/
var myNote = angular.module('myNote', [], function($httpProvider) {
    // Use x-www-form-urlencoded Content-Type
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    /**
     * The workhorse; converts an object to x-www-form-urlencoded serialization.
     * @param {Object} obj
     * @return {String}
     */
    var param = function(obj) {
        var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

        for(name in obj) {
            value = obj[name];

            if(value instanceof Array) {
                for(i=0; i<value.length; ++i) {
                    subValue = value[i];
                    fullSubName = name + '[' + i + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            }
            else if(value instanceof Object) {
                for(subName in value) {
                    subValue = value[subName];
                    fullSubName = name + '[' + subName + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            }
            else if(value !== undefined && value !== null)
                query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
        }

        return query.length ? query.substr(0, query.length - 1) : query;
    };

    // Override $http service's default transformRequest
    $httpProvider.defaults.transformRequest = [function(data) {
        return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
    }];
});


//主控制器
myNote.controller('mainController',['$rootScope','$scope','$location','$modal','$cookieStore','$http', function ($rootScope,$scope,$location,$modal,$cookieStore,$http) {
    //退出
    $scope.logout = function (){
        $http.get('/logout?auth='+$rootScope.ticket).then(function (result) {
            var data = result.data;
            if(data.code==50001){
                layer.msg(data.info);
                $location.path("/login");
            } else {
                layer.msg(data.info);
            }
        });
    };

    $scope.openModal = function(size){  //打开模态
        var modalInstance = $modal.open({
            templateUrl : 'myModelContent.html',  //指向上面创建的视图
            controller : 'addCateController',// 初始化模态范围
            size : size, //大小配置
        });
    };

}]);

myNote.controller('categoryController',['$scope','$location','$cookieStore','localStorageService','Data', function ($scope,$location,$cookieStore,localStorageService,Data) {

    $scope.category = {};
    $scope.getCategory = function () {
        //如果存在本地LocalStorage缓存则读缓存
        var category = localStorageService.get("category");
        if(category!=undefined){
            $scope.category = category;
            return false;
        }
        //当没有缓存则从服务器请求数据
        $scope.cateUrl = 'attribute/list?';
        Data.get($scope.cateUrl).then(function (result) {
            if (result.code==50001) {
                localStorageService.set("category",result.cate);
                $scope.category = result.cate;
            } else {
                console.log('(┬＿┬)额，服务器老兄好像没有给我返回类别的数据.我正准备打他~');
            }
        });

    };
    $scope.getCategory();

    $scope.deleteCate = function (category) {
        $scope.deleteUrl = 'attribute/handle?category='+category;
        layer.confirm('你确定要这个类别吗？（注意：如果类别下有笔记，则无法删除。）', {
            btn: ['确定','取消'], //按钮
            shade: false //不显示遮罩
        }, function(index){
            Data.delete($scope.deleteUrl).then(function (result) {
                if(result.code=50001){
                    layer.msg('类别删除成功', {icon: 1});
                    $location.path('/category/inbox');
                } else {
                    layer.msg('类别删除失败，请检查这个类别下是否还有笔记', {icon: 2});
                }
            });
        }, function(index){
            layer.close(index);
        });
    };

}]);

//新增类别
myNote.controller('addCateController',['$scope','$location','$modalInstance','$cookieStore','Data', function ($scope,$location,$modalInstance,$cookieStore,Data) {

    $scope.ticket = $cookieStore.get('persm_ticket');

    $scope.cancel = function(){
        $modalInstance.dismiss('cancel'); // 退出
    };

    $scope.formData = {
        'name' : 'cate',
        'auth' : $scope.ticket
    };
    $scope.processForm = function () {
        $scope.cateUrl = 'attribute/handle?';
        Data.post($scope.cateUrl,$.param($scope.formData)).then(function (result) {
            if (result.code==50001) {
                layer.msg('类别创建成功', {icon: 1});
                $scope.cancel();
            }
        });
    };

}]);

//登陆控制器
myNote.controller('loginController', ['$rootScope','$scope','$cookieStore','$location', function ($rootScope,$scope,$cookieStore,$location) {
    //检查登陆
    $scope.ticket = $cookieStore.get('persm_ticket');
    if ($scope.ticket!=undefined) {
        $location.path('/index');
    }
}]);

//首页统计控制器
myNote.controller('countController', ['$scope','Data', function ($scope,Data) {
    //Get Count Info
    Data.get('notes/count?').then(function (result) {
        $scope.data = result;
    });
    Data.get('notes/weather?').then(function (result) {
        $scope.weather = result;
    });
}]);


/* 获取笔记数据的控制器 */
myNote.controller('noteController', ['$scope', '$stateParams', 'Data', function ($scope, $stateParams, Data) {
    $scope.params = $stateParams.cat;

    $scope.header = $scope.params+" List";

    $scope.pagingconf = {
        currentPage : 1,
        pageSize : 30,
        total : 200 ,
        dots : '...',
        adjacent : 2,
        ulClass : 'pagination',
        activeClass : 'active',
        disabledClass : 'disabled',
        hideIfEmpty : true,
        scrollTop : true,
        showPrevNext : true
    };
    //Get First Page
    $scope.url = 'notes/list?prams='+$scope.params+'&p=1&row='+$scope.pagingconf.pageSize;
    Data.get($scope.url).then(function (result) {
        $scope.data = result.note;
        $scope.pagingconf.total = result.total;
    });
    /*
    * console.log({text, page, pageSize, total});
    * Object {text: "Paging Clicked", page: 2, pageSize: 50, total: 200}
    *
    * */
    $scope.DoCtrlPagingAct = function(page, pageSize, total){
        //console.log(page, pageSize, total);
        var page_current = page?page:1;
        var page_row = pageSize?pageSize:30;
        $scope.url = 'notes/list?prams='+$scope.params+'&p='+page_current+'&row='+page_row;
        Data.get($scope.url).then(function (result) {
            $scope.data = result.note;
            $scope.pagingconf.total = result.total;
        });
    };

    $scope.formData = {};
    $scope.getSearch = function (keyword) {
        $scope.pagingconf.total = 0;
        $scope.SearchUrl = 'notes/search/'+$scope.formData.keyword + '?';
        Data.get($scope.SearchUrl).then(function (result) {
            $scope.data = result.note;
        });
    }
}]);

/* 笔记内容控制器 */
myNote.controller('notePostController',['$scope','$location','$sce','$compile','$stateParams','Data', function ($scope,$location,$sce,$compile,$stateParams,Data) {

    $scope.noteId = $stateParams.noteId;
    $scope.noteUrl = 'notes/read/'+$scope.noteId+'?';
    Data.get($scope.noteUrl).then(function (result) {
        $scope.note = result;
        //拆分tags
        var tags = result.tags;
        if(tags!=null){
            $scope.tags = tags.split(',');
        }
        // Loads and runs script
        //var el = $compile($.parseHTML(result.content, document, true))($scope);
        var el = $compile($.parseHTML(result.content, document, true))($scope);
        //console.log(el);
        angular.element(document.querySelector('#notecontent')).append(el);
    });

    //打印
    $scope.printDiv = function(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var popupWin = window.open('', '_blank', 'width=400,height=300');
        popupWin.document.open()
        popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="style.css" /></head><body onload="window.print()">' + printContents + '</html>');
        popupWin.document.close();
    };

    //删除
    $scope.deleteNote = function () {
        $scope.noteDeleteUrl = 'notes/handle?';
        layer.confirm('你确定要删除这篇笔记吗？', {
            btn: ['确定','取消'], //按钮
            shade: false //不显示遮罩
        }, function(index){
            Data.delete($scope.noteDeleteUrl).then(function (result) {
                if(result.code=50001){
                    layer.msg('笔记删除成功', {icon: 1});
                    $location.path('/category/inbox');
                }
            });
        }, function(index){
            layer.close(index);
        });
    };

}]);

/* Note Post Controller */
myNote.controller('addNoteController', ['$scope','$stateParams','$cookieStore','$location','Data',function ($scope,$stateParams,$cookieStore,$location,Data) {

    var ticket = $cookieStore.get('persm_ticket');

    $scope.category = [{'id':'00','value':'inbox'}, {'id':'01','value':'note'}, {'id':'02','value':'knowledge'}];
    $scope.getCategory = function () {
        //如果存在本地LocalStorage缓存则读缓存
        var category = storageBP.S("category");
        if(category!=undefined){
            //根据JSON数组长度吧数据压入到数组中
            for (var i = 0; i < category.length; i++) {
                $scope.category.push(category[i]);
            }
            return false;
        }
        //当没有缓存则从服务器请求数据
        $scope.cateUrl = 'attribute/list?';
        Data.get($scope.cateUrl).then(function (result) {
            if (result.code==50001) {
                storageBP.S("category",result.cate);
                var items = result.cate;
                //根据JSON数组长度吧数据压入到数组中
                for (var i = 0; i < items.length; i++) {
                    $scope.category.push(items[i]);
                }
            } else {
                console.log('(┬＿┬)额，服务器老兄好像没有给我返回类别的数据.我正准备打他~');
            }
        });
    };
    $scope.getCategory();

    $scope.formData = {
        category:'inbox',
        attribute:0,
        auth: ticket

    };
    $scope.postUrl = 'notes/handle?';
    $scope.processForm = function () {
        Data.post($scope.postUrl,$.param($scope.formData)).then(function (result) {
            if (result.code==50001) {
                localStorage.removeItem('persimmon-editor-content');
                layer.msg('笔记发布成功', {icon: 1});
                $location.path('/category/inbox');
            }
        });
    };
}]);

/* Note Post Controller */
myNote.controller('editNoteController', ['$scope','$location','$cookieStore','$stateParams','Data',function ($scope,$location,$cookieStore,$stateParams,Data) {

    var ticket = $cookieStore.get('persm_ticket');
    $scope.category = [{'id':'00','value':'inbox'}, {'id':'01','value':'note'}, {'id':'02','value':'knowledge'}];
    $scope.getCategory = function () {
        //如果存在本地LocalStorage缓存则读缓存
        var category = storageBP.S("category");
        if(category!=undefined){
            //根据JSON数组长度吧数据压入到数组中
            for (var i = 0; i < category.length; i++) {
                $scope.category.push(category[i]);
            }
            return false;
        }
        //当没有缓存则从服务器请求数据
        $scope.cateUrl = 'attribute/list?';
        Data.get($scope.cateUrl).then(function (result) {
            if (result.code==50001) {
                storageBP.S("category",result.cate);
                var items = result.cate;
                //根据JSON数组长度吧数据压入到数组中
                for (var i = 0; i < items.length; i++) {
                    $scope.category.push(items[i]);
                }
            } else {
                console.log('(┬＿┬)额，服务器老兄好像没有给我返回类别的数据.我正准备打他~');
            }
        });
    };
    $scope.getCategory();

    //获取原始数据
    $scope.formData = {
        auth : ticket
    };
    $scope.noteId = $stateParams.noteId;
    $scope.noteUrl = 'notes/read/'+$scope.noteId+'?action=edit';
    Data.get($scope.noteUrl).then(function (result) {
        $scope.formData.title = result.title;
        $scope.formData.category = result.category;
        $scope.formData.attribute = result.attribute;
        $scope.formData.tags = result.tags;
        $scope.formData.content = result.content;
    });

    //更新数据
    $scope.updateUrl = 'notes/handle?';
    $scope.processForm = function () {
        $scope.formData.id = $scope.noteId;
        Data.put($scope.updateUrl,$.param($scope.formData)).then(function (result) {
            if (result.code==50001) {
                localStorage.removeItem('persimmon-editor-content');
                layer.msg('笔记修改成功', {icon: 1});
                $location.path('/category/inbox');
            }
        });
    };
}
]);

/* Note Post Controller */
myNote.controller('archivesController', ['$scope','$sce','$compile','Data',function ($scope,$sce,$compile,Data) {
    //获取原始数据
    $scope.archivesUrl = 'notes/archives?';
    Data.get($scope.archivesUrl).then(function (result) {
        // Loads and runs script
        //var el = $compile($.parseHTML(result.content, document, true))($scope);
        var el = $compile($.parseHTML(result, document, true))($scope);
        //console.log(el);
        angular.element(document.querySelector('#notecontent')).append(el);
    });
}
]);

myNote.controller('tagsController',['$scope','$stateParams','Data', function ($scope,$stateParams,Data) {
    $scope.tags = $stateParams.tag;
    $scope.header = 'Tags: '+$scope.tags;

    $scope.pagingconf = {
        currentPage : 1,
        pageSize : 30,
        total : 200 ,
        dots : '...',
        adjacent : 2,
        ulClass : 'pagination',
        activeClass : 'active',
        disabledClass : 'disabled',
        hideIfEmpty : true,
        scrollTop : true,
        showPrevNext : true
    };

    $scope.tagUrl = 'notes/filter/tag/'+$scope.tags+'?&p=1';
    Data.get($scope.tagUrl).then(function (result) {
        $scope.data = result.note;
        $scope.pagingconf.total = result.total;
    });

    /*
     * console.log({text, page, pageSize, total});
     * Object {text: "Paging Clicked", page: 2, pageSize: 50, total: 200}
     *
     * */
    $scope.DoCtrlPagingAct = function(page, pageSize, total){
        //console.log(page, pageSize, total);
        var page_current = page?page:1;
        var page_row = pageSize?pageSize:30;
        $scope.url = 'notes/filter/tag/'+$scope.tags+'?&p='+page_current+'&row='+page_row;
        Data.get($scope.url).then(function (result) {
            $scope.data = result.note;
            $scope.pagingconf.total = result.total;
        });
    };

}]);
myNote.controller('settingController',['$scope','$location','Data', function ($scope,$location,Data) {
    $scope.formData = {};
    $scope.settingUrl = 'setting';
    Data.get($scope.settingUrl).then(function (result) {
        for(var key in result){
            var name = result[key]['name'];
            $scope.formData[name] = result[key]['value'];
            //console.log(result[key]['value']);
        }
    });

    //更新数据
    $scope.processForm = function () {
        Data.post($scope.settingUrl,$.param($scope.formData)).then(function (result) {
            if (result.code==50001) {
                layer.msg('保存成功', {icon: 1});
                for(var key in result.config){
                    var name = result.config[key]['name'];
                    $scope.formData[name] = result.config[key]['value'];
                    //console.log(result[key]['value']);
                }
            }
        });
    };

}]);


/* 用户修改信息控制器 */
myNote.controller('userController',['$scope','$http','$timeout', function ($scope,$http,$timeout) {
    $scope.formData = {};
    $scope.url = updateUrl;

    $scope.processForm = function () {
        $http.post($scope.url,$.param($scope.formData))
            .success(function (status,data) {
                if (data.status==50004) {
                    $scope.data = data;
                    layer.msg('资料修改失败',{icon: 11});
                }
                if(data.status==10003) {
                    $scope.data = data;
                    layer.msg('你输入的两次密码不一致',{icon: 1});
                    $timeout(function () {
                        location.reload();
                    },3000)
                }
                if(data.status==50001) {
                    $scope.data = data;
                    layer.msg('资料修改成功',{icon: 1});
                    $timeout(function () {
                        location.reload();
                    },3000)
                }
                $scope.data = data;
            });
    }
}]);



/* 获取父级 Todo控制器,调用sortable 插件 */
myNote.controller('todosController',['$scope', function ($scope) {
    $scope.sortableOptions = {
        connectWith: '.connectList'
    };
    //$scope.disableSelection();
}]);

/* 获取Todo控制器 */
myNote.controller('todoController',['$scope','$cookieStore','Data', function ($scope,$cookieStore,Data) {

    var ticket = $cookieStore.get('persm_ticket');

    /* 获取未开始的Todo */
    $scope.todoPaging = {
        currentPage : 1,
        pageSize : 10,
        total : 0 ,
        dots : '...',
        adjacent : 2,
        ulClass : 'pagination',
        activeClass : 'active',
        disabledClass : 'disabled',
        hideIfEmpty : true,
        scrollTop : false,
        showPrevNext : true
    };

    $scope.getFirstTodo = function () {
        Data.get('todo/list?status=0&p=1').then(function (result) {
            $scope.todoData = result.list;
            $scope.todoTotal = result.total;
            $scope.todoPaging.total = result.total;
        });
    };
    $scope.getFirstTodo();

    $scope.todoPage = function(page, pageSize, total) {
        $scope.getListUrl = 'todo/list?status=0&p='+page;
        Data.get($scope.getListUrl).then(function (result) {
            $scope.todoData = result.list;
            $scope.todoTotal = result.total;
            $scope.todoPaging.total = result.total;
        });
    };

    //* 已经完成的Todo
    $scope.completedPaging = {
        currentPage : 1,
        pageSize : 10,
        total : 0 ,
        dots : '...',
        adjacent : 2,
        ulClass : 'pagination',
        activeClass : 'active',
        disabledClass : 'disabled',
        hideIfEmpty : true,
        scrollTop : false,
        showPrevNext : true
    };

    $scope.getCompletedTodo = function () {
        Data.get('todo/list?status=1&p=1').then(function (result) {
            $scope.completedData = result.list;
            $scope.completedTotal = result.total;
            $scope.completedPaging.total = result.total;
        });
    };
    $scope.getCompletedTodo();

    $scope.completedPage = function(page, pageSize, total) {
        $scope.completedUrl = 'todo/list?status=1&p='+page;
        Data.get($scope.completedUrl).then(function (result) {
            $scope.completedData = result.list;
            $scope.completedTotal = result.total;
            $scope.completedPaging.total = result.total;
        });
    };

    //* 添加Todo
    $scope.formData = {
        'auth': ticket
    };
    $scope.addUrl = 'todo/handle?';
    $scope.addTodo = function () {
        Data.post($scope.addUrl,$.param($scope.formData)).then(function (result) {
            if (result.code==50004) {
                layer.msg('Todo添加失败',{icon: 11});
            }
            if(result.code==50001) {
                layer.msg('Todo添加成功',{icon: 1});
                $scope.formData.todo = '';
                $scope.todoData = result.todoList;
            }
        });
    };

    //* 更新Todo [完成]
    $scope.updateTodo = function (todoStatus,id) {
        var updateInfo = {
            status:todoStatus,
            id:id,
            auth:ticket,
            begintime:1
        };
        $scope.updateTodoUrl = 'todo/handle?';
        Data.put($scope.updateTodoUrl, $.param(updateInfo)).then(function (result) {
            if (result.code==50004) {
                layer.msg('Todo更新失败',{icon: 11});
            }
            if(result.code==50001){
                //更新完成后重新获取Todo的数据
                layer.msg('Todo更新成功',{icon: 1});
                $scope.getFirstTodo();
                $scope.getCompletedTodo();
            }
        });
    };

    //* 删除Todo
    $scope.deleteTodo = function (toDostatus,id) {
        $scope.deleteTodoUrl = 'todo/handle?status='+toDostatus+'&id='+id+'&auth='+ticket;
        //询问框
        layer.confirm('确定删除吗？', {
            btn: ['确定','取消'], //按钮
            shade: false //不显示遮罩
        }, function(index){
            Data.delete($scope.deleteTodoUrl).then(function (result) {
                if (result.code==50004) {
                    layer.msg('Todo删除失败',{icon: 11});
                }
                if(result.code==50001) {
                    //更新完成后重新获取Todo的数据
                    layer.msg('Todo删除成功',{icon: 1});
                    $scope.getFirstTodo();
                    $scope.getCompletedTodo();
                }
            });
            layer.close(index);
        }, function(index){
            layer.close(index);
        });
    };
    
}]);

myNote.controller('favController',['$scope','Data', function ($scope,Data) {
    $scope.url = 'fav/list?p=';
    $scope.lists = [];
    $scope.busy = false;
    $scope.page = 1;
    $scope.countPage = 1;

    $scope.noMoreItemsAvailable = false;
    $scope.loadMore = function () {
        if ($scope.busy) return;
        $scope.busy = true;
        //如果到最后一页了,则停止
        if($scope.page > $scope.countPage){
            console.log('page: %s,countPage: %s',$scope.page,$scope.countPage);
            $scope.busy = true;
            return false;
        }
        //否则加载数据
        var index = layer.load(2, {time: 10*1000});
        Data.get($scope.url+$scope.page).then(function (result) {
            var lists = result.list;
            $scope.countPage = result.page;
            //转换Json Object To Array
            var itemArr = [];
            for (var prop in lists) {
                itemArr.push(lists[prop]);
            }
            //根据JSON数组长度吧数据压入到数组中
            for (var i = 0; i < itemArr.length; i++) {
                $scope.lists.push(itemArr[i]);
            }
            layer.close(index);
            $scope.busy = false;
            $scope.page+=1;
            if($scope.lists.length == result.total){
                $scope.noMoreItemsAvailable = true;
            }
            $scope.$broadcast('scroll.infiniteScrollComplete');
        });
    };

    $scope.$on('stateChangeSuccess', function() {
        $scope.loadMore();
    });

    //Search
    $scope.searchUrl = 'fav/search?word=';
    $scope.formData = '';
    $scope.remove = false;

    if($scope.formData.word==undefined){
        $scope.remove = false;
    }

    $scope.processForm = function () {
        if($scope.formData.word!=undefined){
            $scope.remove = true;
        }
        Data.get($scope.searchUrl+$scope.formData.word).then(function (result) {
            if(result.list==''){
                $scope.lists = [{
                    id: "0",
                    userid: null,
                    title: "抱歉,暂时找不到内容",
                    source: "#/favorites",
                    snapshot: null,
                    thumb: "./Public/static/img/nopic.jpg",
                    create_time: null,
                    update_time: null
                }];
            } else {
                $scope.lists = result.list;
            }
            $scope.busy = true;

        });
    };
    
    $scope.reload = function () {
        location.reload();
    }

}]);
