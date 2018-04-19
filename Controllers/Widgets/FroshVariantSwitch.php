<?php

class Shopware_Controllers_Widgets_FroshVariantSwitch extends \Enlight_Controller_Action
{
    public function variantSwitchFormAction()
    {
        $basketID = $this->Request()->get('basketId');
        $articleID = $this->Request()->get('articleId');
        $number = $this->Request()->get('number');
        $quantity = $this->Request()->get('quantity');
        $offCanvas = $this->Request()->get('offCanvas');

        $context = $this->get('shopware_storefront.context_service')->getShopContext();
        $product = $this->get('shopware_storefront.list_product_service')->get($number, $context);

        /** @var \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct $product */
        if (!$product || !$product->hasConfigurator()) {
            return;
        }

        $this->view->hasActiveVariants = true;

        $this->view->basketID = $basketID;
        $this->view->articleID = $articleID;
        $this->view->number = $number;
        $this->view->quantity = $quantity;
        $this->view->offCanvas = $offCanvas;
    }

    public function switchVariantAction()
    {
        $this->get('front')->Plugins()->ViewRenderer()->setNoRender();

        $number = $this->Request()->get('sAdd');
        $quantity = (int) $this->Request()->get('sQuantity');
        $basketID = (int) $this->Request()->get('detailId');

        if (!empty($number) && !empty($basketID)) {
            $this->get('frosh.variant_switch')->switchVariant(
                $number,
                $basketID,
                $this->get('modules')->Basket(),
                $quantity
            );
        }

        $this->Response()->setBody(json_encode(['success' => true]));
    }
}
