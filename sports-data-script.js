

(function ($) {
    $(document).ready(function () {
        
        function getLiveData() {
            var selectedSport = $('#sportSelector').val();
            var data = {
                'action': 'get_live_data',
                'sport': selectedSport
            };
            var responseContainer = $('#sportsData');
        
            // Make the AJAX request
            $.post(ajaxurl, data, function (response) {
                responseContainer.html(response);
        
                // Hide or remove the sportSelectorForm
                $('#sportsData #sportSelectorForm').hide();
            }).fail(function (xhr, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
            });
        }
        

        $('#getLiveDataButton').on('click', getLiveData);

        $('.add-to-database').on('click', function () {
            var matchId = $(this).data('match-id');

            // AJAX request to store match ID
            var data = {
                'action': 'store_match_id',
                'match_id': matchId
            };

            $.post(ajaxurl, data, function (response) {
                alert('Match ID ' + matchId + ' added to the database!');
            }).fail(function (xhr, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
            });
        });
    });
})(jQuery);
