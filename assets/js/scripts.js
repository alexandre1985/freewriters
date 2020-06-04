// MyQuery
let $o = arg => document.querySelector(arg)
let $a = arg => document.querySelectorAll(arg)

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


const urlParams = new URLSearchParams(window.location.search);
const myParam = urlParams.get('form');

if(myParam) {
  const index = Number(myParam);
  $a('option')[index].selected = true;
}
/* DropDowns */

const dds = $a('.dropdown-toggle')
const ddmenus = $a('.dropdown-menu')

function toggleDisplay(el) {
    if (el.style.display === 'block') {
        el.style.display = ''
    } else {
        el.style.display = 'block'
    }
}

function hideDDMenus(exceptElement) {
    for (let i = 0; i < ddmenus.length; i++) {
        if(ddmenus[i] !== exceptElement) {
            ddmenus[i].style.display = ''
        }
    }
}

for (let j = 0; j < dds.length; j++) {
    ['click','ontouchstart'].forEach(function(evt) {

        dds[j].addEventListener(evt, function() {
            let ddmenu = ddmenus[j]
            hideDDMenus(ddmenu)
            toggleDisplay(ddmenu)
        })
        
    })
}

/* Navbar ARIA */

function ariaToggle(togglerEl, bool=null) {
    if(bool === null) {
        // toggle
        bool = (togglerEl.getAttribute('aria-expanded') !== 'true')
    }
    togglerEl.setAttribute('aria-expanded', bool)
}

let navbarToggler = $o('.navbar-toggler')

navbarToggler.addEventListener('click', function(evt) {
    ariaToggle(navbarToggler)
})

let ano = new Date().getFullYear();
document.getElementsByClassName('ano')[0].innerHTML = ano;
