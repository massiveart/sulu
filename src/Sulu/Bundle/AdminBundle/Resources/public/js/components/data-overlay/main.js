/*
 * This file is part of the Sulu CMF.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

/**
 * @class DataOverlay
 * @constructor
 *
 * @param {Object} [options] Configuration object
 * @param {String} [options.instanceName] The instance name of the sidebar
 * @param {String} [options.component] The component to start
 */
define(['text!components/data-overlay/main.html'], function(mainTpl) {

    'use strict';

    var defaults = {
        instanceName: '',
        component: ''
    },

    createEventName = function(postfix) {
        return 'sulu.data-overlay.' + ((!!this.options.instanceName) ? this.options.instanceName + '.' : '') + postfix;
    },

    /**
     * trigger after initialization has finished
     * @event sulu.data-overlay.[INSTANCE_NAME].initialized
     */
    INITIALIZED = function() {
        return createEventName.call(this, 'initialized');
    },

    /**
     * show the overlay
     * @event sulu.data-overlay.[INSTANCE_NAME].show
     */
    SHOW = function() {
        return createEventName.call(this, 'show');
    },

    /**
     * hide the overlay
     * @event sulu.data-overlay.[INSTANCE_NAME].hide
     */
    HIDE = function() {
        return createEventName.call(this, 'hide');
    };

    return {
        /**
         * @method initialize
         */
        initialize: function() {
            // merge defaults
            this.options = this.sandbox.util.extend(true, {}, defaults, this.options);
            this.mainTpl = this.sandbox.util.template(mainTpl);

            this.render();
            this.startComponent();
            this.bindEvents();
            this.bindDOMEvents();
            this.sandbox.emit(INITIALIZED.call(this));
        },

        /**
         * @method bindEvents
         */
        bindEvents: function() {
            this.sandbox.on(SHOW.call(this), this.show.bind(this));
            this.sandbox.on(HIDE.call(this), this.hide.bind(this));
        },

        /**
         * @method bindDOMEvents
         */
        bindDOMEvents: function() {
            this.$el.on('click', '.data-overlay-close', this.hide.bind(this));
        },

        /**
         * @method render
         */
        render: function() {
            var tpl = this.mainTpl();
            this.$el.html(tpl);
        },

        /**
         * start the declared component
         * @method startComponent
         */
        startComponent: function() {
            var component = this.options.component;

            if (!!component) {
                this.sandbox.start([
                    {
                        name: component,
                        options: {
                            el: '.data-overlay-content'
                        }
                    }
                ]);
            } else {
                throw new Error('No component defined!');
            }
        },

        /**
         * show the overaly instance
         * @method show
         */
        show: function() {
            this.$el.fadeIn(150);
        },

        /**
         * hide the overaly instance
         * @method hide
         */
        hide: function() {
            this.$el.fadeOut(150);
        }
    };
});