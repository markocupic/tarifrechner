/**
 * Created by Marko on 06.11.2016.
 */
(function ($) {

    /**
     *
     */
    function resetForm() {
        $('#rollgeldNetto').find('span').text('0');
        $('#rollgeldMwst').find('span').text('0');
        $('#rollgeldBrutto').find('span').text('0');

        // Save values to hidden form inputs
        $('input[name="ctrlRollgeldNetto"]').prop('value', '0');
        $('input[name="ctrlRollgeldMwst"]').prop('value', '0');
        $('input[name="ctrlRollgeldBrutto"]').prop('value', '0');
    }

    /**
     *
     * @param elInput
     */
    function getPrice(elInput) {

        if (!isNumber($(elInput).val())) {
            $(elInput).prop('value', '');
            resetForm();
            return;
        }

        var weight = $(elInput).val();
        if (weight <= 0 || weight == '') {
            weight = 0;
        }

        var table = $('input[name="ctrlDatentabelle"]').val();

        var request = $.ajax({
            url: '?isAjax=true&act=getPrice&weight=' + weight + '&table=' + table,
            method: "get",
            data: {
                //
            },
            dataType: 'json'
        });


        request.done(function (json) {
            if (json) {
                if (json.serverResponse == 'true') {
                    // Display values
                    $('#rollgeldNetto').find('span').text(json.netto);
                    $('#rollgeldMwst').find('span').text(json.mwst);
                    $('#rollgeldBrutto').find('span').text(json.brutto);

                    // Save values to hidden form inputs
                    $('input[name="ctrlRollgeldNetto"]').prop('value', json.netto);
                    $('input[name="ctrlRollgeldMwst"]').prop('value', json.mwst);
                    $('input[name="ctrlRollgeldBrutto"]').prop('value', json.brutto);
                }
            }
            console.log(json);
        });
    }

    /**
     *
     * @param data
     * @returns {boolean}
     */
    function isNumber(data) {
        if (typeof parseInt(data) === 'number' && parseInt(data) % 1 == 0) {
            return true;
        }
        return false;
    }


    // Fire request on keyup-event
    $(document).ready(function () {
        if ($('input[name="ctrlGewicht"]').val() != '') {
            getPrice($('input[name="ctrlGewicht"]'));
        }

        $('input[name="ctrlGewicht"]').on('keyup', function () {
            getPrice(this);
        });

        $('input[name="ctrlGewicht"]').closest('form').find('button[type="reset"]').click(function () {
            resetForm();
        });

    });
})(jQuery);