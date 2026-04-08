// Global search functionality
const globalSearch = document.getElementById('globalSearch');
if (globalSearch) {
    globalSearch.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const cards = document.querySelectorAll('.prod-card');
        
        cards.forEach(card => {
            const title = card.querySelector('.prod-title').textContent.toLowerCase();
            const creator = card.querySelector('.prod-creator');
            const creatorText = creator ? creator.textContent.toLowerCase() : '';
            const alignment = card.querySelector('.alignment-badge').textContent.toLowerCase();
            
            if (title.includes(filter) || creatorText.includes(filter) || alignment.includes(filter)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Alignment Filter Logic
const alignmentFilter = document.getElementById('alignmentFilter');
if (alignmentFilter) {
    alignmentFilter.addEventListener('change', function() {
        const selectedAlignment = this.value.toLowerCase();
        const cards = document.querySelectorAll('.prod-card');
        
        cards.forEach(card => {
            const alignmentBadge = card.querySelector('.alignment-badge');
            if (!alignmentBadge) return;
            
            const alignment = alignmentBadge.textContent.toLowerCase().trim();
            
            if (selectedAlignment === 'all' || alignment === selectedAlignment) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Prevent default link behavior on overlay button clicks
document.querySelectorAll('.overlay-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // The parent link will still work
    });
});
