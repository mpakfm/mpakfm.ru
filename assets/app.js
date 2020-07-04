require('jquery');
require('jquery-rss');
require('bootstrap');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.min');
var GitHubActivity = require('./dist/plugins/github-activity/github-activity-0.1.5.min');
global.GitHubActivity = GitHubActivity;
var GitHubCalendar = require('github-calendar');
global.GitHubCalendar = GitHubCalendar;
require('@fancyapps/fancybox/dist/jquery.fancybox.min');
require('tinymce')
require('./dist/main');
require('./dist/blog');
