/**
 * Created by Mr.Cong on 2015/5/14.
 */

var persimFactory = angular.module('persimService', []);

/**
 * 封装连接器
 *
 */
persimFactory.factory("Data", ['$http','$rootScope', function ($http,$rootScope) {
    var serviceBase = 'https://api.cong5.net/v2/';
    var params = '&auth='+$rootScope.ticket;

    var object = {};
    object.layer = function (info,icon,time) {
        layer.msg(info, {icon: icon,time:time});
    }
    object.get = function (q) {
        return $http.get(serviceBase + q + params).then(function (results) {
            return results.data;
        });
    };
    object.post = function (q, object) {
        return $http.post(serviceBase + q , object).then(function (results) {
            return results.data;
        });
    };
    object.put = function (q, object) {
        return $http.put(serviceBase + q + params, object).then(function (results) {
            return results.data;
        });
    };
    object.delete = function (q) {
        return $http.delete(serviceBase + q + params).then(function (results) {
            return results.data;
        });
    };

    return object;
}]);