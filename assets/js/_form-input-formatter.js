String.prototype.capitalize = function() {
    const text = this.valueOf()
    return text.charAt(0).toUpperCase() + text.substr(1)
}

String.prototype.capitalizeWords = function() {
    return this.valueOf().replace(/\w\S*/g, function(text) {
        return text.capitalize()
    })
}


$o('form button[type=submit]').addEventListener('mouseup', function(evt) {
    
    const nameField = $o('form input[name=name]')
    
    // Capitalize every word of a Name
    nameField.value = nameField.value.capitalizeWords()
    
})

$o('form textarea').addEventListener('keyup', function(evt) {
    
    // Capitalize first letter of Message Text
    evt.target.value.length < 5 ? evt.target.value = evt.target.value.capitalize() : 0
    
})
;
