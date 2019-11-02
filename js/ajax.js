$('form.ajax').on('submit', function() {
    
    var form = $(this),
        url = form.attr('action'),
        type = form.attr('method'),
        data = {};

    form.find('[name]').each(function(index, value) {
        
        var input = $(this),
            name = input.attr('name'),
            value = input.val();
        
        data[name] = value;

    });


    $.ajax({

        url: url,
        type: type,
        data: data,
        beforeSend: function(){
            document.querySelector('input[type="submit"]').setAttribute('disabled', 'disabled');
            document.querySelector('input[type="submit"]').setAttribute('value', ' • • • ');

            for (let inputDisabled of document.querySelectorAll('input:not([disabled])')) {
                inputDisabled.setAttribute('disabled', 'disabled');
            }
        },
        success: function(response) {
            
            setTimeout(function() {

                responseArray = response.split("#");

                var alertDiv = document.querySelector('#hint_' + document.querySelector('input[name="action"]').getAttribute('value'));

                if (responseArray[0] == "SUCCESS") {
                    alertDiv.setAttribute('class', 'alert alert-success');
                    alertDiv.innerHTML = '<i class="fas fa-check-circle"></i>  &nbsp; ' + responseArray[1];

                    setTimeout(function() { window.location.href = "/espace-utilisateur"; }, 1500);
                } else {
                    alertDiv.setAttribute('class', 'alert alert-warning');
                    alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i>  &nbsp; ' + responseArray[1];
                }

                alertDiv.removeAttribute('style');
                

                document.querySelector('input[type="submit"]').removeAttribute('disabled');
                document.querySelector('input[type="submit"]').setAttribute('value', 'Me connecter');
                document.querySelector('input[type="password"]').value = "";

                if (responseArray[0] != "SUCCESS") {
                    for (let inputDisabled of document.querySelectorAll("input[disabled='disabled']")) {
                        inputDisabled.removeAttribute('disabled');
                    }
                }

            }, 100);
        }

    });



    return false;
});