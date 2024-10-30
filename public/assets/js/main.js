import '../styles/main.scss'

jQuery(document).ready(function( $ ) {

    /**
     * Check if a node is blocked for processing.
     *
     * @param {JQuery Object} $node
     * @return {bool} True if the DOM Element is UI Blocked, false if not.
     */
    const is_blocked = function( $node ) {

        if (!$node) {
            return
        }

        return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
    };

    /**
     * Block a node visually for processing.
     *
     * @param {JQuery Object} $node
     */
    const block = function( $node ) {

        if (!$node) {
            return
        }

        if ( ! is_blocked( $node ) ) {
            $node.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
        }
    };

    /**
     * Unblock a node after processing is complete.
     *
     * @param {JQuery Object} $node
     */
    const unblock = function( $node ) {

        if (!$node) {
            return
        }

        $node.removeClass( 'processing' ).unblock();
    };

    const compensate = {

        ipLocation: null,

        init: function() {
            this.injectPlausible();
            $('#compensate-widget input[type="checkbox"]').change(this.handleWidgetCheckbox)
            $('#compensate-widget .compensate-widget__readmore').on('click', this.handleReadMoreClick)
            $(document).on('click', this.closeWidgetExtraOnClickOutSide)

            // Update compensate value when cart changed or shipping address changed
            $( document.body ).on( 'updated_cart_totals', this.update_compensate_surcharge_value)
            $('form.checkout').on( 'change', '.address-field select', this.update_compensate_surcharge_value);

            // Initial surcharge calculation

            $.ajax({
                url: compensate_obj.ipinfo_url,
                type : 'get',
                success: function( response ) {
                    compensate.ipLocation = response.country;
                },
                complete: function() {
                    compensate.update_compensate_surcharge_value()
                }
            })
        },
        injectPlausible(){
            jQuery('<script async defer data-domain="compensate.com" src="https://plausible.io/js/plausible.js"></script>').appendTo('head');
        },

        /**
         * Close widget extra when clicking outside
         */
        closeWidgetExtraOnClickOutSide: function(e) {

            // Close widget extra when clicking outside
            if(!$(e.target).closest('#compensate-widget .compensate-widget__readmore').length &&
               !$(e.target).closest('#compensate-widget .extra').length) {
                $('#compensate-widget .extra').hide()
            }
        },

        /**
         * Handle when a widget checkbox is clicked
         */
        handleReadMoreClick: function() {

            const extra = $('#compensate-widget .extra')

            if(extra.css('display') == 'none') {
                extra.css('display', 'flex')
            } else {
                extra.css('display', 'none')
            }
        },

        /**
         * Handle when a widget checkbox is clicked
         */
        handleWidgetCheckbox: function() {

            $.ajax({
                url: compensate_obj.ajax_url,
                type : 'post',
                data : {
                    action : 'toggle_compensate_surcharge',
                    is_compensate_added: this.checked
                },
                success: function() {

                    setTimeout(function() {
                        // Update checkout
                        $('body').trigger('update_checkout');

                        // Update cart
                        jQuery("[name='update_cart']").removeAttr("disabled").trigger("click");
                        jQuery("[name='update_cart']").trigger("click");
                    },200);
                }
            })
        },


        /**
         * Handle when cart total is updated (items added, removed, etc)
         */
        update_compensate_surcharge_value: function(evt) {

            const $form = evt && $(evt.currentTarget);

            block( $form );

            let country			 = $( '#billing_country' ).val()
            let s_country        = country

            if ( $( '#ship-to-different-address' ).find( 'input' ).is( ':checked' ) ) {
                s_country		 = $( '#shipping_country' ).val();
            }

            $('.compensate-widget__amount').html('<div class="compensate-widget__spinner"></div>')

            $.ajax({
                url: compensate_obj.ajax_url,
                type : 'post',
                data : {
                    action : 'update_compensate_surcharge_value',
                    shipping_country: s_country,
                    ipLocation: compensate.ipLocation
                },
                success: function( response ) {

                    // Update checkout
                    $('body').trigger('update_checkout');

                    // Update cart
                    $('.cart_totals' ).replaceWith(response.updated_cart_total);
                    $('.compensate-widget__amount').html( response.compensate_surcharge_value)
                    if(!response.compensate_amount){
                        console.log('Got error in price response');
                        $('#compensate-widget').hide();
                    }
                },
                complete: function() {
                    unblock( $form );
                }
            })
        }
    }

    if($("#compensate-widget").length) {
        compensate.init();
    }
})
