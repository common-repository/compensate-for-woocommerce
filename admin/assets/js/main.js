import 'select2/dist/css/select2.min.css'
import '../styles/main.scss'
import 'jquery';
import 'jquery-validation'
import 'select2'

jQuery(document).ready(function( $ ) {

    let currentTab = 0; // Current tab is set to be the first tab (0)
    let initialWizardFormValidation = true
    const wizardSetupForm = {

        init: function() {
            this.showTab(currentTab); // Display the current tab

            // Initial validation
            this.validateForm()

            $('.nextBtn').on('click', () => this.next(1))
            $('.backBtn').on('click', () => this.next(-1))
            $('.step').on('click', this.handleStepClick)
            $('#compensate-wizard-setup').on('change', 'input', wizardSetupForm.validateForm)
            $('#compensate-wizard-setup').on('change', 'select', wizardSetupForm.validateForm)
        },

        handleStepClick: function() {
            const index = +$(this).attr('data-index')

            // Do nothing when click on inactive step or current tab
            if (!$(this).hasClass('active') || index === currentTab) {
                return
            }

            // Validate form only when clicking on next tab
            initialWizardFormValidation = false
            if (index < currentTab || (index > currentTab && wizardSetupForm.validateForm())) {
                currentTab = index
                wizardSetupForm.showTab(index)
            }
        },

        showTab: function(n) {
            // This function will display the specified tab of the form
            const tabs = $('.tab')

            // hide all tabs
            tabs.hide()

            // show current tab
            tabs.eq(n).show()

            this.fixStepIndicator(n)

            // New tab initial validation
            initialWizardFormValidation = true
            this.validateForm()
        },

        next: function(n) {
            // This function will figure out which tab to display
            const tabs = $('.tab')
            initialWizardFormValidation = false

            // Exit the function if any field in the current tab is invalid:
            if (n == 1 && !this.validateForm()) return false

            // Increase or decrease the current tab by 1:
            currentTab = currentTab + n;

            // if you have reached the end of the form... :
            if (currentTab >= tabs.length) {
                $('#compensate-wizard-setup').submit()
                return false;
            }

            // Otherwise, display the correct tab:
            this.showTab(currentTab);
        },

        validateForm: function() {

            const form = $('#compensate-wizard-setup')

            if(!form.length) return

            form.validate({
                rules: {
                    is_store_info_agreed: 'required',
                    is_privacy_accepted: 'required',
                    store_country: 'required',
                    average_weight: 'required',
                    business_id: 'required',
                    vat_number: 'required',
                    company_name: 'required',
                    company_address: 'required',
                    company_city: 'required',
                    company_postcode: 'required',
                    company_country: 'required',
                },
                messages: {
                    is_store_info_agreed: compensate_i18n.store_info_agreed_required,
                    is_privacy_accepted: compensate_i18n.privacy_accepted_required,
                    store_country: compensate_i18n.store_country_required,
                    average_weight: compensate_i18n.average_weight_required,
                    business_id: compensate_i18n.business_id_required,
                    vat_number: compensate_i18n.vat_number_required,
                    company_name: compensate_i18n.company_name_required,
                    company_address: compensate_i18n.company_address_required,
                    company_city: compensate_i18n.company_city_required,
                    company_postcode: compensate_i18n.company_postcode_required,
                    company_country: compensate_i18n.company_country_required,
                },
                errorPlacement: function(error, element) {
                    error.appendTo(element.closest('.compensate-form-row').find('.error-container'));
                },
                showErrors: function() {

                    // Avoid showing erros when initial validation
                    if (!initialWizardFormValidation) {
                        this.defaultShowErrors();
                    }
                }
            });

            const formValid = form.valid()

            if (formValid) {
                $('.nextBtn').removeClass('compensate-button--disabled')
            } else {
                $('.nextBtn').addClass('compensate-button--disabled')
            }

            return formValid
        },

        fixStepIndicator: function(n) {
            $('.step').eq(n).addClass('active')

            if (n > 0) {
                $('.step-divier').eq(n-1).addClass('active')
            }
        }
    }

    const generalSettingsForm = {
        init: function() {
            $('button.submit').on('click', this.handleFormSubmit)
            $(document).on('click', this.closeNotificationOnClickOutSide)
            $('#general-settings-form').on('change', 'input', generalSettingsForm.validateForm)
            $('#general-settings-form').on('change', 'select', generalSettingsForm.validateForm)
        },

        closeNotificationOnClickOutSide:function(e) {

            // Hide when clicking outside
            if(!$(e.target).closest('.notification').length) {
                $('.notification').hide()
            }
        },

        validateForm: function() {
            const form = $('#general-settings-form')

            form.validate({
                rules: {
                    store_country: 'required',
                    average_weight: 'required',
                },
                messages: {
                    store_country: compensate_i18n.store_country_required,
                    average_weight: compensate_i18n.average_weight_required,
                },
                errorPlacement: function(error, element) {
                    error.appendTo(element.closest('.compensate-form-row').find('.error-container'));
                }
            });

            const formValid = form.valid()

            if (formValid) {
                $('button.submit').attr('disabled', false);
            } else {
                $('button.submit').attr('disabled', true);
            }

            return formValid
        },

        handleFormSubmit: function() {

            const form = $('#general-settings-form')

            if (generalSettingsForm.validateForm()) {
                form.submit()
            }
        }
    }

    const successModal = {

        init: function () {
            $('.success-modal__backdrop').on('click', this.closeModal)
            $('.success-modal__close-button').on('click', this.closeModal)
        },

        closeModal: function () {
            $('.success-modal').hide()
        }
    }

    // Init
    wizardSetupForm.init()
    generalSettingsForm.init()
    successModal.init()

    // Enhance select inputs
    $('.compensate_enhanced_select').select2();

    $('.compensate_enhanced_select.search-disabled').select2({
        minimumResultsForSearch: -1
    });
})