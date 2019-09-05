$(document).ready(() => {
    $("#hideLogin").click(() => {
        $("#loginForm").hide();
        $("#registerForm").show();
    });
    $("#hideRegister").click(() => {
        $("#loginForm").show();
        $("#registerForm").hide();
    });
});