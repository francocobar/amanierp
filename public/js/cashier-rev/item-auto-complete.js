var urlSearchItems = '';
var ItemAutoComplete = function() {
    return {
        //main function to initiate the module
        init: function () {
            var cache = {};
            $("#jsSearchItemKeyword").autocomplete({
                minLength: 3,

                source: function( request, response ) {
                    // var term = request.term;
                    // if (term in cache) {
                    //     response(cache[term]);
                    //     return;
                    // }
                    $.getJSON($('#jsUrlGetItemsAutoComplete').val().replace('trans_id',$('#jsViewedOngoingTransId').val()), request, function( data, status, xhr ) {
                            // cache[term] = data;
                            response(data);
                        });
                },
                focus: function( event, data ) {
                    // alert("focus");
                    return false;
                },
                select: function( event, ui ) {
                    var price = parseInt(ui.item.price);
                    $('#jsQty').attr('data-price',price);
                    $(this).val(ui.item.item_name);
                    $(this).attr('title', ui.item.item_name);
                    $(this).prop('disabled', true);
                    $('#jsSelectedItemCode').val(ui.item.item_code);
                    $('#jsBtnResetSelectedItem').prop('disabled', false);
                    $('#jsAddItemOngoingTrans').prop('disabled', false);
                    $('#jsQty').trigger('change');
                    return false;
                }
            })
            .autocomplete("instance")._renderItem = function( ul, data ) {
                var price = parseInt(data.price);
                return $("<li>")
                .append("<div><b>" + data.item_code + "</b> | " + data.item_name + " | "+ maskMoney(price) + "</div>")
                .appendTo( ul );

            };
        }
    };
}();


jQuery(document).ready(function() {
    ItemAutoComplete.init();
});
