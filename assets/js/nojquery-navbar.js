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
