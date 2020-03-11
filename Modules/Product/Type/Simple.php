<?php

namespace Modules\Product\Type;

/**
 * Class Simple.
 *
 * @author  Hemant Achhami
 * @copyright 2020 Hazesoft co
 */
class Simple extends AbstractType
{
    /**
     * Skip attribute for simple product type
     *
     * @var array
     */
    protected $skipAttributes = [];



    /**
     * Show quantity box
     *
     * @var boolean
     */
    protected $showQuantityBox = true;

    /**
     * Return true if this product type is saleable
     *
     * @return boolean
     */
    public function isSaleable()
    {
        if (! $this->product->status)
            return false;

        if ($this->haveSufficientQuantity(1))
            return true;

        return false;
    }

    /**
     * @param integer $qty
     *
     * @return boolean
     */
    public function haveSufficientQuantity($qty)
    {
       // $backorders = core()->getConfigData('catalog.inventory.stock_options.backorders');
       // return $qty <= $this->totalQuantity() ? true : $backorders;
    }

}