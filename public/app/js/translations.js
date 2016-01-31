/**
 * INSPINIA - Responsive Admin Theme
 *
 */
function config($translateProvider) {

    $translateProvider
        .translations('en', {

            // Error Response
            ERROR_USERNAME_EXISTS: 'Username exists already',
            // Define all menu elements
            DASHBOARD: 'Dashboard',
            ADVERTS: 'Adverts',
            MY_ADVERTS: 'My Adverts',
            LANGUAGE: 'Language',
            PROFILE: 'Profile',
            ENGLISH: 'English',
            POLISH: 'Polish',
            LOGOUT: 'Log out',
            GRAPHS: 'Graphs',
            MAILBOX: 'Mailbox',
            WIDGETS: 'Widgets',
            METRICS: 'Metrics',
            FORMS: 'Forms',
            APPVIEWS: 'App views',
            OTHERPAGES: 'Other pages',
            UIELEMENTS: 'UI elements',
            MISCELLANEOUS: 'Miscellaneous',
            GRIDOPTIONS: 'Grid options',
            TABLES: 'Tables',
            COMMERCE: 'E-commerce',
            GALLERY: 'Gallery',
            MENULEVELS: 'Menu levels',
            ANIMATIONS: 'Animations',
            LANDING: 'Landing page',
            LAYOUTS: 'Layouts',

            // Define some custom text
            WELCOME: 'Welcome Amelia',
            MESSAGEINFO: 'You have 42 messages and 6 notifications.',
            SEARCH: 'Search for something...',

        })
        .translations('pl', {

            // Error Response
            ERROR_USERNAME_EXISTS: 'Username exists already',
            // Define all menu elements
            DASHBOARD: 'Tablica',
            ADVERTS: 'Ogłoszenia',
            MY_ADVERTS: 'Moje Ogłoszenia',
            LANGUAGE: 'Język',
            PROFILE: 'Profil',
            ENGLISH: 'Angielski',
            POLISH: 'Polski',
            LOGOUT: 'Wyloguj się',
            GRAPHS: 'Gráficos',
            MAILBOX: 'El correo',
            WIDGETS: 'Widgets',
            METRICS: 'Métrica',
            FORMS: 'Formas',
            APPVIEWS: 'Vistas app',
            OTHERPAGES: 'Otras páginas',
            UIELEMENTS: 'UI elements',
            MISCELLANEOUS: 'Misceláneo',
            GRIDOPTIONS: 'Cuadrícula',
            TABLES: 'Tablas',
            COMMERCE: 'E-comercio',
            GALLERY: 'Galería',
            MENULEVELS: 'Niveles de menú',
            ANIMATIONS: 'Animaciones',
            LANDING: 'Página de destino',
            LAYOUTS: 'Esquemas',

            // Define some custom text
            WELCOME: 'Bienvenido Amelia',
            MESSAGEINFO: 'Usted tiene 42 mensajes y 6 notificaciones.',
            SEARCH: 'Busca algo ...',
        });

    $translateProvider.preferredLanguage('en');

}

angular
    .module('gowithme')
    .config(config)
