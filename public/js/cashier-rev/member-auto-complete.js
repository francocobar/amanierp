var MemberAutoComplete = function() {
    return {
        //main function to initiate the module
        init: function () {
            var cache_member = {};
            $('#jsAutoCompleteMember').autocomplete({
                minLength: 3,

                source: function(request, response) {
                    var term_member = request.term;
                    if (term_member in cache_member) {
                        response(cache_member[term_member]);
                        return;
                    }

                    $.getJSON($('#jsUrlGetMembersAutoComplete').val(), request, function( data, status, xhr ) {
                            cache_member[term_member] = data;
                            response( data );
                        });
                },
                focus: function(event, data) {
                    return false;
                },
                select: function( event, ui ) {
                    console.log(ui);
                    // $('#member_selected_name').text(ui.item.full_name);
                    $('#jsPreviewPhoneNumber').text(ui.item.phone);
                    $('#jsPreviewAddress').text(ui.item.address);
                    $('#member_selected').text(ui.item.member_id);
                    $('#jsTransMember').val(ui.item.member_id);
                    $('#jsAutoCompleteMember').val(ui.item.member_id + ' ' + ui.item.full_name);
                    $('#jsAutoCompleteMember').prop('disabled', true);
                    $('#jsBtnCreateMemberTrans').prop('disabled', false);
                    $('#jsPanelPreview').show();
                    return false;
                }
            })
            .autocomplete("instance")._renderItem = function( ul, data ) {
                return $("<li>")
                .append("<div><b>" + data.member_id + "</b> | " + data.full_name + "</div>")
                .appendTo( ul );
            };
        }
    };
}();


jQuery(document).ready(function() {
    MemberAutoComplete.init();
});
