var bookForm = document.getElementById('form-book-upload');

var filelist, file, formHasBook;

if(bookForm && bookForm.length) {
  
  bookForm.addEventListener("submit", function(event) {
    
    var buttons_of_books = document.querySelectorAll('#form-book-upload .book');

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


const urlParams = new URLSearchParams(window.location.search);
const myParam = urlParams.get('form');

if(myParam) {
  const index = Number(myParam);
  document.querySelectorAll('option')[index].selected = true;
}
const dds = document.getElementsByClassName('dropdown-toggle')
const ddmenus = document.getElementsByClassName('dropdown-menu')

function hideDDMenus(exceptElement) {
	for (let i = 0; i < ddmenus.length; i++) {
		if(ddmenus[i] !== exceptElement) {
			ddmenus[i].classList.remove('show')
		}
	}
}

for (let j = 0; j < dds.length; j++) {
	dds[j].addEventListener('click', function() {
		let ddmenu = ddmenus[j]
		hideDDMenus(ddmenu)
		ddmenu.classList.toggle('show')
	})
}
let ano = new Date().getFullYear();
document.getElementsByClassName('ano')[0].innerHTML = ano;
