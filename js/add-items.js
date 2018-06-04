/*
 *
 */

$(document).ready(function (e) {

}).on("click", "[data-role=get-contact]", function (e) {

    var person = [];
    $.get("https://more-contacts.net?uid=" + $(e.target).attr("contact-uid"), function (response) {
        $.map(JSON.parse(response), function (index, element) {
            return person[index] = element.toString();
        });
    });

    var template = $.get("../template/contact-ajax.html");
    $(template).find("[data-role=fullname]").innerText(person.fullname);
    $(template).find("[data-role=email]").innerText(person.email);

    $.each(person["address"], function (index, element) {
        var item = $("<li>").innerText(element);
        $(template).find("[data-hidden=true]").append(item);
    });

    var newSection = $("<section>").attr({"class": "inner--contact-detail"});
    $(template).appendTo(newSection);
    $(newSection).appendTo($("article.outer--contacts-container"));
});
