document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const wikiEntries = document.querySelectorAll('.wiki-entry');
    const tagContainer = document.getElementById('tag-container');

    // Suchfunktion
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        wikiEntries.forEach(entry => {
            const title = entry.querySelector('h2').textContent.toLowerCase();
            const content = entry.querySelector('p').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || content.includes(searchTerm)) {
                entry.style.display = '';
            } else {
                entry.style.display = 'none';
            }
        });
    });

    // Tags generieren und anzeigen
    const tags = new Set();
    wikiEntries.forEach(entry => {
        const tagElements = entry.querySelectorAll('.tag');
        tagElements.forEach(tag => {
            tags.add(tag.textContent);
        });
    });

    tags.forEach(tag => {
        const tagElement = document.createElement('span');
        tagElement.textContent = tag;
        tagElement.classList.add('tag');
        tagElement.addEventListener('click', () => {
            searchInput.value = tag;
            searchInput.dispatchEvent(new Event('input'));
        });
        tagContainer.appendChild(tagElement);
    });
});

document.querySelector('.collapsible-toggle').addEventListener('click', () => {
    const menu = document.querySelector('.collapsible-menu');
    menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
});
