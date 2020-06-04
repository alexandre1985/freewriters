$o('form button[type=submit]').addEventListener('click', function(evt) {
    
    evt.preventDefault()
    
    alert('Now, it will open your email client, for you to send a message?')
    
    const form = evt.target.closest('form')
    
    const sendToEmail = form.dataset.email
    
    const email = form.querySelector('input[type=email]').value
    const subject = form.querySelector('*[name=_subject]').value
    const name = form.querySelector('input[name=name]').value
    const text = form.querySelector('textarea[name=message]').value
    
    const message = text + '\r\n\r\n---\r\n' + name
    
    const mailtoLink = 'mailto:' + sendToEmail + '?body=' + encodeURIComponent(message) + '&subject=' + encodeURIComponent(subject)
    
    window.open(mailtoLink)
});
