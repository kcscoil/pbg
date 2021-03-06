/*!
 * jQuery Require Plugin v1.0.1
 * https://github.com/masalygin/jquery.require
 *
 * Copyright 2014 Ilya Masalygin
 * Released under the MIT license
 */

(function($) {

    var require = function(options) {

        if ($.type(options) !== 'object') {
            return require({files: [].slice.call(arguments)});
        }

        options = $.extend({}, {
            files: [],
            async: true,
            cache: true,
            once: true
        }, options);

        return options.async ? require.async(options) : require.sync(options);

    };

    $.extend(require, {

        cache: {},

        clear: function(type) {

            if (type) {
                delete require.cache[type];
            } else {
                require.cache = {};
            }

        },

        async: function(options) {

            return (function(options) {

                var deferred = $.Deferred();
                var files = options.files;
                var len = files.length;
                var index = 0;

                $.each(files, function(i, file) {
                    require.load(file, options).done(function() {
                        index++;
                        deferred.notify(file, index, len);
                        if (index === len) {
                            deferred.resolve();
                        }
                    });
                });

                return deferred.promise();

            })(options);

        },

        sync: function(options) {

            return (function(options) {

                var deferred = $.Deferred();
                var files = options.files;
                var len = files.length;
                var index = 0;

                function load(file) {
                    require.load(file, options).done(function() {
                        index++;
                        deferred.notify(file, index, len);
                        if (index < len) {
                            load(files[index]);
                        } else {
                            deferred.resolve();
                        }
                    });
                }

                load(files[index]);

                return deferred.promise();

            })(options);

        },

        load: function(url, options) {

            var parts = url.split('.');
            var ext = parts[parts.length - 1];

            if (require[ext]) {

                if (options.once) {

                    if (!require.cache[ext]) {
                        require.cache[ext] = {};
                    }

                    if (!require.cache[ext][url]) {
                        require.cache[ext][url] = require[ext](url, options);
                    }

                    return require.cache[ext][url];

                } else {

                    return require[ext](url, options);

                }

            }

            throw new Error('extension ' + ext + ' is not supported');

        },

        js: function(url, options) {

            return $.ajax({
                dataType: 'script',
                cache: options.cache,
                url: url
            });

        },

        css: function(url, options) {

            var deferred = $.Deferred();
            var img;

            if (!options.cache) {
                url += '?_=' + $.now();
            }

            //$('<link>', {
            //    rel: 'stylesheet',
            //    href: url
            //}).appendTo(document.head);

            // Add by yep
            $("head").contents().filter(function() {
                return this.nodeType == 8;
            }).each(function(i, e) {
                if ($.trim(e.nodeValue) == "Related css to this page") {
                    $('<link>', {
                        rel: 'stylesheet',
                        href: url
                    }).insertAfter(e);
                }
            });


            img = new Image();
            img.onerror = function() {
                deferred.resolve();
            };
            img.src = url;

            return deferred.promise();

        }

    });


    $.require = require;

})(jQuery);