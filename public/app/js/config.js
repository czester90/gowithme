/**
 * INSPINIA - Responsive Admin Theme
 *
 * Inspinia theme use AngularUI Router to manage routing and views
 * Each view are defined as state.
 * Initial there are written state for all view in theme.
 *
 */
function config($stateProvider, $urlRouterProvider, $ocLazyLoadProvider, IdleProvider, KeepaliveProvider) {

    // Configure Idle settings
    IdleProvider.idle(2); // in seconds
    IdleProvider.timeout(120); // in seconds

    $urlRouterProvider.otherwise("/landing");

    $ocLazyLoadProvider.config({
        // Set to true if you want to see what and when is dynamically loaded
        debug: false
    });

    $stateProvider

        .state('landing', {
            url: "/landing",
            templateUrl: "views/home/landing.html",
            data: { pageTitle: 'Landing page', specialClass: 'landing-page' }
        })
        .state('login', {
            url: "/login",
            controller: LoginCtrl,
            templateUrl: "views/home/login.html",
            data: { pageTitle: 'Login', specialClass: 'gray-bg' }
        })
        .state('register', {
            url: "/register",
            controller: RegisterCtrl,
            templateUrl: "views/home/register.html",
            data: { pageTitle: 'Register', specialClass: 'gray-bg' }
        })
        .state('app', {
            abstract: true,
            url: "/app",
            templateUrl: "views/common/content.html"
        })
        .state('app.dashboard', {
            url: "/dashboard",
            controller: DashboardCtrl,
            templateUrl: "views/dashboard.html",
            resolve: {
                loadPlugin: function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {

                            serie: true,
                            name: 'angular-flot',
                            files: [ 'js/plugins/flot/jquery.flot.js', 'js/plugins/flot/jquery.flot.time.js', 'js/plugins/flot/jquery.flot.tooltip.min.js', 'js/plugins/flot/jquery.flot.spline.js', 'js/plugins/flot/jquery.flot.resize.js', 'js/plugins/flot/jquery.flot.pie.js', 'js/plugins/flot/curvedLines.js', 'js/plugins/flot/angular-flot.js', ]
                        },
                        {
                            name: 'angles',
                            files: ['js/plugins/chartJs/angles.js', 'js/plugins/chartJs/Chart.min.js']
                        },
                        {
                            name: 'angular-peity',
                            files: ['js/plugins/peity/jquery.peity.min.js', 'js/plugins/peity/angular-peity.js']
                        }
                    ]);
                }
            }
        });
}
angular
    .module('gowithme')
    .config(config)
    .run(function($rootScope, $state) {
        $rootScope.$state = $state;
    });
