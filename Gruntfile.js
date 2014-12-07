module.exports = function (grunt) {
    'use strict';
    
    grunt.initConfig({
        jshint: {
            files: [
                'public/js/common.js',
                'public/js/form.js',
                'public/js/modal.js',
                'public/js/bannerEditor.js',
                'public/js/videoBannerEditor.js',
                'public/js/imageBannerEditor.js',
                'public/js/textPopupBannerEditor.js',
                'public/js/creepingLineBannerEditor.js',
                'public/js/stat.js'
            ],
            options: {
                globals: {
                    jQuery: true,
                    console: true,
                    module: true
                }
            }
        },
        uglify: {
            buildJs: {
                files: {
                    './public/js/main.js': [
                        'public/js/bootstrap.min.js',
                        'public/js/common.js',
                        'public/js/form.js',
                        'public/js/modal.js',
                        'public/js/jquery.pickmeup.min.js',
                        'public/js/bannerEditor.js',
                        'public/js/videoBannerEditor.js',
                        'public/js/imageBannerEditor.js',
                        'public/js/textPopupBannerEditor.js',
                        'public/js/creepingLineBannerEditor.js',
                        'public/js/stat.js'
                    ]
                }
            }
        },
        cssmin: {
            buildCss: {
                files: {
                    './public/css/main.css': [
                        'public/css/bootstrap.min.css',
                        'public/css/ui.css',
                        'public/css/pickmeup.min.css'
                    ]
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('default', ['jshint', 'uglify', 'cssmin']);
};