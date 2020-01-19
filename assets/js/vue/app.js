//"use strict"

// VUE JS

// Component Pagination

const paginationRangeMax = 5;

Vue.component('Pagination', {
	props: ['currentPage', 'totalPages'],
	created: function() {
		this.page = this.currentPage;
	},
	data: function() {
		return {
			page: 0
		}
	},
	computed: {
		rangeFirstPage() {
			var metade = Math.floor(paginationRangeMax/2);

			if (this.page <= metade) {
				return 1;
			}

			if (this.page > this.totalPages - metade) {
				if ((this.totalPages - paginationRangeMax) < 0) {
					return 1;
				}
				else {
					return this.totalPages - paginationRangeMax + 1;
				}
			}

			return (this.page - metade);
		},

		rangeLastPage() {
			return Math.min(this.rangeFirstPage + paginationRangeMax - 1, this.totalPages);
		},

		range() {
			let rangeList = [];
			for (let page = this.rangeFirstPage; page <= this.rangeLastPage; page+= 1) {
				rangeList.push(page);
			}
			return rangeList;
		},
		hasFirst() {
			return (this.page === 1);
		},

		hasLast() {
			return (this.page === this.totalPages);
		},
	},
	methods: {
		goto(page_var) {
			if(page_var !== this.page) {
				this.page = page_var;
				this.$emit('getbooks', page_var);
			}
		},
		isActive(page_var) {
			return this.page === page_var;
		},
		prev() {
			if (!this.hasFirst) {
				this.page -= 1;
				this.$emit('getbooks', this.page);
			}
		},
		next() {
			if (!this.hasLast) {
				this.page += 1;
				this.$emit('getbooks', this.page);
			}
		},
	},
	template: `
	<nav class="d-inline-block">
	<ul class="pagination">
	<li class="page-item"><a class="page-link" aria-label="Previous" @click="prev"><span aria-hidden="true">«</span></a></li>
	<li v-for="number in range" class="page-item" v-bind:class="{active: isActive(number)}"><a class="page-link" v-text="number" @click="goto(number)"></a></li>
	<li class="page-item"><a class="page-link" aria-label="Next" @click="next"><span aria-hidden="true">»</span></a></li>
	</ul>
	</nav>`
});

// Component Book
Vue.component('Book', {
	props: ['info'],
	template: `<div class="row mb-3">
	<div class="col-5" v-if="info['cover-filename'] != 'null'">
	<img class="img-thumbnail" crossorigin="anonymous" :src="'/library-data/' + info['cover-filename']"/>
	</div>
	<div class="col">
	<h3 class="mb-0" v-text="info['book-title']"></h3>
	<h4 style="font-style:italic;font-weight:400;" v-text="info['book-author-name']"></h4>
	<p style="font-size:14.5px;" v-text="info['book-description']"></p>
	<div>
	<h4 class="h5 mb-0">Links</h4>
	<ul class="fa-ul">
	<li v-if="info['pdf-file-hash'] != 'null'">
	<i class="fas fa-arrow-right"></i>
	<a :href="'/library-data/'+info['pdf-file-hash']+'.pdf'" :download="info['book-title']+' - '+info['book-author-name']+'.pdf'">PC version (PDF)</a>
	<span v-if="info['has-pdf-sig']" style="color:grey;"> &nbsp;|&nbsp;</span>
	<a v-if="info['has-pdf-sig']" :href="'/library-data/'+info['pdf-file-hash']+'.sig'" :download="info['book-title']+' - '+info['book-author-name']+'.pdf.sig'">signature file</a>
	</li>
	<li v-if="info['epub-file-hash'] != 'null'">
	<i class="fas fa-arrow-right"></i>
	<a :href="'/library-data/'+info['epub-file-hash']+'.epub'" :download="info['book-title']+' - '+info['book-author-name']+'.epub'">Mobile version (EPUB)</a>
	<span v-if="info['has-epub-sig']" style="color:grey;"> &nbsp;|&nbsp;</span>
	<a v-if="info['has-epub-sig']" :href="'/library-data/'+info['epub-file-hash']+'.sig'" :download="info['book-title']+' - '+info['book-author-name']+'.epub.sig'">signature file</a>
	</li>
	</ul>
	</div>
	</div>
	</div>`
});

// Component Library
Vue.component('Library', {
	data: function() {
		return {
			books: [],
			error: false,
			totalPages: 0,
			currentPage: 1,
			firstUse: true
		}
	},
	methods: {
		get_books: function(page) {
			if(!this.firstUse && this.totalPages === 0) {
				return null;
			}
			this.firstUse = false;
			fetch('/get-library-books.php?currentpage=' + page).then(function(resp) {
				resp.json().then(function(json) {
					this.error = json.error;
					if(!this.error) {
						this.totalPages = json.total_pages;
						this.books = json.books;
						this.currentPage = page ? page : 1;
					}
				})
			})
		}
	},
	template: `<div>
	<div class="row">
	<h3 v-if="totalPages == 0" class="flex-fill text-center"><i>No books available</i></h3>
	<div class="col-lg-6" v-for="book in books">
	<Book :info="book"></Book>
	</div>
	</div>
	<div class="row">
	<div class="col-12 d-flex justify-content-center">
	<Pagination v-on:getbooks="get_books" :currentPage="currentPage" :totalPages="totalPages"></Pagination>
	</div>
	</div>
	</div>
	`,
	created: function() {
		this.get_books(0);
	}
});

// Main Vue instance
new Vue({
	el: '#app',
})
