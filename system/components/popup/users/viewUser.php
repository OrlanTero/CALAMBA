<?php

include_once "./../../../includes/Connection.php";
include_once "./../../../includes/Functions.php";

$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

$user = $CONNECTION->Select("user", ["id" => $data['id']], false);

?>

<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-content">
            <div class="popup-top">
                <div class="headline">
                    <h1>View User</h1>
                </div>
                <div class="paragraph">
                    <p>View User Information</p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form class="">
                <div class="popup-bot">
                    <div class="profile-container">
                        <div class="left">
                            <div class="pic-container">
                                <div class="picture" style="background-image: url('<?=  !empty($user['profile_picture']) ? './uploads/' . $user['profile_picture'] : GetPhotoURLByName($user['first_name']) ?>');"></div>
                            </div>
                        </div>
                        <div class="right">
                            <h3><?= $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name'] ?></h2>
                            <p><?= $user['course'] ?></p>
                            <small><?= ucwords($user['user_type']) ?></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="location">First Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter First Name"  value="<?= $user['first_name'] ?>" />
                    </div>

                    <div class="form-group">
                        <label for="location">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" placeholder="Enter Middle Name"  value="<?= $user['middle_name'] ?>"  />
                    </div>

                    <div class="form-group">
                        <label for="location">Last Name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter Last Name"  value="<?= $user['last_name'] ?>"  />
                    </div>

                    <div class="form-group">
                        <label for="location">Suffix</label>
                        <input type="text" id="suffix" name="suffix" placeholder="Enter Suffix"  value="<?= $user['suffix'] ?>"  />
                    </div>

                    <div class="form-group">
                        <label for="location">Phone Number</label>
                        <input type="text" id="phone" name="phone" placeholder="Enter Phone Number"  value="<?= $user['phone'] ?>"  />
                    </div>
                    <div class="form-group mb-3">
                            <label for="course">Course:</label>
                            <select id="course" name="course" class="form-control" required>
                                <option value="">-- Select Course --</option>
                                <option value="RAC Servicing (DomRAC)" <?= $user["course"] == "RAC Servicing (DomRAC)" ? "selected" : "" ?>>RAC Servicing (DomRAC)</option>
                                <option value="Basic Shielded Metal Arc Welding" <?= $user["course"] == "Basic Shielded Metal Arc Welding" ? "selected" : "" ?>>Basic Shielded Metal Arc Welding</option>
                                <option value="Advanced Shielded Metal Arc Welding" <?= $user["course"] == "Advanced Shielded Metal Arc Welding" ? "selected" : "" ?>>Advanced Shielded Metal Arc Welding</option>
                                <option value="Pc operation" <?= $user["course"] == "Pc operation" ? "selected" : "" ?>>Pc Operation</option>
                                <option value="Bread and pastry production NC II" <?= $user["course"] == "Bread and pastry production NC II" ? "selected" : "" ?>>Bread and Pastry Production NC II</option>
                                <option value="Computer aid design (CAD)" <?= $user["course"] == "Computer aid design (CAD)" ? "selected" : "" ?>>Computer Cid Design (CAD)</option>
                                <option value="Culinary arts" <?= $user["course"] == "Culinary arts" ? "selected" : "" ?>>Culinary Arts</option>
                                <option value="Dressmaking NC II" <?= $user["course"] == "Dressmaking NC II" ? "selected" : "" ?>>Dressmaking NC II</option>
                                <option value="Food and beverage service NC II" <?= $user["course"] == "Food and beverage service NC II" ? "selected" : "" ?>>Food and Beverage Service NC II</option>
                                <option value="Hair care" <?= $user["course"] == "Hair care" ? "selected" : "" ?>>Hair Care</option>
                                <option value="Junior beautician" <?= $user["course"] == "Junior beautician" ? "selected" : "" ?>>Junior Beautician</option>
                                <option value="Gas metal Arc Welding -- GMAW NC I" <?= $user["course"] == "Gas metal Arc Welding -- GMAW NC I" ? "selected" : "" ?>>Gas Metal Arc Welding -- GMAW NC I</option>
                                <option value="Gas metal Arc Welding -- GMAW NC II" <?= $user["course"] == "Gas metal Arc Welding -- GMAW NC II" ? "selected" : "" ?>>Gas Metal Arc Welding -- GMAW NC II</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="category">Status</label>
                        <select name="archived" class="form-control" required>
                            <option value="">-- Select Status --</option>
                            <option value="0" <?= $user["archived"] == 0 ? "selected" : "" ?>>Active</option>
                            <option value="1" <?= $user["archived"] == 1 ? "selected" : "" ?>>Archived</option>
                        </select>
                    </div>
                </div>
                <div class="popup-footer">
                    <button type="button" class="popup-button change-password-button">Change Password</button>
                    <button type="submit">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>