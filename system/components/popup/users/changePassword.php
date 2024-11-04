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
                    <h1>Change Password</h1>
                </div>
                <div class="paragraph">
                    <p>Change User Password</p>
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
                        <label for="location">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter Password"   />
                    </div>

                    <div class="form-group">
                        <label for="location">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Enter Confirm Password"  />
                    </div>
                </div>
                <div class="popup-footer">
                    <button type="submit">Save Password</button>
                </div>
            </form>
        </div>
    </div>
</div>