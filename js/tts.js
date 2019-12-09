var synth = window.speechSynthesis;

var voices = synth.getVoices();
var voice_fr = null;

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

function speak(txt) {
    if (synth.speaking) {
        synth.cancel();
    }
    if (txt !== '') {
    var utterThis = new SpeechSynthesisUtterance(txt);
    utterThis.onend = function (event) {
        //console.log('SpeechSynthesisUtterance.onend');
    }
    utterThis.onerror = function (event) {
        console.error('Erreur TTS');
        console.log(event);
    }

    utterThis.voice = voice_fr;
    utterThis.pitch = 1;
    utterThis.rate = 1.3;
    synth.speak(utterThis);
  }
}

function speakGeneral() {
    var txt = document.querySelector('.txt').value;

    speak(txt);
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

            if (synth.speaking) {
                synth.cancel();
            }
        }
    }
});
