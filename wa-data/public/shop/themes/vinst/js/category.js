/**
 * Created by Nikita on 11.03.2016.
 */
$(document).on('filterProductsList', function(){
    var filters = {};
    var $brandInput = $('[name=brand]');
    var brandValue = $brandInput.val();
    if($brandInput.length !== 0 && brandValue !== undefined){
        filters.brand = brandValue;
    }
    var colorValues = [];
    $.each($('[name=color]:checked'), function(){
        colorValues.push($(this).val());
    });
    if(colorValues.length !== 0){
        filters.color = colorValues;
    }
    var category = $('[name=category]').val();
    location.href = '/category/' + (category.length !== 0 ? category+'/' : '') + (!jQuery.isEmptyObject(filters) ? '?' + $.param(filters): '');
});