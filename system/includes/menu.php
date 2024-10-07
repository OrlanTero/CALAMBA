<style>
    .custom-menu {
        position: fixed;
        top: 85px;
        left: 0;
        width: 260px;
        height: 100%;
        background-color: #2980b9;
        transition: left 0.3s ease;
        z-index: 999;
        display: block;
    }

    .custom-menu.show {
        left: -300px ;
    }

    .custom-menu .menu-header {
        padding: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .custom-menu .main-nav {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    .custom-menu .main-nav .link{
        padding: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .custom-menu .main-nav .link.drop {
        border-bottom: none !important;
    }

    .custom-menu .main-nav .link a, .as-link {
        display: flex;
        align-items: center;
        color: white;
        padding: 15px;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }

    .custom-menu .main-nav .link i {
        margin-right: 15px;
    }

    .custom-menu .main-nav .link .link-contents a {
        margin-left: 20px;
    }
</style>

<div class="custom-menu" >
    <div class="menu-header">Navigation</div>
    <div class="main-nav">
        <div class="link"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></div>

        <div class="link"><a href="catalog.php" id="list-admin"><i class="fas fa-list"></i><span>Equipments</span></a></div>

        <div class="link drop">
            <div class="as-link" id="list-admin" role="button">
                <i class="fas fa-list"></i><span>Equipment Manager</span>
            </div>
            <div class="link-contents">
                <div class="link"><a href="requests.php" id="borrow-admin"><i class="fas fa-history"></i> Requests</a></div>
                <div class="link"><a href="borrow.php" id="borrow-admin"><i class="fas fa-history"></i> Borrowed</a></div>
                <div class="link"><a href="returned.php" id="returned-admin"><i class="fas fa-undo"></i> Returned</a></div>
                <div class="link"><a href="not_returned.php" id="not-returned-admin"><i class="fas fa-times"></i> Not Returned</a></div>
                <div class="link"><a href="lost.php" id="lost-admin"><i class="fas fa-exclamation-circle"></i> Lost</a></div>
                <div class="link"><a href="damaged.php" id="damaged-admin"><i class="fas fa-tools"></i> Damaged</a></div>
            </div>
        </div>

<!--        <li><a href="QR_request.php" id="borrower-admin"><i class="fas fa-paper-plane"></i> Request Borrowers</a></li>-->
<!--        <li><a href="borrow_history.php" id="borrow-user"><i class="fas fa-history"></i> Borrow History</a></li>-->
    </div>
</div>