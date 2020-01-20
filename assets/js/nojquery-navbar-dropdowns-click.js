const dds = document.getElementsByClassName('dropdown-toggle')
const ddmenus = document.getElementsByClassName('dropdown-menu')

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
	dds[j].addEventListener('click', function() {
		let ddmenu = ddmenus[j]
		hideDDMenus(ddmenu)
		toggleDisplay(ddmenu)
	})
}