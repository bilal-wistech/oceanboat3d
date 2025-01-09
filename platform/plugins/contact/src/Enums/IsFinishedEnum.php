<?php

namespace Botble\Contact\Enums;

use Botble\Base\Supports\Enum;
use Html;
use Illuminate\Support\HtmlString;

/**
 * @method static IsFinishedEnum NO()
 * @method static IsFinishedEnum YES()
 */
class IsFinishedEnum extends Enum
{
    public const YES = '1';
    public const NO = '0';

    public static $langPath = 'plugins/contact::contact.statuses';

    public function label(): string
    {
        return match ($this->value) {
            self::NO => __('No'),
            self::YES => __('Yes'),
            default => parent::label(),
        };
    }

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::NO => Html::tag('span', self::NO()->label(), ['class' => 'label-warning status-label'])->toHtml(),
            self::YES => Html::tag('span', self::YES()->label(), ['class' => 'label-success status-label'])->toHtml(),
            default => parent::toHtml(),
        };
    }
}
