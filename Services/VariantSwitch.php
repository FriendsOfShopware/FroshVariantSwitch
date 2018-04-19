<?php

namespace FroshVariantSwitch\Services;

use Shopware\Bundle\StoreFrontBundle\Service\AdditionalTextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Components\Model\ModelManager;

/**
 * Class VariantSwitch
 */
class VariantSwitch implements VariantSwitchInterface
{
    /** @var ModelManager */
    private $models;

    /** @var \Enlight_Components_Session_Namespace */
    private $session;

    /** @var ContextServiceInterface */
    private $context;

    /** @var ListProductServiceInterface */
    private $listProductService;

    /** @var AdditionalTextServiceInterface */
    private $additionalTextService;

    /**
     * VariantSwitch constructor.
     *
     * @param ModelManager                          $models
     * @param \Enlight_Components_Session_Namespace $session
     * @param ContextServiceInterface               $contextService
     * @param ListProductServiceInterface           $listProductService
     * @param AdditionalTextServiceInterface        $additionalTextService
     */
    public function __construct(
        ModelManager $models,
        \Enlight_Components_Session_Namespace $session,
        ContextServiceInterface $contextService,
        ListProductServiceInterface $listProductService,
        AdditionalTextServiceInterface $additionalTextService
    ) {
        $this->models = $models;
        $this->session = $session;
        $this->context = $contextService;
        $this->listProductService = $listProductService;
        $this->additionalTextService = $additionalTextService;
    }

    /**
     * {@inheritdoc}
     */
    public function switchVariant(
        $number,
        $basketID,
        \sBasket $sBasket,
        $quantity = 1
    ) {
        /** @var \Shopware\Models\Order\Basket $basket */
        $basket = $this->models->getRepository('Shopware\Models\Order\Basket')->find($basketID);

        if (!$basket || $basket->getSessionId() !== $this->session->get('sessionId')) {
            return;
        }

        $basket->setOrderNumber($number);

        $context = $this->context->getProductContext();
        $product = $this->listProductService->get($number, $context);
        /** @var \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct $product */
        $product = $this->additionalTextService->buildAdditionalText($product, $context);

        $basket->setEsdArticle($product->getEsd() ? 1 : 0);
        $basket->setArticleName($product->getName() . ' ' . $product->getAdditional());

        $this->models->persist($basket);
        $this->models->flush();

        $sBasket->sUpdateArticle($basketID, $quantity);
    }
}
