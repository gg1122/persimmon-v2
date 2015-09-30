/**
 * Created by Mr.Cong on 2015/5/19.
 */
var perismFilter = angular.module('perismFilter',[]);

perismFilter.filter('to_trusted',['$sce', function ($sce) {
    return function (text) {
        return $sce.trustAsHtml(text);
    }
}]);