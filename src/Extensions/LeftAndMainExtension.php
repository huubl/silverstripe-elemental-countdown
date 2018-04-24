<?php

namespace Dynamic\Elements\CountDown\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Class LeftAndMainExtension.
 */
class LeftAndMainExtension extends Extension
{
    public function init()
    {
        Requirements::css('dynamic/silverstripe-elemental-countdown-block: icons/icons.css');
    }
}
