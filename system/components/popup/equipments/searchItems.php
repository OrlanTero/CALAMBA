<?php 

include_once "./../../../includes/Connection.php";

session_start();

$CONNECTION = new Connection();

?>

<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-table huge-popup">
            <div class="popup-top">
                <div class="headline">
                    <h1>Search Items</h1>
                </div>
                <div class="paragraph">
                    <p>Please search your item using the search bar</p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form id="search-form">
                <div class="popup-bot">
                    <div class="search-container">
                        <input type="text" id="search-input" placeholder="Search items...">
                    </div>
                    <div id="search-results" class="search-results">
                        <!-- Results will be dynamically populated here -->
                    </div>
                    <div id="no-results" class="no-results" style="display: none;">
                        <p>No items found. Please try a different search term.</p>
                    </div>
                </div>
            </form>
            <div class="popup-footer">
            </div>
        </div>
    </div>
</div>

<style>
    .search-container {
        display: flex;
        margin-bottom: 20px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    #search-input {
        flex-grow: 1;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .search-results {
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    .search-result {
        display: flex;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .search-result:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .search-result .result-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 15px;
    }
    .search-result .result-details {
        flex-grow: 1;
    }
    .search-result h3 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 18px;
    }
    .search-result p {
        margin: 5px 0;
        color: #666;
        font-size: 14px;
    }
    .search-result .category {
        font-weight: bold;
        color: #007bff;
    }
    .search-result .serials {
        font-family: monospace;
        background-color: #eee;
        padding: 2px 4px;
        border-radius: 2px;
    }
    .search-result .location {
        font-style: italic;
    }
    .no-results {
        text-align: center;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #666;
        font-size: 16px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const noResults = document.getElementById('no-results');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        if (searchTerm.length > 0) {
            // Perform the search (you'll need to implement this part)
            // For now, let's simulate an empty result
            searchResults.innerHTML = '';
            
            // Show "No results" message if there are no results
            noResults.style.display = 'block';
        } else {
            // Clear results and hide "No results" message when search is empty
            searchResults.innerHTML = '';
            noResults.style.display = 'none';
        }
    });
});
</script>
