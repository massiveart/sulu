/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require.config({
    paths: {
        sulusearch: '../../sulusearch/js'
    }
});

define({

    name: "Sulu Search Bundle",

    initialize: function(app) {

        'use strict';

        var sandbox = app.sandbox;

        app.components.addSource('sulusearch', '/bundles/sulusearch/js/components');
    }
});
