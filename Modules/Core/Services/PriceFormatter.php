<?php

namespace Modules\Core\Services;

use Modules\Core\Facades\SiteConfig;

class PriceFormatter {

    public function get(int $price, int $channel_id, ?string $scope = null)
    {
        $scope =  $scope ?? "channel";
        $currency = SiteConfig::fetch("channel_currency", $scope, $channel_id);
        $currency_position = SiteConfig::fetch("symbol_position", $scope, $channel_id);
        $group_separator = SiteConfig::fetch("group_seperator", $scope, $channel_id);
        $decimal_separator = SiteConfig::fetch("decimal_seperator", $scope, $channel_id);

        $group_separator_value = $this->group_separator($group_separator);
        $decimal_separator_value = $this->decimal_separator($decimal_separator);
        $separator_price = number_format($price, 2, $decimal_separator_value, $group_separator_value);

        if($price < 0) {
            $minus = SiteConfig::fetch("minus_sign", $scope, $channel_id); 
            $minus_position = SiteConfig::fetch("minus_sign_position", $scope, $channel_id); 
        }
        
        $symbol_price = $this->symbolPosition($currency, $separator_price, $currency_position);

    }

    public function symbolPosition($symbol, $price, $currency_position)
    {
        switch($currency_position)
        {
            case 1:
                return "{$symbol}{$price}";
                break;

            case 2:
                return "{$symbol} {$price}";
                break;

            case 3:
                return "{$price}{$symbol}";
                break;

            case 4:
                return "{$price} {$symbol} ";
                break;

            default:
                return null;
                break;
        }
    }

    public function group_separator($group_separator)
    {
        switch($group_separator)
        {
            case 1:
                return ",";
                break;

            case 2:
                return ".";
                break;

            case 3:
                return " ";
                break;

            case 4:
                return null;
                break;
        }
    }

    public function decimal_separator($decimal_separator)
    {
        switch($decimal_separator)
        {
            case 1:
                return ",";
                break;

            case 2:
                return ".";
                break;

            default:
                return null;
                break;
        }
    }

    public function minusSignPosition($minus, $minus_position)
    {
        switch($minus_position)
        {
            case 1:
                return "{$minus_position}{$minus}";
                break;

            case 2:
                return "{$minus_position} {$minus}";
                break;

            case 3:
                return "{$minus}{$minus_position}";
                break;

            case 4:
                return "{$minus} {$minus_position} ";
                break;

            default:
                return null;
                break;
        }
    }
}