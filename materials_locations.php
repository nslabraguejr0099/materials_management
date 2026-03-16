<?php
$url = 'http://localhost/materials_management/';
?>
<html>

<head>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?php echo $url.'/main.js' ?>" defer></script>

    <link rel="stylesheet" href="<?php echo $url.'/main.css'?>">

    <script>
        _url = 'modules/materials_locations.php';
        _module = 'materials_locations';
        window.onload = function() {
            col_display = [
                'material_code',
                'location_code',
                'price',
                'availability',
                'status'
            ];
            loadTbl();
        };
    </script>
</head>


<body>

<div class="header">
    <a href="#" class="logo">MATERIALS MANAGEMENT</a>
    <div class="header-right">
        <a href="locations.php">Locations</a>
        <a href="materials.php">Materials</a>
        <a href="categories.php">Categories</a>
        <a href="materials_locations.php" class="active">Materials-Locations</a>
    </div>
</div>

<div id="mainPanel" class="move-right" style="margin: 30px;">
    <h1>Materials-Locations Reference</h1>

    <div id="msgBoxMain">
    </div>

    <div style="position: relative;">
        <div id="grayScreen" style="position: absolute; height: 100%; width: 100%; z-index: 2;" >
            <div style="background-color: #6c757d; position: absolute; width: 100%; top: 0; bottom: 0; left: 0; opacity: 0.5;"></div>
            <div style="background-color: white; text-align: center; border: 1px solid black; border-radius: 10px; position: absolute;
                width: 170px; height: auto; top: 50%; left: 50%; margin: auto 0 0 -90px; padding: 10px;">
                <!-- <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><br> -->
                <span>Loading...</span>
            </div>
        </div>

        <div class="container-filter" style="margin-bottom: 10px;">
            <div class="box" name="filterBox" id="filterBox">

            </div>
            <div class="box">
                <div style="float: right;">
                    <button type="button" style="margin-right: 10px;" onclick="generateReport()">Generate Report</button>
                    <button type="button" style="margin-right: 10px;" onclick="addBtn()">Add</button>
                    <form onsubmit="event.preventDefault()" style="display:inline-block; margin: 0;">
                        Search: <input type="text" name="search" id="search" placeholder="Search" autocomplete="off">
                        <button type="submit" onclick="loadTbl()">Apply</button>
                    </form>
                    <button type="button" onclick="resetFilter()">Reset</button>
                </div>
            </div>
        </div>
        <table class="tbl tbl-display" name="tbl" id="tbl">
            <thead>
                <tr>
                    <th>Materials Code</th>
                    <th>Locations Code</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <th>Status</th>
                    <th style="width:205px;">Options</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div id="formPanel" class="move-right out" style="margin: 30px;">
    <h1>Materials-Locations Reference: <span id="formTitle">Add New</span> Record</h1>

    <div id="msgBoxForm">
    </div>

    <form onsubmit="event.preventDefault()" name="formBody" id="formBody">

        <div style="width: 70%;">
            <input type="hidden" class="form-input" name="id" id="id">

            <div style="padding: 5px 10px;">
                <h4 style="margin: 0;">Material Code</h4>
                <input type="text" class="form-input uppercase-all" style="width: 100%;" name="material_code" id="material_code" placeholder="Material Code" autocomplete="off"
                maxlength="10">
                <div class="input-error" id="feed_material_code"></div>
            </div>

            <div style="padding: 5px 10px;">
                <h4 style="margin: 0;">Location Code</h4>
                <input type="text" class="form-input uppercase-all" style="width: 100%;" name="location_code" id="location_code" placeholder="Location Code" autocomplete="off"
                maxlength="10">
                <div class="input-error" id="feed_location_code"></div>
            </div>
    
            <div style="padding: 5px 10px;">
                <h4 style="margin: 0;">Price</h4>
                <input type="text" class="form-input float-input" style="width: 100%;" name="price" id="price" placeholder="Price" autocomplete="off"
                maxlength="100">
                <div class="input-error" id="feed_price"></div>
            </div>

            <div style="padding: 5px 10px;">
                <h4 style="margin: 0;">Availability</h4>
                <select class="form-input" name="availability" id="availability">
                    <option value="">Select Availability</option>
                    <option value="1">Available</option>
                    <option value="0">Unavailable</option>
                </select>
                <div class="input-error" id="feed_availability"></div>
            </div>

            <div style="padding: 5px 10px;">
                <h4 style="margin: 0;">Status</h4>
                <select class="form-input" name="status" id="status">
                    <option value="">Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <div class="input-error" id="feed_status"></div>
            </div>
    
            <div style="padding: 20px 10px;" id="btnOptGroup">
                <div id="submitBtns" style="display: inline-block;">
                    <button type="submit" onclick="saveDB('submit')">Submit</button>
                    <button type="submit" onclick="saveDB('submitAndNew')">Submit And New</button>
                </div>
                <button onclick="togglePanel('mainPanel')">Cancel</button>
            </div>
        </div>

    </form>
</div>

</body>
</html>