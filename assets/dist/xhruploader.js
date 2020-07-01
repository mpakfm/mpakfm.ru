if (!window.Clipboard) {
    var pasteCatcher = document.createElement("div");

    pasteCatcher.setAttribute("contenteditable", "");

    pasteCatcher.style.display = "none";
    document.body.appendChild(pasteCatcher);

    pasteCatcher.focus();
    document.addEventListener("click", function() { pasteCatcher.focus(); });
    console.log('!window.Clipboard');
} else {
    console.log('window.Clipboard');
}
window.addEventListener("paste", pasteHandler);
function pasteHandler(e) {
    console.log('pasteHandler');
    if (e.clipboardData) {
        var items = e.clipboardData.items;
        console.log('items:');
        console.log(items);
        if (items) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf("image") !== -1) {
                    console.log('items['+i+']');
                    console.log(items[i]);
                    var blob = items[i].getAsFile();
                    console.log('blob');
                    console.log(blob);
                    var URLObj = window.URL || window.webkitURL;
                    var source = URLObj.createObjectURL(blob);
                    createImage(source);
                }
            }
        }
    } else {
        setTimeout(checkInput, 1);
    }
}

function checkInput() {
    console.log('checkInput');
    var child = pasteCatcher.childNodes[0];
    pasteCatcher.innerHTML = "";
    if (child) {
        if (child.tagName === "IMG") {
            createImage(child.src);
        }
    }
}

function createImage(source) {
    console.log('createImage source:');
    console.log(source);
    var pastedImage = new Image();
    pastedImage.onload = function() {
        document.getElementById("contenteditable").src = source;
    }
    pastedImage.src = source;
    console.log('pastedImage');
    console.log(pastedImage);

    var xhr = new XMLHttpRequest();
    console.log('createImage pastedImage.src: ' + pastedImage.src);
    xhr.open('GET', pastedImage.src, true);
    xhr.responseType = 'blob';
    xhr.onload = function(e) {
        console.log('this.status: ' + this.status);
        console.log(this.response);
        if (this.status == 200) {
            var reader = new window.FileReader();
            reader.readAsDataURL(this.response);
            reader.onloadend = function() {
                console.log('reader:');
                console.log(reader);
                loadImg(reader.result);
            }
        }
    };
    xhr.send();
}

function loadImg(dataURL) {
    console.log('loadImg dataURL:')
    console.log(dataURL)
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', '/blog/fileupload', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send("a=" + encodeURIComponent(dataURL));
    xmlhttp.onreadystatechange = function() {
        console.log('xmlhttp.status: ' + xmlhttp.status)
        console.log('xmlhttp.responseText: ' + xmlhttp.responseText)
        if (xmlhttp.readyState == 4) {
            if(xmlhttp.status == 200) {
                document.getElementById("base64").placeholder = xmlhttp.responseText;
                document.getElementById("done").src = xmlhttp.responseText;
            }
        }
    };
}

function getXmlHttp() {
    var xmlhttp;
    try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {
        try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); } catch (E) { xmlhttp = false; }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp = new XMLHttpRequest(); }
    return xmlhttp;
}