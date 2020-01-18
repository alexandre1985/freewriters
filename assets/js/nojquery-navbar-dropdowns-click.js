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