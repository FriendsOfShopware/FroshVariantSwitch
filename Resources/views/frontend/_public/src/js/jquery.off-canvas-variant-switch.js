;(function($, window, document) {
    'use strict';

    $.plugin('froshOffCanvasVariantSwitch', {

        defaults: {
            url: '',
            basketId: 0,
            articleId: 0,
            number: '',
            quantity: 0,
            offCanvas: false
        },

        init: function () {
            var me = this;

            me.applyDataAttributes();

            $.ajax({
                data: me.opts,
                url: me.opts.url,
                type: "GET",
                dataType: "html",
                success: function (response) {
                    me.$el.html(response);

                    window.StateManager.addPlugin(
                        '*[data-variant-switch="true"]',
                        'froshVariantSwitch',
                        ['xs', 's', 'm', 'l', 'xl']
                    );
                }
            });
        }

    });

    $.subscribe("plugin/swCollapseCart/onLoadCartFinished", function() {
        window.StateManager.addPlugin(
            '*[data-off-canvas-variant-switch="true"]',
            'froshOffCanvasVariantSwitch',
            ['xs', 's', 'm', 'l', 'xl']
        );
    });

    $.subscribe("plugin/swCollapseCart/onArticleAdded", function() {
        window.StateManager.addPlugin(
            '*[data-off-canvas-variant-switch="true"]',
            'froshOffCanvasVariantSwitch',
            ['xs', 's', 'm', 'l', 'xl']
        );
    });

    $.subscribe("plugin/swCollapseCart/onRemoveArticleFinished", function() {
        window.StateManager.addPlugin(
            '*[data-off-canvas-variant-switch="true"]',
            'froshOffCanvasVariantSwitch',
            ['xs', 's', 'm', 'l', 'xl']
        );
    });

})(jQuery, window);