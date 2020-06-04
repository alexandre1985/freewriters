String.prototype.capitalize = function() {
    return this.valueOf().replace(/\w\S*/g, function(text) {
        return text.charAt(0).toUpperCase() + text.substr(1);
    });
}


$o('form input[name=name]').addEventListener('keyup', function(evt) {
    evt.target.value = evt.target.value.capitalize()
})

$o('form textarea').addEventListener('keyup', function(evt) {
    
    evt.target.value.length < 5 ? evt.target.value = evt.target.value.capitalize() : 0
    
})
;
