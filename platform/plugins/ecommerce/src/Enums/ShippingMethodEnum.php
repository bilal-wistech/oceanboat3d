<?php

namespace Botble\Ecommerce\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static ShippingMethodEnum DEFAULT()
 */
class ShippingMethodEnum extends Enum
{
    public const DEFAULT = 'default';
    public const NONE = '';
    public const DHL = '100';

    public static $langPath = 'plugins/ecommerce::shipping.methods';
}
