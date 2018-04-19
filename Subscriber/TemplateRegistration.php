<?php

namespace FroshVariantSwitch\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;

/**
 * Class TemplateRegistration
 */
class TemplateRegistration implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @var \Enlight_Template_Manager
     */
    private $templateManager;

    /**
     * @var \Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * TemplateRegistration constructor.
     *
     * @param $pluginDirectory
     * @param \Enlight_Template_Manager            $templateManager
     * @param \Shopware_Components_Snippet_Manager $snippetManager
     */
    public function __construct(
        $pluginDirectory,
        \Enlight_Template_Manager $templateManager,
        \Shopware_Components_Snippet_Manager $snippetManager
    ) {
        $this->pluginDirectory = $pluginDirectory;
        $this->templateManager = $templateManager;
        $this->snippetManager = $snippetManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'addJsFiles',
        ];
    }

    public function onPreDispatch()
    {
        $this->templateManager->addTemplateDir($this->pluginDirectory . '/Resources/views');
        $this->snippetManager->addConfigDir($this->pluginDirectory . '/Resources/snippets/');
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function addJsFiles()
    {
        $jsFiles = [
            $this->pluginDirectory . '/Resources/views/frontend/_public/src/js/jquery.off-canvas-variant-switch.js',
            $this->pluginDirectory . '/Resources/views/frontend/_public/src/js/jquery.variant-switch.js',
        ];

        return new ArrayCollection($jsFiles);
    }
}
