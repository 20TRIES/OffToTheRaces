
require('./bootstrap');

window.Vue = require('vue');

const app = new Vue({
    el: '#app',
    data: {
        tab: 'active',
        races: {
            active: [],
            recent: [],
            topPerformances: [],
        }
    },
    created: function () {
        this.load();
    },
    methods: {
        load: function () {
            this.loadActiveRaces();
            this.loadRecentRaces();
            this.loadTopPerformances();
        },
        loadActiveRaces: function () {
            let req;
            req = new XMLHttpRequest();
            req.open("GET", "/api/races/active");
            req.setRequestHeader('Accept', 'application/json');
            req.setRequestHeader('Content-Type', 'application/json');
            req.addEventListener('load', (event) => {
                let data;
                data = JSON.parse(req.responseText);
                this.races.active = data.races;
            });
            req.send();
        },
        loadRecentRaces: function () {
            let req;
            req = new XMLHttpRequest();
            req.open("GET", "/api/races/finished/1500");
            req.setRequestHeader('Accept', 'application/json');
            req.setRequestHeader('Content-Type', 'application/json');
            req.addEventListener('load', (event) => {
                let data;
                data = JSON.parse(req.responseText);
                this.races.recent = data.races;
            });
            req.send();
        },
        loadTopPerformances: function () {
            let req;
            req = new XMLHttpRequest();
            req.open("GET", "/api/performances/fastest/1500");
            req.setRequestHeader('Accept', 'application/json');
            req.setRequestHeader('Content-Type', 'application/json');
            req.addEventListener('load', (event) => {
                let data;
                data = JSON.parse(req.responseText);
                this.races.topPerformances = data.performances;
            });
            req.send();
        },
        progressTime: function () {
            let req;
            req = new XMLHttpRequest();
            req.open("PUT", "/api/time");
            req.setRequestHeader('Accept', 'application/json');
            req.setRequestHeader('Content-Type', 'application/json');
            req.addEventListener('load', (event) => {
                this.load();
            });
            req.send();
        },
        createRace: function () {
            let req;
            req = new XMLHttpRequest();
            req.open("POST", "/api/race");
            req.setRequestHeader('Accept', 'application/json');
            req.setRequestHeader('Content-Type', 'application/json');
            req.addEventListener('load', (event) => {
                this.load();
            });
            req.send();
        },
    }
});
