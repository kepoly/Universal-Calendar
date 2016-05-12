var app = angular.module('londonBubble', ['angularUtils.directives.dirPagination', 'ui.bootstrap', 'ngRoute', 'angular.filter', 'firebase']);

app.factory("services", ['$http', function ($http) {

}]);


app.controller('homeCtrl', function ($scope, $location, $routeParams, $route, $uibModal) {



});

app.config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider, $firebaseArray) {
        $routeProvider.
            when('/', {
                title: 'Home',
                templateUrl: 'src/views/home2.php',
                controller: 'homeCtrl'
            })
            .otherwise({
                redirectTo: '/'
            });

        if (window.history && window.history.pushState) {

            $locationProvider.html5Mode(true);
        }
    }
]);
app.run(['$location', '$rootScope', function ($location, $rootScope) {
    //I think this does nothing...
    $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {

    });
}]);