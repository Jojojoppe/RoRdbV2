function rordbv2_sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function rordbv2_downscaleImage_and_put_imgcontent(dataUrl, newWidth, iid, did, imageType, imageArguments) {
    "use strict";
    var image, oldWidth, oldHeight, newHeight, canvas, ctx, newDataUrl;

    var html_img = document.getElementById(iid);
    var html_div = document.getElementById(did);

    // Provide default values
    imageType = imageType || "image/jpeg";
    imageArguments = imageArguments || 0.7;

    // Create a temporary image so that we can compute the height of the downscaled image.
    image = new Image();
    image.src = dataUrl;
    await rordbv2_sleep(500);
    oldWidth = image.width;
    oldHeight = image.height;
    newHeight = Math.floor(oldHeight / oldWidth * newWidth)

    console.log(image.width);
    console.log(oldHeight);
    console.log(newWidth);
    console.log(newHeight);

    // Create a temporary canvas to draw the downscaled image on.
    canvas = document.createElement("canvas");
    canvas.width = newWidth;
    canvas.height = newHeight;
    var ctx = canvas.getContext("2d");

    // Draw the downscaled image on the canvas and return the new data URL.
    ctx.drawImage(image, 0, 0, newWidth, newHeight);
    newDataUrl = canvas.toDataURL(imageType, imageArguments);

    html_img.src = newDataUrl;
    html_div.value = newDataUrl;

    return newDataUrl;
}

function rordbv2_put_filecontent_in_div(fid, did){
    var html_file = document.getElementById(fid);
    var file = html_file.files[0];
    var reader = new FileReader();
    reader.onload = function(e){
        var content = e.target.result;
        var html_div = document.getElementById(did);
        html_div.innerText = content;
    };
    reader.readAsText(file);
}

function rordbv2_put_imgcontent_in_img(fid, iid, did){
    var html_file = document.getElementById(fid);
    var file = html_file.files[0];
    var reader = new FileReader();
    reader.onload = function(e){
        // var content = e.target.result;
        var content = rordbv2_downscaleImage_and_put_imgcontent(e.srcElement.result, 500, iid, did);
    };
    reader.readAsDataURL(file);
}
