/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.4.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
foodcoopshop.ModalIOrderForDifferentCustomerAdd = {

    init : function(button) {

        $(button).on('click', function() {

            var modalSelector = '#order-for-different-customer-add';

            foodcoopshop.Modal.appendModalToDom(
                modalSelector,
                '',
                '',
                []
            );

            $(modalSelector).on('hidden.bs.modal', function (e) {
                foodcoopshop.ModalIOrderForDifferentCustomerAdd.getCloseHandler(modalSelector);
            });

            var iframeSrcInit = '/admin/order-details/initInstantOrder';
            var iframeSrc = '/admin/order-details/iframeInstantOrder';
            var isInstantOrder = true;

            if ($(this).closest('.add-button-wrapper').attr('id') == 'add-self-service-order-button-wrapper') {
                iframeSrcInit = '/admin/order-details/initSelfServiceOrder';
                iframeSrc = '/admin/order-details/iframeSelfServiceOrder';
                isInstantOrder = false;
            }

            foodcoopshop.ModalIOrderForDifferentCustomerAdd.getOpenHandler(modalSelector, iframeSrcInit, iframeSrc, isInstantOrder);

        });

    },

    getCloseHandler : function(modalSelector) {
        $(modalSelector).remove();
        foodcoopshop.Helper.ajaxCall(
            '/carts/ajaxDeleteOrderForDifferentCustomer',
            {},
            {
                onOk: function (data) {},
                onError: function (data) {}
            }
        );
    },

    onWindowResize: function(iframe) {
        var height = $(window).height() - 141;
        iframe.css('height', height);
    },

    getOpenHandler : function(modalSelector, iframeSrcInit, iframeSrc, isInstantOrder) {

        $(modalSelector).modal();

        // START DROPDOWN
        var customerDropdownId = 'customerDropdown';
        var header = $('<div class="message-container"><span class="start"><span class="title">' + foodcoopshop.LocalizedJs.admin.PlaceOrderFor + ': </span><select id="' + customerDropdownId + '"><option value="0">' + foodcoopshop.LocalizedJs.admin.PleaseSelect + '</option></select></span></div>');
        $(modalSelector + ' .modal-title').append(header);

        var customerDropdownSelector = '#' + customerDropdownId;

        $(customerDropdownSelector).selectpicker({
            liveSearch: true,
            size: 7,
            title: foodcoopshop.LocalizedJs.admin.PleaseSelectMember
        });

        // always preselect user if there is a dropdown called #customerId (for call from order detail)
        var customerId = $('#customerid').val();
        foodcoopshop.Admin.initCustomerDropdown(customerId, 0, 0, customerDropdownSelector, function () {
            var newSrc = foodcoopshop.Helper.cakeServerName + iframeSrcInit + '/' + $(customerDropdownSelector).val();
            $(modalSelector + ' iframe').attr('src', newSrc);
        });

        $(customerDropdownSelector).show();
        $(customerDropdownSelector).removeClass('hide');

        // START IFRAME
        var iframe = $('<iframe></iframe>');
        iframe.attr('src', foodcoopshop.Helper.cakeServerName + iframeSrc);
        iframe.css('width', '100%');
        iframe.css('border', 'none');
        $(modalSelector + ' .modal-body').append(iframe);

        $(window).on('resize', function () {
            foodcoopshop.ModalIOrderForDifferentCustomerAdd.onWindowResize(iframe);
        });
        foodcoopshop.ModalIOrderForDifferentCustomerAdd.onWindowResize(iframe);

        $(modalSelector + ' iframe').on('load', function () {
            // called after each url change in iframe
            var message = '';
            if (isInstantOrder) {
                var currentUrl = $(this).get(0).contentWindow.document.URL;
                var cartFinishedRegExp = new RegExp(foodcoopshop.LocalizedJs.admin.routeCartFinished);
                if (currentUrl.match(cartFinishedRegExp)) {
                    message = $(this).contents().find('#flashMessage').html().replace(/<(a|i)[^>]*>/g,'');
                }
            } else {
                if ($(this).contents().find('.btn-flash-message-continue').length == 1) {
                    message = foodcoopshop.LocalizedJs.admin.TheOrderWasPlacedSuccessfully;
                }
            }
            if (message != '') {
                document.location.href = foodcoopshop.Admin.addParameterToURL(
                    foodcoopshop.Admin.getParentLocation(),
                    'message=' + encodeURIComponent(message)
                );
            }
        });

        $(modalSelector).modal('handleUpdate');

    }

};