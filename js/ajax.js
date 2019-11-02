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
        },
        success: function(response) {
            
            setTimeout(function() {

                responseArray = response.split("#");

                if (responseArray[0] == "SUCCESS") {
                    window.location.href = "/espace-utilisateur";
                }


                document.querySelector('#hint_' + document.querySelector('input[name="action"]').getAttribute('value')).innerHTML = responseArray[1];
                

                document.querySelector('input[type="submit"]').removeAttribute('disabled');
                document.querySelector('input[type="submit"]').setAttribute('value', 'Me connecter');
                document.querySelector('input[type="password"]').value = "";

            }, 500);
        }

    });



    return false;
});