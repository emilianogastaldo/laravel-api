const placeholder = 'https://marcolanci.it/boolean/assets/placeholder.png';
const imageFiled = document.getElementById('image');
const previewField = document.getElementById('preview');

// input.addEventListener('input', () =>{
//     preview.src = input.value || placeholder;
// })
// MAGIA NERA
// cancello le due costanti input  e preview e scrivo direttamente:
// 
// image.addEventListener('input', () =>{
//     preview.src = input.value || placeholder;
// })

let blobUrl;

imageFiled.addEventListener('change', () => {
    // controllo se ho il file
    if (imageFiled.files && imageFiled.files[0]) {
        // prendo il file
        const file = imageFiled.files[0];
        //  URL temporaneo
        blobUrl = URL.createObjectURL(file);

        previewField.src = blobUrl;
    }
    else {
        previewField.src = placeholder;
    }

});

// I blob sono pesanti, quindi quando lascio la pagina devo cancellarlo
window.addEventListener('beforeunload', () => {
    if (blobUrl) URL.revokeObjectURL(blobUrl);
});