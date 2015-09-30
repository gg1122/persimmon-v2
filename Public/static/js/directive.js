/**
 * Created by Mr.Cong on 2015/5/5.
 */
'use strict';
var noteDirective = angular.module('noteDirective', []);

/**
 * Sets focus to this element if the value of focus-me is true.
 * @example
 *  <a ng-click="addName=true">add name</a>
 *  <input ng-show="addName" type="text" ng-model="name" focus-me="{{addName}}" />
 */
noteDirective.directive('focusMe', ['$timeout', function ($timeout) {
    return {
        scope: {trigger: '@focusMe'},
        link: function (scope, element) {
            scope.$watch('trigger', function (value) {
                if (value === "true") {
                    $timeout(function () {
                        element[0].focus();
                    });
                }
            });
        }
    };
}]);


//Pagination Plugin
noteDirective.directive('paging', function () {

    // Assign null-able scope values from settings
    function setScopeValues(scope, attrs) {

        scope.List = [];
        scope.Hide = false;
        scope.dots = scope.dots || '...';
        scope.page = parseInt(scope.page) || 1;
        scope.total = parseInt(scope.total) || 0;
        scope.ulClass = scope.ulClass || 'pagination';
        scope.adjacent = parseInt(scope.adjacent) || 2;
        scope.activeClass = scope.activeClass || 'active';
        scope.disabledClass = scope.disabledClass || 'disabled';

        scope.scrollTop = scope.$eval(attrs.scrollTop);
        scope.hideIfEmpty = scope.$eval(attrs.hideIfEmpty);
        scope.showPrevNext = scope.$eval(attrs.showPrevNext);

    }


    // Validate and clean up any scope values
    // This happens after we have set the
    // scope values
    function validateScopeValues(scope, pageCount) {

        // Block where the page is larger than the pageCount
        if (scope.page > pageCount) {
            scope.page = pageCount;
        }

        // Block where the page is less than 0
        if (scope.page <= 0) {
            scope.page = 1;
        }

        // Block where adjacent value is 0 or below
        if (scope.adjacent <= 0) {
            scope.adjacent = 2;
        }

        // Hide from page if we have 1 or less pages
        // if directed to hide empty
        if (pageCount <= 1) {
            scope.Hide = scope.hideIfEmpty;
        }
    }


    // Internal Paging Click Action
    function internalAction(scope, page) {

        // Block clicks we try to load the active page
        if (scope.page == page) {
            return;
        }

        // Update the page in scope
        scope.page = page;

        // Pass our parameters to the paging action
        scope.pagingAction({
            page: scope.page,
            pageSize: scope.pageSize,
            total: scope.total
        });

        // If allowed scroll up to the top of the page
        if (scope.scrollTop) {
            scrollTo(0, 0);
        }
    }


    // Adds the first, previous text if desired
    function addPrev(scope, pageCount) {

        // Ignore if we are not showing
        // or there are no pages to display
        if (!scope.showPrevNext || pageCount < 1) {
            return;
        }

        // Calculate the previous page and if the click actions are allowed
        // blocking and disabling where page <= 0
        var disabled = scope.page - 1 <= 0;
        var prevPage = scope.page - 1 <= 0 ? 1 : scope.page - 1;

        var first = {
            value: '<<',
            title: 'First Page',
            liClass: disabled ? scope.disabledClass : '',
            action: function () {
                if (!disabled) {
                    internalAction(scope, 1);
                }
            }
        };

        var prev = {
            value: '<',
            title: 'Previous Page',
            liClass: disabled ? scope.disabledClass : '',
            action: function () {
                if (!disabled) {
                    internalAction(scope, prevPage);
                }
            }
        };

        scope.List.push(first);
        scope.List.push(prev);
    }


    // Adds the next, last text if desired
    function addNext(scope, pageCount) {

        // Ignore if we are not showing
        // or there are no pages to display
        if (!scope.showPrevNext || pageCount < 1) {
            return;
        }

        // Calculate the next page number and if the click actions are allowed
        // blocking where page is >= pageCount
        var disabled = scope.page + 1 > pageCount;
        var nextPage = scope.page + 1 >= pageCount ? pageCount : scope.page + 1;

        var last = {
            value: '>>',
            title: 'Last Page',
            liClass: disabled ? scope.disabledClass : '',
            action: function () {
                if (!disabled) {
                    internalAction(scope, pageCount);
                }
            }
        };

        var next = {
            value: '>',
            title: 'Next Page',
            liClass: disabled ? scope.disabledClass : '',
            action: function () {
                if (!disabled) {
                    internalAction(scope, nextPage);
                }
            }
        };

        scope.List.push(next);
        scope.List.push(last);
    }


    // Add Range of Numbers
    function addRange(start, finish, scope) {

        var i = 0;
        for (i = start; i <= finish; i++) {

            var item = {
                value: i,
                title: 'Page ' + i,
                liClass: scope.page == i ? scope.activeClass : '',
                action: function () {
                    internalAction(scope, this.value);
                }
            };

            scope.List.push(item);
        }
    }


    // Add Dots ie: 1 2 [...] 10 11 12 [...] 56 57
    function addDots(scope) {
        scope.List.push({
            value: scope.dots
        });
    }


    // Add First Pages
    function addFirst(scope, next) {
        addRange(1, 2, scope);

        // We ignore dots if the next value is 3
        // ie: 1 2 [...] 3 4 5 becomes just 1 2 3 4 5
        if (next != 3) {
            addDots(scope);
        }
    }


    // Add Last Pages
    function addLast(pageCount, scope, prev) {

        // We ignore dots if the previous value is one less that our start range
        // ie: 1 2 3 4 [...] 5 6  becomes just 1 2 3 4 5 6
        if (prev != pageCount - 2) {
            addDots(scope);
        }

        addRange(pageCount - 1, pageCount, scope);
    }


    // Main build function
    function build(scope, attrs) {

        // Block divide by 0 and empty page size
        if (!scope.pageSize || scope.pageSize < 0) {
            return;
        }

        // Assign scope values
        setScopeValues(scope, attrs);

        // local variables
        var start,
            size = scope.adjacent * 2,
            pageCount = Math.ceil(scope.total / scope.pageSize);

        // Validate Scope
        validateScopeValues(scope, pageCount);

        // Calculate Counts and display
        addPrev(scope, pageCount);
        if (pageCount < (5 + size)) {

            start = 1;
            addRange(start, pageCount, scope);

        } else {

            var finish;

            if (scope.page <= (1 + size)) {

                start = 1;
                finish = 2 + size + (scope.adjacent - 1);

                addRange(start, finish, scope);
                addLast(pageCount, scope, finish);

            } else if (pageCount - size > scope.page && scope.page > size) {

                start = scope.page - scope.adjacent;
                finish = scope.page + scope.adjacent;

                addFirst(scope, start);
                addRange(start, finish, scope);
                addLast(pageCount, scope, finish);

            } else {

                start = pageCount - (1 + size + (scope.adjacent - 1));
                finish = pageCount;

                addFirst(scope, start);
                addRange(start, finish, scope);

            }
        }
        addNext(scope, pageCount);

    }


    // The actual angular directive return
    return {
        restrict: 'EA',
        scope: {
            page: '=',
            pageSize: '=',
            total: '=',
            dots: '@',
            hideIfEmpty: '@',
            ulClass: '@',
            activeClass: '@',
            disabledClass: '@',
            adjacent: '@',
            scrollTop: '@',
            showPrevNext: '@',
            pagingAction: '&'
        },
        template: '<ul ng-hide="Hide" ng-class="ulClass"> ' +
        '<li ' +
        'title="{{Item.title}}" ' +
        'ng-class="Item.liClass" ' +
        'ng-click="Item.action()" ' +
        'ng-repeat="Item in List"> ' +
        '<span ng-bind="Item.value"></span> ' +
        '</li>' +
        '</ul>',
        link: function (scope, element, attrs) {

            // Hook in our watched items
            scope.$watchCollection('[page,pageSize,total]', function () {
                build(scope, attrs);
            });
        }
    };
});


//Simeditor 编辑器
noteDirective.directive('simditor', ['$timeout', function ($timeout) {
    return {
        restrict: 'A',
        require: '?ngModel',
        scope: true,
        link: function (scope, elem, attrs, ngModel) {
            var option = {
                textarea: elem,
                toolbar: ['title', 'bold', 'italic', 'underline', 'strikethrough', 'color', '|', 'ol', 'ul', 'blockquote', 'code', 'table', '|', 'link', 'image', 'hr', '|', 'indent', 'outdent', 'emoji'], //, '|', 'source'
                placeholder: '#Here ...',
                toolbarFloat: true,
                toolbarFloatOffset: 0,
                pasteImage: true,
                emoji: {
                    imagePath: '/Public/static/simditor/extends/emoji/images/emoji/'
                },
                autosave: 'editor-content',
                defaultImage: '/Public/static/simditor/images/image.png',
                upload: {
                    url: '/upload',
                    params: null,
                    fileKey: 'upload_file',
                    connectionCount: 3,
                    leaveConfirm: '正在上传文件，如果离开上传会自动取消'
                }
            };
            if (attrs.operation == "view") {
                option.toolbarHidden = true;
            }

            var editor = new Simditor(option);

            if (typeof(attrs.minheight) != "undefined") {
                elem.prev().css("min-height", attrs.minheight);
            }

            if (attrs.operation == "view") {
                elem.prev().attr("contenteditable", false);
            }

            ngModel.$render = function () {
                editor.setValue(ngModel.$viewValue);
            };

            elem.click(function (event) {
                event.stopPropagation();
            })

            editor.on('valuechanged', function () {
                $timeout(function () {
                    scope.$apply(function () {
                        var value = editor.getValue();
                        ngModel.$setViewValue(value);
                    });
                });
            });
        }
    };
}]);

/**
 * AngularJS directive.
 * Allows you to modify the page DOM and CSS styles before printing by exexuting the passed expression.
 * Will force a $digest phase before the print operation.
 *
 * Example: <div ng-class="{printed: isPrinted}" on-before-print="printed = true" />
 */
noteDirective.directive('onBeforePrint', ['$window', '$rootScope', '$timeout', function onBeforePrint($window, $rootScope, $timeout) {
    var beforePrintDirty = false;
    var listeners = [];

    var beforePrint = function () {
        if (beforePrintDirty) return;

        beforePrintDirty = true;

        if (listeners) {
            for (var i = 0, len = listeners.length; i < len; i++) {
                listeners[i].triggerHandler('beforePrint');
            }

            var scopePhase = $rootScope.$$phase;

            // This must be synchronious so we call digest here.
            if (scopePhase != '$apply' && scopePhase != '$digest') {
                $rootScope.$digest();
            }
        }

        $timeout(function () {
            // This is used for Webkit. For some reason this gets called twice there.
            beforePrintDirty = false;
        }, 100, false);
    };

    if ($window.matchMedia) {
        var mediaQueryList = $window.matchMedia('print');
        mediaQueryList.addListener(function (mql) {
            if (mql.matches) {
                beforePrint();
            }
        });
    }

    $window.onbeforeprint = beforePrint;

    return function (scope, iElement, iAttrs) {
        function onBeforePrint() {
            scope.$eval(iAttrs.onBeforePrint);
        }

        listeners.push(iElement);
        iElement.on('beforePrint', onBeforePrint);

        scope.$on('$destroy', function () {
            iElement.off('beforePrint', onBeforePrint);

            var pos = -1;

            for (var i = 0, len = listeners.length; i < len; i++) {
                var currentElement = listeners[i];

                if (currentElement === iElement) {
                    pos = i;
                    break;
                }
            }

            if (pos >= 0) {
                listeners.splice(pos, 1);
            }
        });
    };
}]);

/*
 * Prism JS 代码高亮
 *
 * <ng-prism class="language-css">
 *      body {
 *         color: red;
 *      }
 * </ng-prism>
 *
 * the files prismjs and prism css must be included in HTML, use class "language-XXX" to specify language
 * */
noteDirective.directive('prism', [function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                element.ready(function () {
                    Prism.highlightElement(element[0]);
                });
            }
        }
    }]
);

//未完成
noteDirective.directive('minimalize',[function () {
    return {
        restrict: 'AE',
        replace: false,
        template: '',
        scope: {},
        link: function(scope, element, attrs) {
            var children = element.children();
            console.log(children);
        }
    }
}]);
