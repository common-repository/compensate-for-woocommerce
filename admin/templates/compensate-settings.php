<?php
/**
 * General settings template
 *
 * Display settings view
 **/
?>

<div class="wrap compensate-settings">
    <div class="header">
        <img src="<?php echo esc_url(COMPENSATE_IMG_ADMIN_URI . 'logo-compensate.svg') ?>" alt="Compensate Logo">
        <h1><?php esc_html_e('Let\'s combat climate change, together!', 'compensate') ?></h1>
    </div>

    <div class="content">

        <?php if(isset($_GET['status']) && $_GET['status'] == 1): ?>
        <div class="notification notification--success">
            <span class="notification__icon"></span>
            <div class="notification__content">
                <p class="notification__text"><?php esc_html_e('Your changes have been saved', 'compensate') ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['status']) && $_GET['status'] == 0): ?>
        <div class="notification notification--error">
            <span class="notification__icon"></span>
            <div class="notification__content">
                <p class="notification__text"><?php esc_html_e('There was a connectivity error. We couldn’t save your changes.', 'compensate') ?></p>
                <span class="notification__helper"><?php esc_html_e('Refresh and try again. If the error persist, please contact us at support@compensate.com', 'compensate') ?></span>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" id="general-settings-form" class="general-settings-form" action="admin-post.php">
            <input type="hidden" name="action" value="compensate_settings_save_options"/>
            <?php wp_nonce_field('compensate_settings_verify') ?>

            <section class="settings-section sustainability-statement-section">
                <h2 class="settings-section__heading"><?php esc_html_e('Sustainability Statement', 'compensate') ?></h2>
                <p class="sustainability-statement-section__leading-text"><?php esc_html_e('We want to ensure that the Compensate brand and the act of compensating one’s carbon footprint is represented consistently across all applications and audiences. Therefore it’s important our partners commit to the following:', 'compensate') ?></p>
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
            </section>

            <section class="settings-section opt-in-section">
                <h2 class="settings-section__heading"><?php esc_html_e('Compensate default', 'compensate') ?></h2>
                <p class="settings-section__lead-text"><?php esc_html_e('How your customers interact with Compensate widget. You can decide wether you want them to opt-in to the compensation or having it activated by default.', 'compensate') ?></p>
                <div class="settings-section__content">
                   <input type="radio" value="off" id="off" name="compensation_opt_out" <?php checked($general_options['compensation_opt_out'], false)?>>
                   <label for="off"><?php esc_html_e('Compensation default off. (Opt-in)', 'compensate') ?></label>

                   <input type="radio" value="on" id="on" name="compensation_opt_out" <?php checked($general_options['compensation_opt_out'])?>>
                   <label for="on"><?php esc_html_e('Compensation default on. (Opt-out)', 'compensate') ?></label>
               </div>
            </section>

            <section class="settings-section">
                <h2 class="settings-section__heading"><?php esc_html_e('Carbon Emission Calculation', 'compensate') ?></h2>
                <p class="settings-section__lead-text"><?php esc_html_e('Information needed to accurately calculate the carbon emission of the shipping goods.', 'compensate') ?>.</p>
                <div class="settings-section__content">

                    <?php
                    compensate_form_field('store_country', array(
                        'type' => 'country',
                        'label' => __( 'From where do you ship the goods to your customer?', 'compensate'),
                    ), $general_options['store_country']);
                    ?>

                    <?php compensate_form_field('average_weight', array(
                        'label' => __( 'What is the average weight of the packages you ship?', 'compensate'),
                        'placeholder' => __('Add your shipping weight', 'compensate'),
                        'type'  => 'weight',
                    ), array(
                        'weight' => $general_options['average_weight'],
                        'unit'  => $general_options['weight_unit']
                    )) ?>

                    <?php compensate_button(array(
                        'type' => 'button',
                        'text' => __('Save', 'compensate'),
                        'class' => 'submit',
                        'disabled' => true
                    )) ?>
                </div>

            </section>

            <section class="contact">
                <h3 class="contact__heading"><?php esc_html_e('Questions or issues with the setup? We’re here to help:', 'compensate') ?></h3>
                <p>Email: <span>support@compensate.com</span></p>
                <p>Phone: <span>+358401789667</span></p>
            </section>
        </form>

    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 1 && $show_wizard_settings_success): ?>
        <?php \CompensateAdmin\OptionsHelper::update('show_wizard_settings_success', 0); ?>

        <div class="success-modal">
            <div class="success-modal__backdrop"></div>
            <div class="success-modal__content">
                <span class="success-modal__close-button">
                   <img src="<?php echo esc_url(COMPENSATE_IMG_ADMIN_URI . 'icon-close.svg') ?>">
                </span>
                <img class="success-modal__banner" src="<?php echo esc_url(COMPENSATE_IMG_ADMIN_URI . 'banner-modal-success.jpg') ?>">
                <h2 class="success-modal__heading"><?php esc_html_e('You’re all set!', 'compensate') ?></h2>
                <p><?php esc_html_e('We’re excited to welcome you to the #WeCompensate-family! We can’t wait to let you know how your community’s climate impact is growing.', 'compensate') ?></p>
                <p><?php esc_html_e('You can come back here any time to adjust your settings or to update your information.', 'compensate') ?></p>
                <div class="success-modal__contact">
                    <div class="text-container">
                        <p><?php esc_html_e('We’d love to hear from you: Please get in touch if you have any questions or need help, or if you have any feedback for us!', 'compensate') ?></p>
                    </div>
                    <div class="contact-info">
                        <p>Email: <span>support@compensate.com</span></p>
                        <p>Phone: <span>+358401789667</span></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

