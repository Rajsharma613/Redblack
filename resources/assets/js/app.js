// /**
//  * First we will load all of this project's JavaScript dependencies which
//  * includes Vue and other libraries. It is a great starting point when
//  * building robust, powerful web applications using Vue and Laravel.
//  */
//
// import {createApp} from "vue";
//
// require('./bootstrap');
// require('./parts/leftMenu');
//
// import Vue from '@vue/compat';
// import Vuex from 'vuex';
// import VueNextSelect from 'vue-next-select';
//
// import store from './store'
//
// import DatePicker from 'vue-datepicker-next';
// import 'vue-datepicker-next/locale/ru';
//
// import FileManager from 'gameap-file-manager';
// import Progressbar from './components/Progressbar';
//
// const InputTextList = () => import('./components/InputTextList' /* webpackChunkName: "components/input-text-list" */);
// const InputManyList = () => import('./components/InputManyList' /* webpackChunkName: "components/input-many-list" */);
// const ServerStatus = () => import('./components/ServerStatus' /* webpackChunkName: "components/server-status" */);
// const ServerConsole = () => import('./components/ServerConsole' /* webpackChunkName: "components/server-console" */);
// const ServerTasks = () => import('./components/ServerTasks' /* webpackChunkName: "components/server-tasks" */);
// const TaskOutput = () => import('./components/TaskOutput' /* webpackChunkName: "components/task-output" */);
// const UserServerPrivileges = () => import('./components/servers/UserServerPrivileges' /* webpackChunkName: "components/user-server-privileges" */);
// const GameModSelector = () => import('./components/servers/GameModSelector' /* webpackChunkName: "components/game-mod-selector" */);
// const DsIpSelector = () => import('./components/servers/DsIpSelector' /* webpackChunkName: "components/game-mod-selector" */);
// const SmartPortSelector = () => import('./components/servers/SmartPortSelector' /* webpackChunkName: "components/smart-port-selector" */);
// const ServerSelector = () => import('./components/servers/ServerSelector' /* webpackChunkName: "components/server-selector" */);
//
// const RconPlayers = () => import('./components/rcon/RconPlayers' /* webpackChunkName: "components/rcon-players" */);
// const RconConsole = () => import('./components/rcon/RconConsole' /* webpackChunkName: "components/rcon-console" */);
//
// const SettingsParameters = () => import('./components/SettingsParameters' /* webpackChunkName: "components/user-server-privileges" */);
//
// Vue.use(Vuex);
//
// Vue.use(FileManager, {store: store, lang: document.documentElement.lang});
//
// require('./parts/serverControl');
//
// var vm = new Vue({
//     // el: "#app",
//     // data: {
//     //     actionConfirmed: false
//     // },
//     data() {
//         return {
//             actionConfirmed: false
//         }
//     },
//     components: {
//         'v-select': VueNextSelect,
//         'progressbar': Progressbar,
//         'input-text-list': InputTextList,
//         'input-many-list': InputManyList,
//         'server-status': ServerStatus,
//         'server-console': ServerConsole,
//         'server-tasks': ServerTasks,
//         'task-output': TaskOutput,
//
//         'rcon-players': RconPlayers,
//         'rcon-console': RconConsole,
//
//         'user-server-privileges': UserServerPrivileges,
//         'smart-port-selector': SmartPortSelector,
//         'settings-parameters': SettingsParameters,
//         'game-mod-selector': GameModSelector,
//         'ds-ip-selector': DsIpSelector,
//         'server-selector': ServerSelector,
//
//         'date-picker': DatePicker,
//     },
//     methods: {
//         alert: function(message, callback) {
//             bootbox.alert(message, function() {
//                 if (typeof callback === "function") {
//                     callback();
//                 }
//             });
//         },
//         confirm: function(message, callback) {
//             bootbox.confirm({
//                 message: message,
//                 buttons: {
//                     confirm: {
//                         label: this.trans('main.yes'),
//                         className: 'btn-success'
//                     },
//                     cancel: {
//                         label: this.trans('main.no'),
//                         className: 'btn-danger'
//                     }
//                 },
//                 callback: function(result) {
//                     if (result) {
//                         callback();
//                     }
//                 }
//             });
//         },
//         confirmAction: function (e, message) {
//             if (!this.actionConfirmed) {
//                 e.preventDefault();
//
//                 this.confirm(message, function () {
//                     this.actionConfirmed = true;
//                     $(e.target).trigger(e.type);
//                 }.bind(this));
//             }
//
//             this.actionConfirmed = false;
//         },
//         mountProgressbar: function(mountPoint) {
//             var progressbar = Vue.extend(this.$options.components.progressbar);
//             return new progressbar().$mount(mountPoint);
//         },
//         appendComponent: function(componentName, appendPoint) {
//             var component = Vue.extend(this.$options.components[componentName]);
//
//             var componentInstance = new component().$mount();
//             $(appendPoint).append(componentInstance.$el);
//             return componentInstance;
//         },
//         setActiveTab () {
//             store.commit('activeTab');
//         }
//     },
//     computed: {
//         activeTab: {
//             get() { return this.$store.state.activeTab.name; },
//             set(tab) { this.$store.dispatch('activeTab/setName', tab) },
//         },
//     },
//     store
// });
//
// // createApp(vm).mount("#app")
//
// import fontawesome from '@fortawesome/fontawesome-free';
//
// window.gameap = vm.$root;
// window.gameapStore = store;

import { createApp } from 'vue'

const app = createApp({
    // components: {
    //     VueNextSelect,
    //     Progressbar,
    //     InputTextList,
    //     InputManyList,
    //     ServerStatus,
    //     ServerConsole,
    //     ServerTasks,
    //     TaskOutput,
    //     RconPlayers,
    //     RconConsole,
    //     UserServerPrivileges,
    //     SmartPortSelector,
    //     SettingsParameters,
    //     GameModSelector,
    //     DsIpSelector,
    //     ServerSelector,
    //     DatePicker
    // },
    // methods: {
    //     alert(message, callback) {
    //         bootbox.alert(message, () => {
    //             if (typeof callback === "function") {
    //                 callback();
    //             }
    //         });
    //     },
    //     confirm(message, callback) {
    //         bootbox.confirm({
    //             message: message,
    //             buttons: {
    //                 confirm: {
    //                     label: this.trans('main.yes'),
    //                     className: 'btn-success'
    //                 },
    //                 cancel: {
    //                     label: this.trans('main.no'),
    //                     className: 'btn-danger'
    //                 }
    //             },
    //             callback: (result) => {
    //                 if (result) {
    //                     callback();
    //                 }
    //             }
    //         });
    //     },
    //     confirmAction(e, message) {
    //         if (!this.actionConfirmed) {
    //             e.preventDefault();
    //
    //             this.confirm(message, () => {
    //                 this.actionConfirmed = true;
    //                 $(e.target).trigger(e.type);
    //             });
    //         }
    //
    //         this.actionConfirmed = false;
    //     },
    //     mountProgressbar(mountPoint) {
    //         const ProgressbarComponent = app.component('Progressbar');
    //         return app.mount(ProgressbarComponent, mountPoint);
    //     },
    //     appendComponent(componentName, appendPoint) {
    //         const component = app.component(componentName);
    //
    //         const componentInstance = app.mount(component);
    //         $(appendPoint).append(componentInstance.$el);
    //         return componentInstance;
    //     },
    //     setActiveTab() {
    //         store.commit('activeTab');
    //     }
    // },
    // computed: {
    //     activeTab: {
    //         get() { return store.state.activeTab.name; },
    //         set(tab) { store.dispatch('activeTab/setName', tab) },
    //     },
    // },
})

// app.use(store)
app.mount("#app")
window.gameap = app
