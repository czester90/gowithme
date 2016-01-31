angular
    .module('gowithme').factory( 'AuthService', function($window, $http, $rootScope, $translate) {
    var user = getSession();

    return {
        isLoggedIn: isLoggedIn,
        getUserData: getUserData
    };

        function isLoggedIn()
        {
            return (user) ? true : false;
        }

        function getSession()
        {
            var userInfo = $window.sessionStorage.userInfo
            //return JSON.parse(userInfo);
            return 'ok';
        }

        function getUserData()
        {
            $http({
                method  : 'GET',
                url     : '/api/user/token/' + user.token + '/',
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                .success(function(data) {
                    $rootScope.user = data.data;
                    $translate.use($rootScope.user.locale);
                });
        }
});