{if $hasActiveVariants}
    <form href="{url controller="detail" sArticle=$articleID number=$number}"
          class="content--variant-switch-form block"
          data-variant-switch="true"
          data-switchUrl="{url module="widgets" controller="FroshVariantSwitch" action="switchVariant"}"
          data-detailId="{$basketID}"
          data-productUrl="{url controller="detail" sArticle=$articleID}"
          data-productQuery="?number={$number}&template=ajax"
          data-quantity="{$quantity}"
          data-offCanvas="{$offCanvas}">
        <button class="btn is--small is--icon-right{if $offCanvas} right{/if}" type="submit" name="Submit" value="submit">
            {s name="ChangeVariant" namespace="frontend/plugins/frosh/variantswitch"}Variante wechseln{/s} <i class="icon--shuffle"></i>
        </button>
    </form>
{/if}