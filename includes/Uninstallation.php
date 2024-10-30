<?php
namespace CompensateInc;

use CompensateAdmin\AdminApiHelper;

class Uninstallation {

    public static function uninstall() {
        // Cleanup wordpress option table
        CompensateInit::instance()->settings->cleanup();

        // Cleanup compensate db
        AdminApiHelper::cleanup_profile();
    }
}