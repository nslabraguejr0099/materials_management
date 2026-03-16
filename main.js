var appStat = true;
var closeMsgBox;
var _msgKey = 0;
var input_mode;
var col_display;

function createFilters ( filters )
{
    let filterBox = document.getElementById("filterBox");
    if(filterBox)
        filterBox.innerHTML = '';

    for ( let fKey in filters )
    {
        let filter = filters[fKey];

        let div = document.createElement('DIV');
        div.style.display = 'inline-block';
        div.style.marginRight = '10px';
        div.innerHTML = filter['title']+': ';

        let select = document.createElement('SELECT');
        select.className = 'filter';
        select.id = 'filter_'+fKey;

        if ( !filter.hasOwnProperty('create_blank') || filter.create_blank )
            select.innerHTML = '<option value="">Select '+filter.title+'</option>';

        for ( let key in filter.data ) {
            let option = document.createElement('OPTION');
            option.value = filter.data[key].value;
            option.text = filter.data[key].title;

            select.append(option);
        }

        if ( filter.hasOwnProperty('value') ) {
            select.value = filters[fKey].value;
        } else {
            select.value = '';
        }

        div.append(select);
        filterBox.append(div);
    }
}

function resetFilter ()
{
    let filterBox = document.getElementById('filterBox');
    let filters = filterBox.querySelectorAll('.filter');
    filters.forEach(_elem => {
        _elem.value = '';
    });
    document.getElementById('search').value = '';
    loadTbl();
}

function addBtn ()
{
    input_mode = "POST";
    togglePanel("formPanel");
    clearForm();
    formAttr();
    document.getElementById('formTitle').innerHTML = 'Add New';
    document.getElementById("submitBtns").classList.remove('d-none');
    document.getElementById("submitBtns").classList.add('d-inline-block');
}

function togglePanel ( _mode ) {
    if(_mode == "mainPanel"){
        document.getElementById("mainPanel").classList.remove("out");
        document.getElementById("formPanel").classList.add("out");
        clearForm();
        window.scrollTo(0, 0);
    }
    else if(_mode == "formPanel"){
        document.getElementById("mainPanel").classList.add("out");
        document.getElementById("formPanel").classList.remove("out");
    }
}

function clearForm () {
    let form = document.getElementById('formBody');
    let formElem;

    // Clear value of text inputs
    formElem = formBody.querySelectorAll("input[type='text']");
    formElem.forEach(_elem => {
        _elem.value = "";
    });

    // Clear value of hidden inputs
    formElem = formBody.querySelectorAll("input[type='hidden']");
    formElem.forEach(_elem => {
        _elem.value = "";
    });

    // Clear value of select
    formElem = formBody.querySelectorAll("select");
    formElem.forEach(_elem => {
        if ( $('#'+_elem.id+" option[value='']").length > 0 ){
            _elem.value = "";
        }else{
            _elem.selectedIndex = 0;
        }
    });
}

function formAttr( _attr) {
    let form = document.getElementById('formBody');
    let formElem = form.querySelectorAll('.form-input');

    $(formElem).removeAttr("disabled");
    $(formElem).removeAttr("readonly");

    if(_attr){
        formElem.forEach(_elem => {
            _attr == "readonly" 
                ? _elem.readOnly = true
                : _elem.disabled = true;
        });
    }
}

function btnOptGroupAttr(_attr){
    let btnGroup = document.getElementById('btnOptGroup');
    let btns = btnGroup.querySelectorAll("button");
    $(btns).removeAttr("disabled");

    if( _attr == "disabled" ){
        btns.forEach(_btn => {
            _btn.disabled = true;
        })
    }
}

function displayMsg ( _msgBoxName, _status, _reportMsg, _btnTimerStat = true ) {
    let msgBox = document.querySelectorAll(_msgBoxName);
    closeMsgBox = [];

    msgBox.forEach(function (_msgBox) {
        const divBox = document.createElement("DIV");
        divBox.className = 'msg msg-'+_status;

        const divBtn = document.createElement("BUTTON");
        divBtn.className = 'msg-btn';
        divBtn.id = "msgBoxBtn_"+_msgKey;
        divBtn.addEventListener('click', function(){
            divBox.remove();
        });
        if ( _btnTimerStat ) {
            closeMsgBox[_msgKey] = setTimeout(function(){
                divBtn.click();
            }, 10000);
            divBtn.addEventListener("click" , function(){
                clearTimeout(closeMsgBox[_msgKey]);
                divBtn.click();
            });
        }
        divBox.append(divBtn);

        const divMsg = document.createElement("SPAN");
        divMsg.innerHTML = _reportMsg;
        divBox.append(divMsg);

        _msgBox.append(divBox);
        _msgKey++;
    });
    window.scrollTo(0, 0);
}

function validateForm ( _errors ) {
    let formBody = document.getElementById('formBody');
    let formElem = formBody.querySelectorAll(".form-input");
    formElem.forEach(_elem => {
        _elem.style.borderColor = '';
    });
    let feedback = formBody.querySelectorAll(".input-error");
    feedback.forEach(_elem => {
        _elem.style.display = '';
    });

    if ( _errors ) {
        for( const [key, value] of Object.entries(_errors) ) {
            let inputElement = document.getElementById(key);
            if ( inputElement ) 
                inputElement.style.borderColor = '#b30000';

            let inputMsg = document.getElementById("feed_"+key);
            if ( inputMsg ) {
                inputMsg.innerHTML = value;
                inputMsg.style.display = 'block';
            }
        }
    }
}

function viewBtn ( _data, _mode ) {
    let formBody = document.getElementById("formBody");
    let formElem;

    // 
    formElem = formBody.querySelectorAll("input[type='text'].form-input");
    formElem.forEach(_elem => {
        let dt = _data[_elem.id];
        if ( dt )
            dt = (decodeEntities(dt)).toString().replace(/(\r\n|\n|\r)/gm, " ");
        _elem.value = dt;
    });

    // 
    formElem = formBody.querySelectorAll("input[type='hidden'].form-input");
    formElem.forEach(_elem => {
        let dt = _data[_elem.id];
        if ( dt )
            dt = (decodeEntities(dt)).toString().replace(/(\r\n|\n|\r)/gm, " ");
        _elem.value = dt;
    });

    //
    formElem = formBody.querySelectorAll("select.form-input");
    formElem.forEach(_elem => {
        _elem.value = _data[_elem.id];
    });

    window.scrollTo(0, 0);
    if ( _mode == "view" ) {
        formAttr("readonly");
        formAttr("disabled");

        input_mode = "";
        $("#formTitle").html("View");
        document.getElementById("submitBtns").style.display = 'none';
        validateForm();
    } else {
        formAttr();

        input_mode = "PUT";
        $("#formTitle").html("Edit");
        document.getElementById("submitBtns").style.display = 'inline-block';
        validateForm();
    }

    togglePanel("formPanel");
}

function setInputFilter(textbox, proc, inputFilter) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
        textbox.addEventListener(event, function() {
            if ( proc == 'float_comma') {
                let val = this.value;
                val = val.replace(/,/g,'');
                
                val = val.toString().split(".");

                let pn = '';
                if ( val[0].substring(0,1) === '-' )
                    pn = '-';

                val[0] = val[0].replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                val = val.join(".");

                this.value = pn + val;
            }

            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
        });
    });
}

function initSetInputFilter () 
{
    var floatCommaInputs = document.querySelectorAll('.float-input');
    floatCommaInputs.forEach(_elem => {
        setInputFilter(_elem, "float_comma", function(value) {
            if ( value.substring(0, 1) === '-' && (value === '-' || /^-0/.test(value)) ) {
                if ( value === '-' || /^-0$/.test(value) ) {
                    return true;
                }
                if ( /^-0\./.test(value) ) {
                    return /^-0\.\d*$/.test(value);
                }
                return false;
            }
            else if ( /^0/.test(value) ) {
                if ( value === '0' ) {
                    return true;
                }
                else if ( /^0\./.test(value) ) {
                    return /^0\.\d*$/.test(value);
                }
                return false;
            }
            
            return /^-?(?:\d{0,3}(?:,\d{3})*|\d+)(?:\.?\d*)?$/.test(value);
        });
    });

    let upperCaseInputs = document.querySelectorAll('.uppercase-all');
    upperCaseInputs.forEach(_elem => {
        _elem.addEventListener('input', function(){
            let p = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(p, p);
        });
    });
}
initSetInputFilter();

var decodeEntities = (function() {
    // this prevents any overhead from creating the object each time
    var element = document.createElement('div');
  
    function decodeHTMLEntities (str) {
        if(str && typeof str === 'string') {
            // strip script/html tags
            str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
            str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
            element.innerHTML = str;
            str = element.textContent;
            element.textContent = '';
        }
    
        return str;
    }
  
    return decodeHTMLEntities;
})();

function hideAllPopup () {
    let allPopUp = document.querySelectorAll('.popuptext');
    allPopUp.forEach(_elem => {
        _elem.classList.remove('show');
    });
}








var loadTblStat = true;
async function loadTbl ( )
{
    if ( !appStat ) return;

    document.getElementById('grayScreen').style.display = '';

    if(loadTblStat == false){
        console.log("Please Wait . . .");
        return;
    }
    loadTblStat = false;

    let datas = {};
    datas['mode'] = 'tbl_load';
    datas['search'] = document.getElementById('search').value;
    datas['filters'] = {};

    let filterBox = document.getElementById('filterBox');
    let filters = filterBox.querySelectorAll('.filter');
    filters.forEach(_elem => {
        let filter_name = _elem.id.toLowerCase().replace('filter_', '');
        datas['filters'][filter_name] = _elem.value;
    });

    let ajax = await $.ajax({
        url: _url,
        method: 'POST',
        data: datas,
        success: function (xhr) {
            let tbl = document.getElementById('tbl');
            let tbl_tbody = tbl.querySelector('tbody');
            tbl_tbody.innerHTML = '';

            let data = (xhr.data && Array.isArray(xhr.data) && xhr.data.length) ? xhr.data : null;
            let filters = 
            (xhr.filters && 
             (Array.isArray(xhr.filters) || typeof xhr.filters == 'object') && 
             (xhr.filters.length || Object.entries(xhr.filters).length)) 
                ? xhr.filters 
                : null;

            if ( data ) {
                for ( data_key in data ) {
                    let dt = data[data_key];

                    let tr = document.createElement('tr');

                    col_display.forEach(_col => {
                        let td = document.createElement('td');
                        td.innerHTML = dt[_col];
                        if ( _col == 'status' )
                            td.innerHTML = dt[_col] == 1 ? 'Active' : 'Inactive';
                        if ( _col == 'availability' )
                            td.innerHTML = dt[_col] == 1 ? 'Available' : 'Unvailable';
                        if ( _col == 'category_code' ) {
                            let _str = '';
                            _str += dt[_col];
                            if ( dt['category_name'] )
                                _str += ' - '+dt['category_name'];
                            td.innerHTML = _str;
                        }

                        tr.appendChild(td);
                    });

                    let td = document.createElement('td');
                    td.style.textAlign = 'center';
                    td.style.padding = '3px';

                    let btn;
                    //  View button
                    btn = document.createElement('button');
                    btn.style.margin = '0 2px';
                    btn.innerHTML = 'View';
                    btn.addEventListener('click', function(){
                        viewBtn( dt, 'view');
                    });
                    td.appendChild(btn);

                    //  Edit button
                    btn = document.createElement('button');
                    btn.style.margin = '0 2px';
                    btn.innerHTML = 'Edit';
                    btn.addEventListener('click', function(){
                        viewBtn( dt, 'edit');
                    });
                    td.appendChild(btn);

                    /** Delete button */
                        delDiv = document.createElement('div');
                        delDiv.className = 'popup';
                        btn = document.createElement('button');
                        btn.style.margin = '0 2px';
                        btn.innerHTML = 'Delete';

                        popup = document.createElement('span');
                        popup.id = 'deletePopup_'+dt['id'];
                        popup.className = 'popuptext';
                        popup.innerHTML = 'Are you sure you want to delete this record?<br>';
                        popupYesBtn = document.createElement('button');
                        popupYesBtn.innerHTML = 'Yes';
                        popupYesBtn.addEventListener('click', function(){
                            deleteRecord(dt);
                        });
                        popup.appendChild(popupYesBtn);
                        popupNoBtn = document.createElement('button');
                        popupNoBtn.innerHTML = 'No';
                        popupNoBtn.addEventListener('click', function(){
                            document.getElementById('deletePopup_'+dt['id']).classList.remove('show');
                        });
                        popup.appendChild(popupNoBtn);

                        btn.addEventListener('click', function(){
                            hideAllPopup();
                            document.getElementById('deletePopup_'+dt['id']).classList.add('show');
                        });
                        // btn.appendChild(popup);
                        delDiv.appendChild(btn);
                        delDiv.appendChild(popup);

                        td.appendChild(delDiv);
                    /** */
                    tr.appendChild(td);


                    tbl_tbody.appendChild(tr);
                }
            } else {
                let tr = document.createElement('tr');
                let td = document.createElement('td');
                td.colSpan = col_display.length + 1;
                td.style.textAlign = 'center';
                td.innerHTML = 'No record found.';
                tr.appendChild(td);
                tbl_tbody.appendChild(tr);
            }

            if ( filters )
                createFilters(filters);

            loadTblStat = true;
            document.getElementById('grayScreen').style.display = 'none';
        },
        error: function (xhr, status) {
            alert('An error occured.');
            console.log(xhr);
            console.log(status);
        },
        complete: function (xhr, txtstat) {
            // console.log(xhr);
            // console.log(txtstat);
        }
    });
}


var savedbStat = true;
async function saveDB ( _submit_mode )
{
    event.preventDefault();

    if ( !appStat ) {
        return;
    }

    btnOptGroupAttr("disabled");
    formAttr("disabled");

    if( ! input_mode ){
        return;
    }

    if(savedbStat == false){
        console.log("Please Wait . . .");
        return;
    }
    savedbStat = false;

    let datas = {};
    datas['mode'] = 'save';
    datas['inputs'] = {};
    let formBody = document.getElementById("formBody");

    //
    let formElem = formBody.querySelectorAll(".form-input");
    formElem.forEach(_elem => {
        datas['inputs'][_elem.name] = _elem.value;
    });

    let ajax = await $.ajax({
        url: _url,
        method: input_mode,
        data: datas,
        success:function(xhr) {
            if(xhr.status == "success")
            {
                if(_submit_mode == 'submit'){
                    displayMsg("#msgBoxMain", xhr.status, xhr.report);
                    togglePanel("mainPanel");
                }else{
                    displayMsg("#msgBoxForm", xhr.status, xhr.report);
                    $("#formTitle").html("Add New");
                    clearForm();
                    input_mode = "POST";
                }
                validateForm();

                loadTbl();
            }else if(xhr.status == "info"){
                displayMsg("#msgBoxForm", xhr.status, xhr.report);
                validateForm();
            }else{
                if ( typeof xhr.report === 'string' ){
                    displayMsg("#msgBoxForm", xhr.status, xhr.report);
                    validateForm();
                }else{
                    validateForm(xhr.report);
                }
                window.scrollTo(0, 0);
            }
        },
        error: function(xhr, status){
            alert('An error occured.');
            console.log(xhr);
            console.log(status);
        },
        complete: function(xhr, txtstat){
            savedbStat = true;
            formAttr();
            btnOptGroupAttr();
        }
    });
}


async function deleteRecord( _data )
{
    if ( !appStat ) {
        return;
    }

    if( _data == null || _data === '' ){
        return;
    }

    document.getElementById('grayScreen').style.display = '';

    let datas = {};
    datas['mode'] = 'delete';
    datas['inputs'] = _data;
    let ajax = await $.ajax({
        url: _url,
        method: 'DELETE',
        data: datas,
        success:function(xhr) {
            if(xhr.status == "success"){
                displayMsg("#msgBoxMain", xhr.status, xhr.report);
                hideAllPopup();
                loadTbl();
            }else if(xhr.status == "info"){
                displayMsg("#msgBoxMain", xhr.status, xhr.report);
                hideAllPopup();
            }else{
                displayMsg("#msgBoxMain", xhr.status, xhr.report);
                hideAllPopup();
            }
        },
        error: function(xhr, status){
            hideAllPopup();
            console.log(xhr);
            console.log(status);
        },
        complete: function(xhr, txtstat){
            document.getElementById('grayScreen').style.display = 'none';
        }
    });
}


var genRepStat = true;
async function generateReport()
{
    if ( !appStat ) {
        return;
    }

    if(genRepStat == false){
        console.log("Please wait . . .");
        return;
    }
    genRepStat = false;

    // Create Form
    var tmpForm = document.createElement("form");
    var tmpElement;

    // Form Properties
    tmpForm.method = "POST";
    tmpForm.action = 'report.php';
    tmpForm.target = "_blank";

    // mode
    tmpElement = document.createElement("input");
    tmpElement.value = 'report';
    tmpElement.name = 'mode';
    tmpForm.appendChild(tmpElement);

    // module
    tmpElement = document.createElement("input");
    tmpElement.value = _module;
    tmpElement.name = 'module';
    tmpForm.appendChild(tmpElement);

    // search value
    tmpElement = document.createElement("input");
    tmpElement.value = document.getElementById('search').value;
    tmpElement.name = 'search';
    tmpForm.appendChild(tmpElement);

    // filters value
    let filterBox = document.getElementById('filterBox');
    let filters = filterBox.querySelectorAll('.filter');
    filters.forEach(_elem => {
        // let filter_name = _elem.id.toLowerCase().replace('filter_', '');

        if ( _elem.value ) {
            tmpElement = document.createElement("input");
            tmpElement.value = _elem.value;
            tmpElement.name = _elem.id; // filter_name;
            tmpForm.appendChild(tmpElement);
        }
    });

    document.body.appendChild(tmpForm);

    tmpForm.submit();
    tmpForm.remove();

    genRepStat = true;
}