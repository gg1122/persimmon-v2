/**
 * Created by MrCong on 15/5/1.
 */

var NoteApp = angular.module('NoteApp', ['ui.router','ui.bootstrap','ngCookies','ngSanitize','wu.masonry','infinite-scroll','AngularPrint','persimService','noteDirective','perismFilter','myNote']);

NoteApp.config(function ($stateProvider,$urlRouterProvider) {

    $urlRouterProvider.otherwise('/login');

    $stateProvider
        .state('/',{
            url : '/',
            templateUrl : 'Themes/Note/default/Public/login.html',
            controller : 'loginController'
        })
        .state('login',{
            url : '/login',
            templateUrl : 'Themes/Note/default/Public/login.html',
            controller : 'loginController'
        })
        .state('index',{
            url : '/index',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@index' : {
                    templateUrl : 'Themes/Note/default/Public/home.html'
                }
            },
            controller : 'mainController'
        })
        .state('category',{
            url : '/category/:cat',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@category' : {
                    templateUrl : 'Themes/Note/default/Public/note.html'
                },
                'notemenu@category' : {
                    templateUrl : 'Themes/Note/default/Public/sidebar.html'
                }
            },
            controller : 'noteController'
        })
        .state('archives',{
            url : '/archives',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@archives' : {
                    templateUrl : 'Themes/Note/default/Public/archives.html'
                },
                'notemenu@archives' : {
                    templateUrl : 'Themes/Note/default/Public/sidebar.html'
                }
            },
            controller : 'archivesController'
        })
        .state('post',{
            url : '/post/:noteId',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@post' : {
                    templateUrl : 'Themes/Note/default/Public/note_post.html'
                },
                'notemenu@post' : {
                    templateUrl : 'Themes/Note/default/Public/sidebar.html'
                }
            },
            controller : 'notePostController'
        })
        .state('add',{
            url : '/add',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@add' : {
                    templateUrl : 'Themes/Note/default/Public/add.html'
                },
                'notemenu@add' : {
                    templateUrl : 'Themes/Note/default/Public/sidebar.html'
                }
            },
            controller : 'addNoteController'
        })
        .state('edit',{
            url : '/edit/:noteId',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@edit' : {
                    templateUrl : 'Themes/Note/default/Public/edit.html'
                },
                'notemenu@edit' : {
                    templateUrl : 'Themes/Note/default/Public/sidebar.html'
                }
            },
            controller : 'editNoteController'
        })
        .state('tag',{
            url : '/tag/:tag',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@tag' : {
                    templateUrl : 'Themes/Note/default/Public/tag.html'
                },
                'notemenu@tag' : {
                    templateUrl : 'Themes/Note/default/Public/sidebar.html'
                }
            },
            controller : 'tagsController'
        })
        .state('todo',{
            url : '/todo',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@todo' : {
                    templateUrl : 'Themes/Note/default/Public/todo.html'
                }
            },
            controller : 'todoController'
        })
        .state('favorites',{
            url : '/favorites',
            views : {
                '' : {
                    templateUrl : 'Themes/Note/default/Public/index.html'
                },
                'body@favorites' : {
                    templateUrl : 'Themes/Note/default/Public/favorites.html'
                }
            },
            controller : 'favController'
        })
        .state('logout',{
            url : '/logout',
            templateUrl : 'Themes/Note/default/Public/login.html',
            controller : 'logoutController'
        })
});
//运行检查
NoteApp.run(function ($rootScope,$state,$cookieStore,$location) {
    $rootScope.ticket = $cookieStore.get('persm_ticket');
    $rootScope.$on('$stateChangeStart',
        function(event, toState, toParams, fromState, fromParams){
            if ($rootScope.ticket==undefined) {
                var nextUrl = toState.name;
                if (nextUrl != '/login') {
                    $location.path("/login");
                }
            }
    });
    $rootScope.user = $cookieStore.get('persm_user');
});
