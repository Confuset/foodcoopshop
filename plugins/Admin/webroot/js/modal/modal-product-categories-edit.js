/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
foodcoopshop.ModalProductCategoriesEdit = {

    init : function() {
        
        var modalSelector = '#product-categories-edit-form';

        foodcoopshop.Modal.bindSuccessButton(modalSelector, function() {
            foodcoopshop.ModalProductCategoriesEdit.getSuccessHandler(modalSelector);
        });

        $('.product-categories-edit-button').on('click', function() {
            foodcoopshop.Modal.appendModalToDom(
                modalSelector,
                '',
                ''
            );
            foodcoopshop.ModalProductCategoriesEdit.getOpenHandler($(this), modalSelector);
        });

    },
    
    getSuccessHandler : function(modalSelector) {
        
        var productId = $(modalSelector + ' .product-id').val();
        var selectedCategories = [];
        $(modalSelector + ' .categories-checkboxes input:checked').each(function () {
            selectedCategories.push($(this).val());
        });

        foodcoopshop.Helper.ajaxCall(
            '/admin/products/editCategories/',
            {
                productId: productId,
                selectedCategories: selectedCategories
            },
            {
                onOk: function (data) {
                    document.location.reload();
                },
                onError: function (data) {
                    document.location.reload();
                    alert(data.msg);
                }
            }
        );        
    },

    syncWithElementsInHigherHierarchy: function(element) {
        if (element.text().match(/^-/)) {
            var prevLabel = element.closest('.checkbox').prev().find('label');
            this.syncWithElementsInHigherHierarchy(prevLabel);
        }
        element.find('input').prop('checked', true);
    },
    
    syncWithElementsInLowerHierarchy: function(element) {
        var nextLabel = element.closest('.checkbox').next().find('label');
        if (nextLabel.text().match(/^-/)) {
            this.syncWithElementsInLowerHierarchy(nextLabel);
            nextLabel.find('input').prop('checked', false);
        }
    },

    getOpenHandler : function(button, modalSelector) {
        
        var productId = button.data('objectId');
        var formHtml = $('.categories-checkboxes').clone();

        $(modalSelector).modal();
        
        $(modalSelector + ' .modal-body').append(formHtml);

        var productName = $('#product-' + productId + ' span.name-for-dialog').html();
        $(modalSelector + ' .modal-title').html(
            foodcoopshop.LocalizedJs.admin.ChangeCategories + ': ' + productName
        );

        $(modalSelector + ' .categories-checkboxes input[type="checkbox"]').on('click', function() {
            if ($(this).prop('checked')) {
                foodcoopshop.ModalProductCategoriesEdit.syncWithElementsInHigherHierarchy($(this).closest('label'), $(this).prop('checked'));
            } else {
                foodcoopshop.ModalProductCategoriesEdit.syncWithElementsInLowerHierarchy($(this).closest('label'), );
            }
        });

        // ids and for attribute needs to be unique - because of clone() it still exists...
        $(modalSelector + ' .categories-checkboxes label').each(function() {
            $(this).attr('for', $(this).attr('for') + '-' + productId);
            $(this).find('input').attr('id', $(this).find('input').attr('id') + '-' + productId);
        })

        var selectedCategories = $('#selected-categories-' + productId).val().split(',');
        $(modalSelector + ' .categories-checkboxes input[type="checkbox"]').each(function () {
            if ($.inArray($(this).val(), selectedCategories) != -1) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        });
        
        $(modalSelector + ' .product-id').val(productId);


    }

};