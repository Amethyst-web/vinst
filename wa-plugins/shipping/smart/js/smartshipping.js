/*
 SMART SHIPPING PLUGIN FOR SHOP-SCRIPT 5, 6
    v. 1.2

 (c) fullpro.ru
 Created on 30.06.14.
 */

/*
 * jQuery.bind-first library v0.2.3
 * Copyright (c) 2013 Vladimir Zhuravlev
 *
 * Released under MIT License
 * @license
 *
 * Date: Thu Feb  6 10:13:59 ICT 2014
 **/
(function(t){function e(e){return u?e.data("events"):t._data(e[0]).events}function n(t,n,r){var i=e(t),a=i[n];if(!u){var s=r?a.splice(a.delegateCount-1,1)[0]:a.pop();return a.splice(r?0:a.delegateCount||0,0,s),void 0}r?i.live.unshift(i.live.pop()):a.unshift(a.pop())}function r(e,r,i){var a=r.split(/\s+/);e.each(function(){for(var e=0;a.length>e;++e){var r=t.trim(a[e]).match(/[^\.]+/i)[0];n(t(this),r,i)}})}function i(e){t.fn[e+"First"]=function(){var n=t.makeArray(arguments),i=n.shift();return i&&(t.fn[e].apply(this,arguments),r(this,i)),this}}var a=t.fn.jquery.split("."),s=parseInt(a[0]),f=parseInt(a[1]),u=1>s||1==s&&7>f;i("bind"),i("one"),t.fn.delegateFirst=function(){var e=t.makeArray(arguments),n=e[1];return n&&(e.splice(0,2),t.fn.delegate.apply(this,arguments),r(this,n,!0)),this},t.fn.liveFirst=function(){var e=t.makeArray(arguments);return e.unshift(this.selector),t.fn.delegateFirst.apply(t(document),e),this},u||(t.fn.onFirst=function(e,n){var i=t(this),a="string"==typeof n;if(t.fn.on.apply(i,arguments),"object"==typeof e)for(type in e)e.hasOwnProperty(type)&&r(i,type,a);else"string"==typeof e&&r(i,e,a);return i})})(jQuery);

var
    ssMap,//карта
    polygonObjects  = [],
    distanceInput   = $("#smart-shipping-" + smart_shipping_id + "-distance"),
    defaultVal      = "_",
    dataInput       =  $("#smart-shipping-" + smart_shipping_id + "-data")


$(function(){

    $('.checkout-step.step-shipping').on('click', '.shipping-calc-link', function(e){
        e.preventDefault();
        $('[name^="customer_' + smart_shipping_id + '[address.shipping]"]').first().trigger('change');
    });


    $('[name^="customer_' + smart_shipping_id + '[address.shipping]"]').bindFirst('change', function(e){
        var input = $(this);

        if (input.data('recursion')) {
            input.data('recursion', '');
            return false;
        }
        input.data('ignore', 1);// предотвратить вызов другой функции раньше чем закончит прокладываться маршрут
        e.stopImmediatePropagation();

        var calcCallback = function(){// проложить маршрут
            input.data('ignore', '')// снять флаг предотвращения
            input.data('no-route', '');
            input.data('recursion', 1);// спастись от рекурсии

            input.change();// запустить другую
        };

        if (input.data('no-route')) {
            calcCallback();
        } else {
            var route_status = route(getAddressString(), calcCallback);
            if (route_status) {
                $(".shipping-" + smart_shipping_id + " .price").html('<i class="icon16 loading"></i>');
                $(".shipping-" + smart_shipping_id + " .est_delivery").html('');
            } else {
                calcCallback();
            }
        }
    });


    $("input[name=shipping_id]").on('change', function(){
        var radio = $(this);
        if (radio.val() == smart_shipping_id) {

        }
    });
});

if (typeof smartshippingCallbacks == 'undefined') {
    var smartshippingCallbacks = {};
}

function init() {

    $('li.shipping-'+smart_shipping_id+ ' .wa-form').css({position: 'absolute', 'left': '-9999px'});

    ssMap = new ymaps.Map("smart-shipping-map", mapSettings);


    for (var i in zones) {
        if (typeof zones[i].geometry.coordinates != "undefined" && zones[i].geometry.coordinates.length > 0) {
            var polygon = new ymaps.GeoObject({geometry: zones[i].geometry}, zones[i].options);
            polygon.options.setParent(ssMap.options);
            ssMap.geoObjects.add(polygon);
            polygonObjects.push(polygon);
        } else {
            //console.log('Polygon '+i+' skipped.');
        }
    }

    route(
        getAddressString(),
        function(){
            $('li.shipping-'+smart_shipping_id+ ' .wa-form').each(function(){
                var
                    form = $(this),
                    data = $(this).data();
                $(this).css({'position': data.position, 'left': data.left});
            });
            $('input[name="customer_' + smart_shipping_id + '[address.shipping][city]"]').data('no-route', 1).trigger('change');
            if ($("input[name=shipping_id]").length > 0 && $("input[name=shipping_id]:first").val() != smart_shipping_id) {
                $("input[name=shipping_id]:first").attr('checked', 'checked').change();
            }
        }
    );
}

function route(addr, callback) {

    distanceInput.val(defaultVal);
    dataInput.val('');

    addr = addr.trim();

    if (addr == "") {
        if (typeof callback == 'function') {
            return callback();
        }
        return false;
    }

    var multiRoute = new ymaps.multiRouter.MultiRoute({
            referencePoints: [
                routeStartPoint,
                addr
            ]
        }, {
            options: {
                boundsAutoApply: true
            }
        });

    //ssMap.geoObjects.add(multiRoute);

    multiRoute.model.events
        .add("requestsuccess", function (event) {

            ymaps.geoQuery(ssMap.geoObjects).search('geometry.type != "Polygon"').removeFromMap(ssMap);

            var
                routes = event.get("target").getRoutes(),
                shortest_index = false,
                shortest_distance = false,
                distances_value = '';

            if (routes.length > 0) {
                for (var i = 0, l = routes.length; i < l; i++) {
                    if (false === shortest_index) {
                        shortest_index = i; shortest_distance = routes[i].properties.get("distance").value / 1000;
                    } else {
                        if (routes[i].properties.get("distance").value / 1000 < shortest_distance) {
                            shortest_index = i;
                            shortest_distance = routes[i].properties.get("distance").value / 1000;
                        }
                    }
                }
            } else {
                distances_value = 'no_routes';
            }

            if (shortest_distance && shortest_distance <= max_route_distance) {

                var shortest_route = routes[shortest_index], paths = shortest_route.getPaths(), path = paths[0], coordinates = path.properties.get('coordinates'), edges = [];

                for (var i = 1, l = coordinates.length; i < l; i++) {
                    edges.push({
                        type: 'LineString',
                        coordinates: [coordinates[i], coordinates[i - 1]]
                    });
                }

                var routeObjects = ymaps.geoQuery(edges)
                        .add(multiRoute.getWayPoints())
                        .setOptions('strokeWidth', 5)
                        .addToMap(ssMap),
                    distances = {};

                ssMap.setBounds(routeObjects.getBounds());

                if (typeof smartshippingCallbacks['_global'] === 'undefined') {
                    // для каждого полигона найти: - элементы входящие, невходящие, пересекающие; посчитать их длину
                    for (var i in polygonObjects) {
                        var
                            polygon = polygonObjects[i],
                            polygonId = zones[i].id,
                            objectsIn = routeObjects.searchInside(polygon),
                            objectsBoundary = routeObjects.remove(objectsIn).searchIntersect(polygon),
                            routeObjects = routeObjects.remove(objectsIn).remove(objectsBoundary),
                            callbacks = smartshippingCallbacks[polygonId];


                        distances[polygonId] = {
                            'in': fireCallbackAndCountDistance('inCallback', callbacks, objectsIn),
                            'boundary': fireCallbackAndCountDistance('boundaryCallback', callbacks, objectsBoundary)
                        };
                    }

                    // вызвать колбэк для объектов, выходящих за пределы доступных полигонов
                    distances['_outside'] = { "in":  fireCallbackAndCountDistance('outsideCallback', smartshippingCallbacks, routeObjects)};
                } else {
                    distances = smartshippingCallbacks['_global'](routeObjects, polygonObjects);
                }

                distances_value = JSON.stringify(distances);

            } else if (distances_value == "") {
                distances_value = 'max_route_distance_exceeded';
            }

            if (distanceInput.val() == defaultVal) {
                distanceInput.val(distances_value);
            }

            if (typeof callback == 'function') {
                callback();
            }

        });
    return true;
}

function countDistance(objects) {
    var distance = 0;
    objects.each(function(pm) {
        if (typeof pm.geometry.getDistance == 'function') {
            distance += pm.geometry.getDistance();
        }
    });
    return distance / 1000; // сразу вернуть в километрах
}

function fireCallback(func_name, params, scope) {
    if (typeof scope !== 'undefined' && typeof scope[func_name] !== 'undefined') {
        return scope[func_name](params);
    }
}

function fireCallbackAndCountDistance(callbackName, callbacksScope, objects) {

    var distance = fireCallback(callbackName, objects, callbacksScope);

    if (typeof distance === 'undefined') {
        distance = countDistance(objects);
    }

    return distance;
}

function getAddressString() {
    return '' +
        ($('select[name="customer_' + smart_shipping_id + '[address.shipping][region]"]').length ? $('select[name="customer_' + smart_shipping_id + '[address.shipping][region]"] option:selected').text() + ' ' : '') +
        $('input[name="customer_' + smart_shipping_id + '[address.shipping][city]"]').val() + ' ' + $('input[name="customer_' + smart_shipping_id + '[address.shipping][street]"]').val();
}

function smartShippingResponseCallback(data) {
    console.log(data);
    if (data.status == 'ok') {
        dataInput.val(data.data[0].text);
    }

}
