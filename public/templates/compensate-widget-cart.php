<?php
/**
 * Widget cart template
 *
 **/
?>
<div id="compensate-widget" class="compensate-widget">
    <div class="compensate-widget__main">
        <div class="compensate-widget__content">
            <img class="compensate-widget__logo" src="<?php echo esc_url(COMPENSATE_IMG_PUBLIC_URI . 'logo-compensate.png') ?>" alt="compensate-logo">
            <p class="compensate-widget__text">
                <?php esc_html_e('Yes, I want to compensate the climate impact of my shipment!', 'compensate') ?>
            </p>
        </div>
        <div class="compensate-widget__checkbox-container">

            <?php $checkbox_label = '<span class="compensate-widget__amount">' . esc_html($compensate_surcharge_value) .'</span>' ?>

            <?php compensate_form_field('compensate-checkbox', array(
                'type' => 'checkbox',
                'label' => $checkbox_label,
                'label_class' => 'reversed'
            ), $is_compensate_surcharged_added) ?>
        </div>
    </div>

    <div class="compensate-widget__readmore">
        <img src="<?php echo esc_url(COMPENSATE_IMG_PUBLIC_URI . 'icon-arrow-down.svg') ?>">
        <span><?php esc_html_e('What is this?', 'compensate') ?></span>
    </div>

    <div class="extra">
        <img class="extra__image" src="<?php echo esc_url(COMPENSATE_IMG_PUBLIC_URI . 'widget-extra.jpg') ?>">
        <div class="extra__content">
            <h2 class="extra__heading"><?php esc_html_e('Join us in climate action', 'compensate') ?></h2>
            <p class="extra__text">
                <?php
                $text= wp_kses( __( 'Compensate is a <strong>nonprofit on a mission to combat climate change.</strong>', 'compensate' ),
                    array('strong' => array()));

                echo $text; ?>
            </p>
            <p class="extra__learnmore">
                <?php
                $compensate_link = "https://www.compensate.com/ecommerce";

                $learnmore = sprintf( wp_kses( __( 'Get to know us: <a href="%s" target="_blank" rel="noopener noreferrer">compensate.com</a>', 'compensate' ),
                    array(
                        'a' => array(
                            'href' => array(),
                            'target' => array(),
                            'rel' => array(),
                        )
                    )),
                    esc_url($compensate_link));

                echo $learnmore;
                ?>
            </p>
        </div>
    </div>
</div>