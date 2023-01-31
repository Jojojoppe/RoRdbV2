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

images = {}
categories = []
locations = []
items = []
with open('rordbv1_data.csv') as csvfile:
    reader = csv.reader(csvfile)
    id = None
    for row in reader:
        if id is None:
            id = 1
            continue
        imguri = img_to_url(row[0], str(id))
        images[id] = (imguri, row[1])
        with open(f'images/{id}.imguri', 'w') as f:
            f.write(imguri)

        # Add category and location
        if row[2] not in categories:
            categories.append(row[2])
        if row[3] not in locations:
            locations.append(row[3])

        items.append({
            'name':row[1],
            'category':categories.index(row[2])+3,
            'location':locations.index(row[3])+3,
            'claimedby':row[8],
            'hidden':row[9],
            'img':id,
            'color':row[4],
            'amount':row[6],
            'size':row[5],
            'comments':row[7],
        })

        id += 1

# Generate SQL
cattable = "wp_rordbv2_cat"
loctable = "wp_rordbv2_loc"
imgtable = "wp_rordbv2_img"
itemstable = "wp_rordbv2_items"
with open('rordbv1_catloc.sql', 'w') as sql:
    for cat in categories:
        sql.write(f"INSERT INTO {cattable} (name, parentid, parentid_list, childid_list, searchtags) VALUES ('{cat}', 1, ',1,', ',', ',{cat},All,');\n")
    for loc in locations:
        sql.write(f"INSERT INTO {loctable} (name, parentid, parentid_list, childid_list, searchtags) VALUES ('{loc}', 1, ',1,', ',', ',{loc},All,');\n")

imgfile=0
sql = open(f"rordbv1_img{imgfile}.sql", 'w')
nr = 0
for img in images.values():
    # if nr>25:
        # nr = 0
        # sql.close()
        # imgfile += 1
        # sql = open(f"rordbv1_img{imgfile}.sql", 'w')
    sql.write(f"INSERT INTO {imgtable} (name, data) VALUES ('{img[1]}', '{img[0]}');\n")
    nr += 1
sql.close()

with open('rordbv1_items.sql', 'w') as sql:
    for item in items:
        if item['name']=='':
            continue
        if item['img']=='':
            continue
        sql.write(f"INSERT INTO {itemstable} (name, category, location, claimedby, hidden, img, color, amount, size, comments) VALUES ('{item['name']}', {item['category']}, {item['location']}, '{item['claimedby']}', {item['hidden']}, {item['img']}, '{item['color']}', '{item['amount']}', '{item['size']}', '{item['comments']}');\n")
