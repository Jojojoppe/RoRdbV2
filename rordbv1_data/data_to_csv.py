from html.parser import HTMLParser
import csv

class MyParser(HTMLParser):
    img = ''
    name = ''
    category = ''
    location = ''
    color = ''
    size = ''
    amount = ''
    comments = ''
    claimed = ''
    hidden = ''

    def __init__(self, csvf):
        HTMLParser.__init__(self)
        self.csvf = csvf
        self.csvf.writerow(['image', 'name', 'category', 'location', 'color', 'size', 'amount', 'comments', 'claimed', 'hidden'])

    def emit_item(self):
        self.csvf.writerow([self.img, self.name, self.category, self.location, self.color, self.size, self.amount, self.comments, self.claimed, self.hidden])

    def handle_starttag(self, tag, attrs):
        if str(tag)=='img':
            self.img = attrs[1]

    def handle_endtag(self, tag):
        pass

    def handle_data(self, data):
        if data.startswith('Name:'):
            self.name = ' '.join(data.split(' ')[1:])
        elif data.startswith('Category:'):
            self.category = ' '.join(data.split(' ')[1:])
        elif data.startswith('Location:'):
            self.location = ' '.join(data.split(' ')[1:])
        elif data.startswith('Color:'):
            self.color = ' '.join(data.split(' ')[1:])
        elif data.startswith('Size:'):
            self.size = ' '.join(data.split(' ')[1:])
        elif data.startswith('Amount:'):
            self.amount = ' '.join(data.split(' ')[1:])
        elif data.startswith('Comments:'):
            self.comments = ' '.join(data.split(' ')[1:])
        elif data.startswith('Claimed:'):
            self.claimed = ' '.join(data.split(' ')[1:])
        elif data.startswith('Hidden:'):
            self.hidden = ' '.join(data.split(' ')[1:])
            self.emit_item()

with open('rordbv1_data.html') as file, open('rordbv1_data.csv', 'w') as csvf:
    parser = MyParser(csv.writer(csvf))
    parser.feed(file.read())
