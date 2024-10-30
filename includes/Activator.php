<?php

namespace CompensateInc;

use CompensateAdmin\AdminApiHelper;
use CompensateAdmin\OptionsHelper;

class Activator {

    public static function activate() {

        // Default settings
        OptionsHelper::initHelperData();
        Compensate()->settings->initGeneralSettings();

        // Register merchant
        $shop_name = get_bloginfo('name');
        $shop_email = get_bloginfo('admin_email');
        $shop_url = site_url();

        $auth_options = OptionsHelper::get('auth');

        if (isset($auth_options['shopId'])) {
            $shopId = $auth_options['shopId'];
        } else {

            // Create new uniqId and save it to db
            $shopId = wp_generate_uuid4();

            OptionsHelper::update(
                'auth',
                array(
                    'shopId' => $shopId,
                )
            );
        }


        AdminApiHelper::register_merchant(array(
            'shop_name'     => $shop_name,
            'shop_email'    => $shop_email,
            'shop_id'       => $shopId,
            'shop_url'      => $shop_url
        ));
    }
}