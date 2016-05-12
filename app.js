var app = angular.module('calendar', ['ui.bootstrap', 'ngRoute']);

app.filter('startFrom', function() {
    return function(input, start) {
        if(input) {
            start = +start; //parse to int
            return input.slice(start);
        }
        return [];
    }
});

//api
app.factory("services", ['$http', function ($http) {
    var serviceBase = 'backend/booking/api/v1/';
    var obj = {};
    obj.getFormData = function (input) {
        return $http.get(serviceBase + 'returnCalendarData?' + input);
    }
    obj.getCalendarHeader = function () {
        return $http.get(serviceBase + 'returnCalendarHeader');
    }
    return obj;
}]);

//beautiful directive <3
app.directive("scroll", function ($window) {
    return function(scope, element, attrs) {
        angular.element($window).bind("scroll", function() {
            if (this.pageYOffset >= 300) {
                scope.boolChangeClass = true;
            } else {
                scope.boolChangeClass = false;
            }
            scope.$apply();
        });
    };
});

//booking controller
app.controller('bookingPageCtrl', function ($scope, $http, $location, services) {

    $scope.showBookingForm = false;

    services.getFormData().then(function(response) {
        $scope.calendarDays = response.data.calendarDays;
        $scope.calendarCells = response.data.calendarCells;
        $scope.bookingFormData = response.data.bookingForm;
        $scope.calendarHeader = response.data.calendarHeader;
    });

    $scope.getFormWithData = function(input) {
        $scope.showBookingForm = true;
        console.log(input);
    }

    $scope.changeMonth = function(input) {
        services.getFormData(input).then(function(response) {
            $scope.calendarDays = response.data.calendarDays;
            $scope.calendarCells = response.data.calendarCells;
            $scope.bookingFormData = response.data.bookingForm;
            $scope.calendarHeader = response.data.calendarHeader;
        });
    }

});

app.config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider) {
        $routeProvider.
            when('/', {
                title: 'Home',
                templateUrl: 'backend/booking/calendar/calendar.php',
                controller: 'bookingPageCtrl'
            })
            .when('/booking', {
                title: 'Home',
                templateUrl: 'backend/booking/calendar/calendar.php',
                controller: 'bookingPageCtrl'
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
    $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {

    });
}]);