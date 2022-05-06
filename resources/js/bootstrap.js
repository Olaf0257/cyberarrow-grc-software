window._ = require("lodash");

window.TimezoneList = require("./utils/timezone-list").default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require("popper.js").default;
} catch (e) {}

/* Meta data */
window.appBaseURL = document.head.querySelector(
    'meta[name="api-base-url"]'
).content;
window.appStorageURL = document.head.querySelector(
    'meta[name="api-storage-url"]'
).content;
window.appDiskDriver = document.head.querySelector(
    'meta[name="api-disk-driver"]'
).content;
window.pageTitle = document.head.querySelector(
    'meta[name="page-title"]'
).content;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axiosFetch = require("axios").create({
    baseURL: appBaseURL,
    headers: { "X-Requested-With": "XMLHttpRequest" },
});
window.axiosFetch.defaults.headers.common["X-Requested-With"] =
    "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
/*
 * GlOBAL FUNCTIONS AND VARIABLE
 */
const { decode } = require("html-entities");
window.decodeHTMLEntity = function (value) {
    return decode(value, { level: "html5" });
};

/* Sweet alter */
const Swal = require("sweetalert2");
const RcSwal = require("sweetalert2-react-content");
const RcSwalBox = RcSwal(Swal);

require("sweetalert2/src/sweetalert2.scss");

window.AlertBox = (option, callback = null) => {
    RcSwalBox.fire(option).then(callback);
};

/*
 * SETTING TOP LEVEL DATA SCOPE
 */
window.topLevelDataScope = JSON.parse(
    document.head.querySelector('meta[name="top-level-data-scope"]').content
);

window.getInitialDataScope = () => {
    const localStorageDataScope = JSON.parse(
        localStorage.getItem("data-scope")
    );

    return localStorageDataScope ? localStorageDataScope : topLevelDataScope;
};

window.reactAppBasePath = new URL(appBaseURL).pathname;

/* axios request interceptor */
window.axiosFetch.interceptors.request.use(
    function (config) {
        let dataScope = getInitialDataScope();

        if (dataScope) {
            config.params = { ...config.params, data_scope: dataScope.value };
        }

        // Do something before request is sent
        return config;
    },
    function (error) {
        // Do something with request error
        return Promise.reject(error);
    }
);

/**
 * ziggy route management
 */
// const ziggyjs= require('ziggy-js');
// import { Ziggy } from './ziggy';
// window.route = (name,arg1,arg2, callback = null) => {
//     return ziggyjs(name, arg1 , arg2, Ziggy);
// }
