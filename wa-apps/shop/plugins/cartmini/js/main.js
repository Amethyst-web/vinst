/**
 * Created by Nikita on 11.05.2016.
 */
$(document).on('cartUpdate', function(e, data){
    var container = $('.cart-mini');
    var products = container.find('.cart-item');
    if(products.length !== 0){
        products.remove();
    } else {
        container.find('li:first-child').remove();
    }
    $.each(data.items, function(){
        container.find('.dropdown-menu').prepend('<li class="cart-item"><a href="'+this.frontend_url+'"><span class="pull-left"><small>'+this.quantity+'x</small> '+this.sku_name+'</span>&nbsp;<small class="pull-right label label-info">'+this.full_price+'</small></a></li>');
    });
});