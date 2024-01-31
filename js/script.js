jQuery('form.checkout').on('checkout_place_order', function (event) {
    if (jQuery('input#createaccount').length>0 && !jQuery('input#createaccount').prop('checked')) {
        alert('Pentru a achita o sponsorizare, trebuie să aveți un cont. Vă rugăm bifați căsuța "Creați un cont?"');
        event.preventDefault();
        return false;
    }
 return true;
});