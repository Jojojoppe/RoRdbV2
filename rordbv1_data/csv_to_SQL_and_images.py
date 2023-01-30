import csv

# use 500x500 images
import base64
import io
from PIL import Image
import wget
import urllib

def pillow_image_to_base64_string(img):
    buffered = io.BytesIO()
    img.save(buffered, format="JPEG")
    return base64.b64encode(buffered.getvalue()).decode("utf-8")

def base64_string_to_pillow_image(base64_str):
    return Image.open(io.BytesIO(base64.decodebytes(bytes(base64_str, "utf-8"))))

def img_to_url(img, name):
    print(img)
    imgobj = eval(img)
    url = imgobj[1].replace('w200-h200', 'w500-h500')
    try:
        wget.download(url, f'images/{name}.jpeg')
    except urllib.error.HTTPError:
        return ''
    print()
    i = Image.open(f'images/{name}.jpeg')
    s = 'data:image/jpeg;base64,' + pillow_image_to_base64_string(i)
    i.close()
    return s

with open('rordbv1_data.csv') as csvfile:
    reader = csv.reader(csvfile)
    id = None
    for row in reader:
        if id is None:
            id = 0
            continue
        imguri = img_to_url(row[0], str(id))
        with open(f'images/{id}.imguri', 'w') as f:
            f.write(imguri)
        id += 1
