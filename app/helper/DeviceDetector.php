<?php
namespace HelloWP\HWMyMenu\App\Helper;

use Detection\MobileDetect;

class DeviceDetector {

    /**
     * MobileDetect instance.
     *
     * @var MobileDetect
     */
    private $mobileDetect;

    /**
     * Initialize the DeviceDetector.
     */
    public function __construct() {
        $this->mobileDetect = new MobileDetect();
    }

    /**
     * Check if the current device is mobile.
     *
     * @return bool
     */
    public function isMobile(): bool {
        return $this->mobileDetect->isMobile();
    }

    /**
     * Check if the current device is a tablet.
     *
     * @return bool
     */
    public function isTablet(): bool {
        return $this->mobileDetect->isTablet();
    }

    /**
     * Check if the current device is desktop.
     *
     * @return bool
     */
    public function isDesktop(): bool {
        return !$this->mobileDetect->isMobile() && !$this->mobileDetect->isTablet();
    }
}
