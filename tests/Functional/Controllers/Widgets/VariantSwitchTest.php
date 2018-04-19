<?php

class Shopware_Tests_Controllers_Widgets_VariantSwitchTest extends Enlight_Components_Test_Controller_TestCase
{
    const ARTICLE_ID = 202;
    const ARTICLE_NUMBER = 'SW10201.2';
    const ARTICLE_NUMBER_SWITCH = 'SW10201.3';
    const USER_AGENT = 'Mozilla/5.0 (Android; Tablet; rv:14.0) Gecko/14.0 Firefox/14.0';

    public function testVariantSwitchForm()
    {
        $sessionId = $this->addBasketArticle();

        $basketId = Shopware()->Db()->fetchOne(
            'SELECT id FROM s_order_basket WHERE sessionID = ?',
            [$sessionId]
        );

        $this->Request()
            ->setMethod('POST')
            ->setPost('articleId', self::ARTICLE_ID)
            ->setPost('number', self::ARTICLE_NUMBER)
            ->setPost('basketId', $basketId);

        $this->dispatch('/Widgets/FroshVariantSwitch/variantSwitchForm');

        $this->assertTrue($this->View()->getAssign('hasActiveVariants'));

        $this->reset();

        $this->Request()
            ->setMethod('POST')
            ->setPost('sQuantity', 2)
            ->setPost('sAdd', self::ARTICLE_NUMBER_SWITCH)
            ->setPost('detailId', $basketId);

        $this->dispatch('/Widgets/FroshVariantSwitch/switchVariant');

        $updatedBasketDetail = Shopware()->Db()->fetchOne(
            'SELECT id FROM s_order_basket WHERE ordernumber = ? AND id = ?',
            [self::ARTICLE_NUMBER_SWITCH, $basketId]
        );

        $this->assertTrue(!empty($updatedBasketDetail));
    }

    /**
     * Fires the add article request with the given user agent
     *
     * @param int $quantity
     *
     * @return string session id
     */
    private function addBasketArticle($quantity = 1)
    {
        $this->reset();
        $this->Request()->setMethod('POST');
        $this->Request()->setHeader('User-Agent', self::USER_AGENT);
        $this->Request()->setParam('sQuantity', $quantity);
        $this->Request()->setParam('sAdd', self::ARTICLE_NUMBER);
        $this->dispatch('/checkout/addArticle');

        return Shopware()->Container()->get('SessionID');
    }
}
