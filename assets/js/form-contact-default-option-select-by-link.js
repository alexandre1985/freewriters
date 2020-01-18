const urlParams = new URLSearchParams(window.location.search);
const myParam = urlParams.get('form');

if(myParam) {
  const index = Number(myParam);
  document.querySelectorAll('option')[index].selected = true;
}