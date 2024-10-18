<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-content">
            <div class="popup-top">
                <div class="headline">
                    <h1>New Category</h1>
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
                    <div class="form-container" >
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" id="name" name="name"   required/>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description"  name="description"  required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="picture">Picture</label>
                            <input type="file" id="picture" name="picture" required/>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" id="price" step="0.01" name="price" required/>
                        </div>
                        <div class="form-group mb-3">
                            <label for="category">Category:</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                <option value="equipment">Equipment</option>
                                <option value="tools">Tools</option>
                                <option value="material">Items</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="course">Course:</label>
                            <select id="course" name="course" class="form-control" required>
                                <option value="">-- Select Course --</option>
                                <option value="RAC Servicing (DomRAC)">RAC Servicing (DomRAC)</option>
                                <option value="Basic Shielded Metal Arc Welding">Basic Shielded Metal Arc Welding</option>
                                <option value="Advanced Shielded Metal Arc Welding">Advanced Shielded Metal Arc Welding</option>
                                <option value="Pc operation">Pc Operation</option>
                                <option value="Bread and pastry production NC II">Bread and Pastry Production NC II</option>
                                <option value="Computer aid design (CAD)">Computer Cid Design (CAD)</option>
                                <option value="Culinary arts">Culinary Arts</option>
                                <option value="Dressmaking NC II">Dressmaking NC II</option>
                                <option value="Food and beverage service NC II">Food and Beverage Service NC II</option>
                                <option value="Hair care">Hair Care</option>
                                <option value="Junior beautician">Junior Beautician</option>
                                <option value="Gas metal Arc Welding -- GMAW NC I">Gas Metal Arc Welding -- GMAW NC I</option>
                                <option value="Gas metal Arc Welding -- GMAW NC II">Gas Metal Arc Welding -- GMAW NC II</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="popup-footer">
                    <button type="submit">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>