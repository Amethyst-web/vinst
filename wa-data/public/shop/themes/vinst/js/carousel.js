/**
 * Created by Nikita on 24.05.2016.
 */
$(document).ready(function(){
    var itemHeight = 87.5,
        container = $('.carousel'),
        visibleCount = container.data('visible'),
        block = false;

    container.find('.carousel-inner').css('height', visibleCount*itemHeight+'px');
    container.find('.slider-up').click(function(){
        if(block){
            return false;
        }
        block = true;
        var firstItem = container.find('.item.active:first-child()');
        if(firstItem.length === 0){
            block = false;
            return false;
        }
        var position = parseFloat(firstItem.css('margin-top').replace('px', ''));
        if(position == -(container.find('.item.active').length - visibleCount)*itemHeight){
            block = false;
            return false;
        }
        firstItem.animate({
            marginTop: position - itemHeight
        }, function(){
            block = false;
        });
    });
    container.find('.slider-down').click(function(){
        if(block){
            return false;
        }
        block = true;
        var firstItem = container.find('.item.active:first-child()');
        if(firstItem.length === 0){
            block = false;
            return false;
        }
        var position = parseFloat(firstItem.css('margin-top').replace('px', ''));
        if(position == 0){
            block = false;
            return false;
        }
        firstItem.animate({
            marginTop: position + itemHeight
        }, function(){
            block = false;
        });
    });
});