<?php
return array(
    /* URL模式 */
    'URL_MODEL' => 2,
    'URL_ROUTER_ON' => true, // 是否开启URL路由
    'URL_ROUTE_RULES' => array(
        //Note
        array('v2/notes/list', 'Notes/noteList', '', array('method' => 'GET')),
        array('v2/notes/read/:id', 'Notes/oneNote', '', array('method' => 'GET')),
        array('v2/notes/search/:keyword', 'Notes/searchNote', '', array('method' => 'GET')),
        array('v2/notes/archives', 'Notes/archives', '', array('method' => 'GET')),
        array('v2/notes/count', 'Notes/countNote', '', array('method' => 'GET')),
        array('v2/notes/weather', 'Notes/weather', '', array('method' => 'GET')),
        array('v2/notes/upload', 'Notes/upload', '', array('method' => 'POST')),
        array('v2/notes/handle', 'Notes/optionsRequest', '', array('method' => 'OPTIONS')),
        array('v2/notes/handle', 'Notes/createNote', '', array('method' => 'POST')),
        array('v2/notes/handle', 'Notes/updateNote', '', array('method' => 'PUT')),
        array('v2/notes/handle', 'Notes/deleteNote', '', array('method' => 'DELETE')),
        array('v2/notes/filter/:type/:keyword', 'Notes/filter', '', array('method' => 'GET')),
        //Category
        array('v2/attribute/list', 'Attribute/index', '', array('method' => 'GET')),
        array('v2/attribute/handle', 'Attribute/addCate', '', array('method' => 'POST')),
        array('v2/attribute/handle', 'Attribute/optionsRequest', '', array('method' => 'OPTIONS')),
        array('v2/attribute/handle', 'Attribute/editCate', '', array('method' => 'PUT')),
        array('v2/attribute/handle', 'Attribute/deleteCate', '', array('method' => 'DELETE')),
        //To-Do
        array('v2/todo/list', 'Todo/todoList', '', array('method' => 'GET')),
        array('v2/todo/handle', 'Todo/optionsRequest', '', array('method' => 'OPTIONS')),
        array('v2/todo/handle', 'Todo/createTodo', '', array('method' => 'POST')),
        array('v2/todo/handle', 'Todo/updateTodo', '', array('method' => 'PUT')),
        array('v2/todo/handle', 'Todo/deleteTodo', '', array('method' => 'DELETE')),
        //Fav
        array('v2/fav/post','Fav/post','',array('method' => 'POST')),
        array('v2/fav/list','Fav/favList','',array('method' => 'GET')),
        array('v2/fav/search','Fav/search','',array('method' => 'GET')),
        array('v2/fav/delete','Fav/delete','',array('method' => 'OPTIONS')),
        array('v2/fav/delete','Fav/delete','',array('method' => 'DELETE')),
        array('v2/fav/thumb','Fav/createThumb','',array('method' => 'GET')),
        //Empty
        array('v2/notes', 'Notes/_empty', '', array('method' => 'GET')),
        array('v2/todo', 'Todo/_empty', '', array('method' => 'GET')),


    ),

);