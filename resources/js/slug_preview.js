const title = document.getElementById('title');
const slug = document.getElementById('slug');

title.addEventListener('blur', () => {
    slug.value = title.value.trim().toLowerCase().split(' ').join('-')
});