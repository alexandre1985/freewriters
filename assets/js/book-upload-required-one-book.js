var bookForm = $o('#form-book-upload');

var filelist, file, formHasBook;

if(bookForm && bookForm.length) {
  
  bookForm.addEventListener('submit', function(event) {
    
    var buttons_of_books = $a('#form-book-upload .book');

    formHasBook = false;

    buttons_of_books.forEach(function(button) {
      
      var filelist = button.files;

      for(i=0;i<filelist.length;i++) {
        var file = filelist[i];
        var fileMimeType = file.type;
        if(!formHasBook && (fileMimeType === 'application/pdf' || fileMimeType === 'application/epub+zip')) {
          formHasBook = true;
        }
      }
    });

    if(!formHasBook){
      event.preventDefault();
      alert('Please add a book file.');
    }
    
  });
}

