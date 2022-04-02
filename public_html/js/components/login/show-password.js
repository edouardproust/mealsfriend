const passwordField = document.querySelector('#loginPassword');
const eyeOpen = document.querySelector('#eyeOpen');
const eyeClosed = document.querySelector('#eyeClosed');
const eyeContainer = eyeOpen.parentNode;

eyeContainer.style.marginTop = '-4px';
eyeClosed.style.display = "none";

eyeOpen.addEventListener('click', function(){
    passwordField.type = "text";
    eyeOpen.style.display = "none";
    eyeClosed.style.display = "flex";
    // margins
    eyeContainer.style.marginRight = '-1px';
});
eyeClosed.addEventListener('click', function(){
    passwordField.type = "password";
    eyeClosed.style.display = "none";
    eyeOpen.style.display = "flex";
    // margins
    eyeContainer.style.marginRight = '0px';
});