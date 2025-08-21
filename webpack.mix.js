const mix = require("laravel-mix");
require("laravel-mix-purgecss");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js")
    .js("resources/js/common.js", "public/js")
    .js("resources/js/home.js", "public/js")
    .js("resources/js/notification.js", "public/js")
    .js("resources/js/user_notification.js", "public/js")
    .js("resources/js/admin_notification.js", "public/js")
    .js("resources/js/chatting.js", "public/js")
    .js("resources/js/messenger.js", "public/js")
    .sass("resources/css/home.scss", "public/css")
    .sass("resources/css/main.scss", "public/css")
    .sass("resources/css/icons.scss", "public/css")
    .sass("resources/css/custom.scss", "public/css")
    .sass("resources/css/error.scss", "public/css")
    .sass("resources/css/messenger.scss", "public/css")
    .version()
    .purgeCss({
        safelist: [
            /active/,
            /show/,
            /modal/,
            /page/,
            /sidebar/,
            /backgdrop/,
            /fade/,
            /collaps/
        ]
    });
