# Persimmon Note+

一个个人笔记、任务管理一体的一个Web APP。

后台系统使用了ThinkPHP框架来编写API接口。

前台使用了AngularJS来做前台框架，然后向API接口提交和请求数据。

Example：https://i.cong5.net/

## Note 笔记管理模块(主模块)

本系统基于“如何构建完善的笔记系统”来开发的，Note模块是整个系统唯一的后台，后端PHP只抛出JSON格式的数据，前端完全使用AngularJS来开发，由于目前是单用户使用，所以暂时不涉及权限控制，目前只有用户登陆。

笔记一般归属为3个分类，分别是：

	Inbox（收集）
	Note（储存）
	Knowledge（主题）
	
Inbox：用来收集任何时刻、任何方式得到的零碎资讯，用关键词记下，放在Inbox下随便一个页面里。称为关键词笔记。

Note：用来存放Inbox中经过整理的完整笔记。每一则笔记都有完整的时间、标题及脉络。称为参考笔记。

Knowledge：这是重点。用来将Note里面储存的笔记主题化。称为主题笔记。

如果默认的3个类别无法满足您的需求，你还可以自己创建新的category

## API接口模块


AngularJS跨域的时候使用put请求会产生一个OPTIONS请求

#Note
请求地址：/v2/notes/list                     请求方法：'GET'         获取笔记列表接口

请求地址：/v2/notes/read/:id                 请求方法：'GET'         获取单条接口

请求地址：/v2/notes/search/:keyword          请求方法：'GET'         笔记搜索接口

请求地址：/v2/notes/archives                 请求方法：'GET'         笔记存档列表接口

请求地址：/v2/notes/count                    请求方法：'GET'         笔记/Todo统计接口

请求地址：/v2/notes/weathe                   请求方法：'GET'         天气接口

请求地址：/v2/notes/upload                   请求方法：'POST'        文件上传接口

请求地址：/v2/notes/handle                   请求方法：'OPTIONS'     响应angularjs的put请求前发起的OPTIONS请求的接口

请求地址：/v2/notes/handle                   请求方法：'POST'        新增笔记接口

请求地址：/v2/notes/handle                   请求方法：'PUT'         更新笔记接口

请求地址：/v2/notes/handle                   请求方法：'DELETE'      删除笔记接口

请求地址：/v2/notes/filter/:type/:keyword    请求方法：'GET'         笔记搜索接口

#Todo           
请求地址：/v2/todo/list                      请求方法：'GET'         To-Do列表接口

请求地址：/v2/todo/handle                    请求方法：'OPTIONS'     响应angularjs的put请求前发起的OPTIONS请求的接口

请求地址：/v2/todo/handle                    请求方法：'POST'        新增To-Do接口

请求地址：/v2/todo/handle                    请求方法：'PUT'         更新To-Do接口

请求地址：/v2/todo/handle                    请求方法：'DELETE'      删除To-Do接口

#Fav            
请求地址：/v2/fav/post                       请求方法：'POST'        新增收藏接口

请求地址：/v2/fav/list                       请求方法：'GET'         获取收藏列表接口

请求地址：/v2/fav/search                     请求方法：'GET'         收藏搜索接口

请求地址：/v2/fav/thumb                      请求方法：'GET'         根据URL生成网页快照的缩略图的接口



## 状态码

    50001   数据更新成功
    50002   数据更新失败
    50003   没有权限
    50004   用户不存在
    50005   新增用户失败[错误]
    50006   用户密码错误
    50007   用户被禁用
    50008   用户被删除
    50009   两次输入的密码不一致
    50010   删除失败

