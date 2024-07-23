document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const searchHistoryContainer = document.getElementById('searchHistoryContainer');
    const clearHistoryButton = document.getElementById('clearHistory');

    function loadSearchHistory() {
        const searchHistory = JSON.parse(localStorage.getItem('searchHistory')) || [];
        if (searchHistory.length > 0) {
            searchHistoryContainer.innerHTML = searchHistory.map(term =>
                `<div class="searchContainer">
                    <div class="search-history-item">
                        ${term} 
                    </div>
                    <div class="crossIcon">&#10006;</div>
                </div>`
            ).join('');
            searchHistoryContainer.appendChild(clearHistoryButton);
            clearHistoryButton.style.display = 'block';
        } else {
            searchHistoryContainer.innerHTML = '';
            clearHistoryButton.style.display = 'none';
        }
    }

    function saveSearchTerm(term) {
        let searchHistory = JSON.parse(localStorage.getItem('searchHistory')) || [];
        if (!searchHistory.includes(term)) {
            searchHistory.push(term);
            if (searchHistory.length > 10) {
                searchHistory.shift();
            }
            localStorage.setItem('searchHistory', JSON.stringify(searchHistory));
        }
    }

    function removeSearchTerm(term) {
        let searchHistory = JSON.parse(localStorage.getItem('searchHistory')) || [];
        searchHistory = searchHistory.filter(t => t !== term);
        localStorage.setItem('searchHistory', JSON.stringify(searchHistory));
        loadSearchHistory();
    }

    loadSearchHistory();

    searchInput.addEventListener('focus', function () {
        if (searchHistoryContainer.innerHTML.trim() !== '') {
            searchHistoryContainer.style.display = 'block';
        }
    });

    document.addEventListener('click', function (event) {
        if (!searchForm.contains(event.target)) {
            searchHistoryContainer.style.display = 'none';
        }
    });

    searchForm.addEventListener('submit', function (event) {
        const searchTerm = searchInput.value.trim();
        if (searchTerm) {
            saveSearchTerm(searchTerm);
        }
    });

    clearHistoryButton.addEventListener('click', function () {
        localStorage.removeItem('searchHistory');
        loadSearchHistory();
    });

    searchHistoryContainer.addEventListener('click', function (event) {
        if (event.target.classList.contains('search-history-item')) {
            searchInput.value = event.target.textContent.trim();
            searchForm.submit();
        } else if (event.target.classList.contains('crossIcon')) {
            const parentContainer = event.target.closest('.searchContainer');
            const termToRemove = parentContainer.querySelector('.search-history-item').textContent.trim();
            removeSearchTerm(termToRemove);
        }
    });
});
