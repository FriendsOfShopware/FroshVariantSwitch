{extends file='parent:frontend/checkout/ajax_cart_item.tpl'}

{block name='frontend_checkout_ajax_cart_articlename' append}
    {block name='frontend_checkout_ajax_cart_item_frosh_switch_variant'}
        {if $sBasketItem.modus == 0}
            <div data-off-canvas-variant-switch="true"
                 data-url="{url module="widgets" controller="FroshVariantSwitch" action="variantSwitchForm"}"
                 data-basketId="{$sBasketItem.id}"
                 data-articleId="{$sBasketItem.articleID}"
                 data-number="{$sBasketItem.ordernumber}"
                 data-quantity="{$sBasketItem.quantity}"
                 data-offCanvas="true"></div>
        {/if}
    {/block}
{/block}