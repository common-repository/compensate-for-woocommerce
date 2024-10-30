<?php


if ( ! function_exists( 'compensate_get_template' ) ) {
    /**
     * Get compensate template
     *
     * @param $template_name
     * @param array $args
     * @param string $tempate_path
     */
    function compensate_get_template($template_name, $args = array(), $tempate_path = '') {

        if ( is_array( $args ) && isset( $args ) ) :
            extract( $args );
        endif;

        $template_file = $tempate_path . '/' . $template_name;

        if ( ! file_exists( $template_file ) ) :
            _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
            return;
        endif;

        include $template_file;
    }
}


if( ! function_exists( 'compensate_form_field' ) ) {
    /**
     * Outputs compensate form field.
     *
     * @param string $key Key.
     * @param mixed  $args Arguments.
     * @param string $value (default: null).
     * @return string
     */
    function compensate_form_field( $key, $args, $value = null ) {
        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'autocomplete'      => false,
            'id'                => $key,
            'class'             => array(),
            'label_class'       => array(),
            'input_class'       => array(),
            'return'            => false,
            'options'           => array(),
            'custom_attributes' => array(),
            'validate'          => array(),
            'default'           => ''
        );

        $args = wp_parse_args( $args, $defaults );

        if ( $args['required'] ) {
            $args['class'][] = 'validate-required';
            $required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'compensate' ) . '">*</abbr>';
        } else {
            $required = '';
        }

        if ( is_string( $args['label_class'] ) ) {
            $args['label_class'] = array( $args['label_class'] );
        }

        if ( is_null( $value ) ) {
            $value = $args['default'];
        }

        // Custom attribute handling.
        $custom_attributes         = array();
        $args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

        if ( $args['maxlength'] ) {
            $args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
        }

        if ( ! empty( $args['autocomplete'] ) ) {
            $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
        }


        if ( $args['description'] ) {
            $args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
        }

        if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
            foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        if ( ! empty( $args['validate'] ) ) {
            foreach ( $args['validate'] as $validate ) {
                $args['class'][] = 'validate-' . $validate;
            }
        }

        $field           = '';
        $label_id        = $args['id'];
        $field_container = '<div class="compensate-form-row %1$s" id="%2$s">%3$s</div>';

        switch ( $args['type'] ) {

            case 'country':
                $countries = 'company_country' === $key ? compensate_get_supported_country() : WC()->countries->get_countries();

                if ( 1 === count( $countries ) ) {

                    $field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';

                    $field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" readonly="readonly" />';

                } else {

                    $field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select compensate_enhanced_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' style="width: 283px"><option value="">' . $args['placeholder'] . '</option>';

                    foreach ( $countries as $ckey => $cvalue ) {
                        $field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
                    }

                    $field .= '</select>';

                }

                break;

            case 'weight':

                $weight_units = array(
                    'kg'    => 'kg',
                    'lbs'    => 'lbs'
                );

                $field .= '<div class="compensate-weight-input">';
                $field .= '<input min="1" step="0.5" type="number" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value['weight'] ) . '" ' . implode( ' ', $custom_attributes ) . ' />';
                $field .= '<select name="weight_unit" id="weight_unit" class="compensate-weight-unit country_select compensate_enhanced_select search-disabled' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' style="width: 76px">';

                foreach ( $weight_units as $ckey => $cvalue ) {
                    $field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value['unit'], $ckey, false ) . '>' . $cvalue . '</option>';
                }

                $field .= '</select>';
                $field .= '</div>';
                break;

            case 'textarea':
                $field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

                break;
            case 'checkbox':
                $field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '>
						<input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' /> ' . $args['label'] . $required . '
						<span class="checkmark"></span>
						</label>';

                break;
            case 'text':
            case 'number':
                $field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

                break;

            case 'select':
                $field   = '';
                $options = '';

                if ( ! empty( $args['options'] ) ) {
                    foreach ( $args['options'] as $option_key => $option_text ) {
                        if ( '' === $option_key ) {
                            // If we have a blank option, select2 needs a placeholder.
                            if ( empty( $args['placeholder'] ) ) {
                                $args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'compensate' );
                            }
                            $custom_attributes[] = 'data-allow_clear="true"';
                        }
                        $options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) . '</option>';
                    }

                    $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
							' . $options . '
						</select>';
                }

                break;

            case 'radio':
                $label_id .= '_' . current( array_keys( $args['options'] ) );

                if ( ! empty( $args['options'] ) ) {
                    foreach ( $args['options'] as $option_key => $option_text ) {
                        $field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
                        $field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . $option_text . '</label>';
                    }
                }

                break;
        }

        if ( ! empty( $field ) ) {
            $field_html = '';

            if ( $args['label'] && 'checkbox' !== $args['type'] ) {
                $field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . $args['label'] . $required . '</label>';
            }

            $field_html .= '<span class="compensate-input-wrapper">' . $field;

            if ( $args['description'] ) {
                $field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
            }
            $field_html .= '</span>';
            $field_html .= '<div class="error-container"></div>';
            ;

            $container_class = esc_attr( implode( ' ', $args['class'] ) );
            $container_id    = esc_attr( $args['id'] ) . '_field';
            $field           = sprintf( $field_container, $container_class, $container_id, $field_html );
        }

        if ( $args['return'] ) {
            return $field;
        } else {
            echo $field; // WPCS: XSS ok.
        }
    }

}

if( ! function_exists('compensate_button')) {
    function compensate_button($args) {
        $defaults = array(
            'type'              => 'button',
            'text'              => '',
            'href'              => '',
            'icon'              => '',
            'disabled'          => false,
            'class'             => array(),
            'return'            => false
        );

        $args = wp_parse_args( $args, $defaults );

        if ( is_string( $args['class'] ) ) {
            $args['class'] = array( $args['class'] );
        }

        if ( $args['icon'] ) {
            $icon_html = '<span style="background-image: url(' . esc_attr($args['icon']) .')" class="compensate-button__icon"></span>';
        } else {
            $icon_html = '';
        }

        $text_html = '<span>' . $args['text'] . '</span>';
        $button_html = '';

        $disabled = $args['disabled'] ? 'disabled' : '';

        switch ( $args['type'] ) {

            case 'button':
            case 'submit':
                $button_html = '
                <button ' . $disabled . ' type="' . esc_attr($args['type']) . '" class="compensate-button ' . esc_attr( implode( ' ', $args['class'] ) ) . '">' . $text_html . $icon_html . '</button>';
                break;
            case 'link':
                $button_html = '
                <a href="' . esc_attr($args['href']) . '" class="compensate-button ' . esc_attr( implode( ' ', $args['class'] ) ) . '">' . $text_html . $icon_html . '</a>';
                break;
        }


        if ( $args['return'] ) {
            return $button_html;
        } else {
            echo $button_html; // WPCS: XSS ok.
        }

    }
}

if( ! function_exists( 'compensate_get_weight' ) ) {

    /**
     * Get weight for compensate calculation
     *
     * @return float|int
     */
    function compensate_get_weight() {
        $total_cart_weight = WC()->cart->get_cart_contents_weight();
        $current_weight_unit = get_option('woocommerce_weight_unit');

        // convert to grams
        $total_cart_weight_in_grams = wc_get_weight( $total_cart_weight, 'g', $current_weight_unit);

        $default_avg_weight = Compensate()->settings->get_average_packages_weight();
        $default_weight_unit = Compensate()->settings->get_weight_unit();

        $weight = $total_cart_weight_in_grams == 0 ?
            wc_get_weight($default_avg_weight, 'g', $default_weight_unit) :
            $total_cart_weight_in_grams;

        return $weight;
    }
}

if( ! function_exists( 'compensate_get_supported_country' ) ) {

    /**
     * Get compensate supported country list
     *
     * @return float|int
     */
    function compensate_get_supported_country() {
      $all_countries = WC()->countries->get_countries();
      $supported_country_keys = array(

          // EU
          'BE',
          'BG',
          'CZ',
          'DK' ,
          'DE',
          'EE',
          'IE',
          'GR',
          'ES',
          'FR',
          'HR',
          'IT',
          'CY',
          'LV',
          'LT',
          'LU',
          'HU',
          'MT',
          'NL',
          'AT',
          'PL',
          'PT',
          'RO',
          'SI',
          'SK',
          'FI',
          'SE',

          // Others
          'US',
          'GB',
          'NO'
      );

      $supported_countries = array_filter(
          $all_countries,
          function ($key) use ($supported_country_keys) {
              return in_array($key, $supported_country_keys);},
            ARRAY_FILTER_USE_KEY
      );
      
      return $supported_countries;
    }
}