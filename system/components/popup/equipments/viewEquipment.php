<?php

require_once "../../../includes/Functions.php";

$data = json_decode($_POST["data"], true);

$id = $data["id"];

$equipment = GetEquipment($id);

?>

<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-content">
            <div class="popup-top">
                <div class="headline">
                    <h1>View Equipment</h1>
                </div>
                <div class="paragraph">
                    <p>View Information</p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form class="">
                
                <div class="popup-bot">
                <div class="pic-container" style="width: 100%;display: flex;justify-content: center;">
                        <div class="picture" style="background-image: url('uploads/<?= $equipment["picture"] ?>'); background-size: contain;background-repeat:no-repeat; background-position: center; width: 90%; height: 200px;border-radius: 10px;"></div>
                    </div>
                    <div class="form-container" >
                        <div class="form-group">
                            <label for="name">Equipment Name</label>
                            <input type="text" id="name" name="name" value="<?= $equipment["name"] ?>" required/>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description"  name="description"  required><?= $equipment["description"] ?></textarea>
                        </div>
                        <div class="form-group">
                            
                            <label for="picture">Picture</label>
                            <input type="file" id="picture" name="picture"/>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" id="price" step="0.01" name="price" value="<?= $equipment["price"] ?>" required/>
                        </div>
                        <div class="form-group mb-3">
                            <label for="category">Category:</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                <option value="equipment" <?= $equipment["category"] == "equipment" ? "selected" : "" ?>>Equipment</option>
                                <option value="tools" <?= $equipment["category"] == "tools" ? "selected" : "" ?>>Tools</option>
                                <option value="material" <?= $equipment["category"] == "material" ? "selected" : "" ?>>Items</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="course">Course:</label>
                            <select id="course" name="course" class="form-control" required>
                                <option value="">-- Select Course --</option>
                                <option value="RAC Servicing (DomRAC)" <?= $equipment["course"] == "RAC Servicing (DomRAC)" ? "selected" : "" ?>>RAC Servicing (DomRAC)</option>
                                <option value="Basic Shielded Metal Arc Welding" <?= $equipment["course"] == "Basic Shielded Metal Arc Welding" ? "selected" : "" ?>>Basic Shielded Metal Arc Welding</option>
                                <option value="Advanced Shielded Metal Arc Welding" <?= $equipment["course"] == "Advanced Shielded Metal Arc Welding" ? "selected" : "" ?>>Advanced Shielded Metal Arc Welding</option>
                                <option value="Pc operation" <?= $equipment["course"] == "Pc operation" ? "selected" : "" ?>>Pc Operation</option>
                                <option value="Bread and pastry production NC II" <?= $equipment["course"] == "Bread and pastry production NC II" ? "selected" : "" ?>>Bread and Pastry Production NC II</option>
                                <option value="Computer aid design (CAD)" <?= $equipment["course"] == "Computer aid design (CAD)" ? "selected" : "" ?>>Computer Cid Design (CAD)</option>
                                <option value="Culinary arts" <?= $equipment["course"] == "Culinary arts" ? "selected" : "" ?>>Culinary Arts</option>
                                <option value="Dressmaking NC II" <?= $equipment["course"] == "Dressmaking NC II" ? "selected" : "" ?>>Dressmaking NC II</option>
                                <option value="Food and beverage service NC II" <?= $equipment["course"] == "Food and beverage service NC II" ? "selected" : "" ?>>Food and Beverage Service NC II</option>
                                <option value="Hair care" <?= $equipment["course"] == "Hair care" ? "selected" : "" ?>>Hair Care</option>
                                <option value="Junior beautician" <?= $equipment["course"] == "Junior beautician" ? "selected" : "" ?>>Junior Beautician</option>
                                <option value="Gas metal Arc Welding -- GMAW NC I" <?= $equipment["course"] == "Gas metal Arc Welding -- GMAW NC I" ? "selected" : "" ?>>Gas Metal Arc Welding -- GMAW NC I</option>
                                <option value="Gas metal Arc Welding -- GMAW NC II" <?= $equipment["course"] == "Gas metal Arc Welding -- GMAW NC II" ? "selected" : "" ?>>Gas Metal Arc Welding -- GMAW NC II</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="popup-footer">
                    <button type="submit">Save Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>