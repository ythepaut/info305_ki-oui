var synth;

if (readCookie('tts') == 'on') {
    var muted = false;
} else {
    var muted = true;
}

var voices;
var voice_fr;

/**
 * Initialise la voix
 */
function initVoices() {
    synth = window.speechSynthesis;

    voices = synth.getVoices();
    voice_fr = null;

    for (let i=0; i<voices.length; i++) {
        if (voices[i].lang.includes("fr")) {
            voice_fr = voices[i];
        }
        else if (voice_fr === null && voices[i].lang.includes("en")) {
            voice_fr = voices[i];
        }
    }

    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = voices;
    }
}

/**
 * Annule la lecture d'un son
 */
function cancelSound() {
    if (synth.speaking) {
        synth.cancel();
    }
}

/**
 * Lit un texte à voix hante
 *
 * @param   {string}            txt             Texte à lire
 */
function speak(txt) {
    if (!muted) {
        cancelSound();

        if (txt !== '') {
            var utterThis = new SpeechSynthesisUtterance(txt);
            utterThis.onend = function (event) {
                // fin du texte
            }
            utterThis.onerror = function (event) {
                if (!muted) {
                    initVoices();
                }

                console.error('Erreur TTS');
                console.log(event);
            }

            utterThis.voice = voice_fr;
            utterThis.pitch = 1;
            utterThis.rate = 1.1;
            synth.speak(utterThis);
        }
    }
}

var body = document.querySelector("html");

var balise_precedente = null;

body.addEventListener('mousemove', e => {
    let inside = false;
    var text = "";
    var balise = null;

    var elements = document.querySelectorAll(':hover');

    for (let i=0; i<elements.length; i++) {
        balise = elements[i];
        var nodeType = balise.nodeName.toLowerCase();

        if (["p", "h1", "h2", "h3", "h4", "h5", "h6", "label", "a", "button", "li", "span", "th", "td"].includes(nodeType)) {
            text = balise.innerText;
            inside = true;
        } else if (nodeType === "input") {
            text = balise.placeholder;
            inside = true;
        } else if (nodeType === "img") {
            text = balise.alt;
            inside = true;
        } else {}
    }

    var div = document.createElement("div");
    div.innerHTML = text;
    text = div.textContent || div.innerText || "";

    if (text !== "") {
        if (inside) {
            if (balise === balise_precedente) {
                balise_precedente = balise;
            } else {
                balise_precedente = balise;
                speak(text);
            }
        } else {
            balise_precedente = null;

            cancelSound();
        }
    }
});

synth = window.speechSynthesis;

if (synth === undefined) {
    muted = true;
}
else {
    initVoices();
    cancelSound();
}



/**
 * Fonction qui inverse le cookie du TTS en on ou off
 * Exécute ensuite le passage du tts en on ou off
 * Ainsi que le changement de bouton dans le modal
 * 
 * @return void
 */
function editModalTTS() {

    readTTS = readCookie('tts');

    switch (readTTS) {
        case 'on':
            createCookie('tts', 'off', false);
            break;
        case 'off':
            createCookie('tts', 'on', false);
            break;
    }

    var ttsCookie = readCookie('tts');
    muted = toggleTTS(ttsCookie);
    
    // modification du bouton après changement
    button = document.getElementById("tts");
    if (muted) {
        button.innerHTML = 'Désactivé';
    } else {
        button.innerHTML = 'Activé';
    }
}

/**
 * Vrai si value est à 'off', faux si value est à 'on'
 * 
 * @param  {string} value
 * @return {boolean}
 */
function toggleTTS(value) {
    if (value == 'on') {
        var res = false;
    }
    else if (value == 'off') {
        var res = true;
    }
    return res;
}


// modification du bouton au chargement
button = document.getElementById("tts");
if (muted) {
    button.innerHTML = 'Désactivé';
} else {
    button.innerHTML = 'Activé';
}