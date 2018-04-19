<?php

namespace FroshVariantSwitch\Services;

/**
 * Interface VariantSwitchInterface
 */
interface VariantSwitchInterface
{
    /**
     * @param string   $number
     * @param int      $basketID
     * @param \sBasket $sBasket
     * @param int      $quantity
     *
     * @return mixed
     */
    public function switchVariant(
        $number,
        $basketID,
        \sBasket $sBasket,
        $quantity
    );
}
