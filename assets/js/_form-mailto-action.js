$o('form button[type=submit]').addEventListener('click', function(evt) {
    
    evt.preventDefault()
    
    const form = evt.target.parentElement.parentElement
    
    const sendToEmail = 'contact@freewriters.org'
    
    const email = form.querySelector('input[type=email]').value
    const subject = form.querySelector('*[name=_subject]').value
    const message = form.querySelector('textarea[name=message]').value
    
    const mailtoLink = 'mailto:' + sendToEmail + '?body=' + encodeURIComponent(message) + '&subject=' + encodeURIComponent(subject)
    
    
    window.open(mailtoLink)
});
