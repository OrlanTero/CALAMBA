<?php

include_once "./../../../includes/Connection.php";

$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

$equipment = $CONNECTION->Select("equipment_info", ["id" => $data['id']], false);

?>

<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-content">
            <div class="popup-top">
                <div class="headline">
                    <h1>New Item</h1>
                </div>
                <div class="paragraph">
                    <p>Input Information</p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form class="">
                <div class="popup-bot">
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="Enter location" />
                    </div>
                    <div class="form-group">
                        <label for="serials">Serial Number</label>
                        <input type="text" id="serials" name="serials" placeholder="Enter serial number" />
                    </div>

                    <?php if($equipment['category'] == "material"): ?>
                        
                        <div class="material-content">
                            <div class="form-group">
                                <label for="price">Quantity</label>
                                <input type="number" id="quantity" placeholder="Enter Quantity"  name="quantity" value="0" required/>
                            </div>
                            <div class="form-group mb-3">
                                <label for="category">Borrowable/Disposable</label>
                                <select name="borrow_availability" class="form-control" required>
                                    <option value="">-- Select Borrowable/Disposable --</option>
                                    <option value="1">Borrowable</option>
                                    <option value="0">Disposable</option>
                                </select>
                            </div>
                        </div>
                    <?php endif ?>


                </div>
                <div class="popup-footer">
                    <button>Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>