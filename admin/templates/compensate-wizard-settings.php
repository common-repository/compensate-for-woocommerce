<?php
/**
 * Onboard settings template
 *
 **/
?>

<div class="wrap compensate-settings">
    <div class="wizard-setup">
        <div class="step-container">
            <span class="step" data-index="0"><?php esc_html_e('Getting started', 'compensate') ?></span>
            <span class="step-divier"></span>
            <span class="step" data-index="1"><?php esc_html_e('Widget Set Up', 'compensate') ?></span>
            <span class="step-divier"></span>
            <span class="step" data-index="2"><?php esc_html_e('Settings adjustment', 'compensate') ?></span>
        </div>
        <div class="form-container">
            <form method="POST" action="admin-post.php" id="compensate-wizard-setup" class="wizard-setup-form">

                <span class="wizard-setup-form__close-button">
                    <a href="<?php echo get_admin_url();?>">
                        <img src="<?php echo esc_url(COMPENSATE_IMG_ADMIN_URI . 'icon-close.svg') ?>">
                    </a>
                </span>

                <input type="hidden" name="action" value="compensate_wizard_save_options"/>
                <?php wp_nonce_field('compensate_wizard_settings_verify') ?>

                <div class="logo-container">
                    <img src="<?php echo esc_url(COMPENSATE_IMG_ADMIN_URI . 'logo-compensate.svg') ?>" alt="Compensate Logo">
                </div>

                <div class="tab tab-1">
                    <h1 class="wizard-setup-form__heading"><?php esc_html_e('Let’s combat climate change together!', 'compensate') ?></h1>
                    <div class="tab-1__content">
                        <p><?php esc_html_e('Thank you for being here, let’s get you started. The Compensate app will enable your customers to compensate for the climate impact of their shipments. We take no cuts from these payments and use them in full towards carbon capture.', 'compensate') ?></p>
                        <p class="tab-1__cta"><?php esc_html_e('Climate action is just a few clicks away!', 'compensate') ?></p>
                    </div>

                    <div class="compensate-notice">
                        <img class="compensate-notice__icon" src="<?php echo esc_url(COMPENSATE_IMG_ADMIN_URI . 'icon-exclamation-mark.svg') ?>">
                        <div class="compensate-notice__content">
                            <p class="compensate-notice__heading"><?php esc_html_e('Quick note!', 'compensate') ?></p>
                            <p><?php esc_html_e('We’ll automatically match the currency and language to the ones in your store. If we don’t yet support your language or currency, we’ll set the currency to EUR and the language to English.', 'compensate') ?></p>
                            <div class="compensate-notice__supported-languages">
                                <p><?php esc_html_e('Supported languages', 'compensate') ?></p>
                                <p class="compensate-notice__highlighted"><?php esc_html_e('English, Swedish, Danish, German, Dutch, Finnish', 'compensate') ?></p>
                            </div>
                            <div class="compensate-notice__supported-currencies">
                                <p><?php esc_html_e('Supported currencies', 'compensate') ?></p>
                                <p class="compensate-notice__highlighted"><?php esc_html_e('EUR, USD, SEK, DKK, GBP, CAD', 'compensate') ?></p>
                            </div>
                        </div>
                    </div>


                    <div class="buttons-container">
                        <?php compensate_button(array(
                            'text' => __('Continue', 'compensate'),
                            'class' => 'nextBtn compensate-button--disabled',
                            'icon' => COMPENSATE_IMG_ADMIN_URI . 'icon-arrow-right.svg'
                        )) ?>
                    </div>

                    <div class="tab-1__checkbox-container">
                        <?php
                        compensate_form_field('is_store_info_agreed', array(
                            'type' => 'checkbox',
                            'label' => __('To calculate the emissions, I allow Compensate to access my store information and activity data.', 'compensate'),
                            'required' => true,
                        ), $general_options['is_store_info_agreed'])
                        ?>

                        <?php
                            // TODO: update toc and privacy policy url
                            $toc_url = '';
                            $privacy_policy_url = '';

                            $toc_label = sprintf( __( 'I accept the <a href="https://www.compensate.com/woocommerce-terms-of-service" rel="noopener noreferrer" target="_blank">Terms and Conditions</a> and <a href="https://www.compensate.com/woocommerce-privacy-policy" rel="noopener noreferrer" target="_blank">Privacy Policy</a>', 'compensate' ),
                                $toc_url,
                                $privacy_policy_url);
                        ?>

                        <?php
                        compensate_form_field('is_privacy_accepted', array(
                            'type' => 'checkbox',
                            'label' => $toc_label,
                            'required' => true,
                        ), $general_options['is_privacy_accepted'])
                        ?>
                    </div>

                    <div class="contacts">
                        <p><?php esc_html_e('Questions or issues with the setup? We’re here to help:', 'compensate') ?></p>
                        <div class="contacts__contact-info">
                            <p>Email: support@compensate.com</p>
                            <p>Phone: +358401789667</p>
                        </div>
                    </div>
                </div>

                <div class="tab tab-2">
                    <div class="tab-2__content">
                        <p class="tab-2__leading-text"><?php esc_html_e('Great! First, please answer these questions. They’ll help us calculate the emissions of the shipments accurately.', 'compensate') ?></p>

                        <div class="inputs-section">
                            <h2 class="inputs-section__heading"><?php esc_html_e('Shipping Information', 'compensate') ?></h2>

                            <?php
                            compensate_form_field('store_country', array(
                                'type' => 'country',
                                'label' => __( 'From where do you ship the goods to your customer?', 'compensate'),
                                'placeholder' => __('Choose your warehouse location', 'compensate'),
                                'required' => true,
                            ), $general_options['store_country']);
                            ?>

                            <?php compensate_form_field('average_weight', array(
                                'label' => __( 'What is the average weight of the packages you ship?', 'compensate'),
                                'placeholder' => __('Add your shipping weight', 'compensate'),
                                'required' => true,
                                'type'  => 'weight',
                            ), array(
                                    'weight' => $general_options['average_weight'],
                                    'unit'  => $general_options['weight_unit']
                            )) ?>

                        </div>

                        <div class="inputs-section">
                            <h2 class="inputs-section__heading"><?php esc_html_e('Billing information', 'compensate') ?></h2>

                            <?php
                            compensate_form_field('business_id', array(
                                'type'  => 'text',
                                'label' => __( 'What is your Business ID?', 'compensate'),
                                'placeholder' => __('Add your Business ID', 'compensate'),
                                'required' => true,
                            ), $general_options['business_id']);
                            ?>

                            <?php
                            compensate_form_field('vat_number', array(
                                'type'  => 'text',
                                'label' => __( 'What is your VAT number?', 'compensate'),
                                'placeholder' => __('Add your VAT', 'compensate'),
                            ), $general_options['vat_number']);
                            ?>

                            <div class="billing-address-form-row">
                                <label>
                                    <?php esc_html_e('Billing address', 'compensate') ?>
                                    &nbsp;<abbr class="required" title="<?php echo esc_attr__( 'required', 'compensate' ) ?>">*</abbr>
                                </label>
                                <?php
                                compensate_form_field('company_name', array(
                                    'type'  => 'text',
                                    'label' => __('Company name', 'compensate'),
                                    'required' => true,
                                ), $general_options['company_name']);
                                ?>

                                <?php
                                compensate_form_field('company_country', array(
                                    'type' => 'country',
                                    'label' => __('Country of registration', 'compensate'),
                                    'placeholder' => __('Choose your country of registration', 'compensate'),
                                    'required' => true,
                                ),$general_options['company_country']);
                                ?>

                                <?php
                                compensate_form_field('company_address', array(
                                    'type'  => 'text',
                                    'label' => __('Address', 'compensate'),
                                    'required' => true,
                                ), $general_options['company_address']);
                                ?>

                                <?php
                                compensate_form_field('company_state', array(
                                    'type'  => 'text',
                                    'label' => __('State / County / Province', 'compensate'),
                                ), $general_options['company_state']);
                                ?>

                                <div class="city-postcode-group">
                                    <?php
                                    compensate_form_field('company_city', array(
                                        'type'  => 'text',
                                        'label' => __('Town / City', 'compensate'),
                                        'required' => true,
                                    ), $general_options['company_city']);
                                    ?>
                                    <?php
                                    compensate_form_field('company_postcode', array(
                                        'type'  => 'text',
                                        'label' => __('Postcode / ZIP', 'compensate'),
                                        'required' => true,
                                    ), $general_options['company_postcode']);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="buttons-container">
                            <?php compensate_button(array(
                                'text' => __('Back', 'compensate'),
                                'class' => 'backBtn compensate-button--icon-left compensate-button--outline',
                                'icon' => COMPENSATE_IMG_ADMIN_URI . 'icon-arrow-left.svg'
                            )) ?>
                            <?php compensate_button(array(
                                'text' => __('Continue', 'compensate'),
                                'class' => 'nextBtn compensate-button--disabled',
                                'icon' => COMPENSATE_IMG_ADMIN_URI . 'icon-arrow-right.svg'
                            )) ?>
                        </div>
                    </div>

                    <div class="contacts">
                        <p><?php _e('Questions or issues with the setup? We’re here to help:', 'compensate') ?></p>
                        <div class="contacts__contact-info">
                            <p>Email: support@compensate.com</p>
                            <p>Phone: +358401789667</p>
                        </div>
                    </div>

                    <p class="tab-2__readmore-text">
                        <?php
                        $read_more_url = 'https://www.compensate.com/faq-ecommerce';
                        echo sprintf( __( 'How do we calculate these emissions? Read more on the math <a href="%s" rel="noopener noreferrer" target="_blank">here</a>', 'compensate' ),
                            $read_more_url) ?>
                    </p>
                </div>

                <div class="tab tab-3">
                    <h2 class="wizard-setup-form__heading"><?php esc_html_e('Sustainability Statement', 'compensate') ?></h2>
                    <p class="tab-3__leading-text"><?php esc_html_e('We want to ensure that the Compensate brand and the act of compensating one’s carbon footprint is represented consistently across all applications and audiences. Therefore it’s important our partners commit to the following:', 'compensate'); ?></p>

                    <div class="tab-3__text-container">

                        <div class="statement-section">
                            <h3><?php esc_html_e('Sustainability', 'compensate') ?></h3>
                            <p><?php esc_html_e('Offering the Compensate plugin to our customers is not our only sustainability effort. We actively seek to lower our carbon footprint and make our operations more climate friendly in other ways as well. ', 'compensate') ?></p>
                        </div>

                        <div class="statement-section">
                            <h3><?php esc_html_e('Communications with integrity', 'compensate') ?></h3>
                            <p><?php esc_html_e('We communicate about the Compensate plugin to our customers in a manner which elevates both our brand and that of Compensate. We don’t moralize, nor do we exaggerate the positive impact of our customer’s actions or that of climate compensation in general. We ensure our customers fully understand the choice they are making with the Compensate plugin. In this, Compensate’s communications kit will help us.', 'compensate') ?></p>
                        </div>

                        <div class="statement-section">
                            <h3><?php esc_html_e('Digital materials', 'compensate') ?></h3>
                            <p><?php esc_html_e('In keeping with Compensate’s values, we minimize the production of new material as part of the Compensate experience (printouts, documents, material, kitsch). Within reason, we direct customers to online sources and digital materials for more information.', 'compensate') ?></p>
                        </div>
                    </div>

                    <div class="buttons-container">
                        <?php compensate_button(array(
                            'text' => __('Back', 'compensate'),
                            'class' => 'backBtn compensate-button--icon-left compensate-button--outline',
                            'icon' => COMPENSATE_IMG_ADMIN_URI . 'icon-arrow-left.svg'
                        )) ?>
                        <?php compensate_button(array(
                            'text' => __('Accept and Continue', 'compensate'),
                            'class' => 'nextBtn',
                            'icon' => COMPENSATE_IMG_ADMIN_URI . 'icon-arrow-right.svg'
                        )) ?>
                    </div>

                    <p class="tab-3__learnmore">
                        <?php
                        $compensate_link = "https://compensate.com/";

                        $learnmore = sprintf( wp_kses( __( 'Learn more about our mission at <a href="%s" target="_blank" rel="noopener noreferrer">compensate.com</a>', 'compensate' ),
                            array(
                                'a' => array(
                                    'href' => array(),
                                    'target' => array(),
                                    'rel' => array(),
                                )
                            )),
                            $compensate_link);

                         echo $learnmore;
                        ?>
                    </p>


                    <div class="contacts">
                        <p><?php esc_html_e('Questions or issues with the setup? We’re here to help:', 'compensate') ?></p>
                        <div class="contacts__contact-info">
                            <p>Email: support@compensate.com</p>
                            <p>Phone: +358401789667</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>