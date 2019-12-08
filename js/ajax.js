$('form input[type="checkbox"]').on('click', function() {
    if (this.checked) { this.setAttribute('checked', 'checked'); } else { this.removeAttribute('checked'); }
});

$('form.ajax').on('submit', function() {
    
    //Recuperation du formulaire et des données de soumission
    var form = $(this),
        url = form.attr('action'),
        type = form.attr('method'),
        data = {};

    //Creation de l'objet qui contient les données noms/valeurs
    form.find('[name]').each(function(index, value) {
        
        var input = $(this),
            name = input.attr('name'),
            value = input.val(),
            type = input.attr('type'),
            checked = input.attr('checked');
        
        if (type != "checkbox" || (type == "checkbox" && checked == "checked")) {
            data[name] = value;
        }
        

    });



    $.ajax({

        url: url,
        type: type,
        data: data,
        //Fonction executée avant l'envoi du formulaire
        beforeSend: function(){

            //Desactivation des champs
            for (let submit of document.querySelectorAll('input[type="submit"]')) {
                submit.setAttribute('value', ' • • • ');
            }
            
            for (let inputDisabled of document.querySelectorAll('input:not([disabled])')) {
                inputDisabled.setAttribute('disabled', 'disabled');
            }


            //Validation re-captcha
            grecaptcha.execute('6LcikMEUAAAAAIzTpwihiSatPxk_MV8h3n6NI99l', {action: 'homepage'}).then(function(token) {
                for (let tokeninput of document.querySelectorAll('input[class="recaptcha"]')) {
                    tokeninput.setAttribute('value', token);
                }
            });

        },
        //Fonction executée lorsque l'on reçoit une reponse du php
        success: function(response) {
            
            setTimeout(function() {

                //Separation des elements de la reponse
                responseArray = response.split("#");

                //Div qui affiche le message retour
                var alertDiv = document.querySelector('#hint_' + data['action']);

                if (responseArray[0] == "SUCCESS") {
                    alertDiv.setAttribute('class', 'alert alert-success');
                    alertDiv.innerHTML = '<i class="fas fa-check-circle"></i>  &nbsp; ' + responseArray[1];

                    //Redirection si lien non "null"
                    if (responseArray[2] != "null") {
                        if (responseArray[3] != null) {
                            setTimeout(function() { window.location.href = responseArray[2] + "#" + responseArray[3]; window.location.reload(); }, 1000);
                        } else {
                            setTimeout(function() { window.location.href = responseArray[2]; }, 1000);
                        }
                    }
                    
                } else {
                    alertDiv.setAttribute('class', 'alert alert-warning');
                    alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i>  &nbsp; ' + responseArray[1];

                    //Suppression champs mot de passe si erreur
                    let pwdfield = document.querySelector('input[type="password"]')
                    if (pwdfield != null) {
                        pwdfield.value = "";
                    }
                }

                //Affichage du div message
                alertDiv.removeAttribute('style');
                
                //Réactivation des champs
                for (let submit of document.querySelectorAll('input[type="submit"]')) {
                    submit.removeAttribute('disabled');
                    
                    if (submit.getAttribute('name') == null) {
                        submit.setAttribute('value', 'Valider');
                    } else {
                        submit.setAttribute('value', submit.getAttribute('name'));
                    }
                }
                

                for (let inputDisabled of document.querySelectorAll("input[disabled='disabled']")) {
                    inputDisabled.removeAttribute('disabled');
                }

            }, 200);

        },
        //Fonction executée en cas d'erreur
        error: function() {

            var alertDiv = document.querySelector('#hint_' + data['action']);
            
            alertDiv.setAttribute('class', 'alert alert-warning');
            alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i>  &nbsp; ' + "Une erreur est survenue lors de la requête. Veuillez réessayer ulterieurement.";

            //Affichage du div message
            alertDiv.removeAttribute('style');
            
            //Réactivation des champs
            document.querySelector('input[type="submit"]').removeAttribute('disabled');
            document.querySelector('input[type="submit"]').setAttribute('value', 'Me connecter');
            for (let inputPwd of document.querySelectorAll('input[type="password"]')) {
                inputPwd.value = "";
            }

            for (let inputDisabled of document.querySelectorAll("input[disabled='disabled']")) {
                inputDisabled.removeAttribute('disabled');
            }
        }

    });



    return false;
});


//Recaptcha
window.setInterval(function(){
    grecaptcha.execute('6LcikMEUAAAAAIzTpwihiSatPxk_MV8h3n6NI99l', {action: 'homepage'}).then(function(token) {
        for (let tokeninput of document.querySelectorAll('input[class="recaptcha"]')) {
            tokeninput.setAttribute('value', token);
        }
    });
}, 60000);
//Init page re-captcha
grecaptcha.ready(function() {
    grecaptcha.execute('6LcikMEUAAAAAIzTpwihiSatPxk_MV8h3n6NI99l', {action: 'homepage'}).then(function(token) {
        for (let tokeninput of document.querySelectorAll('input[class="recaptcha"]')) {
            tokeninput.setAttribute('value', token);
        }
    });
});