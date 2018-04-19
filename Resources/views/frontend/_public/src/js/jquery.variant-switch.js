;(function($, window, document) {
    'use strict';

    $.plugin('froshVariantSwitch', {

        defaults: {

            switchUrl: '',

            detailId: 0,

            productUrl: '',

            productQuery: '',

            quantity: 1,

            offCanvas: false,

            modalWidth: 1024,

            zIndex: 8001,

            modalContentOuterCls: 'product--details ajax-modal--custom'

        },

        init: function () {
            var me = this;

            me.applyDataAttributes();

            me._isOpened = false;

            me.opts.modal = $.extend({}, Object.create($.modal.defaults), me.opts);
            me.opts.modal.additionalClass = 'switch-variant--modal';
            me.opts.modal.width = me.opts.modalWidth;
            me.opts.modal.overlay = !me.opts.offCanvas;

            me.registerEvents();
        },

        registerEvents: function() {
            var me = this;

            me._on(me.$el, 'submit', $.proxy(me.onSubmit, me));

            $.subscribe(me.getEventName('plugin/swModal/onClose'), $.proxy(me.onClose, me));

            $.publish('plugin/froshVariantSwitch/onRegisterEvents', [ me ]);
        },

        onSubmit: function (event) {
            event.preventDefault();

            var me = this,
                target = me.opts.productUrl,
                query = me.opts.productQuery;

            $.loadingIndicator.open();

            $.ajax({
                url: target + query,
                type: "GET",
                dataType: "html",
                success: function (response) {
                    var $response = $($.parseHTML(response, document, true)),
                        $detail = $response.find('.product--detail-upper'),
                        index = $('*[data-variant-switch="true"]').index(me.$el);

                    $.loadingIndicator.close();

                    if (!$detail.length) {
                        return;
                    }

                    me.removeDetailElements($detail);

                    $.modal.open(
                        $('<div></div>')
                            .prop('class', me.opts.modalContentOuterCls)
                            .attr('data-index', index)
                            .attr('data-ajax-variants-container', 'true')
                            .attr('data-ajax-wishlist', 'true')
                            .append($detail[0].outerHTML)[0].outerHTML,
                        me.opts.modal
                    );

                    window.StateManager.addPlugin(
                        '*[data-ajax-variants-container="true"]',
                        'swAjaxVariant',
                        ['xs', 's', 'm', 'l', 'xl']
                    );

                    var $modal = $('.switch-variant--modal'),
                        $buyboxForm = $modal.find('*[data-add-article="true"]');

                    $modal.css('zIndex', me.opts.zIndex);
                    $modal.find('.modal--close').css('zIndex', 1001);

                    $modal.find('*[data-ajax-variants-container="true"]').data('plugin_swAjaxVariant')._getUrl = function () {
                       return target;
                    };

                    if (!$buyboxForm.length) {
                        return;
                    }

                    me._on($buyboxForm, 'submit', $.proxy(me.onBuyboxSubmit, me));

                    me.setBuyboxQuantity($buyboxForm);
                }
            });

            me._isOpened = true;
        },

        onBuyboxSubmit: function (event) {
            event.preventDefault();

            var me = this,
                data = $(event.target).serialize();

            $.loadingIndicator.open({
                closeOnClick: false,
                renderElement: '.switch-variant--modal .content'
            });

            $.ajax({
                'data': data + '&detailId=' + me.opts.detailId,
                'method': 'GET',
                'url': me.opts.switchUrl,
                'success': function () {
                    if (me.opts.offCanvas) {
                        var plugin = $('*[data-collapse-cart="true"]').data('plugin_swCollapseCart');

                        plugin.loadCart(function () {
                            $.modal.close();
                            plugin.openMenu();

                            window.StateManager.addPlugin(
                                '*[data-off-canvas-variant-switch="true"]',
                                'froshOffCanvasVariantSwitch',
                                ['xs', 's', 'm', 'l', 'xl']
                            );
                        });

                        return;
                    }

                    if (window.location.href.includes("addArticle")) {
                        window.location.href = window.location.href.replace("addArticle", "cart");
                    } else {
                        window.location.reload();
                    }
                }
            });
        },

        setBuyboxQuantity: function ($buyboxForm) {
            var me = this,
                $sQuantity = $buyboxForm.find('#sQuantity');

            if ($sQuantity.length) {
                var $option = $sQuantity.find('option[value="' + me.opts.quantity + '"]');

                if ($option) {
                    $option.prop('selected', true);
                }
            }
        },

        removeDetailElements: function ($el) {
            $el.find('*[data-product-compare-add="true"]').parent('form').remove();
            $el.find('*[data-show-tab="true"]').remove();
        },

        onClose: function () {
            var me = this;

            me._isOpened = false;
            $.loadingIndicator.close();

            $.publish('plugin/froshVariantSwitch/onClose', [ me ]);
        },

        destroy: function () {
            var me = this;

            if (me._isOpened) {
                $.modal.close();
            }

            $.unsubscribe(me.getEventName('plugin/swModal/onClose'));

            me._destroy();
        }

    });

    window.StateManager.addPlugin(
        '*[data-variant-switch="true"]',
        'froshVariantSwitch',
        ['xs', 's', 'm', 'l', 'xl']
    );

    $.subscribe("plugin/swAjaxVariant/onBeforeRequestData", function() {
        var $el = $('.switch-variant--modal');

        if ($el.length) {
            $.loadingIndicator.close();
            $.loadingIndicator.open({
                closeOnClick: false,
                renderElement: '.switch-variant--modal .content'
            });
        }
    });

    $.subscribe("plugin/swAjaxVariant/onRequestData", function(e, me) {
        var $modal = $('.switch-variant--modal'),
            index = me.$el.data('index');

        if ($modal.length) {
            $.loadingIndicator.close();

            var $buyboxForm = $modal.find('*[data-add-article="true"]'),
                plugin = $($('*[data-variant-switch="true"]').get(index)).data('plugin_froshVariantSwitch');

            if (!$buyboxForm.length) {
                return;
            }

            window.StateManager.removePlugin('.switch-variant--modal *[data-add-article="true"]', 'swAddArticle');
            $buyboxForm.data('plugin_swAddArticle').destroy();

            me._on($buyboxForm, 'submit', $.proxy(
                plugin.onBuyboxSubmit,
                plugin
            ));

            plugin.removeDetailElements($modal);
            plugin.setBuyboxQuantity($buyboxForm);

            me.hasHistorySupport = false;
            setTimeout(function(){
                me.hasHistorySupport = true;
            }, 50);
        }
    });

})(jQuery, window);